<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/create.css'); ?>">
    <title>Create a New Post</title>
</head>
<body>
    <header>
        <h1>Create a New Post</h1>
    </header>
    
    <div class="form-group">
        <form action="<?php echo base_url('discussion_forum/save'); ?>" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required="required">
            <br><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required="required" cols="40" rows="1"></textarea>
            <br><br>
            <label for="content">Content:</label>
            <textarea name="content" id="content" required="required" cols="50" rows="10"></textarea>
            <br><br>
            <label for="attachments">Attachments:</label>
            <input type="file" name="attachments[]" id="attachments" multiple>
            <br><br>
            <button type="submit">Save</button>
        </form>
    </div>
</body>
</html>
