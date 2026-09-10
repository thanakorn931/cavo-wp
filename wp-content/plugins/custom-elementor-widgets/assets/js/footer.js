/**
 * Footer, the sign-up.
 *
 * A pause on the button after an address goes through is manners rather than
 * defence: it says the press was heard, and it keeps a second press from
 * sending the same thing twice.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var form = root.querySelector( '.custom-footer__form' );
		var send = root.querySelector( '.custom-footer__submit' );

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
		var roots = document.querySelectorAll( '.custom-footer' );

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
				'frontend/element_ready/footer.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-footer' ) );
				}
			);
		}
	} );
}() );
