<?php
  ini_set('display_errors', true);
  error_reporting(E_ALL ^ E_NOTICE);
  session_start();
  // Check if user is already logged in
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("location: login.php");
    exit;
  }
  require_once('../db.php');
  $courses_enrolled = $courses_created = [];
  $id = $_SESSION["id"];
  $email = $_SESSION["email"];
  $name = $_SESSION["name"];

  // get enrolled courses
  $sql = "SELECT enrollments.courseId, courses.name, courses.description, users.name FROM ( (enrollments INNER JOIN courses ON enrollments.courseId = courses.courseId) INNER JOIN users ON users.id = courses.creatorId ) WHERE enrollments.userId = ?";
  if ($stmt = $mysqli->prepare($sql)) {
    $param_userId = $id;
    $stmt->bind_param("s", $param_userId);
    if ($stmt->execute()) {
      $stmt->store_result();
      $result = $stmt->get_result();
      if ($stmt->num_rows > 0) {
        $stmt->bind_result($enrolledCourseId, $enrolledCourseName, $enrolledCourseDescription, $enrolledCourseCreatorName);
        while ($stmt->fetch()) {
          $course = array(
            'id' => $enrolledCourseId,
            'name' => $enrolledCourseName,
            'description' => $enrolledCourseDescription,
            'creator' => $enrolledCourseCreatorName,
          );
          array_push($courses_enrolled, $course);
        }
      }
    } else {
      echo 'Failed to execute sql query';
    }
  }
  // get created courses
  $sql = "SELECT * FROM courses WHERE creatorId = ? ORDER BY courseId DESC";
  if ($stmt = $mysqli->prepare($sql)) {
    $param_creatorId = $id;
    $stmt->bind_param("s", $param_creatorId);
    if ($stmt->execute()) {
      $stmt->store_result();
      $result = $stmt->get_result();
      if ($stmt->num_rows > 0) {
        $stmt->bind_result($courseId, $courseName, $courseDescription, $courseCreatorId);
        while ($stmt->fetch()) {
          $course = array(
            'id' => $courseId,
            'name' => $courseName,
            'description' => $courseDescription,
            'creatorId' => $courseCreatorId,
          );
          array_push($courses_created, $course);
        }
      }

    } else {
      echo 'Failed to execute sql query';
    }
  }
/*  echo '<pre>';
  var_dump($courses_enrolled);
  var_dump($courses_created);
  echo '</pre>';*/
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
    include ('nav-dashboard.php');
  ?>
  <div class="container-fluid">
  <h4><span class="text-muted">Hello, </span> <?= $name ?></h4>
  <h3 class="enrolled-created-courses-headings">Enrolled Courses:</h3>
  <div class="courses-cnt">
    <?php
      if (!empty($courses_enrolled)) {
      foreach($courses_enrolled as $course) {
      ?>
      <div class="courses">
        <a href="course.php?courseId=<?= $course["id"] ?>">
          <div class="courses-head">
            <h4><?= $course["name"] ?></h4>
            <?= $course["creator"] ?>
          </div>
          <div class="courses-desc">
            Assigned Works
          </div>
        </a>
      </div>
      <?php
      }
      } else {
        echo '<p class="small lead">You have not enrolled for any course. <a href="enroll.php">Enroll in a course.</a></p>';
      }
    ?>
  </div>

  <h3 class="enrolled-created-courses-headings">Created Courses:</h3>

  <div class="courses-cnt">
    <?php
      if (!empty($courses_created)) {
      foreach($courses_created as $course) {
        ?>
        <!-- Course card for each of the created course -->
        <div class="courses">
          <a href="course.php?courseId=<?= $course["id"] ?>">
            <div class="courses-head">
              <h4><?= $course["name"] ?></h4>
            </div>
            <div class="courses-desc">
              Assigned Works
            </div>
          </a>
        </div>

        <?php
      }
      }
      else {
        echo '<p class="small lead">You have not created any courses. <a href="create.php">Create a new course.</a></p>';
      }
    ?>

  </div>
  </div>
  <?php
    include ('footer.php');
  ?>

  <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <?php
    // handle php alerts
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      if ($_GET["course"] == "404") {
        echo '<script>alert("Invalid course id.")</script>';
      }
      if ($_GET["enrolled"] == "success") {
        echo '<script>alert("Enrolled in course successfully.")</script>';
      }
    }
  ?>

</body>
</html>
