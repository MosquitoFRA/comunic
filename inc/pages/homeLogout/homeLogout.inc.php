<?php
/**
 * Home screen when no user is logged in
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call ! - homeLogout.inc.php');

//On vérifie si il s'agit de Internet Explorer 7
//Si c'est le cas, on affiche un message d'incompatibilté
if(isset($_SERVER['HTTP_USER_AGENT']))
{
	if(preg_match('/MSIE 7/', $_SERVER['HTTP_USER_AGENT']))
	{
		?><table align='center'>
			<tr>
				<td>
					<img src='img/erreur.png' />
				</td>
				<td>
					<p><b><?php echo $lang[24]; ?> :</b> Internet Explorer 7.0 est un navigateur trop ancien pour &ecirc;tre encore utilis&eacute;.</p>
					<p>Internet Explorer 7 ne permet pas l'ex&eacute;cution de toutes les fonctionalit&eacute;es de Comunic. Par exemple, le chat priv&eacute; pourra ne pas s'ouvrir correctement.</p>
					<p>Ce navigateur pr&eacute;sente d'importantes failles de s&eacute;curit&eacute; pouvant nuire &agrave; la s&eacute;curit&eacute; de l'ordinateur.</p>
					<p>Nous vous recommandons fortement d'installer sur votre ordinateur un nouveau navigateur comme Mozilla Firefox, qui est un navigateur gratuit, moderne et securis&eacute;.</p>
				</td>
			</tr>
		</table><?php
	}

	//On vérifie si il s'agit de Internet Explorer 6
	//Si c'est le cas, on affiche un message d'incompatibilté
	if(preg_match('/MSIE 6.0/', $_SERVER['HTTP_USER_AGENT']))
	{
		//Fenêtre d'erreur dans ce cas-là
		?><table align='center'>
			<tr>
				<td>
					<img src='img/exclamation.png' />
				</td>
				<td>
			<p>Le navigateur que vous utilisez (Internet Explorer 6) n'est pas compatible avec Comunic.</p>
			<p>Nous vous recommandons fortement de mettre à niveau vers un nouveau navigateur tel que Mozilla Firefox accessible à cette adresse : <a href='http://mozilla.org/' title='Site officiel de mozilla'>http://mozilla.org/</a></p>
		</td>
			</tr>
		</table><?php
	}
}

//Check if we have to show a beta message
if ($affichebeta == 1)
{
	//On affiche le message
	?>
		<!-- Message 'en beta' -->
		<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
			<p>
				<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<strong><?php echo $lang[24]; ?> :</strong> <?php echo $textebeta[$lang['nomlangue_raccourcis']]; ?>
			</p>
		</div>
		<!-- Fin de: Message 'en beta' -->
	<?php
}

if($affichemessageclassique == 1)
{
	?>
	<table align='center'>
		<tr>
			<td>
				<img src='img/erreur.png' />
			</td>
			<td>
				<p><b><?php echo $lang[24]; ?> :</b><?php echo corrige_caracteres_speciaux($textecookie[$lang['nomlangue_raccourcis']]); ?></p>
			</td>
		</tr>
	</table>
	<?php
}
?>
<!-- Open Graph content -->
<meta property="og:title" content="Communiquons.org"/>
<meta property ="og:description" content="D&eacute;couvrez un nouveau moyen de communication respectant la vie priv&eacute;e."/>
<meta property="og:image" content="<?php echo path_img_asset("logo_comunic.png"); ?>"/>

<br />
<div class="metro home_contener">
	<!-- Texte d'acceuil -->
	<div class="message_page_acceuil_login">
		<?php echo add_url_site(file_get_contents(__DIR__.'/acceuil-'.$lang['nomlangue_raccourcis'].'.html'), $urlsite); ?>
	</div>
	<!-- Fin de: texte d'acceuil -->
	
	<!-- Boutons d'accès -->
	<div class="newaccount_page_acceuil_login">
		<a class="command-button info" href="creercompte.php">
			<i class="icon-pencil on-left"></i>
			<small>Cr&eacute;ez-vous un compte</small>
		</a>
			
		<p class="or">ou</p>
		
		<a class="command-button success" href="whatiscomunic.php">
			<i class="icon-arrow-right-3 on-left"></i>
			<small>D&eacute;couvrez les fonctionnalit&eacute;s de Comunic</small>
		</a>
	</div>
	<!-- Fin de: Boutons d'accès-->
	</div>
</div>
<?php include(pagesRelativePath('common/pageBottom.php'));