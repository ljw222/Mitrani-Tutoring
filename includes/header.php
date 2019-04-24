<!-- <div id="full-header"> -->
<div id="header-div">
    <div id="title-diag">
        <h1 class = "header_h1"><a href="index.php">MITRANI TUTORING</a></h1>
    </div>
    <div class="studentcenter-div">
        <?php
        if ( is_user_logged_in() ) {
                // Add a logout query string parameter
                $logout_url = htmlspecialchars( $_SERVER['PHP_SELF'] ) . '?' . http_build_query( array( 'logout' => '' ) );

                echo '<a class="logout" href="' . $logout_url . '">Sign Out ' . htmlspecialchars($current_user['username']) . '</a>';
        } else {
            echo "<a class='studentcenter' href ='studentcenter.php'>Student Center</a>";
        }
        ;?>
    </div>
</div>

<nav>
    <div class = "header_ul">
    <?php
    $pages = [
        ['k-5.php', 'Pre-K - 5th'],
        ['6-12.php', '6th - 12th'],
        ['testimonials.php', 'Testimonials'],
        ['about.php', 'About Me']
    ];

    if (is_user_logged_in()) {
        array_push($pages, ['studentcenter.php', 'Student Center']);
    }

    foreach ($pages as $page){
        $current_file = basename($_SERVER['PHP_SELF']);

        if ($current_file == $page[0]) { //if on current page
            echo "<a class = 'header_a current_page' href ='".$page[0]."'>".$page[1]."</a>";
        } else {
            echo "<a class = 'header_a' href ='".$page[0]."'>".$page[1]."</a>";
        }
    }
    ?>
    </div>
</nav>
<!-- </div> -->
