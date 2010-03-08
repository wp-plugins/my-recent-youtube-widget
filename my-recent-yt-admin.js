jQuery(document).ready(function() {
	jQuery('.my-recent-yt-extoggle').click(function() {
		jQuery(this).hide();
		// Safari makes us show() the div before it can slideDown()
		jQuery(this).siblings('div.my-recent-yt-expanel').show().slideDown();
	});
});