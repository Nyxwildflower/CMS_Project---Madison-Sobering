<?php
    require('connect.php');
    require('authorize.php');
    
    $query = "SELECT * FROM categories";
    $statement = $db->prepare($query);
    $statement->execute();

    if($_POST){
        $current_timestamp = date("F d, Y, h:i a");
        $title = filter_input(INPUT_POST, "text", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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

        header("Location: index.php");
        exit("Page created.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Page</title>
</head>
<body>    
    <form action="create.php" method="post">
        <label for="title">Title</label>
        <input name="title" type="text"/>

        <label for="content"></label>
        <textarea id="create_content" name="content" id="" rows="10"></textarea>

        <label for="select_category">Select Category that this page best fits:</label>
        <select name="select_category" id="select_category">
            <?php while($category = $statement->fetch()): ?>
                <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
            <?php endwhile ?>
        </select>
        <button type="submit">Submit</button>
    </form>
</body>
</html>