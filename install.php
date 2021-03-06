<?php
if ( !isset($_POST['dbName']) ) {
?>
<h3>Instructions</h3>
Create a database and database user.
<br />
<br />
Upload all files.
<br />
<br />
Rename include/configure-stock.php to include/configure.php and set its file permissions to allow writing to it.
<br />
<br />
Use the form below to enter your database settings, username and password, URL preference, and installation directory
(If installing in a non-top level directory, e.g.
mysite.com/myfolder/ as opposed to mysite.com, enter the directory path ).
Or manually edit include/configure.php with database info, url and menu preferences, and path information, run ("Query") include/sql.sql
and then delete that file,
and manually create a user in the
"User" database table and a the home page in the "Content" table using "index" as the seoPageName.
<br />
<br />
Set include/configure.php to 444 (read permission only).
<br />
<br />
Upload tinyMCE (download from tinymce.com) to the include directory.
<br />
<br />
Set uploads directory to be writeable by the web server user to allow file uploads.
<br />
<br />
If you're not in the US Central Timezone, change the timezone in configure.php on the line that has "date_default_timezone_set("
<br />
<br />
Add your page HTML to header.php and footer.php - Voila! You're done.
<br />
<br />
<br />
<form action="install.php" method="post">
  Use Search-Engine Friendly URLs: <input type="radio" name="seoURLs" value="1" checked /> Yes &nbsp;
                                   <input type="radio" name="seoURLs" value="0" /> No
  <br />
  <br />
  Use Top Menu: <input type="radio" name="menuPref" value="0" checked /> Yes &nbsp;
  <br />
  Use Left Menu: <input type="radio" name="menuPref" value="1" /> No
  <br />
  <br />
  Database Server: <input type="text" name="dbServer" value="localhost" />
  <br />
  Database Name: <input type="text" name="dbName" />
  <br />
  Database Username: <input type="text" name="dbUser" />
  <br />
  Database Password: <input type="password" name="dbPass" />
  <br />
  <br />
  Admin Username: <input type="text" name="adminUser" />
  <br />
  Admin Password: <input type="password" name="adminPw" />
  <br />
  <br />
  Installation Directory (no slash at beginning or end): <input type="text" name="installDir" />
  <br />
  <br />
  <input type="submit" value="Install" />
</form>
<?php
}
else {
 // process form


 // connect to DB
 $db_server = $_POST['dbServer'];
 $db_user = $_POST['dbUser'];
 $db_pass = $_POST['dbPass'];
 $db_name = $_POST['dbName'];

 try {
    $dbh = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // throw exceptions on PDO errors
  }
  catch (PDOException $e) {
    print " Error!: Unable to connect to database ";

    // DO NOT ENABLE BELOW 2 LINES
    /*
    print $e->getMessage();
    print $e->getTraceAsString();
     */
    die();
  }

 // run installation sql (create tables)
 $qs =
 "CREATE TABLE `Content` (
  `id` int(11) NOT NULL auto_increment,
  `titleTag` varchar(255) NOT NULL default '',
  `metaDescription` varchar(255) NOT NULL default '',
  `metaKeywords` varchar(255) NOT NULL default '',
  `stylesheet` varchar(80) NOT NULL default '',
  `content` longtext NOT NULL,
  `seoPageName` varchar(255) NOT NULL default '',
  `pageName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
 mcmQuery($qs);

 $qs =
 "CREATE TABLE `User` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(80) NOT NULL default '',
  `password` varchar(160) NOT NULL default '',
  `admin` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
 mcmQuery($qs);

 $qs =
 "CREATE TABLE `Menu` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(80) NOT NULL default '',
  `orderNum` int(11) NOT NULL default '0',
  `href` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
 mcmQuery($qs);

 $qs =
 "CREATE TABLE `Menu_Item` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(80) NOT NULL default '',
  `menuID` int(11) NOT NULL default '0',
  `pageID` int(11) NOT NULL default '0',
  `orderNum` int(11) NOT NULL default '0',
  `isHeader` int(11) NOT NULL default '0',
  `href` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
 mcmQuery($qs);

 $qs =
 "CREATE TABLE `Permission` (
  `id` int(11) NOT NULL auto_increment,
  `contentID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
 mcmQuery($qs);

 $qs =
 "CREATE TABLE `Revision_Log` (
  `id` int(11) NOT NULL auto_increment,
  `contentID` int(11) NOT NULL default '0',
  `revisionDateTime` datetime NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `contentID` (`contentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
 mcmQuery($qs);

 $qs =
 "CREATE TABLE `Config` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";
 mcmQuery($qs);

 // delete include/sql.sql
 unlink($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['installDir'].'/include/sql.sql');

 function randomstring($len) {
    while($i<$len)
     {
        $str.=chr((mt_rand()%26)+97);
        $i++;
    }

    $str=$str.substr(uniqid (""),0,22);
    return $str;
 }

 $saltVal = randomstring(40); // generate randomly

 // insert admin user into DB
 $qs = "INSERT INTO `User`
        VALUES (1, :username, :password, 1)";
 $params = array(
   'username'=>$_POST['adminUser'],
   'password'=>sha1($saltVal.$_POST['adminPw'])
 );
 mcmQuery($qs, $params);

 // insert home page into DB
 $qs = "INSERT INTO `Content` (`content`, `seoPageName`, `pageName`)
        VALUES ('', 'index', 'Home Page')";
 mcmQuery($qs);

 // insert config
 $configArr = array();
 $configArr[] = array('key'=>'USE_SEO_URLS',
                      'value'=>$_POST['seoURLs']);
 $configArr[] = array('key'=>'USE_LEFT_MENU',
                      'value='=>$_POST['menuPref']);
 $configArr[] = array('key'=>'MAX_NUM_REVISIONS',
                      'value'=>'5');
 $qs = "INSERT INTO `Config` (`key`, `value`)
        VALUES (:key, :value)";
 foreach ($configArr as $c)
   mcmQuery($qs, $c);

 // write to config file
 $str =
 '<?php

  // file/location config - edit these as needed
  $WS_DIR = "'.$_POST['installDir'].'/"; // web directory (e.g. if you install in "new" under your web root, enter "new/")
  $BASE_DIR = $_SERVER[\'DOCUMENT_ROOT\'].\'/\'.$WS_DIR; // where mcmsimplecms is installed (physical path no trailing slash)
  // end file/location config editable area


  // DB Config/Connect - edit these with your database settings
  $db_server = "'.$db_server.'";
  $db_user = "'.$db_user.'";
  $db_pass = "'.$db_pass.'";
  $db_name = "'.$db_name.'";
  // end db config editable area

  $saltVal = "'.$saltVal.'";

 ?>';

 $config_handle = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['installDir'].'/include/configure.php', 'w') or die("Can't open config file: ".$_SERVER['DOCUMENT_ROOT'].'/'.$_POST['installDir'].'/include/configure.php');
 fwrite($config_handle, $str);
?>
  <h3>Installed Successfully - Be sure to delete install.php and include/sql.sql</h3>

  <h3>Set include/configure.php to 444 (read permission only)</h3>

  <h3>Set uploads directory to be writeable to allow file uploads.</h3>

  Go to <a href="login.php">login.php</a> to login and use mcmsimplecms.
<?php
}
?>