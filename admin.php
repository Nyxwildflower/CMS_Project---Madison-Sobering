<?php
    require('connect.php');
    require('check_session.php');

    $sort = isset($_POST['sort']) ? $_POST['sort'] : "p.title";
    $select_values =['p.title'=>'Title','p.created'=>'Created','p.updated'=>'Updated'];

    // Sort isn't bound into the query because the values aren't changeable by the user.
    $list_query = "SELECT * FROM pages p JOIN categories c ON (c.category_id = p.category_id) ORDER BY {$sort}";
    $page_list = $db->prepare($list_query);
    $page_list->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Page List</title>
</head>
<body>
    <?php include('header.php') ?>

    <main class="container">
        <div class="nav justify-content-end">
            <div class="nav-item">
                <a class="ml-3 btn btn-outline-info" href="create.php">Create Page</a>
            </div>
            <div class="nav-item">
                <a class="ml-3 btn btn-outline-info" href="category.php">Manage Categories</a>
            </div>
            <!-- Prevent normal users from performing CRUD tasks on the users table. -->
            <?php if(isset($_SESSION['admin'])): ?>
                <div class="nav-item">
                    <a class="ml-3 btn btn-outline-info" href="users.php">Manage Users</a>
                </div>
            <?php endif ?>
        </div>
    
        <div class="mt-4 alert alert-success alert-dismissible" role="alert">
            <div><?= $_SESSION['message'] ?></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <h2>Page List</h2>

        <form action="admin.php" method="post">
            <div class="input-group">
                <select name="sort" class="custom-select">
                    <?php foreach($select_values as $query_value => $name): ?>
                        <?php if($sort === $query_value): ?>
                            <option selected value="<?= $query_value ?>"><?= $name ?></option>
                        <?php else: ?>
                            <option value="<?= $query_value ?>"><?= $name ?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-outline-dark" type="submit">Sort By</button>
                </div>
            </div>
        </form>

        <table class="table">
            <caption>List of pages</caption>
            <thead>
                <tr>
                    <th scope="col">Page Name</th>
                    <th scope="col">Date Created</th>
                    <th scope="col">Date Updated</th>
                    <th scope="col">Category</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while($page = $page_list->fetch()): ?>
                    <tr>
                        <th scope="row"><a href="edit.php?page_id=<?= $page['page_id'] ?>"><?= $page['title'] ?></a></th>
                        <td><?= $page['created'] ?></td>
                        <td><?= $page['updated'] ?></td>
                        <td><?= $page['category_name'] ?></td>
                        <td><a href="delete.php?page_id=<?= $page['page_id'] ?>">Delete</a></td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>