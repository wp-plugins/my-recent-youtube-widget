<!-- My Recent YT Control -->

<p>
<label for="my-recent-yt_title"><span style="display:block;">Title</span>
<input class="widefat" type="text" id="my-recent-yt_title" name="my-recent-yt_title" value="<?php echo $title; ?>" /></label>
</p>

<p>
<label for="my-recent-yt_username"><span style="display:block;">YouTube Username</span>
<input class="widefat" type="text" id="my-recent-yt_username" name="my-recent-yt_username" value="<?php echo $username; ?>" /></label>
</p>

<p>
<label for="my-recent-yt_num_videos"><span style="display:block;">Number of videos</span>
<input class="fat" type="text" id="my-recent-yt_num_videos" name="my-recent-yt_num_videos" value="<?php echo $numVideos; ?>" size="3" maxlength="3" /></label>
</p>

<p>
<label for="my-recent-yt_width"><span style="display:block;">Width</span>
<input class="fat" type="text" id="my-recent-yt_width" name="my-recent-yt_width" value="<?php echo $width; ?>" size="4" maxlength="4" /></label>
</p>

<p>
<label for="my-recent-yt_height"><span style="display:block;">Height</span>
<input class="fat" type="text" id="my-recent-yt_height" name="my-recent-yt_height" value="<?php echo $height; ?>" size="4" maxlength="4" /></label>
</p>

<div class="my-recent-yt-expandable">
<div class="my-recent-yt-extoggle" style="color: blue;text-decoration: underline;cursor: pointer;">Advanced</div>
<div style="display: none;" class="my-recent-yt-expanel">
	<p>
	<label for="my-recent-yt_wrapper_class"><span style="display:block;">Widget wrapper &lt;div&gt; class</span>
	<input class="widefat" type="text" id="my-recent-yt_wrapper_class" name="my-recent-yt_wrapper_class" value="<?php echo $wrapperClass; ?>" /></label>
	</p>
</div>
</div>

<br />
<!-- END:My Recent YT Control -->