<?php

/*
 *
 * @link              http://stuproizvod.croati.co
 * @since             1.0.0
 * @package           cpt-catalogue
 *
 * @wordpress-plugin
 * Plugin Name:       CPT Catalogue
 * Plugin URI:        http://stuproizvod.croati.co
 * Description:       Custom Post Type catalogue - with CSV bulk import w custom fields and advanced quick-edit
 * Version:           1.0.0
 * Author:            Tonino Jankov
 * Author URI:        http://stuproizvod.croati.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cpt-catalogue
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'csv.php' );


add_action('init', 'register_cptc_plugin');
add_action('wp_footer', 'print_cptc_plugin');

function register_cptc_plugin() {
	wp_register_style('cptc-plugin-style', plugins_url('assets/cptc.plugin.css', __FILE__), array(), '1.0', 'all');
}

function print_cptc_plugin() {
	//global $add_cptc_plugin;
	//if ( ! $add_cptc_plugin )
		//return;
	
	wp_print_styles('cptc-plugin-style');
}



add_action( 'init', 'create_cptc_post_type' );
 
function create_cptc_post_type() {
    $args = array(
                  'description' => 'cptc Post Type',
                  'show_ui' => true,
                  'show_in_menu' => true,
                  'menu_position' => 20,
                  'menu_icon' => 'dashicons-hammer',
                  'exclude_from_search' => false,
                  'labels' => array(
                                    'name'=> 'cptc',
                                    'singular_name' => 'Dio',
                                    'add_new' => 'Dodaj novi proizvod',
                                    'add_new_item' => 'Dodaj novi proizvod',
                                    'edit' => 'Uređuj djelove',
                                    'edit_item' => 'Uredi proizvod',
                                    'new-item' => 'Novi proizvod',
                                    'view' => 'Pregledaj dijelove',
                                    'view_item' => 'Pogledaj proizvod',
                                    'search_items' => 'Pretraži dijelove',
                                    'not_found' => 'Nije pronađen ni jedan proizvod',
                                    'not_found_in_trash' => 'Nije pronađen ni jedan proizvod u smeću',
                                    'parent' => 'Parent proizvod'
                                   ),
                 'public' => true,
                 'has_archive' => true,
                 'capability_type' => 'post',
                 'hierarchical' => false,
                 'rewrite' => array('slug' => 'cptc_i_oprema'),
                 'supports' => array('title', 'thumbnail', 'comments', 'custom-fields', 'revisions')
                 );
    register_post_type( 'proizvod' , $args );
}


/*====================================================
Register Custom Taxonomies
======================================================*/
 
add_action('init', 'register_proizvod_taxonomy');
 
function register_proizvod_taxonomy() {
  register_taxonomy('proizvod_category',
                    'proizvod',
                     array (
                           'labels' => array (
                                              'name' => 'Kategorije',
                                              'singular_name' => 'Kategorija',
                                              'search_items' => 'Pretraži kategorije proizvoda',
                                              'popular_items' => 'Popularne kategorije proizvoda',
                                              'all_items' => 'Sve kategorije proizvoda',
                                              'parent_item' => 'Nad Kategorija proizvoda',
                                              'parent_item_colon' => 'Nad Kategorija proizvoda:',
                                              'edit_item' => 'Uredi kategoriju proizvoda',
                                              'update_item' => 'Ažuriraj kategoriju proizvoda',
                                              'add_new_item' => 'Dodaj novu kategoriju proizvoda',
                                              'new_item_name' => 'Nova kategorija proizvoda',
                                            ),
                            'hierarchical' =>true,
                            'show_ui' => true,
                            'show_tagcloud' => true,
                            'rewrite' => array('slug' => 'product_category'),
                            'public'=>true
                            )
                     );
                     
   register_taxonomy('proizvod_manufacturer',
                     'proizvod',
                      array (
                           'labels' => array (
                                               'name' => 'Proizvođač',
                                               'singular_name' => 'Proizvođač',
                                               'search_items' => 'Pretraži proizvođače proizvoda',
                                               'popular_items' => 'Popularni proizvođači proizvoda',
                                               'all_items' => 'Svi proizvođači proizvoda',
                                               'parent_item' => 'Nad proizvođač proizvoda',
                                               'parent_item_colon' => 'Nad proizvođač proizvoda proizvoda:',
                                               'edit_item' => 'Uredi proizvođača proizvoda',
                                               'update_item' => 'Ažuriraj proizvođača proizvoda',
                                               'add_new_item' => 'Dodaj novog proizvođača proizvoda',
                                               'new_item_name' => 'Novi proizvođač proizvoda',
                                             ),
                             'hierarchical' =>false,
                             'show_ui' => true,
                             'show_tagcloud' => true,
                             'rewrite' => false,
                             'public'=>true
                             )
                      );
}





