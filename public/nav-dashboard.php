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
          class="nav-link <?php if ($file_name === 'dashboard.php') echo 'active' ?>" 
          href="dashboard.php">
          Home
          <?php if ($file_name === 'dashboard.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'enroll.php') echo 'active' ?>" 
          href="enroll.php">
          Enroll course
          <?php if ($file_name === 'enroll.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'create.php') echo 'active' ?>" 
          href="create.php">
          Create course
          <?php if ($file_name === 'create.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link <?php if ($file_name === 'profile.php') echo 'active' ?>" 
          href="profile.php">
          Profile
          <?php if ($file_name === 'profile.php') { ?> 
            <span class="sr-only">(current)</span>
          <?php } ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>