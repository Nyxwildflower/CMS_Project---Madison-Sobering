<?php
    require('connect.php');

    $page_query = "SELECT p.*, c.*, u.username FROM pages p JOIN users u ON (u.user_id = p.user_id) JOIN categories c ON (c.category_id = p.category_id) WHERE p.page_id = :id LIMIT 1";

    $id = isset($_GET['page_id']) ? $_GET['page_id'] : 2;

    $statement = $db->prepare($page_query);
    $statement->bindValue('id', $id, PDO::PARAM_INT);
    $statement->execute();
    $page = $statement->fetch();

    $page_id = $page['page_id'];

    // Separate query to find comments 
    $comment_query = "SELECT c.*, u.username FROM comments c JOIN users u ON (u.user_id = c.user_id) WHERE c.page_id = :page_id";
    
    $comments = $db->prepare($comment_query);
    $comments->bindValue('page_id', $page_id, PDO::PARAM_INT);
    $comments->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Museum of Experience</title>
</head>
<body>
    <?php include('header.php') ?>

    <div class="container">
        <h2><?= $page['title'] ?></h2>
        <h3>By: <?= $page['username'] ?></h3>
        <h3><?= $page['category_name'] ?></h3>
        <div><?= $page['content'] ?></div>
        <div><?= $page['created'] ?></div>
    </div>

    <div class="container">
        <a class="btn btn-dark" href="create.php">Create Page</a>
        <a class="btn btn-dark" href="edit.php?page_id=<?= $page['page_id'] ?>">Edit Page</a>
        <a class="btn btn-dark" href="delete.php?page_id=<?= $page['page_id'] ?>">Delete Page</a>
    </div>

    <div class="container">
        <h2>Comments</h2>
        <?php while($comment = $comments->fetch()): ?>
            <div>
                <h3><?= $comment['username'] ?></h3>
                <p><?= $comment['comment_content'] ?></p>
            </div>
        <?php endwhile ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 