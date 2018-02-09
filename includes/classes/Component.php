<?php namespace WP_Job_Manager_Mail_Applications;

/**
 * Base Component
 *
 * @package WP_Job_Manager_Mail_Applications
 */
class Component extends Singular {
	/**
	 * Plugin Main Component
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init() {
		// vars
		$this->plugin = Plugin::get_instance();
	}
}
