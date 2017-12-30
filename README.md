This is project to  refresh my php skills- no framework. It is based on code from Php and MySQL Web Development. 
It is a simple web bookmark app.

**Features**
- the original valid_url function for adding a bookmark always returned true is any response was received by fopen(),  new functions using curl
are used based on this [stackoverflow post](https://stackoverflow.com/questions/2280394/how-can-i-check-if-a-url-exists-via-php)

**TODO**
- move mysql login code outside of domain
- need to convert sql to prepared statements
- better input validation
- bookmark tags (a la firefox bookmarks)
- browser bookmark import
- export to browser bookmark