////////////////////////////////////////////////////////////////////////////

add_filter("manage_edit-proizvod_columns", "proizvod_edit_columns");
 
function proizvod_edit_columns($columns){
   $columns = array(
                    "cb" => "<input type='checkbox' />",
                    "proizvod_feat" => __("Featured Image"),
                    "title" => __("Naziv"),
                    "proizvod_content" => __("Opis"),
                    "proizvod_category" => __("Kategorija"),
                    "proizvod_cijena" => __("Cijena"),
                    "proizvod_sifra" => __("Šifra proizvoda"),
                    "proizvod_pdf" => __("Pdf katalog"),     
                    "date" => __("Datum"),
                    "proizvod_napomene" => __("Napomene"),
                   );
 
   return $columns;
}
 
add_action("manage_proizvod_posts_custom_column",  "proizvod_custom_columns");
 
function proizvod_custom_columns($column){
  global $post;
  switch ($column){
                 case "proizvod_feat":
                     if(has_post_thumbnail()) {?><?php the_post_thumbnail(array(150,110), array("id"=>"proizvod-attch-".get_post_thumbnail_id( $post->ID ))); ?><?php } else {?><img src="<?php echo plugins_url('assets/images/fill-proizvod.png', __FILE__); ?>" /><?php } 
                 break;
                 case "proizvod_category":
                     echo get_the_term_list($post->ID, 'proizvod_category', '', ', ','');
                 break;
                 case "proizvod_manufacturer":
                     echo get_the_term_list($post->ID, 'proizvod_manufacturer', '', ', ','');
                 break;
                 case "proizvod_cijena":
                     echo  get_post_meta($post->ID, 'cijena', true);
                 break;
                 case "proizvod_content":
                     echo  get_post_meta($post->ID, 'content', true);
                 break;
                 case "proizvod_napomene":
                     echo  get_post_meta($post->ID, 'napomene', true);
                 break;
                 case "proizvod_pdf":
                     echo  get_post_meta($post->ID, 'pdf', true);
                 break;
                 case "proizvod_sifra":
                     echo  get_post_meta($post->ID, 'sifra', true);
                 break;
   }
}

///////////////////////////////////////////////////////////////////////
// proširujemo quick edit proizvoda
///////////////////////////////////////////////////////////////////////


// Add to our admin_init function
add_action('quick_edit_custom_box',  'qe_add_proizvod_content', 10, 2);
add_action('quick_edit_custom_box',  'qe_add_proizvod_napomene', 10, 2);
add_action('quick_edit_custom_box',  'qe_add_proizvod_cijena', 10, 2);
add_action('quick_edit_custom_box',  'qe_add_proizvod_pdf', 10, 2);
add_action('quick_edit_custom_box',  'qe_add_proizvod_sifra', 10, 2);
add_action('quick_edit_custom_box',  'qe_add_proizvod_feat', 10, 2);
 
function qe_add_proizvod_content($column_name, $post_type) {
    if ($column_name != 'proizvod_content') return;
    ?>
    <fieldset class="inline-edit-col-center">
        <div class="inline-edit-col">
            <span class="title">Opis</span><br />
            <input id="proizvod_content_noncename" type="hidden" name="proizvod_content_noncename" value="" />
            <textarea rows="5" cols="42" id="proizvod_content"  name="proizvod_content" style="max-width:287px;"></textarea> 
        </div>
    </fieldset>
     <?php
}
 
