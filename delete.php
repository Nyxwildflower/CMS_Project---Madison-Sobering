<?php
    // Deletes a page based off of the page_id passed. If an image exists with it, it's also deleted.
    require('connect.php');
    require('check_session.php');
    require('image_upload.php');

    if(isset($_GET['page_id']) && filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT)){
        $page_id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT);

        // Removes any image associated with the row from the images file.
        $image_exists = "SELECT image_file FROM pages WHERE page_id = :page_id LIMIT 1";
        $image_goes_too = $db->prepare($image_exists);
        $image_goes_too->bindValue('page_id', $page_id, PDO::PARAM_INT);
        $image_goes_too->execute();

        if($image_goes_too->rowCount() > 0){
            $image_to_delete = $image_goes_too->fetch();
            delete_file($image_to_delete['image_file']);
        }

        $query = "DELETE FROM pages WHERE page_id = :page_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue('page_id', $page_id, PDO::PARAM_INT);
        $statement->execute();
    
        header("Location: admin.php?manage=pages");
        exit("Deleted page successfully.");
    }else{
        header("Location: index.php");
        exit("Delete failed.");
    }
?>