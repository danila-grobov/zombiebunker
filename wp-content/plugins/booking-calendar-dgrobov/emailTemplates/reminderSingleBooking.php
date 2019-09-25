Sveiki {%name%},<br /> <br />
Pirmenu, kad Jūsų laukiame Zombių bunkeryje - Zombie Bunker Vilnius: <br />
<br />Order #: {%orderID%}
<br />Service: {%serviceName%}

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

Jie neatvyksite būtinai perspėkite mus: info@zombiebunker.lt arba +370 684 49944. <br>

Atvykimas:<br>
Taikos g. 1, Kreivalauziu km, Nemencines sen. Kelio Vilnius-Pabradė 22km, EMSI degalinė (kairėje, vaziuojant is Vilniaus) pries pat Nemencine. Sustokite aikšteleje ir laukite vado iš Zombių bunkerio.
Žemėlapis: https://goo.gl/0JyucF


--------------------

Dear {%name%},<br /> <br />
Here is your booking information: <br />
<br />Order #: {%orderID%}
<br />Service: {%serviceName%}

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
