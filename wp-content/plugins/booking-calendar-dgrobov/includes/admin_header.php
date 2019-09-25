<?php ob_end_flush();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<title>BookingWizz <?php echo CURT_VER;?> | Admin area</title>

<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="css/bs-admin.css" type="text/css" />

<?php 

if(IS_WP_PLUGIN=='1'){?>

   <link rel="stylesheet" href="css/bs_wp_admin.css" type="text/css" /> 

<?php

}

?>



<link href="css/dd.css" rel="stylesheet" type="text/css"/>

<link href="css/dropdown-skins.css" rel="stylesheet" type="text/css"/>

<!--[if IE]><link href="css/ie.css" rel="stylesheet" type="text/css" /><![endif]-->	

<script type="text/javascript" >

window.ajaxDIRx = '//<?php echo MAIN_URL; ?>ajax';

window.dashboardChartMonths = [<?php echo "'".Jan."',"."'".Feb."',"."'".Mar."',"."'".Apr."',"."'".May."',"."'".Jun."',"."'".Jul."',"."'".Aug."',"."'".Sep."',"."'".Oct."',"."'".Nov."',"."'".Dec."'"; ?>];

window.dashboardChartDays = [<?php echo "'".Mon." ss',"."'".Tue."',"."'".Wed."',"."'".Thu."',"."'".Fri."',"."'".Sat."',"."'".Sun."'"; ?>];</script>

<script src="js/jquery-1.9.0.min.js"></script>

    <script type="text/javascript" src="js/jquery-migrate-1.2.1.js"></script>



    <link type="text/css" href="./css/redmond/jquery-ui-1.8.20.custom.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>





<link type="text/css" media="screen" rel="stylesheet" href="css/colorbox.css" />

<script type="text/javascript" src="js/jquery.colorbox.js"></script>



<link type="text/css" media="screen" rel="stylesheet" href="css/tipTip.css" />

<script type="text/javascript" src="js/jquery.tipTip.js"></script>



<script type="text/javascript" src="js/jquery.dd.js"></script>

<script>

    $(function(){

        $(".tipTip").tipTip({maxWidth:"200px", edgeOffset:10,defaultPosition:'right'});

        

        jQuery("#BW_frame",top.document).height(jQuery("#content").height()+200)

    });



</script>



<?php



$self = pathinfo($_SERVER['PHP_SELF']);

$self = $self['basename'];

if(in_array($self,array('admin-index.php','bs-reports.php','bs-reports-app.php','bs-reports-mdb.php'))): ?>

<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->	

<script type="text/javascript" src="js/flot/jquery.flot.js"></script>

<script type="text/javascript" src="js/flot/jquery.flot.navigate.js"></script>	

<script type="text/javascript" src="js/reports.flot.js"></script>

<?php endif; # added only for page where used ?>	



<?php bw_do_action("bw_admin_header_includes");?>



<script src="js/main.js"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-52396333-2', 'auto');
  ga('send', 'pageview');

</script>

</head>

<body class="admin">

    <div id="wrapper">

<noscript>

    <div class="js_error">Please enable JavaScript or upgrade to better <a href="http://www.mozilla.com/en-US/firefox/upgrade.html" target="_blank">browser</a></div>

</noscript>

<?php if(IS_WP_PLUGIN!='1'){?>

    <div id="header">

    	<?php if($_SESSION["logged_in"] != true): ?>

				<div class="adj_left" >

					<a href="admin.php"><img src="images/logo.png" border="0"/></a>

				</div>

		<?php endif;#display logo if not logged ?> 

    	<?php if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true) :?>

		<div class="adj_row" >

			<div class="adj_row adj_top_menu_1" >

				<div class="adj_left" >

                    <a href="admin.php"><img src="images/logo.png" border="0"/></a>

				</div>

				<div class="adj_right" >

					<ul>

					<li><a href="#" >Help</a>

						<div class="phl" >

						<ul class="ul_to_left">

							<li><a href="help/index.html" target="_blank">Documentation</a></li>

							<li><a href="http://support.convergine.com" target="_blank">Support Forum</a></li>

						</ul>

						</div>

					</li>

					<li style="margin-left:3px;"><a href="bs-settings.php" >Admin</a>

						<div class="phl" style="left: 0;" >

						<ul>	

							<li><a href="bs-settings.php" >Settings</a></li>

							<li><a href="bs-logout.php" >Logout</a></li>

						</ul>

						</div>

					</li>

					</ul>

				</div>

			</div><!-- end top menu 1 -->

			<div class="adj_row adj_top_menu_2" >

				<div class="adj_right" >

					<ul>

						<li><a href="admin-index.php">Dashboard</a></li>

		                <li><a href="index.php" target="_blank">Calendar Preview</a></li>

		                <li><a href="bs-addons.php">Add Ons</a></li>

		            </ul>

				</div>

			</div><!-- emd top menu 2 -->

		</div>

		<div class="adj_row adj_menu_3" >

			<ul>

            <?php 

                $subMenu = array();

                $currSubMenu = array();

                foreach ($menuList as $k=>$v):

                    $mainActive = false;

                    if(isset($v['sub_menu'])){

                        $subMenu = $v['sub_menu'];

                        foreach($subMenu as $m=>$s){

                            if(in_array(BW_SELF,$s)){

                                $mainActive = true;

                                $currSubMenu = $subMenu;

                            }

                        }

                    }elseif(BW_SELF=="getAdminPage.php"){

                       

                        if(strpos($_SERVER['REQUEST_URI'],$v['menu_link'] ) )$mainActive = true;;

                    }else{

                        if(BW_SELF==$v['menu_link']){

                            $mainActive = true;

                        }

                    }

          ?>

