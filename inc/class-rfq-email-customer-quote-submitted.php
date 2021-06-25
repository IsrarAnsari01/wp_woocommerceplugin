<?php
/**
 * Functions used by plugins
 *
 * @since 2.5.0
 * @package woocomerce/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RFQ_Email_Customer_Quote_Submitted', false ) ) :
	/**
	 * RFQ_Email_Customer_Quote_Submitted
	 */
	class RFQ_Email_Customer_Quote_Submitted extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'customer_quote_submitted';
			$this->customer_email = true;
			$this->title          = __( 'Quote Submited / Quote details', 'b2b-ecommerce' );
			$this->description    = __( 'Customer quote emails can be sent to customers containing their quote information.', 'b2b-ecommerce' );
			$this->template_html  = 'emails/customer-quote-submitted.php';
			// $this->template_plain = 'emails/plain/customer-quote-submitted.php';.
			$this->placeholders = array(
				'{quote_date}'   => '',
				'{quote_number}' => '',
			);
			$this->email_type   = $this->get_option( 'email_type' );

			// Call parent constructor.
			parent::__construct();

			// add_action( 'cwcrfq_quote_sumitted', array( $this, 'trigger' ), 10, 2 );.
			// add_action( 'cwrfq_email_quote_details', array( $this, 'show_quote_details') );.
			// $this->recipient = get_post_meta($quote_id, 'email', true);.

			if ( isset( $_GET['post'] ) ) {
				$quote_id        = sanitize_text_field( wp_unslash( $_GET['post'] ) );
				$this->recipient = get_post_meta( $quote_id, 'email', true );
			}

		}

		/**
		 * Get email subject.
		 *
		 * @param bool $paid Whether the order has been paid or not.
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject( $paid = false ) {

			return __( 'Your latest {site_title} Quotation', 'b2b-ecommerce' );

		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Your quotes for quote #{quote_number}', 'b2b-ecommerce' );
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_subject() {

			$subject = $this->get_option( 'subject', $this->get_default_subject() );
			/*
			@name: cwrfq_email_subject_quote_submitted
			@desc: Modify email subect when quote is subnitted.
			@param: (string) $subject Email Subject.
			@param: (string) $quote Current Quote Object.
			@param: (string) $email Email Object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			return apply_filters( 'cwrfq_email_subject_quote_submitted', $this->format_string( $subject ), $this->object, $this );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_heading() {
			$heading = $this->get_option( 'heading', $this->get_default_heading() );
			/*
			@name: cwrfq_email_heading_quote_submitted
			@desc: Modify email heading when quote is subnitted.
			@param: (string) $heading Email heading.
			@param: (string) $quote Current Quote Object.
			@param: (string) $email Email Object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			return apply_filters( 'cwrfq_email_heading_quote_submitted', $this->format_string( $heading ), $this->object, $this );
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 1.1.5.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for using {site_address}!', 'b2b-ecommerce' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $quote_id The quote ID.
		 * @param B2BE_RFQ_Quote $quote Quote object.
		 */
		public function trigger( $quote_id, $quote = false ) {

			$this->setup_locale();

			if ( $quote_id && ! is_a( $quote, 'B2BE_RFQ_Quote' ) ) {
				$quote = wc_get_quote( $quote_id );
			}

			if ( is_a( $quote, 'B2BE_RFQ_Quote' ) ) {
				$this->object                         = $quote;
				$this->placeholders['{quote_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{quote_number}'] = $this->object->get_quote_number();
			}
			// $this->recipient = $quote->get_requester_email();
			$this->recipient = get_post_meta( $quote_id, 'email', true );

			if ( $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {

			return wc_get_template_html(
				$this->template_html,
				array(
					'quote'         => $this->object,
					'email_heading' => $this->get_heading(),

					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				),
				'b2b-ecommerce-for-woocommerce',
				CWRFQ_TEMPLATE_DIR
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				),
				'b2b-ecommerce-for-woocommerce',
				CWRFQ_TEMPLATE_DIR
			);
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'b2b-ecommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'      => array(
					'title'   => __( 'Enable/Disable', 'b2b-ecommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'b2b-ecommerce' ),
					'default' => 'yes',
				),
				'subject'      => array(
					'title'       => __( 'Subject', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'      => array(
					'title'       => __( 'Email heading', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'subject_paid' => array(
					'title'       => __( 'Subject (paid)', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject( true ),
					'default'     => '',
				),
				'heading_paid' => array(
					'title'       => __( 'Email heading (paid)', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading( true ),
					'default'     => '',
				),
				'email_type'   => array(
					'title'       => __( 'Email type', 'b2b-ecommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'b2b-ecommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
		// public function show_quote_details( $quote, $sent_to_admin, $plain_text, $email ) {.

		// }.
	}
endif;

return new RFQ_Email_Customer_Quote_Submitted();
