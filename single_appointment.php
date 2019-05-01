<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!
if (isset($_GET['time_id'])) {
    $appt_time_id = $_GET['time_id'];
}
if (isset($_GET['date'])) {
    $appt_date = $_GET['date'];
}
if (isset($_GET['start_time'])) {
    $appt_start = $_GET['start_time'];
}
if (isset($_GET['end_time'])) {
    $appt_end = $_GET['end_time'];
}
if (isset($_GET['half'])) {
    $appt_half = $_GET['half'];
}
// COMMENT??
if (isset($_GET['comment'])) {
    $comment = $_GET['comment'];
}

function duration($time_start, $time_end)
{
    $timesplit_start = explode(':', $time_start);
    $min_start = ($timesplit_start[0] * 60) + ($timesplit_start[1]);

    $timesplit_end = explode(':', $time_end);
    $min_end = ($timesplit_end[0] * 60) + ($timesplit_end[1]);

    echo $min_end - $min_start;
}

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
    $show_comment = FALSE;
    if (isset($_POST["field"])) {
        $field = filter_input(INPUT_POST, "field", FILTER_SANITIZE_STRING);
        if ($field == "Date") {
            $show_date = TRUE;
        } elseif ($field == "Time") {
            $show_time = TRUE;
        } elseif ($field == "Subject(s)") {
            $show_subjects = TRUE;
        } elseif ($field == "Comment") {
            $show_comment = TRUE;
        }
    }
}

//UPDATING APPOINTMENT//

    //edit date
if( isset($_POST['edit_appt_date']) ){}

    //edit times
if( isset($_POST['edit_appt_times']) ){}

    //edit times
if( isset($_POST['edit_appt_subjects']) ){}

    //edit times
if( isset($_POST['edit_appt_comment']) ){

}






// if (isset($_POST['edit_appt_submit'])) {
//     // CODE FOR UPDATING APPOINTMENT
// }
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
    <?php
    include("includes/header.php");
    $appointment_id = exec_sql_query(
        $db,
        "SELECT appointments.id FROM appointments WHERE appointments.time_id = $appt_time_id;",
        array()
    )->fetchAll();
    $appt_id = $appointment_id[0][0];
    $subjects = exec_sql_query(
        $db,
        "SELECT subjects.subject FROM subjects WHERE subjects.id IN (SELECT appointment_subjects.subject_id FROM appointment_subjects WHERE appointment_subjects.appointment_id = '$appt_id');",
        array()
    )->fetchAll();
    ?>

    <div class="top-page-div" id="one-appointment-div">
        <h1>View Appointment</h1>
        <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
    </div>

    <div class="body-div">
        <p>Date: <?php echo $appt_date; ?> </p>
        <p>Time: <?php echo $appt_start . '-' . $appt_end . " " . $appt_half; ?> </p>
        <p>Subject(s): <?php print_subjects($subjects); ?> </p>
        <p>Comment??: <?php echo duration($appt_start, $appt_end) . " Minutes"; ?> </p>
    </div>

    <div class="body-div">
        <h2>Edit Appointment</h2>
        <form id="choose_field_form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>#choose_field_form" method="POST">
            <label for="field">Choose a field to change:</label>
            <select name="field" id="field" <?php if (isset($_POST['field'])) { echo "class = 'selected'";} ?>>
                <?php
                $all_fields = ["Date", "Time", "Subject(s)", "Comment"];
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
                // foreach ($fields as $field) {
                //     echo "CREATE AN INPUT";
                // }
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
                        <input class="input_box" type="time" id="change_start_time" name="change_start_time" min="9:00" max="17:00">
                    </div>
                    <div>
                        <div class="form_label">
                            <label for="change_end_time">End Time:</label>
                        </div>
                        <input class="input_box" type="time" id="change_end_time" name="change_end_time" min="9:00" max="17:00">
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
                    <p class="subject"><input type="checkbox" name="history" value="history"> History</p>
                    <p class="subject"><input type="checkbox" name="science" value="science"> Science</p>
                    <p class="subject"><input type="checkbox" name="organization" value="organization"> Organizational Skills</p>
                    <p class="subject"><input type="checkbox" name="study" value="study"> Study Skills</p>
                    <p class="subject"><input type="checkbox" name="test" value="test"> Standardized Test Preparation</p>
                    <button type="submit" name="edit_appt_subjects">Submit</button>
                    <?php
                } elseif ($show_comment) {
                    ?>
                    <div id="comment">
                        <div class="form_label">
                            <label for="comment">Comment:</label>
                        </div>
                        <textarea rows=5 cols=40 name="change_comment" id="change_comment" ></textarea>
                    </div>
                    <button type="submit" name="edit_appt_comment">Submit</button>
                    <?php
                }
                ?>
                <!-- <button type="submit" name="edit_appt_submit">Submit</button> -->
            </form>
        <?php
    }
    ?>
    </div>

    <div class="body-div">
        <!-- INSERT PHP ABOUT CURRENT PAGE ID FOR ACTION -->
        <form id="cancel_appt_form" action="<?php echo "studentcenter.php?" . http_build_query(array('appt_to_delete' => $appt_id)); ?>" method="POST" enctype="multipart/form-data">
            <button name="cancel_appointment" type="submit">Cancel Appointment</button>
        </form>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
