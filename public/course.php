<?php
  ini_set('display_errors', true);
  error_reporting(E_ALL ^ E_NOTICE);
  session_start();
  // Check if user is already logged in
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("location: login.php");
    exit;
  }

  $id = $_SESSION["id"];
  $email = $_SESSION["email"];
  $name = $_SESSION["name"];

  $courseId = $courseName = $courseDescription = $courseCreatorId = "";
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['courseId']) || empty(trim($_GET['courseId']))) {
      header("location: dashboard.php");
    }
    require_once('../db.php');
    $courseId = $mysqli->real_escape_string(trim($_GET['courseId']));
    // check if course exists and if yes, get details from table course
    $sql = "SELECT `name`, `description`, `creatorId` FROM `courses` WHERE `courseId` = ?";
    if ($stmt = $mysqli->prepare($sql)) {
      $param_courseId = $courseId;
      $stmt->bind_param("s", $param_courseId);
      if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
          // course does not exists
          header("location: dashboard.php?course=404");
          exit;
        }
        $stmt->bind_result($courseName, $courseDescription, $courseCreatorId);
        $stmt->fetch();
      }
    }
  }
  $createdCourse = $id == $courseCreatorId;
?>
<!doctype html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

  <title>Assignment Tracker</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous"/>
  <link rel="stylesheet" href="css/main.css"/>

  <script src="js/nav-dashboard.js"></script>

</head>

<body>
  <?php
    include_once ('nav-dashboard.php');
  ?>

  <div>
  <div class="container">
    <div class="row" id="courseBanner">
      <?php
        if ($createdCourse) {
        ?>
        <div class="col-12">
          <p class="lead">You created this course.</p>
        </div>
        <?php
        }
      ?>
      <div class="col-12">
        <h1>
          <?= $courseName ?>
          <small class="text-muted lead">by Jackie Chan</small>
        </h1>
      </div>
      <div class="col-12"><?= $courseDescription ?></div>
    </div>
  </div>
  </div>
  <?php
    include_once ('footer.php');
  ?>

  <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
