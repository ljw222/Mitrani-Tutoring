<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

if (isset($_GET['appt_id'])) {
    $appt_id = intval($_GET['appt_id']);
}

// GET DETAILS
$sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment FROM appointments
        WHERE appointments.user_id = :user_id AND appointments.id = :appt_id;";
$params = array(
    ':user_id' => $current_user['id'],
    ':appt_id' => $appt_id
);
$result = exec_sql_query($db, $sql, $params)->fetchAll()[0];

function print_subjects($subjects)
{
    $numSubjects = count($subjects);
    for ($i = 0; $i < count($subjects) - 1; $i++) {
        echo $subjects[$i][0] . ", ";
    }
    echo $subjects[$numSubjects - 1][0];
}

// EDIT APPOINTMENT
// CHOOSE FIELD FORM
if (isset($_POST["choose_field_submit"])) {
    $show_date = FALSE;
    $show_time = FALSE;
    $show_subjects = FALSE;
    $show_location = FALSE;
    $show_comment = FALSE;
    if (isset($_POST["field"])) {
        $field = filter_input(INPUT_POST, "field", FILTER_SANITIZE_STRING);
        if ($field == "Date") {
            $show_date = TRUE;
        } elseif ($field == "Time") {
            $show_time = TRUE;
        } elseif ($field == "Subject(s)") {
            $show_subjects = TRUE;
        } elseif ($field == "Location") {
            $show_location = TRUE;
        } elseif ($field == "Comments") {
            $show_comment = TRUE;
        }
    }
}

// UPDATING APPOINTMENT
//edit date
if (isset($_POST['edit_appt_date'])) {
    // filter new date
    $new_date = format_date($_POST['change_date']);

    // check that date is Sun - Fri
    if (date("l", strtolower($new_date)) == "Saturday") {
        $valid_day_of_week = FALSE;
    } else {
        $valid_day_of_week = TRUE;
    }

    if ($valid_day_of_week) {
        // check if that time is taken for NEW date
        $sql = "SELECT * FROM appointments WHERE (appointments.date = :new_date) AND ((:start_time < appointments.time_start AND appointments.time_start < :end_time) OR (:start_time < appointments.time_end AND appointments.time_end < :end_time)) AND NOT (appointments.id = :appt_id)";
        $params = array(
            ':new_date' => $new_date,
            ':start_time' => $result['time_start'],
            ':end_time' => $result['time_end'],
            ':appt_id' => $appt_id
        );
        $taken_appt = exec_sql_query($db, $sql, $params)->fetchAll();
        if (count($taken_appt) > 0) { // if there are matches already -- NOT AVAILABLE
            $ok_change_date = FALSE;
        } else { // no matches -- AVAILABLE
            $ok_change_date = TRUE;
        }

        if ($ok_change_date) { // if ok to change date
            $sql = "UPDATE appointments SET date = :new_date WHERE id = :appt_id";
            $params = array(
                ':new_date' => $new_date,
                ':appt_id' => $appt_id
            );
            $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
        }
    } // else: error message below
}

// edit times
if (isset($_POST['edit_appt_times'])) {
    $new_start_time = date("G:i", strtotime($_POST['change_start_time']));
    $new_end_time = date("G:i", strtotime('+1 hour', strtotime($_POST['change_start_time'])));

    // check if existing appointments for that date and time
    $sql = "SELECT * FROM appointments WHERE (appointments.date = :date) AND ((:new_start_time < appointments.time_start AND appointments.time_start < :new_end_time) OR (:new_start_time < appointments.time_end AND appointments.time_end < :new_end_time))  AND NOT (appointments.id = :appt_id)";
    $params = array(
        ':date' => $result['date'],
        ':new_start_time' => $new_start_time,
        ':new_end_time' => $new_end_time,
        ':appt_id' => $appt_id
    );
    $taken_appt_time = exec_sql_query($db, $sql, $params)->fetchAll();
    if (count($taken_appt_time) > 0) { // if there are matches already -- NOT AVAILABLE
        $ok_change_time = FALSE;
    } else { // no matches -- AVAILABLE
        $ok_change_time = TRUE;
    }

    if ($ok_change_time) { // if ok to change time
        $sql = "UPDATE appointments SET time_start = :new_start_time, time_end = :new_end_time WHERE id = :appt_id";
        $params = array(
            ':new_start_time' => $new_start_time,
            ':new_end_time' => $new_end_time,
            ':appt_id' => $appt_id
        );
        $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
    }
}

