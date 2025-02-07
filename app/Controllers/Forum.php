<?php

namespace App\Controllers;

use App\Models\Post_model;
use App\Models\Comment_model;
use App\Models\User_model;
use Intervention\Image\ImageManager;

class Forum extends BaseController
{ 
    public function index() {
        // always check for login authorization first.
        $session = session();
        if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
            header("Location: https://infs3202-ed18d3de.uqcloud.net/Treehouse/login");
            exit();
        }

        $postModel = new Post_model();
        $data['posts'] = $postModel->findAll();

        echo view('navigation');
        echo view("discussion_forum", $data);
    }

    public function create() {
        echo view('navigation');
        echo view("create");
    }

    public function save() {
        $postModel = new Post_model();
        $session = session();
        $username = $session->get('username');
        $data = [
            'author' => $username,
            'description'  => $this->request->getPost('description'),
            'title'  => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'views' => 0,
            'likes' => 0
        ]; 
        $post_id = $postModel->post($data);
        $files = $this->request->getFiles();
        if (!empty($files['attachments'])) {
            $uploadSuccessful = $this->uploadMultipleFiles($post_id, $files);
            if ($uploadSuccessful) {
                return redirect()->to(base_url('discussion_forum'));
            }
        } else {
            return redirect()->to(base_url('discussion_forum'));
        }
    }

    public function uploadMultipleFiles($post_id, $files) {

        $postModel = new Post_model();
        $isSuccessful = true;
        if (isset($files['attachments'])) {
            foreach($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $file->move(WRITEPATH . 'uploads');
                    $fileName = $file->getName();
                    $AttachmentPath = WRITEPATH . 'uploads/' . $fileName;
                    $storePath = 'writable/uploads/' . $fileName;
                    $imagePath = realpath($AttachmentPath);
                    // an image file may have a MIME type of "image/jpeg", "image/png", or "image/gif"
                    if(in_array($file->getClientMimeType(), ['image/jpeg', 'image/png'])) {
                        $this->addWaterMark($imagePath);
                    }

                    if (!$postModel->uploadAttachments($post_id, $storePath)) {
                        $isSuccessful = false;
                    }
                }
            }
        } else {
        }
        return $isSuccessful;
    }

    public function addWaterMark($imagePath) {
        echo $imagePath;
        if (is_readable($imagePath)) {
            echo "File is readable";
        } else {
            echo "File is not readable";
        }
        // create intervention image processor instance
        $imageProcessor = new ImageManager(array('driver' => 'gd'));
        $image = $imageProcessor->make($imagePath);
        $image->resize(320, 240);
        // Add the watermark
        $image->insert('/var/www/htdocs/Treehouse/assets/pictures/watermark.png', 'bottom-right');
        $image->save($imagePath);
    }

    public function autocomplete() {
        $postModel = new Post_model();
        $data = $postModel->findAll();
        $titles = array_column($data, 'title');
        return $this->response->setJSON($titles);
    }

    public function loadPost() {
        $postModel = new Post_model(); 
        $page = $this->request->getVar('page');
        $limit = 4;
        $offset = ($page - 1) * $limit;
        $data['posts'] = $postModel->findAll($limit, $offset);
        
        return $this->response->setJSON($data['posts']);
    }

    public function displayPost($id) {
        $postModel = new Post_model(); 
        $user = new User_model();
        $commentModel = new Comment_model();
        $session = session();
        $username = session()->get('username');
        $userId = $user->getIdByUsername($username);
        
        $post = $postModel->getPostById($id);
        if ($post == null) {
            echo "post not exist";
        }
        $comments = $commentModel->where('post_id', $id)->orderBy('created_at', 'DESC')->findAll();
        $attachments = $postModel->getAttachments($id);
        $data['post'] = $post;
        $data['comments'] = $comments;
        $data['user_id'] = $userId;
        $data['post_id'] = $id;
        $postModel->incrementViews($id);
        $data['views'] = $postModel->getViews($id);
        $data['attachments'] = $attachments;
        echo view('navigation');
        echo view('post_content', $data);
    }

    public function search() {
        $searchInput = $this->request->getVar('title');
        $postModel = new Post_model();
        $data['posts'] = $postModel->getPostByTitle($searchInput);
        return $this->response->setJSON($data['posts']);
    }

}