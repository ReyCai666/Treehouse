<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Post Content Page</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/postStyle.css'); ?>">
</head>
<body>
    <div class="post-wrapper">
        <div class="post-content">
            <h1>Title: <?php echo $post['title']; ?></h1>
            <p>Author: <?php echo $post['author']; ?></p>
            <p>Description: <?php echo $post['description']; ?></p>
            <p>Content: <?php echo $post['content']; ?></p>
            <p>Views: <?php echo $views ?></p>
            <p>Likes: <?php echo $post['likes']; ?></p>
            <div class="attachments">
                <?php if (!empty($attachments)) { ?>
                    <h2>Attachments:</h2>
                    <ul>
                        <?php foreach ($attachments as $attachment) { ?>
                            <?php if (strpos($attachment['file_path'], '.jpg') != false || strpos($attachment['file_path'], '.jpeg') != false || strpos($attachment['file_path'], '.png') !== false || strpos($attachment['file_path'], '.gif') !== false) { ?>
                                <img src="<?php echo base_url($attachment['file_path']); ?>" alt="<?php echo $attachment['file_path']; ?>" class="image">
                            <?php } else { ?>
                                <li><a href="<?php echo base_url($attachment['file_path']); ?>" download><?php echo $attachment['file_path']; ?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
            <div class="buttons">
                <button class="like-btn" id="likeBtn" data-liked="false">Like</button>
                <button class="comment-btn" id="commentBtn">Comment</button>
            </div>
        </div>
    </div>
    <div class="comment-box" id="commentBox" style="display:none;">
        <form action="<?= base_url('/CommentController/postComment') ?>" method="post">
            <textarea class="comment-textarea" name="comment" placeholder="Write a comment..."></textarea>
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <button class="post-comment-btn" id="post-comment-Btn" type="submit">Post</button>
        </form>
    </div>
    <div class="comment-area">
        <?php foreach ($comments as $comment): ?>
            <h3 class="reply-header">Reply:</h3>
            <div class="comment-box">
                <div class="comment-header">
                    <div class="user-info">
                        <img class="profile-pic" src="<?php echo base_url($comment['profile_pic']); ?>" alt="Profile Picture">
                        <p class="username"><?= $comment['author'] ?> <span class="replied-text"> - replied:</span> </p>
                    </div>
                    <p class="created-at"><?= $comment['created_at'] ?></p>
                </div>
                <div class="comment-content">
                    <p><?= $comment['content'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        const likeBtn = document.getElementById("likeBtn");

        document.getElementById("commentBtn").addEventListener("click", function() {
            var commentBox = document.getElementById("commentBox");
            // set the display style of comment box to block if its current display style is none, otherwise, none.
            commentBox.style.display = commentBox.style.display == "none" ? "block" : "none";
        });

        likeBtn.addEventListener("click", function() {
            const isLiked = likeBtn.getAttribute("data-liked") == "true";
            const postId = <?= $post['id'] ?>;
            const userId = <?= $user_id ?>;
            console.log('post_id:', postId);
            console.log('user_id:', userId);
            // Send an AJAX request to update the like count
            fetch("<?= base_url('/CommentController/updateLikeCount') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({ post_id: postId , user_id: userId})
            })
            .then(response => {
                console.log(response);
                response.clone().text().then(text => console.log('Response body:', text));
                return response.json();
            })
            .then(data => {
                console.log('Toggle like result:', data);
                const isLikedByCurrentUser = localStorage.getItem(`post_${postId}_liked_by_${userId}`);
                if (data.liked && !isLikedByCurrentUser) {
                    likeBtn.textContent = "Liked (" + data.likes + ")";
                    likeBtn.setAttribute("data-liked", "true");
                    localStorage.setItem(`post_${postId}_liked_by_${userId}`, true);
                } else {
                    likeBtn.textContent = "Like";
                    likeBtn.setAttribute("data-liked", "false");
                    localStorage.removeItem(`post_${postId}_liked_by_${userId}`);
                }
                window.location.reload();
            });
        });
        window.onload = function() {
            const likeBtn = document.getElementById("likeBtn");
            const postId = <?= $post['id'] ?>;
            const userId = <?= $user_id ?>;

            const isLikedByCurrentUser = localStorage.getItem(`post_${postId}_liked_by_${userId}`);
            if (isLikedByCurrentUser) {
                likeBtn.textContent = "Liked (" + <?= $post['likes'] ?> + ")";
                likeBtn.setAttribute("data-liked", "true");
            }
        }

    </script>
</body>
</html>