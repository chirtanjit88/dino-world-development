<?php
/**
 * The template for displaying the front page
 *
 * @package Dino_World
 */

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
