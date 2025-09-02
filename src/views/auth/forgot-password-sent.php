<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <div class="text-center mb-6">
        <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a1 1 0 001.42 0L21 7" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Check your email</h2>
        <p class="text-sm text-gray-600">
            If an account with the email <strong><?= htmlspecialchars($email) ?></strong> exists,
            we've sent you a password reset link.
        </p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">What to do next:</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Check your email inbox (and spam folder)</li>
                        <li>Click the reset link in the email</li>
                        <li>Follow the instructions to set a new password</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center text-sm space-y-2">
        <p class="text-gray-600">
            Didn't receive the email? Check your spam folder or
            <a href="/forgot-password" class="text-blue-500 hover:text-blue-600">try again</a>
        </p>
        <p class="text-gray-600">
            <a href="/login" class="text-blue-500 hover:text-blue-600">Back to login</a>
        </p>
    </div>
</div>
