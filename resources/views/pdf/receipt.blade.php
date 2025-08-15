@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Official Receipt</title>
        <style>
            body {
                font-family: 'DejaVu Sans', sans-serif;
                margin: 20px;
                font-size: 13px;
            }

            .receipt-container {
                border: 2px solid #000;
                padding: 25px;
                max-width: 750px;
                margin: 0 auto;
            }

            .header-table, .title-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }

            .header-table td, .title-table td {
                vertical-align: top;
                padding: 0;
            }

            .logo-cell {
                width: 25%;
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
                font-size: 11px;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .company-details {
                font-size: 11px;
                line-height: 1.5;
            }
            
            .receipt-title-cell {
                width: 50%;
                vertical-align: middle;
                text-align: left;
            }

            .receipt-number-cell {
                width: 50%;
                vertical-align: middle;
                text-align: right;
            }

            .receipt-title {
                color: #fe0002;
                font-size: 18px;
                font-weight: bold;
                line-height: 1.2;
            }

            .receipt-number {
                text-align: right;
            }

            .receipt-number-text {
                color: #fe0002;
                font-size: 18px;
                font-weight: bold;
                line-height: 1.2;
            }

            .date-line {
                font-size: 12px;
                margin-top: 5px;
                display: block;
            }

            .receipt-body {
                line-height: 1.5;
                margin: 20px 0;
                font-size: 13px;
            }

            .underline {
                display: inline-block;
                border-bottom: 1px solid #000;
                font-weight: bold;
                min-width: 100px;
            }
            .semi-underline {
                display: inline-block;
                border-bottom: 1px solid #000;
                font-weight: bold;
                width: 54%;
            }
            .full-underline {
                border-bottom: 1px solid #000;
                font-weight: bold;
                width: 100%;
                display: inline-block;
            }
            .member-underline {
                display: inline-block;
                border-bottom: 1px solid #000;
                font-weight: bold;
                width: 83%;
            }
            .loantype-underline {
                display: inline-block;
                border-bottom: 1px solid #000;
                font-weight: bold;
                width: 41%;
            }

            .signature-section {
                margin-top: 50px;
                text-align: right;
                font-size: 13px;
            }

            .signature-line {
                display: inline-block;
                border-bottom: 1px solid #000;
                width: 250px;
                margin-bottom: 5px;
            }
        </style>
    </head>
    <body>
        <div class="receipt-container">

            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('images/ilend-logo - Copy.png') }}" alt="Logo">
                    </td>
                    <td class="company-info-cell" colspan="2">
                        <div class="company-name">LUCBAN GENESIS TRANSPORT SERVICE & MPC</div>
                        <div class="company-details">
                            CDA REGISTRATION NO: 9502-00409811<br>
                            OTC Accreditation Non: 2011-265<br>
                            San Luis St., Lucban, Quezon<br>
                            Tel No. (042) 373-0551
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>
            
            <table class="title-table">
                <tr>
                    <td class="receipt-title-cell">
                        <div class="receipt-title">OFFICIAL RECEIPT</div>
                    </td>
                    <td class="receipt-number-cell">
                        <div class="receipt-number">
                            No <span class="receipt-number-text">{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <div class="date-line">
                                Date: {{ $payment->date_received ? Carbon::parse($payment->date_received)->format('F j, Y') : '_______, 20___' }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            
            <div class="receipt-body">
                Received from <span class="member-underline">{{ $loan->user->name ?? '' }}</span><br>
                with TIN <span class="underline">{{ $loan->user->info->tin_number ?? 'N/A' }}</span> and reside at <span class="semi-underline">{{ $loan->user->info->address ?? 'N/A' }}</span><br>
                who agreed to join <strong>LUCBAN GENESIS TRANSPORT SERVICE and MULTIPURPOSE COOPERATIVE</strong> as a member with ID No. 
                <span class="semi-underline">{{ $loan->user->info->member_id ?? 'N/A' }}</span><br>
                the sum of(<span class="underline">₱{{ number_format($ledger->loan->payment_per_term ?? 0, 2) }}</span>) in partial/full payment for <span class="loantype-underline">{{ ucfirst($loan->loan_type) ?? '' }} Loan</span>
            </div>

            <div class="signature-section">
                <span style="display:inline-block; margin-bottom: 5px;">By:</span>
                <div class="signature-line"></div>
                <div style="text-align: center; margin-left: 58%;">Authorized Signature</div>
            </div>
        </div>
    </body>
</html>