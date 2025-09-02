<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <!-- Profile Picture Section -->
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Picture</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your profile picture. Only JPG, PNG and GIF images are allowed.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <div id="profile-picture-container" class="relative">
                                <?php if ($user['profile_picture']): ?>
                                    <img id="current-profile-picture" class="h-20 w-20 object-cover rounded-full"
                                         src="/uploads/profiles/<?= htmlspecialchars($user['profile_picture']) ?>"
                                         alt="<?= htmlspecialchars($user['username']) ?>">
                                <?php else: ?>
                                    <div id="default-profile-picture" class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                        <svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <img id="preview-profile-picture" class="h-20 w-20 object-cover rounded-full hidden"
                                     alt="Preview">
                            </div>
                        </div>

                        <form method="POST" action="/profile/upload-picture" enctype="multipart/form-data" class="flex items-center space-x-4">
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="profile_picture" id="profile-picture-input" accept="image/*" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </label>
                            <button type="submit" id="upload-button" disabled
                                    class="bg-gray-400 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 cursor-not-allowed">
                                Upload
                            </button>
                            <button type="button" id="cancel-button" class="hidden bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information Section -->
    <div class="bg-white shadow rounded-lg mt-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your account's profile information and email address.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <!-- Messages de succès/erreur -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        <?= htmlspecialchars($_SESSION['success']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        <?= htmlspecialchars($_SESSION['error']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
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

                    <form method="POST" action="/profile/update" class="space-y-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" name="username" id="username" required
                                       value="<?= htmlspecialchars($username ?? $user['username']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <input type="email" name="email" id="email" required
                                       value="<?= htmlspecialchars($email ?? $user['email']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Change Password</h4>
                            <p class="text-sm text-gray-600 mb-4">Leave password fields empty if you don't want to change your password.</p>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="col-span-6 sm:col-span-2">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="new_password" id="new_password"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="col-span-6 sm:col-span-2">
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="bg-white shadow rounded-lg mt-6">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Account Information</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Member since</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= date('F j, Y', strtotime($user['created_at'])) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email verified</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php if ($user['email_verified']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Verified
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Not verified
                            </span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= date('F j, Y g:i A', strtotime($user['updated_at'])) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Notifications</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php if ($user['notifications_enabled']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Enabled
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Disabled
                            </span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile-picture-input');
    const uploadButton = document.getElementById('upload-button');
    const cancelButton = document.getElementById('cancel-button');
    const previewImage = document.getElementById('preview-profile-picture');
    const currentImage = document.getElementById('current-profile-picture');
    const defaultImage = document.getElementById('default-profile-picture');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Validation du type de fichier
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, or GIF).');
                fileInput.value = '';
                return;
            }

            // Validation de la taille (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('Image file is too large. Maximum size is 5MB.');
                fileInput.value = '';
                return;
            }

            // Créer la preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.classList.remove('hidden');

                // Masquer l'image actuelle ou l'icône par défaut
                if (currentImage) {
                    currentImage.classList.add('hidden');
                }
                if (defaultImage) {
                    defaultImage.classList.add('hidden');
                }

                // Activer le bouton d'upload et afficher le bouton cancel
                uploadButton.disabled = false;
                uploadButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                uploadButton.classList.add('bg-blue-500', 'hover:bg-blue-600');
                uploadButton.classList.remove('focus:ring-gray-400');
                uploadButton.classList.add('focus:ring-blue-500');

                cancelButton.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            resetPreview();
        }
    });

    cancelButton.addEventListener('click', function() {
        fileInput.value = '';
        resetPreview();
    });

    function resetPreview() {
        // Masquer la preview
        previewImage.classList.add('hidden');

        // Réafficher l'image actuelle ou l'icône par défaut
        if (currentImage) {
            currentImage.classList.remove('hidden');
        }
        if (defaultImage) {
            defaultImage.classList.remove('hidden');
        }

        // Désactiver le bouton d'upload et masquer le bouton cancel
        uploadButton.disabled = true;
        uploadButton.classList.add('bg-gray-400', 'cursor-not-allowed');
        uploadButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        uploadButton.classList.add('focus:ring-gray-400');
        uploadButton.classList.remove('focus:ring-blue-500');

        cancelButton.classList.add('hidden');
    }
});
</script>
