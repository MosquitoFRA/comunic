<?php
/**
 * Change menu settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//Menu Bar
?><nav class="sidebar <?php echo (($afficher['color_menu'] == "blue") || ($afficher['old_menu'] == 1) ? "light" : $afficher['color_menu']); ?>">
	<ul>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="icon-home"></i> <?php echo $lang[56]; ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=groupe_personnes">Groupes de personnes</a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=multi_login"><i class="icon-key"></i> Multi-authentification</a></li>
		<!--<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=pages">Vos pages</a></li>-->
		
		<li class="title">Personnalisation</li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=avatar"><i class="icon-user"></i> Avatar</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=imgfond"><i class="icon-pictures"></i> Image de fond</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=apparence"><i class="icon-tools"></i> Apparence</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=notifications"><i class="icon-comments-4"></i> Notifications</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=rss"><i class="icon-feed"></i> Flux RSS</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=repertoire"><i class="icon-globe"></i> Choix de l'URL</a></li>
		
		 <li class="title">Vie priv&eacute;e</li>
				<?php if($activer_publique_chat == "oui") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=chat"><i class="icon-comments"></i> Chat publique</a></li><?php } ?>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=clean_account"><i class="icon-cycle"></i> Nettoyage de votre compte</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=visibilite_avatar"><i class="icon-menu"></i> Visibilit&eacute; de votre avatar</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=piwik"><i class="icon-stats"></i> Gestion de Piwik</a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=exportinfo"><i class="icon-share"></i> Export des informations personnelles</a></li>
	</ul>
</nav>