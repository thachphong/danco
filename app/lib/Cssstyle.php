<?php
class Cssstyle_lib
{     
	public $background;
	public $background_color;
	public $background_image;
	public $color;
	public $border;
	public $border_top;
	public $border_left;
	public $border_right;
	public $border_bottom;
	public $font;
	public $font_size;
	public $font_family;
	public $font_weight;
	public $font_style;
	public $position;
	public $display;
	public $padding;
	public $padding_left;
	public $padding_top;
	public $padding_right;
	public $padding_bottom;	
	public $margin;
	public $margin_left;
	public $margin_top;
	public $margin_right;
	public $margin_bottom;
	public $height;
	public $width;
	public $float;
	public $index;
	public $line_height;
	public $top;
	public $left;
	public $clear;
	public $list_style;
	public $white_space;
	public $text_decoration;
	public $z_index;
	public $content;
	private $arr_elem= array('background'=>'background',
							 'background_color'=>'background-color',
							 'background_image'=>'background-image',
							 'color'=>'color',
							 'border'=>'border',
							 'border_left'=>'border-left',
							 'border_top'=>'border-top',
							 'border_right'=>'border-rigth',
							 'border_bottom'=>'border-bottom',
							 'font'=>'font',
							 'font_size'=>'font-size',
							 'font_family'=>'font-family',
							 'font_weight'=>'font-weight',
							 'font_style'=>'font-style',
							 'position'=>'position',
							 'display'=>'display',
							 'padding'=>'padding',
							 'padding_left'=>'padding-left',
							 'padding_top'=>'padding-top',
							 'padding_right'=>'padding-right',
							 'padding_bottom'=>'padding-bottom',
							 'margin'=>'margin',
							 'margin_left'=>'margin-left',
							 'margin_top'=>'margin-top',
							 'margin_right'=>'margin-right',
							 'margin_bottom'=>'margin-bottom',
							 'height'=>'height',
							 'width'=>'width',
							 'float'=>'float',
							 'index'=>'index',
							 'line_height'=>'line-height',
							 'top'=>'top',
							 'left'=>'left',
							 'list_style'=>'list-style',
							 'clear'=>'clear',
							 'white_space'=>'white-space',
							 'text_decoration'=>'text-decoration',
							 'z_index'=>'z-index',
							 'content'=>'content',
	);
	function __construct($style=array())
    {
        if(count($style) > 0){            
            foreach($style as $key=>$val){
            	$this->$key = $val;
            }
        }
    }
	public function convert(){
		$result= array();
		foreach($this->arr_elem as $key=>$val){
			if(strlen($this->$key)>0){
				$result[$val] = $this->$key;
			}
		}
		return $result;
	}
}
