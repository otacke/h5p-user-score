var H5PUserScoreStorage = window.H5PUserScoreStorage || {};

	/**
   * Retrive from localStorage.
   * @param {string} keyname Content id to retrieve content for.
   * @return {object|null} Previous score, null if not possible.
   */
  H5PUserScoreStorage.restoreLocalStorage = function( keyname ) {
		var previousScore;

    if ( ! window.localStorage || 'string' !== typeof keyname ) {
      return null;
    }

    previousScore = window.localStorage.getItem( keyname );
    if ( previousScore ) {
      try {
        previousScore = JSON.parse( previousScore );
      } catch ( error ) {
        console.warn( 'Could not parse localStorage content for previous score.' );
        previousScore = null;
      }
    }

    return previousScore;
  };

	/**
   * Save score to LocalStorage
   * @param {string} keyname Key name.
   * @param {object} score Score to store.
   */
  H5PUserScoreStorage.storeLocalStorage = function( keyname, state ) {
    if ( 'string' !== typeof( keyname ) || ! state ) {
      return;
    }

    if ( window.localStorage ) {
      window.localStorage.setItem( keyname, JSON.stringify( state ) );
    }
  };