function qe_add_proizvod_napomene($column_name, $post_type) {
    if ($column_name != 'proizvod_napomene') return;
    ?>
    <br />
    <fieldset class="inline-edit-col-right" style="width:100%;">
        <div class="inline-edit-col">
            <span class="title">Napomene</span><br />
            <input id="proizvod_napomene_noncename" type="hidden" name="proizvod_napomene_noncename" value="" />
            <textarea rows="4" id="proizvod_napomene"  name="proizvod_napomene" style="width:100%;"></textarea> 
        </div>
    </fieldset>
     <?php
}

function qe_add_proizvod_cijena($column_name, $post_type) {
    if ($column_name != 'proizvod_cijena') return;
    ?>
    <fieldset class="inline-edit-col-right" style="float:right; text-align:right; margin-top:14px;">
        <div class="inline-edit-col">
            <span class="title">Cijena</span>
            <input id="proizvod_cijena_noncename" type="hidden" name="proizvod_cijena_noncename" value="" />
            <input type="text" id="proizvod_cijena"  name="proizvod_cijena" value=""/>
        </div>
    </fieldset>
     <?php
}

function qe_add_proizvod_sifra($column_name, $post_type) {
    if ($column_name != 'proizvod_sifra') return;
    ?>
    <fieldset class="inline-edit-col-right" style="float:right; text-align:right; margin-top:12px;">
        <div class="inline-edit-col">
            <span class="title">Šifra</span>
            <input id="proizvod_sifra_noncename" type="hidden" name="proizvod_sifra_noncename" value="" />
            <input type="text" id="proizvod_sifra"  name="proizvod_sifra" value=""/>
        </div>
    </fieldset>
     <?php
}

function qe_add_proizvod_pdf($column_name, $post_type) {
    if ($column_name != 'proizvod_pdf') return;
    wp_enqueue_script('jquery');
    wp_enqueue_media();
    ?>
    <fieldset class="inline-edit-col-right" style="float:right; text-align:right; margin-top:12px;">
        <div class="inline-edit-col">
            <span class="title">Pdf</span><span id="remove_pdf" class="pd-remove-link"></span>
            <input id="proizvod_pdf_noncename" type="hidden" name="proizvod_pdf_noncename" value="" />
            <input type="text" id="proizvod_pdf"  name="proizvod_pdf" value=""/>
        </div>
    </fieldset>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('input#proizvod_pdf').click(function(e) {
        e.preventDefault();
        var pdf = wp.media({ 
            title: 'Postavi pdf',
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_pdf = pdf.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var katalog = uploaded_pdf.toJSON();
            $('input#proizvod_pdf').val(katalog.url);
            jQuery("#remove_pdf").css({"display" : "inline"});
        });
    });
});





</script>
<div style="width:100%;clear:both;height:8px;"></div>  
     <?php
}


