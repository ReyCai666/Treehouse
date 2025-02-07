<?php

namespace App\Controllers;
use App\Models\Comment_model;
use App\Models\User_model;
use App\Models\Post_model;

class CommentController extends BaseController
{
    public function postComment() {
        $commentModel = new Comment_model();
        $user = new User_model();
        $session = session();

        $username = session()->get('username');
        $userID = $user->getIdByUsername($username);
        $profilePic = $user->getProfilePicture($username);
        $commentData = [
            'post_id' => $this->request->getPost('post_id'),
            'user_id' => $userID,
            'profile_pic' => $profilePic,
            'author'  => $username,
            'content' => $this->request->getPost('comment'),
        ];

        $commentModel->insert($commentData);
        return redirect()->to(base_url('discussion_forum/post_content/' . $commentData['post_id']));
    }

    public function updateLikeCount() {
        $post = new Post_model();
        $userID = $this->request->getVar('user_id');
        $postId = $this->request->getVar('post_id');
        $updatedLikeCount = $post->toggleLike($userID, $postId);
        error_log('Updated like count: ' . print_r($updatedLikeCount, true));

        return $this->response->setJSON(['likes' => $updatedLikeCount->likes, 'liked' => $updatedLikeCount->liked]);
    }

}
