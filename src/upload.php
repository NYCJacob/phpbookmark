<?php
// ob-start see https://stackoverflow.com/questions/768431/how-to-make-a-redirect-in-php?rq=1
ob_start();
    require_once('bookmark_fns.php');
    // const for save file location on server
    define('USERDIR2', '/home/vagrant/www/user.temp/');
    session_start();

  // put the file where we'd like it
  $uploaded_file = USERDIR2.$_FILES['user_bookmarks_file']['name'];

  if (is_uploaded_file($_FILES['user_bookmarks_file']['tmp_name']))
  {
     move_uploaded_file($_FILES['user_bookmarks_file']['tmp_name'], $uploaded_file);
     $bmArray = bm_importer($uploaded_file);
     // save extracted array to db
      add_bm_file($bmArray);
  }
    header('Location: member.php?'. htmlspecialchars(SID));
    ob_end_flush();
?>
