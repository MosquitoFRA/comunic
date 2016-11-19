<?php
	//Démarrage de la session
	session_start();
	
	//Connexion d'un utilisateur requise
	if(!isset($_SESSION['ID']))
	{
		header('location: ../connecter.php?redirect=./minifyurl');
		die("<a href='../connecter.php?redirect=./minifyurl'>connexion requise</a>");
	}
	
	//Init page
	include('../inc/initPage.php');
	
	//On vérifie si il faut réduire une URL
	if(isset($_POST['url']))
	{
		if($_POST['url'] != "")
		{
			if(preg_match('<://>', $_POST['url']) AND !preg_match('/javascript:/', $_POST['url']))
				$url = $_POST['url'];
			
			if(!isset($url))
				$erreur = "L'URL saisie est incorrecte !";
			else
			{
				//On vérifie qu'il n'existe pas d'URL portant ce nom dans la bdd
				$sql = "SELECT * FROM minifyURL WHERE url = ?";
				$requete = $bdd->prepare($sql);
				$requete->execute(array($url));
				
				//Enregistrement des résultats
				while($resultats = $requete->fetch())
					$infos_url = $resultats;
				
				//Fermeture de la requête
				$requete->closeCursor();
				
				if(!isset($infos_url))
				{
					//Détermination de l'ID de l'URL
					do {
						$non_ok = false;
						
						srand();
						$randval = rand();
						$randval2 = rand();
						$id_url = strtolower(
							str_replace(
								array('$', '@', ' ', '	', '/', '\\', '.'), 
								'', 
								crypt(sha1($url, $randval2).time().$_SESSION['ID'].$_SERVER['HTTP_COOKIE'].$randval, $randval2)
							)
						);
						
						
						//On raccourcis l'ID
						$id_url = substr($id_url, 0, 20);
						
						//On vérifie qu'il n'existe pas d'URL portant ce nom dans la bdd
						$sql = "SELECT COUNT(*) AS nb_url FROM minifyURL WHERE ID = ?";
						$check = $bdd->prepare($sql);
						$check->execute(array($id_url));
						
						//Enregistrement du résultat
						if(!$nb_url = $check->fetch())
							die('Error. Please try again later.');
						$nb_url = $nb_url['nb_url'];
						
						//Fermeture de la requête
						$check->closeCursor();
					} while($nb_url != 0);
					
					//On enregistre l'URL dans la BDD
					$sql = "INSERT INTO minifyURL (ID, url, date_ajout) VALUES (?, ?, NOW())";
					$insertion = $bdd->prepare($sql);
					if(!$insertion->execute(array($id_url, $url)))
					{
						unset($id_url);
						$erreur = "Merci de r&eacute;essayer, une erreur a survenue.";
					}
				}
				else
				{
					$id_url = $infos_url['ID'];
				}
				
			}
		}
	}
?><!DOCTYPE html>
<html>
	<head>
		<title>R&eacute;ducteur d'URL</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<style type="text/css">
		body {
			margin: auto;
			width: 500px;
			text-align: center;
			margin-top: 30px;
			font-size: 110%;
			border: 1px black solid;
			padding: 15px;
			border-radius: 2px;
		}
		</style>
	</head>
	<body class="metro">
		Reducteur d'URL
		
		<?php
			//On vérifie si il y a une erreur
			if(isset($erreur))
			{
				echo "<p style='color: red; text-align: center;'>".$erreur."</p>";
			}
			
			//Si il y a une URL a afficher
			if(isset($id_url) AND isset($url))
			{
				echo "<p style='color: green; text-align: center;'>".$url." => ".$urlsite."minifyurl/url.php/".$id_url."</p>";
			}
		?>
		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<div class="input-control text">
				<input type="text" name="url" placeholder="http://" required /> 
			</div>
			<input type="submit" value="R&eacute;duire l'URL" />
		</form>
	</body>
</html>