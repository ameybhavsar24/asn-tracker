<?php
  ini_set('display_errors', true);
  error_reporting(E_ALL ^ E_NOTICE);
  include ('read.php');
  include ('plagcheck/autoload.php');
  include ('requests/library/Requests.php');

  $file_content = read_file($_POST['fileName']);
  $file_base64 = base64_encode($file_content);

  $submissionId = rand(10, 1000);

  Requests::register_autoloader();

  $CHECK = true;
  if ($CHECK) {
    echo '<h4>Checking for plagiarism ... </h4><pre>';
    $copyleaks = new Copyleaks\Copyleaks();
    $loginResult = $copyleaks->login('ameybhavsar10@gmail.com', 'c43d1105-8f74-42b1-9cfc-7a9e44c5c752');
    // var_dump($loginResult);

    $accessToken = $loginResult->accessToken;
    var_dump($accessToken);

    $headers = array(
      'Content-type' => 'application/json',
      'Authorization' => 'Bearer ' . $accessToken
    );
    // $customId = 
    // echo $customId;
    $data = '{
      "base64": "'.$file_base64.'",
      "filename": "file.txt",
      "properties": {
        "webhooks": {
          "status": "http://asn-tracker.loca.lt/asn/api/completed.php?id=' . $submissionId . '"
        },
        "sandbox": false
      }
    }';
    var_dump($data);
    $response = Requests::put('https://api.copyleaks.com/v3/education/submit/file/' . $submissionId, $headers, $data);
    var_dump($response);
    
  }
?>
