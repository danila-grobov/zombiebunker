Dear Administrator,<br /> <br />

This is confirmation of your booking.
<br />Service: {%serviceName%}
<br />Email: {%email%}
<br />Phone: {%phone%}
<br />Comments: {%comments%}
<br />Booking Information: 
<br />
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
</table><br>
{%collect%}
<?php if($_payment){?>



<?php if(!empty($discount)){?>

Subtotal amount: {%currencyB%}&nbsp;{%subtotal%}&nbsp;{%currencyA%}&nbsp;<del>( {%currencyB%}&nbsp;{%_subtotal%}&nbsp;{%currencyA%} )</del><br />
Discount : <?php echo ($discount)?><br />
Coupon Code: {%coupon%}<br />
<?php }else{?>
Subtotal amount: {%currencyB%}&nbsp;{%subtotal%}&nbsp;{%currencyA%}
<?php }?>

<?php if($_taxable){?>

Tax : {%currencyB%} <strong>{%tax%}</strong> {%currencyA%} ( {%taxRate%}% )<br />

<?php }?>
Total amount to be paid: {%currencyB%}&nbsp;<strong>{%total%}</strong>&nbsp;{%currencyA%}<br />
    <?php if($deposit<1){?>
        Deposit Amount ( <?php echo ($deposit*100)?>% ): {%currencyB%}&nbsp;<strong>{%totalToPay%}</strong>&nbsp;{%currencyA%}<br />
    <?php }?>
    <br />
Your reservation will be processed/confirmed after we will receive your payment.<br />
<?php }?>
Reservation Status: {%status%}
<br/>
