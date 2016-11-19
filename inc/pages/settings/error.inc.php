<?php
/**
 * Settings page not found
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//This message should appear only when an error occur.
?><table align='center'>
	<tr>
		<td>
			<?php echo code_inc_img(path_img_asset('erreur.png')); ?>
		</td>
		<td>
			<p><b>Erreur :</b> La rubrique demand&eacute;e n'a pas &eacute;t&eacute; trouv&eacute;e ou n'existe pas</p>
			<p>Pour r&eacute;soudre le probl&egrave;me, retournez &agrave; la page pr&eacute;c&eacute;dente et actualisez-la.</p>
			<p>N'h&eacute;sitez pas &agrave; nous <a href='<?php echo siteURL(); ?>contact.php'>contacter</a> pour de plus amples informations.</p>
		</td>
	</tr>
</table><?php