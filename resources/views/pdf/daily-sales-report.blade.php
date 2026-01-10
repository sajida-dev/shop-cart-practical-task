<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Daily Sales Report</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .report {
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .instructions {
            font-size: 14px;
            color: #555;
            margin-bottom: 25px;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .summary {
            text-align: right;
            font-size: 15px;
        }
        

        .summary div {
            margin-bottom: 8px;
        }

        .summary span {
            display: inline-block;
            width: 140px;
            text-align: right;
            font-weight: bold;
        }

        hr {
            border: 0;
            border-top: 1px solid #bbb;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead {
            background: #e1e6ec;
        }

        th,
        td {
            border: 1px solid #bfbfbf;
            padding: 8px;
            text-align: center;
        }

        th {
            font-weight: bold;
            white-space: nowrap;
        }

        tbody tr:nth-child(even) {
            background: #f3f6f9;
        }

        .text-left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .muted {
            color: #888;
        }
    </style>
</head>

<body>

    <div class="report">
        <h1>Daily Sales Report</h1>

        <div class="instructions">
            This report provides a comprehensive overview of the sales activities and performance for {{ $date }}. It
            includes
            detailed information on total sales, revenue generated, and product-wise sales.
        </div>

        <div class="top-section">
            <div class="left"></div>
            <div class="summary">
                @php
                    $salesAmount = $soldProducts->sum('total');
                    $salesTax = 1000; 
                    $salesTotal = $salesAmount + $salesTax;
                @endphp
                <div>SALES AMOUNT <span>${{ number_format($salesAmount, 2) }}</span></div>
                <div>SALES TAX <span>${{ number_format($salesTax, 2) }}</span></div>
                <hr>
                <div><strong>SALES TOTAL</strong> <span><strong>${{ number_format($salesTotal, 2) }}</strong></span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ITEM NO</th>
                    <th>ITEM NAME</th>
                    <th>PRICE</th>
                    <th>QTY</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($soldProducts as $product)
                <tr>
                    <td>{{ $product['id'] ?? '-' }}</td>
                    <td class="text-left">{{ $product['name'] }}</td>
                    <td class="right">${{ number_format($product['price'] ?? ($product['total'] / max($product['quantity'],1)), 2) }}</td>
                    <td class="right">{{ $product['quantity'] }}</td>
                    <td class="right">${{ number_format($product['total'], 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="muted">—</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
