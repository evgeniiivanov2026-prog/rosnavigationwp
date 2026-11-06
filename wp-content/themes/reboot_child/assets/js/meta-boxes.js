/**
 * Logo picker for the experience meta box.
 *
 * Uses the WordPress media frame. No jQuery calls of our own.
 */
( function () {
	'use strict';

	function field( id ) {
		return document.getElementById( id );
	}

	function preview( id ) {
		return document.getElementById( id + '-preview' );
	}

	function openFrame( button ) {
		var target = button.getAttribute( 'data-reboot-child-media' );
		var input = field( target );
		if ( ! input || ! window.wp || ! wp.media ) {
			return;
		}

		var frame = wp.media( {
			title: button.getAttribute( 'data-title' ) || '',
			library: { type: 'image' },
			multiple: false
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			var box = preview( target );
			var image = document.createElement( 'img' );

			input.value = String( attachment.id || '' );
			image.className = 'reboot-child-media-preview__image';
			image.alt = '';
			image.width = 120;
			image.src = attachment.url || '';
			if ( box ) {
				box.replaceChildren( image );
			}
		} );

		frame.open();
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-reboot-child-media]' );
		var clear = event.target.closest( '[data-reboot-child-media-clear]' );
		var target;
		var input;
		var box;

		if ( button ) {
			event.preventDefault();
			openFrame( button );
			return;
		}

		if ( ! clear ) {
			return;
		}

		event.preventDefault();
		target = clear.getAttribute( 'data-reboot-child-media-clear' );
		input = field( target );
		box = preview( target );
		if ( input ) {
			input.value = '';
		}
		if ( box ) {
			box.replaceChildren();
		}
	} );
}() );