function qe_add_proizvod_feat($column_name, $post_type) {
    if ($column_name != 'proizvod_feat') return;
    wp_enqueue_script('jquery');
    wp_enqueue_media();
    ?>

<div style="width:100%;clear:both;height:8px;"></div>    
<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
	    <label for="image_url">Featured Image</label>
	    <input id="proizvod_feat_noncename" type="hidden" name="proizvod_feat_noncename" value="" />
	    <input id="proizvod_feat_id" type="hidden" name="proizvod_feat_id" value="" />
	    <div alt="" class="wp-post-image" id="featured_img" style="" >
			<div id="featured_img_cover" style="height:110px;width:100%; display:none; background:rgba(0,0,0,.2); position:relative;"><p id="remove" class="pd-remove-link" style="position:absolute; top:4px;right:4px;" title="ukloni"></p></div>
		</div>
	</div>
</fieldset>
<style type="text/css">
#featured_img.wp-post-image {width:150px; background-size:contain; height:110px; cursor:pointer; border:1px dotted #ccc;background-size:contain; background-repeat:no-repeat; }
.pd-remove-link {color:#c00; font-size:0.9rem; padding-left:8px; cursor:pointer;}
.pd-remove-link:before {   
	content: "";
    font: 400 20px/1 dashicons;
    vertical-align: middle;
    color:#c00;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#featured_img').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Postavi sliku',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var img = uploaded_image.toJSON();
            // Let's assign the url value to the input field
            $('#proizvod_feat_id').val(img.id);
            $("#featured_img").css({"background-image": "url(" + img.url + ")" });
            $("#remove").css({"display": "block"});
        });
    });
    
    $('#remove').click(function(e) {
		e.stopPropagation();
		$('#proizvod_feat_id').val("");
		$("#featured_img").css({"background-image": "url(<?php echo plugins_url('assets/images/fill-proizvod.png', __FILE__); ?>)"});
		$(this).css({"display": "none"});
	});
    
    $('#remove_pdf').click(function(e) {
		e.stopPropagation();
		$('input#proizvod_pdf').val("");
		$(this).css({"display": "none"});
	});
    
    $('#featured_img').hover(function(e) {
		$('#featured_img_cover').slideToggle();
	});
});
</script>
     <?php
}



add_action('save_post_proizvod', 'proizvod_save_quick_edit_data');  
 
function proizvod_save_quick_edit_data($post_id) {    
  // verify if this is an auto save routine.        
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )          
      return $post_id;        
  // Check permissions    
      
    if ( !current_user_can( 'edit_page', $post_id ) )            
      return $post_id;    
      
  // Authentication passed now we save the data      
  if (isset($_POST['proizvod_content'])) {
        $proizvod_content = esc_attr($_POST['proizvod_content']);
        if ($proizvod_content)
            update_post_meta( $post_id, 'content', $proizvod_content);
        else
            delete_post_meta( $post_id, 'content');
  }
  if (isset($_POST['proizvod_napomene'])) {
        $proizvod_napomene = esc_attr($_POST['proizvod_napomene']);
        if ($proizvod_napomene)
            update_post_meta( $post_id, 'napomene', $proizvod_napomene);
        else
            delete_post_meta( $post_id, 'napomene');
  }
  if (isset($_POST['proizvod_cijena'])) {
        $proizvod_cijena = esc_attr($_POST['proizvod_cijena']);
        if ($proizvod_cijena)
            update_post_meta( $post_id, 'cijena', $proizvod_cijena);
        else
            delete_post_meta( $post_id, 'cijena');
  }
  if (isset($_POST['proizvod_sifra'])) {
        $proizvod_sifra = esc_attr($_POST['proizvod_sifra']);
        if ($proizvod_sifra)
            update_post_meta( $post_id, 'sifra', $proizvod_sifra);
        else
            delete_post_meta( $post_id, 'sifra');
  }
  if (isset($_POST['proizvod_pdf'])) {
        $proizvod_pdf = esc_attr($_POST['proizvod_pdf']);
        if ($proizvod_pdf)
            update_post_meta( $post_id, 'pdf', $proizvod_pdf);
        else
            delete_post_meta( $post_id, 'pdf');
  }
  
  if (isset($_POST['proizvod_feat_id'])) {
        $proizvod_feat_id = esc_attr($_POST['proizvod_feat_id']);
        if ($proizvod_feat_id)
            set_post_thumbnail( $post_id, $proizvod_feat_id );
        else
            delete_post_thumbnail( $post_id );
        
  }
}




//////////////////////////////////////////////////////////////////////
// pre-populiramo quick edit polja
//////////////////////////////////////////////////////////////////////

/* load script in the footer */
if ( ! function_exists('proizvod_qe_admin_enqueue_scripts') ):
function proizvod_qe_admin_enqueue_scripts( $hook ) {

	if ( 'edit.php' === $hook &&
		isset( $_GET['post_type'] ) &&
		'proizvod' === $_GET['post_type'] ) {
		wp_enqueue_script( 'populate-proizvod-admin', plugins_url('assets/cptc.plugin.js', __FILE__), false, null, true );
	}

}
endif;
add_action( 'admin_enqueue_scripts', 'proizvod_qe_admin_enqueue_scripts' );


