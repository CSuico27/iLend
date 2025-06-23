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
            margin: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
        }

        .receipt-no {
            text-align: right;
        }

        .receipt-no strong {
            color: red;
            font-size: 18px;
        }

        .section {
            margin-top: 20px;
            line-height: 1.6;
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
            width: 46%;
        }

        .full-underline {
            border-bottom: 1px solid #000;
            font-weight: bold;
            width: 100%;
            display: inline-block;
        }

        .bottom {
            margin-top: 30px;
            font-size: 12px;
            line-height: 1.4;
        }

        .signature {
            margin-top: 40px;
            font-size: 13px;
        }

        .signature span {
            margin-left: 65%;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>OFFICIAL RECEIPT</h2>
        </div>
        <div class="receipt-no">
            <p>No. <strong>{{ str_pad($ledger->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
            <p>Date: {{optional($ledger->payment)?->date_received ? Carbon::parse($ledger->payment->date_received)->format('F j, Y') : '_______, 20___'}}</p>
        </div>
    </div>

    <div class="section">
        Received from <span class="full-underline">{{ $loan->user->name }}</span>
        <br>with TIN <span class="underline">{{ $loan->user->info->tin_number ?? 'N/A' }}</span> and reside at 
        <span class="semi-underline">{{ $loan->user->info->address ?? 'N/A' }}</span><br>
        who agreed to join LUCBAN GENESIS TRANSPORT SERVICE and MULTIPURPOSE COOPERATIVE as a member with ID No. 
        <span class="semi-underline">{{ $loan->user->info->member_id ?? 'N/A' }}</span><br>
        the sum of<br>
        <span class="full-underline">₱{{ number_format($ledger->loan->payment_per_term ?? 0, 2) }}</span><br>
        in partial/full payment for <span class="full-underline">{{ $loan->loan_type }}</span>
    </div>

    <div class="bottom">
        <strong>LUCBAN GENESIS TRANSPORT SERVICE and MULTIPURPOSE COOPERATIVE</strong><br>
        CDA Registration No: 9520-0409811<br>
        OTC Accreditation No: 2011-265<br>
        Office Address: San Luis St., Lucban Quezon<br>
        Tel No. (042) 373-0551<br>
        CIN: 0104040163, TIN: 005-829-603
    </div>

    <div class="signature">
       <span>By:</span> <div class="signature-line">
            Authorized Signature
        </div>
    </div>
</body>
</html>