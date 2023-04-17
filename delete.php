<?php
    require('connect.php');

    if(isset($_GET['page_id']) && filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT)){
        $page_id = $_GET['page_id'];

        $query = "DELETE FROM pages WHERE page_id = :page_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue('page_id', $page_id, PDO::PARAM_INT);
        $statement->execute();
    
        header("Location: index.php");
        exit("Deleted page successfully.");
    }else{
        header("Location: index.php");
        exit("Delete failed.");
    }
?>