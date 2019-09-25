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
   
    $lang = (!empty($_REQUEST["lang"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lang"])) : '';

    $langList = getLangList();





    if (!empty($_REQUEST["edit_settings"]) && $_REQUEST["edit_settings"] == "yes") {


            updateOption('email', $email);
            updateOption('pemail', $pemail);
            updateOption('pcurrency', $pcurrency);
            updateOption('currency', htmlspecialchars($currency));
            updateOption('tax', $tax);
            updateOption('enable_tax', $enable_tax);
            updateOption('time_mode', $time_mode);
            updateOption('use_popup', $use_popup);
            updateOption('lang', $lang);
            updateOption('date_mode', $date_mode);
            updateOption('currency_position', $currencyPos);
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
    $date_mode = getOption('date_mode');
    $currencyPos = getOption('currency_position');
    ?>
    <?php include "includes/admin_header.php"; ?>

    <script type="text/javascript">
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
            
            <form action="bs-settings.php" enctype="multipart/form-data" method="post" name="ff1">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="settings_title">
                        <td height="25" align="right"><strong><?php echo ACC_SETNG?></strong></td>
                        <td height="23">&nbsp; </td>
                    </tr>

                    <tr>
                        <td width="236" height="25" align="right"><?php echo NEWPASS_ADMN?></td>
                        <td width="548" height="23"><input type="password" name="new_pass" id="new_pass" value=""   /></td>
                    </tr>

                    <tr>
                        <td height="25" align="right"><?php echo CNFRM_PASS?></td>
                        <td height="23"><input type="password" name="new_pass2" id="new_pass2" value="" /></td>
                    </tr>

                    <tr>
                        <td width="236" height="25" align="right"><?php echo NOTIF__EMAIL?></td>
                        <td width="548" height="23">
                            <input type="text" name="email" id="email" value="<?php echo $email ?>" /></td>
                    </tr>

                    <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td height="23">&nbsp;</td>
                    </tr>

                    <tr class="settings_title">
                        <td height="25" align="right"><strong><?php echo PYPAL_STNG?></strong></td>
                        <td height="23">&nbsp; </td>
                    </tr>

                    <tr>
                        <td width="236" height="25" align="right"><?php echo TAX_ON?></td>
                        <td width="548" height="23">
                            <input type="checkbox" name="enable_tax" id="enable_tax" value="1" <?php echo $enable_tax ? "checked" : "" ?>/></td>
                    </tr>

                    <tr id="tax" <?php echo $enable_tax ? "" : "style='display:none'" ?>>
                        <td width="236" height="25" align="right"><?php echo TAX?></td>
                        <td width="548" height="23">
                            <input type="text" onkeyup="noAlpha(this)" name="tax" id="tax" value="<?php echo $tax ?>" />&nbsp;%</td>
                    </tr>



                    <tr>
                        <td width="236" height="25" align="right"><?php echo PAYPAL_EMAIL?></td>
                        <td width="548" height="23">
                            <input type="text" name="pemail" id="pemail" value="<?php echo $pemail ?>" /></td>
                    </tr>

                    <tr>
                        <td width="236" height="25" align="right"><?php echo PAYPAL_CURRN?></td>
                        <td width="548" height="23"><select name="pcurrency">

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
                            <!--(paypal supported currencies only)-->
                        </td>
                    </tr>

                    <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td height="23">&nbsp;</td>
                    </tr>




                    <tr class="settings_title">
                        <td height="25" align="right"><strong><?php echo DIPL_SETTNG?></strong></td>
                        <td height="23">&nbsp; </td>
                    </tr>

                    <tr>
                        <td height="25" align="right" valign="top"><?php echo TIME_MODE?></td>
                        <td height="23"><input type="radio" name="time_mode" id="time_mode" value="0" <?php echo $time_mode == "0" ? "checked" : "" ?> /> 24h <br />
                            <input type="radio" name="time_mode" id="time_mode" value="1" <?php echo $time_mode == "1" ? "checked" : "" ?>/> 12h</td>
                    </tr>

                    <tr>
                        <td height="25" align="right" valign="top"><?php echo DATE_FORMT?></td>
                        <td height="23">
                            <select name="date_mode">
                                <option value="Y-m-d" <?php echo $date_mode == 'Y-m-d' ? "selected='selected'" : "" ?>><?php echo date("Y-m-d") ?></option>
                                <option value="F d,Y" <?php echo $date_mode == 'F d,Y' ? "selected='selected'" : "" ?>><?php echo date("F d,Y") ?></option>
                                <option value="M d,Y" <?php echo $date_mode == 'M d,Y' ? "selected='selected'" : "" ?>><?php echo date("M d,Y") ?></option>
                                <option value="m-d-Y" <?php echo $date_mode == 'm-d-Y' ? "selected='selected'" : "" ?>><?php echo date("m-d-Y") ?></option>
                                <option value="d F Y" <?php echo $date_mode == 'd F Y' ? "selected='selected'" : "" ?>><?php echo date("d F Y") ?></option>
                                <option value="d M Y" <?php echo $date_mode == 'd M Y' ? "selected='selected'" : "" ?>><?php echo date("d M Y") ?></option>
                                <option value="d-m-Y" <?php echo $date_mode == 'd-m-Y' ? "selected='selected'" : "" ?>><?php echo date("d-m-Y") ?></option>
                            </select>
                        </td>
                    </tr>
                    <?php if(getOption('is_word_press') !='1'){ ?>
                    <tr>
                        <td height="25" align="right" valign="top"><?php echo POPUP_MSG_BOOK?></td>
                        <td height="23"><input type="radio" name="use_popup" id="use_popup" value="0" <?php echo $use_popup == "0" ? "checked" : "" ?> /> <?php echo NO?> <br />
                            <input type="radio" name="use_popup" id="use_popup" value="1" <?php echo $use_popup == "1" ? "checked" : "" ?>/> <?php echo YES?></td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td width="236" height="25" align="right" valign="top"><?php echo CURNT_POS?></td>
                        <td width="548" height="23"> 
                            <input type="radio" name="currencyPos" id="currencyPos" value="a" <?php echo $currencyPos=='a'?"checked":"" ?> />After Amount<br>
                            <input type="radio" name="currencyPos" id="currencyPos" value="b" <?php echo $currencyPos=='b'?"checked":"" ?> />Before Amount
                        </td>
                    </tr>
                    <tr>
                        <td width="236" height="25" align="right"><?php echo CURNT_SYMBL?></td>
                        <td width="548" height="23"> <input type="text" name="currency" id="currency" value="<?php echo $currency ?>" /></td>
                    </tr>
                    <tr>
                        <td width="236" height="25" align="right"><?echo LANG?></td>
                        <td width="548" height="23"><select name="lang">
                                <?php foreach ($langList as $item) { ?>
                                    <option value="<?php echo $item ?>" <?php echo $lang == $item ? "selected" : "" ?>><?php echo $item ?></option>
                                <?php } ?>

                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td height="23">&nbsp;</td>
                    </tr>

                    <?php echo $plugin_list; ?>

                    <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td height="32">
                            <input type="submit" name="create" id="create" value="<?php echo BTN_SUBMITCHANGES;?>" />
                            <input value="yes" name="edit_settings" type="hidden" />
                        </td>
                    </tr>
                </table>
            </form>



        </div>
        <?php include "includes/admin_footer.php"; ?>
    <?php } ?>