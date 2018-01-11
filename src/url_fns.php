<?php
require_once('db_fns.php');

//TODO: need to convert to prepared statements

function get_user_urls($username) {
  //extract from the database all the URLs this user has stored

  $conn = db_connect();
  $result = $conn->query("select bm_URL, title, description
                          from bookmark
                          where username = '".$username."'");
  if (!$result) {
    return false;
  }

  //create an array of the URLs
  $url_array = array();
  for ($count = 1; $row = $result->fetch_row(); ++$count) {
    $url_array[$count] = $row;
  }
  return $url_array;
}

//receives a an array of title, description, url
// returns true if data added to db or throws E
function add_bm(array $new_url) {
  // Add new bookmark to the database

  echo "Attempting to add ".htmlspecialchars($new_url['url'])."<br />";
  $valid_user = $_SESSION['valid_user'];

  $conn = db_connect();

  // check not a repeat bookmark
  $result = $conn->query("select * from bookmark
                         where username='$valid_user'
                         and bm_URL='".$new_url['url']."'");
  if ($result && ($result->num_rows>0)) {
    throw new Exception('Bookmark already exists.');
  }

  // insert the new bookmark
  if (!$conn->query("insert into bookmark values
     ('".$valid_user."', 
     '".$new_url['url']."',
     '".$new_url['title']."',
     '".$new_url['description']."')")) {
    throw new Exception('Bookmark could not be inserted.');
  }
  return true;
}

// receives array of link, name, category
// extracted from a uploaded bookmark file
// returns true on success or throws E
function add_bm_file(array $bm_uploaded){
    echo "Attempting to add bookmark file to database<br />";
    $valid_user = $_SESSION['valid_user'];


    $conn = db_connect();

//  see this SO for implode technique https://stackoverflow.com/questions/779986/insert-multiple-rows-via-a-php-array-into-mysql#780046

    $sql = array();
    foreach( $bm_uploaded as $row ) {
        $bmName = mysqli_real_escape_string($row['name']);
        $bmLink = mysqli_real_escape_string($row['link']);
        $bmCategory = mysqli_real_escape_string($row['category']);

        $sql[] = '("'.$bmName.'", '.$bmLink.' , '.$bmCategory.')';
    }
    $conn->query('INSERT INTO bookmark (username, bm_URL, title, description, category) VALUES '.implode(',', $sql));
}

function delete_bm($user, $url) {
  // delete one URL from the database
  $conn = db_connect();

  // delete the bookmark
  if (!$conn->query("delete from bookmark where
                     username='".$user."' 
                    and bm_url='".$url."'")) {
     throw new Exception('Bookmark could not be deleted');
  }
  return true;
}

function recommend_urls($valid_user, $popularity = 1) {
  // We will provide semi intelligent recomendations to people
  // If they have an URL in common with other users, they may like
  // other URLs that these people like
  $conn = db_connect();

  // find other matching users
  // with an url the same as you
  // as a simple way of excluding people's private pages, and
  // increasing the chance of recommending appealing URLs, we
  // specify a minimum popularity level
  // if $popularity = 1, then more than one person must have
  // an URL before we will recomend it

  $query = "select bm_URL
	          from bookmark
	          where username in
	   	        (select distinct(b2.username)
              from bookmark b1, bookmark b2
		          where b1.username='".$valid_user."'
                and b1.username != b2.username
                and b1.bm_URL = b2.bm_URL)
	            and bm_URL not in
 		            (select bm_URL
				        from bookmark
				        where username='".$valid_user."')
            group by bm_url
            having count(bm_url)>".$popularity;

  if (!($result = $conn->query($query))) {
     throw new Exception('Could not find any bookmarks to recommend.');
  }

  if ($result->num_rows==0) {
     throw new Exception('Could not find any bookmarks to recommend.');
  }

  $urls = array();
  // build an array of the relevant urls
  for ($count=0; $row = $result->fetch_object(); $count++) {
     $urls[$count] = $row->bm_URL;
  }

  return $urls;
}
?>
