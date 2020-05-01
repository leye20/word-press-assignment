/**
 * plugin-advanced.js
 *
 * @preserve Copyright 2020 Antoine Hurkmans
 * This is part of the Football Pool WordPress plugin.
 * See https://wordpress.org/plugins/football-pool/ for details and license.
 */

tinymce.PluginManager.add( 'footballpool', function( editor, url ) {
	// Add a button that opens a window
	editor.addButton( 'footballpool', {
		// text: FootballPoolTinyMCE.text,
		icon: false,
		image: url + '/footballpool-tinymce-16.png',
		tooltip: FootballPoolTinyMCE.tooltip,
		onclick: function() {
			// Open window
			editor.windowManager.open( {
				title: FootballPoolTinyMCE.text,
				url: url + '/tinymce-dialog.php',
				width: get_dimension( jQuery( window ).width(), 600 ),
				height: get_dimension( jQuery( window ).height(), 350 ),
				buttons: [ {
					text: FootballPoolTinyMCE.button_text,
					classes: 'primary',
					onclick: function( e ) {
						// A callback to the get_shortcode() function is set as a property of activeEditor in
						// the dialog window so we can use it here.
						var content = tinymce.activeEditor.get_shortcode();
						tinymce.activeEditor.execCommand( 'mceInsertContent', false, content );
						
						// Close the dialog
						top.tinymce.activeEditor.windowManager.close();
					}
				}, {
					text: 'Cancel',
					onclick: 'close'
				} ]
			} );
		}
	} );
	
	function get_dimension( dim, max ) {
		var d = dim * .8;
		return ( d > max ) ? max : d;
	}
	
	return {
		// Return the getMetadata object for the help plugin. Not really needed, but hey, it was in the example plugin :P
		getMetadata: function () {
			return	{
				title: "Football Pool shortcodes",
				url: "https://wordpress.org/plugins/football-pool/"
			};
		}
	};
} );
