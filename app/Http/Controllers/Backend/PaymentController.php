<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\MembershipType;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;

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
        $query = $this->paymentService->getPaymentsEloquent($request);

    // Paginate the results, and ensure filter parameters are kept on pagination links.
    $payments = $query->paginate(15)->withQueryString();

        return view('backend.payments.index',
    [
            'payments' => $payments,
            'members' => Member::active()->get(),
            'membershipTypes' => MembershipType::active()->get(),
            'paymentMethods' => PaymentMethod::get()
        ]);
    }

    public function show(Payment $payment)
    {
        $payment = $this->paymentService->getPayment($payment);
        return view('backend.payments.show', compact('payment'));
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