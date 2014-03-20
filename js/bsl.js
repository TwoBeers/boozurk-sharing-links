var boozurk_social_plugin;

(function($) {

	boozurk_social_plugin = {

		init : function() {

			switch( boozurk_social_plugin_script_data.type ) {

				case 'plusone':
					gapi.plusone.go("posts_content");
					$('body').on('post-load', function(event){
						gapi.plusone.go("posts_content");
					});
					break;

				case 'addthis':
					addthis.button('.addthis_button_compact');
					$('body').on('post-load', function(event){
						addthis.button('.addthis_button_compact');
					});
					break;

				default :
					//no default action
					break;

			}

		}

	};

$(document).ready(function($){ boozurk_social_plugin.init(); });

})(jQuery);