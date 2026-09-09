/**
 * Blog, detail.
 *
 * The last mark hands the reader the article's own address.
 */
( function () {
	'use strict';

	function copy( button ) {
		var address = button.getAttribute( 'data-copy' ) || '';

		if ( ! address ) {
			return;
		}

		function done() {
			button.classList.add( 'is-copied' );

			window.setTimeout( function () {
				button.classList.remove( 'is-copied' );
			}, 1200 );
		}

		if ( window.navigator.clipboard && window.navigator.clipboard.writeText ) {
			window.navigator.clipboard.writeText( address ).then( done, function () {} );

			return;
		}

		// An older page without a clipboard of its own: the address is put in
		// a box for a moment and taken from there.
		var box = document.createElement( 'textarea' );

		box.value = address;
		box.setAttribute( 'readonly', '' );
		box.style.position = 'absolute';
		box.style.left = '-9999px';
		document.body.appendChild( box );
		box.select();

		try {
			document.execCommand( 'copy' );
			done();
		} catch ( error ) {
			// Nothing to do: the reader can still take the address from the bar.
		}

		document.body.removeChild( box );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest ? event.target.closest( '.custom-blog-detail__mark--copy' ) : null;

		if ( button ) {
			event.preventDefault();
			copy( button );
		}
	} );
}() );
