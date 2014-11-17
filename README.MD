Bitly-PHP-Simpleclass
=========================

This class was designed to shorten links in a minimum number of commands.


# Usage for unique link
```
<?php
require_once('class.bitly.php');
$bitly = new bitly("login", "api_key");
$bitly->link("http://www.google.com/"); 
echo $bitly->link;
?>
```

# Usage for multiple links:
```
<?php
require_once('class.bitly.php');
$bitly = new bitly("login", "api_key");
$bitly->content("Hello World! http://www.google.com/ http://www.yahoo.com/"); 
echo $bitly->content;
?>
```
