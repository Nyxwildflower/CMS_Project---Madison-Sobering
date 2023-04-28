<?php
    require('connect.php');

    $input = "";
    $errors = [];

    $user_query = "SELECT username, email FROM users";
    $existing_users = $db->prepare($user_query);
    $existing_users->execute();

    if(!empty($_POST)){
        $create_account_inputs = ["email","username","password","check_password"];
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $email = trim($email);
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = trim($username);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = trim($password);
        $check_password = filter_input(INPUT_POST, "check_password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $check_password = trim($check_password);

        // Check for blank fields
        for($i = 0; $i < count($create_account_inputs); $i++){
            $input = $_POST[$create_account_inputs[$i]];

            if(trim($input) === ""){
                $errors[] .= "The field for {$create_account_inputs[$i]} must not be blank.";
            }
        }

        // Check for duplicate usernames and emails.
        while($user_info = $existing_users->fetch()){
            if($user_info['email'] === $email){
                $errors[] .= "This email: {$email} is taken.";
            }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] .= "Please enter a valid email.";
            }

            if($user_info['username'] === $username){
                $errors[] .= "The username: {$username} is taken.";
            }
        }

        // Confirm passwords are the same
        if($password !== $check_password){
            $errors[] .= "The passwords don't match";
        }else{
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
        }

        if(count($errors) === 0){
            $user_query = "INSERT INTO users (email, username, salted_password) VALUES (:email, :username, :salted_password)";
            $add_user = $db->prepare($user_query);
            
            $add_user->bindValue("email", $email, PDO::PARAM_STR);
            $add_user->bindValue("username", $username, PDO::PARAM_STR);
            $add_user->bindValue("salted_password", $hash_password, PDO::PARAM_STR);

            $add_user->execute();
            header("Location: index.php");
            exit("User created.");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Create Account</title>
</head>
<body>
    <?php include('header.php') ?>

    <main class="container"> 
            <h2>Create Account</h2>
            <form action="create_account.php" method="post">
                <label for="email">Email</label>
                <input class="form-control" name="email" type="email" required>

                <label for="username">Username</label>
                <input class="form-control" name="username" type="text" required>

                <label for="password">Password</label>
                <input class="form-control" name="password" type="password" required>

                <label for="check_password">Re-enter Password</label>
                <input class="form-control" name="check_password" type="password" required>

                <a class="row ml-1 mt-2" href="authorize.php">Already have an account? Log in.</a>
                <button class="btn btn-success my-4" type="submit">Create Account</button>
            </form>

        <?php for($i = 0; $i < count($errors); $i++): ?>
            <p class="alert alert-danger"><?= $errors[$i] ?></p>
        <?php endfor ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>