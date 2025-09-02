<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Photo Studio</h1>

    <div id="csrf-token" data-token="<?= htmlspecialchars($csrfToken) ?>" style="display: none;"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Webcam</h2>
            <div id="camera-container" class="relative">
                <video id="video" class="w-full h-64 bg-gray-200 rounded" autoplay muted></video>
                <canvas id="canvas" class="hidden"></canvas>
                <div id="overlay-preview" class="absolute inset-0 pointer-events-none"></div>
            </div>

            <div class="mt-4 flex justify-center space-x-2">
                <button id="start-camera" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Start Camera
                </button>
                <button id="capture" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400 transition-colors" disabled>
                    Capture
                </button>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Or upload an image:</label>
                <input type="file" id="file-upload" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Frames</h2>
            <div id="frames" class="grid grid-cols-2 gap-2 mb-6">
                <div class="frame-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-frame="none">
                    <div class="w-full h-16 bg-gray-100 rounded flex items-center justify-center text-gray-500 text-xs">No Frame</div>
                </div>
                <div class="frame-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-frame="classic">
                    <div class="w-full h-16 bg-gradient-to-r from-amber-600 to-yellow-600 rounded flex items-center justify-center text-white text-xs border-4 border-amber-800">Classic</div>
                </div>
                <div class="frame-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-frame="modern">
                    <div class="w-full h-16 bg-gradient-to-r from-gray-600 to-gray-800 rounded flex items-center justify-center text-white text-xs border-2 border-gray-900">Modern</div>
                </div>
                <div class="frame-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-frame="vintage">
                    <div class="w-full h-16 bg-gradient-to-r from-orange-600 to-red-600 rounded flex items-center justify-center text-white text-xs border-4 border-orange-800">Vintage</div>
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-4">Emojis</h2>
            <div id="emojis" class="grid grid-cols-4 gap-2">
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="none">
                    <div class="w-full h-12 bg-gray-100 rounded flex items-center justify-center text-gray-500 text-xs">None</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="😊">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">😊</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="❤️">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">❤️</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="🎉">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">🎉</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="🔥">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">🔥</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="✨">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">✨</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="🌟">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">🌟</div>
                </div>
                <div class="emoji-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-emoji="💯">
                    <div class="w-full h-12 bg-gray-50 rounded flex items-center justify-center text-2xl">💯</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">My Photos</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php if (!empty($userImages)): ?>
                <?php foreach ($userImages as $image): ?>
                    <div class="relative group">
                        <div class="aspect-square bg-gray-100 rounded overflow-hidden flex items-center justify-center p-1">
                            <img src="/uploads/<?= htmlspecialchars($image['filename']) ?>"
                                 alt="Photo"
                                 class="max-w-full max-h-full object-contain">
                        </div>
                        <button onclick="deleteImage(<?= $image['id'] ?>)"
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                            ×
                        </button>
                        <div class="text-xs text-gray-500 mt-1 text-center">
                            <?= date('M j', strtotime($image['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="aspect-square bg-gray-200 rounded flex items-center justify-center text-gray-500 text-sm col-span-full">
                    No photos yet
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="/assets/js/camera.js"></script>
