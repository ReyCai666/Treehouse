<?php

namespace App\Models;
use CodeIgniter\Model;
date_default_timezone_set('Australia/Brisbane');

class Comment_model extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $allowedFields = ['post_id', 'profile_pic', 'user_id', 'author', 'content'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    public function getCommentsById($postID) {
        $db = \Config\Database::connect();
        $builder = $db->table('comments');
        $builder->$db->select("content");
        $builder->where('post_id', $postID);
        $result = $builder->get();
        return $result;
    }

    public function getCreatedTimeById($postID) {
        $db = \Config\Database::connect();
        $builder = $db->table('comments');
        $builder->$db->select("created_at");
        $builder->where('post_id', $postID);
        $result = $builder->getRowArray();
        return $result ? strtotime($result['created_at']) : null;
    }
}
