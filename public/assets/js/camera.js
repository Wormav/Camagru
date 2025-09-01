let video, canvas, stream;
let selectedOverlay = null;

document.addEventListener('DOMContentLoaded', function() {
    video = document.getElementById('video');
    canvas = document.getElementById('canvas');

    const startButton = document.getElementById('start-camera');
    const captureButton = document.getElementById('capture');
    const fileUpload = document.getElementById('file-upload');
    const overlayOptions = document.querySelectorAll('.overlay-option');

    startButton.addEventListener('click', startCamera);
    captureButton.addEventListener('click', capturePhoto);
    fileUpload.addEventListener('change', handleFileUpload);

    overlayOptions.forEach(option => {
        option.addEventListener('click', function() {
            selectOverlay(this);
        });
    });
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

function selectOverlay(element) {
    document.querySelectorAll('.overlay-option').forEach(opt => {
        opt.classList.remove('border-blue-500', 'bg-blue-50');
        opt.classList.add('border-gray-200');
    });

    element.classList.remove('border-gray-200');
    element.classList.add('border-blue-500', 'bg-blue-50');

    selectedOverlay = element.dataset.overlay;
    updateCaptureButton();
}

function updateCaptureButton() {
    const captureButton = document.getElementById('capture');
    const hasVideo = video && !video.paused;
    const hasOverlay = selectedOverlay !== null;

    captureButton.disabled = !(hasVideo && hasOverlay);
}

function capturePhoto() {
    if (!selectedOverlay) {
        alert('Please select an overlay');
        return;
    }

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('image', blob, 'capture.png');
        formData.append('overlay', selectedOverlay);

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
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Upload error');
        });
    }, 'image/png');
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!selectedOverlay) {
        alert('Please select an overlay');
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('overlay', selectedOverlay);

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
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Upload error');
    });
}
