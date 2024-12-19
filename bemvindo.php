<?php

//By Marcelo T Yonamine

require_once 'configuracao.php'; //general configuration of Google login auth and postgreSQL database and Mongodb database

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);

  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $userinfo = [
    'email' => $google_account_info['email'],
    'first_name' => $google_account_info['givenName'],
    'last_name' => $google_account_info['familyName'],
    'gender' => $google_account_info['gender'],
    'full_name' => $google_account_info['name'],
    'picture' => $google_account_info['picture'],
    'verifiedEmail' => $google_account_info['verifiedEmail'],
    'token' => $google_account_info['id'],
  ];

  // checking if user is already exists in database
 
  $email = $userinfo['email'];


  $sql =<<<EOF
  SELECT * FROM users WHERE email = '$email';
EOF;

$ret = pg_query($db, $sql);
if(!$ret) {
  echo pg_last_error($db);
  exit;
} 


    if (pg_num_rows($ret) > 0) {

        $userinfo = pg_fetch_assoc($ret);
        $token = $userinfo['token'];

    } else {

    
    

      // Gets the current date and time in YYYY-MM-DD HH:MM:SS format
         $dataAtual = date('Y-m-d H:i:s');

         $email = $userinfo['email'];
         $first_name = $userinfo['first_name'];
         $last_name = $userinfo['last_name'];
         $gender = $userinfo['gender'];
         $full_name = $userinfo['full_name'];
         $picture = $userinfo['picture'];
         $verifiedEmail = $userinfo['verifiedEmail'];
         $token = $userinfo['token'];

        $sqlins =<<<EOF
        INSERT INTO users (ID,email,first_name,last_name,gender,full_name,picture,verifiedEmail,token,numanota,numgeminiai,numnotif,numanotaref,numgeminiairef,numnotifref,transactionid,created)
        VALUES (DEFAULT,'$email','$first_name','$last_name','$gender','$full_name','$picture','$verifiedEmail','$token',0,0,0,25,25,25,'nulo','$dataAtual');        
      EOF;  


      $retins = pg_query($db, $sqlins);
      if(!$retins) {
         echo pg_last_error($db);
         die();
      } else {
        $token = $userinfo['token'];
         echo "*\n";
      }
      


    }



  // save user data into session
  $_SESSION['user_token'] = $token;
} else {
  if (!isset($_SESSION['user_token'])) {
    header("Location: index.php");
    die();
  }



   // checking if user is already exists in database

   $user_token = $_SESSION['user_token'];


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

} else {

  echo "<br/><br/>login error!";
  exit;
  
  }


pg_close($db);


}


?>



<!DOCTYPE html>
<html>

<script>


function mysendPost() {

   effectload(1);

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "busca.php");
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.onload = function() {
    effectload(0);
    document.getElementById("airesult").innerHTML = this.responseText;
  }
  
    let myoptions = document.getElementById("ip0").value;
    let myevent = document.getElementById("ip2").value;
    let myyear = document.getElementById("ip1").value;
    console.log(myoptions)
    console.log(myevent)
    console.log(myyear)  
    
    xhttp.onreadystatechange = () => {
  
  if (xhttp.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
    alert(xhttp.responseText);
     }
  };
    
     let myoptionsr =  myoptions.replace("&", "and");
     let myeventr =  myevent.replace("&", "and");
     let myyearr =  myyear.replace("&", "and");
  
     xhttp.send("postmyoptions=" + myoptionsr + "&postmyevent=" + myeventr + "&postmyyear=" + myyearr);
  
  
}







function writestorageData() {

let mymessage = document.getElementById("ip3").value;

 if (mymessage == "") {} else {

localStorage.setItem("storagemessage", mymessage);

}



let mydatetime = document.getElementById("ip4").value;

 if (mydatetime == "") {

  document.getElementById('resultdatetime').innerHTML = "ERROR! Date Time field";

 } else {

localStorage.setItem("storagedatetime", mydatetime);

document.getElementById('resultdatetime').innerHTML = mydatetime;


}



}



  

</script>


<head>




<script>

 


   function effectload(nu) {
   
   
	if (nu == "1") {
    document.getElementById('effectload').innerHTML = "<center><b>Processing...</b><img src='logos/gear.png' id='loader' style='position:relative; top:15px;'/></center>";
     
	setTimeout('effectload(0)', 60000); 
	}
	if (nu == "0") {
	document.getElementById('effectload').innerHTML = "";
	}
	
	
    
       }
       
       
       



