Sveiki, {%name%},<br /> <br /> Ačiū už rezervaciją.<br> 
Jūsų rezervacijos info: <br />
<br />Pramoga: : {%serviceName%}
<br />El. paštas: {%email%}
<br />Tel. nr: {%phone%}
<br />Komentarai administratoriui: {%comments%}
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
<?php if($_payment){?>


    <?php if(!empty($discount)){?>

        Suma: {%currencyB%}&nbsp;{%subtotal%}&nbsp;{%currencyA%}&nbsp;<del>( {%currencyB%}&nbsp;{%_subtotal%}&nbsp;{%currencyA%} )</del><br />
        Nuolaida : <?php echo ($discount)?><br />
        Kupono kodas: {%coupon%}<br />
    <?php }else{?>
        Viso: {%currencyB%}&nbsp;{%subtotal%}&nbsp;{%currencyA%}
    <?php }?>

   
    <br />
Jūsų rezervacija patvirtinta. Jei kas pasikeistų - būtinai su mumis susisiekite</br>  
Zombie Bunker Vilnius:  info@zombiebunker.lt,  +370 692 53676. <br />
<br />
Atvykimas:<br />
Taikos g. 1, Kreivalauziu km, Nemencines sen. Kelio Vilnius-Pabradė 22km, EMSI degalinė (kairėje, vaziuojant is Vilniaus) pries pat Nemencine. Sustokite aikšteleje ir laukite vado iš Zombių bunkerio.<br />
Žemėlapis: https://goo.gl/0JyucF<br />
<br />
<br />
Paslaugų apmokėjimas: grynais arba bankiniu pavedimu (iš anksto). Perspėkite, jei reikalinga sąskaita - ją rasite atvykę į Zombių bunkerį.<br />
Dėl jūsų pačių saugumo, neblaiviems asmenims dalyvauti žaidime yra griežtai draudžiama, todėl administratorius turi teisę savo nuožiūra nuspręsti ar žmogus gali dalyvauti žaidime ar ne. <br />

.<br />
<?php }?>

<br/><br/><a href="{%google_link%}">Import </a> to Google Calendar




----------------------------

Dear {%name%},<br /> <br /> Thank you for your booking.<br> 
Here is your booking information: <br />
<br />Service: {%serviceName%}
<br />Email: {%email%}
<br />Phone: {%phone%}
<br />Comments: {%comments%}
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
Reservation is confirmed. Please contact us </br>
Zombie Bunker Vilnius:  info@zombiebunker.lt,  +370 692 53676.<br />
<br />
Payment types: cash or pre-payment via bank<br />
 Please call us if you get lost. <br>
Map for arrival: https://goo.gl/0JyucF<br />
<br />


<?php }?>
<br/><br/><a href="{%google_link%}">Import </a> to Google Calendar