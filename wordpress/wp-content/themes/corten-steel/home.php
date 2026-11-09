<?php
/**
 * Blog home template — shows the main /blog/ archive when page_for_posts
 * is set and permalink_structure has a /blog/ prefix.
 *
 * WordPress template hierarchy: is_home() uses home.php first, then
 * index.php. archive.php is only for is_archive() (categories, tags,
 * authors, dates), not for the blog home page.
 *
 * @package Corten_Steel
 */

// Reuse the archive template markup — it already handles the blog
// home layout (hero, category chips, card grid, pagination) and also
// works for is_home() since all the template tags it calls accept
// both contexts.
require get_template_directory() . '/archive.php';
