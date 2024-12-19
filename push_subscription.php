<?php

// Project by https://github.com/Minishlink/web-push-php-example
// Modify by Marcelo T Yonamine


require_once 'configuracao.php'; //mongodb database and PostgreSQL database



// checking if user is already exists in database

if (isset($_SESSION['user_token'])) {

$user_token = $_SESSION['user_token'];

} else {

    $user_token = "nulo";

}



$sqlcheck =<<<EOF
SELECT * FROM users WHERE token ='$user_token';
EOF;

$retcheck = pg_query($db, $sqlcheck);
if(!$retcheck) {
echo pg_last_error($db);
echo "login error!";
exit;
} 

if (pg_num_rows($retcheck) > 0) {

    // user is exists
$userinfo = pg_fetch_assoc($retcheck);
$useremail = trim($userinfo['email']);

} else {

echo "<br/><br/>login error!";
exit;

}

pg_close($db);




$subscription = json_decode(file_get_contents('php://input'), true);




if (!isset($subscription['endpoint'])) {
    echo 'Error: not a subscription';
    return;
}








$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'POST':
        // create a new subscription entry in your database (endpoint is unique)
        //Firefox endpoint subscription push server diferente de Chrome endpoint subscription 
        
        // Checks if the $subscription variable contains data
if (empty($subscription)) {
  echo "Error: No JSON data found in request body";
  exit;
}





// Convert the $subscription variable to JSON
$json = json_encode($subscription, true);


$headers = getallheaders(); // get array headers app.js

$browserID  = "";
$navegadorID = "";

    
    $chavesParaExtrair1 = ['Authorization1'];
    $stringsExtraidas1 = extrairStringsHeaders($headers, $chavesParaExtrair1);
    
    if (is_null($stringsExtraidas1)) {} else { 

     foreach ($stringsExtraidas1 as $chave => $valor) {
     $autorização1 = "$valor";
     }
     
     $browserID = trim($autorização1);
     $navegadorID = $useremail . " " . $browserID; // necessary to use more than one browser with the same email


    }





try {

$mongoClient = new MongoDB\Driver\Manager("mongodb://{$hostmongo}/{$usermongo}", array("username" => $usernamemongo, "password" => $passwordmongo));



$dbName = "encorehitdb"; // mongoDB database
$collectionName = "encorehitauth"; // collection of old documents will be deleted

$bulkWrite = new MongoDB\Driver\BulkWrite();

$bulkWrite->delete(['navegadorid' => $navegadorID]);



$deleteResult = $mongoClient->executeBulkWrite($dbName . "." . $collectionName, $bulkWrite);

// Select the database and collection created in mongosh
$namespace = "encorehitdb.encorehitauth";



$documentuser = [
    'navegadorid' => "$navegadorID",
    'useremail' => "$useremail",      
   'userjsontoken' => "$json"
 ];




// Create a BulkWrite
$bulk = new MongoDB\Driver\BulkWrite();
$bulk->insert($documentuser);

// Run BulkWrite
$writeResult = $mongoClient->executeBulkWrite($namespace, $bulk);

// Verify that the insertion was successful
if ($writeResult->getInsertedCount() === 1) {
    echo "Document inserted successfully!";
} else {
    echo "Failed to insert document";
}

 
 } catch (MongoDB\Driver\Exception\Exception $e) {
 
   echo "Exception:", $e->getMessage(), "\n";
   echo "In file:", $e->getFile(), "\n";
   echo "On line:", $e->getLine(), "\n";       
 }




        
        break;
    case 'PUT':
        // update the key and token of subscription corresponding to the endpoint
        break;
    case 'DELETE':
        // delete the subscription corresponding to the endpoint
        break;
    default:
        echo "Error: method not handled";
        return;
}







function extrairStringsHeaders($headers, $chaves) {
    $stringsExtraidas = [];
    foreach ($chaves as $chave) {
      if (array_key_exists($chave, $headers)) {
        $stringsExtraidas[$chave] = $headers[$chave];
      } else {
        $stringsExtraidas[$chave] = null;
      }
    }
    return $stringsExtraidas;
  }





?>
