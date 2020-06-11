var H5P = H5P || {};

// Include utility functions
var H5PUserScoreStorage = window.H5PUserScoreStorage || {};

( function() {
	'use strict';

	// blogId passed by PHP
	// wpAJAXurl passed by PHP
	var keynamePrefix = 'h5p-user-score';
	var maxScores;

	/**
	 * Send an AJAX request to insert xAPI data.
	 * @param {string} action Action to call.
	 * @param {object} data Data to send.
	 * @param {function} [success] Callback.
	 */
	var sendAJAX = function( action, data, success ) {
		if ( ! action ) {
			return;
		}
		success = success || function() {};

		jQuery.ajax({
			url: wpAJAXurl,
			type: 'post',
			data: {
				action: action,
				data: JSON.stringify( data )
			},
			success: success
		});
	};

	/**
	 * Send an AJAX request to insert xAPI data.
	 * @param {Object} xapi - JSON object with xAPI data.
	 */
	var storeScore = function( xapi ) {
		var regexpId = new RegExp( '(\\?|&)id=([0-9]+)', 'm' );
		var contentId, scoreRaw, scoreMax, time;

		// Sanity check
		if ( ! xapi.object || ! xapi.object.id ||
				! xapi.result || ! xapi.result.score
		) {
			return;
		}

		// Extract content type id
		contentId = xapi.object.id.match( regexpId );
		contentId = ( 3 === contentId.length ) ? contentId[2] : null;
		if ( ! contentId ) {
			return;
		}

		H5PUserScoreStorage.storeLocalStorage(
			[ blogId, keynamePrefix, contentId ].join( '-' ),
			{
				contentId: contentId,
				scoreRaw: xapi.result.score.raw || 0,
				scoreMax: xapi.result.score.max || null,
				time: Math.floor( Date.now() / 1000 ) // keep smaller/equal to db
			}
		);
	};

	/**
	 * Handle storing of xAPI statements.
	 * @param {Object} event - Event.
	 */
	var handleXAPI = function( event ) {
		var verb;
		var regexp;

		if ( ! event.data || ! event.data.statement || ! event.data.statement.verb ||
				! event.data.statement.verb.display || ! event.data.statement.verb.display['en-US']) {
			return;
		}

		// Don't track subcontent
		if ( event.data.statement.object && event.data.statement.object.id &&
				( new RegExp( '\\?subContentId=' ).test( event.data.statement.object.id ) ) ) {
			return;
		}

		verb = event.data.statement.verb.display['en-US'];
		if ( -1 === [ 'answered', 'completed' ].indexOf( verb ) ) {
			return;
		}

		storeScore( event.data.statement );
		updateDocumentScores();
	};

	/**
	 * Update the scores in the document.
	 */
	var updateDocumentScores = function() {

		// Shortcodes 'score'
		var shortcodes = document.querySelectorAll( '.h5p-user-score-score' );
		shortcodes.forEach( function( divScore ) {
			var score;
			var contentId = divScore.getAttribute( 'data-h5p-content-id' );

			if ( contentId && -1 !== contentId ) {
				score = H5PUserScoreStorage.restoreLocalStorage([ blogId, keynamePrefix, contentId ].join( '-' ) );
				if ( score && undefined !== typeof score.scoreRaw ) {
					divScore.innerHTML = score.scoreRaw;
				} else if ( ! maxScores ) {

					// Max scores may not have been loaded yet
					sendAJAX( 'get_max_score', {contentId: contentId}, function( result ) {
						divScore.innerHTML = result ? '0' : divScore.innerHTML;
					});
				} else {

					// Use max score from database
					divScore.innerHTML = maxScores[contentId] ? '0' : divScore.innerHTML;
				}
			}
		});

		// Shortcodes 'maxScore'
		shortcodes = document.querySelectorAll( '.h5p-user-score-max-score' );
		shortcodes.forEach( function( divScore ) {
			var score;
			var contentId = divScore.getAttribute( 'data-h5p-content-id' );
			if ( contentId && -1 !== contentId ) {
				score = H5PUserScoreStorage.restoreLocalStorage([ blogId, keynamePrefix, contentId ].join( '-' ) );
				if ( score && undefined !== typeof score.scoreMax ) {
					divScore.innerHTML = score.scoreMax;
				} else if ( ! maxScores ) {

					// Max scores may not have been loaded yet
					sendAJAX( 'get_max_score', {contentId: contentId}, function( result ) {
						divScore.innerHTML = result ? result : divScore.innerHTML;
					});
				} else {

					// Use max score from database
					divScore.innerHTML = maxScores[contentId] ? maxScores[contentId] : divScore.innerHTML;
				}
			}
		});

		// Shortcodes 'percentage'
		shortcodes = document.querySelectorAll( '.h5p-user-score-percentage' );
		shortcodes.forEach( function( divScore ) {
			var score;
			var contentId = divScore.getAttribute( 'data-h5p-content-id' );
			if ( contentId && -1 !== contentId ) {
				score = H5PUserScoreStorage.restoreLocalStorage([ blogId, keynamePrefix, contentId ].join( '-' ) );
				if ( score && undefined !== typeof score.scoreRaw && undefined !== typeof score.scoreMax ) {
					divScore.innerHTML = Math.round( 100 * score.scoreRaw / score.scoreMax ) + ' %';
				} else if ( ! maxScores ) {

					// Max scores may not have been loaded yet
					sendAJAX( 'get_max_score', {contentId: contentId}, function( result ) {
						divScore.innerHTML = result ? '0 %' : divScore.innerHTML;
					});
				} else {

					// Use max score from database
					divScore.innerHTML = maxScores[contentId] ? '0 %' : divScore.innerHTML;
				}
			}
		});
	};

	/**
	 * Handle init of xAPI Event Dispatcher.
	 * @param {object} contentWindow Content window object containing H5P object.
	 */
	var handleH5PInstanceActive = function( contentWindow ) {

		// Start external dispatcher
		try {
			if ( contentWindow.H5P && contentWindow.H5P.externalDispatcher ) {
				contentWindow.H5P.externalDispatcher.on( 'xAPI', handleXAPI );
			}
		} catch ( error ) {
			console.log( error );
		}

		// Update maximum score for task
		contentWindow.H5P.instances.forEach( function( instance ) {
			maxScores[instance.contentId] = ( 'function' === typeof instance.getMaxScore ) ? instance.getMaxScore() : null;
			sendAJAX(
				'set_max_score',
				{
					contentId: instance.contentId,
					scoreMax: ( 'function' === typeof instance.getMaxScore ) ? instance.getMaxScore() : null
				},
				function() {

					// H5P content may have been updated since last call and max score may have changed
					updateDocumentScores();
				}
			);
		});
	};

	/**
	 * Add xAPI listeners to all H5P instances that can trigger xAPI.
	 */
	document.addEventListener( 'readystatechange', function() {
		var iframes = document.getElementsByTagName( 'iframe' );
		var i;
		var contentWindow;
		var h5pDiv;

		var shortcodes;

		if ( 'interactive' === document.readyState || 'loading' === document.readyState ) {

			// Retrieve max scores of all content types from database
			maxScores = {};
			sendAJAX( 'get_max_scores', {}, function( scores ) {
				scores = JSON.parse( scores );
				scores.forEach( function( score ) {
					maxScores[ score['id_content'] ] = score['score_max'] ? parseInt( score['score_max'], 10 ) : score['score_max'];
				});

				updateDocumentScores();
			});
		}

		// Add xAPI EventListener and update max score if H5P content is present
		if ( 'complete' === document.readyState ) {
			for ( i = 0; i < iframes.length; i++ ) {

				// Skip non H5P iframes and remote iframes
				if ( ! iframes[i].classList.contains( 'h5p-iframe' ) &&
					(
						0 !== iframes[i].src.indexOf( window.location.origin ) ||
						-1 === iframes[i].src.indexOf( 'action=h5p_embed' )
					)
				) {
					continue;
				}

				// Edge needs to wait for iframe to be loaded, others don't
				contentWindow = iframes[i].contentWindow;
				if ( contentWindow.H5P ) {
					handleH5PInstanceActive( contentWindow );
				} else {
					iframes[i].addEventListener( 'load', function() {
						handleH5PInstanceActive( this.contentWindow );
					});
				}
			}

			// DIVs are used instead of iframes
			h5pDiv = document.getElementsByClassName( 'h5p-content' );
			if ( 0 !== h5pDiv.length ) {
				handleH5PInstanceActive( window );
			}
		}
	});
}  () );
