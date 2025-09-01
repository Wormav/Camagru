<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Photo Studio</h1>

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
                <button id="capture" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" disabled>
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
            <h2 class="text-xl font-semibold mb-4">Overlays</h2>
            <div id="overlays" class="grid grid-cols-3 gap-2">
                <div class="overlay-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-overlay="frame1">
                    <div class="w-full h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded flex items-center justify-center text-white text-xs">Frame 1</div>
                </div>
                <div class="overlay-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-overlay="frame2">
                    <div class="w-full h-16 bg-gradient-to-r from-green-400 to-blue-400 rounded flex items-center justify-center text-white text-xs">Frame 2</div>
                </div>
                <div class="overlay-option border-2 border-gray-200 rounded p-2 cursor-pointer hover:border-blue-500" data-overlay="sticker1">
                    <div class="w-full h-16 bg-gradient-to-r from-yellow-400 to-red-400 rounded flex items-center justify-center text-white text-xs">Sticker 1</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">My Photos</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="aspect-square bg-gray-200 rounded flex items-center justify-center text-gray-500 text-sm">
                No photos
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/camera.js"></script>
