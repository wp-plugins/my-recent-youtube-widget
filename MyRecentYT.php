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
	
		wp_enqueue_script('jquery');
		wp_enqueue_script('my-recent-yt-admin', $pluginPath.'/my-recent-yt-admin.js', 'jquery');		
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
                $showTitles = ($options[$number]['showTitles'] == TRUE);
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
				$videoInfo = self::getVideoInfo($options[$number]);
				foreach($videoInfo as $videoID=>$videoTitle)
				{
                                    $embedTitle = ($showTitles) ? $videoTitle : '';
                                    echo self::getVideoEmbed($videoID, $width, $height, $embedTitle);
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
				$numVideos = intval($widget_settings['numVideos']);
				$showTitles = ($widget_settings['showTitles'] == TRUE);
				$height = intval($widget_settings['height']);
				$width = intval($widget_settings['width']);
				$cacheTimeout = intval($widget_settings['cacheTimeout']);
				$wrapperClass = strip_tags(stripslashes($widget_settings['wrapperClass']));
				$wrapperID = strip_tags(stripslashes($widget_settings['wrapperID']));
				
				/**
				if ( current_user_can('unfiltered_html') )
					$text = stripslashes( $widget_settings['text'] );
				else
					$text = stripslashes(wp_filter_post_kses( $widget_settings['text'] ));
				**/
				
				$options[$widget_number] = compact( 'title', 'username', 'numVideos', 'showTitles', 'height', 'width', 'cacheTimeout', 'wrapperID', 'wrapperClass' );
			}
			
			update_option('widget_my-recent-yt', $options);
			$updated = true;
		}

		if ( -1 == $number ) {
			$title = '';
			$number = '%i%';
			$numVideos = 2;
                        $showTitles = FALSE;
			$cacheTimeout = 3600;
			$height = 242;
			$width = 290;
			$wrapperClass = '';
			$wrapperID = '';
		} else {
			$title = attribute_escape($options[$number]['title']);
			$username = format_to_edit($options[$number]['username']);
			$numVideos = format_to_edit($options[$number]['numVideos']);
                        $showTitles = ($options[$number]['showTitles'] == TRUE);
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
	function getVideoInfo($options)
	{
		extract($options);
		
		$info = array();
		
		$cacheIdentifier = "my-recent-yt-$username-$numVideos";
		
		$feedXML = get_transient($cacheIdentifier);
		if(!$feedXML) {
			$feedURL = "http://gdata.youtube.com/feeds/api/users/$username/uploads?v=2&orderby=published&max-results=$numVideos";
			$feedXML = file_get_contents($feedURL);
			set_transient($cacheIdentifier, $feedXML, $cacheTimeout);
		}
		
		$xml = simplexml_load_string($feedXML);	
		
		if($xml)
		{
			foreach($xml->entry as $entry)
			{
				$id = $entry->id;
				
				$matches = array();
				
				preg_match("/video:([^,\\ ]*)/", $id, $matches);
				$info[$matches[1]] = $entry->title;
			}
		}

		return $info;
	}
	
	/**
	 * Builds the YouTube embed HTML for the given video
	 *
	 * @param string $videoID
	 * @param integer $width
	 * @param integer $height
	 * @param string $title
	 * @return string
	 */
	function getVideoEmbed($videoID, $width, $height, $title)
	{
                $titleEmbed = '<div class="my-recent-yt-title">'.$title.'</div>';
		$embed = <<<EMBED
			<div class="my-recent-yt-video">
				<object width="$width" height="$height">
					<param name="movie" value="http://www.youtube.com/v/$videoID"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowscriptaccess" value="always"></param>
					<embed src="http://www.youtube.com/v/{$videoID}?hd=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="$width" height="$height"></embed>
				</object>
				$titleEmbed
			</div>
EMBED;
		return $embed;
	}
	
	/**
	 * @return string
	 */ 
	function getCacheDir() {
		return dirname(__FILE__)."/cache";
	}
}

?>