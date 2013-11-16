<?php
/**
 * Registers IssueM's Leaky Paywall - MailPoet class
 *
 * @package IssueM's Leaky Paywall - MailPoet
 * @since 1.0.0
 */

/**
 * This class registers the main issuem functionality
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'IssueM_Leaky_Paywall_MailPoet' ) ) {
	
	class IssueM_Leaky_Paywall_MailPoet {
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @since 1.0.0
		 */
		function __construct() {
					
			$settings = $this->get_settings();
			
			add_action( 'issuem_leaky_paywall_settings_form', array( $this, 'settings_div' ) );
			add_action( 'issuem_leaky_paywall_update_settings', array( $this, 'update_settings_div' ) );
			
			add_action( 'issuem_leaky_paywall_new_subscriber', array( $this, 'process_subscriber_list' ), 10, 4 );
			add_action( 'issuem_leaky_paywall_update_subscriber', array( $this, 'process_subscriber_list' ), 10, 4 );
			add_action( 'issuem_leaky_paywall_update_subscriber_column', array( $this, 'process_subscriber_update' ), 10, 3 );
			
		}
		
		function process_subscriber_list( $email, $query, $customer, $args ) {
			$settings = $this->get_settings();
		 		    
		    if ( !empty( $args['payment_status'] ) && 'active' === $args['payment_status'] )
		    	$list_id = $settings['subscriber_list'];
		    else
		    	$list_id = $settings['expired_list'];
		    	
		    $user_data = apply_filters( 'issuem_leaky_paywall_mailpoet_user_data', array( 'email' => $email ) );
		    $data_subscriber = array(
		      'user' => $user_data,
		      'user_list' => array( 'list_ids' => array( $list_id ) )
		    );
		    $helper_user = WYSIJA::get('user','helper');
		    $helper_user->addSubscriber($data_subscriber);
		}
		
		function process_subscriber_update( $email, $column, $value ) {
			$settings = $this->get_settings();
			
			if ( 'payment_status' === $column ) {
			    $user_data = apply_filters( 'issuem_leaky_paywall_mailpoet_user_data', array( 'email' => $email ) );
				
				$model_user = WYSIJA::get( 'user','model' );
				$user_get = $model_user->getOne( false, array( 'email' => trim( $email ) ) );

			    if ( 'active' === $value ) {
				    $data_subscriber = array(
				      'user' => $user_data,
				      'user_list' => array( 'list_ids' => array( $settings['subscriber_list'] ) )
				    );
				    $helper_user = WYSIJA::get('user','helper');
				    $helper_user->addSubscriber($data_subscriber);
				    $helper_user->removeFromLists( array( $settings['expired_list'] ), array( $user_get['user_id'] ) );
				    
			    } else {
				    $data_subscriber = array(
				      'user' => $user_data,
				      'user_list' => array( 'list_ids' => array( $settings['expired_list'] ) )
				    );
				    $helper_user = WYSIJA::get('user','helper');
				    $helper_user->addSubscriber($data_subscriber);	
				    $helper_user->removeFromLists( array( $settings['subscriber_list'] ), array( $user_get['user_id'] ) );
		    	}
				
			}
		}
		
		/**
		 * Get IssueM's Leaky Paywall - MailPoet options
		 *
		 * @since 1.0.0
		 */
		function get_settings() {
			
			$defaults = array( 
				'subscriber_list' => '',
				'expired_list' => '',
			);
		
			$defaults = apply_filters( 'issuem_leaky_paywall_mailpoet_default_settings', $defaults );
			
			$settings = get_option( 'issuem-leaky-paywall-mailpoet' );
												
			return wp_parse_args( $settings, $defaults );
			
		}
		
		/**
		 * Update IssueM's Leaky Paywall options
		 *
		 * @since 1.0.0
		 */
		function update_settings( $settings ) {
			
			update_option( 'issuem-leaky-paywall-mailpoet', $settings );
			
		}
		
		/**
		 * Create and Display IssueM settings page
		 *
		 * @since 1.0.0
		 */
		function settings_div() {
			
			// Get the user options
			$settings = $this->get_settings();
			
			// Display HTML form for the options below
			?>
            <div id="modules" class="postbox">
            
                <div class="handlediv" title="Click to toggle"><br /></div>
                
                <h3 class="hndle"><span><?php _e( 'Leaky Paywall - MailPoet', 'issuem-lp-mp' ); ?></span></h3>
                
                <div class="inside">
                
                <table id="issuem_leaky_paywall_ip_exceptions">
                
                    <tr>
                        <th><?php _e( 'Subscriber List', 'issuem-lp-mp' ); ?></th>
                        <td>
                        <?php
                        //this will return an array of results with the name and list_id of each mailing list
						$model_list = WYSIJA::get('list','model');
						$wysija_lists = $model_list->get(array('name','list_id'),array('is_enabled'=>1));
						if ( !empty( $wysija_lists ) ) {
							echo '<select name="issuem_leaky_paywall_mailpoet_subscriber_list">';
						    echo '<option value="-1" ' . selected( '-1', $settings['expired_list'], false ). '>'.__( 'Select List', 'issuem-lp-mp' ) ."</input>";
							foreach($wysija_lists as $list){
							    echo '<option value="'.$list['list_id'].'" ' . selected( $list['list_id'], $settings['subscriber_list'], false ). '>'.$list['name'] ."</input>";
							}
							echo '</select>';
						}
						?>
                        <p class="description"><?php _e( 'The list you want subscribed users to be added to', 'issuem-lp-mp' ); ?></p>
                        </td>
                    </tr>
                
                    <tr>
                        <th><?php _e( 'Expired User List', 'issuem-lp-mp' ); ?></th>
                        <td>
                        <?php
						if ( !empty( $wysija_lists ) ) {
							echo '<select name="issuem_leaky_paywall_mailpoet_expired_list">';
						    echo '<option value="-1" ' . selected( '-1', $settings['expired_list'], false ). '>'.__( 'Select List', 'issuem-lp-mp' ) ."</input>";
							foreach($wysija_lists as $list){
							    echo '<option value="'.$list['list_id'].'" ' . selected( $list['list_id'], $settings['expired_list'], false ). '>'.$list['name'] ."</input>";
							}
							echo '</select>';
						}
						?>
                        <p class="description"><?php _e( 'The list you want expired or cancelled users to be moved to', 'issuem-lp-mp' ); ?></p>
                        </td>
                    </tr>
                    
                </table>
                                                                  
                <p class="submit">
                    <input class="button-primary" type="submit" name="update_issuem_leaky_paywall_settings" value="<?php _e( 'Save Settings', 'issuem-lp-mp' ) ?>" />
                </p>

                </div>
                
            </div>
			<?php
			
		}
		
		function update_settings_div() {
		
			// Get the user options
			$settings = $this->get_settings();
				
			if ( !empty( $_REQUEST['issuem_leaky_paywall_mailpoet_subscriber_list'] ) )
				$settings['subscriber_list'] = $_REQUEST['issuem_leaky_paywall_mailpoet_subscriber_list'];
			else
				$settings['subscriber_list'] = '';
				
			if ( !empty( $_REQUEST['issuem_leaky_paywall_mailpoet_expired_list'] ) )
				$settings['expired_list'] = $_REQUEST['issuem_leaky_paywall_mailpoet_expired_list'];
			else
				$settings['expired_list'] = '';
			
			$this->update_settings( $settings );
			
		}
		
	}
	
}