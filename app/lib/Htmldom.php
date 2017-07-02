<?php

require_once ACW_APP_DIR . '/vendor/simple_html_dom.php';

class Htmldom_lib
{     
	private $xpath ;
    private $sdom ;
    private $log;
    private $filename ;
    function __construct($file_name='')
    {
        if(strlen($file_name) > 0){            
            $this->sdom = file_get_html($file_name);
            $this->filename = $file_name;
        }
    }
    public function Load($file_name){
        if(strlen($file_name) > 0){            
            $this->sdom = file_get_html($file_name);
            $this->filename = $file_name;
        }
        if( $this->sdom  != FALSE){
			return TRUE;
		}
		return FALSE;
    }
    public function reload(){
		$this->sdom = str_get_html($this->sdom->save());
	}
    public function add_content_section($total_section){
    	$element = $this->sdom->find('//div[@id="content"]',0);
		for($i = 1;$i<=$total_section;$i++){
			if($element != NULL){
				$element->innertext .= '<div class="row" id="section_'.$i.'"></div>';
			}
		}
		
		//$this->sdom->save();
		$this->save();
	}
	public function split_column_section($object_id,$total_column,$main_position = 'left'){
		$this->reload();
		$element = $this->sdom->find('//div[@id="'.$object_id.'"]',0);
		if($total_column == 2 && $main_position =='left'){
			$element->innertext .= '<div class="col-md-9" id="'.$object_id.'_col_1"></div>';
			$element->innertext .= '<div class="col-md-3" id="'.$object_id.'_col_2"></div>';
		}else if($total_column == 2 && $main_position =='right'){
			$element->innertext .= '<div class="col-md-3" id="'.$object_id.'_col_1"></div>';
			$element->innertext .= '<div class="col-md-9" id="'.$object_id.'_col_2"></div>';
		}else{
			$element->innertext .= '<div class="col-md-3" id="'.$object_id.'_col_1"></div>';
			$element->innertext .= '<div class="col-md-6" id="'.$object_id.'_col_2"></div>';
			$element->innertext .= '<div class="col-md-9" id="'.$object_id.'_col_3"></div>';
		}
		
		$this->save();
	}
	public function split_column($object_id,$total_column){
		$this->reload();
		$element = $this->sdom->find('//div[@id="'.$object_id.'"]',0);
		$colum_class = 12/$total_column;
		for($i = 1;$i<=$total_column;$i++){
			if($element != NULL){
				$element->innertext .= '<div class="col-md-'.$colum_class.'" id="'.$object_id.'_col_'.$i.'"></div>';
			}
		}
		//$this->sdom->save();
		$this->save();
	}
	
    public function set_html($obj_id, $str_html){
		$element = $this->sdom->find('//div[@id="'.$obj_id.'"]',0);
        if($element != NULL){
			return $element->innertext = $str_html;
		}
		$this->sdom->save();
	}
	public function append_html($obj_id, $str_html){
		$this->reload();
		$element = $this->sdom->find('//div[@id="'.$obj_id.'"]',0);
        if($element != NULL){
			return $element->innertext .= $str_html;
		}
		$this->sdom->save();
	}
	public function add_cssfile($file_name){
		$element = $this->sdom->find('//head',0);
        if($element != NULL){
			$element->innertext .= '<link href="'.$file_name.'" rel="stylesheet" type="text/css">';
		}
		$this->sdom->save();
		$this->save();
	}
	public function add_jsfile($file_name){
		$element = $this->sdom->find('//head',0);
        if($element != NULL){			
			$element->innertext .= '<script type="text/javascript" src="'.$file_name.'"></script>';
		}
		$this->sdom->save();
		$this->save();
	}
	public function save($file_name=''){
		if($file_name==''){
			$this->sdom->save($this->filename);
		}else{
			$this->sdom->save($file_name);
		}
	}
    
