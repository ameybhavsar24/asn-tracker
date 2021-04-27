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
  $course = [];
  $assignments = [];

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    require_once('../db.php');
    $assignment = [
      'title' => $mysqli->real_escape_string(trim($_POST['title'])),
      'description' => $mysqli->real_escape_string(trim($_POST['description'])),
      'dueTime' => $mysqli->real_escape_string(trim($_POST['dueTime'])),
      'assignmentType' => $mysqli->real_escape_string(trim($_POST['assignmentType'])),
      'courseId' => $_SESSION['curr_course']['id'],
    ];
    $sql = "INSERT INTO `assignments`(`title`, `description`, `courseId`, `dueTime`, `assignmentType`) VALUES (?,?,?,?,?)";
    if ($stmt = $mysqli->prepare($sql)) {
      $stmt->bind_param("ssiss", $assignment['title'], $assignment['description'], $assignment['courseId'], $assignment['dueTime'], $assignment['assignmentType']);
      if ($stmt->execute()) {
        header("location: dashboard.php?createAssignment=1");
        exit;
      } else {
        echo 'Failed to add assignment => '.$mysqli->error;
      }
    } else {
      echo 'Invalid query'.$mysqli->error;
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['courseId']) || empty(trim($_GET['courseId']))) {
      header("location: dashboard.php");
    }
    require_once('../db.php');
    $course['id'] = $mysqli->real_escape_string(trim($_GET['courseId']));
    // check if course exists and if yes, get details from table course
    // $sql = "SELECT `name`, `description`, `creatorId` FROM `courses` WHERE `courseId` = ?";
    // $sql = "SELECT courses.name, courses.description, courses.creatorId, users.name, users.email FROM courses INNER JOIN users on courses.creatorId = users.id  WHERE courses.courseId = ?";
    $sql = "SELECT courses.name, courses.description, courses.creatorId, users.name, users.email FROM ( courses LEFT JOIN enrollments on courses.courseId = enrollments.courseId INNER JOIN users on courses.creatorId = users.id ) WHERE ( (enrollments.userId = ? OR courses.creatorId = ?) AND courses.courseId = ? )";
    if ($stmt = $mysqli->prepare($sql)) {
      $param_courseId = $course['id'];
      $stmt->bind_param("sss", $id, $id, $param_courseId);
      if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
          // course does not exists
          header("location: dashboard.php?course=404");
          exit;
        }
        $stmt->bind_result($course['name'], $course['description'], $course['creatorId'], $course['creatorName'], $course['creatorEmail']);
        $_SESSION['curr_course'] = $course;
        $stmt->fetch();
      }
    }
    // get course assignments
    $sql = "SELECT * FROM assignments WHERE courseId = ? ORDER BY creationTime DESC";
    if ($stmt = $mysqli->prepare($sql)) {
      $param_courseId =$course['id'];
      $stmt->bind_param("s", $param_courseId);
      if ($stmt->execute()) {
        $stmt->store_result();
        $result = $stmt->get_result();
        if ($stmt->num_rows > 0) {
          $stmt->bind_result($assignmentId, $assignmentTitle, $assignmentDescription, $assignmentCourseId, $assignmentCreationTime, $assignmentDueTime, $assignmentType);
          while ($stmt->fetch()) {
            $new_assignment = array (
              'id' => $assignmentId,
              'title' => $assignmentTitle,
              'description' => $assignmentDescription,
              'courseId' => $assignmentCourseId,
              'creationTime' => $assignmentCreationTime,
              'dueTime' => $assignmentDueTime,
              'type' => $assignmentType
            );
            array_push($assignments, $new_assignment);
          }
          $_SESSION['assignments'] = $assignments;
        }
      }
    }
    $assignmentIds = [$_SESSION['id']];
    foreach ($assignments as $assignment) {
      array_push($assignmentIds, $assignment['id']);
    }
    // echo '<pre>';
    // var_dump($assignmentIds);
    $in = str_repeat('?,', count($assignmentIds) - 2) . '?'; // placeholders
    // $sql = "SELECT * FROM `submission` WHERE (userId = ? AND assignmentId IN ($in)) ORDER BY submissionTime DESC";
    $sql = "SELECT * FROM (`submission` LEFT JOIN `assignments` ON submission.assignmentId = assignments.assignmentId) WHERE (userId = ? AND submission.assignmentId IN (?)) ORDER BY creationTime DESC";
    // var_dump($sql);
    if ($stmt = $mysqli->prepare($sql)) {
      $types = str_repeat('i', count($assignmentIds)); //types
      if ($stmt->bind_param($types, ...$assignmentIds)) {
        // echo 'binded';
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_all(MYSQLI_ASSOC); // fetch the data 
        if (count($data) > 0) {
          $i = 0; $j = 0;
          
          for ($i=0; $i<count($assignments); $i++) {
            for ($j=0; $j<count($data); $j++) {
              if ($data[$j]['assignmentId'] == $assignments[$i]['id']) {
                $assignments[$i]['submission'] = $data[$j];
                break;
              }
            }
          }
          // echo '<pre>';
          // var_dump($assignments);
          // echo '</pre>';
        }
      } else {
        echo $mysqli->error;
      }
    } else {
      echo 'Failed to prepare' . $mysqli->error;
    }

  }
  $course = $_SESSION['curr_course'];
  $createdCourse = $id == $course['creatorId'];
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

  <div class="container-fluid">
    <div class="row" id="courseBanner">
      <?php
        if ($createdCourse) {
        ?>
        <div class="col-12">
          <p class="muted">You created this course.</p>
        </div>
        <?php
        }
      ?>
      <div class="col-12">
        <h1 class="display-4">
          <?= $course['name'] ?>
          <small style="display: inline;" class="text-muted lead"> by <?= $course['creatorName'] ?></small>
        </h1>
      </div>
      <div class="col-12">
        <div class="row">
          <div class="col-12 col-sm-6">
            <?= $course['description'] ?>
          </div>
          <div class="col-12 col-sm-6 text-right">
            <p class="lead">Course Code: <b><?= $course['id'] ?></b></p>
          </div>
        </div>

      </div>
      <div class="col-12"><hr /></div>
    </div>


    <div class="container">
    <?php if($createdCourse) { ?>
      <div class="row">
        <div class="col-12">
        <div class="card assignment-item">
          <div class="card-body">
            <h5 class="card-title">
             Create new assignment task
            </h5>
            <form class="row form-form" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]).'?course='.$course['id'] ?>" method="post">

              <div class="col-12">
              <div class="form-label-group">
                <input name="title" type="text" id="asnTitle" class="form-control" placeholder="Title of Assignment" required>
                <label for="asnTitle">Title of Assignment</label>
              </div>
              </div>

              <div class="col-12">
              <div class="form-label-group">
                <input name="description" type="text" id="asnDesc" class="form-control"  placeholder="Description" required>
                <label for="asnDesc">Description</label>
              </div>
              </div>

              <div class="col-12 col-md-4">
              <div class="form-label-group">
                <input min="<?= date('Y-m-d').'T'.date('H:i') ?>" name="dueTime" type="datetime-local" id="dueTime" class="form-control"  placeholder="Due Time" required>
                <label for="dueTime">Due Time</label>
              </div>
              </div>

              <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="assignmentType">Select type of submission</label>
                <select name="assignmentType" class="form-control" id="assignmentType">
                  <option value="document">Document like word or pdf</option>
                  <option value="code">Code</option>
                </select>
              </div>
              </div>
              <div class="col-12 row">
                <div class="col-12 col-sm-4">
                  <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Create</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        </div>
      </div>
    <?php } ?>
      <div class="row">
        <?php
          if (!empty($assignments)) {
            foreach($assignments as $key=>$assignment) {
        ?>
        <div class="col-12">
        <div class="card assignment-item" data-toggle="modal" data-target="#assignmentModal<?= $key ?>">
          <div class="card-body">
            <h5 class="card-title"><?= $assignment['title'] ?></h5>
            <p class="lead">
            <?php 
              if (isset($assignment['submission'])) {
                echo '✅';
              }
            ?>
            </p>
            <h6 class="card-subtitle mb-3">
              <p class="mb-1">
                <?php
                  if ($assignment['type'] == 'document') {
                    echo "Written assignment";
                  } else if ($assignment['type'] == 'code') {
                    echo "Coding assignment";
                  }
                ?>
              </p>
            </h6>
            <p class="card-text text-right small">
              <?= date("d F, Y", strtotime($assignment['creationTime'])) ?>
            </p>
          </div>
        </div>
        </div>
        <div class="modal fade" id="assignmentModal<?= $key ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content" action="./submitAsn.php?id=<?= $key ?>" method="POST" enctype="multipart/form-data">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">
                  <?= $assignment['title'] ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <?php
                if (isset($assignment['submission'])) {
                  ?>
                  <p class="lead small">
                    You have submitted this assignment but you can resubmit. ✅
                  </p>
                  <p class="lead">
                  <h5 class="mt-0">Attachment</h5>
                  <div class="card attachment-card">
                    <div class="card-body">
                      <h6 class="card-title mt-0 mb-0"><?= explode('/', $assignment['submission']['file_name'])[1] ?></h6>
                      <a href="<?= $assignment['submission']['file_name'] ?>" class="stretched-link">Download</a>
                    </div>
                    
                  </div>
                  </p>
                  <?php
                }
                ?>
                <div class="mt-1 mb-2">
                  <?= $assignment['description'] ?>
                </div>
                <?php
                  if ($assignment['type'] == 'document') {
                  ?>
                  <h6>Upload assignment as a document. <p class="text-muted small">Supported types are .docx, .ppt, .txt & .pdf</p></h6>
                  <div class="file-input">
                    <input name="assignmentFile" type="file" id="file<?= $assignment['id'] ?>" class="file" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/">
                    <label class="btn btn-primary" for="file<?= $assignment['id'] ?>">Select file</label>
                    <p class="file-name"></p>
                  </div>
                  <?php
                  } else if ($assignment['type'] == 'code') {
                  ?>
                  <div class="form-group">
                    <label class="h6" for="codeTextArea<?= $assignment['id'] ?>">Paste your code in the following textbox.</label>
                    <textarea name="assignmentCode" class="form-control" id="codeTextArea<?= $assignment['id'] ?>" rows="10"></textarea>
                  </div>
                  <?php
                  }
                ?>
                <hr />
                <p class="text-muted small">
                  <span class="text-danger">
                    <?= 'Due on: '.date("H:i A d F, Y", strtotime($assignment['dueTime'])) ?><br />
                  </span>
                  <span class="text-primary">
                    <?= 'Created on: '.date("H:i A d F, Y", strtotime($assignment['creationTime'])) ?>
                  </span>
                </p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit assignment</button>
              </div>
            </form>
          </div>
        </div>
        <?php
            }
          }
        ?>
      </div>
    </div>

  </div>
  <?php
    include_once ('footer.php');
  ?>

  <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script>
  const file = document.querySelector('#file');
  file.addEventListener('change', (e) => {
    // Get the selected file
    const [file] = e.target.files;
    // Get the file name and size
    const { name: fileName, size } = file;
    // Convert size in bytes to kilo bytes
    const fileSize = (size / 1000).toFixed(2);
    // Set the text content
    const fileNameAndSize = `${fileName} - ${fileSize}KB`;
    document.querySelector('.file-name').textContent = fileNameAndSize;
  });

  </script>
</body>
</html>
