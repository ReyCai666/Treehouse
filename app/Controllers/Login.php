<?php

namespace App\Controllers;
use App\Models\User_model;
use App\Models\Course_model;

class Login extends BaseController
{
    public function index()
    {   
        $session = session();
        $userModel = new User_model();
        $username = $session->get('username');
        $user = $userModel->where('username', $username)->first();
        $data['user'] = $user;
        $data['error'] = "";

        if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
            echo view('template/header');
            echo view("navigation");
            echo view('user_profile', $data);
            // echo view('template/footer');
        } else {
            $session = session();
            $username = $session->get('username');
            $password = $session->get('password');
            if ($username && $password) {
                echo view("navigation");
                echo view('user_profile', $data);
                // echo view('template/footer');
            } else {
                echo view('template/header');
                echo view('login', $data);
                echo view('template/footer');
            }
        }
    }

    public function check_login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        // $course_json = __DIR__ . 'assets/Json/course_names.json';
        $model = model('App\Models\User_model');
       
        $user = $model->where('username', $username)->first();
        
        $if_remember = $this->request->getPost('remember');

        if ($user && $user['email_verified'] == true && password_verify($password, $user['password'])) {
            # Create a session 
            $session = session();
            $session->set('username', $username);
            $session->set('password', $password);
            $session->set('user_authenticated', true);
            if ($if_remember) {
                $expiry_time = time() + 3600*8; // 8 hours
                setcookie('username', $username, $expiry_time, "/");
                setcookie('password', $password, $expiry_time, "/");
            }
            
            if (!$user['json_imported']) {
                $this->import_json();
                $model->json_imported($username);
            }
            $data['user'] = $user;
            echo view("navigation");
            echo view("user_profile", $data);

        } else if ($user && $user['email_verified'] != true) {
            $data['error'] = "Your email is not verified.";
            echo view('template/header');
            echo view('login', $data);
            echo view('template/footer');
        } else {
            $data['error'] = "Incorrect username or password!";
            echo view('template/header');
            echo view('login', $data);
            echo view('template/footer');
        }
    }

    public function logout()
    {
        $session = session();
        $session->remove('user_authenticated');
        $session->destroy();
        // delete_cookie('username');
        // delete_cookie('password');
        setcookie('username', '', time() - 300, '/');
        setcookie('password', '', time() - 300, '/');  
        return redirect()->to(base_url('login'));
    }

    public function import_json() {
        $courseModel = new Course_model();
        $json_data = file_get_contents(APPPATH . '../assets/Json/course_names.json');
        $courses = json_decode($json_data, true);

        foreach ($courses as $course_code => $course_name) {
            $data = [
                'course_code' => $course_code,
                'course_name' => $course_name,
            ];
            $courseModel->insert_course($data);
        }
    }
}
