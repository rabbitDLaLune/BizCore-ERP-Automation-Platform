<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Request Report</title>

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

        .pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .approved {
            background: #dcfce7;
            color: #166534;
        }

        .rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .completed {
            background: #dbeafe;
            color: #1e40af;
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
                <h2>Purchase Request Report</h2>
                <p class="muted">Generated: {{ now()->format('d M Y, h:i A') }}</p>
                <p class="muted">
                    Date From: {{ $dateFrom ?: 'All' }} |
                    Date To: {{ $dateTo ?: 'All' }}
                </p>
                <p class="muted">
                    Status: {{ $status ? ucfirst($status) : 'All' }}
                </p>
            </div>

            <div class="clear"></div>
        </div>

        <table class="summary">
            <tr>
                <td>
                    <div class="summary-label">Total Requests</div>
                    <div class="summary-value">{{ $totalRequests }}</div>
                </td>

                <td>
                    <div class="summary-label">Pending</div>
                    <div class="summary-value">{{ $pendingRequests }}</div>
                </td>

                <td>
                    <div class="summary-label">Approved</div>
                    <div class="summary-value">{{ $approvedRequests }}</div>
                </td>

                <td>
                    <div class="summary-label">Estimated Total</div>
                    <div class="summary-value">RM {{ number_format($estimatedTotal, 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Request No</th>
                    <th style="width: 22%;">Supplier</th>
                    <th style="width: 18%;">Requester</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 14%;" class="text-right">Estimated Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($purchaseRequests as $purchaseRequest)
                    <tr>
                        <td>{{ $purchaseRequest->request_no }}</td>
                        <td>{{ $purchaseRequest->supplier->name ?? '-' }}</td>
                        <td>{{ $purchaseRequest->requester->name ?? '-' }}</td>
                        <td>
                            @if ($purchaseRequest->status === 'pending')
                                <span class="badge pending">Pending</span>
                            @elseif ($purchaseRequest->status === 'approved')
                                <span class="badge approved">Approved</span>
                            @elseif ($purchaseRequest->status === 'rejected')
                                <span class="badge rejected">Rejected</span>
                            @else
                                <span class="badge completed">Completed</span>
                            @endif
                        </td>
                        <td>{{ $purchaseRequest->created_at->format('d M Y') }}</td>
                        <td class="text-right">RM {{ number_format($purchaseRequest->estimated_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No purchase request records found.</td>
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
