<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gym Diary -筋トレ記録アプリ-</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" href="favicon.ico">
</head>

<body class="font-sans min-h-screen flex flex-col">
    <!-- Main content -->
    <main class="flex-grow flex flex-col md:flex-row">
        <!-- Left side: Hero section -->
        <div class="w-full md:w-1/2 bg-white p-8 md:p-16 flex flex-col justify-center items-center md:items-start">
            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logo_color-iW3g6PgJe61RpqJvLbYRvZISzshTOD.png"
                alt="Gym Diary Logo" class="mb-8 w-32 h-32" />
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 text-center md:text-left">
                Your Fitness Journey Starts Here
            </h1>
            <p class="text-xl text-gray-600 mt-4 text-center md:text-left">
                Join Gym Diary now and redefine how you train and connect with personal trainers anytime, anywhere.
            </p>
        </div>

        <!-- Right side: Signup form -->
        <div class="w-full md:w-1/2 bg-gray-50 p-8 md:p-16 flex flex-col justify-center">
            <div id="thankYouMessage" class="hidden text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Thank you for joining!</h2>
                <p class="text-gray-600">We'll notify you when Gym Diary launches.</p>
            </div>

            <form id="signupForm" class="space-y-6" method="POST" action="#">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-h-[48px]"
                        placeholder="Your Name" disabled />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-h-[48px]"
                        placeholder="Your Email Address" disabled />
                </div>
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg px-6 py-3 min-h-[48px] transition-all ease-in-out duration-300"
                    disabled>
                    Join the Waitlist
                </button>
            </form>
            <p class="text-sm text-gray-500 mt-4 text-center">準備中</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 py-4">
        <div class="container mx-auto px-4 text-center">
            <a href="/terms" class="text-sm text-gray-500 hover:text-gray-700 mr-4">Terms of Service</a>
            <a href="/privacy" class="text-sm text-gray-500 hover:text-gray-700">Privacy Policy</a>
        </div>
    </footer>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;

            if (name && email) {
                document.getElementById('signupForm').classList.add('hidden');
                document.getElementById('thankYouMessage').classList.remove('hidden');
            }
        });
    </script>
</body>

</html>
