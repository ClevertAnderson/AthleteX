<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATHLETIX Login</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <!-- offline tailwind css below -->
     <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?> 
</head>
<body class="flex flex-col items-center min-h-screen bg-gradient-to-br from-green-900 to-green-800">

    <!-- Header -->
    <div class="w-full bg-green-900/70 p-6 shadow-lg border-b border-green-700/50 backdrop-blur-sm">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-white tracking-wide">SPORTS DEVELOPMENT OFFICE</h1>
        </div>
    </div>

    <!-- Login Container -->
    <div class="flex-1 flex items-center justify-center w-full p-6">
        <div class="bg-[#2e4e1f] p-8 rounded-xl shadow-2xl w-full max-w-md border border-green-800/50">
            
            <!-- Logo -->
            <div class="text-center mb-6">
                <img src="<?php echo e(asset('images/UC.jpg')); ?>" alt="UC Logo" class="mx-auto mb-4 w-24 h-24 rounded-full border-2 border-green-700">
                <h2 class="text-2xl font-bold text-white">ATHLETIX Login</h2>
            </div>

            <!-- Error Message -->
            <?php if($errors->any()): ?>
                <div class="bg-red-900/50 text-red-200 p-3 rounded-lg mb-6 border border-red-700/50 text-sm">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-5">
                    <label for="email" class="block text-green-200 font-medium mb-2 text-sm">Email</label>
                    <input type="email" name="email" id="email" 
                        class="w-full bg-black/20 border border-green-700/50 rounded-lg px-4 py-2.5 text-white placeholder-green-200/50 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition" 
                        placeholder="Enter your email" 
                        required autofocus>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-green-200 font-medium mb-2 text-sm">Password</label>
                    <input type="password" name="password" id="password" 
                        class="w-full bg-black/20 border border-green-700/50 rounded-lg px-4 py-2.5 text-white placeholder-green-200/50 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition" 
                        placeholder="Enter your password" 
                        required>
                </div>

                <button type="submit" 
                    class="w-full bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg font-semibold transition-colors shadow-md hover:shadow-lg">
                    Login
                </button>
            </form>

            <!-- Footer Links -->
            <div class="mt-6 text-center text-sm text-green-200">
                <a href="#" class="text-green-400 hover:text-green-300 transition-colors hover:underline">Forgot Password?</a>
                <span class="mx-2">•</span>
                <a href="#" class="text-green-400 hover:text-green-300 transition-colors hover:underline">Contact Admin</a>
            </div>
        </div>
    </div>

</body>
</html>
<?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/log/login.blade.php ENDPATH**/ ?>