</script>



<style> 


body {
  background-color: #E5E5E5;
}


@font-face {
  font-family: lobster;
  src: url("fonts/Lobster-Regular.ttf");
}

div {
  font-family: lobster;
  text-align:center;
  font-size: 30px;
  color: #322323;
}




#rcorners2 {
  margin: auto;
  background: #FFD600;
  border-radius: 25px;
  border: 2px solid #888888;
  padding: 20px; 
  width: 700px;
  height: 350px;  
  box-shadow: 3px 8px #888888;
}



#ip2 {
    border-radius: 25px;
    border: 2px solid #1A1A1A;
    padding: 18px; 
    width: 200px;
    height: 15px;    
}


#ip1 {
     border-radius: 25px;
    border: 2px solid #1A1A1A;
    padding: 18px; 
    width: 70px;
    height: 15px;   
}



#ip3 {
    border-radius: 25px;
    border: 2px solid #1A1A1A;
    padding: 18px; 
    width: 200px;
    height: 15px;    
}



#ip4 {
     border-radius: 25px;
    border: 2px solid #1A1A1A;
    padding: 18px; 
    width: 200px;
    height: 15px;   
}





.button {
  display: inline-block;
  padding: 12px 22px;
  font-size: 18px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  outline: none;
  color: #fff;
  background-color: #04AA6D;
  border: none;
  border-radius: 20px;
  box-shadow: 0 2px #999;
}


.button:hover {background-color: #3e8e41}

.button:active {
  background-color: #3e8e41;
  box-shadow: 0 2px #BFBFBF;
  transform: translateY(4px);
}

.button5 {border-radius: 50%;padding: 16px;}


#loader{
  width: 70px;
  animation: rotation 3s infinite linear;
}

@keyframes rotation {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}



.image {
  opacity: 1;
  display: block;
  height: auto;
  transition: .5s ease;
  backface-visibility: hidden;
}


.container:hover .image {
  opacity: 0.3;
}




.modal {
  display: none; 
  position: fixed; 
  z-index: 1; 
  padding-top: 100px; 
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%; 
  overflow: auto; /
  background-color: rgb(0,0,0); 
  background-color: rgba(0,0,0,0.4); 
}


.modal-content {
  background-color: #fefefe;
  border-radius: 25px;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 40%;
}


.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}



</style>


<meta charset="utf-8">
  



  <title>Welcome <?= $userinfo['full_name'] ?></title>

   

</head>

<body>




<div id="myModal" class="modal">


  <div class="modal-content">
    <span class="close">&times;</span>
    <p>Create a Reminder 🔔</p><br/>
     
   <label for="fmessage">message:</label>
  <input type="text" id="ip3" maxlength="120" name="message" required><input type="datetime-local" id="ip4" name="notification" required>
   <button id="send-push-button" class="button button5" onclick="writestorageData()">GO!</button><br/>
   <font id="resultdatetime" style="font-size:18px"></font>
   <br/>      
   <button id="push-subscription-button" class="button">notifications !</button>
  </br>
  </br>
  </br>
    
  </div>

</div>

<br/><br/>

<div>

<p id="rcorners2">
  
<img src="logos/encorehit_logo.png" align="left"><span class="container"><a href="logout.php"><img src="logos/ic_close_black_36dp.png" class="image" alt="logout" align="right"></a></span>

<br/><font style="color:black">📢 ENCOREHIT</font><br/><br/><br/>


<select style="width:200px; height:55px; border-radius: 25px; border: 2px solid #1A1A1A; padding: 15px; background-color: #E5E5E5;" id="ip0" name="myoptions"/>
  <option value="artist_name">artist name:</option>
  <option value="sports_event">sports event</option>
</select><input type="text" id="ip2" maxlength="50" name="myevent"/><input type="number" id="ip1" value="<?= date('Y') ?>" name="myyear"/><button class="button button5" onclick="mysendPost()">GO!</button>
<br/>
<br/>
<br/>
<button id="myBtn" class="button">Create a Reminder 🔔</button>

</p>

</div>


<div id="effectload"></div>

<center><font id="airesult" style="font-size:18px"></font></center>


<script type="text/javascript" src="app.js"></script>



<script>

var modal = document.getElementById("myModal");


var btn = document.getElementById("myBtn");


var span = document.getElementsByClassName("close")[0];


btn.onclick = function() {
  modal.style.display = "block";
}


span.onclick = function() {
  modal.style.display = "none";
}


window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>




</body>
</html>
