<?php

namespace Carbon_Fields\Helper;

use Carbon_Fields\Datastore\Datastore;
use Carbon_Fields\Exception\Incorrect_Syntax_Exception;

/**
 * Helper functions and main initialization class.
 */
class Helper {

	/**
	 * Get a value formatted for end-users
	 *
	 * @param int $object_id Object id to get value for (e.g. post_id, term_id etc.)
	 * @param string $container_type Container type to search in
	 * @param string $field_name Field name
	 * @return mixed
	 */
	public static function get_value( $object_id, $container_type, $field_name ) {
		$repository = \Carbon_Fields\Carbon_Fields::resolve( 'container_repository' );
		$field = $repository->get_field_in_containers( $field_name, $container_type );

		if ( ! $field ) {
			Incorrect_Syntax_Exception::raise( 'Could not find a field which satisfies the supplied pattern: ' . $field_name );
		}

		$clone = clone $field;
		if ( $object_id !== null ) {
			$clone->get_datastore()->set_id( $object_id );
		}

		$clone->load();
		return $clone->get_formatted_value();
	}

	/**
	 * Set value for a field
	 *
	 * @param int $object_id Object id to get value for (e.g. post_id, term_id etc.)
	 * @param string $container_type Container type to search in
	 * @param string $field_name Field name
	 * @param array $value Field expects a `value_set`; Complex_Field expects a `value_tree` - refer to DEVELOPMENT.md
	 */
	public static function set_value( $object_id, $container_type, $field_name, $value ) {
		$repository = \Carbon_Fields\Carbon_Fields::resolve( 'container_repository' );
		$field = $repository->get_field_in_containers( $field_name, $container_type );

		if ( ! $field ) {
			Incorrect_Syntax_Exception::raise( 'Could not find a field which satisfies the supplied pattern: ' . $field_name );
		}
		
		$clone = clone $field;
		if ( $object_id !== null ) {
			$clone->get_datastore()->set_id( $object_id );
		}

		if ( is_a( $clone, 'Carbon_Fields\\Field\\Complex_Field' ) ) {
			$value_tree = ( ! empty( $value ) ) ? $value : array( 'value_set' => array(), 'groups' => array() );
			$clone->set_value( $value_tree['value_set'] );
			$clone->set_value_tree( $value_tree );
		} else {
			$clone->set_value( $value );
		}
		$clone->save();
	}

	/**
	 * Shorthand for get_post_meta().
	 * Uses the ID of the current post in the loop.
	 *
	 * @param  string $name Custom field name.
	 * @return mixed        Meta value.
	 */
	public static function get_the_post_meta( $name ) {
		return static::get_post_meta( get_the_ID(), $name );
	}

	/**
	 * Get post meta field for a post.
	 *
	 * @param int    $id   Post ID.
	 * @param string $name Custom field name.
	 * @return mixed        Meta value.
	 */
	public static function get_post_meta( $id, $name ) {
		return static::get_value( $id, 'post_meta', $name );
	}

	/**
	 * Set post meta field for a post.
	 *
	 * @param int $id Post ID
	 * @param string $name Custom field name
	 * @param array $value
	 * @return bool Success
	 */
	public static function set_post_meta( $id, $name, $value ) {
		return static::set_value( $id, 'post_meta', $name, $value );
	}

	/**
	 * Get theme option field value.
	 *
	 * @param string $name Custom field name
	 * @return mixed Option value
	 */
	public static function get_theme_option( $name ) {
		return static::get_value( null, 'theme_options', $name );
	}

	/**
	 * Set theme option field value.
	 *
	 * @param string $name Field name
	 * @param array $value
	 * @return bool Success
	 */
	public static function set_theme_option( $name, $value ) {
		return static::set_value( null, 'theme_options', $name, $value );
	}

	/**
	 * Get term meta field for a term.
	 *
	 * @param  int    $id   Term ID.
	 * @param  string $name Custom field name.
	 * @return mixed        Meta value.
	 */
	public static function get_term_meta( $id, $name ) {
		return static::get_value( $id, 'term_meta', $name );
	}

	/**
	 * Set term meta field for a term.
	 *
	 * @param int $id Term ID
	 * @param string $name Field name
	 * @param array $value
	 * @return bool Success
	 */
	public static function set_term_meta( $id, $name, $value ) {
		return static::set_value( $id, 'term_meta', $name, $value );
	}

	/**
	 * Get user meta field for a user.
	 *
	 * @param  int    $id   User ID.
	 * @param  string $name Custom field name.
	 * @return mixed        Meta value.
	 */
	public static function get_user_meta( $id, $name ) {
		return static::get_value( $id, 'user_meta', $name );
	}

