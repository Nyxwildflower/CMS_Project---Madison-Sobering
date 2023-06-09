<!-- This form edits a page based on the passed page_id. It also has TinyMCE, image create and delete features, 
     and redirects to admin.php. -->

<?php
    require('connect.php');
    require('check_session.php');
    require('image_upload.php');
    
    $edit_errors = [];

    // Prevent page_id GET shenanigans.
    if(isset($_GET['page_id'])){
        $page_id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        $page_id = trim($page_id);

        $get_page = "SELECT * FROM pages WHERE page_id = :page_id LIMIT 1";

        if(!(isset($page_id) && filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT) && $page_id > 0)){
            $page_id = 0;
        }

        $get_statement = $db->prepare($get_page);
        
        $get_statement->bindValue('page_id', $page_id, PDO::PARAM_INT);
        
        $get_statement->execute();
        $page = $get_statement->fetch();

        if($get_statement->rowCount() === 0){
            header("Location: admin.php?manage=pages");
        }
    }else{
        header("Location: admin.php?manage=pages");
        exit("Invalid page_id");
    }

    if(!empty($_POST)){        
        $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $title = trim($title);
        // Remove any html tags that aren't listed here. Any other hack protection is provided by TinyMCE or binding values.
        $tags = '<p><strong><em><u><h1><h2><h3><h4><h5><h6><li><ol><ul><span><pre><blockquote><div><br><ins><del><sup><sub><s>';
        $rawContent = $_POST['content'];
        $content = strip_tags($rawContent, $tags);
        $content = trim($content);
        // Regex for spaces, but not styled tags.
        $space_regex = "/^<p>(&nbsp;\s)+(&nbsp;)+<\/p>$/";
        $not_spaces = preg_match($space_regex, $content);
        $category_id = filter_input(INPUT_POST, "select_category", FILTER_SANITIZE_NUMBER_INT);
        $category_id = trim($category_id);
        $edit_page_id = filter_input(INPUT_POST, "edit_page_id", FILTER_SANITIZE_NUMBER_INT);
        $edit_page_id = trim($edit_page_id);

        if(!isset($title) || $title === ""){
            $edit_errors[] .= "Title must not be blank";
        }

        if(!isset($content) || $content === "" || $content === NULL || $not_spaces === 1){
            $edit_errors[] .= "Content must not be blank";
        }

        if(!(isset($category_id) || filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT))){
            $edit_errors[] .= "This category doesn't exist";
        }

        if(!(isset($edit_page_id) || filter_input(INPUT_POST, 'edit_page_id', FILTER_VALIDATE_INT))){
            $edit_errors[] .= "The page id is invalid. Please go back to the admin page.";
        }

        if(count($edit_errors) === 0 && count($image_errors) === 0){

            // Check for no database image, and no image being added.
            if($page['image_file'] === NULL && empty($rename_image_file)){
                $current_image = NULL;

                // Check for no file in database, but new file being uploaded.
            }elseif($page['image_file'] === NULL && !empty($rename_image_file)){
                $current_image = $rename_image_file;

                // Check for the delete checkbox being selected.
            }elseif($page['image_file'] !== NULL && isset($_POST['upload_image']) && $_POST['upload_image'] === "remove_image"){
                delete_file($page['image_file']);
                $current_image = NULL;

                // Check for no delete, but existing image.
            }elseif($page['image_file'] !== NULL && !isset($_POST['upload_image']) && $_POST['upload_image'] !== "remove_image"){
                $current_image = $page['image_file'];
            }

            $edit_page = "UPDATE pages SET title = :title, content = :content, category_id = :category_id, image_file = :image_file WHERE page_id = :edit_page_id LIMIT 1";
            $edit_statement = $db->prepare($edit_page);
            
            $edit_statement->bindValue('title', $title, PDO::PARAM_STR);
            $edit_statement->bindValue('content', $content, PDO::PARAM_STR);
            $edit_statement->bindValue('category_id', $category_id, PDO::PARAM_INT);
            $edit_statement->bindValue('image_file', $current_image);
            $edit_statement->bindValue('edit_page_id', $edit_page_id, PDO::PARAM_INT);

            $edit_statement->execute();

            header("Location: admin.php?manage=pages");
            exit("Edit Successful");
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
    <title>Edit Page</title>

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
        <form action="edit.php?page_id=<?= $page['page_id'] ?>" method="post" enctype="multipart/form-data">
            <input name="edit_page_id" type="hidden" value="<?= $page['page_id'] ?>">

            <label for="title">Title</label>
            <input class="form-control" name="title" type="text" id="title" value="<?= $page['title'] ?>" />

            <label>Content</label>
            <textarea class="form-control" id="editor" name="content"><?= $page['content'] ?></textarea>

            <label class="mt-3" for="select_category">Select a category that this page best fits</label>
            <select class="form-control" name="select_category" id="select_category">
                <?php while($category = $categories->fetch()): ?>
                    <?php if($page['category_id'] == $category['category_id']): ?>
                        <option selected value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                    <?php else: ?>
                        <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                    <?php endif ?>
                <?php endwhile ?>
            </select>

            <?php if($page['image_file'] !== NULL): ?>
                <div class="form-row mt-3">
                    <div class="col">
                        <p>Current Image: <?= $page['image_file'] ?></p>
                    </div>
                    <div class="col form-check mb-2 mr-sm-2">
                        <input class="upload_image" type="checkbox" name="upload_image" id="inlineFormCheck" value="remove_image">
                        <label class="form-check-label">Delete Image?</label>
                    </div>
                </div>
            <?php else: ?>
                <label class="mt-3" for="upload_image">Add an image</label>
                <input class="form-control-file" name="upload_image" id="upload_image" type="file"/>
            <?php endif ?>

            <button class="btn btn-success my-5" type="submit">Submit</button>
        </form>

        <?php for($i = 0; $i < count($edit_errors); $i++): ?>
            <p class="alert alert-danger"><?= $edit_errors[$i] ?></p>
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