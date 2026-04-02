<?php
/**
 * PaymentController – Record payments, history, receipt PDF
 */
class PaymentController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $payments = Payment::allForSchool($schoolId, 100);
        $this->view('payments.index', ['title' => 'Payments', 'payments' => $payments]);
    }

    /**
     * Show pay form for a student fee.
     */
    public function create($studentFeeId): void
    {
        $studentFeeId = (int) $studentFeeId;
        $schoolId = current_school_id();
        $sf = StudentFee::findSecure($studentFeeId, $schoolId);
        if (!$sf) { flash('error', 'Fee not found.'); redirect(tenant_slug() . '/fees/student-fees'); }

        $student = Student::find($sf['student_id']);
        $feeType = FeeType::find($sf['fee_type_id']);
        $balance = (float)$sf['amount'] - (float)$sf['paid_amount'];

        $this->view('payments.create', [
            'title'   => 'Record Payment',
            'sf'      => $sf,
            'student' => $student,
            'feeType' => $feeType,
            'balance' => $balance,
        ]);
    }

    public function store(): void
    {
        $schoolId      = current_school_id();
        $studentFeeId  = (int) $this->input('student_fee_id');
        $amount        = (float) $this->input('amount');
        $method        = $this->input('payment_method') ?: 'cash';
        $transactionId = $this->input('transaction_id');
        $paymentDate   = $this->input('payment_date') ?: date('Y-m-d');
        $notes         = $this->input('notes');

        $sf = StudentFee::findSecure($studentFeeId, $schoolId);
        if (!$sf) { flash('error', 'Fee not found.'); redirect(tenant_slug() . '/payments'); }

        $balance = (float)$sf['amount'] - (float)$sf['paid_amount'];
        if ($amount <= 0) { flash('error', 'Amount must be positive.'); redirect(tenant_slug() . "/payments/{$studentFeeId}/pay"); }
        if ($amount > $balance) $amount = $balance;

        $paymentId = Payment::create([
            'school_id'      => $schoolId,
            'student_fee_id' => $studentFeeId,
            'student_id'     => $sf['student_id'],
            'amount'         => $amount,
            'payment_method' => $method,
            'transaction_id' => $transactionId,
            'payment_date'   => $paymentDate,
            'received_by'    => current_user_id(),
            'notes'          => $notes,
        ]);

        // Update student fee paid amount & status
        StudentFee::updatePayment($studentFeeId, $amount);

        $this->audit('payment', 'payment', $paymentId);
        flash('success', 'Payment of ' . number_format($amount, 2) . ' recorded successfully.');
        redirect(tenant_slug() . '/payments/' . $paymentId . '/receipt');
    }

    /**
     * View / print receipt.
     */
    public function receipt($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $payment = Payment::findSecure($id, $schoolId);
        if (!$payment) { flash('error', 'Payment not found.'); redirect(tenant_slug() . '/payments'); }

        $school = School::find($schoolId);

        $this->view('payments.receipt', [
            'title'   => 'Payment Receipt',
            'payment' => $payment,
            'school'  => $school,
        ]);
    }

    /**
     * Download receipt as PDF.
     */
    public function receiptPdf($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $payment = Payment::findSecure($id, $schoolId);
        if (!$payment) { flash('error', 'Not found.'); redirect(tenant_slug() . '/payments'); }

        $school = School::find($schoolId);

        $html = PdfHelper::renderView('pdf.fee-receipt', [
            'payment' => $payment,
            'school'  => $school,
        ]);
        PdfHelper::generate($html, 'receipt_' . $id . '.pdf');
    }

    /**
     * Payment history with filters.
     */
    public function history(): void
    {
        $schoolId = current_school_id();
        $dateFrom = $_GET['from'] ?? '';
        $dateTo   = $_GET['to'] ?? '';
        $method   = $_GET['method'] ?? '';

        $sql = "SELECT p.*, s.name as student_name, s.admission_no, ft.name as fee_type_name, c.name as class_name
                FROM payments p
                LEFT JOIN students s ON p.student_id = s.id
                LEFT JOIN student_fees sf ON p.student_fee_id = sf.id
                LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE p.school_id = ?";
        $params = [$schoolId];

        if ($dateFrom) { $sql .= " AND p.payment_date >= ?"; $params[] = $dateFrom; }
        if ($dateTo) { $sql .= " AND p.payment_date <= ?"; $params[] = $dateTo; }
        if ($method) { $sql .= " AND p.payment_method = ?"; $params[] = $method; }
        $sql .= " ORDER BY p.payment_date DESC LIMIT 200";

        $payments = Database::fetchAll($sql, $params);
        $totalFiltered = 0;
        foreach ($payments as $p) $totalFiltered += (float)$p['amount'];

        $this->view('payments.history', [
            'title'         => 'Payment History',
            'payments'      => $payments,
            'totalFiltered' => $totalFiltered,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'method'        => $method,
        ]);
    }
}
