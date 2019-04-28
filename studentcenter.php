<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="styles/all.css" type="text/css" rel="stylesheet">
  <title>Student Center</title>
</head>

<body>
  <?php include("includes/header.php"); ?>

  <div class="top-page-div" id="studentcenter-div">
    <h1>Student Center</h1>
    <div>
      <?php
      if (is_user_logged_in()) {
        echo "<h2>Welcome Back, " . htmlspecialchars($current_user['first_name']) . " " . htmlspecialchars($current_user['last_name']) . "!</h2>";
        echo "<p>In the Student Center, you can view existing appointments, edit appointments, schedule a new appointment, or cancel an appointment.</p>";
      } else {
        echo "<h2>What is the Student Center?</h2>";
        echo "<p>The Mitrani Tutoring Student Center is a place for students and parents to track and schedule tutoring sessions with Laurie. Sign in to access these exclusive tools!</p>";
      }
      ?>
    </div>
    <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
  </div>
  <?php
  if (!is_user_logged_in()) { ?>
    <form id="login_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <fieldset>
        <ul>
          <li>
            <label for="username" class="text_label">Username:</label>
            <input id="username" type="text" name="username" />
          </li>
          <li>
            <label for="password" class="text_label">Password:</label>
            <input id="password" type="password" name="password" />
          </li>
          <li>
            <button name="login" type="submit">Sign In</button>
          </li>
        </ul>
      </fieldset>
    </form>
  <?php
} else { ?>
    <p>Existing appointments:</p>
    <?php
    //gets date and time
    $current_username = $current_user['username'];
    $sql = "SELECT times.id, times.date, times.time_start, times.time_end, times.half FROM times WHERE times.id IN (SELECT appointments.time_id FROM appointments JOIN users ON appointments.user_id = (SELECT id FROM users WHERE users.username = '$current_username'));";
    $result = exec_sql_query($db, $sql, $params = array());
    //gets subjects
      //get appoinment ids
      $appt_ids = "SELECT DISTINCT appointments.id FROM appointments JOIN users ON user_id = (SELECT id FROM users WHERE users.username = '$current_username');";
      //get subject_ids from appointment_subjects
      $subj_ids = "SELECT appointment_subjects.subject_id FROM appointment_subjects WHERE appointment_subjects.appointment_id IN $appt_ids;";
      //get subject names
      $subjects = "SELECT subjects.subject FROM subjects WHERE subjects.id IN $subj_ids;";
   // $result_subjects = exec_sql_query($db, $subjects, $params = array());
    if ($result) {
      $records = $result->fetchAll();
      if (count($records) > 0) { // if there are records
        ?>
          <div class="table-div">
            <table>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Details</th>
              </tr>
              <?php
              foreach ($records as $record) {
                print_appt($record);
              }
              ?>
            </table>
          </div>
        <?php
      }
    } ?>

    <form id="signup_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <fieldset>
        <legend>Schedule an appointment!</legend>
        <ul>
          <li>
            <label for="date" class="text_label">Date:</label>
            <input id="date" type="date" name="date" />
          </li>
          <li>
            <label for="time" class="text_label">Time:</label>
            <input type="time" id="time" name="time" min="9:00" max="17:00">
          </li>
          <li>
            <label class="text_label">Subject:</label>
            <p class="subject"><input type="checkbox" name="math" value="math"> Math</p>
            <p class="subject"><input type="checkbox" name="vehicle2" value="Car"> Reading</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Writing</p>
            <p class="subject"><input type="checkbox" name="vehicle2" value="Car"> History</p>
            <p class="subject"><input type="checkbox" name="vehicle2" value="Car"> Science</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Organizational Skills</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Study Skills</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Standardized Test Preparation</p>
          </li>
          <li>
            <button name="submit" type="submit">Submit</button>
          </li>
        </ul>
      </fieldset>
    </form>
  <?php
} ?>
  <legend>

    <?php include("includes/footer.php"); ?>
</body>
