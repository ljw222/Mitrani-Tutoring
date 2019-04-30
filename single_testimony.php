<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

//id of the testimonial
if (isset($_GET['id'])) {
    $single_testimony_id = intval($_GET['id']);
}

//get the user id of the person who wrote the testimonial
$sql = "SELECT testimonials.user_id FROM testimonials WHERE testimonials.id = $single_testimony_id;";
$result = exec_sql_query($db, $sql, $params);
$id_of_author = intval($result->fetchAll()[0][0]);


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
                    // role and name
                    if ($record["role"] == "Parent" && $id_of_author != 0) {
                        echo "<p><em><strong>" . $record["role"] . " of " . $record["first_name"] . " " . $record["last_name"] . "</strong></em></p>";
                    } else if ($record["role"] != "Parent" && $id_of_author != 0) {
                        echo "<p><em><strong>" . $record["first_name"] . " " . $record["last_name"] . "</strong></em></p>";
                    } else {
                        echo "<p><em><strong>" . "Anonymous" . "</strong></em></p>";
                    }
                    // rating
                    echo print_stars($record["rating"]);
                    // grade
                    if ($record["grade"] == 0) {
                        echo "<p><em>Pre-K</em></p>";
                    } elseif ($record["grade"] != NULL) {
                        echo "<p><em>Grade " . $record["grade"] . "</em></p>";
                    }
                    // date
                    echo "<p><em>" . $record["date"] . "</em></p>";
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    // echo 'current user id is: ' . $current_user['id'];
    // echo 'the author of this testimonial is: ' . (int)$id_of_author;
    if (isset($current_user) && ($id_of_author == $current_user['id'])) {
        $testimonial_to_delete = $single_testimony_id;
        ?>
        <form id="delete_form" method="post" action="<?php echo "testimonials.php?" . http_build_query(array('testimonial_to_delete' => $testimonial_to_delete)); ?>" enctype="multipart/form-data">
            <div class="delete_button">
                <button name="delete_testimonial" type="submit">Delete Testimonial</button>
            </div>
        </form>
    <?php
}
?>

    <?php include("includes/footer.php"); ?>

</body>

</html>
