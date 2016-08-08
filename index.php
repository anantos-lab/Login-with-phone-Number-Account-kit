<?php
session_start();
?>
<html>
<head>
  <title>Login with phone Number - Account kit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://sdk.accountkit.com/en_EN/sdk.js"></script>
</head>
<body>


  <?php

     $app_id = '1130565950343621';
     $kit_secret = 'f5bb1169137f12379f744e344707de10';

      function url($url)
      {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl , CURLOPT_URL, $url);
    $ch = curl_exec($curl);
    curl_close($curl);
    return $ch;
  }

  if (isset($_SESSION["access_token"])){
        $appsecret_proof = hash_hmac('sha256', $_SESSION["access_token"] , $kit_secret);
        $url = url('https://graph.accountkit.com/v1.0/me/?appsecret_proof='.$appsecret_proof.'&access_token='.$_SESSION["access_token"]);
        $rual = json_decode($url);
        $user = json_decode( json_encode($rual), true);

        echo '

        <div class="container style="margin: 0 auto; margin-top:200px">
        <ul class="list-group">
        <li class="list-group-item">'.$user['id'].'</li>
        <li class="list-group-item">'.$user['phone']['number'].'</li>
        <li class="list-group-item">'.$user['phone']['country_prefix'].'</li>
        <li class="list-group-item">'.$user['phone']['national_number'].'</li></ul>

        </div>
        ';

     }
  else{
     echo '
     <div class= "text-center" style="margin-top:300px;">
     <h1>Login with Phone or Mail Without Pass</h1>
     <button class="btn btn-success"  onclick="phone_btn_onclick();">Login with SMS</button>
     <button class="btn btn-warning"  onclick="email_btn_onclick();">Login with Email</button>
     </div>
     </div>
     <form action="" method="POST" id="my_form">
     <input type="hidden" name="code" id="code">
     <input type="hidden" name="csrf_nonce" id="csrf_nonce">';

     if(isset($_POST["code"])){
     $_SESSION["code"] = $_POST["code"];
     $_SESSION["csrf_nonce"] = $_POST["csrf_nonce"];
     $token = 'AA|'.$app_id.'|'.$kit_secret;
     $url = url('https://graph.accountkit.com/v1.0/access_token?grant_type=authorization_code&code='.$_POST["code"].'&access_token='.$token);
     $url = json_decode($url);
     $rual = $url-> access_token;
     $_SESSION["access_token"] = $rual;
   }
 }


 if ($_POST["code"]) {
    echo '<script>window.location = "index.php";</script>';
 }
  ?>
</form>
</body>


<script>
  AccountKit_OnInteractive = function(){
    AccountKit.init(
      {
        appId:1130565950343621,
        state:"abcd",
        version:"v1.0"
      }
    );
  };
  // login callback
  function loginCallback(response) {
    console.log(response);
    if (response.status === "PARTIALLY_AUTHENTICATED") {
      document.getElementById("code").value = response.code;
      document.getElementById("csrf_nonce").value = response.state;
      document.getElementById("my_form").submit();
    }
    else if (response.status === "NOT_AUTHENTICATED") {
      // handle authentication failure
      console.log("Authentication failure");
    }
    else if (response.status === "BAD_PARAMS") {
      // handle bad parameters
      console.log("Bad parameters");
    }
  }
  // phone form submission handler
  function phone_btn_onclick() {
    // you can add countryCode and phoneNumber to set values
    AccountKit.login('PHONE', {}, // will use default values if this is not specified
      loginCallback);
  }
  // email form submission handler
  function email_btn_onclick() {
    // you can add emailAddress to set value
    AccountKit.login('EMAIL', {}, loginCallback);
  }
</script>
</html>
