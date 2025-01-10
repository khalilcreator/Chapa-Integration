<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="col-md-4 mx-auto my-5">
        <h1 class="text-success text-center">✅ Payment Successful!</h1>
        <p class="text-center mt-3">Thank you for your payment. Your transaction has been completed.</p>

        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                Payment Details
            </div>
           {{-- <?php // dd($transactionDetails) ; ?> --}}
            <div class="card-body">
                <p><strong>Transaction ID:</strong> {{ $transactionDetails['transaction_id'] ?? 'N/A' }}</p>
                <p><strong>Amount:</strong> {{ $transactionDetails['amount'] ?? 'N/A' }} {{ $transactionDetails['currency'] ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $transactionDetails['email'] ?? 'N/A' }}</p>
                <p><strong>Name:</strong> {{ $transactionDetails['first_name'] ?? 'N/A' }} {{ $transactionDetails['last_name'] ?? 'N/A' }}</p>
                <p><strong>Payment Method:</strong> {{ $transactionDetails['payment_method'] ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($transactionDetails['status'] ?? 'failed') }}</p>
                <p><strong>Date:</strong> {{ $transactionDetails['created_at'] ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
</body>
</html>
