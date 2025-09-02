let video, canvas, stream;
let selectedFrame = null;
let selectedEmoji = null;

document.addEventListener('DOMContentLoaded', function() {
    video = document.getElementById('video');
    canvas = document.getElementById('canvas');

    const startButton = document.getElementById('start-camera');
    const captureButton = document.getElementById('capture');
    const fileUpload = document.getElementById('file-upload');
    const frameOptions = document.querySelectorAll('.frame-option');
    const emojiOptions = document.querySelectorAll('.emoji-option');

    startButton.addEventListener('click', startCamera);
    captureButton.addEventListener('click', capturePhoto);
    fileUpload.addEventListener('change', handleFileUpload);

    frameOptions.forEach(option => {
        option.addEventListener('click', function() {
            selectFrame(this);
        });
    });

    emojiOptions.forEach(option => {
        option.addEventListener('click', function() {
            selectEmoji(this);
        });
    });

    // Initialiser avec "aucun" sélectionné par défaut
    updateCaptureButton();
});

async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480 }
        });
        video.srcObject = stream;

        document.getElementById('start-camera').disabled = true;
        updateCaptureButton();
    } catch (error) {
        console.error('Camera access error:', error);
        alert('Unable to access camera. Please use file upload.');
    }
}

function selectFrame(element) {
    document.querySelectorAll('.frame-option').forEach(opt => {
        opt.classList.remove('border-blue-500', 'bg-blue-50');
        opt.classList.add('border-gray-200');
    });

    element.classList.remove('border-gray-200');
    element.classList.add('border-blue-500', 'bg-blue-50');

    selectedFrame = element.dataset.frame;
    updateCaptureButton();
    updatePreview();
}

function selectEmoji(element) {
    document.querySelectorAll('.emoji-option').forEach(opt => {
        opt.classList.remove('border-blue-500', 'bg-blue-50');
        opt.classList.add('border-gray-200');
    });

    element.classList.remove('border-gray-200');
    element.classList.add('border-blue-500', 'bg-blue-50');

    selectedEmoji = element.dataset.emoji;
    updateCaptureButton();
    updatePreview();
}

function updateCaptureButton() {
    const captureButton = document.getElementById('capture');
    const hasVideo = video && !video.paused;
    const hasOverlay = (selectedFrame && selectedFrame !== 'none') || (selectedEmoji && selectedEmoji !== 'none');

    const isEnabled = hasVideo && hasOverlay;

    captureButton.disabled = !isEnabled;

    // Mettre à jour les classes CSS selon l'état
    if (isEnabled) {
        captureButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
        captureButton.classList.add('bg-blue-500', 'hover:bg-blue-600');
    } else {
        captureButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        captureButton.classList.add('bg-gray-400', 'cursor-not-allowed');
    }
}

function updatePreview() {
    const overlayPreview = document.getElementById('overlay-preview');
    if (!overlayPreview) return;

    overlayPreview.innerHTML = '';

    // Ajouter le cadre si sélectionné
    if (selectedFrame && selectedFrame !== 'none') {
        const frameDiv = document.createElement('div');
        frameDiv.className = getFrameClass(selectedFrame);
        overlayPreview.appendChild(frameDiv);
    }

    // Ajouter l'emoji si sélectionné
    if (selectedEmoji && selectedEmoji !== 'none') {
        const emojiDiv = document.createElement('div');
        emojiDiv.className = 'absolute bottom-2 right-2 text-4xl';
        emojiDiv.textContent = selectedEmoji;
        overlayPreview.appendChild(emojiDiv);
    }
}

function getFrameClass(frameType) {
    const frameClasses = {
        'classic': 'absolute inset-0 border-8 border-amber-600 pointer-events-none',
        'modern': 'absolute inset-0 border-4 border-gray-800 pointer-events-none',
        'vintage': 'absolute inset-0 border-8 border-orange-700 pointer-events-none'
    };
    return frameClasses[frameType] || '';
}

function capturePhoto() {
    const hasOverlay = (selectedFrame && selectedFrame !== 'none') || (selectedEmoji && selectedEmoji !== 'none');

    if (!hasOverlay) {
        alert('Please select at least one frame or emoji');
        return;
    }

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('image', blob, 'capture.png');
        formData.append('frame', selectedFrame || 'none');
        formData.append('emoji', selectedEmoji || 'none');

        // Ajouter le token CSRF
        const csrfToken = document.getElementById('csrf-token')?.dataset.token;
        console.log('CSRF Token found:', csrfToken); // Debug
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        } else {
            console.error('No CSRF token found!');
        }

        fetch('/camera/capture', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Photo captured successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: undefined');
        });
    }, 'image/png');
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const hasOverlay = (selectedFrame && selectedFrame !== 'none') || (selectedEmoji && selectedEmoji !== 'none');

    if (!hasOverlay) {
        alert('Please select at least one frame or emoji');
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('frame', selectedFrame || 'none');
    formData.append('emoji', selectedEmoji || 'none');

    // Ajouter le token CSRF
    const csrfToken = document.getElementById('csrf-token')?.dataset.token;
    console.log('CSRF Token found in upload:', csrfToken); // Debug
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    } else {
        console.error('No CSRF token found in upload!');
    }

    fetch('/camera/capture', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Image uploaded successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: undefined');
    });
}

function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    const formData = new FormData();
    formData.append('image_id', imageId);

    // Ajouter le token CSRF
    const csrfToken = document.getElementById('csrf-token')?.dataset.token;
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }

    fetch('/camera/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete image'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting image');
    });
}
