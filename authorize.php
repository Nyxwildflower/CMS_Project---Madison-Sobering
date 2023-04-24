<?php
	require('connect.php');
    session_start();

	if($_POST){
        // Sanitize username because it's used in the database query.
		$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$password = $_POST['password'];

        // Collecting bare minimum user info to verify the login and admin status.
		$query = "SELECT admin_verify, username, salted_password FROM users WHERE username = :username";
		$statement = $db->prepare($query);

		$statement->bindValue("username", $username, PDO::PARAM_STR);

		$statement->execute();
		$user = $statement->fetch();

		if($statement->rowCount() > 0 && password_verify($password, $user['salted_password'])){
			if($user['admin_verify'] >= 1){
                $_SESSION['admin'] = $user['username'];
            }elseif($user['admin_verify'] === 0){
                $_SESSION['user'] = $user['username'];
            }
			
			$_SESSION['message'] = "Login successful, welcome {$user['username']}.";
            $error = NULL;

            header("Location: admin.php");
            exit();
		}else{
			$error = "Username or password is incorrect.";
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
    <title>Login</title>
</head>
<body>
    <?php include('header.php') ?>

    <div class="container">
        <h2>Login</h2>
        <form action="authorize.php" method="post">
            <label for="username">Username</label>
            <input class="form-control" name="username" type="text" required>

            <label for="password">Password</label>
            <input class="form-control" name="password" type="password" required>

            <button class="btn btn-success mt-4 mb-2" type="submit">Login</button>
            <a class="btn btn-success mt-4 mb-2" role="button" href="create_account.php">Create Account</a>
        </form>

        <?php if(isset($error)): ?>
            <p class="alert alert-danger"><?= $error ?></p>
        <?php endif ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

