@php
    use Carbon\Carbon;

    $loanAmount = (float) preg_replace('/[^\d.]/', '', $loan->loan_amount ?? '');
    $interestRate = (float) preg_replace('/[^\d.]/', '', $loan->interest_rate ?? '');
    $totalPayment = (float) preg_replace('/[^\d.]/', '', $loan->total_payment ?? '');
    $paymentPerTerm = (float) preg_replace('/[^\d.]/', '', $loan->payment_per_term ?? '');
    $interestAmount = (float) preg_replace('/[^\d.]/', '', $loan->interest_amount ?? '');
    $loanTerm = (int) ($loan->loan_term ?? 0);
    $ledgersCollection = ($loan->ledgers ?? collect())->values();
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loan Ledger Summary</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        h1{
            font-size: 14px;
            margin-bottom: 12px;
        }
        h2 {
            font-size: 16px;
            color: #fe0002;
            margin-bottom: 6px;
        }
        .section {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        td, th {
            border: 1px solid #666;
            padding: 6px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .no-border td {
            border: none;
            padding: 4px 6px;
        }
        .header-color {
            background-color: #fe0002;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <h1 class="text-right">Name: {{ $loan->user->name ?? 'N/A' }}</h1>
    <h1 class="text-right">Member ID: {{ $loan->user->info->member_id ?? 'N/A' }}</h1>
    <h2>Loan Application Details</h2>
    <div class="section">
        <table class="no-border">
            <tr>
                <td>Loan Amount</td>
                <td class="text-right">₱{{ number_format($loanAmount, 2) }}</td>
            </tr>
            <tr>
                <td>Interest Rate</td>
                <td class="text-right">{{ $interestRate }}%</td>
            </tr>
            <tr>
                <td>Loan Term</td>
                <td class="text-right">{{ $loanTerm }}</td>
            </tr>
            <tr>
                <td>Number of Payments</td>
                <td class="text-right">{{ $ledgersCollection->count() }}</td>
            </tr>
            <tr>
                <td>Start Date of Payment</td>
                <td class="text-right">
                    @php
                        $startDateFormatted = 'N/A';
                        try {
                            if ($loan->start_date) {
                                $startDateFormatted = Carbon::parse($loan->start_date)->format('M j, Y');
                            }
                        } catch (\Exception $e) {
                            $startDateFormatted = 'Invalid Date';
                        }
                    @endphp
                    {{ $startDateFormatted }}
                </td>
            </tr>
        </table>
    </div>

    <h2>Loan Summary</h2>
    <div class="section">
        <table class="no-border">
            <tr>
                <td>Scheduled Payment</td>
                <td class="text-right">₱{{ number_format($paymentPerTerm, 2) }}</td>
            </tr>
            <tr>
                <td>Scheduled Number of Payments</td>
                <td class="text-right">{{ $ledgersCollection->count() }}</td>
            </tr>
            <tr>
                <td>Total Loan Payable</td>
                <td class="text-right">₱{{ number_format($totalPayment, 2) }}</td>
            </tr>
            <tr>
                <td>Total Interest</td>
                <td class="text-right">₱{{ number_format($interestAmount, 2) }}</td>
            </tr>
            <tr>
                <td>Lender</td>
                <td class="text-right">iLEND</td>
            </tr>
        </table>
    </div>

    <h2>Loan Ledger</h2>
    <div>
        <table>
            <thead>
                <tr class="header-color">
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Scheduled Payment</th>
                    <th>Total Loan Payable</th>
                    <th>Total Interest</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ledgersCollection as $index => $ledger)
                    @php
                        $paymentCount = $ledgersCollection->count();
                        $interestPerPayment = $paymentCount > 0 ? floatval($interestAmount) / $paymentCount : 0;
                        $principal = floatval($paymentPerTerm) - $interestPerPayment;

                        $ledgerDueDateFormatted = 'N/A';
                        try {
                            if ($ledger->due_date) {
                                $ledgerDueDateFormatted = Carbon::parse($ledger->due_date)->format('F j, Y');
                            }
                        } catch (\Exception $e) {
                            $ledgerDueDateFormatted = 'Invalid Date';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $ledgerDueDateFormatted }}</td>
                        <td>₱{{ number_format($paymentPerTerm, 2) }}</td>
                        <td>₱{{ number_format($totalPayment, 2) }}</td>
                        <td>₱{{ number_format($interestAmount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No ledger entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
