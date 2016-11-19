<?php
	if(!isset($_SESSION))
		die("Invalid call!");

//On vérifie si Piwik est bien activé
if($enable_piwik == 1)
{
	?><!-- Piwik -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  _paq.push(["setDoNotTrack", true]);
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
		var u="<?php echo $adresse_piwik; ?>";
		_paq.push(['setTrackerUrl', u+'piwik.php']);
		_paq.push(['setSiteId', <?php echo $id_site_piwik; ?>]);
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<noscript><p><img src="<?php echo $adresse_piwik; ?>piwik/piwik.php?idsite=<?php echo $id_site_piwik; ?>" style="border:0;" alt="" /></p></noscript>
	<!-- End Piwik Code -->
	<?php
}