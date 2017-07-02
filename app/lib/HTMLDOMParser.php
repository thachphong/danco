<?php

require_once ACW_APP_DIR . '/vendor/simple_html_dom.php';

class HTMLDOMParser_lib {

    public function get_content_body($url, $plaintext = FALSE) {
        try {
            $html = file_get_html($url);
            $e = $html->find('body', 0);
            if ($plaintext == TRUE) {
                $content = $e->plaintext;
            } else {
                $content = $e->outertext;
            }
            return $content;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
    }

    public static function import_replace_image_html($html_path, $data_tags) {
        try {

            $e = file_get_contents($html_path);
            $html = str_get_html($e);
            foreach ($html->find('td') as $dom) {
                //foreach ($data_tags as $value) {

                $link = isset($data_tags['Link']) ? $data_tags['Link'] : '';
                $tags = isset($data_tags['tags']) ? $data_tags['tags'] : '';
                $free_id = '';
                if (isset($data_tags['SectionId']) && !is_numeric($data_tags['SectionId'])) {
                    $free_id = $data_tags['SectionId'];
                }

                if ($link != '' && $tags != '') {
                    $tags = htmlspecialchars($tags, ENT_QUOTES, "UTF-8");

                    $repl = "<img src='" . $link . "' data-free='" . $free_id . "' alt= '' style='max-width:200px; height: auto' />";
                    preg_match_all('/(http|https):\/\/[^ ]/', $dom->innertext, $out);
                    $count = isset($out[0]) ? count($out[0]) : 0;

                    preg_match_all('/(http|https):\/\/[^ ]/', $link, $outLink);
                    $countLink = isset($outLink[0]) ? count($outLink[0]) : 0;

                    if ($count == 0 && $countLink != 0) {
                        //Add Start LIXD-10 hungtn VNIT 20151026
                        $value_no = $dom->innertext;
                        $value_no = explode("<span>", $value_no);
                        $value_no = explode("</span>", $value_no[1]);
                        $value_no = str_replace("&lt;", "<", $value_no[0]);
                        $value_no = str_replace("&gt;", ">", $value_no);
                        
						$br = array('<br>', '<br />', '<br/>');
						$value_no = str_replace($br, '++BR++', $value_no);//Add - LIXD-10 - TrungVNIT - 2015/18/11
						
                        preg_match("/[<]+[(\s|\S)*]+[>]/", $value_no, $output_array); //Edit LIXD-10 hungtn VNIT 20151026
                        if(!isset($output_array[0])){
                            preg_match("/[<]+[(\s|\S)*]+[>]+[\w*]/", $value_no, $output_array); //Edit LIXD-10 hungtn VNIT 20151026
                        }
						
                        $count_arow1 =  substr_count($value_no, "<");
                        $count_arow2 =  substr_count($value_no, ">");
                        
                        if(isset($output_array[0])){
                            $tmp_text = explode("<", $output_array[0]); //Edit LIXD-361 hungtn VNIT 20151119
							if(isset($tmp_text[1])){
								$tmp_text = explode(">", $tmp_text[1]);
								$text_check = trim($tmp_text[0]);
								if (preg_match("/^[0-9]*$/",$text_check) && !empty($text_check)) {
									if($count_arow1 > 1 && $count_arow2 > 1) {

									} else {
										$tag_compare = $tags;
										$tag_compare = str_replace("&lt;", "<", $tag_compare);
										$tag_compare = str_replace("&gt;", ">", $tag_compare);
										$value_no = "<".$tmp_text[0].">";
										$value_no = str_replace('++BR++', '<br />', $value_no);//Add - LIXD-10 - TrungVNIT - 2015/18/11
										if($tag_compare == $value_no) {
											$dom->innertext = str_replace($tags, $repl, trim($dom->innertext));
										}
									}
								}
							}
                        }
                        //Add End LIXD-10 hungtn VNIT 20151026
                    }
                }
                //}
            }
            $html->save($html_path);
            return $html->outertext;
        } catch (Exception $exc) {
            
        }
    }

    public function replace_image_html($html_old, $url, $data_img, $param) {
        try {
            $e = file_get_contents($html_old);
            $html = str_get_html($e);
            foreach ($html->find('td') as $dom) {
                foreach ($data_img as $value) {
                    $exp = explode('|', $value);
                    $link = $exp[0];
                    $tags = trim(htmlentities($exp[1], ENT_QUOTES, "UTF-8"));
                    $name = $exp[2];
                    $section_id = $exp[3]; //Edit Start LIXD-321 hungtn VNIT 20151109
                    if ($link != 'empty' && $name != 'empty' && $tags != 'empty' && $link != '') {
                        //Add Start LIXD-357 hungtn VNIT 20151123
                        $extension = explode(".", $name);
                        $count_tmp = count($extension);
                        $extension = isset($extension[$count_tmp -1]) ? $extension[$count_tmp -1]:'';
                        if($extension == 'xlsx') {
                            continue;
                        }
                        //Add End LIXD-357 hungtn VNIT 20151123
                        $free_id = HTMLDOMParser_lib::get_free_id_from_url($link);
                        if(!is_numeric($section_id)) {
                            $free_id = $section_id;
                        }//Edit End LIXD-321 hungtn VNIT 20151109
                        $repl = "<img src='" . $link . "' data-free='" . $free_id . "' alt= '' style='max-width:200px; height: auto' />";
                        preg_match_all('/(http|https):\/\/[^ ]/', $dom->innertext, $out);
                        $count = isset($out[0]) ? count($out[0]) : 0;

                        preg_match_all('/(http|https):\/\/[^ ]/', $link, $outLink);
                        $countLink = isset($outLink[0]) ? count($outLink[0]) : 0;

                        if ($count == 0 && $countLink != 0) {

                            //Add Start LIXD-10 hungtn VNIT 20151026
                            $value_no = $dom->innertext;
                            $value_no = explode("<span>", $value_no);
                            $value_no = explode("</span>", $value_no[1]);
                            $value_no = str_replace("&lt;", "<", $value_no[0]);
                            $value_no = str_replace("&gt;", ">", $value_no);
							
                            $br = array('<br>', '<br />', '<br/>');
							$value_no = str_replace($br, '++BR++', $value_no);//Add - LIXD-10 - TrungVNIT - 2015/18/11
							
                            preg_match("/[<]+[(\s|\S)*]+[>]/", $value_no, $output_array); //Edit LIXD-10 hungtn VNIT 20151026
                            if(!isset($output_array[0])){
                                preg_match("/[<]+[(\s|\S)*]+[>]+[\w*]/", $value_no, $output_array); //Edit LIXD-10 hungtn VNIT 20151026
                            }
							
                            $count_arow1 =  substr_count($value_no, "<");
                            $count_arow2 =  substr_count($value_no, ">");
                            
                            if(isset($output_array[0])){
                                $tmp_text = explode("<", $output_array[0]);//Edit LIXD-361 hungtn VNIT 20151119
								if(isset($tmp_text[1])){
									$tmp_text = explode(">", $tmp_text[1]);
									$text_check = trim($tmp_text[0]);
									if (preg_match("/^[0-9]*$/",$text_check) && !empty($text_check)) {
										if($count_arow1 > 1 && $count_arow2 > 1) {

										} else {
											$tag_compare = $tags;
											$tag_compare = str_replace("&lt;", "<", $tag_compare);
											$tag_compare = str_replace("&gt;", ">", $tag_compare);
											$value_no = "<".$tmp_text[0].">";

											$value_no = str_replace('++BR++', '<br />', $value_no);//Add - LIXD-10 - TrungVNIT - 2015/18/11

											if($tag_compare == $value_no) {
												$dom->innertext = str_replace($tags, $repl, trim($dom->innertext));
											}
										}
									}
								}
                            }
                            //Add End LIXD-10 hungtn VNIT 20151026
                        }
                    }
                }
            }

            $html->save($url);
            return $html->outertext;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    protected static function get_free_id_from_url($url) {
        $free_id = '';
        $exp = explode('/', $url);
        if (strpos($url, 'itemfile/viewtmpfree') != FALSE || strpos($url, 'itemfile/yoyakuviewtmpfree') != FALSE) {
            return $exp[count($exp) - 1];
        } else {
            if (strpos($url, 'itemfile/viewtmp') != FALSE || strpos($url, 'itemfile/yoyakuviewtmp') != FALSE) {
                foreach ($exp as $key => $value) {
                    if ($value == 'viewtmp' || $value == 'yoyakuviewtmp') {
                        $exp2 = explode('_', $exp[$key + 1]);
                        if (!is_numeric($exp2[0]) && count($exp2) > 1) {
                            return $exp2[0];
                        }
                    }
                }
            }
        }
        return $free_id;
    }

    public function replace_src_img($url, $t_series_head_id, $t_series_mei_id, $yoyaku_flg = null, $free_id = FALSE, $list_free_id) {
        try {
            $html = file_get_html($url);
            if (!empty($html)) {//Add LIXD-327 MinhVnit 2015/10/09
	            foreach ($html->find('img') as $dom) {
	                $src = $dom->src;
	                $att = 'data-free';
	                $data_free = $dom->$att;
	                $filename = Series_model::get_image_name_viewtmp($src);
	                if ($yoyaku_flg == 1) {
	                    if ($data_free != '') {
	                        $v = 'yoyakuviewfree/' . $data_free;
	                    } else {
	                        $v = 'yoyakuview';
	                    }
	                } else {
	                    if ($data_free != '') {
	                        if ($list_free_id != '' & count($list_free_id) > 0) {
	                            if (isset($list_free_id[$data_free])) {
	                                $data_free = $list_free_id[$data_free];
	                            }
	                        }

	                        $v = 'viewfree/' . $data_free;
	                    } else {
	                        $v = 'view';
	                    }
	                }

	                $exp = explode('_', $filename);
	                if (count($exp) > 1) {
	                    $filename = $exp[count($exp) - 1];
	                }
	                $path_src = ACW_BASE_URL . 'itemfile/' . $v . '/' . $filename . '/' . $t_series_head_id . '/' . $t_series_mei_id . '/s';
	                $dom->src = $path_src;
	            }
            
	            $html->save($url);
	            $html->clear();
            }
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    protected static function yoyaku_get_img($string) {
        $exp = explode('_', $string);
        return $exp[count($exp) - 1];
    }

    protected function html_file_list($list) {
        $exp = explode('|', $list);
        $array = array_unique(array_filter($exp));
        $data = array();
        foreach ($array as $val) {
            $data[] = Itemfile_model::replace_name_uploaded($val, 'html');
        }
        return $data;
    }

    public static function replace_image_uploaded($sec_no, $html_file_list, $tmp_name, $name_old, $name_new, $yoyaku_flg = '') {
        try {
            $html = new HTMLDOMParser_lib();
            $list = $html->get_all_path_uploaded($tmp_name, $yoyaku_flg, '');

            $listCurrent = $html->html_file_list($html_file_list);

            foreach ($list as $path) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if ($ext == 'html') {
                    $html->change_image_name_html($listCurrent, $path, $name_old, $name_new, $tmp_name, $sec_no, $yoyaku_flg);
                }
                if ($ext == 'xlsx' || $ext == 'xls') {
                    $html->change_image_name_excel($path, $name_old, $name_new);
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    public function get_all_path_uploaded($tmp_name, $yoyaku_flg = '', $path_data = '') {
        try {
            if ($tmp_name != '') {
                if ($yoyaku_flg == 1) {
                    $pathlib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
                } else {
                    $pathlib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
                }
                $pathlib->combine($tmp_name);
                $tmp_path = $pathlib->get_full_path();
            } else {
                $tmp_path = $path_data;
            }


            $file = new File_lib();
            $list = $file->FileFolderList($tmp_path);
            $data = array();
            foreach ($list as $key => $value) {
                if (!is_array($value) && strpos($value, '_uploaded.') != FALSE) {
                    $path = $tmp_path . '/' . $value;
                    if ($file->FileExists($path) == TRUE) {
                        $data[] = $path;
                    }
                }
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if (!is_array($val) && strpos($val, '_uploaded.') != FALSE) {
                            $path = $tmp_path . $key . '/' . $val;
                            if ($file->FileExists($path) == TRUE) {
                                $data[] = $path;
                            }
                        }
                    }
                }
            }
            $data = array_unique($data);
            return $data;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    protected function change_image_name_html($listCurrent, $html_path, $name_old, $name_new, $tmp_name, $sec_no, $yoyaku_flg) {
        try {
            $name_path = basename($html_path);
            if (!in_array($name_path, $listCurrent)) {
                $content = file_get_contents($html_path);
                $content = str_replace($name_old, $name_new, $content);
                file_put_contents($html_path, $content);
            } else {
                $html = file_get_html($html_path);
                if (!empty($html)) {//Add LIXD-327 MinhVnit 2015/10/09
	                foreach ($html->find('img') as $dom) {
	                    $src = isset($dom->src) ? $dom->src : '';
	                    $df = 'data-free';
	                    $data_free = isset($dom->$df) ? $dom->$df : '';

	                    if ($yoyaku_flg == 1) {
	                        if ($src != '' && strpos($src, $name_old) != FALSE) {
	                            if ($data_free == '') {
	                                $v = 'yoyakuviewtmp';
	                                $path_src = ACW_BASE_URL . 'itemfile/' . $v . '/' . $name_new . '/' . $tmp_name . '/s';
	                            } else {
	                                $v = 'yoyakuviewtmp';
	                                $path_src = ACW_BASE_URL . 'itemfile/' . $v . '/' . $name_new . '/' . $tmp_name . '/s';
	                            }
	                            $dom->src = $path_src;
	                        }
	                    } else {
	                        if ($src != '' && strpos($src, $name_old) != FALSE) {
	                            if ($data_free == '') {
	                                $v = 'viewtmp';
	                                $path_src = ACW_BASE_URL . 'itemfile/' . $v . '/' . $name_new . '/' . $tmp_name . '/s';
	                            } else {
	                                $v = 'viewtmpfree';
	                                $path_src = ACW_BASE_URL . 'itemfile/viewtmp/' . $name_new . '/' . $tmp_name . '/s';
	                            }
	                            $dom->src = $path_src;
	                        }
	                    }
	                    
	                }
	                $html->save($html_path);
	                $html->clear();
				}
            }
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    protected function change_image_name_excel($excel_path, $name_old, $name_new) {
        try {
            $excel = new ImportExport_lib();
            if ($excel->load($excel_path) === false) {
                ACWLog::debug_var('LIXD-10', 'Can not open excel file: ' . $excel_path);
                return FALSE;
            }
            $total_column = $excel->get_total_column();
            $total_row = $excel->get_total_row();
            for ($i = 0; $i < $total_column; $i++) {
                $col = $i;
                for ($r = 0; $r < $total_row; $r++) {
                    $row = $r + 1;
                    $value_no = trim($excel->get_value_no($col, $row));
                    $cel_data = str_replace($name_old, $name_new, $value_no);
                    $excel->set_value_no($col, $row, $cel_data);
                }
            }
            $excel->save($excel_path);
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    public function delete_image_html($tmp_name, $filename, $yoyaku_flg = '') {
        try {
            $html = new HTMLDOMParser_lib();
            $list = $html->get_all_path_uploaded($tmp_name, $yoyaku_flg);
            foreach ($list as $path) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if ($ext == 'html') {
                    $content = file_get_contents($path);
                    $content = str_replace($filename, 'del-' . $filename, $content);
                    file_put_contents($path, $content);
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

    public function replace_to_yoyaku($mei_id_list) {
        try {
            foreach ($mei_id_list as $mei) {
                $dst_head_id = $mei['dst_t_yoyaku_series_lang_id'];
                $dst_mei_id = $mei['dst_t_series_mei_id'];

                $path_lib = new Path_lib(AKAGANE_YOYAKU_STRAGE_PATH);
                $path_lib->combine('head_' . sprintf('%010d', $dst_head_id));
                $path_lib->combine('mei_' . sprintf('%010d', $dst_mei_id));
                $path = $path_lib->get_full_path();

                $html = new HTMLDOMParser_lib();
                $list = $html->get_all_path_uploaded('', 1, $path);

                $fileLib = new File_lib();
                foreach ($list as $path) {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    if ($ext == 'html') {
                        if ($fileLib->FileExists($path) == FALSE) {
                            return FALSE;
                        }
                        $e = file_get_contents($path);
                        $html = str_get_html($e);
                        if (!empty($html)) {//Add LIXD-327 MinhVnit 2015/10/09
	                        foreach ($html->find('img') as $dom) {
	                            $src = isset($dom->src) ? $dom->src : '';
	                            if ($src != '' && strpos($src, 'itemfile/view') != FALSE) {
	                                $src_exp = explode('/', $src);
	                                $src_exp[7] = $dst_head_id;
	                                $src_exp[8] = $dst_mei_id;
	                                $src_new = implode('/', $src_exp);
	                                $dom->src = str_replace('itemfile/view', 'itemfile/yoyakuview', $src_new);
	                            }
	                            if ($src != '' && strpos($src, 'itemfile/viewfree') != FALSE) {
	                                $src_exp = explode('/', $src);
	                                $src_exp[8] = $dst_head_id;
	                                $src_exp[9] = $dst_mei_id;
	                                $src_new = implode('/', $src_exp);
	                                $dom->src = str_replace('itemfile/viewfree', 'itemfile/yoyakuviewfree', $src_new);
	                            }
	                        }
	                        $html->save($path);
	                        $html->clear();
						}
                    }
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
        return FALSE;
    }

//    public static function remove_tr_xml($str) {
//        $html = str_get_html($str, false, true, DEFAULT_TARGET_CHARSET, false);
//        $total = count($html->find('Row'));
//        $i = 0;
//        foreach ($html->find('Row') as $dom) {
//            $i++;
//            if ($i == $total) {
//                $line = preg_replace('/\s+/', '', strip_tags($dom->outertext));
//               
//                if ($line == '') {
//                    $dom->outertext = '';
//                }
//            }
//        }
//        return $html->save();
//    }
//
//    public static function remove_tr_empty($html_path) {
//        try {
//            $e = file_get_contents($html_path);
//            $html = str_get_html($e);
//            $total = count($html->find('tr'));
//            $z = 0;
//            foreach ($html->find('tr') as $dom) {
//                $z++;
//                if ($total == $z) {
//                    $line = preg_replace('/\s+/', '', strip_tags($dom->outertext));
//                    if ($line == '') {
//                        $dom->outertext = '';
//                    }
//                }
//            }
//            $html->save($html_path);
//            return TRUE;
//        } catch (Exception $exc) {
//            return FALSE;
//        }
//    }
}
