<?php

require_once 'vendor_login/autoload.php';

session_start();

// init configuration Google Login Auth
$clientID = '717706321999-fj5rf0t2lj6lg99f8urk3q623kmtjooo.apps.googleusercontent.com'; // change it
$clientSecret = 'GOCSPY-dhESemWzoMbwkzKKe-XOqUU2zccc'; // change it
$redirectUri = 'http://127.0.0.1/encorehit/bemvindo.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Connect to database postgresql
$host = "host = localhost";
$port = "port = 5432";
$dbname = "dbname = usuarios";
$credentials = "user=postgres password=mypasswordpostg"; // change it

   $db = pg_connect( "$host $port $dbname $credentials"  );

   if(!$db) {
    echo "Error : Unable to open database\n";
 } else {
    echo "\n";
 }



// Connect to database mongodb
// include security:
// authorization: enabled
// in /etc/mongod.conf to enable password in mongodb then create a root user in the admin database and username admin
// enable PHP mongodb extension in php.ini file


 $hostmongo = "localhost:27017"; 
 $usermongo = "admin";
 $usernamemongo = "admin";
 $passwordmongo = "mypassword"; // change it 






 ?>
