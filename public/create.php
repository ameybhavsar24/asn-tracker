<?php
  session_start();
  // Check if user is already logged in
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("location: login.php");
    exit;
  }
  require_once("../db.php");
  $name = $description = "";
  $name_err = $description_err = "";
  $check_errors = true;
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["name"]))) {
      $name_err = "Please enter course name.";
    } else {
      $name = $mysqli->real_escape_string($_POST["name"]);
    }
    if (empty(trim($_POST["description"]))) {
      $description_err = "Please enter course description.";
    } else {
      $description = $mysqli->real_escape_string($_POST["description"]);
    }

    $check_errors = empty($name_err) && empty($description_err);
    if ($check_errors) {
      $sql = "INSERT INTO courses (name, description, creatorId) VALUES (?, ?, ?)";
      if ($stmt = $mysqli->prepare($sql)) {
        // bind variables to prepared statement as parameters
        $param_name = $name;
        $param_description = $description;
        $param_creatorId = $_SESSION["id"];
        $stmt->bind_param("sss", $param_name, $param_description, $param_creatorId);

        // Attempt to exectute the prepared statement
        if ($stmt->execute()) {
          
        } else {
          echo "<script>alert('Oops! Something went wrong. Please try again later.')</script>";
        }
        $stmt->close();
      }
    }
    $mysqli->close();
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
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6" >
            <div class="card card-form my-2">
                <div class="card-body">
                    <h5 class="card-title text-center">Create a new course.</h5>
                    <form class="form-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php
                          if (!$check_errors) {
                        ?>
                        <div class="alert alert-danger" role="alert">
                          <!-- Form errors here -->
                          <?php
                            if (!empty($name_err)) echo "<p>".$name_err."</p>";
                            if (!empty($description_err)) echo "<p>".$name_err."</p>";
                          ?>
                        </div>
                        <?php
                          }
                        ?>
                        <div class="form-label-group">
                            <input name="name" type="text" id="courseName" class="form-control" placeholder="Course Name" required autofocus />
                            <label for="courseName">Course Name</label>
                        </div>
                        <div class="form-label-group">
                          <textarea name="description"  type="text" id="courseDescription" class="form-control" placeholder="Add more information about this course." required></textarea>  
                        </div>
                        <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Create course</button>
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