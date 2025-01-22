<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="profile-actions.css">
</head>
<body>
    <div class="success-popup">
        <p>Your payment was successful! Redirecting to place order...</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = "place-order.php";
        }, 2000);
    </script>
</body>
</html>
