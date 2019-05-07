<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

$appt_error_messages = array();

//DELETE APPTS THAT HAVE PASSED
$sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment FROM appointments
        JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
        JOIN subjects ON appointment_subjects.subject_id = subjects.id
        WHERE appointments.user_id = :user_id
        ORDER BY appointments.date";
$params = array(
  ':user_id' => $current_user['id']
);
$result = exec_sql_query($db, $sql, $params);
if ($result) {
  $records = $result->fetchAll();
  if (count($records) > 0) { // if there are records
    $now = date('m/d/Y', time());
    $yesterday = date('m/d/Y', time() - 60 * 60 * 24);
    $current_time = date('h:i');
    foreach ($records as $record) {
      $date = $record['date'];
      if ($date <= $yesterday || ($date == $now && $record['time_start'] < $current_time)) {
        $appt_to_delete = $record['id'];
        //Delete from appointments table
        $sql = "DELETE FROM appointments WHERE id = :appt_to_delete;";
        $params = array(
          ':appt_to_delete' => $appt_to_delete
        );
        $result = exec_sql_query($db, $sql, $params);
        //Delete from appointment_subjects table
        $sql = "DELETE FROM appointment_subjects WHERE appointment_id = :appt_to_delete;";
        $params = array(
          ':appt_to_delete' => $appt_to_delete
        );
        $result = exec_sql_query($db, $sql, $params);
      }
    }
  }
}

