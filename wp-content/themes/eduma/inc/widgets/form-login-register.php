<?php
add_action( 'thim_form_login_widget', 'thim_form_login_widget', 10, 1 );
function thim_form_login_widget( $captcha ) { ?>
	<form name="loginpopopform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>"
		  method="post" novalidate>
		<?php do_action( 'thim_before_login_form' ); ?>
		<p class="login-username">
			<input type="text" name="log" placeholder="<?php esc_html_e( 'Username or email', 'eduma' ); ?>"
				   class="input required" size="20"/>
		</p>
		<p class="login-password">
			<input type="password" name="pwd" placeholder="<?php esc_html_e( 'Password', 'eduma' ); ?>"
				   class="input required" value="" size="20"/>
		</p>
		<?php
		/**
		 * Fires following the 'Password' field in the login form.
		 *
		 * @since 2.1.0
		 */
		do_action( 'login_form' );
		?>
		<?php if ( $captcha == 'yes' ) : ?>
			<p class="thim-login-captcha">
				<?php
				$value_1 = rand( 1, 9 );
				$value_2 = rand( 1, 9 );
				?>
				<input type="text" data-captcha1="<?php echo esc_attr( $value_1 ); ?>"
					   data-captcha2="<?php echo esc_attr( $value_2 ); ?>"
					   placeholder="<?php echo esc_attr( $value_1 . ' &#43; ' . $value_2 . ' &#61;' ); ?>"
					   class="captcha-result required"/>
			</p>
		<?php endif; ?>
		<?php echo '<a class="lost-pass-link" href="' . thim_get_lost_password_url() . '" title="' . esc_attr__( 'Lost Password', 'eduma' ) . '">' . esc_html__( 'Lost your password?', 'eduma' ) . '</a>'; ?>

		<p class="forgetmenot login-remember">
			<label for="popupRememberme"><input name="rememberme" type="checkbox"
												value="forever"
												id="popupRememberme"/> <?php esc_html_e( 'Remember Me', 'eduma' ); ?>
			</label></p>
		<p class="submit login-submit">
			<input type="submit" name="wp-submit"
				   class="button button-primary button-large"
				   value="<?php esc_attr_e( 'Login', 'eduma' ); ?>"/>
			<input type="hidden" name="redirect_to"
				   value="<?php echo esc_url( thim_eduma_get_current_url() ); ?>"/>
			<input type="hidden" name="testcookie" value="1"/>
			<input type="hidden" name="nonce"
				   value="<?php echo wp_create_nonce( 'thim-loginpopopform' ) ?>"/>
			<input type="hidden" name="eduma_login_user">
		</p>

		<?php do_action( 'thim_after_login_form' ); ?>

	</form>
<?php }

add_action( 'thim_form_register_widget', 'thim_form_register_widget', 10, 3 );
function thim_form_register_widget( $captcha, $term, $redirect_to = 'account' ) { ?>
	<form class="<?php if ( get_theme_mod( 'thim_auto_login', true ) ) {
		echo 'auto_login';
	} ?>" name="registerformpopup"
		  action="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>"
		  method="post" novalidate="novalidate">

		<?php wp_nonce_field( 'ajax_register_nonce', 'register_security' ); ?>

		<p>
			<input placeholder="<?php esc_attr_e( 'Username', 'eduma' ); ?>"
				   type="text" name="user_login" class="input required"/>
		</p>

		<p>
			<input placeholder="<?php esc_attr_e( 'Email', 'eduma' ); ?>"
				   type="email" name="user_email" class="input required"/>
		</p>

		<?php if ( get_theme_mod( 'thim_auto_login', true ) ) { ?>
			<p>
				<input placeholder="<?php esc_attr_e( 'Password', 'eduma' ); ?>"
					   type="password" name="password" class="input required"/>
			</p>
			<p>
				<input
					placeholder="<?php esc_attr_e( 'Repeat Password', 'eduma' ); ?>"
					type="password" name="repeat_password"
					class="input required"/>
			</p>
		<?php } ?>

		<?php
		if ( is_multisite() && function_exists( 'gglcptch_login_display' ) ) {
			gglcptch_login_display();
		}

		do_action( 'register_form' );
		?>

		<?php if ( $captcha == 'yes' ) : ?>
			<p class="thim-login-captcha">
				<?php
				$value_1 = rand( 1, 9 );
				$value_2 = rand( 1, 9 );
				?>
				<input type="text"
					   data-captcha1="<?php echo esc_attr( $value_1 ); ?>"
					   data-captcha2="<?php echo esc_attr( $value_2 ); ?>"
					   placeholder="<?php echo esc_attr( $value_1 . ' &#43; ' . $value_2 . ' &#61;' ); ?>"
					   class="captcha-result required"/>
			</p>
		<?php endif; ?>

		<?php
		if ( $term ):
			$target = ( isset( $term['is_external'] ) && ! empty( $term['is_external'] ) ) ? '_blank' : '_self';
			$rel = ( isset( $term['nofollow'] ) && ! empty( $term['nofollow'] ) ) ? 'nofollow' : 'dofollow';
			?>
			<p>
				<input type="checkbox" class="required" name="term" id="termFormFieldPopup">
				<label
					for="termFormField"><?php printf( __( 'I accept the <a href="%s" target="%s" rel="%s">Terms of Service</a>', 'eduma' ), esc_url( $term['url'] ), $target, $rel ); ?></label>
			</p>
		<?php endif; ?>
		<?php
		if ( $redirect_to == 'current' ) {
			$register_redirect = esc_url( thim_eduma_get_current_url() );
		} else {
			$register_redirect = get_theme_mod( 'thim_register_redirect', false );
			if ( empty( $register_redirect ) ) {
				$register_redirect = add_query_arg( 'result', 'registered', thim_get_login_page_url() );
			}
		}
		?>
		<input type="hidden" name="redirect_to"
			   value="<?php echo ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $register_redirect; ?>"/>
		<input type="hidden" name="modify_user_notification" value="1">
		<input type="hidden" name="eduma_register_user">


		<?php do_action( 'signup_hidden_fields', 'create-another-site' ); ?>
		<p class="submit">
			<input type="submit" name="wp-submit" class="button button-primary button-large"
				   value="<?php echo esc_attr_x( 'Sign up', 'Login popup form', 'eduma' ); ?>"/>
		</p>
	</form>
<?php }
