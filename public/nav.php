<?php
  session_start();
  $name = "";
  if ($_SESSION["loggedin"]) {
    $name = $_SESSION["name"];
  }
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand mb-0 h1" href="/">Assignment Tracker</a>

  <button
    class="navbar-toggler"
    type="button"
    data-toggle="collapse"
    data-target="#navbarTogglerDemo01"
    aria-controls="navbarTogglerDemo01"
    aria-expanded="false"
    aria-label="Toggle navigation"
  >
  <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
      <?php
      if ($_SESSION["loggedin"]) {
      ?>
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="profile.php">Hello, <?= $name ?><span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="logout.php">Logout<span class="sr-only">(current)</span></a>
        </li>
      <?php
      } else {
      ?>
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="login.php">Login<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="register.php">Register<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="about.php">About<span class="sr-only">(current)</span></a>
        </li>
      <?php
      }
      ?>
    </ul>
  </div>
</nav>
