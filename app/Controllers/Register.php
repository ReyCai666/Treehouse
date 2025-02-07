<?php

namespace App\Controllers;

use App\Models\User_model;

class Register extends BaseController
{
    public function index()
    {
        helper(['form']);
        $userModel = new User_model();
        $data = [];
        if ($this->request->getMethod() == 'post') {
            $rules = [
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.email]|check_uqconnect_domain',
                    'errors' => [
                        'required' => 'Email is required.',
                        'valid_email' => 'Please provide a valid email address.',
                        'is_unique' => 'This email is already used.',
                        'check_uqconnect_domain' => 'The email must have the @uqconnect domain.',
                    ],
                ],
                'username' => [
                    'rules' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
                    'errors' => [
                        'required' => 'username is required.',
                        'min_length' => 'Your username must contain at least 3 characters.',
                        'is_unique' => 'This username is already used.',
                    ],
                ],
                'password' => 'required|min_length[8]|max_length[255]',
                'confirm_password' => 'matches[password]',
            ];

            // email verification

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                $data['error'] = $this->validator->getErrors();      
            } else {
                $defaultProfilePic = 'assets/pictures/IKUN.jpg';
                $userModel = new User_model();
                $data = [ 'email' => $this->request->getPost('email'),
                          'username' => $this->request->getPost('username'),
                          'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                          'profile_pic' => $defaultProfilePic,
                        ];
                if ($userModel->register($data)) {
                    // generate verification token for user first.
                    $verificationToken = rand(100000, 999999);
                    // save token to database for later check.
                    $userModel->saveVerificationToken($data['email'], $verificationToken);
                    // send the verification email to user with the generated token.
                    $this->sendVerificationEmail($data['email'], $verificationToken);
                    // set email_verified to false before user enter the correct token.
                    $userModel->updateEmailVerificationStatus($data['email'], false);
                    
                    session()->set('email', $data['email']);
                    return redirect()->to(base_url('register/verify'));

                    // return redirect()->to(base_url('register/success'));
                } else {
                    $data['error'] = 'An error occurred.';
                }
            }
      
        }
        echo view('register', $data);
    }

    public function success() {
        $userModel = new User_model();
        $email = session()->get('email');

        if (!$email) {
            return redirect()->to(base_url('register'));
        }

        $email_verified = $userModel->getEmailVerificationStatus($email);
        if ($email_verified) {
            echo view('success');
        } else {
            echo "Something occured while verifying your email, please try again later :(";
        }
    }

    public function verify() {
        $userModel = new User_model();
        $email = session()->get('email');
        $data = [];

        if (!$email) {
            return redirect()->to(base_url('register'));
        }
        
        // check if form is submitted.
        if ($this->request->getMethod() == 'post') {
            // get the verification token submitted by user
            $verificationToken = $this->request->getPost('verification_code');
            $savedToken = $userModel->getVerificationToken($email);

            if ($savedToken && $savedToken == $verificationToken) {
                // update email verification status to true
                $userModel->updateEmailVerificationStatus($email, true);

                // remove verification token from database
                $userModel->deleteVerificationToken($email);
                return redirect()->to(base_url('register/success'));
            } else {
                $data['error'] = 'Invalid verification token.';
            }
        }
        echo view('verify', $data);
    }

    private function sendVerificationEmail($emailAddress, $verificationToken) {
        $email = new \CodeIgniter\Email\Email();
        $config['protocol'] = 'smtp';
        $config['SMTPHost'] = 'mailhub.eait.uq.edu.au';   
        $config['SMTPPort'] = 25; 
        $config['starttls'] = true;
        $config['charset'] = 'UTF-8'; //iso-8859-1
        $config['mailtype'] = 'html';
        $config['newline'] = "\r\n";
        $config['wordwrap'] = TRUE;

        $email->initialize($config);
        $email->setFrom(get_current_user().'@student.uq.edu.au', 'UQTreehouse Official - DO NOT REPLY'); //get_current_user().'@student.uq.edu.au'
        $email->setTo($emailAddress);
        $email->setSubject('UQ Treehouse - Please Verify Your Email using the token');

        $email->setMessage('Please use the token to verify your email: ' .$verificationToken);

        if ($email->send()) {
            log_message('info', 'Successfully send verification email to '.$emailAddress);
        } else {
            log_message('error', 'Failed to send verification email to '.$emailAddress);
        }
    }
}


