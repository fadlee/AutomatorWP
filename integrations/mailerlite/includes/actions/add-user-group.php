<?php
/**
 * Add user group
 *
 * @package     AutomatorWP\Integrations\MailerLite\Actions\Add_User_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MailerLite_Add_User_List extends AutomatorWP_Integration_Action {

    public $integration = 'mailerlite';
    public $action = 'mailerlite_add_user_group';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to group', 'automatorwp' ),
            'select_option'     => __( 'Add <strong>user</strong> to <strong>group</strong>', 'automatorwp' ),
            /* translators: %1$s: Group. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{group}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{group}' ),
            'options'           => array(
                'group' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'group',
                    'option_default'    => __( 'group', 'automatorwp' ),
                    'name'              => __( 'Group:', 'automatorwp' ),
                    'option_none'       => false,
                    'action_cb'         => 'automatorwp_mailerlite_get_groups',
                    'options_cb'        => 'automatorwp_mailerlite_options_cb_group',
                    'placeholder'       => 'Select a group',
                    'default'           => ''
                ) ),
            ),
        ) );

    }


    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $group_id = $action_options['group'];        
        $user = get_user_by ( 'ID', $user_id );
        
        $this->result = '';

        // Bail if MailerLite not configured
        if( ! automatorwp_mailerlite_get_api() ) {
            $this->result = __( 'MailerLite integration not configured in AutomatorWP settings.', 'automatorwp' );
            return;
        }

        // Bail if group is empty
        if ( empty( $group_id ) ) {
            $this->result = __( 'Group field field is empty.', 'automatorwp' );
            return;
        }
        
        $subscriber_data = array(
            'email'         => $user->user_email,
        );

        $response = automatorwp_mailerlite_add_subscriber_group( $subscriber_data, $group_id );

        if ( $response == 201 || $response == 200 ) {

            $this->result = sprintf( __( '%s added to group.', 'automatorwp' ), $user->user_email );

        }

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Configuration notice
        add_filter( 'automatorwp_automation_ui_after_item_label', array( $this, 'configuration_notice' ), 10, 2 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();

    }

    /**
     * Configuration notice
     *
     * @since 1.0.0
     *
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The object type (trigger|action)
     */
    public function configuration_notice( $object, $item_type ) {

        // Bail if action type don't match this action
        if( $item_type !== 'action' ) {
            return;
        }

        if( $object->type !== $this->action ) {
            return;
        }

        // Warn user if the authorization has not been setup from settings
        if( ! automatorwp_mailerlite_get_api() ) : ?>
            <div class="automatorwp-notice-warning" style="margin-top: 10px; margin-bottom: 0;">
                <?php echo sprintf(
                    __( 'You need to configure the <a href="%s" target="_blank">MailerLite settings</a> to get this action to work.', 'automatorwp' ),
                    get_admin_url() . 'admin.php?page=automatorwp_settings&tab=opt-tab-mailerlite'
                ); ?>
                <?php echo sprintf(
                    __( '<a href="%s" target="_blank">Documentation</a>', 'automatorwp' ),
                    'https://automatorwp.com/docs/mailerlite/'
                ); ?>
            </div>
        <?php endif;

    }

    /**
     * Action custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        // Store the action's result
        $log_meta['result'] = $this->result;

        return $log_meta;
    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }


}

new AutomatorWP_MailerLite_Add_User_List();