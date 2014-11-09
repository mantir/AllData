<table id="ckt" width="620" style="background:#FEF; color:#333;">

<tr><td><h1 style="color:#B8466C; padding:4px; font-family:Arial, Helvetica, sans-serif; margin:5px 0;"><?=__('Buchungsbestätigung')?></h1></td><tr>

<tr><td style="background:#FEF; font-size:16px; font-family:Arial, Helvetica, sans-serif;">
	<div style="padding-left:5px;">
	Sehr geehrte<? if($b["anrede"] == "Herr") echo "r "; echo $b["kunde"]["anrede"]." ".$b["kunde"]["vorname"]." ".$b["kunde"]["nachname"]; ?>, <?=__('Sie haben folgendes Angebot gebucht'); ?>:
    <br /> <br />
    </div>
<table style="color:#333; font-family:Arial, Helvetica, sans-serif;" class="tabelle" cellpadding="5" cellspacing="0">
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Angebot:</strong></td>    	
    	<td style="background:#FEF; border-bottom:thin solid #FDF;">
			<table style="color:#333;">
				<tr>
                    <td>
                        <img style="max-width:200px; max-height:200px" alt="" src="<?=$this->Mohr->fullPathImage('../images/'.$a["Image"]['filename'], true)?>" />
                    </td>
                	<td><?=$a["aname"]?><td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Leistungen:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;"><?= $this->Mohr->dotted_list($a["leistungen"], "leistungen")?></td></tr>
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Anbieter:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;"><?= Mohrl::$h["name"]?></td></tr>
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Adresse:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;"><?= Mohrl::$h["adresse"]?></td></tr>
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Ort:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;"><?= Mohrl::$h["plz"]?> <?= Mohrl::$h["ort"]?>, <?= Mohrl::$h["bundesland"]?></td></tr>
    <tr>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Anreisetermin:</strong></td>
        <td style="background:#FEF; border-bottom:thin solid #FDF;"><?= $this->Mohr->get_name("week", date("N", $b["date"]) - 1).", ".date("d.m.Y", $b["date"])?> ab <?= Mohrl::$h["anreise"]?></td>
    </tr>
    <tr>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Abreisetermin:</strong></td>
        <td style="background:#FEF; border-bottom:thin solid #FDF;"><?= $this->Mohr->get_name("week", date("N", $b["end"]) - 1).", ".date("d.m.Y", $b["end"])?> bis <?= Mohrl::$h["abreise"]?></td>
    </tr>
    
    <tr>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Dauer:</strong></td>
        <td style="background:#FEF; border-bottom:thin solid #FDF;"><span nights="<?= $a["nights"]?>" id="nights">
        <?= $b["nights"]?></span>
        <span id="night-name"><? if($b["nights"] > 1) echo "Nächte"; else echo "Nacht"; ?></span>
        </td>
    </tr>          
    <tr <? if($b["s"] < 4 || $b["pcid"]){ ?>style="display:none"<? } ?>>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Zimmer:</strong></td>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" id="zimmer">
            <? $j = -1; 
            foreach($this->Mohr->names["z"] as $n) { $j++;                                              
                    if($b[$n]) echo $b[$n]." ".$this->Mohr->get_name("zimmer", $j)."<br />";                       
            } ?>   
        </td>
    </tr>            
    <tr id="erow" <? if(empty($b["extras"])){?>class="hidden"<? } ?>>
    <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Extras:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;"><div id="extras"><?
        $this->Mohr->template_list("<div>%s x %s = %01.2f €</div>", $b["extras"], array("menge", "ename", "gesamt"));
    ?></div></td></tr>  
    <tr <? if(!$b["properson"]){?>class="hidden"<? } ?>>
    <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Preis pro Person:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;"><span class="price-tag-big" id="properson">
        <?=number_format($b["properson"], 2, ",", " ")?> €</span></td></tr>
    <tr <? if(!$b["insgesamt"]){?>class="hidden"<? } ?>>
    <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Gesamtpreis:</strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;">
        <span id="gesamt"><?=number_format($b["insgesamt"], 2, ",", "")?> €</span></td></tr>
     <? if($b["gutschein"]) { ?>
    <tr>
    	<td style="background:#FEF; border-bottom:thin solid #FDF;"><strong>Als Gutschein gebucht!</strong></td>
        <td style="background:#FEF; border-bottom:thin solid #FDF;">
        	Bitte überweisen Sie den Gesamtbetrag von <strong><?=number_format($b["insgesamt"], 2, ",", "")?> €</strong> auf folgendes Konto:<br /><br />
			<?=__('Inhaber').': '.mohrl::$vars["mohr_konto_inhaber"] ?><br />
            <?=__('Kontonr.').': '.mohrl::$vars["mohr_konto_nummer"] ?><br />
            <?=__('BLZ').': '.mohrl::$vars["mohr_konto_blz"] ?><br />
            <?=__('Bank').': '.mohrl::$vars["mohr_konto_bank"] ?><br />
            <?=__('Verwendungszweck').': '.$b['gid']?><br /><br />
            Sobald die Überweisung bei uns eingegangen ist, senden wir Ihnen umgehend den Gutschein per Email an <strong><?=$b['email']?></strong><? if($b['perpost']) :?> 
            und per Post an Ihre Ihre angegebene Adresse<? endif; ?>.<br />
            <? if($b["bid"]) {?>Ihre Buchungsnummer ist <strong><?= $b["bid"] ?></strong>.<br /><? } ?>
            Die Gutscheinnummer ist <strong><?= $b["gid"] ?></strong><br />
            Bei Anreise muss der Gutschein an der Rezeption vorgelegt werden.<br />
            Bei Problemen oder Fragen zum Gutschein senden Sie uns einfach eine Email an 
            <strong><?=Mohrl::$vars["mohr_email"]?></strong>
            <br />
            Geben Sie dabei bitte die Gutscheinnummer als Referenz an!
        </td>
    
    </tr><? } ?>    
    <? if($b['date']) : ?>
    <tr>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right">Stornobedingungen:</td>
        <td style="background:#FEF; color:#333; font-size:12px; border-bottom:thin solid #FDF;">
        <? echo $this->element('storno'); ?>
        </td>
    </tr>
    <? endif; ?>
</table>
</td></tr>
</table>