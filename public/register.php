<?php
  session_start();
  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
  }
  require_once("../db.php");

  $name = $email = $password = $confirm_password = "";
  $name_err = $email_err = $password_err = $confirm_password_err = "";
  $check_errors = true;
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate email
    if (empty(trim($_POST["email"]))) {
      $email_err = "Please enter a email.";
    } else {
      // prepare a select statement
      $sql = "SELECT id FROM users WHERE email = ?";
      if ($stmt = $mysqli->prepare($sql)) {
        // bind variables to prepared statement as parameters
        $param_email = trim($_POST["email"]);
        $stmt->bind_param("s", $param_email);

        // attempt to execute the prepared statement
        if ($stmt->execute()) {
          // store result
          $stmt->store_result();
          if ($stmt->num_rows == 1) {

            $email_err = "This email already exists";
          } else {
            $email = $mysqli->real_escape_string(trim($_POST["email"]));
          }
        } else {
          echo "<script>alert('Oops! Something went wrong. Please try again later.')</script>";
        }

       $stmt->close();

      }
    }

    // validate name
    if (empty(trim($_POST["name"]))) {
      $name_err = "Please enter your name.";
    } else {
      $name = $mysqli->real_escape_string(trim($_POST["name"]));
    }

    // validate password
    if (empty(trim($_POST["password"]))) {
      $password_err = "Please enter a password.";
    } else if (strlen(trim($_POST["password"])) < 6) {
      $password_err = "Password must have atleast 6 characters.";
    } else {
      $password = $mysqli->real_escape_string(trim($_POST["password"]));
    }

    // validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
      $confirm_password_err = "Please confirm your password.";
    } else {
      $confirm_password = $mysqli->real_escape_string(trim($_POST["confirm_password"]));
      if (empty($password_err) && ($password != $confirm_password)) {
        $confirm_password_err = "Passwords did not match.";
      }
    }

    $check_errors = empty($email_err) && empty($password_err) && empty($name_err) && empty($confirm_password_err);

    if ($check_errors) {
      // insert new user to database
      $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
      if ($stmt = $mysqli->prepare($sql)) {
        // bind variables to prepared statement as parameters
        $param_name = $name;
        $param_email = $email;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $param_name, $param_email, $param_password);

        // Attempt to exectute the prepared statement
        if ($stmt->execute()) {
          header("location: login.php?status=1");
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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous" />
  <link rel="stylesheet" href="css/main.css" />
</head>

<body>


  <?php
    include ('nav.php');
  ?>

  <div class="container">
    <div class="row">
      <div class="col-lg-10 col-xl-9 mx-auto my-5">
        <div class="card card-form flex-row my-5">
          <div class="card-img-left d-none d-md-flex">
              <!-- Background image for card set in CSS from "_register.scss" -->
          </div>
          <div class="card-body">
            <h5 class="card-title text-center">Sign Up</h5>
              <form class="form-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <?php
                  if (!$check_errors) {
                    ?>
                    <div class="alert alert-danger" role="alert">
                      <!-- Form errors here -->
                      <?php
                        if (!empty($name_err)) echo $name_err.'<br />';
                        if (!empty($email_err)) echo $email_err.'<br />';
                        if (!empty($password_err)) echo $password_err.'<br />';
                        if (!empty($confirm_password_err)) echo $confirm_password_err.'<br />';
                      ?>
                    </div>
                    <?php
                  }
                ?>

                <div class="form-label-group">
                  <input name="name" value="<?= $name ?>" type="text" id="inputUserame" class="form-control" placeholder="Name" required autofocus>
                  <label for="inputUserame">Name</label>
                </div>

                <div class="form-label-group">
                  <input name="email" value="<?= $email ?>" type="email" id="inputEmail" class="form-control" placeholder="Email address" required>
                  <label for="inputEmail">Email address</label>
                </div>

                <div class="form-label-group">
                  <input name="password" value="<?= $password ?>" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                  <label for="inputPassword">Password</label>
                </div>

                <div class="form-label-group">
                  <input name="confirm_password" type="password" id="inputConfirmPassword" class="form-control" placeholder="Confirm password" required>
                  <label for="inputConfirmPassword">Confirm password</label>
                </div>

                <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Sign Up</button>
                <a class="d-block text-center mt-2 small" href="login.php">New here ? Sign In</a>
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
