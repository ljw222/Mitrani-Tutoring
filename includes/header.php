<h1 class = "header_h1">MITRANI TUTORING</h1>
    <nav class = "header_nav">
        <ul class = "header_ul">
        <?php
            $pages = [
                ['k-12.php', 'Pre-K - 5th'],
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
