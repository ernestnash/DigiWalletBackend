<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            padding: 1em;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
        }

        .user-info,
        .company-info {
            width: 50%
        }

        .user-info p,
        .company-info p {
            margin: 0;
        }

        h3 {
            margin-top: 1em;
        }

        .account-number {
            font-weight: bold;
            color: #333;
        }
    </style>
    <title>DigiWallet {{ $userData->full_name }} Info</title>
</head>

<body>
    <header>
        <div class="user-info">
            <p>Account Number: <span class="account-number">{{ $userData->id }}</span></p>
            <p>Full Name: {{ $userData->full_name }}</p>
            <p>Phone Number: {{ $userData->phone_number }}</p>
        </div>

        <div class="company-info">
            <div class="logo">
                <!-- <i class="fas fa-wallet"></i>--> DigiWallet
            </div>
            <p>123 Company Street</p>
            <p>City, Country</p>
            <p>Phone: +123 456 7890</p>
        </div>
    </header>

    <h3>Account Holder's Information</h3>

    <div class="user-info-body">
        <p>Account Number: <span class="account-number">{{ $userData->id }}</span></p>
        <p>Full Name: {{ $userData->full_name }}</p>
        <p>Phone Number: {{ $userData->phone_number }}</p>
    </div>

</body>

</html>
