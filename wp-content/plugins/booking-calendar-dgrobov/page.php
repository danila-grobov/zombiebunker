<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<?php
define('PLUGIN_DIR', plugins_url('', __FILE__));
wp_enqueue_script('custom_script', PLUGIN_DIR . '/js/page.js');


?>
<style>
    .admin_wrapper {
        width: 100%;
    }

    #wpcontent {
        padding-left: 0;
    }

    #wpwrap {
        height: 100%;
    }

    #wpbody {
        height: 100%;
    }

    #wpbody-content {
        height: 100%;
        padding-bottom: 0px;
    }
</style>
<iframe src="https://zombiebunker.lt/wp-content/plugins/booking-calendar-dgrobov/admin.php" frameborder="0" class="admin_wrapper" width="100%" height="100%"></iframe>