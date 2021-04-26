<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
ini_set('display_warnings', 1);

session_start();
echo '<pre>';
var_dump($_SESSION['assignments'][(int)$_GET['id']]);
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["assignmentFile"]["name"]);
var_dump($target_file);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["assignmentFile"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
// if (file_exists($target_file)) {
//   echo "Sorry, file already exists.";
//   $uploadOk = 0;
// }

// Check file size
if ($_FILES["assignmentFile"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
// if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
// && $imageFileType != "gif" ) {
//   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
//   $uploadOk = 0;
// }

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["assignmentFile"]["tmp_name"], $target_file)) {
    // echo "The file ". htmlspecialchars( basename( $_FILES["assignmentFile"]["name"])). " has been uploaded.";
    $sql = "INSERT INTO `submission`(`assignmentId`, `userId`, `file_name`) VALUES (?,?,?)";
    require_once('../db.php');
    if ($stmt = $mysqli->prepare($sql)) {

        $stmt->bind_param("iis", $_SESSION['assignments'][(int)$_GET['id']]['id'], $_SESSION['id'], $target_file);
        
        if ($stmt->execute()) {
            // header('location: course.php');1
            echo 'Inserted submission';            
        } else {
            echo 'Failed to submit.' . $mysqli->error;
        }
    } else {
        echo $mysqli->error;
    }
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
?>