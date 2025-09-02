<?php

class GalleryController extends Controller {

    public function index() {
        $imageModel = new Image();

        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = IMAGES_PER_PAGE;

        $images = $imageModel->getAllWithPagination($page, $limit);
        $totalImages = $imageModel->getTotalCount();
        $totalPages = ceil($totalImages / $limit);

        $this->view('gallery/index', [
            'title' => 'Gallery - Camagru',
            'images' => $images,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalImages' => $totalImages
        ]);
    }
}
