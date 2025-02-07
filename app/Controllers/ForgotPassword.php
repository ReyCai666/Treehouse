<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\PasswordReset_model;
date_default_timezone_set('Australia/Brisbane');

class ForgotPassword extends BaseController
{   
    protected $token = 1;

    protected $email = "";

    public function index() {
        if ($this->request->getMethod() == 'post') {
            $email = $this->request->getPost('email');
            session()->set('reset_email', $email);

            $userModel = new User_model();

            // Check if the email exists in the database
            $user = $userModel->where('email', $email)->first();

            if ($user) {
                $token = rand(100000, 999999);
                $expiryTimestamp = date('Y-m-d H:i:s', strtotime('+2 minutes'));
                
                $passwordResetsModel = new PasswordReset_model();
                $passwordResetsModel->insert(['user_id' => $user['id'], 'token' => $token, 'expire_at' => $expiryTimestamp]);
                $this->send_reset_token($email, $token);

                return redirect()->to(base_url('forgot_password/reset_password'));
            } else {
                session()->setFlashdata('error', 'The email address you entered does not match our records.');
            } 
        }
       
        echo view('forgot_password');
    }

    public function reset_password() {
        $passwordResetModel = new PasswordReset_model();
        $email = session()->get('reset_email');
        $data = [];
        // get the token stored in the database according to user's registration email:
        $token = $passwordResetModel->getTokenByEmail($email);
    
        if ($this->request->getMethod() == 'post') {
            $inputToken = $this->request->getPost('token');
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');
            
 
            if ($inputToken != $token) {
                session()->setFlashdata('error', 'The password reset token is invalid. Please try again.');
            } else {
                session()->remove('error');
                // Check if the token has expired
                $expiryTimestamp = $passwordResetModel->getTokenExpiryTimestamp($email);
                if (time() > $expiryTimestamp) {
                    session()->setFlashdata('error', 'The password reset token has expired. Please try again.');
                    $passwordResetModel->deleteTokenByEmail($email);
                    return redirect()->to(base_url('forgot_password'));
                }
    
                // Validate password input
                $rules = [
                    'password' => 'required|min_length[8]|max_length[255]',
                    'confirm_password' => 'matches[password]',
                ];
                if (!$this->validate($rules)) {
                    $data['validation'] = $this->validator;
                    $data['error'] = $this->validator->getErrors();
                } else {
                    // Update user's password
                    $userModel = new User_model();
                    $user = $userModel->where('email', $email)->first();
                    $userModel->update($user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
    
                    // Delete password reset token from database
                    $passwordResetModel->deleteTokenByEmail($email);
    
                    session()->setFlashdata('success', 'Your password has been successfully reset. This page will be redirected to login in 3 seconds');
    
                    // Redirect to login page after 3 seconds
                    header('refresh:3; url='.base_url('/login'));
                }
            }
        }
    
        return view('reset_password', $data);
    }
    
    
    public function send_reset_token ($emailAddress, $token){
        $emailService = new \CodeIgniter\Email\Email();

        $config['protocol'] = 'smtp';
        $config['SMTPHost'] = 'mailhub.eait.uq.edu.au';   
        $config['SMTPPort'] = 25; 
        $config['starttls'] = true;
        $config['charset'] = 'UTF-8'; //iso-8859-1
        $config['mailtype'] = 'html';
        $config['newline'] = "\r\n";
        $config['wordwrap'] = TRUE;

        $emailService->initialize($config);
        $emailService->setFrom(get_current_user().'@student.uq.edu.au', 'UQTreehouse Official - DO NOT REPLY');
        $emailService->setTo($emailAddress);
        $emailService->setSubject('Password Reset');
        $emailService->setMessage('Please use this token to reset your password: ' .$token);

        if ($emailService->send()) {
            session()->setFlashdata('success', 'A password reset token has been sent to your email address.');
            // return redirect()->to(base_url('forgot_password/reset_password'));
        } else {
            log_message('error', 'Failed to send verification email to '.$emailAddress);
        }
    }
    
}