<li><a class="<?php echo $mainActive?"active":""?>" href="<?php echo $v['menu_link']?>"><?php echo strtoupper($v['menu_title'])?></a></li>

			<?php endforeach;#end main menu ?>

			<li><?php build_plugins_menu();?></li>

			</ul>



		</div><!-- end top menu 3 -->

		<div class="adj_row adj_menu_4" >

			<div class="adj_left" >

				<ul class="reset" >

                <?php $i=0;foreach($currSubMenu as $k=>$v): ?>

					<li class="<?php echo $i==0?"nosep":""?> <?php echo BW_SELF==$v['menu_link']?"active":""?>"><a href="<?php echo $v['menu_link']?>"><?php echo $v['menu_title']?></a></li>

                 <?php $i++;endforeach;#end sub menu ?>

				</ul>

			</div>

		</div>

		<?php endif;# end if logged ?>

    </div><!-- end header -->

        

        <?php } else { ?>



        <div id="header_wp">

            	<?php if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true) :?>

        		<div class="adj_row adj_menu_3" >

                    <a href="admin.php"><img src="images/bw_wp_logo.jpg" class="bw_wp_logo" border="0" /></a>

                    <ul>

                        <?php

                            $subMenu = array();

                            $currSubMenu = array();

                            foreach ($menuList as $k=>$v):

                                $mainActive = false;

                                if(isset($v['sub_menu'])){

                                    $subMenu = $v['sub_menu'];

                                    foreach($subMenu as $m=>$s){

                                        if(in_array(BW_SELF,$s)){

                                            $mainActive = true;

                                            $currSubMenu = $subMenu;

                                        }

                                    }

                                }elseif(BW_SELF=="getAdminPage.php"){



                                    if(strpos($_SERVER['REQUEST_URI'],$v['menu_link'] ) )$mainActive = true;;

                                }else{

                                    if(BW_SELF==$v['menu_link']){

                                        $mainActive = true;

                                    }

                                }

                      ?>

            <li><a class="<?php echo $mainActive?"active":""?>" href="<?php echo $v['menu_link']?>"><?php echo strtoupper($v['menu_title'])?></a></li>

                        <?php endforeach;#end main menu ?>

                    </ul>

        		</div><!-- end top menu 3 -->

        		<div class="adj_row adj_menu_4" >

        			<div class="adj_left" >

        				<ul class="reset" >

                        <?php $i=0;foreach($currSubMenu as $k=>$v): ?>

        					<li class="<?php echo $i==0?"nosep":""?> <?php echo BW_SELF==$v['menu_link']?"active":""?>"><a href="<?php echo $v['menu_link']?>"><?php echo $v['menu_title']?></a></li>

                         <?php $i++;endforeach;#end sub menu ?>

        				</ul>

        			</div>

                    <br clear="all"/>

        		</div>

        		<?php endif;# end if logged ?>

            </div><!-- end header wp-->







        <?php } ?>

    <div style="clear: both"></div>

        <?php if($demo){ echo '<h5 style="color:red; width: 100%; margin: 0 auto; display: block; font-size:14px; margin-top:10px; text-align:center;">! PLEASE NOTE - DEMO WILL BE REFRESHED EVERY 15 MINUTES !</h5>'; }