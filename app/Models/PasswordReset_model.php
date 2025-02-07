<?php

namespace App\Models;

use CodeIgniter\Model;
date_default_timezone_set('Australia/Brisbane');

class PasswordReset_model extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'token', 'expire_at'];

    public function deleteTokenByEmail($email) {
        $db = \Config\Database::connect();
        $builder = $db->table('password_resets');
        $userModel = new User_model();
        $user = $userModel->where('email', $email)->first();
        if ($user) {
            $builder->where('user_id', $user['id'])->delete();
        }
    }
    
    public function getTokenByEmail($email) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $user = $builder->select('id')
                        ->where('email', $email)
                        ->get()
                        ->getRowArray();
    
        if ($user) {
            $builder = $db->table('password_resets');
            $row = $builder->where('user_id', $user['id'])
                            ->where('expire_at >', time())
                            ->get()
                            ->getRowArray();
            return ($row) ? $row['token'] : null;
        }

        return null;
    }

    public function getTokenExpiryTimestamp($email) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('password_resets.expire_at');
        $builder->join('password_resets', 'users.id = password_resets.user_id');
        $builder->where('users.email', $email);
        $query = $builder->get();
        $result = $query->getRowArray();
        return $result ? strtotime($result['expire_at']) : null;
    }
    
}
