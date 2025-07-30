<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PaymentMethodService;
use App\Http\Requests\PaymentMethod\CreatePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;

class PaymentMethodController extends Controller
{
    protected $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->middleware('permission:paymentmethod-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:paymentmethod-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:paymentmethod-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:paymentmethod-delete', ['only' => ['destroy']]);

        $this->paymentMethodService = $paymentMethodService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $paymentMethods = $this->paymentMethodService->getPaymentMethodEloquent($request->all());
                return DataTables::eloquent($paymentMethods)
                    ->addIndexColumn()
                    ->addColumn('payment_logo_display', function ($paymentMethod) {
                       if ($paymentMethod->payment_logo) {
                    // Check if the path already contains 'storage/' (Laravel storage path)
                    if (Str::contains($paymentMethod->payment_logo, 'payment_methods/')) {
                        return '<img src="' . asset($paymentMethod->payment_logo) . '" alt="Logo" style="width: 50px; height: auto;">';
                    }
                    // Check if it's a public path (starts with assets/)
                    elseif (Str::startsWith($paymentMethod->payment_logo, 'assets/')) {
                        return '<img src="' . asset($paymentMethod->payment_logo) . '" alt="Logo" style="width: 50px; height: auto;">';
                    }
                    // Default case - assume it's a storage path
                    else {
                        return '<img src="' . asset('storage/' . $paymentMethod->payment_logo) . '" alt="Logo" style="width: 50px; height: auto;">';
                    }
                }
                return 'N/A';
                    })
                    ->addColumn('status_badge', function ($paymentMethod) {
                        $badgeClass = $paymentMethod->is_active ? 'success' : 'secondary';
                        $status = $paymentMethod->is_active ? 'Active' : 'Inactive';
                        return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
                    })
                    ->addColumn('action', function ($paymentMethod) {
                        $btn = '<div class=" m-sm-n1">';
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-success" href="' . route('paymentmethods.edit', $paymentMethod->id) . '"
                                    data-original-title="" title="Edit">
                                    <i class="fas fa-edit"></i>
                                    <div class="ripple-container"></div>
                                    </a></div>';
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-primary" href="' . route('paymentmethods.show', $paymentMethod->id) . '"
                                    data-original-title="" title="Show">
                                    <i class="fas fa-eye"></i>
                                    <div class="ripple-container"></div>
                                </a></div>';
                        if (auth()->user()->can('paymentmethod-delete')) {
                            $btn .= '<div class="my-1 text-center"><form action="' . route('paymentmethods.destroy', $paymentMethod->id) . '" method="POST" id="del-paymentmethod-' . $paymentMethod->id . '" class="d-inline">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-original-title="" data-origin="del-paymentmethod-' . $paymentMethod->id . '" title="Delete">
                                        <i class="fas fa-trash"></i>
                                        </button>                                                    
                                        </form></div>';
                        }
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->rawColumns(['payment_logo_display', 'status_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DataTables error in PaymentMethodController@index: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'An error occurred while fetching data. Please check server logs.'
                ], 500);
            }
        }
        return view('backend.payment_methods.index');
    }

    public function create()
    {
        return view('backend.payment_methods.create');
    }

    public function store(CreatePaymentMethodRequest $request)
    {
        $this->paymentMethodService->createPaymentMethod($request->validated());

        return redirect()->route('paymentmethods.index')
            ->with('success', 'Payment Method created successfully.');
    }

    public function show(PaymentMethod $paymentmethod)
    {
        return view('backend.payment_methods.show', compact('paymentmethod'));
    }

    public function edit(PaymentMethod $paymentmethod)
    {
        return view('backend.payment_methods.edit', compact('paymentmethod'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentmethod)
    {
        $this->paymentMethodService->updatePaymentMethod($paymentmethod, $request->validated());

        return redirect()->route('paymentmethods.index')
            ->with('success', 'Payment Method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentmethod)
    {
        // You might want to add logic here to prevent deletion if there are associated payments
        // or active subscriptions using this payment method.
        // Example: if ($paymentmethod->payments()->count() > 0) { ... }

        $this->paymentMethodService->deletePaymentMethod($paymentmethod);

        return response()->json([
            'success' => true,
            'message' => 'Payment Method deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, PaymentMethod $paymentmethod)
    {
        $this->paymentMethodService->changeStatus($paymentmethod);

        return response()->json([
            'success' => true,
            'message' => 'Payment Method status updated successfully.'
        ]);
    }
}