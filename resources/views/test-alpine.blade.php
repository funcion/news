<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Alpine.js</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Test Alpine.js</h1>
    
    <!-- Test simple -->
    <div x-data="{ count: 0 }" class="mb-8 p-4 border rounded">
        <h2 class="text-lg font-semibold mb-2">Simple Counter Test</h2>
        <button @click="count++" class="px-4 py-2 bg-blue-500 text-white rounded">
            Increment
        </button>
        <span class="ml-4" x-text="count"></span>
    </div>
    
    <!-- Test mobile menu -->
    <div x-data="{ mobileMenuOpen: false }" class="mb-8 p-4 border rounded">
        <h2 class="text-lg font-semibold mb-2">Mobile Menu Test</h2>
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="px-4 py-2 bg-green-500 text-white rounded">
            Toggle Menu
        </button>
        <div x-show="mobileMenuOpen" class="mt-4 p-4 bg-gray-100 rounded">
            Menu is open!
        </div>
    </div>
    
    <!-- Test dark mode -->
    <div x-data="{ isDarkMode: false }" class="mb-8 p-4 border rounded">
        <h2 class="text-lg font-semibold mb-2">Dark Mode Test</h2>
        <button @click="isDarkMode = !isDarkMode" 
                class="px-4 py-2 bg-purple-500 text-white rounded">
            Toggle Dark Mode
        </button>
        <div class="mt-4" x-text="isDarkMode ? 'Dark Mode ON' : 'Dark Mode OFF'"></div>
    </div>
    
    <script>
        console.log('Alpine.js test page loaded');
        console.log('Alpine available:', typeof Alpine !== 'undefined');
        if (typeof Alpine !== 'undefined') {
            console.log('Alpine version:', Alpine.version);
        }
    </script>
</body>
</html>