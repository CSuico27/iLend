@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Statement of Account</title>
        <style>
            body {
                font-family: 'DejaVu Sans', sans-serif;
                margin: 20px;
                font-size: 14px;
            }

            .soa-container {
                border: 2px solid #000;
                padding: 20px;
                max-width: 800px;
                margin: 0 auto;
            }

            /* Header Section */
            .header-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .header-table td {
                vertical-align: top;
                padding: 0;
            }

            .logo-cell {
                width: 20%;
            }

            .logo-cell img {
                max-height: 80px;
                display: block;
            }

            .company-info-cell {
                width: 100%;
                text-align: center;
            }

            .company-name {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 3px;
                color: #000;
            }

            .company-details {
                font-size: 9px;
                line-height: 1.4;
                color: #555;
            }

            /* Title Section */
            .soa-title {
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                color: #fe0002;
                margin: 15px 0;
                padding: 12px 0;
                border-top: 3px solid #000;
                border-bottom: 3px solid #000;
                letter-spacing: 1px;
            }

            .document-info {
                text-align: right;
                font-size: 10px;
                margin-bottom: 15px;
                color: #666;
            }

            /* Member Information */
            .member-section {
                background-color: #f8f9fa;
                padding: 12px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .section-title {
                font-weight: bold;
                font-size: 12px;
                margin-bottom: 8px;
                color: #333;
                border-bottom: 2px solid #fe0002;
                padding-bottom: 4px;
            }

            .info-grid {
                display: table;
                width: 100%;
            }

            .info-row {
                display: table-row;
            }

            .info-label {
                display: table-cell;
                font-weight: bold;
                width: 25%;
                padding: 4px 8px 4px 0;
                color: #555;
            }

            .info-value {
                display: table-cell;
                padding: 4px 0;
                color: #000;
            }

            /* Loan Summary Box */
            .summary-box {
                border: 2px solid #fe0002;
                padding: 15px;
                margin: 15px 0;
                background: linear-gradient(to bottom, #fff 0%, #ffe6e6 100%);
                border-radius: 4px;
            }

            .summary-table {
                width: 100%;
                border-collapse: collapse;
            }

            .summary-table td {
                padding: 6px 8px;
                border-bottom: 1px dashed #ccc;
            }

            .summary-table tr:last-child td {
                border-bottom: none;
                font-weight: bold;
                font-size: 13px;
                padding-top: 12px;
                border-top: 2px solid #333;
            }

            .label-col {
                width: 60%;
                color: #555;
            }

            .amount-col {
                text-align: right;
                color: #000;
            }

            .highlight-amount {
                color: #fe0002;
                font-size: 14px;
            }

            .schedule-title {
                font-weight: bold;
                font-size: 12px;
                margin: 20px 0 10px 0;
                color: #333;
            }

            .ledger-table {
                width: 100%;
                border-collapse: collapse;
                margin: 10px 0 20px 0;
                font-size: 10px;
            }

            .ledger-table th {
                background-color: #333;
                color: #fff;
                font-weight: bold;
                padding: 8px 5px;
                text-align: center;
                border: 1px solid #000;
            }

            .ledger-table td {
                padding: 6px 5px;
                text-align: center;
                border: 1px solid #ddd;
            }

            .ledger-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .ledger-table tr:hover {
                background-color: #f0f0f0;
            }

            .status-paid {
                color: #fff;
                background-color: #28a745;
                padding: 3px 8px;
                border-radius: 3px;
                font-weight: bold;
                font-size: 9px;
            }

            .status-pending {
                color: #fff;
                background-color: #ffc107;
                padding: 3px 8px;
                border-radius: 3px;
                font-weight: bold;
                font-size: 9px;
            }

            .status-overdue {
                color: #fff;
                background-color: #dc3545;
                padding: 3px 8px;
                border-radius: 3px;
                font-weight: bold;
                font-size: 9px;
            }

            .footer-section {
                margin-top: 25px;
                padding-top: 15px;
                border-top: 2px solid #333;
            }

            .footer-note {
                font-size: 9px;
                text-align: center;
                color: #666;
                font-style: italic;
                margin: 10px 0;
            }

            .generated-info {
                text-align: right;
                font-size: 8px;
                color: #999;
                margin-top: 15px;
            }

            /* .signature-section {
                margin-top: 30px;
                text-align: right;
            } */

            /* .signature-line {
                display: inline-block;
                width: 200px;
                border-bottom: 1px solid #000;
                margin-bottom: 5px;
            } */

            /* .signature-label {
                text-align: center;
                font-size: 9px;
                color: #666;
            } */

            /* Progress Bar */
            /* .progress-section {
                margin: 15px 0;
                padding: 10px;
                background-color: #f8f9fa;
                border-radius: 4px;
            } */

            /* .progress-label {
                font-size: 10px;
                margin-bottom: 5px;
                color: #555;
            } */

            /* .progress-bar-container {
                width: 100%;
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                border: 1px solid #ccc;
            } */

            /* .progress-bar-fill {
                height: 100%;
                background: linear-gradient(to right, #28a745, #5cb85c);
                text-align: center;
                line-height: 20px;
                color: white;
                font-weight: bold;
                font-size: 10px;
            } */
        </style>
    </head>
    <body>
        <div class="soa-container">

            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('images/ilend-logo - Copy.png') }}" alt="Logo">
                    </td>
                    <td class="company-info-cell">
                        <div class="company-name">LUCBAN GENESIS TRANSPORT SERVICE & MPC</div>
                        <div class="company-details">
                            CDA REGISTRATION NO: 9502-00409811<br>
                            OTC Accreditation No: 2011-265<br>
                            San Luis St., Lucban, Quezon | Tel No. (042) 373-0551
                        </div>
                    </td>
                </tr>
            </table>

            <div class="document-info">
                <strong>SOA No:</strong> {{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}<br>
                <strong>Date Issued:</strong> {{ Carbon::now()->format('F d, Y') }}
            </div>

            <div class="soa-title">STATEMENT OF ACCOUNT</div>

            <div class="member-section">
                <div class="section-title">MEMBER INFORMATION</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Member Name:</div>
                        <div class="info-value">{{ $loan->user->name ?? 'N/A' }}</div>
                        <div class="info-label">Member ID:</div>
                        <div class="info-value">{{ $loan->user->info->member_id ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">TIN Number:</div>
                        <div class="info-value">{{ $loan->user->info->tin_number ?? 'N/A' }}</div>
                        <div class="info-label">Contact:</div>
                        <div class="info-value">{{ $loan->user->info->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address:</div>
                        <div class="info-value" colspan="3">
                            {{ 'Brgy. ' . ($loan->user->info->barangay ?? '') . ', ' . ($loan->user->info->municipality ?? '') . ', ' . ($loan->user->info->province ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-box">
                <div class="section-title">LOAN SUMMARY</div>
                <table class="summary-table">
                    <tr>
                        <td class="label-col">Loan Type:</td>
                        <td class="amount-col">{{ ucfirst($loan->loan_type) }} Loan</td>
                    </tr>
                    <tr>
                        <td class="label-col">Loan Amount:</td>
                        <td class="amount-col">&#8369;{{ number_format($loan->loan_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Interest Rate:</td>
                        <td class="amount-col">{{ $loan->interest_rate }}%</td>
                    </tr>
                    <tr>
                        <td class="label-col">Interest Amount:</td>
                        <td class="amount-col">&#8369;{{ number_format($loan->interest_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Total Loan Payable:</td>
                        <td class="amount-col">&#8369;{{ number_format($loan->total_payment, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Total Payments Made:</td>
                        <td class="amount-col" style="color: #28a745;">₱{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">REMAINING BALANCE:</td>
                        <td class="amount-col highlight-amount">₱{{ number_format($remainingBalance, 2) }}</td>
                    </tr>
                </table>
            </div>

            {{-- <div class="progress-section">
                <div class="progress-label">
                    <strong>Payment Progress:</strong> 
                    {{ number_format(($totalPaid / $loan->total_payment) * 100, 1) }}% Complete
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ min(($totalPaid / $loan->total_payment) * 100, 100) }}%">
                        {{ number_format(min(($totalPaid / $loan->total_payment) * 100, 100), 0) }}%
                    </div>
                </div>
            </div> --}}

            <div class="schedule-title">PAYMENT SCHEDULE & HISTORY</div>
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 15%;">Due Date</th>
                        <th style="width: 15%;">Total Loan Payable</th>
                        <th style="width: 15%;">Remaining Balance</th>
                        <th style="width: 15%;">Amount Due</th>
                        <th style="width: 15%;">Amount Paid</th>
                        <th style="width: 15%;">Date Paid</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $runningBalance = $loan->total_payment;
                    @endphp
                    @foreach($loan->ledgers as $index => $ledger)
                        @php
                            $isPaid = $ledger->status === 'Paid';
                            $isOverdue = !$isPaid && Carbon::parse($ledger->due_date)->isPast();
                            $statusClass = $isPaid ? 'status-paid' : ($isOverdue ? 'status-overdue' : 'status-pending');
                            $statusText = $isPaid ? 'Paid' : ($isOverdue ? 'Overdue' : 'Pending');
                            $amountPaid = $ledger->payment && $ledger->payment->status === 'Approved' ? $ledger->payment->amount : 0;
                            $runningBalance -= $amountPaid;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ Carbon::parse($ledger->due_date)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($loan->total_payment, 2) }}</td>
                            <td style="font-weight: bold; color: #dc3545;">
                                &#8369;{{ number_format(max($runningBalance, 0), 2) }}
                            </td>
                            <td>₱{{ number_format($loan->payment_per_term, 2) }}</td>
                            <td style="color: {{ $isPaid ? '#28a745' : '#999' }}; font-weight: {{ $isPaid ? 'bold' : 'normal' }};">
                                {{ $amountPaid > 0 ? '₱' . number_format($amountPaid, 2) : 'N/A' }}
                            </td>
                            <td>
                                {{ $ledger->payment && $ledger->payment->date_received ? Carbon::parse($ledger->payment->date_received)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                <span class="{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Footer -->
            <div class="footer-section">
                <div class="footer-note">
                    This is a computer-generated statement and does not require a signature.<br>
                    For questions or concerns, please contact our office at (042) 373-0551 or visit us at San Luis St., Lucban, Quezon.
                </div>

                {{-- <div class="signature-section">
                    <div>Verified by:</div>
                    <div class="signature-line"></div>
                    <div class="signature-label">Authorized Representative</div>
                </div> --}}

                <div class="generated-info">
                    Generated on: {{ Carbon::now()->format('F d, Y h:i A') }}<br>
                    System Reference: SOA-{{ $loan->id }}-{{ Carbon::now()->format('YmdHis') }}
                </div>
            </div>

        </div>
    </body>
</html>