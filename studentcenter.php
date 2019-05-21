<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

$appt_error_messages = array();
$register_error_messages = array();

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
    $current_time = date("G:i");

    foreach ($records as $record) {
      $date = $record['date'];
      if ($date <= $yesterday || (($date == $now) && ($record['time_start'] < $current_time))) {
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

//submit testimony
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
        echo "<p>The Mitrani Tutoring Student Center is a place where students and parents can track and schedule tutoring sessions with Laurie. Schedule or cancel an appointment or submit a testimony! Sign in to access these exclusive tools!</p>";
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
        <form id="signup_form" class="signup_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#signup_form" method="post">
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
              <input class="input_box" id="date" type="date" name="date" <?php
                                                                          if( isset($_POST['date']) && !isset($submit_success) ){
                                                                            echo 'value = '. $_POST['date'];
                                                                          } ?> >
            </div>
            <div>
              <div class="form_label">
                <p class="required">*</p>
                <label for="time">Start Time:</label>
              </div>
              <input class="input_box" type="time" id="time" name="start_time" min="09:00" max="18:00" <?php
                                                                          if( isset($_POST['start_time']) &&
                                                                            !isset($submit_success) ){
                                                                              echo 'value = '. $_POST['start_time'];
                                                                          } ?> >
            </div>
            <div>
              <div class="form_label">
                <p class="required">*</p>
                <label>Subject(s):</label>
              </div>
              <?php
                $records = exec_sql_query($db, "SELECT subject FROM subjects", $params = array())->fetchAll();
                foreach ($records as $record) {
                  if( isset($_POST[$record['subject']]) && !isset($submit_success) ){
                    $checked = 'checked';
                  }
                  else{
                    $checked = '';
                  }
                  echo "<p class='subject'><input type='checkbox' name='" . $record['subject'] . "' value='" . $record['subject'] . "'" . $checked . ">" . $record['subject'] . "</p>";
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
                  if (isset($_POST['location']) && $_POST['location'] == $chosen_location && !isset($submit_success) ) {
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
              <textarea rows=5 cols=40 name="comment" id="comment"><?php if( isset($comment) && !isset($submit_success) )
                                                                  { echo $comment; } ?>
              </textarea>
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
  if (isset($_POST["register"]) && is_user_logged_in() && $current_user['id'] == 1) {
    // filter input for upload
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $new_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    if (isset($_POST['password']) && trim($_POST['password']) != '') {
      $new_password = trim( $_POST["password"] );
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    }
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $grade = filter_input(INPUT_POST, 'grade', FILTER_SANITIZE_STRING);
    $home = filter_input(INPUT_POST, 'home', FILTER_SANITIZE_STRING);
    $school = filter_input(INPUT_POST, 'school', FILTER_SANITIZE_STRING);

    if ($first_name == "" or $last_name == "") { // no first or last name
      $valid_name = false;
      if ($first_name == "") { // first name is empty
        array_push($register_error_messages, "Please enter your first name.");
      }
      if ($last_name == "") { // last name is empty
        array_push($register_error_messages, "Please enter your last name.");
      }
    }
    if($new_username == ""){
      $valid_username = false;
      array_push($register_error_messages, "Please enter a username.");
    } else {
      $existing_usernames = exec_sql_query(
        $db,
        "SELECT username FROM users",
        array())->fetchAll();
      // var_dump($existing_usernames);
      if( in_array("$new_username", $existing_usernames) ){
        $valid_username = false;
        array_push($register_error_messages, "Username is already taken. Please enter a different username.");
      }
    }
    if ($email == "" or $phone == ""){
      $valid_contact_info = false;
      if ($email == ""){
        array_push($register_error_messages, "Please enter an email address.");
      }
      if ($phone == ""){
        array_push($register_error_messages, "Please enter a phone number.");
      }
    }
    //Register User
    if (!isset($valid_name) && !isset($valid_username) && !isset($valid_contact_info))  {
      $sql = "INSERT INTO users (username,password,first_name,last_name,grade,home,school,email,phone) VALUES (:username,:password,:first_name,:last_name,:grade,:home,:school,:email,:phone)";
      $params = array(
        ':username' => $new_username,
        ':password' => $hashed_password,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':grade' => $grade,
        ':home' => $home,
        ':school' => $school,
        ':email' => $email,
        ':phone' => $phone
      );
      $result = exec_sql_query($db, $sql, $params);
      if ($result) {
        $register_success = TRUE;
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
      <h2>Scheduled appointments</h2>

      <form id="sort_appts-form" action="studentcenter.php#existing_appointments_div" method="POST">
        <p>Sort by:</p>
        <select name="sort_by_name">
          <option value="student_first_name" name="student_first_name">Student First Name</option>
          <option value="student_last_name" name="student_last_name">Student Last Name</option>
          <option value="date" name="date">Date</option>
        </select>
        <button type="submit" name="submit-sortappt">Sort</button>
      </form>

      <?php
      if( isset($_POST['submit-sortappt']) && $_POST['sort_by_name'] == "student_first_name"){
        $sql = "SELECT DISTINCT appointments.id as id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment, users.first_name, users.last_name FROM users
        JOIN appointments ON users.id = appointments.user_id
        JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
        JOIN subjects ON appointment_subjects.subject_id = subjects.id
        ORDER BY users.first_name";
      }
      elseif( isset($_POST['submit-sortappt']) && $_POST['sort_by_name'] == "student_last_name"){
        $sql = "SELECT DISTINCT appointments.id as id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment, users.first_name, users.last_name FROM users
        JOIN appointments ON users.id = appointments.user_id
        JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
        JOIN subjects ON appointment_subjects.subject_id = subjects.id
        ORDER BY users.last_name";
      }
      else{
        $sql = "SELECT DISTINCT appointments.id as id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment, users.first_name, users.last_name FROM users
        JOIN appointments ON users.id = appointments.user_id
        JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
        JOIN subjects ON appointment_subjects.subject_id = subjects.id
        ORDER BY appointments.date";
      }
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
    <!-- Register Form -->
    <div class="body-div" id="register_div">
      <h2>Register a New Student</h2>
      <?php
          foreach ($register_error_messages as $register_error_message) {
            echo "<p class='appt_error'>" . $register_error_message . "</p>";
          }
          if (isset($register_success) && $register_success) {
            echo "<p class='success'>Student successfully Registered!</p>";
          }
      ?>
      <form class="register_form" action="studentcenter.php#register_div" method="POST">
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="first_name">First Name</label>
          </div>
          <input class="input_box" id="first_name" type="text" name="first_name" <?php
                                                                      if( isset($_POST['first_name']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['first_name'];
                                                                      } ?> >
        </div>
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="last_name">Last Name</label>
          </div>
          <input class="input_box" id="last_name" type="text" name="last_name" <?php
                                                                      if( isset($_POST['last_name']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['last_name'];
                                                                      } ?> >
        </div>
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="username">Username</label>
          </div>
          <input class="input_box" id="username" type="text" name="username" <?php
                                                                      if( isset($_POST['username']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['username'];
                                                                      } ?> >
        </div>
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="password">Password</label>
          </div>
          <input class="input_box" id="password" type="password" name="password">
        </div>
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="email">Email</label>
          </div>
          <input class="input_box" id="email" type="email" name="email" <?php
                                                                      if( isset($_POST['email']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['email'];
                                                                      } ?> >
        </div>
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="phone">Phone Number</label>
          </div>
          <input class="input_box" id="phone" type="text" name="phone" <?php
                                                                      if( isset($_POST['phone']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['phone'];
                                                                      } ?> >
        </div>
        <div>
          <div class="form_label">
            <p class="required">*</p>
            <label for="grade">Grade</label>
          </div>
          <select name="grade">
            <?php
              for($g = -1; $g <=12; $g++){
                if($g == -1){
                  echo "<option value='k'>Pre-K</option>";
                }
                else if($g == 0){
                  echo "<option value='k'>Kindergarten</option>";
                }
                else{
                  echo "<option value='$g'>$g</option>";
                }
              }
            ?>
          </select>
        </div>
        <div>
          <div class="form_label">
            <label for="home">Home Address</label>
          </div>
          <input class="input_box" id="home" type="text" name="home" <?php
                                                                      if( isset($_POST['home']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['home'];
                                                                      } ?> >
        </div>
        <div>
          <div class="form_label">
            <label for="school">School</label>
          </div>
          <input class="input_box" id="school" type="text" name="school" <?php
                                                                      if( isset($_POST['school']) && !isset($register_success) ){
                                                                        echo 'value = '. $_POST['school'];
                                                                      } ?> >
        </div>

        <button type="submit" name="register">Register</button>
      </form>
    </div>
    <!-- display all students -->
    <div class="body-div" id="existing_appointments_div">
      <h2>All Students</h2>
      <?php
        $users = exec_sql_query(
          $db,
          "SELECT * FROM users WHERE id != 1 ORDER BY first_name ASC",
          array())->fetchAll();
        foreach($users as $user){
          $student_name = $user['first_name']." ". $user['last_name'];
          echo "<ul class='all_students'>";
          echo "<li><a href='single_student.php?" . http_build_query(array('user_id' => $user['id']))."'>$student_name</a></li>";
          echo "</ul>";
        }
      ?>
    </div>
  <?php
}
?>
  <?php include("includes/footer.php"); ?>
</body>
