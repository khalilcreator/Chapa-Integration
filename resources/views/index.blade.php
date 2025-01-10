<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  {{-- <style>
        /* Right-align search bar */
        .dataTables_filter {
            float: right;
            text-align: center;
        }

        /* Right-align pagination controls */
        .dataTables_paginate {
            float: right;
        }

        /* Optional: Adjust the alignment of the table's bottom row */
        .dataTables_info {
            float: left;
        }
    </style> --}}
</head>
<body>

<div class="container">
    <h2 class="mb-4">Transaction List</h2>
    <table id="transactionsTable" class="table table-striped table-bordered w-100">
        <thead>
            <tr>

                <th>Reference ID</th>
                {{-- <th>Transaction Type</th> --}}
                {{-- <th>Created At</th> --}}
                <th>Currency</th>
                <th>Amount</th>
                <th>Charge</th>
                <th>Email</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allTransactions as $transaction)
            <tr>
                <td>{{ $transaction['ref_id'] }}</td>
                {{-- <td>{{ $transaction['type'] }}</td>
                <td>{{ \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d H:i:s') }}</td> --}}
                <td>{{ $transaction['currency'] }}</td>
                <td>{{ $transaction['amount'] }}</td>
                <td>{{ $transaction['charge'] }}</td>
                <td>{{ $transaction['email'] }}</td>
                <td>{{ $transaction['payment_method'] }}</td>
                <td>{{ $transaction['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- jQuery (needed for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.12.1/js/jquery.dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.12.1/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JS (for Bootstrap components like modals, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#transactionsTable').DataTable();
    });
</script>

</html>
