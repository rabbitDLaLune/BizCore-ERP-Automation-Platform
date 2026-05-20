<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>

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

        .low-stock {
            color: #991b1b;
            font-weight: bold;
        }

        .normal-stock {
            color: #166534;
            font-weight: bold;
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
                <h2>Inventory Report</h2>
                <p class="muted">Generated: {{ now()->format('d M Y, h:i A') }}</p>
                <p class="muted">Search: {{ $search ?: 'All' }}</p>
                <p class="muted">Stock Status: {{ $stockStatus ? ucwords(str_replace('_', ' ', $stockStatus)) : 'All' }}
                </p>
            </div>

            <div class="clear"></div>
        </div>

        <table class="summary">
            <tr>
                <td>
                    <div class="summary-label">Total Products</div>
                    <div class="summary-value">{{ $totalProducts }}</div>
                </td>

                <td>
                    <div class="summary-label">Low Stock</div>
                    <div class="summary-value">{{ $lowStockProducts }}</div>
                </td>

                <td>
                    <div class="summary-label">Total Quantity</div>
                    <div class="summary-value">{{ $totalStockQuantity }}</div>
                </td>

                <td>
                    <div class="summary-label">Stock Value</div>
                    <div class="summary-value">RM {{ number_format($totalStockValue, 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 14%;">SKU</th>
                    <th style="width: 26%;">Product</th>
                    <th style="width: 18%;">Category</th>
                    <th style="width: 10%;" class="text-right">Qty</th>
                    <th style="width: 10%;" class="text-right">Reorder</th>
                    <th style="width: 11%;" class="text-right">Cost</th>
                    <th style="width: 11%;" class="text-right">Value</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td
                            class="text-right {{ $product->quantity <= $product->reorder_level ? 'low-stock' : 'normal-stock' }}">
                            {{ $product->quantity }}
                        </td>
                        <td class="text-right">{{ $product->reorder_level }}</td>
                        <td class="text-right">RM {{ number_format($product->cost_price, 2) }}</td>
                        <td class="text-right">RM {{ number_format($product->quantity * $product->cost_price, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">No inventory records found.</td>
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
