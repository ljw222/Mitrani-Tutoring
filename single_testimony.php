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
    <title>View Testimony</title>
</head>

<body>
    <?php include("includes/header.php"); ?>
    <div class="top-page-div">
        <h1>View Full Testimony</h1>
    </div>

    <div class="body-div">
        <div>
            "Full testimony goes here."
        </div>
        <div id="testimony-credits">
            <div>
                <?php
                // SQL JOIN USERS AND TESTIMONIAL
                // EXAMPLE
                $id = array(
                    "first_name" => firstname,
                    "last_name" => lastname,
                    "rating" => 4,
                    "grade" => 2,
                    "date" => 2012
                );
                echo "<p>" . $id["first_name"] . " " . $id["last_name"] . "</p>";
                for ($star = 1; $star <= $id["rating"]; $star++) {
                    echo "<img class='rating_star' src='images/star.png' alt='rating star'/>";
                };
                echo "<p>Grade " . $id["grade"] . "</p>";
                echo "<p>" . $id["date"] . "</p>";
                ?>
            </div>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
