<?php

//by Marcelo T Yonamine


require_once 'configuracao.php'; // mongodb database and PostgreSQL database configuration


if(isset($_SESSION['user_token'])) {

  $user_token = $_SESSION['user_token'];

} else {

  $user_token = "nulo";
  

}


$headers = getallheaders(); //get array headers app.js

$msgnotification  = "";
$datetimenotification = "";
$navegadorID = "";
    
    $chavesParaExtrair1 = ['Authorization1'];
    $stringsExtraidas1 = extrairStringsHeaders($headers, $chavesParaExtrair1);
    
    if (is_null($stringsExtraidas1) || empty($stringsExtraidas1)) {} else { 

     foreach ($stringsExtraidas1 as $chave => $valor) {
     $autorização1 = "$valor";
     }
     
     $msgnotification = trim($autorização1);


    }


    $chavesParaExtrair2 = ['Authorization2'];
    $stringsExtraidas2 = extrairStringsHeaders($headers, $chavesParaExtrair2);
    
    if (is_null($stringsExtraidas2) || empty($stringsExtraidas2)) {} else {

     foreach ($stringsExtraidas2 as $chave => $valor) {
     $autorização2 = "$valor";
     }
    
     
     $datetimenotification = trim($autorização2);

    }


     if (empty($msgnotification)) {
      echo "error message field empty";

      $msgnotification = "";
      
     }


     if (empty($datetimenotification)) {
      echo "error date time field empty";

       $datetimenotification = "";
      
     }


     if (strlen($msgnotification) > 120) {

      echo "error, limit char!</b>";

      $msgnotification = "";
      
    
     }


     $msgnotification = htmlspecialchars($msgnotification);

     if (isDataHoraValida($datetimenotification)) {} else {
 
      echo "Invalid date and time error";

      $datetimenotification = "";
      
      
      }



      // Convert  string date and time in object DateTime
      $dateTimeprogramado = strtotime($datetimenotification);

      // Convert DateTime object to milliseconds
      $milisegundosprogramado = intval($dateTimeprogramado * 1000);


      // Current time and date in milliseconds
      $milisegundosatual = intval(round(microtime(true) * 1000));


      $chavesParaExtrair3 = ['Authorization3'];
      $stringsExtraidas3 = extrairStringsHeaders($headers, $chavesParaExtrair3);
      
      if (is_null($stringsExtraidas3) || empty($stringsExtraidas3)) {} else {
  
       foreach ($stringsExtraidas3 as $chave => $valor) {
       $autorização3 = "$valor";
       }
      
       
       $navegadorID = trim($autorização3);
  
      }


      if (strlen($navegadorID) > 800) {

        echo "error, limit char!</b>";

        $navegadorID = "";

      }  




// here I'll get the subscription endpoint in the POST parameters
// but in reality, you'll get this information in your database
// because you already stored it (cf. push_subscription.php)
// $subscription = Subscription::create(json_decode(file_get_contents('php://input'), true));


   
    if ($msgnotification == "" && $datetimenotification == "" && $navegadorID == "") {


      echo "error all fields are empty";




    } else {  
      
      // Create document in mongodb for notification
     


        // checking if user is already exists in database





$sqlcheck =<<<EOF
SELECT * FROM users WHERE token ='$user_token';
EOF;

$retcheck = pg_query($db, $sqlcheck);
if(!$retcheck) {
echo pg_last_error($db);
echo "Login ERROR! session time expired. Did not create reminder";
exit;
} 

if (pg_num_rows($retcheck) > 0) {

    // user is exists
$userinfo = pg_fetch_assoc($retcheck);
$useremail = trim($userinfo['email']);

} else {

echo "Login ERROR! session time expired. Did not create reminder";
exit;

}

pg_close($db);





     try {

     $mongoClient = new MongoDB\Driver\Manager("mongodb://{$hostmongo}/{$usermongo}", array("username" => $usernamemongo, "password" => $passwordmongo));    

        // Select the database and collection created in the mongosh shell
        $namespaceauth = "encorehitdb.encorehitauth";

        $browserID = $useremail . " " . $navegadorID; // necessary to use more than one browser with the same email

       // Prepare the consultation
  $filter = ['navegadorid' => "$browserID"];
  
  $query = new MongoDB\Driver\Query($filter);

      // Run the query and get the result
      $cursor = $mongoClient->executeQuery($namespaceauth, $query);

// Check for results

foreach ($cursor as $document) {

  $emailauth = $document->useremail;
  
   $jstoken = $document->userjsontoken;
 

}



     // Select the database and collection created in the mongosh shell
     $namespace = "encorehitdb.encorehit";

     $remote_addr = $_SERVER['REMOTE_ADDR'];
     $http_user_agent = $_SERVER['HTTP_USER_AGENT'];


     $document = [
      'email' => "$emailauth",  
      'message' => "$msgnotification",
     'ipaddr' => "$remote_addr",
     'browser' => "$http_user_agent",
     'refdatetime' => $milisegundosprogramado, // future time and date to be scheduled for sending notification in milliseconds, without quotes
     'atualdatetime' => $milisegundosatual, // Current date and time of recording in the db in milliseconds, without quotes
     'jsontoken' => "$jstoken"
   ];




// Create a BulkWrite
$bulk = new MongoDB\Driver\BulkWrite();
$bulk->insert($document);

// Execute a BulkWrite
$writeResult = $mongoClient->executeBulkWrite($namespace, $bulk);

// Verify that the insertion was successful
if ($writeResult->getInsertedCount() === 1) {
    echo "Reminder was created successfully!";
} else {


    echo "Error failed to create reminder!";

    $dbName = "encorehitdb"; // mongodb database
    $collectionName = "encorehitauth"; // collection of old documents will be deleted
    
    $bulkWrite = new MongoDB\Driver\BulkWrite();
    
    $bulkWrite->delete(['navegadorid' => $browserID]);
    
    
    
    $deleteResult = $mongoClient->executeBulkWrite($dbName . "." . $collectionName, $bulkWrite);

}




    } catch (MongoDB\Driver\Exception\Exception $e) {
 
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";       
      }
     
     
       




 
 
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





  function isDataHoraValida($dataHora) {
    try {
        new DateTime($dataHora);
        return true;
    } catch (Exception $e) {
        return false;
    }
    }




 ?> 
