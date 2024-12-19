<?php

//by Marcelo T Yonamine

require_once 'configuracao.php';

if (isset($_SESSION['user_token'])) {
  header("Location: bemvindo.php");
} else {

  ?>




<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
  <title>Encorehit Schedule with notification of concerts by your favorite artists, sporting events or other events. Includes event search engine with Artificial Intelligence.</title>

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
  font-size: 30px;
  color: #4D4D4D;
}




#rcorners2 {
  margin-top: 100px;
  background: #FFD600;
  border-radius: 25px;
  border: 2px solid #888888;
  padding: 20px; 
  width: 700px;
  height: 550px;  
  box-shadow: 3px 8px #888888;
}


</style>
</head>
<body>

<div>

<center><p id="rcorners2"><img src="logos/encorehit_logo.png" align="left"><img src="logos/bell_notification.png" align="right">

<br/><font style="color:black">ENCOREHIT</font><br/><br/><br/><font style="color:black">R</font>eminding you about music concerts or sporting events schedule an alert notification for the day, time, month and year.
  With event search system with Artificial Intelligence.
  <br/> <font style="color:green">Continue with Google</font> <br/><br/>


    <?php

echo "<a href='" . $client->createAuthUrl() . "'><img src=\"logos/web_dark_rd_na@4x.png\" alt=\"Continue with Google\"></a>";

   ?>
  

</center>

</div>


</body>
</html>




<?php

}

?>
