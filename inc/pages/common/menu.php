<?php
/**
 * Website menu
 *
 * @author Pierre HUBERT
 */

if($afficher['old_menu'] == 0 AND !isset($menu_light))
{
	//Nouvelle version du menu
	?><font class="metro">
		 <div class="navigation-bar <?php echo $afficher['color_menu']; ?> new_navbar">
                <div class="navbar-content">

                	<!-- Home link -->
                    <a href="<?php echo $urlsite; ?>index.php" id="titre_site_nom" class="element">COMUNIC</a>
                    <a href="<?php echo $urlsite; ?>index.php" id="titre_site_lettre" class="element">C</a>

                    <!-- Two dividers -->
                    <span class="element-divider"></span>
					<span class="element-divider"></span>

                    <!-- Notifications button -->
                    <a class="element brand" title="Afficher les notifications" onClick="affiche_notifications()">
                    	<span class="button_moderne_menu" id="notification_area">
                    		<span class="icon-newspaper"></span>
                    	</span>
                    	<span id="nb_notification_area"></span>
                    	<script>setInterval('verifie_notifications_non_vues("<?php echo $urlsite; ?>");', 3000);</script>
                    </a>

                    <!-- Private chat button -->
					<a class="element brand" title="Ouvrir le chat priv&eacut;" id="button_private_chat" onClick="affiche_chat_prive(0);">
						<span class="button_moderne_menu">
							<span class="icon-comments-4"></span>
						</span>
					</a>

					<!-- Fil button -->
					<a class="element brand" id="button_view_fil" onClick="open_page_ameliore(0)">
						<span class="button_moderne_menu">
							<span class="icon-history"></span>
						</span>
					</a>

					<!-- New message button -->
					<span id="new_message"></span><script>verifie_messages_non_lus(1);</script>

					<!-- New friends button -->
					<span id="new_friend"></span><script>verifie_messages_non_lus(2);</script>

					<!-- Update alert information -->
					<?php if(isset($alert_last_update)) echo $alert_last_update ?>

					<!-- Divider -->
					<span class="element-divider"></span>

					<!-- Search someone -->
                    <div class="element input-element recherche_menu">
                            <div class="input-control text">
								<form action="<?php echo $urlsite; ?>recherche.php" method="post">
									<input placeholder="Recherche..." type="text" id="search_user_input" name="nom"  autocomplete="off" onkeyup="search_user_ajax();">
									<button class="btn-search" type="submit"></button>
								</form>
                            </div>
                    </div>
					<div id="result_search"></div>
					<div id="list_results"></div>

                    <div class="element place-right">
                        <a class="dropdown-toggle" href="#">
                            <span class="icon-cog"></span>
                        </a>
                        <ul class="dropdown-menu place-right <?php echo ($afficher['color_menu'] == "light" ? "inverse" : $afficher['color_menu']); ?>" data-role="dropdown">
                            <li><a href="<?php echo $urlsite; ?>amis.php"><i class="icon-list"></i> Vos Amis</a></li>
                            <li><a href="<?php echo $urlsite; ?>messagerie.php"><i class="icon-mail"></i> Messagerie interne</a></li>
							<!--<li><a href="<?php echo $urlsite; ?>webmail.php"><i class="icon-mail"></i> Webmail</a></li>-->
                            <li><a href="<?php echo $urlsite; ?>galerie_videos.php"><i class="icon-film"></i> Galerie de vid&eacute;os</a></li>
                            <li><a href="<?php echo $urlsite; ?>parametres.php"><i class="icon-tools"></i> Vos param&egrave;tres</a></li>
							
							<!-- Multi-authentification -->
							<?php
								//On vérifie si $_SESSION['ID_parent'] existe
								if(!isset($_SESSION['ID_parent']))
									$_SESSION['ID_parent'] = $_SESSION['ID'];
								
								if(count_sql("multi_login", "ID_personne = ?", $bdd, array($_SESSION['ID_parent'])) != 0)
								{
									?><li><a href="<?php echo $urlsite; ?>parametres.php?c=multi_login"><i class="icon-key"></i> Multi-authentification</a></li><?php
								}
								
								if($_SESSION['ID_parent'] != $_SESSION['ID'])
								{
									?><li><a href="<?php echo $urlsite; ?>parametres.php?c=multi_login&back_main_account"><i class="icon-key"></i> Compte parent</a></li><?php
								}
							?>
							
							<!-- End of: multi-authentification -->
							
							
                            <li><a href="<?php echo $urlsite; ?>deconnexion.php"><i class="icon-switch"></i> D&eacute;connexion</a></li>
							<li class="divider"></li>
							<li><a onClick="affiche_panneau_amis()">Afficher le panneau des amis</a></li>
							
                        </ul>
						<a>&nbsp;</a>
					</div>
					
					<span class="element-divider place-right"></span><!-- Séparateur -->

					
                    <button class="element image-button image-left place-right retour_page_user" onClick="open_page_ameliore(<?php echo $_SESSION['ID']; ?>);">
						<?php echo $afficher['prenom']." ".$afficher['nom']; ?>
						<?php echo avatar($_SESSION['ID'], './'); ?>
                    </button>
						
                </div>
			</div></font>
		<div class="correctifnouvbarre">&nbsp;</div>
		<div id="new_notification">
			<div class='new_iframenotification notifications_contener' id='new_iframenotification'>
				<div class="chargement">
					<?php echo code_inc_img(path_img_asset('chargement.gif'), "Veuillez patienter, chargement en cours..."); ?>
				</div>
			</div>
			<div id="new_notification_close">
				<input type="button" value="Fermer" onClick='MM_showHideLayers("new_notification", "", "hide");' />
			</div>
			<div id="new_notification_refresh">
				<?php
				echo code_inc_img(path_img_asset('small/date_delete.png'), "Supprimer les anciennes notifications", "", "", "", "delete_old_notifications();");
				echo " ".code_inc_img(path_img_asset('small/bin_closed.png'), "Supprimer toute les notifications", "", "", "", "supprime_toute_les_notifications();");
				echo " ".code_inc_img(path_img_asset('refresh.png'), "Actualiser les notifications", "", "", "", "affiche_notifications();"); ?>
			</div>
		</div>
		<?php
}
elseif(isset($menu_light))
{
	?><font class="metro">
		 <div class="navigation-bar <?php echo $afficher['color_menu']; ?>" style="position: fixed">
                <div class="navbar-content">
					<a href="<?php echo $urlsite; ?>index.php" target="_blank" class="element">COMUNIC</a>
                    <span class="element-divider"></span>
					
                    <!-- Logout button -->
					<a href="deconnexion.php" class="element place-right" >
						D&eacute;connexion
					</a>
					<!-- End of: Logout button -->
                </div>
            </div>
			</div></font>
		<div class="correctifnouvbarre">&nbsp;</div><?php
}
else
{
	echo "<p>The required menu couldn't be found !</p>";


	//Fatal error : required menu not found
	report_error("Required menu not found", $raison = "Required menu not found in menu.php.");
}