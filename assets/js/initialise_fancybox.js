//Préparation de FancyBox (images des posts)
$(document).ready(function() {
	$(".fancybox").fancybox({
		openEffect : 'none',
		closeEffect : 'none',
		prevEffect : 'none',
		nextEffect : 'none',
		
		helpers : {
			title : {
				type : 'inside'
			}
		}
	}); 
});