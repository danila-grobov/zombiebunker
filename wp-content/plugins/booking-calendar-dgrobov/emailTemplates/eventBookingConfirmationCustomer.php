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
Event name: {%eventName%}<br>
Event date: {%eventDate%}<br>
Ticket Quantity: {%qty%}<br><br>
Event description: {%eventDescr%}<br>
Event location: <a href="{%eventMapLink%}">{%eventLocation%}</a><br>

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
Tax : {%currencyB%} {%tax%} {%currencyA%} ( {%taxRate%}% )<br />

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
<br /><br />
<a href="{%google_link%}">Import event</a> to Google Calendar
