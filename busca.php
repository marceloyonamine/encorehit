<?php

//by Marcelo T Yonamine

require_once 'configuracao.php';


// checking if user is already exists in database

if(isset($_SESSION['user_token'])) {

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
echo "<br/><br/>login ERROR! Session time expired";
exit;
} 

if (pg_num_rows($retcheck) > 0) {

    // user is exists
$userinfo = pg_fetch_assoc($retcheck);

} else {

echo "<br/><br/>login ERROR! Session time expired";
exit;

}

pg_close($db);


$ptmyoptions = $_POST['postmyoptions'];
$pmyevent = $_POST['postmyevent'];
$pmyyear = $_POST['postmyyear'];

$ptmyoptions = htmlspecialchars($ptmyoptions);
$pmyevent = htmlspecialchars($pmyevent);
$pmyyear = htmlspecialchars($pmyyear);

if (empty($ptmyoptions)) {

 echo "<br/><b>error, not found!</b>";
 exit;

}


if (empty($pmyevent)) {

  echo "<br/><b>error, not found!</b>";
  exit;
 
 }



 if (empty($pmyyear)) {

  echo "<br/><b>error, not found!</b>";
  exit;
 
 }


 if (strlen($ptmyoptions) > 30) {

  echo "<br/><b>error, limit char!</b>";
  exit;

 }



 if (strlen($pmyevent) > 50) {

  echo "<br/><b>error, limit char!</b>";
  exit;

 }


 if (strlen($pmyyear) > 10) {

  echo "<br/><b>error, limit char!</b>";
  exit;

 }



// value of API Google Search e Gemini

  // Replace with your API key Google Gemini https://ai.google.dev/gemini-api/docs/api-key?hl=pt-br
  $apiKeygemini = 'AIzaSyBC2xbCrJ8J-c3z6UdYKnssU-8vsHvI700'; // change it

  // Define the API URL Gemini
  $apiUrlgemini = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=$apiKeygemini";


 // https://console.cloud.google.com 
$apiKey = 'AIzaSyBh2bB5eVJXQ7DugINBJh6yxPXo9c29Doo'; // change it
$cseId = 'a55d2709f45445555'; // change it https://programmablesearchengine.google.com/controlpanel/create/congrats?cx=a55d2709f45444555


if ($ptmyoptions == "artist_name") {
$termoBusca = "date shows concert $pmyevent $pmyyear";
}


if ($ptmyoptions == "sports_event") {
  $termoBusca = "show date of the sporting event $pmyevent $pmyyear";
  }


// Set the "lr" parameter to lang_rome
$lr = "lang_rome";

// Construa a URL da API
$apiUrl = 'https://www.googleapis.com/customsearch/v1?';
$apiUrl .= 'key=' . $apiKey;
$apiUrl .= '&cx=' . $cseId;
$apiUrl .= '&q=' . urlencode($termoBusca);
$apiUrl .= '&lr=' . $lr;

// Init cURL
$curl = curl_init();

// Configure cURL request
curl_setopt($curl, CURLOPT_URL, $apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the request and get the response
$response = curl_exec($curl);

// Check if there was any error
if ($response === false) {
  echo 'Erro cURL: ' . curl_error($curl);
  curl_close($curl);
  exit;
}

// close cURL
curl_close($curl);

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the request was successful
if (isset($responseData['error'])) {
  echo 'Erro na API: ' . $responseData['error']['message'];
  exit;
}

// Display search results



$textoJunto = "";

foreach ($responseData['items'] as $item) {
  
  $textoJunto .= $item['link'] . " " . $item['snippet'];
  

  }

  //echo $textoJunto;

  //End Google Search API


  sleep(2); // 2 second delay


  //Init Gemini API

  if (isset($textoJunto)) {


    if ($ptmyoptions == "artist_name") {

     $geminiprompt = "show only the dates and location and URL table format remove all special character in the following text: $textoJunto";

    }


    if ($ptmyoptions == "sports_event") {

      $geminiprompt = "show only the dates and location and URL table format remove all special character in the following text: $textoJunto";
 
     }


// Prepare the request body
$data = array(
    'contents' => array(
        array(
            'parts' => array(
                array(
                   'text' => "$geminiprompt"
                )
            )
        )
    )
);

// Convert the request body to JSON
$jsonData = json_encode($data);

// Create a cURL object
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $apiUrlgemini);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json"
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the request and get the response
$response = curl_exec($curl);

// Check if the request was successful
if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 400) {

   echo "INVALID API KEY";

} else {

  //  echo "$response";

    
    // Decode JSON string
$dataGemini = json_decode($response);

// Extract the text variable
$text = $dataGemini->candidates[0]->content->parts[0]->text;

if (isset($text)) {

// Return the variable
//echo $text;

$escaped_comment = escape_output($text);


// Convert URLs to links
$text_with_links = convert_urls_to_links($escaped_comment);

$textotagbr = nl2br($text_with_links);

echo "<h1>$pmyevent</h1>\n";
echo "$textotagbr";


} else {

echo "Failed to find generated content in response.";   

}






}


// Close the cURL object
curl_close($curl);


  } else {

   echo "data error";

  }




//Here's PHP code that incorporates best practices to prevent Cross-Site Scripting (XSS) attacks:
function escape_output($datas) {
    if (is_string($datas)) {
      // Escape special characters for HTML output
      return htmlspecialchars($datas, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    } else {
      // Handle non-string data (avoid escaping unintended characters)
      return $datas;
    }
  }





  function convert_urls_to_links($texto) {
    // Define a pattern to match URLs
    $pattern = '/(https?|ftp):\/\/[^\s]+/i';
  
    // Replace URLs with HTML links using preg_replace
    $texto = preg_replace($pattern, '<a href="$0" target="_blank">$0</a>', $texto);
  
    return $texto;
  }
  
