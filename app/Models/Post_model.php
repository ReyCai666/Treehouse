<?php

namespace App\Models;

use CodeIgniter\Model;

class Post_model extends Model
{
    protected $table = 'post';
    protected $allowedFields = ['author', 'title', 'content', 'views', 'likes'];

    public function post($data) {
        $db = \Config\Database::connect();
        $builder = $db->table('post');
        $builder->insert($data);
        return $db->insertID();
    }

    public function updateAuthor($oldUsername, $newUsername) {
        $db = \Config\Database::connect();
        $builder = $db->table('post');
        $builder->set('author', $newUsername);
        $builder->where('author', $oldUsername);
        if($builder->update()) {
            return true;
        } else {
            return false;
        }
    }

    public function getPostById($id) {
        return $this->where('id', $id)->first();
    }

    public function getPostByTitle($title) {
        return $this->like('title', $title)->findAll();
    }

    public function getLikeById($id) {
        $db = \Config\Database::connect();
        $builder = $db->table('post');
        $builder->select('likes');
        $builder->where('id', $id);
        $result = $builder->get();
        $row = $result->getRowArray();
        return isset($row['likes']) ? (int)$row['likes'] : null;
    }

    public function toggleLike($userId, $postId) {
        $db = \Config\Database::connect();
        $builder = $db->table('user_likes');
        
        // check if the user has liked this post before.
        $builder->where(['user_id' => $userId, 'post_id' => $postId]);
        $yes = $builder->countAllResults();
        if ($yes) {
            $builder->where(['user_id' => $userId, 'post_id' => $postId]);
            $builder->delete(); // unlike
            $yes=0;
        } else { // if not liked before
            $builder->insert(['user_id' => $userId, 'post_id' => $postId]);
            $yes=1;
        }
        // start updating the like count in post table.
        $builder2 = $db->table('post');
        $builder2->where('id', $postId);
        $post = $builder2->get()->getRow();
        $newLikeCount = 0;
        if ($post) {
            // plus 1 if liked, minus 1 if unliked
            $newLikeCount = $yes > 0 ? $post->likes + 1 : $post->likes - 1;
            $builder2->where('id', $postId);
            $builder2->update(['likes' => $newLikeCount]);
        }
        $result = (object)['likes' => $newLikeCount, 'liked' => ($yes == 1 ? true : false)];
        error_log('Toggle like result: ' . print_r($result, true));
        return $result;
    }

    public function incrementViews($postId) {
        $db = \Config\Database::connect();
        $builder = $db->table('post');
        $builder->where('id', $postId);
        $builder->set('views', 'views+1', FALSE);
        $builder->update();
    }

    public function getViews($postId) {
        $db = \Config\Database::connect();
        $builder = $db->table('post');
        $builder->select('views');
        $builder->where('id', $postId);
        $result = $builder->get();
        $row = $result->getRowArray();
        return isset($row['views']) ? (int)$row['views'] : null;
    }

    public function uploadAttachments($postId, $file_path) {
        $db = \Config\Database::connect();
        $builder = $db->table('post_attachments');
        $data =['post_id' => $postId, 'file_path' => $file_path];
        if($builder->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAttachments($postId) {
        $db = \Config\Database::connect();
        $builder = $db->table('post_attachments');
        $builder->select('file_path');
        $builder->where('post_id', $postId);
        $result = $builder->get();
        $attachments = array();
        foreach ($result->getResultArray() as $row) {
            $attachments[] = array(
                'file_path' => $row['file_path']
            );
        }
        return $attachments;
    }
}