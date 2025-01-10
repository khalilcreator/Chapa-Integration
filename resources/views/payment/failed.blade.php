<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5 text-center">
        <h1 class="text-danger">⚠️ Payment Failed!</h1>
        <p class="mt-3">Unfortunately, we couldn't process your payment. Please try again.</p>
        <a href="{{ url('/payment') }}" class="btn btn-warning mt-4">Try Again</a>
    </div>
</body>
</html>
