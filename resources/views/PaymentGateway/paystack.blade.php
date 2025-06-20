<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with Paystack</title>
</head>
<body>
    <form id="payment-form" action="{{ url('paystack/initialize') }}" method="POST">
        @csrf
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <input type="number" name="amount" id="amount" placeholder="Enter amount (in Kobo)" required>
        <button type="submit">Pay Now</button>
    </form>

</body>
</html>
