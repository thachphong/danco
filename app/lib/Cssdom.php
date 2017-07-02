<?php
class Cssdom_lib
{     
	private $xpath ;
    private $sdom ;
    private $log;
    private $filename ;
    
    function __construct($file_name='')
    {
    	$file = new FilePHP_lib();
    	if($file->FolderExists(PUBLIC_TEMPLATE_PATH.'/css')==FALSE){
			$file->CreateFolder(PUBLIC_TEMPLATE_PATH.'/css');
		}
        if(strlen($file_name) > 0){                       
            $this->filename = $file_name;
        }
    }
    public function Load($file_name){
        $this->filename = $file_name;
    }
    
    public function set_style($obj_name,$this_style,$new_flg = FALSE ){
    	if($new_flg){
			if(file_exists($this->filename)){
				unlink($this->filename);
			}
		}
		$list = $this_style->convert();
		$str= $obj_name.'{';
		foreach($list as $key=>$val){
			$str .= $key.": ".$val.";";
		}
		$str .='}';
		$file = new FilePHP_lib();
		$file->write_file($this->filename,$str);
	}
	public function append_style($str){
		$file = new FilePHP_lib();
		$file->write_file($this->filename,$str);
	}
	public function create_icon($icon_name,$icon_val){
		$style = new Cssstyle_lib();
		$style->font_family = 'FontAwesome';
		$style->content = $icon_val;//'\f137';
		$style->font_style = 'normal';
		$style->font_weight = 'normal';
		$style->text_decoration = 'inherit';
		$style->padding_right = '0.5em';
		$style->float = 'right';
		$this->set_style($icon_name,$style);
	}
	public function set_style_mobile($obj_name,$this_style,$new_flg = FALSE){
		$file_name = str_replace('.css','_mobi.css', $this->filename);
		//var_dump($this->filename);
		//var_dump($file_name);
		$str= "";
		if($new_flg){			
			$str ="@media only screen and (max-width: 768px){";
		}else{
			if(!file_exists($file_name)){
				$str ="@media only screen and (max-width: 768px){";
			}else{
				$str = file_get_contents($file_name);
				$str = str_replace('}}','}',$str);
			}
		}
		
		$list = $this_style->convert();
		$str .= $obj_name.'{';
		foreach($list as $key=>$val){
			$str .= $key.": ".$val.";";
		}
		$str .='}}';
		if(file_exists($file_name)){
			unlink($file_name);
		}
		$file = new FilePHP_lib();
		$file->write_file($file_name,$str);
	}
	public function set_menu_style($param){
		$background_1 = $param['background1'];
		//$background_hover_1 = $param['background_hover1'];
		//$color_hover_1 = $param['color_hover1'];
		$background_2 = $param['background2'];
		
		$style = new Cssstyle_lib();
		$style->padding =0;
		$style->margin  =0;
		$style->list_style = 'none';
		$style->background = $background_1;//'#1bc2a2';	
		if(isset($param['font_family'])){
			$style->font_family = $param['font_family'];
		}
		if(isset($param['font_size'])){
			$style->font_size = $param['font_size'].'px';
		}
		$style->z_index = 99;
		//$style->font_family ="Tahoma,Arial,Times New Roman";		
		$this->set_style('#menu_top ul',$style);
		$style = new Cssstyle_lib();
		$style->display="block";
		$style->position = "relative";
		$style->float = "left";
		$style->background =$background_1;// '#1bc2a2';
		$this->set_style('#menu_top ul li',$style);
		$style = new Cssstyle_lib();
		$style->display ='none';
		$this->set_style('#menu_top li ul',$style);
		$style = new Cssstyle_lib();
		$style->display = 'block';
		//$style->padding = '1em';
		$style->color = '#fff';		
		$style->white_space="nowrap";
		$style->text_decoration="none";	
		$style->line_height ='58px';	
		$style->padding_left = '10px';
		$style->padding_right = '10px';
		$this->set_style('#menu_top ul li a',$style);
		$style = new Cssstyle_lib();
		if(isset($param['background_hover1'])){
			$style->background = $param['background_hover1'];//'#2c3e50';	
		}
		if(isset($param['color_hover1'])){
			$style->color = $param['color_hover1'];
		}
		
		$this->set_style('#menu_top ul li a:hover',$style);	
			
		$style = new Cssstyle_lib();
		$style->display="block";
		$style->position="absolute";
		$this->set_style('#menu_top li:hover > ul',$style);
		$style = new Cssstyle_lib();
		$style->float='none';
		$this->set_style('#menu_top li:hover li',$style);
		/*$style = new Cssstyle_lib();
		$style->background =$background_1;//'#1bc2a2';		 
		$this->set_style('#menu_top li:hover a',$style);*/
		$style = new Cssstyle_lib();
		$style->background = $background_2;//'#1bc2a2';	
		$style->line_height = '40px';	 
		$this->set_style('#menu_top li:hover ul li a',$style);
		
		$style = new Cssstyle_lib();
		if(isset($param['background_hover2'])){
			$style->background = $param['background_hover2'];//'#2c3e50';	
		}		 
		//$style->background ="#2c3e50";
		$style->color ='#fff';		
		$this->set_style('#menu_top li:hover li a:hover',$style);
		$style = new Cssstyle_lib();
		$style->border_top =0;
		$this->set_style('##menu_top .dropDownMenu li ul li',$style);
		$style = new Cssstyle_lib();
		$style->left = '100%';
		$style->top ="0";
		$this->set_style('#menu_top ul ul ul',$style);
		$style = new Cssstyle_lib();
		$style->display = 'table';		
		$this->set_style('#menu_top ul:before,#menu_top ul:after',$style);
		$style = new Cssstyle_lib();
		$style->clear ='both';
		$this->set_style('#menu_top ul:after',$style);
		$style = new Cssstyle_lib();
		$style->height= '60px';
		$style->background = $background_1;	
		if(isset($param['background_hover1'])){		
			$style->border_bottom="2px solid ".$param['background_hover1'];	
			$style->padding_bottom="2px";
		}
		
		$this->set_style('#menu_top',$style);
	}
}
