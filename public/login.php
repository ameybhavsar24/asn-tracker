<?php
  // Initialize session
  session_start();

  // Check if user is already logged in, if yes redirect to dashboard
  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
  }
  require_once("../db.php");
  $email = $password = "";
  $email_err = $password_err = "";
  $check_errors = true;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
      $email_err = "Please enter your email.";
    } else {
      $email = $mysqli->real_escape_string($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
      $password_err = "Please enter a password.";
    } else {
      $password = $mysqli->real_escape_string($_POST["password"]);
    }


    $check_errors = empty($email_err) && empty($password_err);
    if ($check_errors) {
      $sql = "SELECT id, name, email, password FROM users where email = ?";
      if ($stmt = $mysqli->prepare($sql)) {
        $param_email = $email;
        $stmt->bind_param("s", $param_email);
        if ($stmt->execute()) {
          $stmt->store_result();
          if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $name, $email, $hashed_password);
            if ($stmt->fetch()) {
              if (password_verify($password, $hashed_password)) {
                session_start();

                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["name"] = $name;
                $_SESSION["email"] = $email;

                header('location: dashboard.php');

              } else {
                $password_err = "The password you entered was incorrect.";
              }
            }
          } else {
            $email_err = "No such account exists.";
          }
        } else {
          echo "<script>alert('Something went wrong! Try again later.')</script>";
        }

        $stmt->close();

      }
    }
    $check_errors = empty($email_err) && empty($password_err);
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
      <div class="col-12 col-sm-9 col-md-7 col-lg-5 mx-auto my-5">
        <div class="card card-form my-5">
          <div class="card-body">
            <h5 class="card-title text-center">Sign In</h5>
              <form class="form-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

              <?php
                if (!$check_errors) {
                  ?>
                  <div class="alert alert-danger" role="alert">
                    <!-- Form errors here -->
                    <?php
                      if (!empty($email_err)) echo "<p>".$email_err."</p>";
                      if (!empty($password_err)) echo "<p>".$password_err."</p>";
                    ?>
                  </div>
                  <?php
                }
              ?>

                <div class="form-label-group">
                  <input name="email" value="<?= $email ?>" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
                  <label for="inputEmail">Email address</label>
                </div>

                <div class="form-label-group">
                    <input name="password"  type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                    <label for="inputPassword">Password</label>
                </div>

                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">Remember password</label>
                </div>

                <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Sign in</button>
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

  <?php
    // handle php alerts
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      if ($_GET["register"] == "success") {
        echo '<script>alert("Successfully registered. Please login to continue.")</script>';
      }
    }
  ?>

</body>
</html>
