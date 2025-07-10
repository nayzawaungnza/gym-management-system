<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Member;
use App\Models\MembershipType;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use DataTables;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('permission:payment-list', ['only' => ['index']]);
        $this->middleware('permission:payment-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:payment-edit', ['only' => ['edit', 'update']]);
        
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payments = $this->paymentService->getPayments($request);
            
            return DataTables::eloquent($payments)
                ->addIndexColumn()
                ->addColumn('member_name', function ($payment) {
                    return $payment->member?->full_name ?? 'Unknown';
                })
                ->addColumn('membership_type', function ($payment) {
                    return $payment->membershipType?->type_name ?? 'N/A';
                })
                ->addColumn('amount_formatted', function ($payment) {
                    return '$' . number_format($payment->amount, 2);
                })
                ->addColumn('payment_date_formatted', function ($payment) {
                    return $payment->payment_date->format('M d, Y H:i');
                })
                ->addColumn('status_badge', function ($payment) {
                    $badgeClass = match($payment->status) {
                        'Completed' => 'success',
                        'Pending' => 'warning',
                        'Failed' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $badgeClass . '">' . $payment->status . '</span>';
                })
                ->addColumn('action', function ($payment) {
                    $actions = '<div class="dropdown">';
                    $actions .= '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">';
                    $actions .= '<i class="bx bx-dots-vertical-rounded"></i>';
                    $actions .= '</button>';
                    $actions .= '<div class="dropdown-menu">';
                    
                    $actions .= '<a class="dropdown-item" href="' . route('payments.receipt', $payment->id) . '" target="_blank">';
                    $actions .= '<i class="bx bx-receipt me-1"></i> Receipt';
                    $actions .= '</a>';
                    
                    if ($payment->status === 'Pending' && auth()->user()->can('payment-edit')) {
                        $actions .= '<a class="dropdown-item text-success" href="javascript:void(0);" onclick="markCompleted(\'' . $payment->id . '\')">';
                        $actions .= '<i class="bx bx-check me-1"></i> Mark Completed';
                        $actions .= '</a>';
                    }
                    
                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('backend.payments.index');
    }

    public function create()
    {
        $members = Member::active()->get();
        $membershipTypes = MembershipType::active()->get();
        return view('backend.payments.create', compact('members', 'membershipTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Credit Card,Bank Transfer',
            'membership_type_id' => 'required|exists:membership_types,id'
        ]);

        $this->paymentService->createPayment($request->all());

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function receipt(Payment $payment)
    {
        return view('backend.payments.receipt', compact('payment'));
    }

    public function markCompleted(Payment $payment)
    {
        $this->paymentService->markCompleted($payment);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as completed.'
        ]);
    }
}
