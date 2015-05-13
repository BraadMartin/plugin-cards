/**
 * Plugin Cards JS.
 *
 * Used to ensure we show a single column view if the container element is narrow.
 */
( function( $ ) {

	// Debouncing function from John Hann.
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var debounce = function( func, threshold ) {

		// The timer.
		var timeout;

		return function debounced() {

			// Store the passed in function and args.
			var obj = this;
			var args = arguments;

			// This is the callback that the timer triggers when debouncing is complete.
			function delayed() {

				// We have successfully debounced, trigger the function.
				func.apply( obj, args );

				// And clear the timer.
				timeout = null;
			}

			// If the timer is active, clear it.
			if ( timeout ) {
				clearTimeout( timeout );
			}

			// Set the timer to 50ms and have it call delayed() when it completes.
			timeout = setTimeout( delayed, threshold || 60 );
		};
	};

	// Add/remove a class based on the width of the container.
	var pluginCardsCheckWidth = function() {

		$( '.plugin-cards' ).each( function() {
			var width = $( this ).width();
			if ( width < 689 ) { // 689 matches the official wp-admin breakpoint
				$( this ).addClass( 'single-column' );
			} else {
				$( this ).removeClass( 'single-column' );
			}
		});
	};

	// Trigger as the page first loads and again if the screen changes size.
	$( document ).ready( function() {

		pluginCardsCheckWidth();

		$( window ).on( 'resize orientationchange', debounce( pluginCardsCheckWidth ) );
	});

})( jQuery );

