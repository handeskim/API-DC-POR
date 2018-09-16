<?php get_header(); ?>
<?php get_template_part('slogen_form'); ?>
<div id="content" style="width:100%;float:left; margin: 12px auto;">

	<div id="page_main_content" style="margin:2%;">
		<?php get_template_part('whois_form'); ?>
	
		<?php //get_template_part('layout_1'); ?>
	<!--<div id="page_main_content">-->
	</div>
	<div id="page_sidebar">
		<?php get_sidebar('facebook'); ?>
	</div>
<?php get_template_part('layout_hosting_form'); ?>
<?php get_template_part('why_in_dotvn'); ?>
</div><!--<div id="content">-->
<?php
get_template_part('partner_form');
get_footer();
?>

