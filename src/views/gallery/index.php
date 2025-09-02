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
                    <a href="/image/<?= $image['id'] ?>" class="block">
                        <div class="h-80 bg-gray-100 flex items-center justify-center p-2">
                            <img src="/uploads/<?= htmlspecialchars($image['filename']) ?>"
                                 alt="Photo by <?= htmlspecialchars($image['username']) ?>"
                                 class="max-w-full max-h-full object-contain hover:scale-105 transition-transform">
                        </div>
                    </a>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <a href="/image/<?= $image['id'] ?>" class="text-sm text-gray-500 hover:text-gray-700">
                                    By <?= htmlspecialchars($image['username']) ?>
                                </a>
                                <span class="text-xs text-gray-400"><?= date('M j, Y', strtotime($image['created_at'])) ?></span>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex items-center">
                                    <div onclick="<?= isset($_SESSION['user_id']) ? "toggleLike({$image['id']})" : "window.location.href='/login'" ?>"
                                         class="cursor-pointer hover:scale-110 transition-transform p-1 rounded-full hover:bg-red-50">
                                        <span class="text-red-500 text-lg heart-<?= $image['id'] ?>">♥</span>
                                    </div>
                                    <span class="text-sm like-count-<?= $image['id'] ?> ml-1"><?= $image['like_count'] ?></span>
                                </div>
                                <a href="/image/<?= $image['id'] ?>" class="flex items-center text-blue-500 hover:text-blue-700">
                                    <span class="mr-1">💬</span>
                                    <span class="text-sm"><?= $image['comment_count'] ?></span>
                                </a>
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

<script>
function toggleLike(imageId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        window.location.href = '/login';
        return;
    <?php endif; ?>

    const likeElement = document.querySelector('.like-count-' + imageId);
    const heartElement = document.querySelector('.heart-' + imageId);

    fetch('/image/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `image_id=${imageId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            likeElement.textContent = data.like_count;

            // Mettre à jour l'apparence du cœur
            if (data.liked) {
                heartElement.textContent = '❤️'; // Cœur plein
                heartElement.style.color = '#DC2626';
            } else {
                heartElement.textContent = '🤍'; // Cœur vide
                heartElement.style.color = '#EF4444';
            }

            // Animation
            heartElement.style.transform = 'scale(1.3)';
            setTimeout(() => {
                heartElement.style.transform = 'scale(1)';
            }, 150);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while toggling like');
    });
}

// Charger l'état initial des likes
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['user_id'])): ?>
        // Pour chaque image, vérifier si elle est likée
        const imageIds = [<?php
            $ids = [];
            foreach ($images as $img) {
                $ids[] = $img['id'];
            }
            echo implode(',', $ids);
        ?>];

        imageIds.forEach(imageId => {
            fetch(`/image/like-status?image_id=${imageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const heartElement = document.querySelector('.heart-' + imageId);
                    if (heartElement) {
                        if (data.liked) {
                            heartElement.textContent = '❤️'; // Cœur plein
                        } else {
                            heartElement.textContent = '🤍'; // Cœur vide
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error loading like status:', error);
            });
        });
    <?php endif; ?>
});
</script>
