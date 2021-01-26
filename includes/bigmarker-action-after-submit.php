<?php
/**
 * Class BigMarker_Action_After_Submit
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a subsciber to
 * BigMarker channel and conference via API
 */

if(!class_exists( '\ElementorPro\Modules\Forms\Classes\Action_Base' )){
	 return;
}

class BigMarker_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'bigmarker';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'BigMarker', 'text-domain' );
	}

	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );

		//  Make sure that there is a BigMarker API key
		if ( empty( $settings['bigmarker_api_key'] ) ) {
			return;
		}

		// Get submitted Form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		// Make sure that the user entered an email
		// which is required by BigMarker's API to add a subscriber
		if ( empty( $fields[ 'email' ] ) ) {
			return;
		}
		if ( empty( $fields[ 'firstname' ] ) ) {
			return;
		}
		if ( empty( $fields[ 'lastname' ] ) ) {
			return;
		}

		$bigmarker_url = 'https://www.bigmarker.com/api/v1/';
		$current_user_id = get_current_user_id();

		// Subscribe the user to the channel
		// Based on the param list at https://docs.bigmarker.com/#add-a-subscriber
		if ( !empty( $settings['bigmarker_channel'] ) ) {
			$bigmarker_channel_data = [
				'email' => $fields[ 'email' ],
				'first_name' => $fields[ 'firstname' ],
				'last_name' => $fields[ 'lastname' ]
			];
			$channel_endpoint = $bigmarker_url . 'channels/' . $settings['bigmarker_channel'] . '/add_subscriber';
			$response = wp_remote_post( $channel_endpoint, array(
				'method' => 'PUT',
				'headers' => array('Content-Type' => 'application/json', 'accept' => 'application/json', 'API-KEY' => $settings['bigmarker_api_key']),
				'body' => json_encode($bigmarker_channel_data),
			));

			if ( is_wp_error( $response ) ) {
			  return $response->get_error_message();
			} else {
				$bigmarker_subscriber_data = json_decode($response['body'],true);
				if ( !empty($bigmarker_subscriber_data['error']) ) {
					return $bigmarker_subscriber_data['error'];
				}
				if ( !empty($bigmarker_subscriber_data['bmid']) && is_user_logged_in() ) {
					update_user_meta( $current_user_id, 'bmid', $bigmarker_subscriber_data['bmid'] );
				}
			}
		}

		// Register the user to the webinar (conference)
		// Based on the param list at https://docs.bigmarker.com/#register-a-user-to-a-conference
		if ( !empty( $fields[ 'webinar' ] ) ) {
			$conference_endpoint = $bigmarker_url . 'conferences/register';
			$bigmarker_conference_data = [
				'id' => $fields[ 'webinar' ],
				'email' => $fields[ 'email' ],
				'first_name' => $fields[ 'firstname' ],
				'last_name' => $fields[ 'lastname' ]
			];
			if (!empty($fields[ 'utm_bmcr_source' ])) $bigmarker_conference_data['utm_bmcr_source'] = $fields[ 'utm_bmcr_source' ];
			if (is_user_logged_in()) $bigmarker_conference_data['custom_user_id'] = $current_user_id;

			$response = wp_remote_post( $conference_endpoint, array(
				'method' => 'PUT',
				'headers' => array('Content-Type' => 'application/json', 'accept' => 'application/json', 'API-KEY' => $settings['bigmarker_api_key']),
				'body' => json_encode($bigmarker_conference_data),
			));

			if ( is_wp_error( $response ) ) {
			  return $response->get_error_message();
			} else {
				$bigmarker_attendee_data = json_decode($response['body'],true);
				if ( !empty($bigmarker_attendee_data['error']) ) {
					return $bigmarker_attendee_data['error'];
				}
				if ( !empty($bigmarker_attendee_data['conference_url']) && is_user_logged_in() ) {
					update_user_meta( $current_user_id, 'bigmarker_conference_url', $bigmarker_attendee_data['conference_url'] );
				}
			}
		}
	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_bigmarker',
			[
				'label' => __( 'BigMarker', 'text-domain' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'bigmarker_api_key',
			[
				'label' => __( 'BigMarker API Key', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'XXXXXXXXXXXX',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter your API Key', 'text-domain' ),
			]
		);

		$widget->add_control(
			'bigmarker_channel',
			[
				'label' => __( 'BigMarker Channel Name', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'before',
				'description' => __( 'the channel name you want to subscribe a user to.', 'text-domain' ),
			]
		);

		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['bigmarker_api_key'],
			$element['bigmarker_channel']
		);
	}
}
