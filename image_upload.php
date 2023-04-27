<?php
    require('\xampp\htdocs\wd2\assignments\CMS_Project - Madison Sobering\php-image-resize-master\lib\ImageResize.php');
    require('\xampp\htdocs\wd2\assignments\CMS_Project - Madison Sobering\php-image-resize-master\lib\ImageResizeException.php');

    // Check for file or error.
    $file_is_selected = isset($_FILES['upload_image']) && ($_FILES['upload_image']['error'] === 0);
    $file_error = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

    // Creates a path for temporary file.
    function file_upload($original_filename, $upload_subfolder_name = 'images'){
        $current_folder = dirname(__FILE__);
        $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

        return join(DIRECTORY_SEPARATOR, $path_segments);
    }

    // File type check.
    function file_is_an_image($temporary_image_path, $new_image_path){
        $accepted_mime_types = ["image/jpeg", "image/gif", "image/png"];
        $accepted_file_extensions = ["jpg", "jpeg", "gif", "png"];

        $actual_mime_type = mime_content_type($temporary_image_path);
        $actual_file_extension = pathinfo($new_image_path, PATHINFO_EXTENSION);

        $mime_type_is_valid = in_array($actual_mime_type, $accepted_mime_types);
        $file_extension_is_valid = in_array($actual_file_extension, $accepted_file_extensions);

        return $mime_type_is_valid && $file_extension_is_valid;
    }

    // Rename the file.
    function rename_file($resize_file, $rename_to, $extension){
        $renamed_file = basename($resize_file, $extension);

        $renamed_file .= $rename_to . $extension;

        return $renamed_file;
    }

    // Upload file.
    if($file_is_selected){
        $image_filename = $_FILES['upload_image']['name'];
        $temporary_image_path = $_FILES['upload_image']['tmp_name'];
        $new_image_path = file_upload($image_filename);

        if(file_is_an_image($temporary_image_path, $new_image_path)){
            // $extension = "." . pathinfo($new_image_path, PATHINFO_EXTENSION);
            // $new_name_file = rename_file($image_filename, "_resized", $extension);
            // $rename_image_path = file_upload($new_name_file);

            // $image_resize = new \Gumlet\ImageResize($rename_image_path);
            // $image_resize->resizeToWidth(600);
            // $image_resize->save($rename_image_path);

            move_uploaded_file($temporary_image_path, $new_image_path);

            $extension = "." . pathinfo($new_image_path, PATHINFO_EXTENSION);


            $medium_file = rename_file($new_image_path, "_medium", $extension);

            $medium_resize = new \Gumlet\ImageResize($image_filename); 
            $medium_resize->resizeToWidth(600);
            $medium_resize->save(file_upload($medium_file));

        }else{
            $errors[] .= "This is not an accepted image file.";
        }
    }
?>