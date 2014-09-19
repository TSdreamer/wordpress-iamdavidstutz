<?php

/**
 * Custom menu walker for navigation.
 * 
 * @see http://codex.wordpress.org/Function_Reference/wp_nav_menu
 * @class IAMDAVIDSTUTZ_Walker
 * @author David Stutz
 */
class IAMDAVIDSTUTZ_Walker extends Walker_Nav_Menu {

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $post;
		
		$indent = ($depth) ? str_repeat("\t", $depth) : '';
		
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
        $classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args);
        
        $reading = get_category_by_slug('reading');
        if (FALSE !== array_search('menu-item-object-category', $classes)) {
            if ($reading->term_id == get_query_var('cat')
					|| in_category('reading', $post)) {
                $classes[] = 'active';
            }
        }
        else if (FALSE !== array_search('menu-item-home', $classes)) {
            if ($reading->term_id != get_query_var('cat')
					&& !in_category('reading', $post)) {
                $classes[] = 'active';
            }
        }
        
		$class_names = join(' ', $classes);
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

		$id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
		$id = $id ? ' id="' . esc_attr($id) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
		$atts['target'] = ! empty($item->target)     ? $item->target     : '';
		$atts['rel']    = ! empty($item->xfn)        ? $item->xfn        : '';
		$atts['href']   = ! empty($item->url)        ? $item->url        : '';

		$atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

		$attributes = '';
		foreach ($atts as $attr => $value) {
			if (!empty($value)) {
				$value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . strtoupper($item->title) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}