<table id="ckt" width="620" style="background:#FEF; color:#333;">

<tr><td><h1 style="color:#B8466C; padding:4px; font-family:Arial, Helvetica, sans-serif; margin:5px 0;"><?=__('Buchungsbestätigung')?></h1></td><tr>

<tr><td style="background:#FEF; font-size:16px; font-family:Arial, Helvetica, sans-serif;">
	<div style="padding-left:5px;">
	<table style="color:#666; width:100%; font-family:Arial, Helvetica, sans-serif; border-top:thin solid #666; margin-top:10px;" class="tabelle" cellpadding="5" cellspacing="0">
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
            <?=$b["anrede"] ?></td>    	
        </tr>
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
        <?=$b["vorname"]." ".$b["kunde"]["nachname"];?>
        </td>
        </tr>
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
       <?=$b["adresse"] ?></td>
       </tr>
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
        <?=$b["plz"]." ".$b["kunde"]["ort"];?>
        </td>
        </tr>
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
        <span style="background:#FFF; color:#F60; padding:3px;"><?=$b["kunde"]["email"]?></span>
        </td>
        </tr>    
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
        Tel: <?=$b["telefon"]?>
        </td>
        </tr> 
        <? if($b["fax"]) { ?>
        <tr><td style="background:#FEF; border-bottom:thin solid #FDF;">
        Fax: <?=$b["fax"]?>
        </td>
        </tr>   
        <? } ?>
    </table>
	<?=__('hat folgendes Angebot gebucht'); ?>:
    <br /> <br />
    </div>
<table style="color:#333; font-family:Arial, Helvetica, sans-serif;" class="tabelle" cellpadding="5" cellspacing="0">
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right">
    	<strong>Angebot:</strong></td>    	
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
    <? if($b["gutschein"]) { ?>
    <tr><td style="background:#FEF; border-bottom:thin solid #FDF;"></td>
    <td style="background:#FEF; border-bottom:thin solid #FDF;">
    	<strong>Als Gutschein gebucht!</strong><br />
        <?=$this->Html->link('Zum Gutschein ('.$b['gid'].')', array('action' => 'index', 'controller' => 'coupons', 'full_base' => true, 'addy' => true, '?' => 'filter='.$b['gid'])) ?>
    </td></tr><? } ?>              
    <tr <? if($b["s"] < 4 || $b["pcid"]){ ?>style="display:none"<? } ?>>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><strong>Zimmer:</strong></td>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" id="zimmer">
            <? $j = -1; 
            foreach($this->Mohr->names["z"] as $n) { $j++;                                              
                    if($b[$n]) echo $b[$n]." ".$this->Mohr->get_name("zimmer", $j)."<br />";                       
            } ?>   
        </td>
    </tr>             
    <tr <? if(!$b["personen"]) { ?>style="display:none"<? } ?>><td style="background:#FEF; border-bottom:thin solid #FDF;" align="right"><b id="leute-name">
        <? if($b["pcid"] == 1) echo "Mädels:"; else
        if($b["pcid"] == 2) echo "Kumpels:"; else echo "Personen:" ?>
   </strong></td><td style="background:#FEF; border-bottom:thin solid #FDF;" id="leute"><? if($a["pcid"] == 3 && $b["leute"]) {
       $family_names = array(
       array("Erwachsener", "Kind 6-12 J.", "Kind 0-5 J."),  array("Erwachsene", "Kinder 6-12 J.", "Kinder 0-5 J."));
        $leute = ""; 	
        foreach(array_keys($b["leute"]) as $k) {
            if($b["leute"][$k] > 0) {
                $leute .= $b["leute"][$k]." ";
                $leute .= $b["leute"][$k] > 1 ? $family_names[1][$k] : $family_names[0][$k];
                $leute .= "<br />";	
            }
        }
        echo $leute;
   } else echo $b["personen"]?></td></tr>
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
    <? if($b['date']) : ?>
    <tr>
        <td style="background:#FEF; border-bottom:thin solid #FDF;" align="right">
        Stornobedingungen:</td>
        <td style="background:#FEF; color:#CCC; font-size:12px; border-bottom:thin solid #FDF;">
        <? echo $this->element('storno'); ?>
        </td>
    </tr>
    <? endif; ?>
</table>
</td></tr>
</table>