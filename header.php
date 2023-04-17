<?php
    $query = "SELECT * FROM pages LIMIT 10";
    $statement = $db->prepare($query);
    $statement->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
</head>
<body>
    <header>
        <h1>Museum of Experience</h1>
        <form action="login.php" method="post">
            <button type="submit">Login</button>
        </form>
        <form action="index.php" method="post">
            <label for="search">Search</label>
            <input name="search" type="text">
            <button type="submit">Search</button>
        </form>
        <nav>
            <ul>
                <?php while($link = $statement->fetch()): ?>
                    <li><a href="index.php?page_id=<?= $link['page_id'] ?>&title=<?= $link['title'] ?>"><?= $link['title'] ?></a></li>
                <?php endwhile ?>
            </ul>
        </nav>
    </header>
</body>
</html>