//Delete appointment
$deleted_appt = FALSE;
if (isset($_POST['cancel_appointment'])) {
  $appt_to_delete = intval($_GET['appt_to_delete']);
  //Delete from appointments table
  $sql = "DELETE FROM appointments WHERE id = :appt_to_delete;";
  $params = array(
    ':appt_to_delete' => $appt_to_delete
  );
  $result = exec_sql_query($db, $sql, $params);
  //Delete from appointment_subjects table
  $sql = "DELETE FROM appointment_subjects WHERE appointment_id = :appt_to_delete;";
  $params = array(
    ':appt_to_delete' => $appt_to_delete
  );
  $result = exec_sql_query($db, $sql, $params);
  //cancel appt complete
  $deleted_appt = TRUE;
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
        echo "<h2>Welcome, " . htmlspecialchars($current_user['first_name']) . " " . htmlspecialchars($current_user['last_name']) . "!</h2>";
        echo "<p>In the Student Center, you can view existing appointments, edit appointments, schedule a new appointment, or cancel an appointment. As a member, you can also submit testimonials.</p>";
      } else {
        echo "<h2>What is the Student Center?</h2>";
        echo "<p>The Mitrani Tutoring Student Center is a place where students and parents can track and schedule tutoring sessions with Laurie. You can also submit testimonials. Sign in to access these exclusive tools!</p>";
      }
      ?>
    </div>
    <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
  </div>
  <?php
  if (!is_user_logged_in()) { ?>
    <div class="body-div" id="login_form_div">
      <form id="login_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#login_form" method="post">
        <?php
        foreach ($session_messages as $session_messages) {
          echo "<p class='error'>" . $session_messages . "</p>";
        }
        ?>
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
} elseif (is_user_logged_in() && $current_user['id'] != 1) { // signed in, NOT admin
  if (isset($_POST["submit"]) && is_user_logged_in()) {
    // filter input for upload
    $date = format_date($_POST["date"]);
    $time = $_POST['start_time']; //filter input
    $time_start = date("G:i", strtotime($time));
    $time_end = date("G:i", strtotime('+1 hour', strtotime($time)));
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    if ($date == "//" or $time == NULL) { // no date and time
      $valid_date_time = false;
      if ($date == "//") { // date is empty
        array_push($appt_error_messages, "Please provide a date.");
      }
      if ($time == NULL) { // time is null
        array_push($appt_error_messages, "Please provide a start time between 9 AM and 6 PM.");
      }
    } else { // given date and time
      //is date in the past?
      $test_time = date("G:i:s", strtotime($time));
      $test_date_time = $date . " " . $test_time;
      $now = date('m/d/Y G:i:s', time());
      // is time 9-7?
      $time_int = date("G", strtotime($time));
      if ($time_int < date("G", strtotime("9 am")) || $time_int > date("G", strtotime("6 pm"))) { // not 9-7
        $valid_time = FALSE;
      } else {
        $valid_time = TRUE;
      }
      //date is in the future, Sun-Fri, 9-7, then okay to schedule
      if (($test_date_time > $now) && (date("l", strtotime($date)) != "Saturday") && $valid_time) {
        $valid_date_time = TRUE;
        // check if given date + time overlaps with any other apptmt start or end time frames
        $sql = "SELECT * FROM appointments WHERE (appointments.date = :date) AND (((:start_time < appointments.time_start AND appointments.time_start < :end_time) OR (:start_time < appointments.time_end AND appointments.time_end < :end_time)) OR (:start_time = appointments.time_start AND appointments.time_end = :end_time));";
        $params = array(
          ':date' => $date,
          ':start_time' => $time_start,
          ':end_time' => $time_end
        );
        $time_overlap = exec_sql_query($db, $sql, $params)->fetchAll();
        if (count($time_overlap) > 0) { // if overlap -- NOT AVAILABLE
          $time_is_available = FALSE;
          array_push($appt_error_messages, "Sorry, this time slot is not available. Please try another date and start time between 9 AM and 6 PM.");
        } else { // AVAILABLE TIME
          $time_is_available = TRUE;
        }
      } else { // date not in future
        $valid_date_time = FALSE;
        array_push($appt_error_messages, "Please select a valid future date and time. 1-hour appointments are Sunday - Friday, starting at 9 AM - 6 PM.");
      }
    }

    //check if any subjects are checked
    $subj_selected = FALSE;
    $subjects = exec_sql_query($db, "SELECT * FROM subjects", $params = array())->fetchAll();
    foreach ($subjects as $subject) {
      $subj = $subject['subject'];
      if (isset($_POST[$subj])) {
        $subj_selected = TRUE;
      };
    }
    if ($subj_selected == FALSE) {
      array_push($appt_error_messages, "Please select at least one subject for your appointment.");
    }

    // location
    if (!in_array($location, ["Home", "School", "Office"])) { // if given location NOT in valid options
      array_push($appt_error_messages, "Invalid location. Please select one of the given locations.");
      $valid_location = FALSE;
    } else {
      $valid_location = TRUE;
    }

    //Upload Time of Appointment
    if (isset($time_is_available) && $time_is_available && $valid_date_time && $subj_selected && $valid_location) {
      $sql = "INSERT INTO appointments (date,time_start,time_end,location,comment,user_id) VALUES (:date,:time_start,:time_end,:location,:comment,:user_id)";
      $params = array(
        ':date' => $date,
        ':time_start' => $time_start,
        ':time_end' => $time_end,
        ':location' => $location,
        ':comment' => $comment,
        ':user_id' => intval($current_user['id'])
      );
      $result = exec_sql_query($db, $sql, $params);
      $appt_id = intval($db->lastInsertId("id"));
      // update subjects
      // check for each subject that has been checked, insert respective subject id
      $all_subjects = exec_sql_query($db, "SELECT * FROM subjects", $params = array())->fetchAll();
      foreach ($all_subjects as $all_subject) {
        $subj_id = $all_subject['id'];
        $subj = $all_subject['subject'];
        if (isset($_POST[$subj])) {
          $sql = "INSERT INTO 'appointment_subjects' (appointment_id, subject_id) VALUES (:appt_id, :subj_id);";
          $params = array(
            ':appt_id' => $appt_id,
            ':subj_id' => $subj_id
          );
          $result = exec_sql_query($db, $sql, $params);
        }
      }
      if ($result) {
        $submit_success = TRUE;
      }
    }
  }
  ?>
    <div class="body-div" id="existing_appointments_div">
      <?php
      if (isset($deleted_appt) && $deleted_appt) {
        echo "<p class='success'>Appointment successfully cancelled!</p>";
      }
      ?>
      <h2>Existing appointments</h2>
      <?php
      $sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment FROM appointments
        JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
        JOIN subjects ON appointment_subjects.subject_id = subjects.id
        WHERE appointments.user_id = :user_id
        ORDER BY appointments.date";
      $params = array(
        ':user_id' => $current_user['id']
      );
      $result = exec_sql_query($db, $sql, $params);
      if ($result) {
        $records = $result->fetchAll();
        if (count($records) > 0) { // if there are records
          ?>
          <div class="table-div">
            <table>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
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
      } else {
        echo "<p class='no_appt'>You do not have any scheduled appointments.</p>";
      }
    } ?>
    </div>
    <!-- Appointment Form -->
    <div class="body-div">
      <div class="form-div">
        <form id="signup_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#signup_form" method="post">
          <h2>Schedule an Appointment</h2>
          <h4>(All appointments last <span class="underline">1 hour</span>)</h4>
          <?php
          foreach ($appt_error_messages as $appt_error_message) {
            echo "<p class='appt_error'>" . $appt_error_message . "</p>";
          }
          if (isset($submit_success) && $submit_success) {
            echo "<p class='success'>Appointment successfully scheduled!</p>";
          }
          ?>
          <div>
            <div>
              <div class="form_label">
                <p class="required">*</p>
                <label for="date">Date:</label>
              </div>
              <input class="input_box" id="date" type="date" name="date" />

            </div>
            <div>
              <div class="form_label">
                <p class="required">*</p>
                <label for="time">Start Time:</label>
              </div>
              <input class="input_box" type="time" id="time" name="start_time" min="09:00" max="18:00">
            </div>
            <div>
              <div class="form_label">
                <p class="required">*</p>
                <label>Subject(s):</label>
              </div>
              <?php
              $records = exec_sql_query($db, "SELECT subject FROM subjects", $params = array())->fetchAll();
              foreach ($records as $record) {
                echo "<p class='subject'><input type='checkbox' name='" . $record['subject'] . "' value='" . $record['subject'] . "'>" . $record['subject'] . "</p>";
              }
              ?>
            </div>
            <div>
              <div class="form_label">
                <p class="required">*</p>
                <label for="location">Location:</label>
              </div>
              <select name="location" id="location" <?php if (isset($_POST['location'])) {
                                                      echo "class = 'selected'";
                                                    } ?>>
                <?php
                $all_locations = ["Home", "School", "Office"];
                foreach ($all_locations as $chosen_location) {
                  if (isset($_POST['change_location']) && $_POST['change_location'] == $chosen_location) {
                    $selected = "selected = 'selected' class='selected-option'";
                  } else {
                    $selected = "";
                  }
                  echo "<option value='" . $chosen_location . "' " . $selected . ">" . $chosen_location . "</option>";
                }
                ?>
              </select>
              <p class="office_address">Office Address: 301 Arthur Godfrey Rd. Penthouse</p>
            </div>
            <div id="comment-div">
              <div class="form_label">
                <label for="comment">Comment:</label>
              </div>
              <textarea rows=5 cols=40 name="comment" id="comment"></textarea>
            </div>
            <div>
              <button name="submit" type="submit">Schedule</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="body-div">
      <?php include("includes/testimonial_form.php"); ?>
    </div>
  <?php
} elseif (is_user_logged_in() && $current_user['id'] == 1) { // admin
  ?>
    <div class="body-div" id="existing_appointments_div">
      <?php
      if (isset($deleted_appt) && $deleted_appt) {
        echo "<p class='success'>Appointment successfully cancelled!</p>";
      }
      ?>
      <h2>Scheduled appointments</h2>
      <?php
      $sql = "SELECT DISTINCT appointments.id as id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment, users.first_name, users.last_name FROM users
      JOIN appointments ON users.id = appointments.user_id
      JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
      JOIN subjects ON appointment_subjects.subject_id = subjects.id
      ORDER BY appointments.date";
      $params = array();
      $result = exec_sql_query($db, $sql, $params);
      if ($result) {
        $records = $result->fetchAll();
        if (count($records) > 0) { // if there are records
          ?>
          <div class="table-div">
            <table>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Student</th>
                <th>Location</th>
                <th>Details</th>
              </tr>
              <?php
              foreach ($records as $record) {
                print_all_appt($record);
              }
              ?>
            </table>
          </div>
        <?php
      } else {
        echo "<p class='no_appt'>You do not have any scheduled appointments.</p>";
      }
    } ?>
    </div>
  <?php
}
?>
  <?php include("includes/footer.php"); ?>
</body>
