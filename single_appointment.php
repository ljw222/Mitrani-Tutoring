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

function duration($time_start, $time_end){
    $timesplit_start = explode(':', $time_start);
    $min_start = ($timesplit_start[0]*60)+($timesplit_start[1]);

    $timesplit_end = explode(':', $time_end);
    $min_end = ($timesplit_end[0]*60)+($timesplit_end[1]);

    echo $min_end - $min_start;
}

function print_subjects($subjects){
    $numSubjects = count($subjects);
    for($i = 0; $i < count($subjects) - 1; $i++){
        echo $subjects[$i][0] . ", ";
    }
    echo $subjects[$numSubjects - 1][0];
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
    <?php
        include("includes/header.php");
        $appointment_id = exec_sql_query(
            $db,
            "SELECT appointments.id FROM appointments WHERE appointments.time_id = $appt_time_id;",
            array())->fetchAll();
        $appt_id = $appointment_id[0][0];
        $subjects = exec_sql_query(
            $db,
            "SELECT subjects.subject FROM subjects WHERE subjects.id IN (SELECT appointment_subjects.subject_id FROM appointment_subjects WHERE appointment_subjects.appointment_id = '$appt_id');",
            array())->fetchAll();
    ?>

    <div class="top-page-div" id="one-appointment-div">
        <h1>View Appointment</h1>
        <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
    </div>

    <div class="body-div">
        <p>Date: <?php echo $appt_date;?> </p>
        <p>Time: <?php echo $appt_start . '-' . $appt_end . " " . $appt_half;?> </p>
        <p>Subject(s): <?php print_subjects($subjects); ?> </p>
        <p>Duration: <?php echo duration($appt_start, $appt_end) . " Minutes" ; ?> </p>
    </div>

    <div class="body-div">
        <h2>Edit Appointment</h2>
        <form id="choose_field_form" action="" method="POST">
            <label for="field">Choose a field to change:</label>
            <select name="field" id="field">
                <option value="date">Date</option>
                <option value="time">Time</option>
                <option value="subjects">Subject(s)</option>
                <option value="duration">Duration</option>
            </select>
            <button type="submit" name="choose_field_submit">Select</button>
        </form>
        <?php if (isset($_POST["choose_field_submit"])) {
            ?>
            <form id="edit_appt_form" action="" method="POST">
                <?php
                foreach ($fields as $field) {
                    echo "CREATE AN INPUT";
                }
                ?>
                <button type="submit" name="edit_appt_submit">Submit</button>
            </form>
        <?php
    }
    ?>
    </div>

    <div class="body-div">
        <!-- INSERT PHP ABOUT CURRENT PAGE ID FOR ACTION -->
        <form id="cancel_appt_form" action="" method="POST">
            <button name="cancel_appointmnt" type="submit">Cancel Appointment</button>
        </form>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
