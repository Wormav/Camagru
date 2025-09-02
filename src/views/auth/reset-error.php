<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <div class="text-center mb-6">
        <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset link invalid</h2>
        <p class="text-sm text-gray-600">
            This password reset link is invalid or has expired.
        </p>
    </div>

    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Possible reasons:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>The link has already been used</li>
                        <li>The link has expired</li>
                        <li>The link is malformed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center space-y-4">
        <a href="/forgot-password"
           class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-block">
            Request new reset link
        </a>

        <p class="text-sm text-gray-600">
            <a href="/login" class="text-blue-500 hover:text-blue-600">Back to login</a>
        </p>
    </div>
</div>
