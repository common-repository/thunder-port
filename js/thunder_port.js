$.fn.replaceText = function( search, replace, text_only ) {
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
                    $(node).before( new_val );
                    remove.push( node );
                  } else {
                    node.nodeValue = new_val;
                  }
                }
              }
            } while ( node = node.nextSibling );
        }
        remove.length && $(remove).remove();
    });
};

function enter_input_keyword() {
	console.log('clicked!');
	$("#submit_keyword").click();
}

$('#submit_keyword').click(function(){
	var query = $('#searchtext').val();
	console.log(query);
	var datas = {
		'query' : query
	}
	$('#success').show();
	$.ajax({            
        method: "POST",
        url: '<?php echo plugin_dir_url( __FILE__ );?>tp_search.php',
        data: datas, 
        dataType: 'json',
        success: function(datas) {
        	if($('.tp_center').find('.descbox').length != 0) {
        		$('.descbox').each(function(){
            		$(this).remove();
            	});	
        	}
        	$('.col-md-3').show();
        	var data = datas['data'];
        	var notsave = datas['notsave'];
        	console.log(notsave);
        	for(i=0;i<data.length;i++) {
        		var ldata = data[i].length;
        		if(ldata != 0) {
        			console.log(data[i]);
        			
        			var plsource_0 = data[i][0]['plugin'];
        			var plid_0 = '#' + plsource_0 + '_00';
        			$(plid_0).parent().show();
        			$(plid_0).append('<div class="descbox" id="'+ plsource_0+ '_0"></div>');
        			for(j=0;j<data[i].length;j++) {
        				var pladdr = data[i][j]['address'];
        				var pldesc = data[i][j]['string'];
        				var plsource = data[i][j]['plugin'];
        				var plid = '#' + plsource + '_0';
        				$(plid).append('<a href="'+pladdr+'" class="q_link"><p id="'+plsource+'_p'+j+'">'+pldesc+'</p></a>');
        				var plid_l = '#' + plsource + '_p'+j;
        				var re = new RegExp(query,"gi");
        				$(plid_l).replaceText(re, '<span class="highlight">'+query+'</span>');
        			}
        		} else {
        			$('.col-md-3').hide();
        		}
        	}
        	$('#success').hide();
        
        },error: function(jqXHR, textStatus, errorThrown) {
        	console.log('tp_search.php error');
        	$('#success').hide();
            
        }
    });
});


function get_supports(dataurl,dname) {
	var dataform = {
		'pluginurl':dataurl,
		'pluginname':dname,
	};
	console.log(dataform);
	//$('#success').show();
	var changeids = '#' + dname;
	var html_ore = $(changeids).html();
    $(changeids).html('<img src="<?php echo plugin_dir_url( __FILE__ );?>loader.gif">');

	$.ajax({            
        method: "POST",
        url: '<?php echo plugin_dir_url( __FILE__ );?>get_support.php',
        data: dataform, 
        dataType: 'json',
        success: function(data) {
        	$('#success').hide();
            var t = parseInt(data.data.number);
            var s = dataurl;
            var pl = data.data.pluginname;
            var dataform2 = {
            	'pluginurl':s,
            	'pluginname':pl,
            	'page':t
            };
            
            console.log('get_support.php success');
            var answer = confirm ("Do you want to parse questions on support page?\nLoading time : " + data.duration + ' seconds')
			if (answer) {
				
				$.ajax({            
		            method: "POST",
		            url: '<?php echo plugin_dir_url( __FILE__ );?>indexing.php',
		            data: dataform2, 
		            dataType: 'json',
		            success: function(datas) {
		            	$('#success').hide();
		                console.log('indexing.php success');
		                alert('Success!');
		                var changeid = '#' + dname;

						$(changeid).html('<button class="btn btn-large btn-info">completed</button>');
		                var f_array = new Array(); 
		                for(i=0;i<datas[0].length;i++) {
		                	var datai = datas[0][i];
		                	if(datai.title == "" && datai.address == "") {
		                		
		                	} else {
		                		f_array.push(datai);
		                	}
		                	
		                }
		                
		            },error: function(jqXHR, textStatus, errorThrown) {
            			console.log('indexing.php error');
            			$(changeids).html(html_ore);
            		}
                });
			} else {
				alert ("canceled!")	
				$(changeids).html(html_ore);
			}
			
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
        	console.log('get_support.php error');
        	$(changeids).html(html_ore);
            
        }
    });
}