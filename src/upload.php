<!DOCTYPE html>
<html>
<head>
  <title>Uploading...</title>
</head>
<body>
   <h1>Uploading File...</h1>

<?php
require_once('bookmark_fns.php');


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

  // Does the file have the right MIME type?
  if ($_FILES['user_bookmarks_file']['type'] != 'text/html')
  {
    echo 'Problem: file is not a HTML document.';
    exit;
  }

  // put the file where we'd like it
  $uploaded_file = USERDIR.$_FILES['user_bookmarks_file']['name'];

  if (is_uploaded_file($_FILES['user_bookmarks_file']['tmp_name']))
  {
     if (!move_uploaded_file($_FILES['user_bookmarks_file']['tmp_name'], $uploaded_file))
     {
        echo 'Problem: Could not move file to destination directory.';
        exit;
     }
  }
  else
  {
    echo 'Problem: Possible file upload attack. Filename: ';
    echo $_FILES['user_bookmarks_file']['name'];
    exit;
  }

  echo 'File uploaded successfully.';

?>
</body>
</html>
