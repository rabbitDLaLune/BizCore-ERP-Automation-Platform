<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .page {
            padding: 24px;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 12px;
        }

        .company {
            float: left;
            width: 55%;
        }

        .report-info {
            float: right;
            width: 40%;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        h1 {
            margin: 0;
            font-size: 22px;
        }

        h2 {
            margin: 0;
            font-size: 18px;
        }

        p {
            margin: 4px 0;
        }

        .muted {
            color: #6b7280;
        }

        .summary {
            width: 100%;
            margin-top: 18px;
            margin-bottom: 18px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #e5e7eb;
            padding: 10px;
            background: #f9fafb;
        }

        .summary-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #111827;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.report-table th {
            background: #f3f4f6;
            color: #374151;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        table.report-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .paid {
            background: #dcfce7;
            color: #166534;
        }

        .partial {
            background: #fef9c3;
            color: #854d0e;
        }

        .unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 24px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header">
            <div class="company">
                <h1>FlowERP</h1>
                <p class="muted">Enterprise Inventory, Sales and Workflow Automation System</p>
                <p class="muted">Penang, Malaysia</p>
            </div>

            <div class="report-info">
                <h2>Sales Report</h2>
                <p class="muted">Generated: {{ now()->format('d M Y, h:i A') }}</p>
                <p class="muted">
                    Date From: {{ $dateFrom ?: 'All' }} |
                    Date To: {{ $dateTo ?: 'All' }}
                </p>
                <p class="muted">
                    Payment Status: {{ $paymentStatus ? ucfirst($paymentStatus) : 'All' }}
                </p>
            </div>

            <div class="clear"></div>
        </div>

        <table class="summary">
            <tr>
                <td>
                    <div class="summary-label">Total Invoices</div>
                    <div class="summary-value">{{ $totalInvoices }}</div>
                </td>

                <td>
                    <div class="summary-label">Total Sales</div>
                    <div class="summary-value">RM {{ number_format($totalSalesAmount, 2) }}</div>
                </td>

                <td>
                    <div class="summary-label">Paid Amount</div>
                    <div class="summary-value">RM {{ number_format($paidAmount, 2) }}</div>
                </td>

                <td>
                    <div class="summary-label">Unpaid Amount</div>
                    <div class="summary-value">RM {{ number_format($unpaidAmount, 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Invoice</th>
                    <th style="width: 24%;">Customer</th>
                    <th style="width: 14%;">Payment</th>
                    <th style="width: 22%;">Created By</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 10%;" class="text-right">Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                        <td>
                            @if ($sale->payment_status === 'paid')
                                <span class="badge paid">Paid</span>
                            @elseif ($sale->payment_status === 'partial')
                                <span class="badge partial">Partial</span>
                            @else
                                <span class="badge unpaid">Unpaid</span>
                            @endif
                        </td>
                        <td>{{ $sale->user->name ?? '-' }}</td>
                        <td>{{ $sale->created_at->format('d M Y') }}</td>
                        <td class="text-right">RM {{ number_format($sale->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No sales records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            This report was generated by FlowERP.
        </div>
    </div>
</body>

</html>
