<?php
require_once("vendor_push/autoload.php");
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


// Project by https://github.com/Minishlink/web-push-php-example
// Modify by Marcelo T Yonamine

require_once 'configuracao.php'; // config file mongodb database and PostgreSQL database



try {


$mongoClient = new MongoDB\Driver\Manager("mongodb://{$hostmongo}/{$usermongo}", array("username" => $usernamemongo, "password" => $passwordmongo));

// Select the database and collection created in mongosh shell
$namespace = "encorehitdb.encorehit";


$dbName = "encorehitdb"; // mongoDB database name
$collectionName = "encorehit"; // collection where documents will be deleted after sending the notification


 // Current time and date in milliseconds
 $milisegundosatual = intval(round(microtime(true) * 1000));



 // Prepare a consulta
// quando campo data hora referencia for menor que a data hora atual, envia notificacao push notificacao disparada 30 horas antes
// quando campo data hora referencia for maior que a data hora atual, nao envia notificacao push 



$query = new MongoDB\Driver\Query([
    'refdatetime' => [
        '$lt' => $milisegundosatual 
    ]
]);
 

     // Run the query and get the result
$cursor = $mongoClient->executeQuery($namespace, $query);



foreach ($cursor as $document) {


   $email = $document->email;
  
   
    $jstoken = $document->jsontoken;


    $mensagem = $document->message;

   // $datetimeprogramado = milisegundosParaDataHora($document->refdatetime);

   $datetimeprogramado = "";
   
   
    if (isset($jstoken)) {


$subscription = Subscription::create(json_decode("$jstoken", true));





 $auth = array(
    'VAPID' => array(
        'subject' => 'www.encorehit.com',
        'publicKey' => "BKSTVs2g4xGE_Q80mdXd0H_evakOEn7AFwXcQPZA7N1O7Gcztn-qZA_qPgjdTiDUhXZzAQVUtVzWbWXWVv8Wbbb", // don't forget that your public key also lives in app.js VAPID.php
        'privateKey' => "Ml7hJ5RZJ3qSlprFbamL_wzG5Afhh9WASba7mhsbggg", // in the real world, this would be in a secret file run VAPID.php after delete VAPID.php
    ),
);


$jsonString = "{\"message\":\"$mensagem\"}";


$webPush = new WebPush($auth);

$report = $webPush->sendOneNotification(
    $subscription,
    $jsonString,
);

// handle eventual errors here, and remove the subscription from your server if it is expired
$endpoint = $report->getRequest()->getUri()->__toString();

if ($report->isSuccess()) {
    //echo "[v] Message sent successfully for subscription {$endpoint}.";
    // echo "Message sent successfully!. $datetimeprogramado";

    
    $bulkWrite = new MongoDB\Driver\BulkWrite();
    $bulkWrite->delete([
        'refdatetime' => [
            '$lt' => $milisegundosatual 
        ]
        ]);
    
   
    
    $deleteResult = $mongoClient->executeBulkWrite($dbName . "." . $collectionName, $bulkWrite);
    
    //echo $deleteResult->getDeletedCount() . " deleted documents.";    


} else {
    //echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
    echo "Message failed to sent  {$report->getReason()}";
}


  }


     }





    



} catch (MongoDB\Driver\Exception\Exception $e) {
 
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";       
  }




  function milisegundosParaDataHora($milisegundos) {
    $segundos = $milisegundos / 1000; // Convert milliseconds to seconds
    return gmdate("Y-m-d H:i", $segundos); // Formats the date and time in UTC
  }



?>
