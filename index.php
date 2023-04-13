<?php
    require('connect.php');

    $page_query = "SELECT p.*, c.*, u.username FROM pages p JOIN users u ON (u.user_id = p.user_id) JOIN categories c ON (c.category_id = p.category_id) LIMIT 1";

    $statement = $db->prepare($page_query);
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
    <title>PDO SELECT</title>
</head>
<body>    
    <h2><?= $page['title'] ?></h2>
    <h3>By: <?= $page['username'] ?></h3>
    <h3><?= $page['category_name'] ?></h3>
    <div><?= $page['content'] ?></div>
    <div>
        <?php while($comment = $comments->fetch()): ?>
            <div>
                <h3><?= $comment['username'] ?></h3>
                <p><?= $comment['comment_content'] ?></p>
            </div>
        <?php endwhile ?>
    </div>
</body>
</html> 