{%name%},
<br><br>
Thank you for your reservation.
<br><br>
Service: {%service%}<br>
Name: {%name%}<br>
Phone: {%phone%}<br>
Email: {%email%}<br>
Comments: <br>{%comments%}<br>
=======================================================
<br>
Confirmation Number: {%orderID%}<br>
Reservation : <br>
<table cellspacing=0 cellpadding=4 border=1>
<tr>
	<th bgcolor="#cccccc">Date From</th>
	<th bgcolor="#cccccc">Date To</th>
	<th bgcolor="#cccccc">Days</th>
	<th bgcolor="#cccccc">Total</th>
</tr>
<?php foreach($summery as $item){?>
<tr>
	<td><?php echo getDateFormat($item['from'])?></td>
	<td><?php echo getDateFormat($item['to'])?></td>
	<td><?php echo getDaysInterval($item['from'], $item['to'])?></td>
        <td><?php echo number_format($item['price'])?></td>
	
</tr>
<?php }?>
</table>
Description: <br>{%dayDescr%}<br>

<br>
<?php if($_payment){?>

Subtotal amount: {%currencyB%}&nbsp;{%subtotal%}&nbsp;{%currencyA%}

<?php if(!empty($discount)){?>
<del>( {%currencyB%}&nbsp;{%_subtotal%}&nbsp;{%currencyA%} )</del><br />
Discount : <?php echo ($discount)?><br />
Coupon Code: {%coupon%}
<?php }?>

<?php if($_taxable){?>
<br />
Tax : {%currencyB%} <strong>{%tax%}</strong> {%currencyA%} ( {%taxRate%}% )<br />

<?php }?>
Total amount to be paid: {%currencyB%}&nbsp;<strong>{%total%}</strong>&nbsp;{%currencyA%}<br />
    <?php if($deposit<1){?>
        Deposit Amount ( <?php echo ($deposit*100)?>% ): {%currencyB%}&nbsp;<strong>{%totalToPay%}</strong>&nbsp;{%currencyA%}<br />
    <?php }?>
    <br />
Your reservation will be processed/confirmed after we will receive your payment.<br />
<?php }?>
Reservation Status:{%status%}

You can easily manage your booking using this {%link%}
<br/><br/><a href="{%google_link%}">Import </a> to Google Calendar
