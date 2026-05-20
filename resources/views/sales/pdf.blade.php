<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $sale->invoice_no }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .page {
            padding: 30px;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .company {
            float: left;
            width: 55%;
        }

        .invoice-info {
            float: right;
            width: 40%;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .company h1 {
            margin: 0;
            font-size: 24px;
            color: #111827;
        }

        .company p,
        .invoice-info p {
            margin: 4px 0;
            color: #4b5563;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        .badge-partial {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .section {
            margin-top: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #111827;
        }

        .box {
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 8px;
            background: #f9fafb;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .items-table th {
            background: #f3f4f6;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
            padding: 10px;
            border: 1px solid #e5e7eb;
        }

        .items-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            width: 42%;
            margin-left: auto;
            margin-top: 25px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            border: none;
            padding: 7px 0;
            font-size: 12px;
            line-height: 1.4;
        }

        .summary-table .label {
            color: #4b5563;
            text-align: left;
        }

        .summary-table .value {
            text-align: right;
            font-weight: bold;
            color: #111827;
        }

        .summary-table .total-row td {
            border-top: 1px solid #d1d5db;
            padding-top: 13px;
            font-size: 16px;
            font-weight: bold;
        }

        .remarks-box {
            margin-top: 25px;
        }

        .footer {
            margin-top: 70px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="header">
            <div class="company">
                <h1>FlowERP</h1>
                <p>Enterprise Inventory, Sales and Workflow Automation System</p>
                <p>Penang, Malaysia</p>
                <p>Email: admin@flowerp.com</p>
            </div>

            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <p><strong>{{ $sale->invoice_no }}</strong></p>
                <p>Date: {{ $sale->created_at->format('d M Y, h:i A') }}</p>
                <p>Created By: {{ $sale->user->name ?? '-' }}</p>

                @if ($sale->payment_status === 'paid')
                    <span class="badge badge-paid">Paid</span>
                @elseif ($sale->payment_status === 'partial')
                    <span class="badge badge-partial">Partial</span>
                @else
                    <span class="badge badge-unpaid">Unpaid</span>
                @endif
            </div>

            <div class="clear"></div>
        </div>

        <div class="section">
            <div class="section-title">Bill To</div>

            <div class="box">
                <p><strong>{{ $sale->customer->name ?? 'Walk-in Customer' }}</strong></p>

                @if ($sale->customer)
                    <p>Phone: {{ $sale->customer->phone ?? '-' }}</p>
                    <p>Email: {{ $sale->customer->email ?? '-' }}</p>
                    <p>Address: {{ $sale->customer->address ?? '-' }}</p>
                @else
                    <p>No customer record selected.</p>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="section-title">Invoice Items</div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Product</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 15%;" class="text-right">Unit Price</th>
                        <th style="width: 10%;" class="text-right">Qty</th>
                        <th style="width: 15%;" class="text-right">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product->name ?? '-' }}</strong><br>
                                <span style="color: #6b7280;">
                                    {{ $item->product->category->name ?? '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $item->product->sku ?? '-' }}
                            </td>

                            <td class="text-right">
                                RM {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="text-right">
                                {{ $item->quantity }}
                            </td>

                            <td class="text-right">
                                RM {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">RM {{ number_format($sale->subtotal, 2) }}</td>
                </tr>

                <tr>
                    <td class="label">Discount</td>
                    <td class="value">RM {{ number_format($sale->discount, 2) }}</td>
                </tr>

                <tr>
                    <td class="label">Tax</td>
                    <td class="value">RM {{ number_format($sale->tax, 2) }}</td>
                </tr>

                <tr class="total-row">
                    <td class="label">Total</td>
                    <td class="value">RM {{ number_format($sale->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="clear"></div>

        @if ($sale->remarks)
            <div class="section remarks-box">
                <div class="section-title">Remarks</div>

                <div class="box">
                    {{ $sale->remarks }}
                </div>
            </div>
        @endif

        <div class="footer">
            This invoice was generated by FlowERP. Thank you for your business.
        </div>

    </div>
</body>

</html>
