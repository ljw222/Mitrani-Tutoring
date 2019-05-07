<?php
   // DO NOT REMOVE!
   include("includes/init.php");
   // DO NOT REMOVE!
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

//delete appts that are in the pst
$sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location,   appointments.comment FROM appointments
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
    foreach ($records as $record) {
      $date = new DateTime($record['date']);
      $now = new DateTime();

      if($date < $now) {
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
} else {
  if (isset($_POST["submit"]) && is_user_logged_in()) {
    // filter input for upload
    $appt_id =intval($db->lastInsertId("id"));
    $date = format_date($_POST["date"]);
    $time = $_POST['start_time']; //filter input
    $time_start = date("G:i", strtotime($time));
    $time_end = date("G:i",strtotime('+1 hour',strtotime($time)));
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    // check if given date + time overlaps with any other apptmt start or end time frames
    $sql = "SELECT * FROM appointments WHERE (appointments.date = :date) AND ((:start_time < appointments.time_start AND appointments.time_start < :end_time) OR (:start_time < appointments.time_end AND appointments.time_end < :end_time)) OR (:start_time == appointments.time_start AND appointments.time_end == :end_time);";
    $params = array(
      ':date' => $date,
      ':start_time' => $time_start,
      ':end_time' => $time_end
    );
    $time_overlap = exec_sql_query($db, $sql, $params)->fetchAll();
    if (count($time_overlap) > 0) { // if overlap -- NOT AVAILABLE
      $time_is_available = FALSE;
    } else { // AVAILABLE TIME
      $time_is_available = TRUE;
    }

    //validate form -- messages
    $valid_field = true;

     //is date in the past?
    $given = new DateTime($_POST["date"]);
    echo "<script>console.log('".$given."')</script>";
    $now = new DateTime();
      //date is in the future, okay to schedule
    if ($given > $now) {
      $valid_date = TRUE;
      $valid_field = TRUE;
    } else { // date not in future
      $valid_date = FALSE;
      $valid_field = FALSE;
    }
    if ($date == NULL){
        $valid_field = false;
        $valid_date = false;
    }

    if ($time == NULL){
        $valid_field = false;
        $valid_time = false;
    }
    //check if any subjects are checked
    $subjects = exec_sql_query($db, "SELECT subject FROM subjects", $params=array())->fetchAll();
    foreach($subjects as $subject){
      if (isset($_POST[$subject['subject']])){
        $subj_selected = TRUE;
      };
    }
    if (!isset($subj_selected)){
        $valid_field = false;
        $valid_subject = false;
    }
    if (!in_array($location, ["Home", "School", "Office"])) { // if given location NOT in valid options
      $valid_field = FALSE;
      $valid_location = FALSE;
    }

    //Upload Time of Appointment
    // $upload_info['error']== UPLOAD_ERR_OK &&
    if ($time_is_available && $valid_field && !isset($valid_location) && $valid_date) {
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
      $appt_id =intval($db->lastInsertId("id"));
      // update subjects
      // check for each subject that has been checked, insert respective subject id
      $all_subjects = exec_sql_query($db, "SELECT * FROM subjects", $params=array())->fetchAll();
      foreach ($all_subjects as $all_subject) {
        $subj_id = $all_subject['id'];
        if (isset($_POST[$all_subject['subject']])){
          $sql = "INSERT INTO 'appointment_subjects' (appointment_id, subject_id) VALUES (:appt_id, :subj_id);";
          $params = array(
            ':appt_id' => $appt_id,
            ':subj_id' => $subj_id
          );
          $result = exec_sql_query($db, $sql, $params);
        }
      }
    } else {
        $invalid_time_id = TRUE;
    }
  }
  ?>
    <div class="body-div" id="existing_appointments_div">
      <?php
        if ($deleted_appt) {
          echo "<p class='success'>Appointment successfully cancelled!</p>";
        }
        if ($valid_field) {
          echo "<p class='success'>Appointment successfully scheduled!</p>";
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
               <h4>(All appointments last <p class="underline">1 hour</p>)</h4>
               <p class="appt_error <?php if(!isset($valid_date) OR $valid_date) { echo "hidden";} ?>">Please enter a valid date</p>
               <p class="appt_error <?php if(!isset($valid_time)) { echo "hidden";} ?>">Please enter a valid time, between 9 AM and 6 PM</p>
               <p class="appt_error <?php if(!isset($valid_subject)) { echo "hidden";} ?>">Please select a subject for your appointment</p>
               <p class="appt_error <?php if(!isset($invalid_time_id)) { echo "hidden";} ?>">This time slot is not available. Please make an appointment with an open time slot</p>
               <ul>
                  <li>
                     <div class="form_label">
                        <p class="required">*</p>
                        <label for="date">Date:</label>
                     </div>
                     <input class="input_box" id="date" type="date" name="date"/>

                  </li>
                  <li>
                     <div class="form_label">
                        <p class="required">*</p>
                        <label for="time">Start Time:</label>
                     </div>
                     <input class="input_box" type="time" id="time" name="start_time" min="9:00" max="18:00">
                  </li>
                  <li>
                    <div class="form_label">
                      <p class="required">*</p>
                      <label>Subject(s):</label>
                    </div>
                    <?php
                    $records = exec_sql_query($db, "SELECT subject FROM subjects", $params=array())->fetchAll();
                    foreach ($records as $record) {
                      echo "<p class='subject'><input type='checkbox' name='".$record['subject']."' value='".$record['subject']."'>".$record['subject']."</p>";
                    }
                     ?>
                  </li>
                  <li>
                    <div class="form_label">
                        <p class="required">*</p>
                        <label for="location">Location:</label>
                     </div>
                    <select name="location" id="location" <?php if (isset($_POST['location'])) { echo "class = 'selected'";} ?>>
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
                  </li>
                  <div id="comment">
                     <div class="form_label">
                        <label for="comment">Comment:</label>
                     </div>
                     <textarea rows=5 cols=40 name="comment" id="comment" ></textarea>
                  </div>
                  <li>
                     <button name="submit" type="submit">Schedule</button>
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
