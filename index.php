<!-- Index is the CMS home that displays the pages based off of page id, an displays comments. -->

<?php
    require('connect.php');
    session_start();

    $deleted_all_pages = false;

    if(!isset($_GET['page_id'])){
        // If no link is clicked, or GET is empty, get the first page in the database
        $page_query = "SELECT p.*, c.*, u.username FROM pages p LEFT JOIN users u ON (u.user_id = p.user_id) LEFT JOIN categories c ON (c.category_id = p.category_id) LIMIT 1";
    }else{
        $id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        $id = trim($id);

        // Get the selected page info with the connected user and category.
        $page_query = "SELECT p.*, c.*, u.username FROM pages p LEFT JOIN users u ON (u.user_id = p.user_id) LEFT JOIN categories c ON (c.category_id = p.category_id) WHERE p.page_id = :id LIMIT 1";
    }

    $statement = $db->prepare($page_query);

    // If page_id is set, bind id value.
    if(isset($id) && filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT) && $id > 0){
        $statement->bindValue('id', $id, PDO::PARAM_INT);
    }elseif(isset($_GET['page_id'])){
        $id = 0;
        $statement->bindValue('id', $id, PDO::PARAM_INT);
    }
    
    $statement->execute();
    $page = $statement->fetch();

    // If no page has that id, return to index main page.
    if($statement->rowCount() === 0){
        // Stops constant redirecting when all pages are deleted.
        if(!isset($_GET['page_id'])){
            $deleted_all_pages = true;
        }else{
            header("Location: index.php");
        }
    }else{
        // No sanitization because the value is from the database and should already be sanitized.
        $page_id = $page['page_id'];

        // Separate query to find comments 
        $comment_query = "SELECT c.*, u.username FROM comments c JOIN users u ON (u.user_id = c.user_id) WHERE c.page_id = :page_id ORDER BY c.publish_time DESC";
        
        $comments = $db->prepare($comment_query);
        $comments->bindValue('page_id', $page_id, PDO::PARAM_INT);
        $comments->execute();
    }

    // Create comment query.
    if(isset($_POST)){
        $content = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = trim($content);
        $id = filter_input(INPUT_POST, 'page_id', FILTER_SANITIZE_NUMBER_INT);

        if(isset($content) && $content !== "" && filter_var($id, FILTER_VALIDATE_INT)){
            $create_comment = "INSERT INTO comments (comment_content, page_id, user_id) VALUES (:comment_content, :page_id, :user_id)";
        
            $comment_statement = $db->prepare($create_comment);
        
            $comment_statement->bindValue('comment_content', $content, PDO::PARAM_STR);
            $comment_statement->bindValue('page_id', $id, PDO::PARAM_INT);
            $comment_statement->bindValue('user_id', $_SESSION['id'], PDO::PARAM_INT);

            $comment_statement->execute();

            header("Location: index.php?page_id={$id}");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Grand Museum of Passengers</title>
</head>
<body>
    <?php include('header.php') ?>

    <?php if($deleted_all_pages): ?>
        <main class="container">
            <h2 class="text-center mt-5">There are no pages to view, please create a new one.</h2>
        </main>
    <?php else: ?>
        <main class="container mb-4">
            <h2 class="text-center  mb-4"><?= $page['title'] ?></h2>
            <div class="row">
                <?php if($page['user_id'] === NULL): ?>
                    <h4 class="col-auto">Author no longer exists</h4>
                <?php else: ?>
                    <h4 class="col-auto">By: <?= $page['username'] ?></h4>
                <?php endif ?>
                <?php if($page['category_id'] === NULL): ?>
                    <h4 class="col-auto"><span class="badge badge-pill badge-dark">No category</span></h4>
                <?php else: ?>
                    <h4 class="col-auto"><span class="badge badge-pill badge-dark"><?= $page['category_name'] ?></span></h4>
                <?php endif ?>
            </div>
            <div class="row">
                <div class="col-auto text-muted">Created: <?= $page['created'] ?></div>
                <div class="col-auto text-muted">Updated: <?= $page['updated'] ?></div>
            </div>

            <?php if($page['image_file'] !== NULL): ?>
                <div class="text-center my-5">
                    <img class="rounded" src="images/<?= $page['image_file'] ?>" alt="<?= $page['image_file'] ?>"/>
                </div>
            <?php endif ?>
            <div class="container mt-5"><?= $page['content'] ?></div>
        </main>

        <div class="container">
            <h2 class="mb-4">Comments</h2>

            <?php if(isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>
                <form class="container mb-3" action="index.php" method="post">
                    <div class="form-group">
                        <label for="comment">Write a comment:</label>
                        <textarea class="form-control" name="comment" id="comment" rows="5"></textarea>    
                    </div>

                    <input type="hidden" name="create" value="create_comment">
                    <input type="hidden" name="page_id" value="<?= $page_id ?>">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Comment</button>
                </form>
            <?php endif ?>

            <?php if($comments->rowCount() === 0): ?>
                <div class="alert alert-secondary">There are no comments.</div>
            <?php else: ?>
                <?php while($comment = $comments->fetch()): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><?= $comment['username'] ?></h5>
                            <h6 class="text-muted"><?= $comment['publish_time'] ?></h6>
                        </div>
                        <div class="card-body">
                            <p><?= $comment['comment_content'] ?></p>
                        </div>
                    </div>
                <?php endwhile ?> 
            <?php endif ?>
        </div>
    <?php endif ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 