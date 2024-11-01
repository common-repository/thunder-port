<?php
/*
Plugin Name: Thunder-port
Plugin URI: http://vividl.net
Description: This is a plugin for all wordpress plugin's support.
Version: 1.1
Author: vividl
Author URI: http://vividl.net
License: GPL2
*/
require_once('simple_html_dom.php');

add_action( 'admin_menu', 'thunderport_add_admin_menu' );
add_action( 'admin_init', 'thunderport_settings_init' );

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}


function curl_file_get_html($base) {
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);

	// Create a DOM object
	$html_base = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);
	// Load HTML from a string
	$html_base->load($str,true,true);
	return $html_base;

}


function thunderport_add_admin_menu(  ) { 

	add_menu_page( 'Thunder Port', 'Thunder Port', 'manage_options', 'thunder_port', 'thunder_port_options_page' ,plugin_dir_url( __FILE__ ) . '/img/logo16.png');
	add_submenu_page( 'admin.php', 'thunder_port', 'thunder_port', 'manage_options', 'thunder_port_detail', 'thunder_port_detail' );

}

function thunderport_settings_init(  ) { 

	register_setting( 'pluginPage', 'thunderport_settings' );

	add_settings_section(
		'thunderport_pluginPage_section', 
		__( 'Your section description', 'thunderport' ), 
		'thunderport_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'thunderport_text_field_0', 
		__( 'Settings field description', 'thunderport' ), 
		'thunderport_text_field_0_render', 
		'pluginPage', 
		'thunderport_pluginPage_section' 
	);

	add_settings_field( 
		'thunderport_text_field_1', 
		__( 'Settings field description', 'thunderport' ), 
		'thunderport_text_field_1_render', 
		'pluginPage', 
		'thunderport_pluginPage_section' 
	);


}


function thunderport_text_field_0_render(  ) { 

	$options = get_option( 'thunderport_settings' );
	?>
	<input type='text' name='thunderport_settings[thunderport_text_field_0]' value='<?php echo $options['thunderport_text_field_0']; ?>'>
	<?php

}


function thunderport_text_field_1_render(  ) { 

	$options = get_option( 'thunderport_settings' );
	?>
	<input type='text' name='thunderport_settings[thunderport_text_field_1]' value='<?php echo $options['thunderport_text_field_1']; ?>'>
	<?php

}


function thunderport_settings_section_callback(  ) { 

	echo __( 'This section description', 'thunderport' );

}


