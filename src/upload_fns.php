<?php
/**
 * Created by PhpStorm.
 * User: devJacob
 * Date: 12/31/17
 * Time: 11:36 AM
 */


function checkSelectedFile(){

if ($_FILES['user_bookmarks_file']['error'] > 0)
{
    echo 'Problem: ';
    switch ($_FILES['user_bookmarks_file']['error'])
    {
        case 1:
            echo 'File exceeded upload_max_filesize.';
            break;
        case 2:
            echo 'File exceeded max_file_size.';
            break;
        case 3:
            echo 'File only partially uploaded.';
            break;
        case 4:
            echo 'No file uploaded.';
            break;
        case 6:
            echo 'Cannot upload file: No temp directory specified.';
            break;
        case 7:
            echo 'Upload failed: Cannot write to disk.';
            break;
    }
    exit;
}

}


function checkMimeType() {
    // Does the file have the right MIME type?
    if ($_FILES['user_bookmarks_file']['type'] != 'text/html')
    {
        echo 'Problem: file is not a HTML document.';
        exit;
    }
}


function moveUploaded($uploaded_file){
    if (!move_uploaded_file($_FILES['user_bookmarks_file']['tmp_name'], $uploaded_file))
    {
        echo 'Problem: Could not move file to destination directory.';
        exit;
    }

}