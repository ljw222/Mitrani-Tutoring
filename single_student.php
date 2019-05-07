<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!
if (isset($_GET['appt_id'])) {
    $appt_id = intval($_GET['appt_id']);
}
// GET DETAILS
if ($current_user['id'] != 1) {
    $sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment FROM appointments
        WHERE appointments.user_id = :user_id AND appointments.id = :appt_id;";
    $params = array(
        ':user_id' => $current_user['id'],
        ':appt_id' => $appt_id
    );
    $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
} else {
    $sql = "SELECT DISTINCT * FROM appointments JOIN users ON appointments.user_id = users.id
        WHERE appointments.id = :appt_id;";
    $params = array(
        ':appt_id' => $appt_id
    );
    $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
}

function print_subjects($subjects)
{
    $numSubjects = count($subjects);
    for ($i = 0; $i < count($subjects) - 1; $i++) {
        echo $subjects[$i]['subject'] . ", ";
    }
    echo $subjects[$numSubjects - 1]['subject'];
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="styles/all.css" type="text/css" rel="stylesheet">
    <title>View Student Information</title>
</head>

<body>
    <?php include("includes/header.php"); ?>

    <div class="top-page-div" id="one-appointment-div">
        <a href="<?php echo 'single_appointment.php?' . http_build_query(array('appt_id' => $appt_id)); ?>">
            <h1>View Student Information</h1>
        </a>
        <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
    </div>

    <?php
    // GET DETAILS
    if ($current_user['id'] != 1) {
        $sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.time_end, appointments.location, appointments.comment FROM appointments
        WHERE appointments.user_id = :user_id AND appointments.id = :appt_id;";
        $params = array(
            ':user_id' => $current_user['id'],
            ':appt_id' => $appt_id
        );
        $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
    } else {
        $sql = "SELECT DISTINCT * FROM appointments JOIN users ON appointments.user_id = users.id
        WHERE appointments.id = :appt_id;";
        $params = array(
            ':appt_id' => $appt_id
        );
        $result = exec_sql_query($db, $sql, $params)->fetchAll()[0];
    }
    //gets the subjects
    $subjects = exec_sql_query(
        $db,
        "SELECT subjects.subject FROM subjects WHERE subjects.id IN (SELECT appointment_subjects.subject_id FROM appointment_subjects WHERE appointment_subjects.appointment_id = :appt_id);",
        array(':appt_id' => $appt_id)
    )->fetchAll();

    if ($current_user['id'] != 1) { // not admin
        ?>
        <div class="body-div">
            <p> You do not have permission to view this Student's Information</p>
        </div>
    <?php
    } else { // admin
    ?>
        <div class="body-div">
            <h3><em><?php echo $result["first_name"] . " " . $result["last_name"]." (grade ".$result["grade"].")"; ?></em></h3>

            <p>Student Email: <?php echo $result['email']; ?> </p>
            <p>Student Phone: <?php echo $result['phone']; ?> </p>
            <p>Student Address: <?php echo $result['home']; ?></p>
            <p> Student School: <?php echo $result['school']; ?></p>
        </div>
    <?php
}
?>
