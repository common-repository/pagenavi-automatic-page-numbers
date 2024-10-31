<?php
/*
Plugin Name: PageNavi Automatic Page Numbers
Plugin URI:
Description: Automatically adds page numbers at the bottom for easier navigation
Author: EnergieBoer
Version: 1.06
License: GPLv2  or later
*/
class pagenavi_automic_pagenumbers {
public function go_page_navi( $args = '' ) {
	if ( ! ( is_archive() || is_home() || is_search() ) ) { return; }
	global $wp_query,$paged;
	$maxNumPages =  10;
	$pageOfPageText = get_option('pagenavi_auto_page') . " %u " . get_option('pagenavi_auto_of') . " %u";
	$prevPage = "&lt;";
	$nextPage = "&gt;";
	$isFirstLastNumbers = true;
	$isFirstLastGap = true;
	$firstGap = "...";
	$lastGap = "...";

	$total_pages = $wp_query->max_num_pages; // total pages in category
	if ($total_pages==1) { return null;}  // ends function if there is only one page.

	$current_page = (!empty($paged)) ? $paged : 1; // current page

	$min_page = $current_page - floor(intval($maxNumPages)/2); // works out the lowest page number to be displayed
	$maxNumPages = (intval($maxNumPages)-1);
	if ($min_page<1) $min_page=1;
	$max_page = $min_page + $maxNumPages; // words out the highest page number to be displayed
	if ($max_page>$total_pages) $max_page=$total_pages;
	if ($max_page==$total_pages && $max_page>$maxNumPages) $min_page= ($max_page-$maxNumPages); // changes min_page if max is last page

	$pagingString = "<ul class='page_navi'>"; // builds output

	// displays "Page x of y"
	$pagingString.= sprintf("<li class='page_info'>".$pageOfPageText."</li>",floor ($current_page),floor($total_pages));

	// displays link to previous page
	if($current_page!=1)
		$pagingString.=sprintf("<li><a href='%s'>%s</a></li>",get_pagenum_link($current_page-1),$prevPage);

	// displays page 1 link and ellipses when min page is more than 1
	if ($min_page>1) {
		if ($isFirstLastNumbers) $pagingString.= sprintf("<li class='first_last_page'><a href='%s'>%u</a>",get_pagenum_link(1),1);
		if ($isFirstLastGap) $pagingString.= sprintf("<li class='space'>%s</li>",$firstGap);
		}

	// displays lowest to highest page
	for($i=$min_page; $i<=$max_page; $i++)
		$pagingString.= ($current_page == $i) ?
			sprintf("<li class='current'><span><a>%u</a></span></li>",$i) :
			sprintf("<li %s><a href='%s'>%u</a></li>",($current_page == $i) ? "class='after'" : null,get_pagenum_link($i),$i);

	// displays total page link and ellipses when max page is lower than total page
	if ($max_page<$total_pages) {
		if ($isFirstLastGap) $pagingString.= sprintf("<li class='space'>%s</li>",$lastGap);
		if ($isFirstLastNumbers) $pagingString.= sprintf("<li class='first_last_page'><a href='%s'>%u</a>",get_pagenum_link($total_pages),$total_pages);
		}

	// displays link to next page
	if($current_page!=$total_pages)
		$pagingString.=sprintf("<li><a href='%s'>%s</a></li>",get_pagenum_link($current_page+1),$nextPage);

  	$pagingString.= "</ul>\n\n";


	printf($pagingString);
}

} // class end
$pagenavi_automic_pagenumbers = new pagenavi_automic_pagenumbers();
function your_function($query) {
  global $wp_the_query;
  if ($query === $wp_the_query) {
    go_page_navi();
  }
}
add_action('loop_end', 'your_function');

function styling_the_shit()
{
    // Register the style like this for a plugin:
    wp_register_style( 'custom-style-navi', plugins_url( '/pagenavi-automic-pagenumbers.css', __FILE__ ), array(), '20120208', 'all' );
    wp_enqueue_style( 'custom-style-navi' );

}
add_action( 'wp_enqueue_scripts', 'styling_the_shit' ); //maybe choose another function name then 'styling the shit' later on


if ( ! function_exists( 'go_page_navi' ) ) {
	function go_page_navi( $args = '' ) {
		global $pagenavi_automic_pagenumbers;
		return $pagenavi_automic_pagenumbers->go_page_navi( $args );
	}
}
?>
<?php

function pagenavi_auto_activate() {
 add_option("pagenavi_auto_page", 'Page', '', 'yes');
 add_option("pagenavi_auto_of", 'of', '', 'yes');
}
add_action( 'init', 'pagenavi_auto_activate' );



function pagenavi_auto_admin_menu() {
  add_options_page('PageNavi Automatic', 'PageNavi Automatic', 'administrator', 'page_navi_automatic_pagenumbers', 'pagenavi_auto_page');
}
add_action('admin_menu', 'pagenavi_auto_admin_menu');



function pagenavi_auto_page() {
?>
<div>
<h2>PageNavi Automatic Page Numbers - Settings</h2>

You can set translation and options in the settings below:<BR>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table width="850">
<tr valign="top">
<th width="250" scope="row">Translation of 'Page 1 of 38' part</th>
<td width="600">
<input name="pagenavi_auto_page" type="text" id="pagenavi_auto_page" value="<?php echo get_option('pagenavi_auto_page'); ?>" /> 1 <input name="pagenavi_auto_of" type="text" id="pagenavi_auto_of" value="<?php echo get_option('pagenavi_auto_of'); ?>" /> 38</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="pagenavi_auto_page, pagenavi_auto_of" />

<p>
<input type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<BR><BR>
<?php
}
?>