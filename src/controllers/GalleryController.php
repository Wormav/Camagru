<?php

class GalleryController extends Controller {

    public function index() {
        $this->view('gallery/index', [
            'title' => 'Gallery - Camagru'
        ]);
    }
}
