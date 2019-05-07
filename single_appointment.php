<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!
$appt_id =$_GET['appt_id'];
$sql = "SELECT appointments.user_id FROM appointments WHERE id = $appt_id;";
$result = intval(exec_sql_query($db, $sql, $params)->fetchAll()[0][0]);


$sql = "SELECT first_name, last_name, grade, school, home, phone, email FROM users WHERE id = $result;";
$student = exec_sql_query($db, $sql, $params)->fetchAll()[0];

if($student['grade'] == 0){
    $grade = "Pre-K";
}
else{
    $grade = $student['grade'];
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

    <div class="body-div">
        <h3><em><?php echo $student["first_name"] . " " . $student["last_name"]; ?></em></h3>
        <p>Grade: <?php echo $grade; ?> </p>
        <p>School: <?php echo $student["school"]; ?> </p>
        <p>Home Address: <?php echo $student["home"]; ?> </p>
        <p>Email: <?php echo $student["email"]; ?> </p>
        <p>Phone Number: <?php echo $student["phone"]; ?> </p>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
