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
		register_sidebar_widget('My Recent YouTube', array(__CLASS__, 'renderWidget'));
		register_widget_control('My Recent YouTube', array(__CLASS__, 'renderControl'), 400, 300);
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
		
		return compact('username', 'numVideos', 'width', 'height', 'title', 'wrapperClass');
	}
	
	/**
	 * Render the widget for display (like in a sidebar)
	 */
	function renderWidget($widget_args = array())
	{	
		extract( $widget_args, EXTR_SKIP );
		extract(self::getOptions());
				
		echo $before_widget;
		if(!empty($wrapperClass)) echo "<div class=\"$wrapperClass\">";
		if(!empty( $title ))
		{
			echo $before_title.$title.$after_title;
		}
		
		echo '<div class="my-recent-yt-widget">';
		$videoIDs = self::getVideoIDs($username, $numVideos);
		foreach($videoIDs as $videoID)
		{
			echo self::getVideoEmbed($videoID, $width, $height);
		}
		echo '</div>';
		if(!empty($wrapperClass)) echo "</div>";
		echo $after_widget;
	}
	
	function renderControl($widget_args = array())
	{
		// TODO support multiple instances with different configs
		static $updated = false;
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );
	
		if ( !$updated && !empty($_POST['sidebar']))
		{
			$title = $_POST['my-recent-yt_title'];
			self::setOption('my-recent-yt_title', $title);
			$username = $_POST['my-recent-yt_username'];
			self::setOption('my-recent-yt_username', $username);
			$numVideos = $_POST['my-recent-yt_num_videos'];
			self::setOption('my-recent-yt_num_videos', $numVideos);
			$width = $_POST['my-recent-yt_width'];
			self::setOption('my-recent-yt_width', $width);
			$height = $_POST['my-recent-yt_height'];
			self::setOption('my-recent-yt_height', $height);
			$wrapperClass = $_POST['my-recent-yt_wrapper_class'];
			self::setOption('my-recent-yt_wrapper_class', $wrapperClass);
		}
		else
		{
			extract(self::getOptions());	
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
	 * @param unknown_type $username
	 * @param unknown_type $numVideos
	 * @return Array
	 */
	function getVideoIDs($username, $numVideos = 10)
	{
		$ids = array();
		
		$cacheIdentifier = "my-recent-yt-$username-$numVideos";
		
		try
		{
			$cache = DavesFileCache::forIdentifier($cacheIdentifier);
			$feedXML = $cache->get();
		}
		catch(Exception $e)
		{
			$feedURL = "http://gdata.youtube.com/feeds/api/users/$username/uploads?v=2&max-results=$numVideos";
			$feedXML = file_get_contents($feedURL);
			
			$cache = new DavesFileCache($cacheIdentifier);
			$cache->store($feedXML, 3600);
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
					<embed src="http://www.youtube.com/v/$videoID" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="$width" height="$height"></embed>
				</object>
			</div>
EMBED;
		return $embed;
	}
}

?>