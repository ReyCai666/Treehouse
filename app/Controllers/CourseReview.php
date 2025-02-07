<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\Course_model;

class CourseReview extends BaseController
{   
    public function index() {
        $session = session();
        if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
            header("Location: https://infs3202-ed18d3de.uqcloud.net/Treehouse/login");
            exit();
        }

        $courseModel = new Course_model();
        $data['courses'] = $courseModel->findAll();
        echo view('navigation');
        echo view("course_review", $data);
    }

    public function autocomplete() {
        $postModel = new Course_model();
        $data = $postModel->findAll();
        $titles = array_column($data, 'course_code');
        return $this->response->setJSON($titles);
    }
}