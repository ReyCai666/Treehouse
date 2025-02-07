<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = ['username', 'email', 'password', 'profile_pic'];
    
    public function login($username, $password)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('username', $username);
        // $builder->where('password', $password);
        $query = $builder->get();
        $row = $query->getRowArray();
        if ($row && password_verify($password, $row['password'])) {
            return true;
        }
        return false;
    }

    public function register($data) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        return $builder->insert($data);
    }

    public function saveVerificationToken($email, $verificationToken) {
        $db = \Config\Database::connect();
        $builder = $db->table('verification_tokens');
        $data = ['email' => $email,
                 'token' => $verificationToken
        ];
        return $builder->insert($data);
    }

    public function updateEmailVerificationStatus($email, $status) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->set('email_verified', $status);
        
        $builder->where('email', $email);
        return $builder->update();
    }

    public function getEmailVerificationStatus($email) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('email_verified');
        $builder->where('email', $email);
        $query = $builder->get();
        $row = $query->getRowArray();
        if ($row && $row['email_verified'] == true) {
            return true;
        }
        return false;
    }

    public function getVerificationToken($email) {
        $db = \Config\Database::connect();
        $builder = $db->table('verification_tokens');
        $builder->select('token');
        $builder->where('email', $email);
        $query = $builder->get();
        $row = $query->getRowArray();
        if ($row) {
            return $row['token'];
        }
        return null;
    }

    public function deleteVerificationToken($email) {
        $db = \Config\Database::connect();
        $builder = $db->table('verification_tokens');
        $builder->where('email', $email);
        return $builder->delete();
    }

    public function updateProfilePicture($username, $picPath) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->set('profile_pic', $picPath);
        $builder->where('username', $username);
        if($builder->update()) {
            return true;
        } else {
            return false;
        }
    }

    public function getProfilePicture($username) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('profile_pic');
        $builder->where('username', $username);
        $result = $builder->get()->getRowArray(); 
        if ($result) {
            return $result['profile_pic'];
        } else {
            return null; 
        }
    }

    public function updateUsername($oldUsername, $newUsername) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->set('username', $newUsername);
        $builder->where('username', $oldUsername);
        if($builder->update()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateBio($username, $newBio) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->set('bio', $newBio);
        $builder->where('username', $username);
        if($builder->update()) {
            return true;
        } else {
            return false;
        }
    }

    public function json_imported($username) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->set('json_imported', true);
        $builder->where('username', $username);
        $builder->update();
    }

    public function getIdByUsername($username) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('id');
        $builder->where('username', $username);
        $result = $builder->get();
        $row = $result->getRowArray();
        return isset($row['id']) ? (int)$row['id'] : null;
    }
}