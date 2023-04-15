<?php
    require('connect.php');
    require('authorize.php');
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Page</title>
</head>
<body>    
    <form action="create.php" method="post">
        <label>Title</label>
        <input />

        <label for="content"></label>
        <textarea id="create_content" name="content" id="" rows="10"></textarea>

        <label for="select_category">Select Category that this page best fits:</label>
        <select name="select_category" id="select_category">
            <option value=""></option>
        </select>
    </form>
</body>
</html>