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

	$week =(isset($_REQUEST["week"]))?strip_tags(str_replace("'","`",$_REQUEST["week"])):'';

					$step = 15;

?>						
						<div class="row_a">
						    <span class="left lh30">From&nbsp;&nbsp;</span>
						    <div class="adj_se"><input type="text" name="week_from_h_<?php echo $week;?>[]" value="  - -" class="adjStartEnd adj_hrs_0"><div class="adj_se_input_bg" style="width:32px;height:27px;padding:1px 20px 1px 5px;"><span class="adj_se_nav_top"></span><span class="adj_se_nav_bottom"></span></div></div>
						    <span class="left lh30">&nbsp;:&nbsp;</span>
						    <div class="adj_se"><input type="text" name="week_from_m_<?php echo $week;?>[]" value="  - -" class="adjStartEnd adj_mins_0"><div class="adj_se_input_bg" style="width:32px;height:27px;padding:1px 20px 1px 5px;"><span class="adj_se_nav_top"></span><span class="adj_se_nav_bottom"></span></div></div>
						    <span class="left lh30">&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;</span>
						    <div class="adj_se"><input type="text" name="week_to_h_<?php echo $week;?>[]" value="  - -" class="adjStartEnd adj_hrs_0"><div class="adj_se_input_bg" style="width:32px;height:27px;padding:1px 20px 1px 5px;"><span class="adj_se_nav_top"></span><span class="adj_se_nav_bottom"></span></div></div>
						    <span class="left lh30">&nbsp;:&nbsp;</span>
						    <div class="adj_se"><input type="text" name="week_to_m_<?php echo $week;?>[]" value="  - -" class="adjStartEnd adj_mins_0"><div class="adj_se_input_bg" style="width:32px;height:27px;padding:1px 20px 1px 5px;"><span class="adj_se_nav_top"></span><span class="adj_se_nav_bottom"></span></div></div>
							<a href="#" class="adj_SE_remove" >delete this</a>
						</div>
					