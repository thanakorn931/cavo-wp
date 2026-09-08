/**
 * The Space, hero carousel.
 *
 * One slide is current; the title and the two links above it, and the words in
 * the band below it, belong to that slide and change with it. The track is
 * moved so the current slide sits in the middle of the band, which is what puts
 * its neighbours half off either edge.
 *
 * Where there is more than one, the whole run is copied either side of itself,
 * so an edge always has a slide standing at it and the last leads back to the
 * first. Two slides means each is the other's neighbour on both sides.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var track = root.querySelector( '.custom-space-hero__track' );
		var stage = root.querySelector( '.custom-space-hero__stage' );
		var title = root.querySelector( '.custom-space-hero__title' );
		var words = root.querySelector( '.custom-space-hero__words' );
		var tour  = root.querySelector( '.custom-space-hero__tour-link' );
		var host  = root.querySelector( '.custom-space-hero__button' );

		var written = root.querySelectorAll( '.custom-space-hero__slide' );
		var many    = written.length;

		if ( ! track || ! stage || many === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		// One slide has no neighbours and nowhere to go. The stylesheet centres
		// it; all it wants from here is to be told it is the one being read.
		if ( many === 1 ) {
			written[ 0 ].classList.add( 'is-current' );

			return;
		}

		var i;

		// A copy of the run before it and another after it. A reader never
		// reaches an edge with nothing beside it.
		for ( i = many - 1; i >= 0; i-- ) {
			track.insertBefore( copy( written[ i ] ), track.firstChild );
		}

		for ( i = 0; i < many; i++ ) {
			track.appendChild( copy( written[ i ] ) );
		}

		var slides  = track.querySelectorAll( '.custom-space-hero__slide' );
		var current = many;

		function copy( slide ) {
			var made = slide.cloneNode( true );

			// A copy is scenery: it says nothing a reader is meant to hear.
			made.setAttribute( 'aria-hidden', 'true' );
			made.dataset.copy = '1';

			return made;
		}

		/**
		 * One address and the two toggles that travel with it, the same three
		 * the widget prints for the slide it starts on.
		 */
		function follow( link, slide, name ) {
			if ( ! link ) {
				return;
			}

			var address  = slide.getAttribute( 'data-' + name );
			var blank    = slide.getAttribute( 'data-' + name + '-blank' ) === 'yes';
			var nofollow = slide.getAttribute( 'data-' + name + '-nofollow' ) === 'yes';
			var rel      = [];

			if ( address ) {
				link.setAttribute( 'href', address );
			} else {
				link.removeAttribute( 'href' );
			}

			if ( blank ) {
				link.setAttribute( 'target', '_blank' );
				rel.push( 'noopener' );
				rel.push( 'noreferrer' );
			} else {
				link.removeAttribute( 'target' );
			}

			if ( nofollow ) {
				rel.push( 'nofollow' );
			}

			if ( rel.length ) {
				link.setAttribute( 'rel', rel.join( ' ' ) );
			} else {
				link.removeAttribute( 'rel' );
			}
		}

		// What a slide is worth is read once, before anything is moving. Read
		// again mid-move it would answer with the width it is passing through,
		// and the run would settle half a slide's growth off centre.
		var gap    = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
		var wide   = slides[ current ].offsetWidth;
		var narrow = slides[ current === 0 ? 1 : 0 ].offsetWidth;
		var spread = wide + ( ( slides.length - 1 ) * ( narrow + gap ) );

		function place( moving ) {
			var room  = stage.offsetWidth;
			var left  = ( ( room - spread ) / 2 ) + ( current * ( narrow + gap ) );
			var shift = left + ( wide / 2 ) - ( room / 2 );

			track.style.transition = moving ? '' : 'none';
			track.style.transform  = 'translateX(' + ( -shift ) + 'px)';

			if ( ! moving ) {
				// Read something back so the browser settles the jump before the
				// transition is handed back to it.
				void track.offsetWidth;
				track.style.transition = '';
			}
		}

		function tell() {
			var slide = slides[ current ];
			var said  = slide.getAttribute( 'data-title' );

			if ( title && said ) {
				title.textContent = said;
			}

			if ( words ) {
				words.textContent = slide.getAttribute( 'data-words' ) || '';
			}

			follow( tour, slide, 'link' );
			follow( host, slide, 'host' );
		}

		function mark() {
			for ( var j = 0; j < slides.length; j++ ) {
				slides[ j ].classList.toggle( 'is-current', j === current );
			}
		}

		// A step that would leave the middle run is taken from the copy of where
		// it already is instead, so the reader is always a step away from the
		// edge and never reaches it. The swap is made before the move, not after
		// it, because a move that is never drawn never reports itself finished.
		// A press landing while the last one is still travelling would replace the
		// move in flight, and the easing would start again from wherever the
		// track had reached — which is the jerk. The press is let go instead.
		var moving = false;

		function settle() {
			moving = false;
		}

		track.addEventListener( 'transitionend', function ( event ) {
			if ( event.target === track && event.propertyName === 'transform' ) {
				settle();
			}
		} );

		function step( way ) {
			if ( moving ) {
				return;
			}

			var target = current + way;

			if ( target < many || target >= many * 2 ) {
				current += target < many ? many : -many;

				mark();
				place( false );

				target = current + way;
			}

			current = target;

			mark();
			place( true );
			tell();

			moving = true;

			// A move that is never drawn never reports itself finished, so the
			// press is handed back after the longest a move can take.
			window.clearTimeout( settle.timer );
			settle.timer = window.setTimeout( settle, 800 );
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-space-hero__arrow--prev' ) ) {
				step( -1 );
			} else if ( event.target.closest( '.custom-space-hero__arrow--next' ) ) {
				step( 1 );
			}
		} );

		window.addEventListener( 'resize', function () {
			settle();
			place( false );
		} );

		mark();
		place( false );
		tell();
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-space-hero' );

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
				'frontend/element_ready/the-space-hero-carousel.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-space-hero' ) );
				}
			);
		}
	} );
}() );
