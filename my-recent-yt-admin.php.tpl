<!-- My Recent YT Control -->

<p>
<label for="my-recent-yt_title-<?php echo $number; ?>"><span style="display:block;">Title</span>
<input class="widefat" type="text" id="my-recent-yt_title-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /></label>
</p>

<p>
<label for="my-recent-yt_username-<?php echo $number; ?>"><span style="display:block;">YouTube Username</span>
<input class="widefat" type="text" id="my-recent-yt_username-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][username]" value="<?php echo $username; ?>" /></label>
</p>

<p>
<label for="my-recent-yt_num_videos-<?php echo $number; ?>"><span style="display:block;">Number of videos</span>
<input class="fat" type="text" id="my-recent-yt_num_videos-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][numVideos]" value="<?php echo $numVideos; ?>" size="3" maxlength="3" /></label>
</p>

<p>
<label for="my-recent-yt_show_titles-<?php echo $number; ?>"><span style="display:block;">Show titles under videos</span>
<input type="hidden" name="widget-my-recent-yt[<?php echo $number; ?>][showTitles]" value="0" />
<input class="fat" type="checkbox" id="my-recent-yt_show_titles-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][showTitles]" value="1" <?php if($showTitles) : ?>checked="checked" <?php endif; ?>/></label>
</p>

<p>
<label for="my-recent-yt_width-<?php echo $number; ?>"><span style="display:block;">Width</span>
<input class="fat" type="text" id="my-recent-yt_width-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][width]" value="<?php echo $width; ?>" size="4" maxlength="4" /></label>
</p>

<p>
<label for="my-recent-yt_height-<?php echo $number; ?>"><span style="display:block;">Height</span>
<input class="fat" type="text" id="my-recent-yt_height-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][height]" value="<?php echo $height; ?>" size="4" maxlength="4" /></label>
</p>

<p>
<label for="my-recent-yt_cache_timeout-<?php echo $number; ?>"><span style="display:block;">Cache timeout (seconds)</span>
<input class="fat" type="text" id="my-recent-yt_cache_timeout-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][cacheTimeout]" value="<?php echo $cacheTimeout; ?>" size="4" maxlength="4" /></label>
</p>

<div class="my-recent-yt-expandable">
<div class="my-recent-yt-extoggle" style="color: blue;text-decoration: underline;cursor: pointer;">Advanced</div>
<div style="display: none;" class="my-recent-yt-expanel">
	<p>
	<label for="my-recent-yt_wrapper_class-<?php echo $number; ?>"><span style="display:block;">Widget wrapper &lt;div&gt; class</span>
	<input class="widefat" type="text" id="my-recent-yt_wrapper_class-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][wrapperClass]" value="<?php echo $wrapperClass; ?>" /></label>
	</p>
	
	<p>
	<label for="my-recent-yt_wrapper_id-<?php echo $number; ?>"><span style="display:block;">Widget wrapper &lt;div&gt; ID</span>
	<input class="widefat" type="text" id="my-recent-yt_wrapper_id-<?php echo $number; ?>" name="widget-my-recent-yt[<?php echo $number; ?>][wrapperID]" value="<?php echo $wrapperID; ?>" /></label>
	</p>
</div>

</div>

<input type="hidden" name="widget-my-recent-yt[<?php echo $number; ?>][submit]" value="1" />

<br />
<!-- END:My Recent YT Control -->