<?php
	//Vérification de la clé de sécurité
	if(!isset($add_comunic_as_search_engine_check))
		die('Invalid call!');
		
	//Envoi de l'header XML
	header('Content-Type: application/xml');
echo "<"; ?>?xml version="1.0" encoding="UTF-8" ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
    <ShortName>Comunic</ShortName>
    <Description>Un r&#233;seau social qui respecte votre vie priv&#233;e.</Description>
    <InputEncoding>ISO-8859-1</InputEncoding>
    <OutputEncoding>ISO-8859-1</OutputEncoding>
    <Image width="16" height="16" type="image/x-icon"><?php echo $urlsite; ?>img/favicon.ico</Image>
    <Image width="64" height="64" type="image/x-icon"><?php echo $urlsite; ?>img/favicon_64.ico</Image>
    <Url type="text/html" method="get" template="<?php echo $urlsite; ?>recherche.php?q={searchTerms}"></Url>
    <Language>fr_FR</Language>
</OpenSearchDescription>