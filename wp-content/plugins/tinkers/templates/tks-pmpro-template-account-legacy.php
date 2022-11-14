<?php
/**
 * Copy the function below into your custom plugin / Code Snippets plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 * Replace [pmpro_account] with [my_pmpro_account]
 * This example replaces "Change Password" link to point to site's password-reset URL.
 * "元ソース：\wp-content\plugins\paid-memberships-pro\shortcodes\pmpro_account.php
 */

	global $wpdb, $pmpro_msg, $pmpro_msgt, $pmpro_levels, $current_user, $levels;

	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_account] [pmpro_account sections="membership,profile"/]

	extract(shortcode_atts(array(
		'section' => '',
		'sections' => 'membership,profile,invoices,links'
	), $atts));

	//did they use 'section' instead of 'sections'?
	if(!empty($section))
		$sections = $section;

	//Extract the user-defined sections for the shortcode
	$sections = array_map('trim',explode(",",$sections));
	//Tinkers↓↓↓↓↓↓
	if (in_array(get_current_user_id(),tks_const::ACCOUNT_PAGE_HIDE_INVOICE_SECTION_USER)){
		$key = array_search('invoices', $sections);
		if(!is_bool($key))
			unset( $sections[$key] );
		
	}
	//Tinkers↑↑↑↑↑↑
	ob_start();

	//if a member is logged in, show them some info here (1. past invoices. 2. billing information with button to update.)
	$order = new MemberOrder();
	$order->getLastMemberOrder();
	$mylevels = pmpro_getMembershipLevelsForUser();
	$pmpro_levels = pmpro_getAllLevels(false, true); // just to be sure - include only the ones that allow signups
	$invoices = $wpdb->get_results("SELECT *, UNIX_TIMESTAMP(CONVERT_TZ(timestamp, '+00:00', @@global.time_zone)) as timestamp FROM $wpdb->pmpro_membership_orders WHERE user_id = '$current_user->ID' AND status NOT IN('review', 'token', 'error') ORDER BY timestamp DESC LIMIT 6");
	$tks_before_payment = get_user_meta(get_current_user_id(),'tks_before_payment_day',true);
	$is_date_tks_before_payment = true;
	if(!preg_match('/^[1-9]{1}[0-9]{0,3}\/[0-9]{1,2}\/[0-9]{1,2}$/', $tks_before_payment)){
		$is_date_tks_before_payment = false;
	}	
	?>
	<div id="pmpro_account">
		<?php if(in_array('membership', $sections) || in_array('memberships', $sections)) { ?>
			<div id="pmpro_account-membership" class="<?php echo pmpro_get_element_class( 'pmpro_box', 'pmpro_account-membership' ); ?>">

				<h3><?php _e("My Memberships", 'paid-memberships-pro' );?></h3>
				<table class="<?php echo pmpro_get_element_class( 'pmpro_table' ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
					<thead>
						<tr>
							<th><?php _e("Level", 'paid-memberships-pro' );?></th>
							<th><?php _e("ご請求額(月額)", 'tinkers' ); ?></th>
                            <th><?php _e("ご契約日", 'tinkers' ); ?></th>
							<th><?php _e("お支払い方法", 'tinkers' ); ?></th>
							<?php if ($is_date_tks_before_payment){ ?>
								<th><?php _e("前回のお支払い", 'tinkers' ); ?></th>
							<?php } ?>	
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($mylevels as $level) {
						?>
						<tr>
							<?php //Tinkersカスタマイズ(プラン名)↓↓↓ ?>
							<td class="<?php echo pmpro_get_element_class( 'pmpro_account-membership-levelname' ); ?>">
								<?php echo $level->name?>
								<div class="<?php echo pmpro_get_element_class( 'pmpro_actionlinks' ); ?>">
									<?php do_action("pmpro_member_action_links_before"); ?>

									<?php do_action("pmpro_member_action_links_after"); ?>
								</div> <!-- end pmpro_actionlinks -->
							</td>
							<?php //Tinkersカスタマイズ(月額金額)↓↓↓ ?>
							<td class="<?php echo pmpro_get_element_class( 'pmpro_account-membership-levelfee' ); ?>">
								<p><?php echo pmpro_formatPrice(get_user_meta(get_current_user_id(),"tks_monthly_price",true));?></p>
							</td>
							<?php //Tinkersカスタマイズ(ご契約日)↓↓↓ ?>
							<td class="<?php echo pmpro_get_element_class( 'pmpro_account-membership-expiration' ); ?>">
								<p><?php echo ( ! empty( $level->startdate ) ? ucfirst( date_i18n( get_option('date_format'), $level->startdate  ) ) : '' );?></p>
							</td>
							<?php //Tinkersカスタマイズ(支払い方法)↓↓↓ ?>
							<td class="<?php echo pmpro_get_element_class( 'pmpro_account-membership-expiration' ); ?>">
								<?php
									$expiration_text = '<p>';
									$expiration_text .= '</p>';
									echo get_user_meta(get_current_user_id(),'tks_payment_gateway',true);
								?>
							</td>
							<?php //Tinkersカスタマイズ(前回お支払い))↓↓↓ ?>
							<?php if ($is_date_tks_before_payment){ ?>
							<td class="<?php echo pmpro_get_element_class( 'pmpro_account-membership-expiration' ); ?>">
							<p><?php echo ucfirst( date_i18n( get_option('date_format'), strtotime($tks_before_payment)  ));?></p>
							</td>
							<?php } ?>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php //Todo: If there are multiple levels defined that aren't all in the same group defined as upgrades/downgrades ?>
				
			</div> <!-- end pmpro_account-membership -->
		<?php } ?>

		<?php if(in_array('profile', $sections)) { ?>
			<div id="pmpro_account-profile" class="<?php echo pmpro_get_element_class( 'pmpro_box', 'pmpro_account-profile' ); ?>">
				<?php wp_get_current_user(); ?>
				<h3><?php _e("My Account", 'paid-memberships-pro' );?></h3>
				<?php if($current_user->user_firstname) { ?>
					<p><?php echo $current_user->user_firstname?> <?php echo $current_user->user_lastname?></p>
				<?php } ?>
				<ul>
					<?php do_action('pmpro_account_bullets_top');?>
					<li><strong><?php _e("Username", 'paid-memberships-pro' );?>:</strong> <?php echo $current_user->user_login?></li>
					<li><strong><?php _e("Email", 'paid-memberships-pro' );?>:</strong> <?php echo $current_user->user_email?></li>
					<?php do_action('pmpro_account_bullets_bottom');?>
				</ul>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_actionlinks' ); ?>">
					<?php
						// Get the edit profile and change password links if 'Member Profile Edit Page' is set.
						if ( ! empty( pmpro_getOption( 'member_profile_edit_page_id' ) ) ) {
							$edit_profile_url = pmpro_url( 'member_profile_edit' );
							$change_password_url = add_query_arg( 'view', 'change-password', pmpro_url( 'member_profile_edit' ) );
						} elseif ( ! pmpro_block_dashboard() ) {
							$edit_profile_url = admin_url( 'profile.php' );
							$change_password_url = admin_url( 'profile.php' );
						}

						// Build the links to return.
						$pmpro_profile_action_links = array();
						if ( ! empty( $edit_profile_url) ) {
							$pmpro_profile_action_links['edit-profile'] = sprintf( '<a id="pmpro_actionlink-profile" href="%s">%s</a>', esc_url( $edit_profile_url ), esc_html__( 'Edit Profile', 'paid-memberships-pro' ) );
						}

						if ( ! empty( $change_password_url ) ) {
							$pmpro_profile_action_links['change-password'] = sprintf( '<a id="pmpro_actionlink-change-password" href="%s">%s</a>', esc_url( $change_password_url ), esc_html__( 'Change Password', 'paid-memberships-pro' ) );
						}

						$pmpro_profile_action_links['logout'] = sprintf( '<a id="pmpro_actionlink-logout" href="%s">%s</a>', esc_url( wp_logout_url() ), esc_html__( 'Log Out', 'paid-memberships-pro' ) );

						$pmpro_profile_action_links = apply_filters( 'pmpro_account_profile_action_links', $pmpro_profile_action_links );

						$allowed_html = array(
							'a' => array (
								'class' => array(),
								'href' => array(),
								'id' => array(),
								'target' => array(),
								'title' => array(),
							),
						);
						echo wp_kses( implode( pmpro_actions_nav_separator(), $pmpro_profile_action_links ), $allowed_html );
					?>
				</div>
			</div> <!-- end pmpro_account-profile -->
		<?php } ?>

		<?php if(in_array('invoices', $sections) && !empty($invoices)) { ?>
		<div id="pmpro_account-invoices" class="<?php echo pmpro_get_element_class( 'pmpro_box', 'pmpro_account-invoices' ); ?>">
			<h3><?php _e("Past Invoices", 'paid-memberships-pro' );?></h3>
			<table class="<?php echo pmpro_get_element_class( 'pmpro_table' ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th><?php _e("Date", 'paid-memberships-pro' ); ?></th>
						<th><?php _e("Level", 'paid-memberships-pro' ); ?></th>
						<th><?php _e("Amount", 'paid-memberships-pro' ); ?></th>
						<th><?php _e("Status", 'paid-memberships-pro'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$count = 0;
					foreach($invoices as $invoice)
					{
						if($count++ > 4)
							break;

						//get an member order object
						$invoice_id = $invoice->id;
						$invoice = new MemberOrder;
						$invoice->getMemberOrderByID($invoice_id);
						$invoice->getMembershipLevel();

						if ( in_array( $invoice->status, array( '', 'success', 'cancelled' ) ) ) {
						    $display_status = __( 'Paid', 'paid-memberships-pro' );
						} elseif ( $invoice->status == 'pending' ) {
						    // Some Add Ons set status to pending.
						    $display_status = __( 'Pending', 'paid-memberships-pro' );
						} elseif ( $invoice->status == 'refunded' ) {
						    $display_status = __( 'Refunded', 'paid-memberships-pro' );
						}
						?>
						<tr id="pmpro_account-invoice-<?php echo $invoice->code; ?>">
							<td><a href="<?php echo pmpro_url("invoice", "?invoice=" . $invoice->code)?>"><?php echo date_i18n(get_option("date_format"), $invoice->getTimestamp())?></a></td>
							<td><?php if(!empty($invoice->membership_level)) echo $invoice->membership_level->name; else echo __("N/A", 'paid-memberships-pro' );?></td>
							<td><?php echo pmpro_escape_price( pmpro_formatPrice($invoice->total) ); ?></td>
							<td><?php echo $display_status; ?></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<?php if($count == 6) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_actionlinks' ); ?>"><a id="pmpro_actionlink-invoices" href="<?php echo pmpro_url("invoice"); ?>"><?php _e("View All Invoices", 'paid-memberships-pro' );?></a></div>
			<?php } ?>
		</div> <!-- end pmpro_account-invoices -->
		<?php } ?>

		<?php if(in_array('links', $sections) && (has_filter('pmpro_member_links_top') || has_filter('pmpro_member_links_bottom'))) { ?>
		<div id="pmpro_account-links" class="<?php echo pmpro_get_element_class( 'pmpro_box', 'pmpro_account-links' ); ?>">
			<h3><?php _e("Member Links", 'paid-memberships-pro' );?></h3>
			<ul>
				<?php
					do_action("pmpro_member_links_top");
				?>

				<?php
					do_action("pmpro_member_links_bottom");
				?>
			</ul>
		</div> <!-- end pmpro_account-links -->
		<?php } ?>
	</div> <!-- end pmpro_account -->
	<?php
