<?php
/**
 * Top menu of the website's page  and common contents to all website
 *
 * @author Pierre HUBERT
 */

//On détermine si il faut afficher un message d'alerte relatif à une mise à jour récente
//Détermination de la période d'alerte d'information d'une mise à jour
$periode_alerte_update = time() - $alert_last_update_website;

//On vérifie si il faut afficher un message d'avertissement
if($last_update_website > $periode_alerte_update)
	$alert_last_update = '<a class="element brand" onClick="show_hide_id(\'alert_last_update\', \'visible\');"><span class="button_moderne_menu"><span class="icon-warning"></span></span></a>'; //Il faut alerter au sujet de la mise à jour

if(!isset($_SESSION['ID']))
{
	?><font class="metro">
		<div class="navigation-bar blue new_navbar">
            <div class="navbar-content">
				<a href="index.php" class="element titre_site_without_login_big">COMMUNIQUONS.ORG</a>
				<a href="index.php" class="element titre_site_without_login_small">Comunic</a>
                <span class="element-divider"></span>
				<?php if(isset($alert_last_update)) echo $alert_last_update ?>
				
				<!-- Formulaire de connexion -->
                <div class="element input-element place-right" id="loginuser">
					<form action="connecter.php" method="post" id="form_login_top">
						<div class="input-control text">
							<input placeholder="Adresse mail..." type="text" name="mail" required <?php if(isset($_COOKIE['usermail'])) echo "value='".$_COOKIE['usermail']."'"; ?> />
						</div>
						
						<div class="input-control text">
							<input placeholder="Mot de passe..." type="password" name="motdepasse" required />
						</div>
						
						<div class="button" onClick="document.getElementById('form_login_top').submit()">
							Connexion
						</div>
						
						<!-- Bouton caché, Permet la validation du formulaire par la touche entrée -->
						<div style="visibility: hidden">
							<input type="submit" />
						</div>
						<!-- Fin de: Bouton caché -->
					</form>
                </div>
				
				<a href="<?php echo $urlsite; ?>connecter.php" class="element bouton_login_user place-right">Connexion</a>
				<!-- Fin de: Formulaire de connexion -->
			</div>		
        </div>
		<div class="correctifnouvbarre">&nbsp;</div> <!-- Correctif de la nouvelle barre de menu -->
	</font>
	<?php
}
else
{
		//On commence par rechercher les informations de la personne
		$afficher = cherchenomprenom($_SESSION['ID'], $bdd);

		//On inclus le menu
		include('menu.php');
		
		//On affiche la liste des amis si nécessaire
		if(!isset($menu_light))
		{
		
			//Affichage de la liste des amis
			if(count(liste_amis($_SESSION['ID'], $bdd)) == 0)
			{
				//Peu d'amis
				?><font class="metro">
					<div class="listeamis_light" id="listeamis">
						<div class="window">
							 <div class="caption" id="topAmis">
								<span class="icon icon-user"></span>
								<div class="title"><?php echo $lang[41]; ?></div>
								<button class="btn-close" onClick="ferme_panneau_amis();" style="cursor: pointer;"></button>
							</div>
							<div class="content">
								<!-- Affichage dynamique des amis -->
									<table id="fileAmis">
										<tr><td><?php echo code_inc_img(path_img_asset('wait.gif'), "Veuillez patienter, chargement en cours..."); ?></td></tr>
									</table>

									<!-- Fermeture du panneau des amis <span class="fermerpanneauami"><input type="button" value="Fermer" onClick="ferme_panneau_amis();" /></span>-->
								<!-- Script javascript avec ajax -->
								<script type='text/javascript'>var beaucoup_amis = false; /* De petits avatars sont préférables */</script>
								<script type='text/javascript'>var debut_URL_liste_amis = "<?php echo $urlsite; ?>"; /* Configuration de l'URL où trouver la liste d'amis */</script>
								<?php echo code_inc_js(path_js_asset('amisajax.js')); ?>

								<!-- Internet errors counter -->
								<span style="display: none;" id="internet_error">0</span>
								<!-- Fin de: Script javascript ajax -->
								<!-- Fin de: affichage dynamique des amis -->
							</div>
						</div>
					</div>
				</font><?php
			}
			else
			{
				//Beacoup d'amis
				?><div class="listeamis metro" id="listeamis">
				<!-- Affichage dynamique des amis -->
					<div class="panel" data-role="panel">
						<div class="panel-header panel-header-liste-amis"> Amis </div>
						
						<div class="panel-content">
							<table id="fileAmis" class="grande_liste_amis">
								<tr><td><?php echo code_inc_img(path_img_asset('wait.gif'), "Veuillez patienter, chargement en cours..."); ?></td></tr>
							</table>
						</div>
					</div>
					<!-- Fermeture du panneau des amis--> <div class="fermerpanneauami"><input type="button" value="Fermer" onClick="ferme_panneau_amis();" /></div>
				<!-- Script javascript avec ajax -->
				<script type='text/javascript'>var beaucoup_amis = true; /* De grands avatars sont préférables */</script>
				<script type='text/javascript'>var debut_URL_liste_amis = "<?php echo $urlsite; ?>"; /* Configuration de l'URL où trouver la liste d'amis */</script>
				<?php echo code_inc_js(path_js_asset('amisajax.js')); ?>

				<!-- Internet erros counter -->
				<span style="display: none;" id="internet_error">0</span>
				<!-- Fin de: Script javascript ajax -->
				<!-- Fin de: affichage dynamique des amis -->
				</div><?php
			}
			
			//On vérifie si il faut masquer le volet des amis
			if($afficher['volet_amis_ouvert'] == 0)
			{
				?><style type="text/css">.listeamis { display: none; }</style><?php
			}
			
		}
}
	

//Message d'avertissement si nécessaire
if(isset($alert_last_update))
{
	?><link rel="stylesheet" href="<?php echo $urlsite ?>css/warning_update.css" />
	<div id="alert_last_update">
		<!-- Image de l'avertissement -->
		<img src="<?php echo $urlsite; ?>img/warning_update.png" />
		
		<div class="close"><input type="button" value="Fermer" onClick="show_hide_id('alert_last_update', 'hidden');" /></div>
	</div><?php
}
?>