<div id="header-div">
    <h1 class = "header_h1"><a href="index.php">MITRANI TUTORING</a></h1>
    <div class="studentcenter-div"> <a class="studentcenter" href ="studentcenter.php">Student Center</a></div>
</div>

<nav class = "header_nav">

    <ul class = "header_ul">
    <?php

    if ( is_user_logged_in() ) {
            // Add a logout query string parameter
            $logout_url = htmlspecialchars( $_SERVER['PHP_SELF'] ) . '?' . http_build_query( array( 'logout' => '' ) );

            ?> <div class="logout_link"><?php echo '<a class="logout" href="' . $logout_url . '">Sign Out ' . htmlspecialchars($current_user['username']) . '</a>'; ?></div><?php
    }
        $pages = [
            ['k-5.php', 'Pre-K - 5th'],
            ['6-12.php', '6th - 12th'],
            ['testimonials.php', 'Testimonials'],
            ['about.php', 'About Me']
        ];

        foreach ($pages as $page){
            $current_file = basename($_SERVER['PHP_SELF']);
            ?> <li class = "header_li <?php if($current_file == $page[0]){ ?>  current_page"<?php ;} else{?>" <?php ;} ?>><a class = "header_a" href = <?php echo $page[0];?>>
            <?php echo $page[1];?></a></li> <?php
        }
    ?>
    </ul>

</nav>
