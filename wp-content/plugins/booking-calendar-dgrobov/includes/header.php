<?php ob_end_flush(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lt" lang="lt" class="index_html">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width" />
    <meta name="format-detection" content="telephone=no">
    <title>Zombie Bunker - rezervacija<?php echo CURT_VER; ?></title>
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="./css/bs-admin.css" type="text/css" />
    <link href="css/dd.css" rel="stylesheet" type="text/css" />
    <link href="css/dropdown-skins.css" rel="stylesheet" type="text/css" />
    <?php if (!empty($canonical)) { ?>
        <link rel="canonical" href="<?php echo $canonical ?>" />
    <?php } ?>


    <script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="js/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="js/jquery.dd.js"></script>
    <script type="text/javascript" src="./js/main.js"></script>
    <script type="text/javascript" src="./js/wp.js"></script>

    <link type="text/css" href="./css/redmond/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="./js/jquery-ui-1.8.20.custom.min.js"></script>


    <link type="text/css" media="screen" rel="stylesheet" href="./css/colorbox.css" />
    <script type="text/javascript" src="./js/jquery.colorbox.js"></script>
    <script type="text/javascript" src="./js/spinner.js"></script>

    <?php bw_do_action("bw_header_includes"); ?>
    <script>
        $(function() {
            try {
                if ($("#index").length) {
                    top.resizeFrame($('#index').height() + 200, 1100);
                } else {
                    top.resizeFrame($('#resize').height() + 200, 1100);
                }

            } catch (e) {}



        })
    </script>

    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window,
            document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1566984383525340'); // Insert your pixel ID here.
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1566984383525340&ev=PageView&noscript=1" /></noscript>
    <!-- DO NOT MODIFY -->
    <!-- End Facebook Pixel Code -->

    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-52396333-2', 'auto');
        ga('send', 'pageview');
    </script>

</head>

<body style="height: auto" itemscope itemtype="http://schema.org/Product">
    <div id="mess">

    </div>
    <?php if (getOption('language_switch') && count(getLangNaw()) > 1) { ?>
        <div class="languageContainer">
            <?php
                if (strpos($_SERVER['SCRIPT_FILENAME'], 'processing') === false) {

                    foreach (getLangNaw() as $k => $v) {
                        $_GET['action'] = 'changelang';
                        $_GET['lang'] = $k;
                        $get = http_build_query($_GET);
                        echo '<a href="?' . $get . '" ' . ($_SESSION['curr_lang'] == $k ? 'class="current"' : '') . '><img src="' . $v . '"/></a>';
                    }
                }
                ?>

        </div>
    <?php } ?>
    <noscript>
        <div class="js_error">Please enable JavaScript or upgrade to better <a href="http://www.mozilla.com/en-US/firefox/upgrade.html" target="_blank">browser</a></div>
    </noscript>