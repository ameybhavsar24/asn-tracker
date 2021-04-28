<?php
  ini_set('display_errors', true);
  error_reporting(E_ALL ^ E_NOTICE);
  echo '<h1>Checking for plagiarism ... </h1><pre>';

  include ('plagcheck/autoload.php');
  include ('requests/library/Requests.php');
  Requests::register_autoloader();

  print_r(get_declared_classes());
  $copyleaks = new Copyleaks\Copyleaks();
  $loginResult = $copyleaks->login('iamamey24@gmail.com', '8771d757-9742-45d7-b069-4ab25b9901f3');
  // var_dump($loginResult);

  $accessToken = $loginResult->accessToken;
  var_dump($accessToken);

  $headers = array(
  	'Content-type' => 'application/json',
  	'Authorization' => 'Bearer ' . $accessToken
  );
  $customId = rand(10, 1000);
  echo $customId;
  $data = '{
    "base64": "SGVsbG8gd29ybGQh",
    "filename": "file.txt",
    "properties": {
      "webhooks": {
        "status": "http://asn-tracker0.loca.lt/asn/api/completed.php"
      },
      "sandbox": true
    }
  }';
  var_dump($data);
  $response = Requests::put('https://api.copyleaks.com/v3/education/submit/file/test1', $headers, $data);
  var_dump($response);

?>
