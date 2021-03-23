<?php
  $file_name = basename($_SERVER["PHP_SELF"]);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
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
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'index.php') echo 'active' ?>" 
          href="index.php">
          Home
          <?php if ($file_name === 'index.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'login.php') echo 'active' ?>" 
          href="login.php">
          Login
          <?php if ($file_name === 'login.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'register.php') echo 'active' ?>" 
          href="register.php">
          Register
          <?php if ($file_name === 'register.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'about.php') echo 'active' ?>" 
          href="about.php">
          About
          <?php if ($file_name === 'about.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
    </ul>
  </div>
</nav>
