<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="relative">
            <div class="h-96 bg-gray-100 flex items-center justify-center p-4">
                <img src="/uploads/<?= htmlspecialchars($image['filename']) ?>"
                     alt="Photo by <?= htmlspecialchars($image['username']) ?>"
                     class="max-w-full max-h-full object-contain">
            </div>

            <a href="/gallery"
               class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-2 rounded hover:bg-opacity-70 transition-all">
                ← Back to Gallery
            </a>
        </div>

        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-300">
                        <?php if ($image['profile_picture']): ?>
                            <img class="h-full w-full object-cover"
                                 src="/uploads/profiles/<?= htmlspecialchars($image['profile_picture']) ?>"
                                 alt="<?= htmlspecialchars($image['username']) ?>">
                        <?php else: ?>
                            <div class="h-full w-full flex items-center justify-center text-lg">
                                😊
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold"><?= htmlspecialchars($image['username']) ?></h2>
                        <p class="text-sm text-gray-500"><?= date('M j, Y at g:i A', strtotime($image['created_at'])) ?></p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button id="like-btn"
                                onclick="toggleLike(<?= $image['id'] ?>)"
                                class="flex items-center space-x-1 px-3 py-2 rounded transition-colors hover:bg-gray-100">
                            <span id="like-icon" class="text-lg">❤️</span>
                            <span id="like-count" class="text-sm font-medium"><?= $image['like_count'] ?></span>
                        </button>
                    <?php else: ?>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <span class="text-lg">❤️</span>
                            <span class="text-sm"><?= $image['like_count'] ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center space-x-1 text-gray-500">
                        <span class="text-lg">💬</span>
                        <span class="text-sm" id="comment-count"><?= $image['comment_count'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold mb-4">Comments</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form id="comment-form" class="mb-6">
                <div class="flex space-x-3">
                    <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-300 flex-shrink-0">
                        <?php if (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture']): ?>
                            <img class="h-full w-full object-cover"
                                 src="/uploads/profiles/<?= htmlspecialchars($_SESSION['profile_picture']) ?>"
                                 alt="<?= htmlspecialchars($_SESSION['username']) ?>">
                        <?php else: ?>
                            <div class="h-full w-full flex items-center justify-center text-sm">
                                😊
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <textarea id="comment-input"
                                  name="comment"
                                  placeholder="Add a comment..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                  rows="2"></textarea>
                        <button type="submit" id="comment-submit-btn"
                                class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span id="submit-text">Post Comment</span>
                            <span id="submit-loading" class="hidden">Posting...</span>
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="text-center py-4 text-gray-500">
                <a href="/login" class="text-blue-500 hover:underline">Login</a> to add a comment
            </div>
        <?php endif; ?>

        <div id="comments-list">
        </div>
    </div>
</div>

<script>
const imageId = <?= $image['id'] ?>;
const currentUserId = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;
let isLiked = false;

document.addEventListener('DOMContentLoaded', function() {
    loadComments();

    <?php if (isset($_SESSION['user_id'])): ?>
        checkLikeStatus();
    <?php endif; ?>

    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addComment();
        });
    }
});

function toggleLike(imageId) {
    if (!currentUserId) {
        window.location.href = '/login';
        return;
    }

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
            isLiked = data.liked;
            document.getElementById('like-icon').textContent = isLiked ? '❤️' : '🤍';
            document.getElementById('like-count').textContent = data.like_count;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function checkLikeStatus() {
    fetch(`/image/like-status?image_id=${imageId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isLiked = data.liked;
            document.getElementById('like-icon').textContent = isLiked ? '❤️' : '🤍';
        }
    });
}

function addComment() {
    const commentInput = document.getElementById('comment-input');
    const submitBtn = document.getElementById('comment-submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const comment = commentInput.value.trim();

    if (!comment) {
        alert('Please enter a comment');
        return;
    }

    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');

    fetch('/image/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `image_id=${imageId}&comment=${encodeURIComponent(comment)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            commentInput.value = '';
            loadComments();

            const commentCountEl = document.getElementById('comment-count');
            commentCountEl.textContent = parseInt(commentCountEl.textContent) + 1;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
    });
}

function loadComments() {
    fetch(`/image/comments?image_id=${imageId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayComments(data.comments);
        }
    });
}

function displayComments(comments) {
    const commentsList = document.getElementById('comments-list');

    if (comments.length === 0) {
        commentsList.innerHTML = '<p class="text-gray-500 text-center py-4">No comments yet</p>';
        return;
    }

    let html = '';
    comments.forEach(comment => {
        html += `
            <div class="flex space-x-3 py-3 border-b border-gray-100">
                <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-300 flex-shrink-0">
                    ${comment.profile_picture ?
                        `<img class="h-full w-full object-cover" src="/uploads/profiles/${comment.profile_picture}" alt="${comment.username}">` :
                        '<div class="h-full w-full flex items-center justify-center text-sm">😊</div>'
                    }
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-medium text-sm">${comment.username}</span>
                            <span class="text-xs text-gray-500 ml-2">${formatDate(comment.created_at)}</span>
                        </div>
                        ${currentUserId && currentUserId == comment.user_id ?
                            `<button onclick="deleteComment(${comment.id})" class="text-red-500 hover:text-red-700 text-xs">Delete</button>` :
                            ''
                        }
                    </div>
                    <p class="text-sm mt-1">${comment.content}</p>
                </div>
            </div>
        `;
    });

    commentsList.innerHTML = html;
}

function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) {
        return;
    }

    fetch('/image/delete-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `comment_id=${commentId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadComments();

            const commentCountEl = document.getElementById('comment-count');
            commentCountEl.textContent = parseInt(commentCountEl.textContent) - 1;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) {
        return '1 day ago';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}
</script>
