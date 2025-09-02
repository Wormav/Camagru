<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Camagru Gallery</h1>
        <p class="text-lg text-gray-600">Discover our community's creations</p>
        <?php if (isset($totalImages)): ?>
            <p class="text-sm text-gray-500 mt-2"><?= $totalImages ?> images total</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($images)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($images as $image): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-64 bg-gray-200 overflow-hidden">
                        <img src="/uploads/<?= htmlspecialchars($image['filename']) ?>"
                             alt="Photo by <?= htmlspecialchars($image['username']) ?>"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500">By <?= htmlspecialchars($image['username']) ?></span>
                                <span class="text-xs text-gray-400"><?= date('M j, Y', strtotime($image['created_at'])) ?></span>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex items-center text-red-500">
                                    <span class="mr-1">♥</span>
                                    <span class="text-sm"><?= $image['like_count'] ?></span>
                                </div>
                                <div class="flex items-center text-blue-500">
                                    <span class="mr-1">💬</span>
                                    <span class="text-sm"><?= $image['comment_count'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center">
                <nav class="flex space-x-2">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>"
                           class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                            ← Previous
                        </a>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    if ($startPage > 1): ?>
                        <a href="?page=1" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="px-3 py-2 text-gray-500">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                        <?php if ($page == $currentPage): ?>
                            <span class="px-3 py-2 bg-blue-500 text-white rounded"><?= $page ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $page ?>"
                               class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                                <?= $page ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="px-3 py-2 text-gray-500">...</span>
                        <?php endif; ?>
                        <a href="?page=<?= $totalPages ?>"
                           class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>"
                           class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                            Next →
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📷</div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">No images yet</h3>
            <p class="text-gray-500">Be the first to share your creation!</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/camera"
                   class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    Create your first image
                </a>
            <?php else: ?>
                <p class="mt-4 text-sm text-gray-400">
                    <a href="/auth/login" class="text-blue-500 hover:text-blue-600">Login</a> to start creating
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