// Check if get_plugins() function exists. This is required on the front end of the
// site, since it is in a file that is normally only loaded in the admin.
if ( ! function_exists( 'get_plugins' ) ) {
   require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// get all plugin's information to array
function thunder_getallplugin_info() {
	$all_plugins = get_plugins();
    $plugin_names = array();
    while ($fruit_name = current($all_plugins)) {

		$keyarray = key($all_plugins);
		if(strpos($keyarray, 'hello.php')!==false) {
			$str = str_replace(".php","",$keyarray);
			$str = "hello-dolly";
		} else {
			$str = explode("/",$keyarray);	
			$str = $str[0]; 
		}
		array_push($plugin_names, $str);

	    next($all_plugins);
	}
	return $plugin_names;
}

function thunder_getallplugin_list() {
	
	// 1. using file 
	$list = file_get_contents("".plugin_dir_path( __FILE__ ) . "list/allplugins.json");

	$return = json_decode($list);
	// 2. using database
	// global $user_ID;
	// $list = get_user_meta($user_ID, 'thunder_list_allplugins', true);
	// $return = $list;

	return $return;
}

// Do list all plugin's name when plugin is updaged or installed 
function thunder_action_upgrader_process_complete( $array ) {
    // get all plugin information 
    $plugin_names = thunder_getallplugin_info();
    
    // 1. using files
    // json encoding for save to file "allplugins.json"
	$json_action = json_encode($plugin_names);
	// fopen and save
	$filename_action = fopen(plugin_dir_path( __FILE__ ) . "list/allplugins.json", "w") or die("Unable to open file!");
    fwrite($filename_action, $json_action);
	fclose($filename_action);

    // 2. using database
 
};        
register_uninstall_hook( __FILE__, 'thunder_action_upgrader_process_complete' );

register_activation_hook( __FILE__, 'thunder_action_upgrader_process_complete' );
add_action( 'upgrader_process_complete', 'thunder_action_upgrader_process_complete', 10, 1 );

//if( strpos($responsive, 'responsive') !== false ) echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';

function thunder_add_script() {

	wp_enqueue_script('jquery');
	wp_enqueue_script('bootstrap', plugin_dir_url( __FILE__ ). 'js/bootstrap.min.js');
	wp_enqueue_script('sweetalert', plugin_dir_url( __FILE__ ). 'js/sweetalert.min.js');
    wp_enqueue_style('sweetalert_css', plugin_dir_url( __FILE__ ). 'css/sweetalert.css');
    wp_enqueue_style('bootstrap_css', plugin_dir_url( __FILE__ ). 'css/bootstrap.min.css');
    wp_enqueue_style('fontawesome_css', plugin_dir_url( __FILE__ ). 'font-awesome/css/font-awesome.min.css');
    wp_enqueue_style('thunderport_css', plugin_dir_url( __FILE__ ). 'css/style.css');
	
}

// add_action( 'admin_enqueue_scripts', 'thunder_add_script' );

function thunder_add_logo() {
	$html = '<div class="thp_logo" align="center">
		<img src="'.plugin_dir_url( __FILE__ ) . '/img/thunder-logo.png">
		<h2 id="slogan">The faster way to access your plugins\' supports!</h2>
	</div>';
	return $html;
	
}

function thunder_get_exist() {
	// 1. using files
	$html2 = file_get_contents("".plugin_dir_path( __FILE__ ) . "list/exist.json");		
	
	$string2 = json_decode($html2);

	// 2. using database
	// global $user_ID;
	// $html2 = get_user_meta($user_ID, 'thunder_list_exist', true);
	// $string2 = $html2;

	return $string2;
		
}

function thunder_check_plugin_exist($array, $array2) {
	$string2 = $array; // exist.json file
	$plugin_names = $array2; // allplugins.json file
	$array= array();
	$checknot = array();
	foreach($string2 as $check => $key2) {
		$i=0;
		foreach($key2 as $key => $val){
			$i++;
		    array_push($array, $key);
	    	if($i>0) {break;}
		}
	}
	$checknot = array_diff($plugin_names,$array);
	return $checknot;
}

 
add_action('admin_init', 'thunder_port_trigger_check');

// log data and redirect to plugin's support page
function thunder_port_trigger_check() {

	if((isset($_GET['url']) && $_GET['url'] != NULL) && (isset($_GET['q']) && $_GET['q'] != NULL) && (isset($_GET['plugin']) && $_GET['plugin'] != NULL)) {
		global $user_ID;
		$url = sanitize_text_field($_GET['url']);
		$plugin = sanitize_text_field($_GET['plugin']);
		$q = sanitize_text_field($_GET['q']);
		$q = rawurldecode($q);
		$time = time();
		
		$new_array = array();
		$get_array = get_user_meta($user_ID, 'thunderport_'.$plugin.'', true);

		
		if($get_array == NULL) {

			array_push($new_array, array('url' => $url, 'q'=> $q, 'time'=>$time, 'repeat'=>1));
			update_user_meta($user_ID, 'thunderport_'.$plugin.'', $new_array);

		} else {
			// adding clicked question in front of unique plugin's questionaries array.
			
			
			$d_array = array('url'=>$url, 'q'=> $q, 'time'=>$time, 'repeat'=> 1);
			foreach ($get_array as $key => $gets) { 
				$g_url = $gets['url'];
				$g_q = $gets['q'];
				$g_repeat = $gets['repeat'];
				$g_repeat = $g_repeat + 1;
				if($g_url == $url && $g_q == $q) {
					$tmp_array = array('url'=>$url, 'q'=> $q, 'time'=>$time, 'repeat'=> $g_repeat);
					unset($get_array[$key]);
				}
			}			

			if(isset($tmp_array)) {
				array_unshift($get_array, $tmp_array);	
				
			} else {
				array_unshift($get_array, $d_array);	
			}
			
			update_user_meta($user_ID, 'thunderport_'.$plugin.'', $get_array);	

		}
		
		// $url = 'https://wordpress.org/support/topic/lots-of-missed-spam-comments';
		wp_redirect($url, 301);
		exit;		
    } else {
    	return false;
    }
}

function whatever($array, $key, $val) {
    $i=0;
    $ever = array();
    foreach ($array as $item) 
        if (isset($item[$key]) && $item[$key] == $val) {
	    	$ever_r = array('string'=>$item['string'], 'address'=>$item['address'], 'plugin'=> $item['plugin']);
	    	array_push($ever, $ever_r);

		} 
		return $ever;
}

function get_scrapping($plurl, $plname, $plpage) {
    // create HTML DOM
    $json = array();
    $pluginurl = $plurl;
    $pluginname = $plname;
    $page = $plpage;

	if($page == 1) {
		// $html = file_get_html(''.$pluginurl.'/');
		$html = curl_file_get_html(''.$pluginurl.'/');
        
        foreach($html->find('ul') as $article) {
            // get title
	    	$title = trim($article->find('li', 0)->find('a',0)->plaintext);
	    	$title1 = htmlentities($title);
	    	$title = utf8_encode($title1);
	    	
	    	$address = trim($article->find('li', 0)->find('a', 0)->href);
	    	$i++;
            if($title == '' ){
	    		//$ret[] = array('title'=> $title, 'address'=> $address);
	    	} else {
            	$ret[] = array('title'=> $title, 'address'=> $address);
	    	}
	
    	}
        // clean up memory
        $html->clear();
        unset($html);
        
       array_push($json, $ret);
        
	} else {
		for ($x = 1; $x <= $page; $x++) {

	        // $html = file_get_html(''.$pluginurl.'/page/'.$x.'/');
	        $html = curl_file_get_html(''.$pluginurl.'/page/'.$x.'/');
	        
	        // get news block

            $i=0;
	        foreach($html->find('ul') as $article) {
	            // get title
		    	$title = trim($article->find('li', 0)->find('a', 0)->plaintext);
		    	$title1 = htmlentities($title);
	    		$title = utf8_encode($title1);
		    	$address = trim($article->find('li', 0)->find('a', 0)->href);
		    	$i++;
	            if($title == '' ){
		    		//$ret[] = array('title'=> $title, 'address'=> $address);
		    	} else {
	            	$ret[] = array('title'=> $title, 'address'=> $address);
		    	}
    	
        	}
	        // clean up memory
	        $html->clear();
	        unset($html);
	        if($x == $page -1) {
	           array_push($json, $ret);
	        }
	        
        }
	}
    
    return $json;

}


function scraping_digg($plurl, $plname) {
    // create HTML DOM
    $json = array();
    $pluginurl = $plurl;
	$pluginname = $plname;
    // $html = file_get_html(''.$pluginurl.'');
    $html = curl_file_get_html(''.$pluginurl.'');
    $ret = array();
    
    foreach($html->find('.bbp-pagination-links') as $article) {
        // get title
        foreach($article->find('a') as $art) {
	
            $title = trim($art->plaintext);
            // $pos = strpos($title, '→');
            
            if(!is_numeric($title)){
            // if($pos !== false) {

            } else {
                $re_array = array('number'=> $title,'pluginname'=>''.$pluginname.'');
                array_push($ret, $re_array);    
            }
            
        }
    }
    if(count($ret) == 0) {
        $re_array = array('number'=>'1','pluginname'=>''.$pluginname.'');
        array_push($ret, $re_array);
    }

    // clean up memory
    $html->clear();
    unset($html);
    $json = $ret;
    return $json;

}

add_action('wp_ajax_thunder_port_indexing_ajax', 'thunder_port_indexing_ajax_callback');

// scrapping question and answer on plugin's support.
function thunder_port_indexing_ajax_callback() {
	if(isset($_POST["pluginurl"])) {

		check_ajax_referer( 'tp-ajax-nonce', 'security');
	
		$pluginurl = sanitize_text_field($_POST['pluginurl']);

		$pluginname = sanitize_text_field($_POST['pluginname']);
	    
	    $page = sanitize_text_field($_POST['page']);

	    ini_set('user_agent', 'My-Application/2.5');

	    $ret = get_scrapping($pluginurl, $pluginname, $page);
	    // $json = json_encode($ret, JSON_UNESCAPED_UNICODE);
	    $json = json_encode($ret);

	    
	    //1. using files
		$filename = fopen(plugin_dir_path( __FILE__ ) . "json/".$pluginname.".json", "w") or die("Unable to open file!");
		fwrite($filename, $json);
		fclose($filename);


		// echo $json;
		wp_send_json($json);
		wp_die();

		//2. using database
		

	}

}

add_action('wp_ajax_thunder_port_getsupport_ajax', 'thunder_port_getsupport_ajax_callback');
// check number of total pages for plugin's support page and calculate scrapping time.
function thunder_port_getsupport_ajax_callback() {

	if(isset($_POST["pluginurl"])) {

		check_ajax_referer( 'tp-ajax-nonce', 'security');

		$pluginurl = sanitize_text_field($_POST['pluginurl']);
		$pluginname = sanitize_text_field($_POST['pluginname']);

		
	    ini_set('user_agent', 'My-Application/2.5');
		$past_time = time();
	    $ret = scraping_digg($pluginurl, $pluginname);
		$last_time = time();
		
	    $end = end($ret);
	    $diff = (float)($last_time - $past_time) * (float)$end['number'];
	    
		$form = array();
		$form['duration'] = $diff;
		$form['data'] = $end;
	    
	    // echo json_encode($form);
	    wp_send_json($form);
		wp_die();
	    
	    
	}

}

add_action('wp_ajax_thunder_port_submit_keyword_ajax', 'thunder_port_submit_keyword_ajax_callback');

// get questions(included query keyword) of plugin from plugin's json file.
function thunder_port_submit_keyword_ajax_callback() {


	if((isset($_POST['query']) && $_POST['query'] != NULL) && (isset($_POST['request']) && $_POST['request'] != NULL) ) {

		check_ajax_referer( 'tp-ajax-nonce', 'security');
		global $user_ID;
		$query = sanitize_text_field($_POST['query']);
		$time = time();

		$new_array = array();
		$get_array = get_user_meta($user_ID, 'thunder_port_search', true);

		// if, there is no clicking log of questions.
		if($get_array == NULL) {

			$push_ele = array('query'=> $query, 'time' => $time, 'repeat' => 1);
			array_push($new_array, $push_ele);
			update_user_meta($user_ID, 'thunder_port_search', $new_array);

		} else {
			// adding clicked question in front of unique plugin's questionaries array.
			$d_array = array('query'=> $query, 'time' => $time, 'repeat' => 1);
			foreach ($get_array as $key => $gets) { 
				$g_q = $gets['query'];
				$g_repeat = $gets['repeat'] + 1;
				if($g_q == $query) {
					$tmp_array = array('query'=>$query, 'time'=> $time, 'repeat'=> $g_repeat);
					unset($get_array[$key]);
				}
			}

			if(isset($tmp_array)) {
				array_unshift($get_array, $tmp_array);	
				
			} else {
				array_unshift($get_array, $d_array);	
			}
			
			update_user_meta($user_ID, 'thunder_port_search', $get_array);	
		}

		$thelist = array();
		// get all created file by searching.

		if ($handle = opendir(plugin_dir_path( __FILE__ ) . "json")) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'json')
		        {
		        	
		            array_push($thelist, $file);
		        }
		    }
		    closedir($handle);
		}

		$final = array();
		$notsave = array();
		
		foreach($thelist as $list) {
			
			$json = file_get_contents(''.plugin_dir_path( __FILE__ ).'json/'.$list.'');		
			
			$string = json_decode($json);
			$string = $string[0];
			$save = array();
			

			$pluginname = str_replace('.json','',$list);
			
			$csave = count($save);
			$csavemin = (int)$csave - 5;
			
			$countsave = array();

			if($string != NULL) {

				foreach ($string as $str) {
					// explode string to words for check multibyte character.
					// 문자열 분해 및 단어로 저장(title 한글 비중 체크를 위함)
					
					$decode_str = utf8_decode($str->title);
					
					$lower_decode_str = strtolower($decode_str);
					$lower_query = strtolower($query);
					$address = $str->address;
					// check multibite character(korean) in string.
					// 문자열에 한글 포함 유무 체크
					 if (strpos($lower_decode_str,$lower_query) !== false ) {
						$check = 'true';
						// save search result to array
						// 문자열 배열에 저장
						$sarray = array('plugin' => $pluginname, 'string' => $decode_str,'address' => $address, 'check' => $check);
						array_push($countsave, $sarray);
					} else {
						$check = 'false';
						
					}
				}
				$count_save = count($countsave);
				$csave_min = (int)$count_save - 5;
				
				foreach ($string as $str) {
					// explode string to words for check multibyte character.
					// 문자열 분해 및 단어로 저장(title 한글 비중 체크를 위함)
					
					$decode_str = utf8_decode($str->title);
					
					$lower_decode_str = strtolower($decode_str);
					$lower_query = strtolower($query);
					$address = $str->address;
					// check multibite character(korean) in string.
					// 문자열에 한글 포함 유무 체크
					 if (strpos($lower_decode_str,$lower_query) !== false ) {
						$check = 'true';
						// save search result to array
						// 문자열 배열에 저장
						$sarray = array('plugin' => $pluginname, 'string' => $decode_str,'address' => $address, 'check' => $check);
						array_push($save, $sarray);
					} else {
						$check = 'false';
						
					}
					$csave = count($save);
					
					if($csave >= 5) {

						$sarray = array('plugin' => $pluginname,'string'=>'more('.$csave_min.')','address' => ''.site_url().'/wp-admin/admin.php?page=thunder_port_detail&plugins='.$pluginname.'&query='.$query.'', 'check' => $check);
						array_push($save, $sarray);
						break;
					}

				}

				$count_save = count($save);
				$result = whatever($save, 'check', 'true');	

				array_push($final, $result);

			}
			
			
		}
		
		$form_data = array();
		$form_data['data'] = $final;
		$form_data['notsave'] = $thelist;
		wp_send_json($form_data);
		wp_die();
    } else {
    	return false;
    }
}


