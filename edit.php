<?php
    require('connect.php');
    require('authorize.php');
    
    if(isset($_GET['page_id']) && filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT)){
        $page_id = $_GET['page_id'];

        $get_page = "SELECT * FROM pages WHERE page_id = :page_id LIMIT 1";
        $get_statement = $db->prepare($get_page);
        
        $get_statement->bindValue('page_id', $page_id, PDO::PARAM_INT);
        
        $get_statement->execute();
        $page = $get_statement->fetch();
    }

    if($_POST){        
        $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $title = trim($title);
        $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = trim($content);
        $category_id = filter_input(INPUT_POST, "select_category", FILTER_SANITIZE_NUMBER_INT);
        $page_id = filter_input(INPUT_POST, "page_id", FILTER_SANITIZE_NUMBER_INT);
        
        $edit_page = "UPDATE pages SET title = :title, content = :content, category_id = :category_id WHERE page_id = :page_id LIMIT 1";
        $edit_statement = $db->prepare($edit_page);
        
        $edit_statement->bindValue('title', $title, PDO::PARAM_STR);
        $edit_statement->bindValue('content', $content, PDO::PARAM_STR);
        $edit_statement->bindValue('category_id', $category_id, PDO::PARAM_INT);
        $edit_statement->bindValue('page_id', $page_id, PDO::PARAM_INT);

        $edit_statement->execute();

        header("Location: index.php");
        exit("Edit Successful");
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
    <title>Edit Page</title>
</head>
<body>
    <?= include('header.php') ?>    

    <form class="container" action="edit.php" method="post">
        <input name="page_id" type="hidden" value="<?= $page['page_id'] ?>">

        <label for="title">Title</label>
        <input class="form-control" name="title" type="text" value="<?= $page['title'] ?>" />

        <label for="content"></label>
        <textarea class="form-control" id="create_content" name="content" rows="10"><?= $page['content'] ?></textarea>

        <label for="select_category">Select a category that this page best fits</label>
        <select class="form-control" name="select_category" id="select_category">
            <?php while($category = $categories->fetch()): ?>
                <?php if($page['category_id'] == $category['category_id']): ?>
                    <option selected value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                <?php else: ?>
                    <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                <?php endif ?>
            <?php endwhile ?>
        </select>

        <button class="btn btn-success" type="submit">Submit</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>