//edit subjects
if (isset($_POST['edit_appt_subjects'])) {
    //delete all entries aka subjects that are tied to this appoinment id
    $sql = "DELETE FROM appointment_subjects WHERE appointment_id = :appt_id;";
    $params = array(
        ':appt_id' => $appt_id
    );
    $deleted_appt_subjs = exec_sql_query($db, $sql, $params);

    // check for each subject that has been checked, insert respective subject id
    $all_subjects = array(1 => 'reading', 2 => 'math', 3 => 'writing', 4 => 'organization', 5 => 'study', 6 => 'test', 7 => 'homework', 8 => 'project');
    foreach ($all_subjects as $all_subject) {
        $subj_id = array_search($all_subject, $all_subjects);
        if (isset($_POST[$all_subject])) {
            $sql = "INSERT INTO appointment_subjects (appointment_id, subject_id) VALUES (:appt_id, :subj_id);";
            $params = array(
                ':appt_id' => $appt_id,
                ':subj_id' => $subj_id
            );
            $add_appt_subjs = exec_sql_query($db, $sql, $params);
            if ($add_appt_subjs) {
                $changed_subjects = TRUE;
            } else {
                $changed_subjects = FALSE;
            }
        }
    }
}

// edit location
if (isset($_POST['edit_appt_location'])) {
    if (in_array($_POST['change_location'], ["Home", "School", "Office"])) { // valid location
        $new_location = filter_input(INPUT_POST, 'change_location', FILTER_SANITIZE_STRING);
        $changed_location = TRUE;
    } else {
        $changed_location = FALSE;
    }
    $sql = "UPDATE appointments SET location = :new_location WHERE id = :appt_id";
    $params = array(
        ':new_location' => $new_location,
        ':appt_id' => $appt_id
    );
    $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
}