function thunder_port_options_page() { 	
	
	thunder_add_script();
	$logo = thunder_add_logo();
	echo $logo;

	global $user_ID;

	?>
	
	
	<script type="text/javascript">
	
	jQuery.fn.replaceText = function( search, replace, text_only ) {
		return this.each(function(){
	        var node = this.firstChild,
	        val, new_val, remove = [];
	        if ( node ) {
	            do {
	              if ( node.nodeType === 3 ) {
	                val = node.nodeValue;
	                new_val = val.replace( search, replace );
	                if ( new_val !== val ) {
	                  if ( !text_only && /</.test( new_val ) ) {
	                    jQuery(node).before( new_val );
	                    remove.push( node );
	                  } else {
	                    node.nodeValue = new_val;
	                  }
	                }
	              }
	            } while ( node = node.nextSibling );
	        }
	        remove.length && jQuery(remove).remove();
	    });
	};
	
	jQuery( document ).ready(function() {
		jQuery("#searchtext").keydown(function (key) {
		    if (key.keyCode == 13) {
		        submit_keyword_fc();
		    }
		});
	});

	
	
	function submit_keyword_fc() {
		var query = jQuery('#searchtext').val();
		
		var datas = {
			'action': 'thunder_port_submit_keyword_ajax',
			'query' : query,
			'request' : 'true',
			'security' : jQuery('#tp-ajax-nonce' ).val(),
		};
		jQuery('#success_loader').show();
		jQuery('.descbox').each(function(){
    		jQuery(this).remove();
    	});	

		jQuery('.searched').each(function(){
			var dsearch = jQuery(this).attr('data-search');
			if(dsearch == query) {
				jQuery(this).remove();
			}
		});
    	jQuery('#recent-search').prepend('<button class="searched" data-search="'+query+'">'+query+'<small style="font-size:0.5em;"><img src="<?php echo plugin_dir_url( __FILE__ );?>img/new.png"></small></button>');

    	jQuery.post(ajaxurl, datas, function(response) {
    		
	       	if(jQuery('.tp_center').find('.descbox').length != 0) {
        		jQuery('.descbox').each(function(){
            		jQuery(this).remove();
            	});	
        	}
        	
        	var data = response['data'];
        	var notsave = response['notsave'];
        	jQuery('.col-md-3').hide();
        	for(i=0;i<data.length;i++) {
        		var ldata = data[i].length;
        		if(ldata != 0) {
        			
        			var plsource_0 = data[i][0]['plugin'];
        			var plid_0 = '#' + plsource_0 + '_00';
        			jQuery(plid_0).parent().show();
        			jQuery(plid_0).append('<div class="descbox" id="'+ plsource_0+ '_0"></div>');
        			var logs;
        			for(j=0;j<data[i].length;j++) {
        				var pladdr = data[i][j]['address'];
        				var pldesc = data[i][j]['string'];
        				var pldesc_encode = encodeURIComponent(pldesc);
        				var plsource = data[i][j]['plugin'];
        				var plid = '#' + plsource + '_0';
        				if(j<5) {

        					// jQuery(plid).append('<a href="<?php echo plugin_dir_url( __FILE__ );?>thunder_port_redirect.php?url='+pladdr+'&plugin='+plsource+'&q='+pldesc_encode+'" class="q_link" target="_blank" id="id_'+plsource+'_'+j+ '"><p id="'+plsource+'_p'+j+'">'+pldesc+'</p></a>');	
        					
        					var redirecutl = '<?php echo add_query_arg( array("url" => "'+pladdr+'", "plugin" => "'+plsource+'","q"=>"'+pldesc_encode+'" ), admin_url("admin.php?page=thunder_port") );?>';
        					jQuery(plid).append('<a href="'+redirecutl+'" class="q_link" target="_blank" id="id_'+plsource+'_'+j+ '"><p id="'+plsource+'_p'+j+'">'+pldesc+'</p></a>');
        					
        				} else {
        					jQuery(plid).append('<a href="'+pladdr+'" class="q_link" target="_blank"><p id="'+plsource+'_p'+j+'">'+pldesc+'</p></a>');	
        				}
        				
        				var plid_l = '#' + plsource + '_p'+j;
        				var re = new RegExp(query,"gi");
        				jQuery(plid_l).replaceText(re, '<span class="highlight">'+query+'</span>');
        			}
        			
        			jQuery(plid_0).show();
        		} else {
        			
        		}
        	}
        	jQuery('#success_loader').hide();
		});
		
		
	};
	function ask_supports(dataname,dataslug) {
		swal("Oops...", "Not ready yet!", "error");
		
		// swal({
		// 	title: dataname,
		// 	text: "Ask your question on <a href='//vividl.net/forums/forum/"+dataslug+"'>"+dataname+" forum</a>,<br/><small>If admin email address is correct,<br/> you can get the answer by email, too.<br/>(<?php echo get_bloginfo('admin_email');?>)</small>",
		// 	html: true,
		// 	type: "input",
		// 	showCancelButton: true,
		// 	closeOnConfirm: false,
		// 	animation: "slide-from-top",
		// 	inputPlaceholder: "Write something"
		// },
		// function(inputValue){
		// 	if (inputValue === false) return false;

		// 	if (inputValue === "") {
		// 		swal.showInputError("You need to write something!");
		// 		return false
		// 	}

		// 	var data = {
		// 		'pluginname':dataname,
		// 		'slug':dataslug,
		// 		'question':inputValue,
		// 		'email':'<?php echo get_bloginfo("admin_email");?>',
		// 	};
		// 	// console.log(data);
		// 	swal({title:"", text:"Asking your question", imageUrl: "<?php echo plugin_dir_url( __FILE__ );?>img/loader.gif", showConfirmButton:false,allowOutsideClick:false});

		// 	jQuery.ajax({            
		// 		type: "GET",
		//         url: 'http://vividl.net/test2.php',
		//         data: data, 
		//         dataType: 'jsonp',
		//         success: function(datas) {
		//         	// console.log(datas);
		//         	swal("Nice!", "You wrote: " + inputValue, "success");
		    	
		//     	},error: function(jqXHR, textStatus, errorThrown) {
		//         	swal("Oops...", "Something went wrong!", "error");
		//         }
		//     });

			
		// });
		

	}
	
	function get_supports(dataurl,dname,dslug) {
		var dataform = {
			'action' : 'thunder_port_getsupport_ajax',
			'pluginurl':dataurl,
			'pluginname':dslug,
			'security' : jQuery('#tp-ajax-nonce' ).val(),
		};
		
		//jQuery('#success').show();
		var changeids = '#' + dslug;
		var html_ore = jQuery(changeids).html();
		// jQuery('#success_loader').show();
	    jQuery(changeids).html('<img src="<?php echo plugin_dir_url( __FILE__ );?>img/loader.gif">');
	
		jQuery.post(ajaxurl, dataform, function(data) {

	        var t = parseInt(data.data.number);
            var s = dataurl;
            var pl = data.data.pluginname;
            var dataform2 = {
            	'action' : 'thunder_port_indexing_ajax',
            	'pluginurl':s,
            	'pluginname':pl,
            	'page':t,
            	'security' : jQuery('#tp-ajax-nonce' ).val(),

            };
            
            // console.log('get_support.php success');

            swal({
		        title: "<i class=\"fa fa-plug\"></i> : <font color=\"#8CD4F5\">"+dname+"</font><br/> <small>get all supports?</small>\n",
		        text: "Loading time : " + data.duration + " seconds",
		        type: "warning",
		        html: true,
		        showCancelButton: true,
		        confirmButtonColor: "#DD6B55",
		        confirmButtonText: "Yes, get it!",
		        closeOnConfirm: false
		    }, function(isConfirm){
		        if (isConfirm) {
		        	swal.close();
		        	jQuery.post(ajaxurl, dataform2, function(data2) {

						
		                swal("Success!",  "\"" + dname + "\" is completed ", "success");
		                var changeid = '#' + dslug;

						jQuery(changeid).html('<?php add_thickbox();?>');
						var htmls = '<button class="btn btn-large btn-default detail_bt" rel="tooltip" title="refresh?" data-delay="{\'show\':\'100\', \'hide\':\'100\'}" onclick="get_supports(this.getAttribute(\'data-url\'),this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-slug="'+dslug+'" data-name="'+dname+'" data-url="'+dataurl+'"><i class="fa fa-refresh"></i></button>';
						htmls += '<a href="<?php echo site_url();?>/wp-admin/admin.php?page=thunder_port_detail&plugins='+dslug+'&query="><button class="btn btn-large btn-default detail_bt" rel="tooltip" title="detail?" data-delay="{\'show\':\'100\', \'hide\':\'100\'}"><i class="fa fa-search-plus"></i></button></a>';
						htmls += '<a href="<?php echo self_admin_url("plugin-install.php?tab=plugin-information&amp;plugin='+dslug+'&amp;TB_iframe=true&amp;width=600&amp;height=550");?>"><button class="btn btn-default btn-large detail_bt" rel="tooltip" title="More information about '+dname+'"><i class="fa fa-info"></i></button></a>';
						// htmls += '<button class="btn btn-default btn-large detail_bt askq" rel="tooltip" title="ask question" onclick="ask_supports(this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-slug="'+dslug+'" data-name="'+dname+'" data-url="'+dataurl+'"><i class="fa fa-pencil"></i></button>';
						htmls += '<button class="btn btn-default btn-large detail_bt askq" rel="tooltip" title="not ready yet..." onclick="ask_supports(this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-slug="'+dslug+'" data-name="'+dname+'" data-url="'+dataurl+'" ><i class="fa fa-pencil"></i></button>';
						jQuery(changeid).html(htmls); 
						var ex_class = jQuery(changeid).parent().parent().attr('')

						jQuery(changeid).parent().parent().addClass('notcomplete').removeClass('notall');
						jQuery(changeid).parent().parent().attr('data-name','1');
						jQuery(changeid).parent().css('margin-left','-80px');
						
						var $people = jQuery('#my-plugins'),
							$peopleli = $people.children('div');

						$peopleli.sort(function(a,b){
							var an = a.getAttribute('data-name'),
								bn = b.getAttribute('data-name');

							if(an < bn) {
								return 1;
							}
							if(an > bn) {
								return -1;
							}
							return 0;
						});

						$peopleli.detach().appendTo($people);

	    				
		                var f_array = new Array(); 
		                for(i=0;i<data2[0].length;i++) {
		                	var datai = data2[0][i];
		                	if(datai.title == "" && datai.address == "") {
		                		
		                	} else {
		                		f_array.push(datai);
		                	}
		                	
		                }
		            });
		        } else {
					jQuery(changeids).html(html_ore);
				}
		        
		    });

		});

		
	}	
	
	</script>

	
	<div class="row" style="width:100%" >
		<div id="success_loader"></div>
		<div class="col-md-12" id="recent-search" align="center" style="padding:10px;">
		<?php
		
		$get_array = get_user_meta($user_ID, 'thunder_port_search', true);
		$limit = 0;
		$ctime = time();

		if($get_array != NULL) {
			foreach($get_array as $g) {
				$limit++;
				$query = $g['query'];
				$tdiff = human_time_diff($g['time'], current_time('timestamp',1) ) . ' ago';
				echo '<button class="searched" id="query_'.$limit.'" data-search="'.$query.'">'.$query.'<small style="font-size:0.5em;">: '.$tdiff.'</small></button>';
				if($limit == 5) {
					//break;
				}			
			}
		}
		
		?>
		</div>
		<div class="col-md-12" id="search-form" align="center">
			<input id="searchtext" type="text" placeholder="Search Supports!"></input>
			<button id="submit_keyword" class="btn btn-inverse" type="submit"><i class="fa fa-search" style="font-size: 35px;"></i></button>
			<div class="legend">
				<button class="legend0 legendclick" data-show="all">All</button>
				<button class="legend1 legendclick" data-show="allcomplete">Popular</button>
				<button class="legend2 legendclick" data-show="notcomplete">Done,but no click!</button>
				<button class="legend3 legendclick" data-show="notall">Not ready...</button>
			</div>
		</div>
		<script>
		jQuery('.legendclick').click(function(){
			var datashow = jQuery(this).attr('data-show');
			if(datashow == 'allcomplete') {
				jQuery('.col-md-3').hide();
				jQuery('.allcomplete').show();
			} else if (datashow == 'notcomplete') {
				jQuery('.col-md-3').hide();
				jQuery('.notcomplete').show();
			} else if (datashow == 'notall'){
				jQuery('.col-md-3').hide();
				jQuery('.notall').show();
			} else if (datashow == 'all') {
				jQuery('.col-md-3').show();
			}
		});
		
		</script>
		
		<div class="col-md-12" id="my-plugins" align="center">		
			<h5><i class="fa fa-plug"></i>&nbsp;My Plugins</h5>
		<?php
		
		// case 1 : wordpress.org 

		if(file_exists(plugin_dir_path( __FILE__ ) . 'list/allplugins.json') && file_exists(plugin_dir_path( __FILE__ ) . 'list/exist.json')) {

			$string2 = thunder_get_exist(); //exist.json
		    $plugin_names = thunder_getallplugin_list(); //allplugins.json	
			$checknot = thunder_check_plugin_exist($string2, $plugin_names);
		} elseif (!file_exists(plugin_dir_path( __FILE__ ) . 'list/allplugins.json') && file_exists(plugin_dir_path( __FILE__ ) . 'list/exist.json')) {
			$plugin_names = thunder_getallplugin_info();
    		// 1. using files
		    // json encoding for save to file "allplugins.json"
			$json_action = json_encode($plugin_names);
			// fopen and save
			$filename_action = fopen(plugin_dir_path( __FILE__ ) . "list/allplugins.json", "w") or die("Unable to open file!");
		    fwrite($filename_action, $json_action);
			fclose($filename_action);
			$checknot = 0;
		} else if (file_exists(plugin_dir_path( __FILE__ ) . 'list/allplugins.json') && !file_exists(plugin_dir_path( __FILE__ ) . 'list/exist.json')) {
			$checknot = 0;
		} else {
			$plugin_names = thunder_getallplugin_info();
    		// 1. using files
		    // json encoding for save to file "allplugins.json"
			$json_action = json_encode($plugin_names);
			// fopen and save
			$filename_action = fopen(plugin_dir_path( __FILE__ ) . "list/allplugins.json", "w") or die("Unable to open file!");
		    fwrite($filename_action, $json_action);
			fclose($filename_action);


			$checknot = 0;

		}
		
		$all_plugins = get_plugins();
		
		$exist = array();
		$notexist = array();
		

		
		if(count($checknot) == 0 && $string2 != NULL) {
			foreach($string2 as $add) {
				$j=0;

				foreach($add as $a) {
					$j++;
					if($a == 1) {
						array_push($exist, $add);
					} elseif($a == 0) {
						array_push($notexist, $add);
					}
					if($j>0) {break;}
				}
			}
			
			

		} else {
			// not same 
			$check_tmp = array();

			$all_result = array();
			foreach($all_plugins as $key=>$val) {
				$valName = $val['Name'];
				$valPluginURI = $val['PluginURI'];
				$valVersion = $val['Version'];
				$valAuthor = $val['Author'];
				$valAuthorURI = $val['AuthorURI'];
				if(strpos($key, 'hello.php')!==false) {
					$str = str_replace(".php","",$key);
					$str = "hello-dolly";
				} else {
					$str = explode("/",$key);	
					$str = $str[0]; 
				}
				$re_array = array('Filename'=>$key, 'slug'=>$str,'Name'=>$valName,'PluginURI'=>$valPluginURI, 'Version'=>$valVersion, 'Author'=>$valAuthor, 'AuthorURI'=>$valAuthorURI);
				array_push($all_result, $re_array);
			}

			foreach($all_result as $key) {

				$pluguri = $key['PluginURI'];
				$name = $key['Name'];
				$slug_ori = $key['slug'];
				//$name = str_replace(' ', '-', $name);
				$args=array('timeout' => 120, 'httpversion' => '1.1');
				$response = wp_remote_post( 'https://api.wordpress.org/plugins/info/1.0/'.$slug_ori.'.json', $args );
				
				
				if($response && is_array($response)){
					$decoded = json_decode($response['body'] );		
					if($decoded && is_object($decoded)){
						
						$slug = basename( 'http://wordpress.org/plugins/{'.$decoded->slug.'}' );
						$links = sprintf( '<a href="%s" class="thickbox" title="%s">',
							self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $slug_ori . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
							esc_attr( sprintf( __( 'More information about %s' ), $key['Name'] ) ),
							__( 'Details' )
						);
						
						// $wordpress_page="http://wordpress.org/plugins/{".$decoded->slug."}";
						$wordpress_page="http://wordpress.org/plugins/".$slug_ori."";
						
						$arr = array(
					    	'name'=>''.$key['Name'].'',
					    	'pluginuri'=>''.$wordpress_page.'',
					    	'version'=>''.$key['Version'].'',
					    	'author'=>''.$key['Author'].'',
					    	'authoruri'=>''.$key['AuthorURI'].'',
					    	'detail' => ''.$links.''
				    	);
				    	$array_file = array(
				    		''.$decoded->slug.''=> 1,
				    		'name'=>''.$key['Name'].'',
					    	'pluginuri'=>''.$wordpress_page.'',
					    	'version'=>''.$key['Version'].'',
					    	'author'=>''.$key['Author'].'',
					    	'authoruri'=>''.$key['AuthorURI'].'',
					    	'detail' => ''.$links.''
				    	);
				    	array_push($exist, $arr);
					} else {
						
						$pos_org = strpos($pluguri, 'wordpress.org/extend/plugins');
						$pos_org2 = strpos($pluguri, 'wordpress.org/plugins');
						
						
						$keyarray = $key['Filename'];
						if(strpos($keyarray, 'hello.php')!==false) {
							$str = str_replace(".php","",$keyarray);
							//$str = "hello-dolly";
							$str = $slug_ori;
						} else {
							$str = explode("/",$keyarray);	
							$str = $str[0]; 
						}
						
						$array_file = array(''.$str.''=> 0);
						
						if($pos_org !== false || $pos_org2 !== false) {
							$slug = basename( 'http://wordpress.org/plugins/{'.$str.'}' );
							$links = sprintf( '<a href="%s" class="thickbox" title="%s">',
								self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $slug_ori . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
								esc_attr( sprintf( __( 'More information about %s' ), $key['Name'] ) ),
								__( 'Details' )
							);
							
							//$wordpress_page="http://wordpress.org/plugins/{$decoded->slug}";
							$arr = array(
						    	'name'=>''.$key['Name'].'',
						    	'pluginuri'=>''.$key['PluginURI'].'',
						    	'version'=>''.$key['Version'].'',
						    	'author'=>''.$key['Author'].'',
						    	'authoruri'=>''.$key['AuthorURI'].'',
						    	'detail' => ''.$links.''
					    	);
					    	$array_file = array(
					    		''.$slug_ori.''=> 0,
					    		'name'=>''.$key['Name'].'',
						    	'pluginuri'=>''.$key['PluginURI'].'',
						    	'version'=>''.$key['Version'].'',
						    	'author'=>''.$key['Author'].'',
						    	'authoruri'=>''.$key['AuthorURI'].'',
						    	'detail' => ''.$links.''
					    	);
					    	array_push($exist, $arr);
						} else {
							$arr2 = array(
						    	'name'=>''.$key['Name'].'',
						    	'pluginuri'=>''.$key['PluginURI'].'',
						    	'version'=>''.$key['Version'].'',
						    	'author'=>''.$key['Author'].'',
						    	'authoruri'=>''.$key['AuthorURI'].'',
						    	'detail' => ''
					    	);
					    	$array_file = array(
					    		''.$slug_ori.''=> 0,
					    		'name'=>''.$key['Name'].'',
						    	'pluginuri'=>''.$key['PluginURI'].'',
						    	'version'=>''.$key['Version'].'',
						    	'author'=>''.$key['Author'].'',
						    	'authoruri'=>''.$key['AuthorURI'].'',
						    	'detail' => ''
					    	);
						    array_push($notexist, $arr2);
						}
						
						
					}
					
					array_push($check_tmp, $array_file);
				}
				
	
				$pos_org = strpos($pluguri, 'wordpress.org/extend/plugins');
				$pos_org2 = strpos($pluguri, 'wordpress.org/plugins');
				
	
				// 1. using files 
				$json_tmp = json_encode($check_tmp);	
			    $filename_tmp = fopen(plugin_dir_path( __FILE__ ) . "list/exist.json", "w") or die("Unable to open file!");
			    fwrite($filename_tmp, $json_tmp);
				fclose($filename_tmp);
				
				// 2. using database
				
				
				error_log( print_r( $checknot, true ) );
	
			    
			}
			
			
		}
		
		add_thickbox();

		//get_plugin_data( $plugin_file, $markup = true, $translate = true )
		
		
		foreach($exist as $ex) {
			
			//$uri = $ex->pluginuri;
			//if($uri != NULL) {
			if( isset( $ex->pluginuri ) ){
				$uri = $ex->pluginuri;
				$name = $ex->name;
				$version = $ex->version;
				$author = $ex->author;
				$authoruri = $ex->authoruri;
				$detail = $ex->detail;

			} else {
				$uri = $ex['pluginuri'];
				$name = $ex['name'];
				$version = $ex['version'];
				$author = $ex['author'];
				$authoruri = $ex['authoruri'];
				$detail = $ex['detail'];
			}
			
			
			
			
			$addr_check1 = 'http://wordpress.org/extend/plugins/';
			$addr_check2 = 'http://wordpress.org/plugins/';
			$addr_change = 'http://wordpress.org/support/plugin';
			
			$pos1 = strpos($uri, $addr_check1);
			$pos2 = strpos($uri, $addr_check2);
			if($pos1 !== false) {
				$final = str_replace($addr_check1, '', $uri);
				$final = str_replace('/', '', $final);
				$address = ''.$addr_change.'/'.$final.'';
			} elseif($pos2 !== false) {
				$final = str_replace($addr_check2, '', $uri);
				$final = str_replace('/', '', $final);
				$address = ''.$addr_change.'/'.$final.'';
			} else {
				$final = 'no';
				$address = 'no';
			}
			
			// 1. using files
			$filename = plugin_dir_path( __FILE__ ) . 'json/'.$final.'.json';

			// 2. using database
			// global $user_ID;
			// $filename = get_user_meta($user_ID,'thunder_json_'.$final.'', true);
			
			if (file_exists($filename)) {
				$logscheck = get_user_meta($user_ID, 'thunderport_'.$final.'', true);
				if($logscheck != NULL) {
					echo '<div class="col-md-3 thp_exist '.$final.' allcomplete" data-name="2">';
				} else {
					echo '<div class="col-md-3 thp_exist '.$final.' notcomplete" data-name="1">';
				}
				
			} else {
				echo '<div class="col-md-3 thp_exist '.$final.' notall" data-name="0">';
			}
			// echo '<div class="col-md-3 thp_exist '.$final.'">';
			// plugin name
			
			
			// searched results from plugin support pages
			// 1. using files
			if (file_exists($filename)) {
			// 2. using database
			// if ($filename != NULL) {
				?>
				<script type="text/javascript">
			    jQuery(function () {
			        jQuery("[rel='tooltip']").tooltip({html:true});

			    });

				</script>

				<?php
				echo '<div class="tp_top">';
				echo '<h4 class="pl_name" >'.$name.'</h4>';
				// echo '<div id="'.$final.'"><button class="btn btn-large btn-info" rel="tooltip" title="refresh?" data-delay="{\'show\':\'100\', \'hide\':\'100\'}" onclick="get_supports(this.getAttribute(\'data-url\'),this.getAttribute(\'data-name\'));" data-name="'.$final.'" data-url="'.$address.'">Completed</button>';
				$log = get_user_meta($user_ID, 'thunderport_'.$final.'', true);
				if($log != NULL) {
					echo '<div class="descbox" id="log_'.$final.'_0">';
					$lim = 0;
					foreach ($log as $lo) {
						$lim++;

						$lo_q = $lo['q'];
						$lo_q_text = htmlspecialchars_decode($lo_q);
						$lo_time = $lo['time'];
						$timediff = human_time_diff($lo_time, current_time('timestamp',1) ) . ' ago';
						$lo_q_encode = urlencode($lo_q);
						$lo_repeat = $lo['repeat'];
						if($lo_repeat <= 1) {
							$times = 'time...';
						} else {
							$times = 'times!';
						}
						$lo_url = $lo['url'];
						echo '<div class="descp" >';
						echo '<p class="timediff" rel="tooltip" title="<i class=\'fa fa-search\'></i> '.$lo_repeat.' '.$times.'">'.$timediff.'</p>';
						// echo '<a class="question" id="id_'.$final.'_log" href="'.plugin_dir_url( __FILE__ ).'thunder_port_redirect.php?url='.$lo_url.'&plugin='.$final.'&q='.$lo_q_encode.'" target="_blank"> '.$lo_q_text.'</a>';
						$redirecturl = add_query_arg(array('url' => ''.$lo_url.'', 'plugin' => ''.$final.'','q'=>''.$lo_q_encode.'' ), admin_url('admin.php?page=thunder_port'));
						
						echo '<a class="question" id="id_'.$final.'_log" href="'.$redirecturl.'" target="_blank"> '.$lo_q_text.'</a>';
						echo '</div>';
						if($lim == 5) {
							//break ;
						}
					}
					echo '</div>';

				}
				// echo '</div>';
				echo '</div>';
			} else {
				echo '<div class="tp_top">';
				echo '<h4 class="pl_name" >'.$name.'</h4>';
				// echo '<div id="'.$final.'"><button rel="tooltip" title="Get plugin\'s support information?" onclick="get_supports(this.getAttribute(\'data-url\'),this.getAttribute(\'data-name\'));" data-name="'.$final.'" data-url="'.$address.'" class="btn btn-large btn-default">Click!</button>';
				$log = get_user_meta($user_ID, 'thunderport_'.$final.'', true);
				if($log != NULL) {
					echo '<div class="descbox" id="log_'.$final.'_0">';
					$lim = 0;
					foreach ($log as $lo) {
						$lim++;

						$lo_q = $lo['q'];
						$lo_q_text = htmlspecialchars_decode($lo_q);
						$lo_time = $lo['time'];
						$timediff = human_time_diff($lo_time, current_time('timestamp',1) ) . ' ago';
						$lo_q_encode = urlencode($lo_q);
						$lo_repeat = $lo['repeat'];
						if($lo_repeat <= 1) {
							$times = 'time...';
						} else {
							$times = 'times!';
						}
						$lo_url = $lo['url'];
						echo '<div class="descp" >';
						echo '<p class="timediff" rel="tooltip" title="<i class=\'fa fa-search\'></i> '.$lo_repeat.' '.$times.'">'.$timediff.'</p>';
						$redirecturl = add_query_arg(array('url' => ''.$lo_url.'', 'plugin' => ''.$final.'','q'=>''.$lo_q_encode.'' ), admin_url('admin.php?page=thunder_port'));
						
						echo '<a class="question" id="id_'.$final.'_log" href="'.$redirecturl.'" target="_blank"> '.$lo_q_text.'</a>';
						// echo '<a class="question" id="id_'.$final.'_log" href="'.plugin_dir_url( __FILE__ ).'thunder_port_redirect.php?url='.$lo_url.'&plugin='.$final.'&q='.$lo_q_encode.'" target="_blank"> '.$lo_q_text.'</a>';
						echo '</div>';
						if($lim == 5) {
							//break ;
						}
					}
					echo '</div>';
				}
				echo '</div>';
			}
			
			echo '<div class="tp_center" id="'.$final.'_00"></div>';
			
			echo '<div class="tp_bottom">';
			
			
		    // plugin author
		    // 1. using files
		    if (file_exists($filename)) {
			// 2. using database
			// if ($filename != NULL) {
		    	echo '<p class="pl_author">&copy; <a href="'.$authoruri.'" target="_blank">'.$author.'</a></p>';
		    	echo '<div id="'.$final.'"><button class="btn btn-large btn-default detail_bt" rel="tooltip" title="refresh?" data-delay="{\'show\':\'100\', \'hide\':\'100\'}" onclick="get_supports(this.getAttribute(\'data-url\'),this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-slug="'.$final.'" data-name="'.$name.'" data-url="'.$address.'"><i class="fa fa-refresh"></i></button>'; 
		    	echo '<a href="'.site_url().'/wp-admin/admin.php?page=thunder_port_detail&plugins='.$final.'&query="><button class="btn btn-large btn-default detail_bt" rel="tooltip" title="detail?" data-delay="{\'show\':\'100\', \'hide\':\'100\'}" data-url="'.$address.'"><i class="fa fa-search-plus"></i></button></a>'; 
		    	echo ''.$detail.'<button class="btn btn-default btn-large detail_bt" rel="tooltip" title="More information about '.$name.'"><i class="fa fa-info"></i></button></a>';
		    	// echo '<button class="btn btn-large btn-default detail_bt askq" rel="tooltip" title="ask question" onclick="ask_supports(this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-name="'.$name.'" data-slug="'.$final.'"><i class="fa fa-pencil"></i></button>';
		    	echo '<button class="btn btn-large btn-default detail_bt askq" rel="tooltip" title="not ready yet..." onclick="ask_supports(this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-name="'.$name.'" data-slug="'.$final.'" ><i class="fa fa-pencil"></i></button>';
		    	echo '</div>';
		    	
		    } else {
		    	echo '<p class="pl_author">&copy; <a href="'.$authoruri.'" target="_blank">'.$author.'</a></p>';
		    	echo '<div id="'.$final.'"><button rel="tooltip" title="Get plugin\'s support information?" onclick="get_supports(this.getAttribute(\'data-url\'),this.getAttribute(\'data-name\'),this.getAttribute(\'data-slug\'));" data-slug="'.$final.'" data-name="'.$name.'" data-url="'.$address.'" class="btn btn-large btn-default" style="background:#000"><i style="color:yellow;" class="fa fa-3x fa-bolt"></i></button></div>';
		    }
		    echo '</div>';
		    
		    echo '</div>';
		    
		}
		
		// These plugins have not address from (http://wordpress.org).
		
		foreach($notexist as $notex) {
			
			//$uri = $notex->pluginuri;
			//if($uri != NULL) {
			if( isset( $notex->pluginuri ) ){
				$uri = $notex->pluginuri;
				$name = $notex->name;
				$version = $notex->version;
				$author = $notex->author;
				$authoruri = $notex->authoruri;
				$detail = $notex->detail;

			} else {
				$uri = $notex['pluginuri'];
				$name = $notex['name'];
				$version = $notex['version'];
				$author = $notex['author'];
				$authoruri = $notex['authoruri'];
				$detail = $notex['detail'];
			}
			
			
			
			echo '<div class="col-md-3 thp_not notall">';
			echo '<div class="tp_top">';
			
			echo '<h4 class="pl_name">'.$name.'</h4>';
			echo '<button class="btn btn-large btn-default" rel="tooltip" title="There is no offical plugin\'s support in \'wordpres.org\'">No support..</button>';
			echo '</div>';
			
			
			echo '<div class="tp_center"></div>';
			echo '<div class="tp_bottom">';
			
			
			// plugin author
		    echo '<p class="pl_author">&copy; <a href="'.$authoruri.'">'.$author.'</a></p>';
		    echo '</div>';
		    echo '</div>';
		    
		}
		?>
		
		
		<?php
		
		// Save the data to the error log so you can see what the array format is like.
		//error_log( print_r( $all_plugins, true ) );
		
		?>
	
		
		</div>
		
	</div>
	
	<script>
	var $people = jQuery('#my-plugins'),
		$peopleli = $people.children('div');

	$peopleli.sort(function(a,b){
		var an = a.getAttribute('data-name'),
			bn = b.getAttribute('data-name');

		if(an < bn) {
			return 1;
		}
		if(an > bn) {
			return -1;
		}
		return 0;
	});

	$peopleli.detach().appendTo($people);

	jQuery('.searched').click(function(){
		var recent = jQuery(this).attr('data-search');
		jQuery('#searchtext').val(recent);
		submit_keyword_fc();

	});	
	</script>


	
	<?php
	echo '<input type="hidden" name="tp-ajax-nonce" id="tp-ajax-nonce" value="' . wp_create_nonce( 'tp-ajax-nonce' ) . '" />';
	
}

