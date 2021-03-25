  <?php
  session_start();
  ini_set('display_errors', true);
  error_reporting(E_ALL ^ E_NOTICE);
  // Check if user is already logged in
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("location: login.php");
    exit;
  }
  require_once('../db.php');
  $courses_created = [];
  $id = $_SESSION["id"];
  $email = $_SESSION["email"];
  $name = $_SESSION["name"];

  $courseCode = "";
  $courseCodeErr = "";
  $check_errors = true;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["courseCode"]))) {
      $courseCodeErr = "Please enter a course code.";
    } else {
      // check if courseCode exists
      $courseCode = $mysqli->real_escape_string($_POST["courseCode"]);
      $sql = "SELECT courses.courseId FROM courses WHERE courses.courseId = ? LIMIT 1";
      if ($stmt = $mysqli->prepare($sql)) {
        $param_courseCode = $courseCode;
        $stmt->bind_param("s", $param_courseCode);
        if ($stmt->execute()) {
          $stmt->store_result();
          if ($stmt->num_rows == 0) {
            $courseCodeErr = "Invalid course code <b>".$courseCode."</b>. Course does not exist.";
          }
        } else {
          echo 'Failed to check if course exists';
        }
      } else {
        echo 'Failed to prepare sql query';
      }
    }
    $check_errors = empty($courseCodeErr);
    if ($check_errors) {
      // Proceed to enroll user in the course if not already enrolled
      $sql = "INSERT INTO enrollments (courseId, userId) VALUES (?, ?)";
      if ($stmt = $mysqli->prepare($sql)) {
       $param_courseCode = $courseCode;
       $param_userId = $id;
       $stmt->bind_param("ss", $param_courseCode, $param_userId);

       // Attempt to insert in enrollments
       if ($stmt->execute()) {
         header('Location: dashboard.php?enrolled=success');
       } else {
         echo 'Failed to execute SQL statement';
       }
      }
    }
  }
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

  <div class="container">
    <div class="row">
      <div class="col-12 col-sm-9 col-md-7 col-lg-5 mx-auto my-5">
        <div class="card card-form my-5">
          <div class="card-body">
            <h5 class="card-title text-center">Enroll Course</h5>
              <form class="form-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php
                  if (!$check_errors) {
                    ?>
                    <div class="alert alert-danger" role="alert">
                      <!-- Form errors here -->
                      <?php
                        if (!empty($courseCodeErr)) echo $courseCodeErr.'<br/ >';
                      ?>
                    </div>
                    <?php
                  }
                ?>
                <div class="form-label-group">
                  <input name="courseCode" type="text" id="inputCode" class="form-control" placeholder="Course code" required autofocus>
                  <label for="inputCode">Course code</label>
                </div>

                <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Join</button>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
    include ('footer.php');
  ?>

  <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>
