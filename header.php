<!-- This is the header template that's added to most of the files. It holds the search form and navbar links to pages and login/logout. -->

<?php    
    $query = "SELECT page_id, title FROM pages";
    $statement = $db->prepare($query);
    $statement->execute();

    $category_search_query = "SELECT * FROM categories";
    $category_search_statement = $db->prepare($category_search_query);
    $category_search_statement->execute();
?>

<header class="container-fluid bg-light mb-4">
    <div class="container">
        <h1 class="display-4 pt-5 mb-4 text-center">Grand Museum of Passengers</h1>

        <nav class="navbar navbar-expand-md">
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
                            <a class="dropdown-item" href="index.php?page_id=<?= $link['page_id'] ?>"><?= $link['title'] ?></a>
                        <?php endwhile ?>
                    </div>
                </li>
            </ul>

            <form action="search.php" method="post" class="form-inline my-2 my-lg-0">
                <div class="input-group">
                    <label class="sr-only" for="search">Search</label>
                    <input type="text" name="search" class="form-control" id="search" placeholder="Search Titles.."/>
                    <select name="in_category" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <option class="dropdown-item" value="Any">Any</option>
                        <?php while($category_specify = $category_search_statement->fetch()): ?>
                            <option class="dropdown-item" value="<?= $category_specify['category_name'] ?>"><?= $category_specify['category_name'] ?></option>
                        <?php endwhile ?>
                    </select> 
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </div>
                </div>
            </form>
        </nav>
    </div>
</header>