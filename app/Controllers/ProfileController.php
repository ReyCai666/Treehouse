<?php

namespace App\Controllers;
use App\Models\User_model;
use App\Models\Post_model;

class ProfileController extends BaseController
{
    public function index() {
        $session = session();
        if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
            header("Location: https://infs3202-ed18d3de.uqcloud.net/Treehouse/login");
            exit();
        }
        $userModel = new User_model();
        $username = $session->get('username');
        if ($username == null) {
            $username = $_COOKIE['username'];
        }
        $user = $userModel->where('username', $username)->first();
        $data['user'] = $user;
        echo view('navigation');
        echo view('user_profile', $data);
    }

    public function upload() {
        if ($this->request->getMethod() == 'post') {
                $validation =  \Config\Services::validation();
                $validation->setRules([
                    'profile_pic' => [
                        'rules' => 'uploaded[profile_pic]|max_size[profile_pic,2048]|is_image[profile_pic]',
                        'errors' => [
                            'is_image' => 'The file you choosed is not an image.',
                        ],
                    ]
                ]);
            if ($validation->withRequest($this->request)->run()) {
                $file = $this->request->getFile('profile_pic');
                if ($file->isValid() && !$file->hasMoved()) {
                    $file->move(WRITEPATH . 'uploads');
                    $fileName = $file->getName();
                    $userModel = new User_model();
                    $username = session()->get('username');
                    $newProfilePicPath = 'writable/uploads/' . $fileName;
                    $check = $userModel->updateProfilePicture($username, $newProfilePicPath);
                    if ($check) {
                        echo "Successfully updated your profile picture!";
                        echo view('navigation');
                        return redirect()->to(base_url('user_profile'));
                    } else {
                        $data['errors'] = "<div class=\"alert alert-danger\" role=\"alert\"> Upload failed! </div> ";
                        echo view('user_profile', $data);
                    }
                }
            } else {
                $data['validation'] = $validation->getErrors();
                echo view('navigation');
                echo view('user_profile', $data);
            }
        }
    }

    public function updateUsername() {
        $session = session();
        $username = $session->get('username');
        $userModel = new User_model();
        $postModel = new Post_model();
        if ($this->request->getMethod() == 'post') {
            $newUsername = $this->request->getPost('new_username');

            $rules = ['new_username' => [
                'rules' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
                    'errors' => [
                        'required' => 'username is required.',
                        'min_length' => 'Your username must contain at least 3 characters.',
                        'is_unique' => 'This username is already used.',
                    ],
                ],
            ];
            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                $data['error'] = $this->validator->getErrors();
                session()->setFlashdata('error', $data['error']['new_username'], 5);
                echo view('navigation');
                return redirect()->to(base_url('user_profile'));
            } else {
                if ($userModel->updateUsername($username, $newUsername) && $postModel->updateAuthor($username, $newUsername)) {
                    $session->set('username', $newUsername);
                    echo view('navigation');
                    return redirect()->to(base_url('user_profile'));
                } else {
                    echo "Something went wrong.";
                }
            }
        }
    }

    public function updateBio() {
        $session = session();
        $username = $session->get('username');
        $userModel = new User_model();
        if ($this->request->getMethod() == 'post') {
            $newBio = $this->request->getPost('new_bio');
            if ($userModel->updateBio($username, $newBio)) {
                echo view('navigation');
                return redirect()->to(base_url('user_profile'));
            } else {
                echo "Faild to update your bio, something went wrong.";
            }
        }
    }
}