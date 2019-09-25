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
session_start();
require_once("includes/dbconnect.php"); //Load the settings
require_once("includes/config.php"); //Load the functions
$msg = "";

//RETRIEVE VARIABLES
$username = (!empty($_REQUEST['username'])) ? strip_tags(str_replace("'", "`", $_REQUEST['username'])) : '';
$email = (!empty($_REQUEST['email'])) ? strip_tags(str_replace("'", "`", $_REQUEST['email'])) : '';

// RETRIEVE
if (!empty($_REQUEST["restore"]) && $_REQUEST['restore'] == "yes") {
    if ($username == "" || $email == "") {
        $msg = WRONG_USERNAME2;
        addMessage(LOGIN_ERROR2, "error");
    } else {



        if (getOption("pemail") !== false && getOption("password") !== false) {


            if ($email != getOption("pemail")) {
                
                addMessage(WRONG_EMAIL2, "error");
                //addLog($row["id"],"Error during password retrieving. Wrong email.");
            } elseif ($username != getOption("email")) {
                
                addMessage(WRONG_EMAIL, "error");
            } else {

                getOption("password");

                $newPass = randomPassword();

                if ($demo === false) {

                    updateOption("password", md5($newPass));
                    //creating message for sending
                    
                    $data = array(
                        "{%username%}"=>getOption("username"),
                        "{%password%}"=>$newPass
                    );
                   
                    sendMail(getOption("email"),$subject,"PasswordRetrieval.php",1,$data);
                   
                    addMessage(NEW_PASS_SENT, "success");
                    
                    //addLog($row["id"],"Successfully reset password.");
                } else {
                    
                    addMessage(NEW_PASS_SENT, "error");
                }
            }
        } else {
            
            addMessage(WRONG_USERNAME, "error");
        }
    }
}

if ($_SESSION["logged_in"] == true) {
    header("Location: admin-index.php");
    exit();
} else {
    ?>
    <?php include "includes/admin_header.php"; ?>
    <div id="content">
        <div style="width: 500px;margin: 0 auto;padding-bottom: 5px">
    <?php getMessages(); ?>
        </div>
        <div class="login_container"> 
            <h3><?php echo MSG_BSFORGOT_TITLE; ?></h3>
            <?php echo "<span style='color:#ff0000'>" . $msg . "</span>"; ?>
            <div class="login">

                <form method="post" action="forgot.php" enctype="multipart/form-data"  name="ff1">

                    <div class="line">
                        <label>Notification Email: </label><input type="text" id="username" name="username" size="30" />
                    </div>
                    <div class="line">
                        <label>Paypal Merchant Email: </label><input type="text" id="email" name="email"  size="30" />
                    </div>
                    <div class="line">
                        <a href="admin.php">Login?</a>
                    </div>
                    <center>
                        <button class="save" type="submit"><span><?php echo ADM_BTN_FORGOT;?></span></button>
                    </center>		
                    <input type="hidden" value="yes" name="restore"  />
                </form>

            </div>
        </div>

    </div>
    <?php include "includes/admin_footer.php"; ?>

<?php } ?>