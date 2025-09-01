<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Create Account</h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
            <ul class="text-red-600 text-sm space-y-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register" class="space-y-4">
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">3-50 characters, letters, numbers and underscores only</p>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters with uppercase, lowercase and number</p>
        </div>

        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Create Account
        </button>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="/login" class="text-blue-500 hover:text-blue-600">Already have an account? Sign in</a>
    </div>
</div>
