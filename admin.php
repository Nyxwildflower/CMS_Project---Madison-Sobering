<?php
    require('connect.php');
    require('check_session.php');

    if($_GET['manage'] === "pages"){
        $sort = isset($_POST['sort']) ? $_POST['sort'] : "p.title";
        $select_values =['p.title'=>'Title','p.created'=>'Created','p.updated'=>'Updated'];

        // Sort isn't bound into the query because the values aren't changeable by the user.
        $list_query = "SELECT * FROM pages p LEFT JOIN categories c ON (c.category_id = p.category_id) ORDER BY {$sort}";
        $page_list = $db->prepare($list_query);
        $page_list->execute();

    }elseif($_GET['manage'] === "categories"){
        $category_query = "SELECT * FROM categories ORDER BY category_name";
        $categories = $db->prepare($category_query);
        $categories->execute();

        if(isset($_POST['command'])){
            $category_name = isset($_POST['category_name']) ? filter_input(INPUT_POST, "category_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS) : NULL;
            $category_name = trim($category_name);
            $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : NULL;
            
            if($_POST['command'] === "create" && $category_name !== "" && isset($category_name)){
                $create = "INSERT INTO categories (category_name) VALUES (:category_name)";
                $create_statement = $db->prepare($create);
    
                $create_statement->bindValue("category_name", $category_name, PDO::PARAM_STR);
    
                $create_statement->execute();
            }elseif($_POST['command'] === "edit" && $category_name !== "" && isset($category_name)){
                $edit = "UPDATE categories SET category_name = :category_name WHERE category_id = :category_id LIMIT 1";
                $edit_statement = $db->prepare($edit);

                $edit_statement->bindValue("category_name", $category_name, PDO::PARAM_STR);
                $edit_statement->bindValue("category_id", $category_id, PDO::PARAM_INT);

                $edit_statement->execute();
            }elseif($_POST['command'] === "delete"){
                $delete = "DELETE FROM categories WHERE category_id = :category_id LIMIT 1";
                $delete_statement = $db->prepare($delete);

                $delete_statement->bindValue("category_id", $category_id, PDO::PARAM_INT);

                $delete_statement->execute();
            }

            header("Location: admin.php?manage=categories");
        }
    }elseif($_GET['manage'] === "users" && isset($_SESSION['admin'])){
        $user_type = [0 => 'User', 1 => 'Admin'];
        $logout = false;
        $user_query = "SELECT user_id, admin_verify, username, email FROM users";

        $users = $db->prepare($user_query);
        $users->execute();

        $admin_query = "SELECT admin_verify FROM users WHERE admin_verify = 1";
        $admin_count = $db->prepare($admin_query);
        $admin_count->execute();
        $admins = $admin_count->rowCount();

        if(!empty($_POST) && filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT)){
            $admin_verify = filter_input(INPUT_POST, 'admin_verify', FILTER_SANITIZE_NUMBER_INT);
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $username = trim($username);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $email = trim($email);
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

            if($_POST['command'] === "edit" && $username !== "" && $email !== "" && filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)){
                $user_update = "UPDATE users SET admin_verify = :admin_verify, username = :username, email = :email WHERE user_id = :user_id LIMIT 1";

                $user_edit = $db->prepare($user_update);

                $user_edit->bindValue("admin_verify", $admin_verify, PDO::PARAM_INT);
                $user_edit->bindValue("username", $username, PDO::PARAM_STR);
                $user_edit->bindValue("email", $email, PDO::PARAM_STR);
                $user_edit->bindValue("user_id", $user_id, PDO::PARAM_INT);

                $user_edit->execute();
            }elseif($_POST['command'] === "delete"){
                if($admins > 1 || (int)$admin_verify === 0){
                    if($_SESSION['id'] === (int)$user_id){
                        $logout = true;
                    }

                    $delete_user = "DELETE FROM users WHERE user_id = :user_id LIMIT 1";
                    $user_delete_statement = $db->prepare($delete_user);
                
                    $user_delete_statement->bindValue("user_id", $user_id, PDO::PARAM_INT);

                    $user_delete_statement->execute();
                }else{






                    // THIS DOESN'T WORK
                    $admin_error = "There must be one admin.";
                }
            }

            // Logs user out if they delete theirself.
            if($logout){
                header("Location: logout.php");
            }else{
                header("Location: admin.php?manage=users");
            }
        }
    }else{
        // Returns to pages admin if get value doesn't work
        header("Location: admin.php?manage=pages");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Admin</title>
</head>
<body>
    <?php include('header.php') ?>

    <main class="container">
        <div class="navbar navbar-expand-sm">
            <div class="navbar-nav flex-wrap" role="navigation">
                <div class="nav-item d-inline-flex">
                    <a class="mx-2 mb-3 btn btn-outline-info" href="create.php">Create Page</a>
                </div>
                <div class="nav-item d-inline-flex">
                    <a class="mx-2 mb-3 btn btn-outline-info" href="admin.php?manage=categories">Manage Categories</a>
                </div>
                <div class="nav-item d-inline-flex">
                    <a class="mx-2 mb-3 btn btn-outline-info" href="create_account.php">Create a new User</a>
                </div>
                <div class="nav-item d-inline-flex">
                    <!-- Prevent normal users from performing CRUD tasks on the users table. -->
                    <?php if(isset($_SESSION['admin'])): ?>
                        <a class="mx-2 mb-3 btn btn-outline-info" href="admin.php?manage=users">Manage Users</a>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <?php if($_GET['manage'] === "pages"): ?>
            <div class="mt-4 alert alert-success alert-dismissible" role="alert">
                <div><?= $_SESSION['message'] ?></div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <h2>Page List</h2>

            <form action="admin.php?manage=pages" method="post">
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

            <div class="table-responsive-lg">
                <table class="table">
                    <caption>List of Pages</caption>
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
                                <td><a class="btn btn-outline-danger" href="delete.php?page_id=<?= $page['page_id'] ?>">Delete</a></td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        <?php elseif($_GET['manage'] === "categories"): ?>
            <h2>Category List</h2>
            
            <form class="mt-5" action="admin.php?manage=categories" method="post">
                <legend>Create New Category</legend>    

                <label class="sr-only" for="category_name">Category Name</label>
                <input class="form-control" type="text" name="category_name"/>

                <input type="hidden" name="command" value="create"/>
                <button class="btn btn-success mt-3" type="submit">Create</button>
            </form>

            <div class="table-responsive-md">
                <table class="table mt-5">
                    <caption>List of Categories</caption>
                    <thead>
                        <tr>
                            <th scope="col">Category Name</th>
                            <th scope="col">Update</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($category = $categories->fetch()): ?>
                            <tr>
                                <th scope="row"><?= $category['category_name'] ?></th>
                                <td>
                                    <!-- Edit form within the table because it's just one value. -->
                                    <form action="admin.php?manage=categories" method="post">
                                        <label class="sr-only" for="edit_category">Edit Category</label>

                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="category_name" value="<?= $category['category_name'] ?>"/>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">Edit</button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="command" value="edit"/>
                                        <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>"/>
                                    </form>
                                </td>
                                <td>
                                    <form action="admin.php?manage=categories" method="post">
                                        <input type="hidden" name="command" value="delete"/>
                                        <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>"/>
                                        
                                        <button class="btn btn-outline-danger" type="submit">Delete Category</button>
                                    </form>    
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        <?php elseif($_GET['manage'] === "users"): ?>            
            <h2>User List</h2>

            <div class="table-responsive-lg">
                <table class="table mt-5">
                    <caption>List of Users</caption>
                    <thead>
                        <tr>
                            <th scope="col">User Type</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch()): ?>
                            <tr>
                                <form action="admin.php?manage=users" method="post">
                                    <th scope="row">
                                        <select name="admin_verify" class="custom-select">
                                            <?php foreach($user_type as $value => $type): ?>
                                                <?php if($user['admin_verify'] === $value): ?>
                                                    <option selected value="<?= $value ?>"><?= $type ?></option>
                                                <?php else: ?>
                                                    <option value="<?= $value ?>"><?= $type ?></option>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </select>                                    
                                    </th>
                                    <td>
                                        <input class="form-control" name="username" type="text" value="<?= $user['username'] ?>"/>
                                    </td>
                                    <td>
                                        <input class="form-control" name="email" type="email" value="<?= $user['email'] ?>"/>
                                    </td>
                                    <td>
                                        <input type="hidden" name="command" value="edit"/>
                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>"/>
                                        <button class="btn btn-outline-secondary" type="submit">Update User</button>
                                    </td>
                                </form>
                                <td>
                                    <form action="admin.php?manage=users" method="post">
                                        <input type="hidden" name="command" value="delete"/>
                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>"/>
                                        <input type="hidden" name="admin_verify" value="<?= $user['admin_verify'] ?>"/>
                                        
                                        <button class="btn btn-outline-danger" type="submit">Delete User</button>
                                    </form>    
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
            
            <p class="alert alert-danger"><?= $admin_error ?></p>
        <?php endif ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>