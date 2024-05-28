<!DOCTYPE html>
<html>
<head>
    <title>Transaction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Transaction Details</h1>
    <table>
        <tr>
            <th>Name</th>
            <td>{{ $transaction->name }}</td>
        </tr>
        <tr>
            <th>Category</th>
            <td>{{ $transaction->category->name }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>{{ $transaction->amount }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $transaction->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $transaction->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
