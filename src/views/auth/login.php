<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login</h2>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
            <div class="flex">
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Please correct the following errors:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login" class="space-y-4">
        <?= CSRFProtection::getTokenField() ?>
        <div>
            <label for="login" class="block text-sm font-medium text-gray-700">Username or Email</label>
            <input type="text" id="login" name="login" required
                   value="<?= htmlspecialchars($login ?? '') ?>"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Sign In
        </button>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="/register" class="text-blue-500 hover:text-blue-600">Create account</a> |
        <a href="/forgot-password" class="text-blue-500 hover:text-blue-600">Forgot password</a>
    </div>
</div>
