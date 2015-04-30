<?php

if ( !function_exists( 'allclients_api_get_landing_page_folders' ) ) {
	/**
	 * Get landing page folders
	 *
	 *
	 * @return array
	 */
	function allclients_api_get_landing_page_folders() {
		return get_allclients()
			->get_api()
			->method( 'GetWebFormFolders' );
	}
}

if ( !function_exists( 'allclients_api_get_landing_pages' ) ) {
	/**
	 * Get landing pages
	 *
	 * @param string $folder
	 *
	 * @return array
	 */
	function allclients_api_get_landing_pages( $folder = '' ) {
		return get_allclients()
			->get_api()
			->method( 'GetWebForms', array(
				'folder' => $folder
			) );
	}
}

if ( !function_exists( 'allclients_api_get_landing_page' ) ) {
	/**
	 * Get landing page by ID
	 *
	 * @param int $id
	 *
	 * @return array|false
	 */
	function allclients_api_get_landing_page( $id ) {
		return get_allclients()
			->get_api()
			->method( 'GetWebFormDetails', array(
				'webformid' => $id
			) );
	}
}
if ( !function_exists( 'allclients_api_get_landing_page_html' ) ) {
	/**
	 * Get landing page HTML by ID
	 *
	 * @param int $id
	 *
	 * @return string|false
	 */
	function allclients_api_get_landing_page_html( $id ) {
		return get_allclients()
			->get_api()
			->method( 'GetWebFormHTML', array(
				'webformid' => $id
			) );
	}
}

if ( !function_exists( 'allclients_ajax_get_landing_pages' ) ) {
	/**
	 * Get landing pages over XHR
	 */
	function allclients_ajax_get_landing_pages() {
		try {
			$folder = isset( $_POST['folder'] ) ? $_POST['folder'] : '';
			if ( $folder === '\\\\' ) {
				$folder = '\\';
			}
			wp_send_json_success( allclients_api_get_landing_pages( $folder ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
}

if ( !function_exists( 'allclients_ajax_get_landing_page' ) ) {
	/**
	 * Get landing page over XHR
	 */
	function allclients_ajax_get_landing_page() {
		try {
			$page_id = isset( $_POST['page'] ) ? $_POST['page'] : '';
			$page    = allclients_api_get_landing_page( $page_id );
			$content = allclients_api_get_landing_page_html( $page_id );

			$page['title'] = '';
			if ( preg_match( '/<title>([^>]*)<\/title>/si', $content, $matches ) && isset( $matches[1] ) ) {
				$page['title'] = trim( $matches[1] );
			}
			wp_send_json_success( $page );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
}