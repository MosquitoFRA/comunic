<?php
//This file extract the title, the description and the image of a webpage, which are indicated in <meta og: markups
function analyse_source_page($source)
{
	//Préparation de l'analyse
	$page_title = null;
	$page_description = null;
	$page_image = null;

	//Analysing datas
	preg_match_all('#<meta (.*?)>#is', $source, $result,PREG_PATTERN_ORDER);

	//Fetching
	$liste_types = array(
		array('title' => 'page_title', 'pattern' => '<property="og:title">', 'begining' => 'content="', 'ending' => '"'),
		array('title' => 'page_title', 'pattern' => "<property='og:title'>", 'begining' => "content='", 'ending' => "'"),
		array('title' => 'page_description', 'pattern' => "<property='og:description'>", 'begining' => "content='", 'ending' => "'"),
		array('title' => 'page_description', 'pattern' => '<property="og:description">', 'begining' => 'content="', 'ending' => '"'),
		array('title' => 'page_image', 'pattern' => '<property="og:image">', 'begining' => 'content="', 'ending' => '"'),
		array('title' => 'page_image', 'pattern' => "<property='og:image'>", 'begining' => "content='", 'ending' => "'"),
	);

	//Analysing list
	foreach($result[1] as $fetching)
	{
		//Parcours des types de traitement
		foreach($liste_types as $fetching_infos)
		{

			//Preparing
			$fetching = str_replace(' =', '=', $fetching);

			//Vérification de la présence des informations
			if(preg_match($fetching_infos['pattern'], $fetching))
			{
				$begining = $fetching_infos['begining'];
				$ending = $fetching_infos['ending'];
				$title = $fetching_infos['title'];
				$content = strstr($fetching, $begining);
				$content = str_replace($begining, '', $content);
				$content = strstr($content, $ending, true);
				${$title} = $content;
			}
		}
	}

	//Sending result
	return array('title' => $page_title, 'description' => $page_description, 'image' => $page_image);
}