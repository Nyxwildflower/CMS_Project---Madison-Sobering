<?php
    require('connect.php');

    $query = "SELECT p.*, c.comment_content, c.publish_time FROM pages p JOIN comments c ON (p.page_id = c.page_id)";

    $statement = $db->prepare($query);

    $statement->execute(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>PDO SELECT</title>
</head>
<body>
    <h1>Found <?= $statement->rowCount() ?> Rows</h1>
    
    <ul>
        <!-- Fetch each table row in turn. Each $row is a table row hash.
             Fetch returns FALSE when out of rows, halting the loop. -->
            <?php while($row = $statement->fetch()): ?>
                <li><?= $row['title'] ?> Content: <?= $row['content'] ?></li>
                <li><?= $row['comment_content'] ?> from <?= $row['publish_time']?></li>
            <?php endwhile ?>
    </ul>
</body>
</html> 