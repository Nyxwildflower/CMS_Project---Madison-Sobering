<?php
    require('connect.php');

    // If post is sent.
    if(!empty($_POST)){
        $search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $search = trim($search);
        $in_category = filter_input(INPUT_POST, 'in_category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $in_category = trim($in_category);

        if(!empty($search)){
            // One category searches for page titles only, the other narrows it down by category as well.
            if(empty($in_category) || $in_category === "Any"){
                $search_query = "SELECT * from pages p LEFT JOIN categories c ON (c.category_id = p.category_id) WHERE p.title LIKE :search";
            }else{
                $search_query = "SELECT * from pages p LEFT JOIN categories c ON (c.category_id = p.category_id) WHERE p.title LIKE :search AND c.category_name = :in_category";
            }

            $formatted_search = "%" . $search . "%";
            $search_statement = $db->prepare($search_query);
            $search_statement->bindValue('search', $formatted_search, PDO::PARAM_STR);

            // Bind category if category was selected.
            if(!empty($in_category) && $in_category !== "Any"){
                $search_statement->bindValue('in_category', $in_category, PDO::PARAM_STR);
            }

            $search_statement->execute();
        
            // Redirect if search was only white space
        }else{
            header("Location: index.php?page_id=2");
            exit("You didn't search anything.");
        }

        // Redirect if the user didn't search for anything.
    }else{
        header("Location: index.php?page_id=2");
        exit("Something went wrong.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Search Results</title>
</head>
<body>
    <?php include('header.php') ?>

    <main class="container">
        <h2 class="mb-4">Search Term: <?= $search ?></h2>

        <?php if($search_statement->rowCount() === 0): ?>
                    <h4 class="text-center">No results match this search.</h4>
        <?php else: ?>
            <table class="table">
                <caption>Search Results</caption>
                    <thead>
                        <tr>
                            <th scope="col">Page Title</th>
                            <th scope="col">Category</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php while($search_result = $search_statement->fetch()): ?>
                        <tr>
                            <th scope="row"><a href="index.php?page_id=<?= $search_result['page_id'] ?>"><?= $search_result['title'] ?></a></th>
                            <td><?= $search_result['category_name'] ?></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        <?php endif ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>