	/**
	 * Set user meta field for a user.
	 *
	 * @param int $id User ID
	 * @param string $name Field name
	 * @param array $value
	 * @return bool Success
	 */
	public static function set_user_meta( $id, $name, $value ) {
		return static::set_value( $id, 'user_meta', $name, $value );
	}

	/**
	 * Get comment meta field for a comment.
	 *
	 * @param  int    $id   Comment ID.
	 * @param  string $name Custom field name.
	 * @return mixed        Meta value.
	 */
	public static function get_comment_meta( $id, $name ) {
		return static::get_value( $id, 'comment_meta', $name );
	}

	/**
	 * Set comment meta field for a comment.
	 *
	 * @param int $id Comment ID
	 * @param string $name Field name
	 * @param array $value
	 * @return bool Success
	 */
	public static function set_comment_meta( $id, $name, $value ) {
		return static::set_value( $id, 'comment_meta', $name, $value );
	}

	/**
	 * Recursive sorting function by array key.
	 * 
	 * @param  array  &$array     The input array.
	 * @param  int    $sort_flags Flags for controlling sorting behavior.
	 * @return array              Sorted array.
	 */
	public static function ksort_recursive( &$array, $sort_flags = SORT_REGULAR ) {
		if ( ! is_array( $array ) ) {
			return false;
		}
		ksort( $array, $sort_flags );
		foreach ( $array as $key => $value ) {
			self::ksort_recursive( $array[ $key ], $sort_flags );
		}
		return true;
	}

	/**
	 * Get the relation type from an array similar to how meta_query works in WP_Query
	 * 
	 * @param array $array
	 * @param array<string> $allowed_relations
	 * @param string $relation_key
	 * @return string
	 */
	public static function get_relation_type_from_array( $array, $allowed_relations = array( 'AND', 'OR' ), $relation_key = 'relation' ) {
		$allowed_relations = array_values( $allowed_relations );
		$allowed_relations = array_map( 'strtoupper', $allowed_relations );
		$relation = isset( $allowed_relations[0] ) ? $allowed_relations[0] : '';

		if ( isset( $array[ $relation_key ] ) ) {
			$relation = strtoupper( $array[ $relation_key ] );
		}

		if ( ! in_array( $relation, $allowed_relations ) ) {
			Incorrect_Syntax_Exception::raise( 'Invalid relation type ' . $relation . '. ' .
			'The rule should be one of the following: "' . implode( '", "', $allowed_relations ) . '"' );
		}

		return $relation;
	}

	/**
	 * Normalize a type string representing an object type
	 * 
	 * @param  string $type
	 * @return string
	 */
	public static function normalize_type( $type ) {
		$normalized_type = str_replace( ' ', '_', $type );
		$normalized_type = preg_replace( '/[_\s]+/', '_', $normalized_type );
		$normalized_type = preg_replace( '/^_|_$/', '', $normalized_type );
		$normalized_type = strtolower( $normalized_type );
		return $normalized_type;
	}

	/**
	 * Convert a string representing an object type to a fully qualified class name
	 * 
	 * @param  string $type
	 * @param  string $namespace
	 * @param  string $class_suffix
	 * @return string
	 */
	public static function type_to_class( $type, $namespace = '', $class_suffix = '' ) {
		$classlike_type = static::normalize_type( $type );
		$classlike_type = str_replace( '_', ' ', $classlike_type );
		$classlike_type = ucwords( $classlike_type );
		$classlike_type = str_replace( ' ', '_', $classlike_type );

		$class = $classlike_type . $class_suffix;
		if ( $namespace ) {
			$class = $namespace . '\\' . $class;
		}

		return $class;
	}

	/**
	 * Convert a string representing an object type to a fully qualified class name
	 * 
	 * @param  string $class
	 * @param  string $class_suffix
	 * @return string
	 */
	public static function class_to_type( $class, $class_suffix = '' ) {
		$reflection = new \ReflectionClass( $class );
		$type = $reflection->getShortName();

		if ( $class_suffix ) {
			$type = preg_replace( '/(' . preg_quote( $class_suffix, '/' ) . ')$/i', '', $type );
		}

		$type = static::normalize_type( $type );

		return $type;
	}
	
	/**
	 * Get an array of sanitized html classes
	 * 
	 * @param  string|array<string> $classes
	 * @return array<string>
	 */
	public static function sanitize_classes( $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = array_values( array_filter( explode( ' ', $classes ) ) );
		}
		$classes = array_map( 'sanitize_html_class', $classes );
		return $classes;
	}
}
