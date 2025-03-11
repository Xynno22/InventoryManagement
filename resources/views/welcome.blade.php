<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to StockSense</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-gray-800 text-white p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">StockSense</h1>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="flex flex-col items-center justify-center text-center py-20 px-6">
        <h1 class="text-4xl font-extrabold text-gray-900">Welcome to StockSense</h1>
        <p class="mt-4 text-lg text-gray-700">Your all-in-one solution for <b>INVENTORY</b> and <b>FINANCE MANAGEMENT.</b></p>
        <a href="{{ url('/register') }}"
            class="mt-6 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-500 transition">
            Get Started
        </a>
    </section>

    <!-- Features Section -->
    <section class="max-w-6xl mx-auto py-16 px-6">
        <h2 class="text-center text-3xl font-bold text-gray-800">Why Choose StockSense?</h2>
        <div class="grid md:grid-cols-3 gap-8 mt-12">
            <div class="p-6 bg-white rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-indigo-600">Real-Time Inventory</h3>
                <p class="text-gray-700 mt-2">Monitor your stock levels with real-time updates.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-indigo-600">Transparent Finance</h3>
                <p class="text-gray-700 mt-2">Track income and expenses with easy-to-read reports.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-indigo-600">Automated Reports</h3>
                <p class="text-gray-700 mt-2">Generate reports automatically for better decision-making.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-6">
        <p>&copy; 2025 StockSense - All Rights Reserved</p>
    </footer>

</body>

</html>
