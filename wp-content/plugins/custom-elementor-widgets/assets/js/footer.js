/**
 * The sign-up form is a mock.
 *
 * The markup and the states are the design's; where the address goes has not
 * been settled yet, so the submit is held here rather than posting somewhere
 * that does not exist. Replace this file's listener with the real handler.
 */
( function () {
	'use strict';

	document.addEventListener( 'submit', function ( event ) {
		var form = event.target.closest( '.custom-footer__form' );

		if ( ! form ) {
			return;
		}

		event.preventDefault();
	} );
}() );