    public function add_slides($obj_id,$width,$height,$speed){
    	$style="width:$width; height:$height";
		$html ='<div id="myCarousel" class="carousel <!--slide-->" data-ride="carousel" data-interval="'.$speed.'">
					<%assign var="slides" value=Slides_model::get_slides()%>
				    <!-- Indicators -->
				    <ol class="carousel-indicators">
				      <%foreach $slides as $key=>$item%>
				      	  <li data-target="#myCarousel" data-slide-to="<%$key%>" <%if $key==0%>class="active"<%/if%>></li>
				      <%/foreach%>
				    </ol>

				    <!-- Wrapper for slides -->
				    <div class="carousel-inner">
				    	<%foreach $slides as $key=>$item%>
					      <div class="item <%if $key==0%> active<%/if%>">
					        <img src="<%$smarty.const.ACW_BASE_URL%><%$item.img_path%>" alt="" style="'.$style.';">
					      </div>
					    <%/foreach%>
				    </div>

				    <!-- Left and right controls -->
				    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
				      <span class="glyphicon glyphicon-chevron-left"></span>
				      <span class="sr-only">Previous</span>
				    </a>
				    <a class="right carousel-control" href="#myCarousel" data-slide="next">
				      <span class="glyphicon glyphicon-chevron-right"></span>
				      <span class="sr-only">Next</span>
				    </a>
				  </div>';
		$this->append_html($obj_id,$html);
		$this->save();
		
		/*$css_file = PUBLIC_TEMPLATE_PATH.'/css/style.css';
		$css = new Cssdom_lib($css_file);
		$css->create_icon('.fa-chevron-circle-left','"\f137"');
		$css->create_icon('.fa-chevron-circle-right','"\f138"');*/
		
	}
    function to_slug($str) {
    	$str = html_entity_decode($str );
	    $str = trim(mb_strtolower($str));
	    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
	    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
	    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
	    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
	    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
	    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
	    $str = preg_replace('/(đ)/', 'd', $str);
	    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
	    $str = preg_replace('/([\s]+)/', '-', $str);
	    return $str;
	}
	public function create_basic_html($file_name){
		//$file_name ="index.html";
		$str ='<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					</head>
					<body>
						<div class="row">
							<div class="container" id="header"></div>
						</div>
						<div class="row"><div class="container" id="content"></div></div>
						<div class="row"><div class="container" id="footer"></div></div>
					</body>
				</html>';
		if(file_exists($file_name)){
			unlink($file_name);		
		}			
		ACWLog::debug_var('file',$file_name);	
		$file = new FilePHP_lib();
		$file->write_file($file_name,$str);
		if(strlen($file_name) > 0){            
            $this->sdom = file_get_html($file_name);
            $this->filename = $file_name;
        }
        if( $this->sdom  != FALSE){
			return TRUE;
		}
		return FALSE;
	}
	public function add_menu($tyle){
		$ul ='<%assign var="menus" value=Menu_model::get_menus()%>
			<div id="menu_top">
			<ul class="dropDownMenu">
			<%foreach $menus as $key=>$item%>                        
                <%if $item.child_flg > 0%>
				 <li><a href="<%if strlen($item.link)>0%><%$smarty.const.ACW_BASE_URL%><%if $item.page_flg==1%>page/v/<%$item.link%><%else if $item.page_flg==2%>danhmuc/ds/<%$item.link%><%else if $item.page_flg==3%>tintuc/ds/<%$item.link%><%/if%><%else%>#<%/if%>"><%$item.menu_name%></a>
					<ul>
					<%foreach $item.child as $sub1%>
                    	<%if $sub1.child_flg > 0%>					
						<li><a href="<%if strlen($item.link)>0%><%$smarty.const.ACW_BASE_URL%><%if $sub1.page_flg==1%>page/v/<%$sub1.link%><%else if $sub1.page_flg==2%>danhmuc/ds/<%$sub1.link%><%else if $sub1.page_flg==3%>tintuc/ds/<%$sub1.link%><%/if%><%else%>#<%/if%>"><%$sub1.menu_name%></a>
							<ul>
								<%foreach $sub1.child as $sub2%>
									<li><a href="<%$smarty.const.ACW_BASE_URL%><%if $sub2.page_flg==1%>page/v/<%$sub2.link%><%else if $sub2.page_flg==2%>danhmuc/ds/<%$sub2.link%><%else if $sub2.page_flg==3%>tintuc/ds/<%$sub2.link%><%/if%>"><%$sub2.menu_name%></a></li>
								<%/foreach%>
							</ul>
						</li>
						<%else%>
							<li><a href="<%$smarty.const.ACW_BASE_URL%><%if $sub1.page_flg==1%>page/v/<%$sub1.link%><%else if $sub1.page_flg==2%>danhmuc/ds/<%$sub1.link%><%else if $sub1.page_flg==3%>tintuc/ds/<%$sub1.link%><%/if%>"><%$sub1.menu_name%></a></li>
						<%/if%>
					<%/foreach%>
					</ul>
				 </li>
				<%else%>
					 <li><a href="<%$smarty.const.ACW_BASE_URL%><%if $item.page_flg==1%>page/v/<%$item.link%><%else if $item.page_flg==2%>danhmuc/ds/<%$item.link%><%else if $item.page_flg==3%>tintuc/ds/<%$item.link%><%/if%>"><%$item.menu_name%></a></li>
				<%/if%>
			<%/foreach%>';
		//}
		$ul .="</ul></div>";
		$this->append_html('header',$ul);
		$this->save();
		$css_file = PUBLIC_TEMPLATE_PATH.'/css/style.css';
		$css = new Cssdom_lib($css_file);
		$css->set_menu_style($tyle);
		//return $ul;
		return TRUE;
	}
	public function add_logo($new_line = TRUE){
		if($new_line){
			$str='<div class="row" >
					  <img src="<%$smarty.const.ACW_BASE_URL%>shared/template1/images/logo.png" />
         		  </div>';
			$this->append_html("header",$str);
			$this->save();
		}
	}
}
