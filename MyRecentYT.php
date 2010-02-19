<?php

/**
 * Copyright (c) 2009 Dave Ross <dave@csixty4.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

include_once("DavesFileCache.php");

class MyRecentYT
{
	/**
	 * Initialize the plugin
	 */
	function init()
	{
		
		add_action('admin_init', array(__CLASS__, 'admin_init'));
		//register_sidebar_widget('My Recent YouTube', array(__CLASS__, 'renderWidget'));
		
		
		//register_widget_control('My Recent YouTube', array(__CLASS__, 'renderControl'), 400, 300);
		if ( !$options = get_option('widget_my-recent-yt') )
			$options = array();
		$widget_ops = array('classname' => 'widget_my-recent-yt', 'description' => __("A YouTube user's most recent videos"));
		$control_ops = array('width' => 400, 'height' => 700, 'id_base' => 'my-recent-yt');
		$name = __('My Recent YouTube');

		$id = false;
		foreach ( (array) array_keys($options) as $o ) {
			// Old widgets can have null values for some reason
			if (!isset($options[$o]['username']) )
				continue;
				
			$id = "my-recent-yt-$o"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, array(__CLASS__, 'renderWidget'), $widget_ops, array( 'number' => $o ));
			wp_register_widget_control($id, $name, array(__CLASS__, 'renderControl'), $control_ops, array( 'number' => $o ));
		}
	
		// If there are none, we register the widget's existance with a generic template
		if ( !$id ) {
			wp_register_sidebar_widget( 'my-recent-yt-1', $name, array(__CLASS__, 'renderWidget'), $widget_ops, array( 'number' => -1 ) );
			wp_register_widget_control( 'my-recent-yt-1', $name, array(__CLASS__, 'renderControl'), $control_ops, array( 'number' => -1 ) );
		}
		
	}
	
	function admin_init()
	{
		$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
		
		if(!DavesFileCache::testCacheDir())
		{
			echo <<<WARNING
				<div class="error"><p>The cache directory for <strong>My Recent YouTube Widget</strong> does not exist or can't be written to.</p><p>Please make sure there is a directory named "cache" in the plugin's directory and it is writable by your web server.</p></div>
WARNING;
		}
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('my-recent-yt-admin', $pluginPath.'/my-recent-yt-admin.js', 'jquery');
		
	}
	
	/**
	 * Retrieve's this plugin's options & sets defaults where needed
	 *
	 * @return Array variables, process with extract()
	 */
	function getOptions()
	{
		// TODO support multiple instances with different configs
		$title = get_option('my-recent-yt_title');
		if(!$title) $title = '';
		$username = get_option('my-recent-yt_username');
		if(!$username) $username = '';
		$numVideos = get_option('my-recent-yt_num_videos');
		if(!$numVideos) $numVideos = 1;
		$width = get_option('my-recent-yt_width');
		if(!$width) $width = 320;
		$height = get_option('my-recent-yt_height');
		if(!$height) $height = 240;
		$wrapperClass = get_option('my-recent-yt_wrapper_class');
		if(!$wrapperClass) $wrapperClass = '';
		$wrapperID = get_option('my-recent-yt_wrapper_id');
		if(!$wrapperID) $wrapperID = '';
		$cacheTimeout = get_option('my-recent-yt_cache_timeout');
		if(!$cacheTimeout) $cacheTimeout = 3600;
		
		return compact('username', 'numVideos', 'width', 'height', 'title', 'wrapperClass', 'cacheTimeout', 'wrapperID');
	}
	
	/**
	 * Render the widget for display (like in a sidebar)
	 */
	function renderWidget($args, $widget_args = 1)
	{	
		extract( $args, EXTR_SKIP );
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );
		$options = get_option('widget_my-recent-yt');

		if ( !isset($options[$number]) )
			return;
	
		$title = apply_filters('widget_title', $options[$number]['title']);
		$username = apply_filters( 'widget_text', $options[$number]['username'] );
		$numVideos = apply_filters('widget_text', $options[$number]['numVideos']);
		$width = apply_filters('widget_text', $options[$number]['width']);
		$height = apply_filters('widget_text', $options[$number]['height']);
		$cacheTimeout = apply_filters('widget_text', $options[$number]['cacheTimeout']);
		$wrapperClass = apply_filters('widget_text', $options[$number]['wrapperClass']);
		$wrapperID = apply_filters('widget_text', $options[$number]['wrapperID']);
	?>
			<?php echo $before_widget; ?>
			<?php if(!empty($wrapperClass) || !empty($wrapperID)) echo "<div id=\"$wrapperID\" class=\"$wrapperClass\">"; ?>
				<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
				<?php 
				echo '<div class="my-recent-yt-widget">';
				$videoIDs = self::getVideoIDs($options[$number]);
				foreach($videoIDs as $videoID)
				{
					echo self::getVideoEmbed($videoID, $width, $height);
				}
				echo '</div>';
			if(!empty($wrapperClass) || !empty($wrapperID)) echo "</div>";
			?>
			<?php echo $after_widget; ?>
	<?php
	}

	
	function renderControl($widget_args = array())
	{
		global $wp_registered_widgets;
		static $updated = false;
	
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );
	
		$options = get_option('widget_my-recent-yt');
		if ( !is_array($options) )
			$options = array();
		
		if ( !$updated && !empty($_POST['sidebar']) ) {
			
			$sidebar = (string) $_POST['sidebar'];
			
	
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar =& $sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();

			foreach ( (array) $this_sidebar as $_widget_id ) {
				if ( 'wp_widget_my-recent-yt' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
					$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
					if ( !in_array( "my-recent-yt-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
						unset($options[$widget_number]);
				}
			}
	
			foreach ( (array) $_POST['widget-my-recent-yt'] as $widget_number => $widget_settings ) {

				if ( !isset($widget_settings['username']) && isset($options[$widget_number]) ) // user clicked cancel
					continue;
				$title = strip_tags(stripslashes(trim($widget_settings['title'])));
				$username = strip_tags(stripslashes(trim($widget_settings['username'])));
				$numVideos = strip_tags(stripslashes($widget_settings['numVideos']));
				$height = strip_tags(stripslashes($widget_settings['height']));
				$width = strip_tags(stripslashes($widget_settings['width']));
				$cacheTimeout = strip_tags(stripslashes($widget_settings['cacheTimeout']));
				$wrapperClass = strip_tags(stripslashes($widget_settings['wrapperClass']));
				$wrapperID = strip_tags(stripslashes($widget_settings['wrapperID']));
				
				
				/**
				if ( current_user_can('unfiltered_html') )
					$text = stripslashes( $widget_settings['text'] );
				else
					$text = stripslashes(wp_filter_post_kses( $widget_settings['text'] ));
				**/
				
				$options[$widget_number] = compact( 'title', 'username', 'numVideos', 'height', 'width', 'cacheTimeout', 'wrapperID', 'wrapperClass' );
			}
			
			update_option('widget_my-recent-yt', $options);
			$updated = true;
		}

		if ( -1 == $number ) {
			$title = '';
			$number = '%i%';
			$numVideos = 2;
			$cacheTimeout = 3600;
			$height = 242;
			$width = 290;
			$wrapperClass = '';
			$wrapperID = '';
		} else {
			$title = attribute_escape($options[$number]['title']);
			$username = format_to_edit($options[$number]['username']);
			$numVideos = format_to_edit($options[$number]['numVideos']);
			$height = format_to_edit($options[$number]['height']);
			$width = format_to_edit($options[$number]['width']);
			$cacheTimeout = format_to_edit($options[$number]['cacheTimeout']);
			$wrapperID = format_to_edit($options[$number]['wrapperID']);
			$wrapperClass = format_to_edit($options[$number]['wrapperClass']);
		}
	
		include('my-recent-yt-admin.php.tpl');	
	}
	
	/**
	 * Sets a WordPress option. Adds or updates as necessary
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	function setOption($name, $value)
	{
		if(FALSE !== get_option($name))
			update_option($name, $value);
		else
			add_option($name, $value);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $options
	 * @return Array
	 */
	function getVideoIDs($options)
	{
		extract($options);
		
		$ids = array();
		
		$cacheIdentifier = "my-recent-yt-$username-$numVideos";
		
		try
		{
			$cache = DavesFileCache::forIdentifier($cacheIdentifier);
			$feedXML = $cache->get();
			throw new Exception("qqq");
		}
		catch(Exception $e)
		{
			$feedURL = "http://gdata.youtube.com/feeds/api/users/$username/uploads?v=2&orderby=published&max-results=$numVideos";
			$feedXML = file_get_contents($feedURL);
			
			$cache = new DavesFileCache($cacheIdentifier);
			$cache->store($feedXML, $cacheTimeout);
		}
		
		$xml = simplexml_load_string($feedXML);	
		
		if($xml)
		{
			foreach($xml->entry as $entry)
			{
				$id = $entry->id;
				
				$matches = array();
				
				preg_match("/video:([^,\\ ]*)/", $id, $matches);
				$ids[] = $matches[1];
			}
		}

		return $ids;
	}
	
	/**
	 * Builds the YouTube embed HTML for the given video
	 *
	 * @param string $videoID
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	function getVideoEmbed($videoID, $width, $height)
	{
		$embed = <<<EMBED
			<div class="my-recent-yt-video">
				<object width="$width" height="$height">
					<param name="movie" value="http://www.youtube.com/v/$videoID"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowscriptaccess" value="always"></param>
					<embed src="http://www.youtube.com/v/{$videoID}?hd=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="$width" height="$height"></embed>
				</object>
			</div>
EMBED;
		return $embed;
	}
}

?>