/**
 * Contact, get in touch.
 *
 * A pause on the button after a message goes through is manners rather than
 * defence: it says the press was heard, and it keeps a second press from
 * sending the same thing twice.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var form = root.querySelector( '.custom-contact-form__form' );
		var send = root.querySelector( '.custom-contact-form__send' );

		if ( ! form || ! send ) {
			return;
		}

		root.dataset.wired = '1';

		form.addEventListener( 'submit', function () {
			// The browser has already refused a form it does not like, so by
			// here the press is going somewhere.
			window.setTimeout( function () {
				send.disabled = true;
			}, 0 );
		} );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-contact-form' );

		for ( var i = 0; i < roots.length; i++ ) {
			setUp( roots[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}

	// Elementor rebuilds a widget in the editor without reloading the page.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/contact-form.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-contact-form' ) );
				}
			);
		}
	} );
}() );
