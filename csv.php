<?php 

function csv_upload_page() {
	

    wp_enqueue_script('jquery');
    wp_enqueue_media();
    ?>
    
<link rel="stylesheet" href="<?php echo plugins_url('assets/jquery-ui.css', __FILE__); ?>" />
<script src="<?php echo plugins_url('assets/jquery-ui.js', __FILE__); ?>"></script>

<style type="">
#csv-php {width:100%; height:100%; margin:0px auto; padding-top:80px; text-align:left; position:relative;}
#csv-php .csv-buttons {height:80px; width:380px; margin:20px auto 0px auto; font-size:1.3rem; color:#5f5f5f; font-weight:bold;}
#csv_status-button { width:380px;  font-size:1.1rem; color:#5f5f5f; font-weight:bold; }
#csv-progressbar {height:80px; width:380px; margin:160px auto 0px auto; text-align:center;}
#csv-progressbar div {}

.csv_result_row {clear:both;}
.csv_result_row h4 {display:block-inline;}
#csv-response {  width:680px; margin:20px auto 0px auto; text-align:right; }

.ui-progressbar {
position: relative;
text-align:center;
}
.progress-label {
position: absolute;
margin:auto;
top: 40%;
left:32%;
font-weight: bold;
text-shadow: 2px 2px 0 #999;
color:#fff;
text-align:center;
}
</style>


    <div id="csv-php" style="">
		<div style="text-align:center;">
            <input id="cptc_csv_noncename" type="hidden" name="cptc_csv_noncename" value="" />
            <input type="hidden" id="cptc_csv"  name="cptc_csv" value=""/>
            <div id="csv_filename" class="csv-buttons" style="height:auto; display:none;"></div><br />
            <input type="button" id="csv_select" class="csv-buttons" value="Uploadaj tablicu proizvoda" />
            
			<select name="csv_status" id="csv_status" class="csv-buttons" style="display:none;">
				<option selected="selected" value="null">Odaberi status uvezenih proizvoda</option>
				<option value="publish">Published</option>
				<option value="draft">Draft</option>
			</select><br />
            <input type="button" id="csv_submit" class="csv-buttons" value="Uvezi" style="display:none;"/>
            <div class="csv-buttons" id="csv-progressbar" style="display:none;"><div class="progress-label"></div></div>
            <br />
            <br />


            <div id="csv-response"></div>
        </div>

    </div>
    
  
    
    
<script type="text/javascript">
jQuery(document).ready(function($){
	

	$( "input[type=button]" ).button()

    $('input#csv_select').click(function(e) {
        e.preventDefault();
        var csv = wp.media({ 
            title: 'Odaberi tablicu s proizvodima:',
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_csv = csv.state().get('selection').first();
            var logobj = csv;
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            csv_tablica = uploaded_csv.toJSON();
            jQuery('input#cptc_csv').val(csv_tablica.id);
            jQuery('#csv_filename').css({'display':'block'}).text('Uvoz ' + csv_tablica.filename + ' ...');
            jQuery('#csv_select').css({'display':'none'});
            jQuery('#csv_submit').css({'display':'inline-block'});
            jQuery('#csv_status').css({'display':'inline-block'}).selectmenu();
        });
    });
    
	$('input#csv_submit').click(function(){
     
        // turn on progressbar
		 jQuery('#csv_submit').css({'display':'none'});
		 jQuery('#csv_status').css({'display':'none'});
		 jQuery('#csv_status-button').css({'display':'none'});
         $( "#csv-progressbar" ).css({'display':'block'}).progressbar({value: false});
			
        var data = {
			'action': 'csv_reactor',
			'cid': jQuery('input#cptc_csv').val(),
			'csv_status': jQuery('#csv_status').val() 
		};
        
         
        /*
         * 'post_receiver.php' - where you will pass the form data
         * $(this).serialize() - to easily read form data
         * function(data){... - data contains the response from post_receiver.php
         */
        $.post(ajaxurl, data, function(data){
             
            //// show the response
             $('#csv-response').html(data);
             $( "#csv-progressbar" ).progressbar( "option", {value: 100});
             $( "#csv-progressbar div" ).css({'background':'#00C92C'});
             $( "#csv-progressbar .progress-label" ).text('Uvoz uspješan');
             
        }).fail(function() {
         
            //// just in case posting your form failed
            alert( "Posting failed." );
             
        });
 
        // to prevent refreshing the whole page page
        return false;
        
    });   
});
</script> 
 
     <?php }


function add_admin_page(){
	if ( function_exists( 'add_submenu_page' ) ) {
		add_submenu_page( 'edit.php?post_type=proizvod', 'CSV Upload', 'CSV Upload', 'manage_options', 'csv_upload_proizvoda', 'csv_upload_page' );
	}
}
			
add_action( 'admin_menu', 'add_admin_page' );


add_action( 'wp_ajax_csv_reactor', 'csv_file_process' );

function csv_file_process() {
	global $wpdb; // this is how you get access to the database

	$cid = intval( $_POST['cid'] );
	$status = intval( $_POST['csv_status'] );
	if ($status == "null"){$status = "draft";}
	
	$csv_file = get_attached_file( $cid );
	
	$csv = array_map("str_getcsv", file($csv_file, FILE_SKIP_EMPTY_LINES));
	$keys = array_shift($csv);	
	foreach ($csv as $i=>$row) { $csv[$i] = array_combine($keys, $row); }
	
	// sad $csv array sadrži dijelove, sad ih treba unijeti.
	$report = array();
	$upload_dir = wp_upload_dir();
	$upload_url = $upload_dir['url'];
	foreach ($csv as $row){
		
		$post_ins = array('post_title'=>'');
		$post_meta = array('content'=>'', 'cijena'=>'', 'cat_slug'=>'', 'manufacturer_slug'=>'', 'primjedba'=>'');
				
		$post_ins = array_intersect_key($row, $post_ins);
		$post_meta = array_intersect_key($row, $post_meta);
	
		$post_ins['post_type'] = 'proizvod';
		$post_ins['post_status'] = $status;
		
		$proizvod_id = wp_insert_post($post_ins);
		
		
		$proizvod_cat = get_term_by("slug", $post_meta['cat_slug'], "proizvod_category");
		$proizvod_man = get_term_by("slug", $post_meta['manufacturer_slug'], "proizvod_manufacturer");
		wp_set_object_terms( $proizvod_id, $proizvod_cat->term_id, 'proizvod_category' );
		wp_set_object_terms( $proizvod_id, $proizvod_man->term_id, 'proizvod_manufacturer' );
		
		unset($post_meta['cat_slug']);
		unset($post_meta['manufacturer_slug']);
		
		if ($row['pdf']){
			$post_meta['pdf'] = $upload_url . '/' . $row['pdf'];
		}
		
		foreach ($post_meta as $meta_key => $meta_value){
			add_post_meta($proizvod_id, $meta_key, $meta_value);
		}
		
		$report[$proizvod_id] = get_post($proizvod_id)->post_title;
		
	
	} 

	

    $response = '';
    foreach ($report as $rkey => $rval){
		$response .= '<div class="csv_result_row"><h4 style="float:left;">'. $rval .' &nbsp; - </h4> <h4 style="float:right;"><a href="'. get_edit_post_link( $rkey, '' ) .'">uredi</a> &nbsp; &nbsp; &nbsp;<a href="'. get_permalink( $rkey ) .'">pregledaj</a></h4></div><br />';
	}
	
	
	echo $response;

	wp_die();
}

