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
    <title>View Appointment</title>
</head>

<body>
    <?php include("includes/header.php"); ?>

    <div class="top-page-div">
        <h1>View Appointment</h1>
        <h2>For <?php echo $current_user["first_name"] . " " . $current_user["last_name"]; ?></h2>
    </div>

    <div class="body-div">
        <p>Date:</p>
        <p>Time:</p>
        <p>Subject(s):</p>
        <p>Duration:</p>
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
