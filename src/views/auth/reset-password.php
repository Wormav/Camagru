<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Reset Password</h2>
    <p class="text-center text-sm text-gray-600 mb-6">
        Enter your new password below.
    </p>

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

    <form method="POST" action="/reset-password" class="space-y-4">
        <?= CSRFProtection::getTokenField() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <input id="confirm_password" name="confirm_password" type="password" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
            <h4 class="text-sm font-medium text-gray-800 mb-2">Password requirements:</h4>
            <ul class="text-xs text-gray-600 space-y-1">
                <li>• At least 8 characters long</li>
                <li>• Contains at least one uppercase letter</li>
                <li>• Contains at least one lowercase letter</li>
                <li>• Contains at least one number</li>
            </ul>
        </div>

        <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Reset password
        </button>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="/login" class="text-blue-500 hover:text-blue-600">Back to login</a>
    </div>
</div>
