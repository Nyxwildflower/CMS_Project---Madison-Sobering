<?php
    $query = "SELECT page_id, title FROM pages LIMIT 10";
    $statement = $db->prepare($query);
    $statement->execute();
?>

<header class="container">
    <div class="row">
        <h1 class="col">Museum of Experience</h1>
    </div>

    <div class="row">
        <form class="col" action="login.php" method="post">
            <button class="btn btn-success" type="submit">Login</button>
        </form>
    </div>

    <div class="row">
        <form class="col" action="index.php" method="post">
            <label class="sr-only" for="search">Search</label>
            <input name="search" type="text" placeholder="search">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>

    <nav >
        <ul class="nav justify-content-center">
            <?php while($link = $statement->fetch()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page_id=<?= $link['page_id'] ?>&title=<?= $link['title'] ?>"><?= $link['title'] ?></a>
                </li>
            <?php endwhile ?>
        </ul>
    </nav>
</header>