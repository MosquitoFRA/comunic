<?php
/**
 * Video upload form
 *
 * @author Pierre HUBERT
 */

if(!isset($verification))
	die(); //Sécurité
		
?><!DOCTYPE html>
<html>
	<head>
		<title>Envoi d'une vid&eacute;o en ligne</title>
        <?php
        /**
         * Third Party elements inclusion
         */
        //Gestionnaire upload
        echo code_inc_css(url_3rdparty('gestionnaire_upload/css/main.css'));
        echo code_inc_js(url_3rdparty('gestionnaire_upload/js/script.js'));

        ?>
		
	</head>
	<body>
		<h1>Mise en ligne d'une vid&eacute;o</h1>

            <div class="upload_form_cont">
                <form id="upload_form" enctype="multipart/form-data" method="post" action="upload.php.txt">
                    <div>
                        <div><label for="file">Veuillez choisir votre vid&eacute;o</label></div>
                        <div><input type="file" name="file" id="image_file fime" onchange="fileSelected();" /></div>
                    </div>
                    <div>
                        <input type="button" value="Mise en ligne" onclick='startUploading("<?php echo $urlsite; ?>action.php?actionid=11")' />
                    </div>
                    <div id="fileinfo">
                        <div id="filename"></div>
                        <div id="filesize"></div>
                        <div id="filetype"></div>
                        <div id="filedim"></div>
                    </div>
                    <div id="error"></div>
                    <div id="error2">Une erreur est survenue lors de l'envoi de la vid&eacute;o. Merci de r&eacute;essayer ult&eacute;rieurement.</div>
                    <div id="abort">L'envoi a &eacute;t&eacute; annul&eacute; ou votre navigateur web a interrompu la connexion.</div>
                    <div id="warnsize">Votre vid&eacute;o est trop lourde. Nous ne pouvons pas l'accepter.</div>

                    <div id="progress_info">
                        <div id="progress"></div>
                        <div id="progress_percent">&nbsp;</div>
                        <div class="clear_both"></div>
                        <div>
                            <div id="speed">&nbsp;</div>
                            <div id="remaining">&nbsp;</div>
                            <div id="b_transfered">&nbsp;</div>
                            <div class="clear_both"></div>
                        </div>
                        <div id="upload_response"></div>
                    </div>
                </form>

            </div>
	</body>
</html>