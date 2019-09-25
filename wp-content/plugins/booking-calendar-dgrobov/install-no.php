<?php
/******************************************************************************
 * #                         BookingWizz v5.5
 * #******************************************************************************
 * #      Author:     Convergine (http://www.convergine.com)
 * #      Website:    http://www.convergine.com
 * #      Support:    http://support.convergine.com
 * #      Version:    5.5
 * #
 * #      Copyright:   (c) 2009 - 2014  Convergine.com
 * #
 * #******************************************************************************/
		//Load the database file

	$BWMessage = "";
	$BWContinue = true;
	$success = false;
	
	//1. check that includes/ is writable
	//2. if not - throw error, else show form.
	//3. form will have 4 fields for database and 1 field for license key and 1 key for user to enter future username name for this license key.
	//4. after form submitted we need to show success message and further instructions.
	
	if(!is_writable("includes/")){ 
                    @chmod("includes/", 0777);
		if(!is_writable("includes/")){ 
                    @chmod("includes/", 777);
                    if(!is_writable("includes/")){
			//chmoding didn't help. throw error
			$BWContinue = false;
			$BWMessage .= "<div class=error><b>ERROR!</b> Please set chmod 755 or 777 for directory \"includes\"</div>";
                    }
                }
	}
	
		
	if(!is_writable("uploads/")){ 
                    @chmod("uploads/", 0777);
		if(!is_writable("uploads/")){ 
                    @chmod("uploads/", 777);
                    if(!is_writable("uploads/")){
                             //chmoding didn't help. throw error
                             $BWContinue = false;
                             $BWMessage .= "<div class=error><b>ERROR!</b> Please set chmod 755 or 777 for directory \"uploads\"</div>";
                    }
		}
	}
        
        if(!is_writable("log/")){ 
                    @chmod("log/", 0777);
		if(!is_writable("log/")){ 
                    @chmod("log/", 777);
                    if(!is_writable("log/")){
                            //chmoding didn't help. throw error
                            $BWContinue = false;
                            $BWMessage .= "<div class=error><b>Warning!</b> Please set chmod 755 or 777 for directory \"log\"</div>";
                    }
		}
	}
	
	
	if($BWContinue){
	//LOGIN VARIABLES
	$dbn = (!empty($_REQUEST['dbn']))?strip_tags(str_replace("'","`",$_REQUEST['dbn'])):'';
	$dbp = (!empty($_REQUEST['dbp']))?strip_tags(str_replace("'","`",$_REQUEST['dbp'])):'';
	$dbu = (!empty($_REQUEST['dbu']))?strip_tags(str_replace("'","`",$_REQUEST['dbu'])):'';
	$dbh = (!empty($_REQUEST['dbh']))?strip_tags(str_replace("'","`",$_REQUEST['dbh'])):'';
	$bdir = (!empty($_REQUEST['bdir']))?strip_tags(str_replace("'","`",$_REQUEST['bdir'])):'';

	$license = (!empty($_REQUEST['license']))?strip_tags(str_replace("'","`",$_REQUEST['license'])):'';
	$username = (!empty($_REQUEST['username']))?strip_tags(str_replace("'","`",$_REQUEST['username'])):'';
        $domain = (!empty($_REQUEST['domain']))?strip_tags(str_replace("'","`",$_REQUEST['domain'])):'';
        $_domain = $_SERVER['HTTP_HOST'];
	// LOGIN
	if(!empty($_REQUEST["install"]) && $_REQUEST['install']=="yes"){
		if($dbn=="" || $dbp=="" || $dbu=="" || $dbh==""){
			$BWMessage = "<div class=error>Some fields were left empty. All fields are mandatory. Try again</div>";
		} else {
			
			//check DB connection.
			if($link = @mysql_connect($dbh, $dbu, $dbp)){
				if(@mysql_select_db($dbn)){
					$BWContinue=true;
				} else { $BWContinue = false; $BWMessage = "<div class=error><b>ERROR!</b> Database doesn't exist!<br /> Please create it and try again.</div>";}
			} else { $BWContinue = false; $BWMessage = "<div class=error><b>ERROR!</b> Couldn't connect to database with provided information. <br />Please check your input and try again.</div>";}
	 		$l = $license;
			
			if(!is_writable("includes/dbconnect.php")){ 
				@chmod("includes/dbconnect.php", 0777);
				if(!is_writable("includes/dbconnect.php")){ 
					//chmoding didn't help. throw error
					$BWContinue = false;
					$BWMessage .= "<div class=error><b>ERROR!</b> Please set chmod 755 or 777 for file \"includes/dbconnect.php\"</div>";
				}
			}
            include "./includes/grid.functions.php";
			if($BWContinue){
			
				//create mysql.php file
				$ourFileName = "includes/dbconnect.php";
				$bdir = "/".trim($bdir,"/")."/";
				$fh = fopen($ourFileName, 'w+');
					$stringData = '<?php
						error_reporting(E_ALL ^ E_NOTICE);
						//EDIT ONLY FOLLOWING 5 LINES
						$db_host = \''.$dbh.'\'; //hostname
						$db_user = \''.$dbu.'\'; // username
						$db_password = \''.$dbp.'\'; // password
						$db_name = \''.$dbn.'\'; //database name
						$baseDir = \''.$bdir.'\'; // Don\'t change this variable if you will be using booking in the ROOT of the username. 
						// otherwise - change to $baseDir = "/directoryName/"; WITH TRAILING SLASH!
						
						$demo=false;
						
						if(!empty($db_host) && !empty($db_user) && !empty($db_password) && !empty($db_name)){
							$link = mysql_connect($db_host, $db_user, $db_password) or die("1. Open dbconnect.php and edit mysql variables. <br/> 2. Run install.php ");
							@mysql_select_db($db_name);
						 	$mysqli->query("SET NAMES utf8") or die("err: " . $mysqli->error());
						} else { echo "Application not installed! <a href=\'install.php\'>Click here</a> to proceed with installation."; exit(); }
					?>';
	
				fwrite($fh, $stringData);
				fclose($fh);
				
				require_once("includes/dbconnect.php");

                                require_once("includes/core.functions.php");

				require_once("includes/sql.php");
							
			
					if($BWContinue){
						$BWMessage .= "<br/><br/><div class=success>Installation successful! Please delete this file now and go to your index.php. <br />Default username/password: <b>admin/pass</b></div>";
						$success = true;
						$a = auth($l,$username,$domain);
						@chmod("includes/dbconnect.php", 0644);

                                                updateOption("api_key",$l);

					}
			}
		} 
	}

	}
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>BookingWizz v5.5</title>
<link rel="stylesheet" href="css/installation-page-styles.css" type="text/css" />
</head>
<body>
<div id="header">
	<div class="scriptname left">BookingWizz v5.5 - Installation Wizard</div>
    <!--<div class="version left"> </div>-->
    <br class="clear" />
