
(function($) {

	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = jQuery( '#edit-' + $post_id );
			var $post_row = jQuery( '#post-' + $post_id );
			var $current_featured = jQuery( '.column-proizvod_feat', $post_row ).find('img').attr('src');
			var $fid = jQuery( '.column-proizvod_feat', $post_row ).find('img').attr('id');
		    var $featured_img_id = (typeof $fid !== typeof undefined && $fid !== false) ? $fid.slice(11) : false;
			var $existing_featured = ($current_featured.substring($current_featured.lastIndexOf('/')+1) == "fill-proizvod.png" || $current_featured.substring($current_featured.lastIndexOf('/')+1) ==  "fill-proizvod-150x110.png") ? false : $current_featured;


			var $proizvod_content = jQuery( '.column-proizvod_content', $post_row ).text();
			var $proizvod_napomene = jQuery( '.column-proizvod_napomene', $post_row ).text();
			var $proizvod_cijena = jQuery( '.column-proizvod_cijena', $post_row ).text();
			var $proizvod_sifra = jQuery( '.column-proizvod_sifra', $post_row ).text();
			var $proizvod_pdf = jQuery( '.column-proizvod_pdf', $post_row ).text();
			var $proizvod_featured_src = $existing_featured ? $existing_featured : "../wp-content/plugins/pro-cptc/assets/images/fill-proizvod.png";
			
			// onesposobi "ukloni" za placeholder image
			if (!$existing_featured){jQuery("#remove", $edit_row ).css({"display" : "none"});}
			

			
			//if ((jQuery( 'input#proizvod_pdf', $edit_row ).val() == false && $proizvod_pdf == false) || ($featured_img_id == false)){
				//jQuery("#remove_pdf", $edit_row ).css({"display" : "none"});			
			//}
			
			jQuery( ':input[name="proizvod_content"]', $edit_row ).val( $proizvod_content );
			jQuery( ':input[name="proizvod_napomene"]', $edit_row ).val( $proizvod_napomene );
			jQuery( ':input[name="proizvod_cijena"]', $edit_row ).val( $proizvod_cijena );
			jQuery( ':input[name="proizvod_sifra"]', $edit_row ).val( $proizvod_sifra );
			jQuery( ':input[name="proizvod_pdf"]', $edit_row ).val( $proizvod_pdf );
			
			
			if ($featured_img_id) {
				jQuery( '#proizvod_feat_id', $edit_row ).val( $featured_img_id );
			} 
			
			if (!$proizvod_pdf){jQuery("#remove_pdf", $edit_row ).css({"display" : "none"});}
			
			
			jQuery("#featured_img", $edit_row ).css({"background-image": "url(" + $proizvod_featured_src + ")"});
			
			console.log("input#proizvod_pdf.val =  " + jQuery( 'input#proizvod_pdf', $edit_row ).val());
			console.log("post_row.column-proizvod_pdf.text =  " + $proizvod_pdf);
		}
	};

})(jQuery);
