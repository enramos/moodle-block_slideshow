<?php
global $CFG;
class block_slideshow extends block_base {

 
    public function get_required_javascript() {
        parent::get_required_javascript();
 
        $this->page->requires->jquery();
        $this->page->requires->jquery_plugin('ui');
        $this->page->requires->jquery_plugin('ui-css');
        $this->page->requires->js('/blocks/slideshow/js/cycle.js');
        $this->page->requires->js('/blocks/slideshow/js/spectrum.js');
    }

	public function init() {
		$this->title = get_string('slideshow', 'block_slideshow');
		$this->height = '200';
		$this->transition = 'fade';
		$this->slidedelay = '4000';
		$this->slidespeed = '1000';
		$this->background = '000000';
		$this->transparent = '0';
		$this->text = 'default text (mine)';
	}
	
  	public function specialization() { 
  		$this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('slideshow', 'block_slideshow'));
   		$this->height = isset($this->config->height) ? format_string($this->config->height) : '200';
		$this->transition = isset($this->config->transition) ? format_string($this->config->transition) : 'fade';
		$this->slidedelay = isset($this->config->slidedelay) ? format_string($this->config->slidedelay) : '4000';
		$this->slidespeed = isset($this->config->slidespeed) ? format_string($this->config->slidespeed) : '1000';
		$this->background = isset($this->config->background) ? $this->config->background : '#000000';
		$this->transparent = isset($this->config->transparent) ? $this->config->transparent : '0';
   		$this->text = isset($this->config->text) ? format_string($this->config->text) : 'default';
	}
	

	
	public function get_content() { 
		global $CFG, $DB;        
        require_once($CFG->libdir . '/filelib.php');
        $this->page->requires->js('/blocks/slideshow/js/cycle.js');
        $this->page->requires->js('/blocks/slideshow/js/spectrum.js');

		if ($this->content !== null) {
    		return $this->content;
  		}
		$this->content = new stdClass;
		if (!empty($this->config->height)) {
	    	$this->content->height = $this->config->height;
		} else {
			$this->content->height = '200';
		}
		
		if (!empty($this->config->text)) {
	    	$this->content->text = $this->config->text;
		} else {
			$this->content->text = '';
		}
		
		if (!empty($this->config->background)) {
	    	$this->content->background = $this->config->background;
		} else {
			$this->content->background = '#000000';
		}
		
		if (!empty($this->config->transparent)) {
	    	$this->content->transparent = $this->config->transparent;
		} else {
			$this->content->transparent = '';
		}
		
		if (!empty($this->config->transition)) {
	    	$this->content->transition = $this->config->transition;
		} else {
			$this->content->transition = 'fade';
		}
		
		if (!empty($this->config->slidedelay)) {
	    	$this->content->slidedelay = $this->config->slidedelay;
		} else {
			$this->content->slidedelay = '4000';
		}
		
		if (!empty($this->config->slidespeed)) {
	    	$this->content->slidespeed = $this->config->slidespeed;
		} else {
			$this->content->slidespeed = '1000';
		}
		
			$this->content->text = '<div id="page-slideshow">';
		
			file_rewrite_pluginfile_urls($this->content->text, 'pluginfile.php', $this->context->id, 'block_slideshow', 'content', NULL);
			
			$table = 'files';
			$select = "component = 'block_slideshow' AND contextid = '" . $this->context->id ."' AND filename != '.'";
			$fields = 'filename';
			$sort = 'filename';
			$images = $DB->get_records_select($table, $select, NULL, $sort, $fields);
			
			foreach ($images as $image) {
			
				$imagefile = $image->filename;
				$url = $CFG->wwwroot . '/pluginfile.php/' . $this->context->id . '/block_slideshow/content/' . $imagefile;
				
				$this->content->text .= '<div class="slide"><img src="' . $url . '" /></div>' ;
			}
			
			$this->content->text .= '</div><div style="clear:both;"> </div>';
			
			if($this->content->transparent) {
				$ssbackground = 'transparent';
			} else {
				$ssbackground = $this->content->background;
			}
			
			
			$node = 'page-header';
			
			$currenttheme = $CFG->theme;
			
			switch ($currenttheme) {
				default:
					$node = 'page-header';
				break;
				case 'fusion':
				case 'nimble':
					$node = 'region-header';
				break;
				case 'magazine':
					$node = 'textcontainer-wrap';
				break;
				case 'overlay':
					$node = 'newheader';
				break;
				case 'splash':
					$node = 'page-header-wrapper';
				break;
			}
			
			$script = "
				<script type=\"text/javascript\">
					$(document).ready(function() {
    					$('#page-slideshow').cycle({
							fx: '" . $this->content->transition . "',
							speed: " . $this->content->slidespeed . ",
							height: '" . $this->content->height . "',
							width: '100%',
							timeout: " . $this->content->slidedelay . ",
							fit: 1
						}); 
						$('#inst" . $this->instance->id . "') .appendTo('#" . $node . "');
						$('#page-header').css('height', 'auto');
						$('#page-slideshow').width($('#inst" . $this->instance->id . "').width());
						$('#page-slideshow').css('background', '" . $ssbackground . "');
						$('.block.block_slideshow .content #page-slideshow').css('height', '" . $this->content->height . "px');
						$('.block.block_slideshow .content #page-slideshow img').css('height', '" . $this->content->height . "px');
					});
					
				</script>
			";
			
			$this->content->text .= $script; // . "<h1>" . $CFG->theme . "</h1>";
			
			

  		return $this->content;
  	}
  	public function is_empty() {
		$this->get_content();
		return(empty($this->content->text) && empty($this->content->footer));
	}
	function has_config() {
		return true;
	}
	public function instance_allow_multiple() { 
		return false;
	}
	
/*	public function instance_config_save($data) {

  		if(get_config('slideshow', 'Allow_HTML') == '1') {
    		$data->text = strip_tags($data->text);
  		}
  		return parent::instance_config_save($data);
	}*/
	public function hide_header() {
		if($this->title == '') {
	  		return true;
	  	}
	}
	public function html_attributes() {
    	$attributes = parent::html_attributes(); // Get default values
    	$attributes['class'] .= ' block_'. $this->name(); // Append our class to class attribute
    	return $attributes;
	}
	public function applicable_formats() {
  		return array(
  			'site' => true,
  			'course-view' => true
  		);
	}
}