<?php
    require('\xampp\htdocs\wd2\assignments\CMS_Project - Madison Sobering\php-image-resize-master\lib\ImageResize.php');
    require('\xampp\htdocs\wd2\assignments\CMS_Project - Madison Sobering\php-image-resize-master\lib\ImageResizeException.php');

    $image_errors = [];

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

    // Removes a file from the images folder.
    function delete_file($file_name){
        $delete_image_path = file_upload($file_name);

        unlink($delete_image_path);
    }

    // Upload file.
    if($file_is_selected){
        $image_filename = $_FILES['upload_image']['name'];
        $temporary_image_path = $_FILES['upload_image']['tmp_name'];
        $new_image_path = file_upload($image_filename);

        if(file_is_an_image($temporary_image_path, $new_image_path)){
            $extension = "." . pathinfo($new_image_path, PATHINFO_EXTENSION);

            move_uploaded_file($temporary_image_path, $new_image_path);

            $rename_image_file = rename_file($new_image_path, "_resized", $extension);

            $rename_image_resize = new \Gumlet\ImageResize($new_image_path); 
            $rename_image_resize->resizeToWidth(600);
            $rename_image_resize->save(file_upload($rename_image_file));

            // Remove the original size image after transfer.
            unlink($new_image_path);
        }else{
            $image_errors[] .= "This is not an accepted image file.";
        }
    }
?>