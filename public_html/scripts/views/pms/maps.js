var map = (function($, L) {


	var sps = [];
	var lat = 0;
	var lng = 0;

	var the_map = null;

	var centre_icon = L.icon({
		iconUrl: "/scripts/leaflet/images/marker-icon-red.png",
		shadowUrl: "/scripts/leaflet/images/marker-shadow.png"
	});

	var marker_list = {};		// list to store markers in
	var markers = null;		// cluster group reference


	/**
	 * Initialise all the things
	 */
	var init = function() {

		// Respond to view changes
		$(document).on("view/switch", function(e, view) {
			if (view === "map") {
				show();
			} else {
				hide();
			}
		});

		// Click event on sidebar list
		$(".sp-list").on("click", ".sp-list-item-heading", function(e) {
			var sp_id = $(this).parent(".sp-list-item").attr("rel");
			show_info(sp_id);
		});

		$(".results").on("click", ".schedule-btn", function(e) {
			var sp_id = $(this).data("sp_id");
			var when = $(this).data("when");
			$(document).trigger("schedule/load", { sp_id: sp_id, when: when });
			//schedule.load(sp_id, when);
			e.preventDefault();
		});

		if (sps.length > 0) {

			// Initialise map on div#map!
			the_map = L.map('map');

			the_map.setView([lat, lng], 13);

			// Add marker for location
			L.marker([lat, lng], { icon: centre_icon }).addTo(the_map).bindPopup("Client location");

			// Add tile layer
			L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
				subdomains: '1234',
				attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>. Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a>.',
				maxZoom: 18
			}).addTo(the_map);


			// Configure marker clustering
			markers = new L.MarkerClusterGroup({
				maxClusterRadius: 40
			});

			$.each(sps, function(idx, sp) {
				var m = L.marker([sp.pc_lat, sp.pc_lng], { sp: sp })
					.on("click", handle_marker_click)
					.bindPopup("<strong>" + sp.name + "</strong>");
				markers.addLayer(m);
				marker_list[sp.id] = m;
			});

			the_map.addLayer(markers);

			$(document).trigger("view/switch", "map");
		}

	}


	/**
	 * Set the service providers
	 */
	var set_sps = function(_sps) {
		sps = _sps;
	}


	/**
	 * Store lat and lng locally - centre of map
	 */
	var set_centre = function(coords) {
		lat = coords[0];
		lng = coords[1];
	}


	/**
	 * Handler to respond to clustered markers clicking
	 */
	var handle_marker_click = function(e) {
		show_info(this.options.sp.id, true);
	}


	/**
	 * Handle the showing of information of a given service provider
	 */
	var show_info = function(sp_id, hide_all) {

		// All items in list
		var $all_items = $(".sp-list-item");

		// This one
		var $li = $(".sp-list-item[rel='" + sp_id + "']");

		// If this item is currently highlighted... "close" this one
		if ($li.hasClass("hilite")) {
			// Show all other items
			$all_items.show();
			// Remove highlight
			$li.removeClass("hilite");
			// Show map
			//show_map();
			//hide_schedule();
			//switch_view("map");
			$(document).trigger("view/switch", "map");
			return;
		}

		// Remove hilite class from all others and hide it
		$all_items.removeClass("hilite");

		if (hide_all) {
			$all_items.hide();
		}

		// Show just this one and hilite it
		$li.addClass("hilite").show();

		// Show on map
		var m = marker_list[sp_id];
		markers.zoomToShowLayer(m, function() {
			m.openPopup();
			the_map.panTo(m.getLatLng());
		});

	}


	var show = function() {
		$("#map").show();
		$(".key-type-map").show();
		the_map.invalidateSize(true);
	}


	var hide = function() {
		$("#map").hide();
		$(".key-type-map").hide();
	}


	return {
		init: init,
		set_sps: set_sps,
		set_centre: set_centre,
		show_info: show_info
	};


})(jQuery, L);
