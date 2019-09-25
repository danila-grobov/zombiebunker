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
######################### DO NOT MODIFY (UNLESS SURE) ########################


require_once("includes/config.php"); //Load the configurations

include "includes/plugins_grid.php";


if ($_SESSION["logged_in"] != true) {
    header("Location: admin.php");
} else {

    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    //bw_dump($BW_actions);
    ######################### DO NOT MODIFY (UNLESS SURE) END ########################

    $email = (!empty($_REQUEST["email"])) ? strip_tags(str_replace("'", "`", $_REQUEST["email"])) : '';
    $pemail = (!empty($_REQUEST["pemail"])) ? strip_tags(str_replace("'", "`", $_REQUEST["pemail"])) : '';
    $pcurrency = (!empty($_REQUEST["pcurrency"])) ? strip_tags(str_replace("'", "`", $_REQUEST["pcurrency"])) : '';
    $currency = (!empty($_REQUEST["currency"])) ? strip_tags(str_replace("'", "`", $_REQUEST["currency"])) : '';
    $currencyPos = (isset($_REQUEST["currencyPos"])) ? strip_tags(str_replace("'", "`", $_REQUEST["currencyPos"])) : 'a';
     
    $tax = (!empty($_REQUEST["tax"])) ? strip_tags(str_replace("'", "`", $_REQUEST["tax"])) : '';
    $enable_tax = (isset($_REQUEST["enable_tax"])) ? strip_tags(str_replace("'", "`", $_REQUEST["enable_tax"])) : '0';

    $new_pass = (!empty($_REQUEST["new_pass"])) ? strip_tags(str_replace("'", "`", $_REQUEST["new_pass"])) : '';
    $new_pass2 = (!empty($_REQUEST["new_pass2"])) ? strip_tags(str_replace("'", "`", $_REQUEST["new_pass2"])) : '';

    $use_popup = (isset($_REQUEST["use_popup"])) ? strip_tags(str_replace("'", "`", $_REQUEST["use_popup"])) : '0';
    $time_mode = (isset($_REQUEST["time_mode"])) ? strip_tags(str_replace("'", "`", $_REQUEST["time_mode"])) : 0;
    $date_mode = (isset($_REQUEST["date_mode"])) ? strip_tags(str_replace("'", "`", $_REQUEST["date_mode"])) : '';
    $timezone = (isset($_REQUEST["timezone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["timezone"])) : '';
    $lang = (isset($_REQUEST["lang"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lang"])) : 'english';
    $language_switch = (isset($_REQUEST["language_switch"])) ? strip_tags(str_replace("'", "`", $_REQUEST["language_switch"])) : 0;

    $multi_day_notification = (isset($_REQUEST["multi_day_notification"])) ?intval( $_REQUEST["multi_day_notification"]) : 0;
    $single_day_notification = (isset($_REQUEST["single_day_notification"])) ?intval( $_REQUEST["single_day_notification"]) : 0;
    $event_notification = (isset($_REQUEST["event_notification"])) ?intval($_REQUEST["event_notification"]) : 0;

    $multi_day_notification_on = (isset($_REQUEST["multi_day_notification_on"])) ?addslashes( $_REQUEST["multi_day_notification_on"]) : 'n';
    $single_day_notification_on = (isset($_REQUEST["single_day_notification_on"])) ?addslashes( $_REQUEST["single_day_notification_on"]) : 'n';
    $event_notification_on = (isset($_REQUEST["event_notification_on"])) ?addslashes($_REQUEST["event_notification_on"]) : 'n';

    $cron_type = (isset($_REQUEST["cron_type"])) ? strip_tags(str_replace("'", "`", $_REQUEST["cron_type"])) : 'cron';


    $langList = getLangList();

    $timezonesList = getTimeZonesList();
    //bw_dump($timezonesList);

    

    if (!empty($_REQUEST["edit_settings"]) && $_REQUEST["edit_settings"] == "yes") {


            updateOption('email', $email);
            updateOption('pemail', $pemail);
            updateOption('pcurrency', $pcurrency);
            updateOption('currency', htmlspecialchars($currency));

            updateOption('time_mode', $time_mode);
            updateOption('use_popup', $use_popup);
            updateOption('date_mode', $date_mode);
            updateOption('currency_position', $currencyPos);

            updateOption('multi_day_notification', $multi_day_notification);
            updateOption('single_day_notification', $single_day_notification);
            updateOption('event_notification', $event_notification);

            updateOption('multi_day_notification_on', $multi_day_notification_on);
            updateOption('single_day_notification_on', $single_day_notification_on);
            updateOption('event_notification_on', $event_notification_on);
            updateOption('language_switch',$language_switch);

            updateOption('cron_type', $cron_type);

            if($enable_tax == 1 && empty($tax)){
                addMessage(MSG_BLANK_TAX,"warning");
            }else{
                updateOption('tax', doubleval($tax));
                updateOption('enable_tax', $enable_tax);
            }

            addMessage(MSG_SETSAVED,"success");
            
        
        if (!empty($new_pass) && !empty($new_pass2)) {
            if (md5($new_pass) == md5($new_pass2)) {

                if ($demo) {
                    $msg.="<span style='color:#aa0000'>".DEMO_PASS_MSG."</span>";
                } else {
                    
                    updateOption('password', md5($new_pass));
                    addMessage(MSG_ADMPSCHG,"success");
                    //$msg.="<span style='color:#00aa00'> Administrator password was changed!</span>";
                }
            } else {
                addMessage(MSG_PSDNTMTCH,"warning");
                $msg.="<span style='color:#00aa00'>".PASS_NOMATCH."</span>";
            }
        }
    }

//print $months;
    $email = getOption('email');
    $pemail = getOption('pemail');
    $pcurrency = getOption('pcurrency');
    $currency = getOption('currency');
    $tax = getOption('tax');
    $enable_tax = getOption('enable_tax');
    $time_mode = getOption('time_mode');
    $use_popup = getOption('use_popup');
    $lang = getOption('lang');
    $language_switch = getOption('language_switch');
    $date_mode = getOption('date_mode');
    $currencyPos = getOption('currency_position');
    $timezone = getOption('timezone')===false?date_default_timezone_get():getOption('timezone');

    $multi_day_notification = getOption('multi_day_notification');
    $single_day_notification = getOption('single_day_notification');
    $event_notification = getOption('event_notification');

    $multi_day_notification_on = getOption('multi_day_notification_on');
    $single_day_notification_on = getOption('single_day_notification_on');
    $event_notification_on = getOption('event_notification_on');

    $cron_type = getOption('cron_type');

    
    
    ?>
    <?php include "includes/admin_header.php"; ?>

    <script type="text/javascript" xmlns="http://www.w3.org/1999/html">
        $(function() {

            $('#enable_tax').bind('change',function(){
    	
                if($(this).is(':checked')){
                    $('#tax').show();
                }else{
                    $('#tax').hide();
                }
            })
        });
        function noAlpha(obj){
            reg = /[^0-9.,]/g;
            obj.value =  obj.value.replace(reg,"");
        }	
    </script>
    <div id="content">
        


         

<?php  getMessages(); ?>
        <div class="content_block">
            <h2><?php echo SCRP_SETNG?></h2>
           <p><?php echo BS_SETTINGS_DESCR?></p>
<hr/>
            <form action="bs-settings.php" enctype="multipart/form-data" method="post" name="ff1">
                <div class="form-row">
                        <h3><?php echo ACC_SETNG ?></h3>
                        <div class="cell third">
                            <label><?php echo NEWPASS_ADMN ?></label>
                            <input type="password" name="new_pass" id="new_pass" value=""   />
                        </div>
                        <div class="cell third">
                            <label><?php echo CNFRM_PASS ?></label>
                            <input type="password" name="new_pass2" id="new_pass2" value="" />
                        </div>
                        <div class="cell third">
                            <label><?php echo NOTIF__EMAIL ?></label>
                            <input type="text" name="email" id="email" value="<?php echo $email ?>" />
                        </div>
                        <div class="clear"></div>
                    </div>
                    <hr/>
                    <div class="form-row">
                        <h3><?php echo PYPAL_STNG ?></h3>
                        <div class="cell third">
                            <label><?php echo PAYPAL_EMAIL ?></label>
                            <input type="text" name="pemail" id="pemail" value="<?php echo $pemail ?>" />
                        </div>
                        <div class="cell third">
                            <label><?php echo PAYPAL_CURRN ?></label>
                            <select name="pcurrency" class="select ">
                                
                                <option value="AUD" <?php echo $pcurrency == "AUD" ? "selected" : "" ?>>Australian Dollar       (AUD)</option>
                                <option value="GBP" <?php echo $pcurrency == "GBP" ? "selected" : "" ?>>British Pound       (GBP)</option>
                                <option value="BRL" <?php echo $pcurrency == "BRL" ? "selected" : "" ?>>Brazil Real       (BRL)</option>
                                <option value="CAD" <?php echo $pcurrency == "CAD" ? "selected" : "" ?>>Canadian Dollar       (CAD)</option>
                                <option value="CHF" <?php echo $pcurrency == "CHF" ? "selected" : "" ?>>Swiss Franc       (CHF)</option>   	          		
                                <option value="CZK" <?php echo $pcurrency == "CZK" ? "selected" : "" ?>>Czech Koruna       (CZK)</option>
                                <option value="DKK" <?php echo $pcurrency == "DKK" ? "selected" : "" ?>>Danish Krone       (DKK)</option>
                                <option value="EUR" <?php echo $pcurrency == "EUR" ? "selected" : "" ?>>European Euro       (EUR)</option>
                                <option value="HKD" <?php echo $pcurrency == "HKD" ? "selected" : "" ?>>Hong Kong Dollar       (HKD)</option>
                                <option value="HUF" <?php echo $pcurrency == "HUF" ? "selected" : "" ?>>Hungarian Forint       (HUF)</option>
                                <option value="ILS" <?php echo $pcurrency == "ILS" ? "selected" : "" ?>>Israeli Shekel       (ILS)</option>
                                <option value="JPY" <?php echo $pcurrency == "JPY" ? "selected" : "" ?>>Japanese Yen       (JPY)</option>
                                <option value="MXN" <?php echo $pcurrency == "MXN" ? "selected" : "" ?>>Mexican pesos       (MXN)</option>
                                <option value="MYR" <?php echo $pcurrency == "MYR" ? "selected" : "" ?>>Malaysian Ringgit       (MYR)</option>
                                <option value="NOK" <?php echo $pcurrency == "NOK" ? "selected" : "" ?>>Norwegian Krone       (NOK)</option>
                                <option value="NZD" <?php echo $pcurrency == "NZD" ? "selected" : "" ?>>New Zealand Dollar       (NZD)</option>
                                <option value="PHP" <?php echo $pcurrency == "PHP" ? "selected" : "" ?>>Philippines Peso       (PHP)</option>
                                <option value="PLN" <?php echo $pcurrency == "PLN" ? "selected" : "" ?>>Polish zloty       (PLN)</option>
                                <option value="SEK" <?php echo $pcurrency == "SEK" ? "selected" : "" ?>>Swedish Krona       (SEK)</option>
                                <option value="SGD" <?php echo $pcurrency == "SGD" ? "selected" : "" ?>>Singapore Dollar       (SGD)</option>
                                <option value="THB" <?php echo $pcurrency == "THB" ? "selected" : "" ?>>Thai Baht       (THB)</option>
                                <option value="TWD" <?php echo $pcurrency == "TWD" ? "selected" : "" ?>>Taiwan Dollar       (TWD)</option>
                                <option value="USD" <?php echo $pcurrency == "USD" ? "selected" : "" ?>>United States Dollar       (USD)</option>
                                    
                            </select> 
                        </div>
                        <div class="cell short1">
                            <label>&nbsp;</label>
                            <div class="valign">
                                
                            <input type="checkbox" name="enable_tax" id="enable_tax" value="1" <?php echo $enable_tax ? "checked" : "" ?>/>&nbsp;
                            <?php echo TAX_ON ?>
                            </div>
                        </div>
                         <div class="cell short1" <?php echo $enable_tax ? "" : "style='display:none'" ?> id="tax">
                            
                                <label><?php echo TAX?></label>
                            <input type="text" onkeyup="noAlpha(this)" name="tax" class="smaller" id="tax" value="<?php echo $tax ?>" />&nbsp;%
                            
                        </div>
                        <div class="clear"></div>
                    </div>
                <hr/>
                <div class="form-row">
                    <h3><?php echo DIPL_SETTNG?></h3>
                    <div class="cell third">
                        <label><?php echo TIME_MODE?></label>
                        <div class="valign">
                        <input type="radio" name="time_mode" id="time_mode" value="0" <?php echo $time_mode == "0" ? "checked" : "" ?> /> 24h 
                        <input type="radio" name="time_mode" id="time_mode" value="1" <?php echo $time_mode == "1" ? "checked" : "" ?>/> 12h
                        </div>
                    </div>
                     <div class="cell third">
                        <label><?php echo DATE_FORMT?></label>
                        <select name="date_mode" class="select">
                                <option value="Y-m-d" <?php echo $date_mode == 'Y-m-d' ? "selected='selected'" : "" ?>><?php echo date("Y-m-d") ?></option>
                                <option value="F d,Y" <?php echo $date_mode == 'F d, Y' ? "selected='selected'" : "" ?>><?php echo date("F d,Y") ?></option>
                                <option value="M d,Y" <?php echo $date_mode == 'M d,Y' ? "selected='selected'" : "" ?>><?php echo date("M d,Y") ?></option>
                                <option value="m-d-Y" <?php echo $date_mode == 'm-d-Y' ? "selected='selected'" : "" ?>><?php echo date("m-d-Y") ?></option>
                                <option value="d F Y" <?php echo $date_mode == 'd F Y' ? "selected='selected'" : "" ?>><?php echo date("d F Y") ?></option>
                                <option value="d M Y" <?php echo $date_mode == 'd M Y' ? "selected='selected'" : "" ?>><?php echo date("d M Y") ?></option>
                                <option value="d-m-Y" <?php echo $date_mode == 'd-m-Y' ? "selected='selected'" : "" ?>><?php echo date("d-m-Y") ?></option>
                            </select>
                    </div>

                    <div class="cell third">
                        <label><?php echo POPUP_MSG_BOOK?></label>
                        <div class="valign">
                       <input type="radio" name="use_popup" id="use_popup" value="0" <?php echo $use_popup == "0" ? "checked" : "" ?> /> <?php echo NO?> 
                            <input type="radio" name="use_popup" id="use_popup" value="1" <?php echo $use_popup == "1" ? "checked" : "" ?>/> <?php echo YES?>
                        </div>
                    </div>

                            <div class="clear"></div>
                </div>
                <div class="form-row">
                    <div class="cell third">
                        <label><?php echo CURNT_SYMBL?></label>
                        <input type="text" class="smaller" name="currency" id="currency" value="<?php echo $currency ?>" />
                    </div>
                    <div class="cell third adj_currency">
                        <label><?php echo CURNT_POS?></label>
                     <input type="radio" name="currencyPos" id="currencyPos" value="b" <?php echo $currencyPos=='b'?"checked":"" ?> />&#36; <span style="color:#999">XXX</span>&nbsp;
                            <input type="radio" name="currencyPos" id="currencyPos" value="a" <?php echo $currencyPos=='a'?"checked":"" ?> /><span style="color:#999">XXX</span> &#36;
                    </div>

                    <div class="clear"></div>
                </div>
                
                <hr/>

                <div class="form-row">
                    <h3><?php echo LANGUAGE_SETTINGS?></h3>
                    <div class="cell third">

                        <label><?php echo LANG ?></label>
                        <select name="lang" class="select">
                            <?php foreach ($langList as $item) { ?>
                                <option value="<?php echo $item ?>" <?php echo $lang == $item ? "selected" : "" ?>><?php echo $item ?></option>
                            <?php } ?>

                        </select>
                    </div>
                    <div class="cell third">
                        <label><?php echo LANGUAGE_SWITCHING?></label>
                        <div class="valign">
                            <input type="radio" name="language_switch" id="language_switch" value="0" <?php echo $language_switch == "0" ? "checked" : "" ?> /> <?php echo NO?>
                            <input type="radio" name="language_switch" id="language_switch" value="1" <?php echo $language_switch == "1" ? "checked" : "" ?>/> <?php echo YES?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <hr/>
                
                <div class="form-row">
                        <h3><?php echo TIMEZONES ?></h3>
                        <div class="cell ">
                            <label><?php echo TIMEZONE_SET ?></label>
                            <select name="timezone" class="select long">
                                <?php foreach($timezonesList as $k=>$v){?>
                                <option value="<?php echo $k?>" <?php echo $timezone == $k ? "selected='selected'" : "" ?>><?php echo $v ?></option>
                                <?php }?>
                            </select>
                        </div>
                        
                        <div class="clear"></div>
                    </div>

                <hr/>

                <div class="form-row">
                    <h3><?php echo REMINDER ?></h3>
                    <div class="cell">
                    <label><strong><?php echo MULTI_DAY_NOTIFICATIONS ?></strong></label>
                        &nbsp;&nbsp;&nbsp;<?php echo SEND_EMAIL?> <input type="text"  onkeyup="noAlpha(this)" name="multi_day_notification" value="<?php echo $multi_day_notification?>" class="smaller"> <?php echo HOURS_BEFORE_BOOKING?><br>
                        &nbsp;&nbsp;&nbsp;<input type="radio" name="multi_day_notification_on" value="y" <?php echo $multi_day_notification_on=='y'?"checked":""?>>&nbsp;<?php echo ENABLED?>&nbsp;&nbsp;
                        <input type="radio" name="multi_day_notification_on" value="n" <?php echo $multi_day_notification_on=='n'?"checked":""?>>&nbsp;<?php echo DISABLED?>
                    </div>
                    <div class="clear"></div>
                    <hr>
                    <div class="cell">
                        <label><strong><?php echo SINGLE_DAY_NOTIFICATIONS ?></strong></label>
                        &nbsp;&nbsp;&nbsp;<?php echo SEND_EMAIL?> <input type="text"  onkeyup="noAlpha(this)" name="single_day_notification" value="<?php echo $single_day_notification?>" class="smaller"> <?php echo HOURS_BEFORE_APPOINTMENT?><br>
                        &nbsp;&nbsp;&nbsp;<input type="radio" name="single_day_notification_on" value="y" <?php echo $single_day_notification_on=='y'?"checked":""?>>&nbsp;<?php echo ENABLED?>&nbsp;&nbsp;
                        <input type="radio" name="single_day_notification_on" value="n" <?php echo $single_day_notification_on=='n'?"checked":""?>>&nbsp;<?php echo DISABLED?>
                    </div>
                    <div class="clear"></div>
                    <hr>
                    <div class="cell">
                        <label><strong><?php echo EVENT_NOTIFICATIONS ?></strong></label>
                        &nbsp;&nbsp;&nbsp;<?php echo SEND_EMAIL?> <input type="text"  onkeyup="noAlpha(this)" name="event_notification" value="<?php echo $event_notification?>" class="smaller"> <?php echo HOURS_BEFORE_EVENT?><br>
                        &nbsp;&nbsp;&nbsp;<input type="radio" name="event_notification_on" value="y" <?php echo $event_notification_on=='y'?"checked":""?>>&nbsp;<?php echo ENABLED?>&nbsp;&nbsp;
                        <input type="radio" name="event_notification_on" value="n" <?php echo $event_notification_on=='n'?"checked":""?>>&nbsp;<?php echo DISABLED?>
                    </div>
                    <div class="clear"></div>
                    <hr>
                    <label><strong><?php echo CRON_FOR_EMAIL_NOTIFICATIONS?></strong>:</label>
                    <div class="cell" style="float: none">
                        <input type="radio" name="cron_type" value="cron" <?php echo $cron_type=='cron'?"checked":""?>>&nbsp; <?php echo USE_CRON_TAB?><input type="text" style="height: 20px" value="wget <?php echo ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?"https://":"http://").$_SERVER['HTTP_HOST'].$baseDir."cron.php"?>" onfocus="this.select()"/>
                        <img src='images/info.png' border="0"  class=" tipTip imgCenter" title="<?php echo CRON_TAB_DESCRIPTION?>"/>
                         <br>
                        <input type="radio" name="cron_type" value="alt" <?php echo $cron_type=='alt'?"checked":""?>>&nbsp; <?php echo USE_ALTERNATIVE_CRON?>
                        <img src='images/info.png' border="0"  class=" tipTip imgCenter" title="<?php echo ALTERNATIVE_CRON_DESCRIPTION?>"/>
                    </div>
                </div>
                <hr/>
                
                
                      <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT;?></span></button>
                       <input value="yes" name="edit_settings" type="hidden" />

                <div class="clear"></div>
                
            </form>
            


        </div>
        <?php include "includes/admin_footer.php"; ?>
    <?php } ?>