function thunder_port_detail() {
	

	if (isset($_GET['plugins'])&&isset($_GET['query']) || isset($_GET['plugins'])) {
		thunder_add_script();
	?>
	
	<div class="thp_logo" align="center">
		<a href="<?php echo site_url();?>/wp-admin/admin.php?page=thunder_port"><img src="<?php echo plugin_dir_url( __FILE__ );?>/img/thunder-logo.png"></a>
		<h2 id="plugintitle">&quot;<?php echo sanitize_text_field($_GET['plugins']);?>&quot;<?php echo '<br/>';?></h2>
		
		<h4 id="slogan">
			<input id="searchkeyword" class="highlight" style="text-decoration: underline;text-align:center;" value="<?php  echo sanitize_text_field($_GET['query']); ?>"></input>
			<input id="searchkeyword2" class="highlight" style="text-decoration: underline;text-align:center;" value="" placeholder="more keyword!"></input>
		</h4>
	</div>
	
	<script type="text/javascript">
	var plugins = '<?php echo sanitize_text_field($_GET["plugins"]);?>';
	jQuery('#searchkeyword').keydown(function(event){
		
	    if(event.keyCode==13){
	    	var keyword = jQuery('#searchkeyword').val();
	    	var url = "<?php echo site_url();?>/wp-admin/admin.php?page=thunder_port_detail&plugins="+plugins+"&query="+keyword;
	    	window.location.href = url;
	       
	       
	    }
	});
	jQuery('#searchkeyword2').keydown(function(event){
		
	    if(event.keyCode==13){
	    	var keyword2 = jQuery('#searchkeyword2').val();
	    	var i=0;
	    	
	    	jQuery("td#string a").each(function () {
			    if (jQuery(this).is(":contains('" + keyword2 + "')")) {
			    	i++;
			        jQuery(this).parent().parent().show();	
			        jQuery(this).parent().parent().children("#num").html(i);
			        if(keyword2 == '') {
				    	jQuery(this).css("color", "black");
				        
				    } else {
				    	jQuery(this).css("color", "red");
			        	
				    }
			    } else {
			        jQuery(this).css("color", "black");
			        jQuery(this).parent().parent().hide();
			    }
			});
			if(i==0) {
				if(jQuery('tbody').find('#noresult').length != 0) {
					
				} else {
					jQuery('tbody').append("<tr id='noresult'><td>No result.</td></tr>");
				}
			} else {
				jQuery('#noresult').remove();
			}
	    	
			

	    }
	});
	
	jQuery.fn.replaceText = function( search, replace, text_only ) {
		return this.each(function(){
	        var node = this.firstChild,
	        val, new_val, remove = [];
	        if ( node ) {
	            do {
	              if ( node.nodeType === 3 ) {
	                val = node.nodeValue;
	                new_val = val.replace( search, replace );
	                if ( new_val !== val ) {
	                  if ( !text_only && /</.test( new_val ) ) {
	                    jQuery(node).before( new_val );
	                    remove.push( node );
	                  } else {
	                    node.nodeValue = new_val;
	                  }
	                }
	              }
	            } while ( node = node.nextSibling );
	        }
	        remove.length && jQuery(remove).remove();
	    });
	};
	
	</script>
	
	
	
		<div class="row" style="width:100%" >
			<div id="success_loader"></div>
			<div class="col-md-12" id="search-form" align="center">
			</div>
			
			<?php
			$plugins = sanitize_text_field($_GET['plugins']);
			$query = sanitize_text_field($_GET['query']);
		
			$list = $plugins.'.json';
			$json = file_get_contents(''.plugin_dir_path( __FILE__ ) . 'json/'.$list.'');		
			
			$string = json_decode($json);
			$string = $string[0];
			$save = array();
			
			
			$final = array();
	
			$pluginname = str_replace('.json','',$list);

			if($string != NULL) {

				foreach ($string as $str) {
					// 문자열 분해 및 단어로 저장(title 한글 비중 체크를 위함)
					
					$decode_str = utf8_decode($str->title);
					
					$lower_decode_str = strtolower($decode_str);
					if($query == NULL) {
						$lower_query = '';
						$address = $str->address;
						$check = 'true';
						$sarray = array('plugin' => $pluginname, 'string' => $decode_str,'address' => $address, 'check' => $check);
						array_push($save, $sarray);
						$csave = count($save);
					} else {
						$lower_query = strtolower($query);
						//$word = explode(' ', $decode_str);
						$address = $str->address;
						// 문자열에 한글 포함 유무 체크
						 if (strpos($lower_decode_str,$lower_query) !== false ) {
							$check = 'true';
							// 문자열 배열에 저장
							$sarray = array('plugin' => $pluginname, 'string' => $decode_str,'address' => $address, 'check' => $check);
							array_push($save, $sarray);
						} else {
							$check = 'false';
							
						}
						$csave = count($save);
					}

				}
		
				$count_save = count($save);
				$result = whatever($save, 'check', 'true');	
		
				array_push($final, $result);
				?>
				
				<table class="table">
	    			<thead>
				      <tr>
				        <th>Num</th>
				        <th>Question</th>
				      </tr>
				    </thead>
				    <tbody>
				<?php
				$i=0;
				foreach ($final[0] as $fi) {
					$i++;
					$links = '<tr class="result">';
					$links .= '<td id="num">'.$i.'</td>';
					$fi_string = urlencode($fi["string"]);
					$redirecturl = add_query_arg(array('url' => ''.$fi['address'].'', 'plugin' => ''.$plugins.'','q'=>''.$fi_string.'' ), admin_url('admin.php?page=thunder_port'));				
					$links .= '<td id="string"><a href="'.$redirecturl.'" class="q_link" target="_blank" style="color:black;">'.$fi["string"].'</a></td>';
					$links .= '</tr>';
					echo $links;
					
				}
				?>
				</table>
				<script>
				jQuery('.result #string a').each(function() {
					var query = "<?php echo $query;?>";					
					var re = new RegExp(query,"gi");
					jQuery(this).replaceText(re, '<span class=\"highlight\">'+query+'</span>');	
				})
				
				</script>
				<?php

			} else {
				echo '<h4 style="text-align:center;">No results...</h4>';
			}
			
			
		
	}

}

?>