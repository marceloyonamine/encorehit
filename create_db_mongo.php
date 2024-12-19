<?php

// by Marcelo T Yonamine

try {

// Connect to database mongodb
// include security::
// authorization: enabled
// to /etc/mongod.conf to enable password in mongodb then create a root user in the admin database and username admin
// enable PHP mongodb extension


//readme.txt to create db and collection

$hostmongo = "localhost:27017"; 
$usermongo = "admin";
$usernamemongo = "admin";
$passwordmongo = "mypassword"; //change it

$mongoClient = new MongoDB\Driver\Manager("mongodb://{$hostmongo}/{$usermongo}", array("username" => $usernamemongo, "password" => $passwordmongo));




// Select the database and collection
$namespace = "encorehitdb.encorehitauth"; 

$documentuser = [
    "useremail" => "",      
   "userjsontoken" => [
     "chave1": "valor1",
     "chave2": "valor2"
   ]
 ];
 
 





// create a BulkWrite
$bulk = new MongoDB\Driver\BulkWrite();
$bulk->insert($documentuser);

// Exec a BulkWrite
$writeResult = $mongoClient->executeBulkWrite($namespace, $bulk);

// Verify that the insertion was successful
if ($writeResult->getInsertedCount() === 1) {
    echo "Document inserted successfully!";
} else {
    echo "Failed to insert document.";
}






?>


