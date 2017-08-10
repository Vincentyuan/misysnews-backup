<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>


    <div ng-app="misysnews" ng-controller="NewsCtrl as news">

      <div id="bg" ng-style="{'background-image': 'url(' + news.currentBackgroundImageURL + ')' }"></div>

      <div ng-include src="'/wp-content/plugins/misysnews/app/views/header.html'"></div>

      <div ng-include src="'/wp-content/plugins/misysnews/app/views/content.html'"></div>

      <div ng-include src="'/wp-content/plugins/misysnews/app/views/bar.html'"></div>

  </div>


<?php get_footer(); ?>
