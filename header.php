<?php    
    $query = "SELECT page_id, title FROM pages";
    $statement = $db->prepare($query);
    $statement->execute();
?>

<header class="container-fluid bg-light mb-4">
    <div class="container">
        <h1 class="display-3 pt-5 mb-4 text-center">Museum of Experience</h1>

        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?manage=pages">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_account.php">Create Account</a>
                    </li>
                    <?php if(isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php endif ?>
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pages</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <?php while($link = $statement->fetch()): ?>
                                <a class="dropdown-item" href="index.php?page_id=<?= $link['page_id'] ?>&title=<?= $link['title'] ?>"><?= $link['title'] ?></a>
                            <?php endwhile ?>
                        </div>
                    </li>
                </ul>
            </div>

            <form action="index.php?ask=search" method="post" class="form-inline my-2 my-lg-0">
                <label class="sr-only" for="search">Search</label>
                <input type="text" class="form-control mr-sm-2" placeholder="Search Titles...">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
            </form>
        </nav>
    </div>
</header>