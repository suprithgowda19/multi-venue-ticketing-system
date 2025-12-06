<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ticket Booking' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f7f8fa;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-sm py-4 px-6">
        <h1 class="text-xl font-bold text-gray-800">
            BIFFES Ticket Booking
        </h1>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 text-gray-500 text-sm">
        © {{ date('Y') }} BIFFES · All Rights Reserved
    </footer>

</body>

</html>
