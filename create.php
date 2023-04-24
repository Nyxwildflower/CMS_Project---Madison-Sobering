<?php
    require('connect.php');
    require('check_session.php');

    if($_POST){
        date_default_timezone_set("America/Winnipeg");
        $current_timestamp = date("Y-m-d h-i-s");
        $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $title = trim($title);
        $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = trim($content);
        $category_id = filter_input(INPUT_POST, "select_category", FILTER_SANITIZE_NUMBER_INT);

        $create_query = "INSERT INTO pages (user_id, title, content, created, category_id) VALUES (:user_id, :title, :content, :created, :category_id)";

        $create_page = $db->prepare($create_query);
        $create_page->bindValue('user_id', 1, PDO::PARAM_INT);
        $create_page->bindValue('title', $title, PDO::PARAM_STR);
        $create_page->bindValue('content', $content, PDO::PARAM_STR);
        $create_page->bindValue('created', $current_timestamp, PDO::PARAM_STR);
        $create_page->bindValue('category_id', $category_id, PDO::PARAM_INT);
        $create_page->execute();


        // Need to check character limits on title and content
        header("Location: index.php");
        exit("Page created.");
    }

    $category_query = "SELECT * FROM categories";
    $categories = $db->prepare($category_query);
    $categories->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Create Page</title>
</head>
<body>
    <?php include('header.php') ?>    

    <form class="container" action="create.php" method="post">
        <label for="title">Title</label>
        <input class="form-control" name="title" type="text"/>

        <label for="content"></label>
        <textarea class="form-control" id="create_content" name="content" id="" rows="10"></textarea>

        <label for="select_category">Select a category that this page best fits</label>
        <select class="form-control" name="select_category" id="select_category">
            <?php while($category = $categories->fetch()): ?>
                    <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
            <?php endwhile ?>
        </select>

        <button class="btn btn-success mt-5" type="submit">Submit</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>