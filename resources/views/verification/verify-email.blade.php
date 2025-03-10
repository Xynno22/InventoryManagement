<!DOCTYPE html>
<html lang="en">

<head>
    <title>Email Verification - Your App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-lg p-6 md:p-10 w-full max-w-md text-center space-y-6">
        <h2 class="text-2xl font-bold text-gray-900">Email Verification Required</h2>
        <p class="text-gray-600">
            Please verify your email address to continue using our service. 
            If you haven't received the email, we can resend it for you.
        </p>

        <form method="POST" action="/email/verification-notification" class="space-y-4">
            @csrf
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Resend Verification Email
            </button>
        </form>

        <p class="text-sm text-gray-500">
            Already verified? 
            <a href="/" class="text-indigo-600 hover:text-indigo-500 font-semibold">Go to Login</a>
        </p>
    </div>

</body>
</html>
