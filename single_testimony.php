<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

if (isset($_GET['id'])) {
    $single_testimony_id = $_GET['id'];
}

$sql = "SELECT testimonials.testimonial, users.first_name, users.last_name, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id WHERE testimonials.id = :id";
$params = array(
    ':id' => $single_testimony_id
);
$result = exec_sql_query($db, $sql, $params);
$records = $result->fetchAll();
if (count($records) > 0) {
    $record = $records[0];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="styles/all.css" type="text/css" rel="stylesheet">
    <title>View Testimony</title>
</head>

<body>
    <?php include("includes/header.php"); ?>
    <div class="top-page-div" id="one-testimony-div">
        <h1>View Full Testimony</h1>
        <p class="source">Source: <a href="https://unsplash.com/photos/_lhefRJtT0U">Unsplash</a></p>
    </div>

    <div class="body-div">
        <div>
            <?php
            echo "\"" . $record["testimonial"] . "\"";
            ?>
        </div>
        <div id="testimony-credits">
            <div id="testimony-credits-div">
                <p>-</p>
                <div>
                    <?php
                    if ($record["role"] == "Parent") {
                        echo "<p><em><strong>" . $record["role"] . " of " . $record["first_name"] . " " . $record["last_name"] . "</strong></em></p>";
                    } else {
                        echo "<p><em><strong>" . $record["first_name"] . " " . $record["last_name"] . "</strong></em></p>";
                    }
                    echo print_stars($record["rating"]);
                    echo "<p><em>Grade " . $record["grade"] . "</em></p>";
                    echo "<p><em>" . $record["date"] . "</em></p>";
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