</div>

<div id="content">

    <div class="install_container"> 
        <div class="login">
        <?php if(!empty($BWMessage)){ echo $BWMessage; } 
		if($success){} else {?><br />
        <form method="post" action="install.php" enctype="multipart/form-data"  name="ff1">
                
                    
                    <p>Please enter your <strong>EXISTING</strong> database login information. <br />
                    All fields are <strong>mandatory</strong>.</p>
                    
                    <label>Database Name:</label> <input type="text" id="dbn" name="dbn" size="30" value="<?php echo $dbn?>" /><br class="clear"/>
                    <label>Database Password:</label> <input type="password" id="dbp" name="dbp"  size="30" value="<?php echo $dbp?>" /><br class="clear"/>
                    <label>Database Hostname:</label> <input type="text" id="dbh" name="dbh"  size="30" value="<?php echo $dbh?>" /><br class="clear"/>
                    <label>Database Username:</label> <input type="text" id="dbu" name="dbu"  size="30" value="<?php echo $dbu?>" /><br class="clear"/>
                    <label>Directory Path:</label> <input type="text" id="bdir" name="bdir"  size="30" value="<?php echo $bdir?>" /> <br class="clear"/>(Example: if booking script is installed in root folder leave this empty, if installing in "booking" folder under root - type "booking" into this field (no quotes), if installing in subfolder "folder/booking" then type "folder/booking" into this field. <strong>NO TRAILING SLASHES!</strong>)<br class="clear"/>
<br />


<p>Please enter your CodeCanyon license key (located in the license text file in your purchase confirmation email from Envato, or login to your account and go to downloads, you will see red link "License Certificate" next to our product). </p>
                    
		  <label>License Key:</label> <input type="text" id="license" name="license" value="<?php echo $license?>" size="100" style="width: 250px" /><br class="clear"/>
                    <label>Username: </label><input type="text" id="username" name="username" value="<?php echo $username?>"  size="30" /><br class="clear"/>
                    <label>Authorized Domain: </label><input type="text" id="domain" name="domain" value="<?php echo $domain?>"  size="30" /><br class="clear"/>
                    
                    
					<br />
					<br />
					<strong>Please note: </strong>authorized domain name is the domain where script will be used &quot;live&quot;. You can only install 1 copy of our script per each license key you have. You can also install the script on your development server one time with condition to remove it after script will go live on the authorized domain name.<br />
<br />
                    
                    <div class="text_center">
                    
                    <input type="image" name="submit" src="images/new/btn_submit.jpg" value="<?php echo ADM_BTN_SUBMIT;?>" tabindex="2"/>
                	</div>
          <input type="hidden" value="yes" name="install"  />
        </form>
        <?php } ?>
        </div>
    </div>
</div>

<div class="footer">
<a href="http://www.convergine.com" target="_blank"><img src="images/convergine.png" border="0"></a>
</div>
</body>
</html>