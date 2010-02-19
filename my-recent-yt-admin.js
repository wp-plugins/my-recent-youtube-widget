jQuery(document).ready(function() {
	jQuery('.my-recent-yt-extoggle').click(function() {
		jQuery(this).hide();
		jQuery(this).siblings('div.my-recent-yt-expanel').slideDown();
	});
});