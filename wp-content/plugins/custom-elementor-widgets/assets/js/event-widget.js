/**
 * The event card's ways to a ticket, on a screen that is touched.
 *
 * With a pointer, the buttons come over the picture while the pointer is on it
 * (event-widget.css). A finger has no such moment, so a touch on the picture
 * brings them, a second touch on it puts them away, and a touch anywhere else
 * puts away whichever were out. A touch on one of the buttons, once they are
 * out, is the link's own.
 */
( function () {
	'use strict';

	function touched() {
		return !! ( window.matchMedia && window.matchMedia( '( hover: none )' ).matches );
	}

	document.addEventListener( 'click', function ( event ) {
		if ( ! touched() || ! event.target.closest ) {
			return;
		}

		var host = event.target.closest( '[data-card-actions]' );

		if ( host && event.target.closest( '.custom-event-card__action' ) && host.classList.contains( 'is-open' ) ) {
			return;
		}

		var open = document.querySelectorAll( '[data-card-actions].is-open' );

		for ( var i = 0; i < open.length; i++ ) {
			if ( open[ i ] !== host ) {
				open[ i ].classList.remove( 'is-open' );
			}
		}

		if ( host ) {
			event.preventDefault();
			host.classList.toggle( 'is-open' );
		}
	} );
}() );
