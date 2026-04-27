<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #1a5276;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 6px 6px 0 0;
        }

        .header img {
            max-width: 140px;
        }

        .body {
            background: #f9f9f9;
            padding: 24px;
            border: 1px solid #ddd;
        }

        .highlight {
            background: #eaf4ea;
            border-left: 4px solid #27ae60;
            padding: 12px 16px;
            margin: 16px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        .table th {
            background: #1a5276;
            color: white;
            padding: 8px 12px;
            text-align: left;
        }

        .table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            padding: 16px;
        }

        .btn {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <div class="header">
            <h2 style="margin:0">SmartK</h2>
            <p style="margin:4px 0 0 0; font-size:13px;">Purchase Order Confirmation</p>
        </div>

        <div class="body">

            <p>Dear <strong>{{ $invoice->customer->name }}</strong>,</p>

            <p>We are pleased to confirm that your Purchase Order has been <strong
                    style="color:#27ae60">approved</strong>.</p>

            <div class="highlight">
                <strong>PO Number:</strong> {{ $invoice->po_number }}<br>
                <strong>Date:</strong> {{ $invoice->invoice_date->format('d M, Y') }}<br>
                <strong>Total Amount:</strong> ₹{{ number_format($invoice->amount, 2) }}
            </div>

            <h4 style="border-bottom: 1px solid #ddd; padding-bottom: 8px;">Order Details</h4>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->invoiceItems as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->rate, 2) }}</td>
                            <td>₹{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right"><strong>Total</strong></td>
                        <td><strong>₹{{ number_format($invoice->amount, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            @if ($invoice->pdcs->count())
                <h4 style="border-bottom: 1px solid #ddd; padding-bottom: 8px;">Payment Schedule (PDC)</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cheque</th>
                            <th>Bank</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->pdcs as $pdc)
                            <tr>
                                <td>{{ $pdc->cheque_number }}</td>
                                <td>{{ $pdc->bank_name }}</td>
                                <td>{{ $pdc->cheque_date->format('d M, Y') }}</td>
                                <td>₹{{ number_format($pdc->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ($invoice->terms)
                <h4 style="border-bottom: 1px solid #ddd; padding-bottom: 8px;">Terms & Conditions</h4>
                <p style="font-size: 13px; color: #555;">{{ $invoice->terms }}</p>
            @endif

            <p style="font-size: 13px; color: #777; margin-top: 20px;">
                <strong>Note:</strong> It is mandatory to submit a signed hard copy of this document along with other
                required documents.
            </p>

        </div>

        <div class="footer">
            <p>This is an automated email from SmartK CRM. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} SmartK. All rights reserved.</p>
        </div>

    </div>
</body>

</html>