//edit comments
if (isset($_POST['edit_appt_comment'])) {
    $new_comment = filter_input(INPUT_POST, 'change_comment', FILTER_SANITIZE_STRING);
    $sql = "UPDATE appointments SET comment = :new_comment WHERE id = :appt_id";
    $params = array(
        ':new_comment' => $new_comment,
        ':appt_id' => $appt_id
    );
    $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
    if (isset($new_comment)) {
        $changed_comment = TRUE;
    } else {
        $changed_comment = FALSE;
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="styles/all.css" type="text/css" rel="stylesheet">
    <title>View Appointment</title>
</head>

<body>
    <?php include("includes/header.php"); ?>

    <div class="top-page-div" id="one-appointment-div">
        <a href=<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?' . http_build_query(array('appt_id' => $appt_id)); ?>>
            <h1>View Appointment</h1>
        </a>
        <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
    </div>

    <?php
    // GET DETAILS
    $sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment FROM appointments
        WHERE appointments.user_id = :user_id AND appointments.id = :appt_id;";
    $params = array(
        ':user_id' => $current_user['id'],
        ':appt_id' => $appt_id
    );
    $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];

    //gets the subjects
    $subjects = exec_sql_query(
        $db,
        "SELECT subjects.subject FROM subjects WHERE subjects.id IN (SELECT appointment_subjects.subject_id FROM appointment_subjects WHERE appointment_subjects.appointment_id = :appt_id);",
        array(':appt_id' => $appt_id)
    )->fetchAll();
    ?>
    <div class="body-div">
        <p>Date: <?php echo $result["date"]; ?> </p>
        <p>Time: <?php echo date("g:i", strtotime($result["time_start"])) .  "-" . date("g:i a", strtotime($result["time_end"])); ?> </p>
        <p>Subject(s): <?php print_subjects($subjects); ?> </p>
        <p>Location: <?php echo print_full_location($result); ?> </p>
        <p>Comments: <?php echo $result["comment"]; ?> </p>
    </div>

    <div class="body-div">
        <h2>Edit Appointment</h2>
        <?php
        if (isset($valid_day_of_week) && $valid_day_of_week == FALSE) {
            echo "<p class='error'>You may only schedule appointments Sundays - Fridays.</p>";
        } elseif (isset($ok_change_date) && $ok_change_date == FALSE) {
            echo "<p class='error'>Sorry, that time is unavailable on " . $new_date . "</p>";
        } elseif (isset($ok_change_date) && $ok_change_date) {
            echo "<p class='success'>You have successfully changed the date to " . $new_date . "</p>";
        }

        if (isset($ok_change_time) && $ok_change_time) {
            echo "<p class='success'> You have successfully changed the time to " . date("g:i", strtotime($new_start_time)) . "-" . date("g:i a", strtotime($new_end_time)) . " on " . $result['date'] . "</p>";
        } elseif (isset($ok_change_time) && $ok_change_time == FALSE) {
            echo "<p class='error' > Sorry, " . date("g:i", strtotime($new_start_time)) . "-" . date("g:i a", strtotime($new_end_time)) . " is unavailable on " . $result['date'] . "</p>";
        }

        if (isset($changed_subjects) && $changed_subjects) {
            echo "<p class='success'>You have successfully changed the subjects for your appointment.</p>";
        } elseif (isset($changed_subjects) && $changed_subjects == FALSE) {
            echo "<p class='error'>Sorry, failed to change subjects for your appointment</p>";
        }

        if (isset($changed_location) && $changed_location) {
            echo "<p class='success'>You have successfully changed the location for your appointment to \"" . $new_location . ".\"</p>";
        } elseif (isset($changed_location) && $changed_location == FALSE) {
            echo "<p class='error'>Sorry, failed to change location for your appointment</p>";
        }

        if (isset($changed_comment) && $changed_comment) {
            echo "<p class='success'>You have successfully changed the comments for your appointment.</p>";
        } elseif (isset($changed_comment) && $changed_comment == FALSE) {
            echo "<p class='error'>Sorry, failed to change comments for your appointment</p>";
        }
        ?>
        <form id="choose_field_form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>#choose_field_form" method="POST">
            <label for="field">Choose a field to change:</label>
            <select name="field" id="field" <?php if (isset($_POST['field'])) {
                                                echo "class = 'selected
                                            '";
                                            } ?>>
                <?php
                $all_fields = ["Date", "Time", "Subject(s)", "Location", "Comments"];
                foreach ($all_fields as $field) {
                    if (isset($_POST['field']) && $_POST['field'] == $field) {
                        $selected = "selected = 'selected' class='selected-option'";
                    } else {
                        $selected = "";
                    }
                    echo "<option value='" . $field . "' " . $selected . ">" . $field . "</option>";
                }
                ?>
            </select>
            <button type="submit" name="choose_field_submit">Select</button>
        </form>
        <?php if (isset($_POST["choose_field_submit"])) {
            ?>
            <form id="edit_appt_form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>#edit_appt_form" method="POST">
                <?php
                if ($show_date) {
                    ?>
                    <div class="form_label">
                        <label for="change_date">Date:</label>
                    </div>
                    <input class="input_box" id="change_date" type="date" name="change_date" />
                    <button type="submit" name="edit_appt_date">Submit</button>
                <?php
            } elseif ($show_time) {
                ?>
                    <div>
                        <div class="form_label">
                            <label for="change_start_time">Start Time:</label>
                        </div>
                        <input class="input_box" type="time" id="change_start_time" name="change_start_time" min="9:00" max="18:00">
                    </div>
                    <button type="submit" name="edit_appt_times">Submit</button>
                <?php
            } elseif ($show_subjects) {
                ?>
                    <div class="form_label">
                        <label>Subject(s):</label>
                    </div>
                    <p class="subject"><input type="checkbox" name="math" value="math"> Math</p>
                    <p class="subject"><input type="checkbox" name="reading" value="reading"> Reading</p>
                    <p class="subject"><input type="checkbox" name="writing" value="writing"> Writing</p>
                    <p class="subject"><input type="checkbox" name="homework" value="homework"> Homework Help</p>
                    <p class="subject"><input type="checkbox" name="project" value="project"> Project Assistance</p>
                    <p class="subject"><input type="checkbox" name="organization" value="organization"> Organizational Skills</p>
                    <p class="subject"><input type="checkbox" name="study" value="study"> Study Skills</p>
                    <p class="subject"><input type="checkbox" name="test" value="test"> Standardized Test Preparation</p>
                    <button type="submit" name="edit_appt_subjects">Submit</button>
                <?php
            } elseif ($show_location) {
                ?>
                    <div class="form_label">
                        <label for="change_location">Location:</label>
                    </div>
                    <select name="change_location" id="change_location" <?php if (isset($_POST['change_location'])) {
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
                    <button type="submit" name="edit_appt_location">Submit</button>
                <?php
            } elseif ($show_comment) {
                ?>
                    <div id="comment">
                        <div class="form_label">
                            <label for="change_comment">Comment:</label>
                        </div>
                        <textarea rows=5 cols=40 name="change_comment" id="change_comment"></textarea>
                    </div>
                    <button type="submit" name="edit_appt_comment">Submit</button>
                <?php
            }
            ?>
            </form>
        <?php
    }
    ?>
    </div>

    <div class="body-div">
        <!-- INSERT PHP ABOUT CURRENT PAGE ID FOR ACTION -->
        <form id="cancel_appt_form" action="<?php echo "studentcenter.php?" . http_build_query(array('appt_to_delete' => $appt_id)); ?>" method="POST" enctype="multipart/form-data">
            <div id="cancel_appointment">
                <button name="cancel_appointment" type="submit">Cancel Appointment</button>
            </div>
        </form>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
