<?php
/**
 * Background Emailer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Background_Process', false ) ) {
	include_once dirname( __FILE__ ) . '/abstracts/class-memberhero-background-process.php';
}

/**
 * MemberHero_Background_Emailer Class.
 */
class MemberHero_Background_Emailer extends MemberHero_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'memberhero_emailer';

		// Dispatch queue after shutdown.
		add_action( 'shutdown', array( $this, 'dispatch_queue' ), 100 );

		parent::__construct();
	}

	/**
	 * Schedule fallback event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 */
	protected function task( $callback ) {
		if ( isset( $callback['filter'], $callback['args'] ) ) {
			try {
				MemberHero_Emails::send_queued_transactional_email( $callback['filter'], $callback['args'] );
			} catch ( Exception $e ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					trigger_error( 'Transactional email triggered fatal error for callback ' . esc_html( $callback['filter'] ), E_USER_WARNING );
				}
			}
		}
		return false;
	}

	/**
	 * Finishes replying to the client, but keeps the process running for further (async) code execution.
	 */
	protected function close_http_connection() {
		// Only 1 PHP process can access a session object at a time, close this so the next request isn't kept waiting.
		if ( session_id() ) {
			session_write_close();
		}

		memberhero_set_time_limit( 0 );

		// fastcgi_finish_request is the cleanest way to send the response and keep the script running, but not every server has it.
		if ( is_callable( 'fastcgi_finish_request' ) ) {
			fastcgi_finish_request();
		} else {
			// Fallback: send headers and flush buffers.
			if ( ! headers_sent() ) {
				header( 'Connection: close' );
			}
			@ob_end_flush();
			flush();
		}
	}

	/**
	 * Save and run queue.
	 */
	public function dispatch_queue() {
		if ( ! empty( $this->data ) ) {
			$this->close_http_connection();
			$this->save()->dispatch();
		}
	}

	/**
	 * Get post args
	 */
	protected function get_post_args() {
		if ( property_exists( $this, 'post_args' ) ) {
			return $this->post_args;
		}

		// Pass cookies through with the request so nonces function.
		$cookies = array();

		foreach ( $_COOKIE as $name => $value ) {
			if ( 'PHPSESSID' === $name ) {
				continue;
			}
			$cookies[] = new WP_Http_Cookie( array(
				'name'  => $name,
				'value' => $value,
			) );
		}

		return array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'body'      => $this->data,
			'cookies'   => $cookies,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);
	}

	/**
	 * Handle
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 */
	protected function handle() {
		$this->lock_process();

		do {
			$batch = $this->get_batch();

			if ( empty( $batch->data ) ) {
				break;
			}

			foreach ( $batch->data as $key => $value ) {
				$task = $this->task( $value );

				if ( false !== $task ) {
					$batch->data[ $key ] = $task;
				} else {
					unset( $batch->data[ $key ] );
				}

				// Update batch before sending more to prevent duplicate email possibility.
				$this->update( $batch->key, $batch->data );

				if ( $this->time_exceeded() || $this->memory_exceeded() ) {
					// Batch limits reached.
					break;
				}
			}
			if ( empty( $batch->data ) ) {
				$this->delete( $batch->key );
			}
		} while ( ! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next batch or complete process.
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}
	}

}