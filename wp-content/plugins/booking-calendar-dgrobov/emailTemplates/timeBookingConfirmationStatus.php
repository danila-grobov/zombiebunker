{%name%},<br /> <br /> 
This is confirmation of your booking.
<br />
<table cellspacing=0 cellpadding=4 border=1>
<tr>
	<th bgcolor="#cccccc"><?php echo TBL_DATE?></th>
	<th bgcolor="#cccccc"><?php echo TBL_TIME1?></th>
	<th bgcolor="#cccccc"><?php echo TBL_TIME2?></th>
	<th bgcolor="#cccccc"><?php echo TBL_QTY?></th>
</tr>
<?php foreach($_info as $item){?>
<tr>
	<td><?php echo $item['date']?></td>
	<td><?php echo $item['timeFrom']?></td>
	<td><?php echo $item['timeTo']?></td>
	<td><?php echo $item['qty']?></td>
	
</tr>
<?php }?>
</table>
Reservation Status: {%status%}
<br/>
