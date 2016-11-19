
<div id="ie5menu" class="menuclickdroit" onmouseover="highlightie5(event,'#663300','#FFFFFF');" onmouseout="lowlightie5(event,'','#000000');" onclick="jumptoie5(event);" style="display:block; visibility:hidden;">
	<div id="menuitemsJSTop" style="display:none;"></div>
	<div class="menuitems" onclick="alert('Afin de copier du texte, veuillez pressez simultan&eacute;nement les touches Ctrl et C de votre clavier.');" title="Copier du texte">Copier</div>
	<div class="menuitems" onclick="alert('Afin de coller du texte, veuillez pressez simultan&eacute;nement les touches Ctrl et V de votre clavier.');" title="Coller du texte">Coller</div>
	<div class="menuitems" onclick="alert('Afin de couper du texte, veuillez pressez simultan&eacute;nement les touches Ctrl et X de votre clavier.');" title="Couper du texte">Couper</div>
	<hr/>
	<div class="menuitems" onclick="window.location='index.php';" title="Votre page">Page d'acceuil</div>
	<div class="menuitems" onclick="history.back();" title="Page pr&eacute;c&eacute;dente">Pr&eacute;c&eacute;dent</div>
	<div class="menuitems" onclick="history.forward();" title="Page suivante">Suivant</div>
	<div id="menuitemsJSBottom" style="display:none;"></div>
</div>
<script language="javascript" type="text/javascript">
	if (MCie5||MCns6) menuobj=document.getElementById("ie5menu");	/* Détermine le menu de départ */
	MCenable=true;
</script>