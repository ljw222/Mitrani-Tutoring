<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

//re-format date input
function format_date($date) {
  $pieces = explode("-", $date);
  return ($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);

}

if (isset($_POST['submit_testimony'])) {
  echo testimonial_php();
}

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
    <div class="body-div">
      <form id="login_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
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
      </form>
    </div>
  <?php
} else { ?>
    <div class="body-div" id="existing_appointments_div">
      <h2>Existing appointments</h2>
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
    </div>
    <?php
    if (isset($_POST["submit"]) && is_user_logged_in()) {

      // filter input for upload
      $date = format_date($_POST["date"]);
      $start_time = $_POST['start_time'] - 12; //filter input

//Upload Time of Appointment
if($upload_info['error']== UPLOAD_ERR_OK) {
  echo 'hello';
      // get id for start time
      $sql = "SELECT times.id FROM times WHERE times.time_start = $start_time AND times.date = $date";
      echo $start_time;
      echo $date;
      $params = array();
      $result = exec_sql_query($db, $sql, $params)->fetchAll();
  $sql = "INSERT INTO 'appointments' (time_id, user_id) VALUES (:time_id, :user_id);";
  $params = array(
     ':time_id' => $result[0],
     ':user_id' => $current_user['id']
  );
  $result = exec_sql_query($db, $sql, $params);
}
    }
    ?>
    <!-- Appointment Form -->
    <div class="body-div">
      <div class="form-div">
        <form id="signup_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <h2>Schedule an Appointment</h2>
          <ul>
            <li>
              <div class="form_label">
                <p class="required">*</p><label for="date">Date:</label>
              </div>
              <input id="date" type="date" name="date" />
            </li>
            <li>
              <div class="form_label">
                <p class="required">*</p><label for="time">Start Time:</label>
              </div>
              <input type="time" id="time" name="start_time" min="9:00" max="17:00">
            </li>
            <li>
              <div class="form_label">
                <p class="required">*</p><label for="time">End Time:</label>
              </div>
              <input type="time" id="time" name="end_time" min="9:00" max="17:00">
            </li>
            <li>
              <div class="form_label">
                <p class="required">*</p><label>Subject:</label>
              </div>
              <p class="subject"><input type="checkbox" name="math" value="math"> Math</p>
              <p class="subject"><input type="checkbox" name="reading" value="reading"> Reading</p>
              <p class="subject"><input type="checkbox" name="writing" value="writing"> Writing</p>
              <p class="subject"><input type="checkbox" name="history" value="history"> History</p>
              <p class="subject"><input type="checkbox" name="science" value="science"> Science</p>
              <p class="subject"><input type="checkbox" name="organization" value="organization"> Organizational Skills</p>
              <p class="subject"><input type="checkbox" name="study" value="study"> Study Skills</p>
              <p class="subject"><input type="checkbox" name="test" value="test"> Standardized Test Preparation</p>
            </li>
            <div id="comment">
         <div class="form_label">
            <label for="comment">Comment:</label>
         </div>
         <textarea rows=10 cols=40 name="comment" id="comment" ></textarea>
      </div>
            <li>
              <button name="submit" type="submit">Submit</button>
            </li>
          </ul>
        </form>
      </div>
    </div>
    <div class="body-div">
      <?php include("includes/testimonial_form.php");?>
    </div>
  <?php
} ?>
    <?php include("includes/footer.php"); ?>
</body>
