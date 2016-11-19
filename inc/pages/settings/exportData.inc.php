<?php
/**
 * Export datas
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

?>
<h3>Export des informations personnelles</h3>
<p>
	<i>Vous permet de g&eacute;n&eacute;rer une page web contenant toute les informations personnelles vous concernant dans une page web que vous pourrez enregister ou imprimer. Vous permet de contr&ocirc;ler l'int&eacute;gralit&eacute; de votre activit&eacute;.</i>
</p>
<form action='exportdonnees.php' method="post">
	<p>Pour des raisons de s&eacute;curit&eacute;, veuillez saisir votre mot de passe : </p>
	<input type="password" name="password" placeholder="Mot de passe" />
	<input type='submit' value='Exporter vos donn&eacute;es personelles vers une page web' />
</form>
<!--<a href="action.php?actionid=4">Envoyer les donn&eacute;es par mail</a>-->
<?php