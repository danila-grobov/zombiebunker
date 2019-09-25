Payment for order #{%orderId%} was succsessfully procesed {%paymentProcessor%}<br/><br/>

<b>Payment Information</b><br/>
Amount :{%amount%} {%currency%}<br/>
Transaction Id :{%trnID%}<br/>
Payer Name : {%payer_name%}<br/>
Payer Email : {%payer_email%}<br/><br/>

<b>Booking Information</b><br/>

Service name: {%serviceName%}<br/>
Date: {%date%}s<br/>
Times: <table border="1" width="300">
<tr><th bgcolor="#ccc">From</th><th bgcolor="#ccc">To</th><th bgcolor="#ccc">QTY</th></tr>
<?php foreach($times as $k){?>
    <tr><td align="center"><?php echo $k['from']?></td><td align="center"> <?php echo $k['to']?></td><td align="center"><?php echo $k['qty']?></td>
<?php }?>
</table>
