<?php
    require('connect.php');
    require('check_session.php');
    require('image_upload.php');

    $create_errors = [];

    if(!empty($_POST)){
        date_default_timezone_set("America/Winnipeg");
        $current_timestamp = date("Y-m-d h-i-s");
        $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $title = trim($title);
        $tags = '<p><strong><em><u><h1><h2><h3><h4><h5><h6><li><ol><ul><span><pre><blockquote><div><br><ins><del>';
        $rawContent = $_POST['content'];
        $content = strip_tags($rawContent, $tags);
        $content = trim($content);
        // Matches spaces or tabs, but if the style is changed, the content passes as not blank due to the style attribute.
        $space_regex = "/^<p>(&nbsp;\s)+(&nbsp;)+<\/p>$/";
        $not_spaces = preg_match($space_regex, $content);
        $category_id = filter_input(INPUT_POST, "select_category", FILTER_SANITIZE_NUMBER_INT);
        $category_id = trim($category_id);

        if(!isset($title) || $title === ""){
            $create_errors[] .= "Title must not be blank";
        }

        if(!isset($content) || $content === "" || $content === NULL || $not_spaces === 1){
            $create_errors[] .= "Content must not be blank";
        }

        if(!(isset($category_id) || filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT))){
            $create_errors[] .= "This category doesn't exist";
        }

        if(count($create_errors) === 0 && count($image_errors) === 0){
            $file_is_selected = isset($_FILES['upload_image']) && ($_FILES['upload_image']['error'] === 0);
            $file_error = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

            $create_query = "INSERT INTO pages (user_id, title, content, created, category_id, image_file) VALUES (:user_id, :title, :content, :created, :category_id, :image_file)";

            $create_page = $db->prepare($create_query);

            $create_page->bindValue('user_id', $_SESSION['id'], PDO::PARAM_INT);
            $create_page->bindValue('title', $title, PDO::PARAM_STR);
            $create_page->bindValue('content', $content, PDO::PARAM_STR);
            $create_page->bindValue('created', $current_timestamp, PDO::PARAM_STR);
            $create_page->bindValue('category_id', $category_id, PDO::PARAM_INT);
            $create_page->bindValue('image_file', $medium_file);

            $create_page->execute();

            header("Location: admin.php?manage=pages");
            exit("Page created.");
        }
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

    <script src="https://cdn.tiny.cloud/1/cfxecaywr83guf9liq8zsidbrkp5qikqla1xshy7jamyn50r/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#editor'
      });
    </script>
</head>
<body>
    <?php include('header.php') ?>    

    <main class="container">
        <form action="create.php" method="post" enctype='multipart/form-data'>
            <label for="title">Title</label>
            <input class="form-control" name="title" type="text"/>

            <label for="content"></label>
            <textarea class="form-control" id="editor" name="content"></textarea>

            <label class="mt-3" for="select_category">Select a category that this page best fits</label>
            <select class="form-control" name="select_category" id="select_category">
                <?php while($category = $categories->fetch()): ?>
                        <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                <?php endwhile ?>
            </select>

            <label class="mt-3" for="upload_image">Add an image</label>
            <input class="form-control-file" name="upload_image" type="file"/>

            <button class="btn btn-success my-5" type="submit">Submit</button>
        </form>

        <?php for($i = 0; $i < count($create_errors); $i++): ?>
            <p class="alert alert-danger"><?= $create_errors[$i] ?></p>
        <?php endfor ?>

        <?php if(count($image_errors) > 0): ?>
            <p class="alert alert-danger"><?= $image_errors[0] ?></p>
        <?php endif ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>