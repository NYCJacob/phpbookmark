<?php
    // const for save file location on server
    define('USERDIR', '/home/vagrant/www/user.temp/');
    session_start();

  // put the file where we'd like it
  $uploaded_file = USERDIR.$_FILES['user_bookmarks_file']['name'];

  if (is_uploaded_file($_FILES['user_bookmarks_file']['tmp_name']))
  {
     move_uploaded_file($_FILES['user_bookmarks_file']['tmp_name'], $uploaded_file);
  }
    header('Location: member.php?'. htmlspecialchars(SID));
    exit;
?>