//////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////

if ( isset($_GET['post_type']) ) {
   $post_type = $_GET['post_type'];
}else {
   $post_type = '';
}
 
if ( $post_type == 'proizvod' ) {
   add_action( 'restrict_manage_posts','proizvod_filter_list' );
   add_filter( 'parse_query','perform_filtering' );
}
 
function proizvod_filter_list() {
   global $typenow, $wp_query;
   if ($typenow=='portfolio') {
      wp_dropdown_categories(array(
                                   'show_option_all' => 'Prikaži sve kategorije proizvoda',
                                   'taxonomy' => 'proizvod_category',
                                   'name' => 'proizvod_category',
                                   'orderby' => 'name',
                                   'selected' =>( isset( $wp_query->query['proizvod_category'] ) ? $wp_query->query['proizvod_category'] : '' ),
                                   'hierarchical' => false,
                                   'depth' => 3,
                                   'show_count' => false,
                                   'hide_empty' => true,
                            ));
 
   }
}
 
function perform_filtering( $query ){
   $qv = &$query->query_vars;
   if (( $qv['proizvod_category'] ) && is_numeric( $qv['proizvod_category'] ) ) {
      $term = get_term_by( 'id', $qv['proizvod_category'], 'proizvod_category' );
      $qv['proizvod_category'] = $term->slug;
   }
}

//////////////////////////////////////////////////////////////////////
/// pre_get_posts za cptc template
//////////////////////////////////////////////////////////////////////



//function proizvod_posts_cptc_template( $query ){
 
    //if ( ! is_admin() && $query->is_main_query() && (is_page( 'cptc_i_oprema' ) )){
        //$query->set( 'post_type', 'proizvod' );
    //}
    

 
//}

//add_action( 'pre_get_posts', 'proizvod_posts_cptc_template' );


//////////////////////////////////////////////////////////////////////


function prod_pagination($pagenumber, $pages = '', $range = 2){
	$showitems = ($range * 2)+1;  
	 
	$paged = $pagenumber;
	if(empty($paged)) $paged = 1;

	if($pages == '')
	 {
		 
	   global $wp_query;
	   $pages = $wp_query->max_num_pages;
			 
	    if(!$pages)
		 {
				 $pages = 1;
		 }
	}   
	 
	if(1 != $pages)
	{
		$html = "<div class=\"prod_pagination\">  ";  
	
		 if($paged > 2 && $paged > $range+1 && $showitems < $pages) {
			 $html .= '<div class="pagination_left_cont pagination_inline_cont">';
			 $html .= '<a id="first_p" class="pagination_plink" href="'. esc_url( get_pagenum_link( 1 ) ) .'">&laquo; '.__("Prva","pro-cptc").'</a>';
			 $previous = $paged - 1;
			 $html .= get_previous_posts_link("« Prethodna");
			 $html .= '</div>';
		 }
	
	 $html .= '<div class="pagination_center_cont pagination_inline_cont">';
	 for ($i=1; $i <= $pages; $i++)
	  {
		 if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
		 {
		 $html .= ($paged == $i)? '<span class="pagination_plink pcurrent" style="color:#dd1100;">'.$i.'</span>': '<a id="" href="'. esc_url( get_pagenum_link( $i ) ) .'" class="pagination_plink">'.$i.'</a>';
		 }
	 }
	 $html .='</div>';
	
	 $html .= '<div class="pagination_right_cont pagination_inline_cont">';
	 if ($paged < $pages && $showitems < $pages){
		 
		 $next = $paged + 1;
		 $html .= get_next_posts_link("Iduća »");
	 }
	
	 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
		 $html .= '<a id="last_p" class="pagination_plink"  href="'. esc_url( get_pagenum_link( $pages ) ) .'">'.__("Posljednja","pro-cptc").' &raquo;</a>';
	 }
	 
	 $html .= "</div>\n";
	 $html .= "</div>\n";
	 $max_num_pages = $pages;
	 return $html;
	}
		 
		 
}// pagination


?>
