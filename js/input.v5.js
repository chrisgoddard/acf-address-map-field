/* **********************************************
     Begin address-map.js
********************************************** */

(function($){
	
	/*
	*  Location
	*
	*  static model for this field
	*
	*  @type	event
	*  @date	1/06/13
	*
	*/
	
	acf.fields.address_map = {
		
		$el : null,
		$input : null,
		
		o : {},
		
		ready : false,
		geocoder : false,
		map : false,
		maps : {},
		
		set : function( o ){
			
			// merge in new option
			$.extend( this, o );
			
			
			// find input
			this.$input = this.$el.find('.value');
			
			
			// get options
			this.o = acf.get_data( this.$el );
			
			
			// get map
			if( this.maps[ this.o.id ] )
			{
				this.map = this.maps[ this.o.id ];
			}
			
				
			// return this for chaining
			return this;
			
		},
		init : function(){
			
			// geocode
			if( !this.geocoder )
			{
				this.geocoder = new google.maps.Geocoder();
			}
			
			
			// google maps is loaded and ready
			this.ready = true;
			
			
			// render map
			this.render();
					
		},
		render : function(){
			
			// reference
			var _this	= this,
				_$el	= this.$el;
			
			
			// vars
			var args = {
        		zoom		: parseInt(this.o.zoom),
        		center		: new google.maps.LatLng(this.o.lat, this.o.lng),
        		mapTypeId	: google.maps.MapTypeId.ROADMAP
        	};
			
			// create map	        	
        	this.map = new google.maps.Map( this.$el.find('.canvas')[0], args);
	        
	        
	        // add search
			var autocomplete = new google.maps.places.Autocomplete( this.$el.find('.search')[0] );
			autocomplete.map = this.map;
			autocomplete.bindTo('bounds', this.map);
			
			
			// add dummy marker
	        this.map.marker = new google.maps.Marker({
		        draggable	: true,
		        raiseOnDrag	: true,
		        map			: this.map,
		    });
		    
		    
		    // add references
		    this.map.$el = this.$el;
		    
		    
		    // value exists?
		    var lat = this.$el.find('.latitude').val(),
		    	lng = this.$el.find('.longitude').val();
		    
		    if( lat && lng )
		    {
			    this.update( lat, lng ).center();
		    }
		    
		    
			// events
			google.maps.event.addListener(autocomplete, 'place_changed', function(e) {				
			
				// reference
				var $el = this.map.$el;
				var place = autocomplete.getPlace();
				console.log(place);				
				
				var addressObject = {
					data: {
						number: '',
						street: '',
						unit: '',
						city: '',
						state: '',
						zip: '',
						country: '',
					},
					format: function() {
						var output = {}
						output.address_line_1 = [this.data.number, this.data.street].join(' ')
						output.address_line_2 = this.data.unit || '';
						output.city = this.data.city || '';
						output.state = this.data.state || '';
						output.zip = this.data.zip || '';
						output.country = this.data.country || '';
						return output;
					}
				}
				var inputForm = {
					street_number: {
						mapTo: 'number',
						use: 'long_name'
					},
					subpremise: {
						mapTo: 'unit',
						use: 'long_name',
					},
					route: {
						mapTo: 'street',
						use: 'long_name'
					},
					locality: {
						mapTo: 'city',
						use: 'long_name'
					},
					country: {
						mapTo: 'country',
						use: 'long_name',
					},
					administrative_area_level_1: {
						mapTo: 'state',
						use: 'short_name',
					},
					postal_code: {
						mapTo: 'zip',
						use: 'long_name'
					}
				}
				for (var k in place.address_components) {
					if (typeof(inputForm[place.address_components[k].types[0]]) !== 'undefined') {
						addressObject.data[inputForm[place.address_components[k].types[0]].mapTo] = place.address_components[k][inputForm[place.address_components[k].types[0]].use];
					}
				}
				var addressFinal = addressObject.format();
				
				
				
				if($el.find('.address_one').val() !== ''){
					
					if(confirm('Changes to address information with be overwritten with data from Google Maps')){
						update_fields();
						
						$el.find('.search').blur();
						
					}
					
					
				} else {
					update_fields();
				}
				
				function update_fields() {
					$el.find('.business_name').val((function() {
						if (place.name !== addressFinal.address_line_1) {
							return place.name;
						} else {
							return '';
						}
					}));
					$el.find('.address_one').val(addressFinal.address_line_1);
					$el.find('.address_two').val(addressFinal.address_line_2);
					$el.find('.city').val(addressFinal.city);
					$el.find('.state').val(addressFinal.state);
					$el.find('.country').val(addressFinal.country);
					$el.find('.zip').val(addressFinal.zip);
					$el.find('.latitude').val(place.geometry.location.k);
					$el.find('.longitude').val(place.geometry.location.A);					
					$el.find('input.google-map').val(place.url);
					
					if(place.formatted_phone_number){
						$el.find('.phone').val(place.formatted_phone_number);
					}
					
					if(place.website){
						$el.find('.website').val(place.website);
					}
					
					// manually update address
					var address = $el.find('.search').val();
					
					$el.find('.input-address').val(address);
					$el.find('.title h4').text(address);
					// vars
					
					// validate
					if (place.geometry) {
						var lat = place.geometry.location.lat(),
							lng = place.geometry.location.lng();
						_this.set({
							$el: $el
						}).update(lat, lng).center();
					} else {
						// client hit enter, manulaly get the place
						_this.geocoder.geocode({
							'address': address
						}, function(results, status) {
							// validate
							if (status != google.maps.GeocoderStatus.OK) {
								console.log('Geocoder failed due to: ' + status);
								return;
							}
							if (!results[0]) {
								console.log('No results found');
								return;
							}
							// get place
							place = results[0];
							var lat = place.geometry.location.lat(),
								lng = place.geometry.location.lng();
							_this.set({
								$el: $el
							}).update(lat, lng).center();
						});
					}
				}
				
			});
		    
		    
		    google.maps.event.addListener( this.map.marker, 'dragend', function(){
		    	
		    	// reference
			    var $el = this.map.$el;
			    
			    
		    	// vars
				var position = this.map.marker.getPosition(),
					lat = position.lat(),
			    	lng = position.lng();
			    	
				_this.set({ $el : $el }).update( lat, lng ).sync();
			    
			});
			
			
			google.maps.event.addListener( this.map, 'click', function( e ) {
				
				// reference
			    var $el = this.$el;
			    
			    
				// vars
				var lat = e.latLng.lat(),
					lng = e.latLng.lng();
				
				
				_this.set({ $el : $el }).update( lat, lng ).sync();
			
			});

			
			
	        // add to maps
	        this.maps[ this.o.id ] = this.map;
	        
	        
		},
		
		update : function( lat, lng ){
			
			// vars
			var latlng = new google.maps.LatLng( lat, lng );
		    
		    
		    // update inputs
			this.$el.find('.input-lat').val( lat );
			this.$el.find('.input-lng').val( lng ).trigger('change');
			
			
		    // update marker
		    this.map.marker.setPosition( latlng );
		    
		    
			// show marker
			this.map.marker.setVisible( true );
		    
		    
	        // update class
	        this.$el.addClass('active');
	        
	        
	        // validation
			this.$el.closest('.acf-field').removeClass('error');
			
			
	        // return for chaining
	        return this;
		},
		
		center : function(){
			
			// vars
			var position = this.map.marker.getPosition(),
				lat = this.o.lat,
				lng = this.o.lng;
			
			
			// if marker exists, center on the marker
			if( position )
			{
				lat = position.lat();
				lng = position.lng();
			}
			
			
			var latlng = new google.maps.LatLng( lat, lng );
				
			
			// set center of map
	        this.map.setCenter( latlng );
		},
		
		sync : function(){
			
			// reference
			var $el	= this.$el;
				
			
			// vars
			var position = this.map.marker.getPosition(),
				latlng = new google.maps.LatLng( position.lat(), position.lng() );
			
			
			this.geocoder.geocode({ 'latLng' : latlng }, function( results, status ){
				
				// validate
				if( status != google.maps.GeocoderStatus.OK )
				{
					console.log('Geocoder failed due to: ' + status);
					return;
				}
				
				if( !results[0] )
				{
					console.log('No results found');
					return;
				}
				
				
				// get location
				var location = results[0];
				
				
				// update h4
				$el.find('.title h4').text( location.formatted_address );

				
				// update input
				$el.find('.input-address').val( location.formatted_address ).trigger('change');
				
			});
			
			
			// return for chaining
	        return this;
		},
		
		locate : function(){
			
			// reference
			var _this	= this,
				_$el	= this.$el;
			
			
			// Try HTML5 geolocation
			if( ! navigator.geolocation )
			{
				alert( acf.l10n.address_map.browser_support );
				return this;
			}
			
			
			// show loading text
			_$el.find('.title h4').text(acf.l10n.address_map.locating + '...');
			_$el.addClass('active');
			
		    navigator.geolocation.getCurrentPosition(function(position){
		    	
		    	// vars
				var lat = position.coords.latitude,
			    	lng = position.coords.longitude;
			    	
				_this.set({ $el : _$el }).update( lat, lng ).sync().center();
				
			});

				
		},
		
		clear : function(){
			
			// update class
	        this.$el.removeClass('active');
			
			
			// clear search
			this.$el.find('.search').val('');
			
			
			// clear inputs
			this.$el.find('.input-address').val('');
			this.$el.find('.input-lat').val('');
			this.$el.find('.input-lng').val('');
			
			
			// hide marker
			this.map.marker.setVisible( false );
		},
		
		edit : function(){
			
			// update class
	        this.$el.removeClass('active');
			
			
			// clear search
			var val = this.$el.find('.title h4').text();
			
			
			this.$el.find('.search').val( val ).focus();
			
		},
		
		refresh : function(){
			
			// trigger resize on div
			google.maps.event.trigger(this.map, 'resize');
			
			// center map
			this.center();
			
		}

	
	};
	
	
	/*
	*  acf/setup_fields
	*
	*  run init function on all elements for this field
	*
	*  @type	event
	*  @date	20/07/13
	*
	*  @param	{object}	e		event object
	*  @param	{object}	el		DOM object which may contain new ACF elements
	*  @return	N/A
	*/
	
	acf.add_action('ready append', function( $el ){
		
		//vars
		var $fields = acf.get_fields({ type : 'address_map'}, $el);
		
		
		// validate
		if( !$fields.exists() )
		{
			return;
		}
		
		
		// validate google
		if( typeof google === 'undefined' )
		{
			$.getScript('https://www.google.com/jsapi', function(){
			
			    google.load('maps', '3', { other_params: 'sensor=false&libraries=places', callback: function(){
			    
			        $fields.each(function(){
					
						acf.fields.address_map.set({ $el : $(this).find('.acf-address-map') }).init();
						
					});
			        
			    }});
			});
			
		}
		else
		{
			$fields.each(function(){
				
				acf.fields.address_map.set({ $el : $(this).find('.acf-address-map') }).init();
				
			});
			
		}
		
		
	});
	
	
	/*
	*  Events
	*
	*  jQuery events for this field
	*
	*  @type	function
	*  @date	1/03/2011
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	$(document).on('click', '.acf-address-map .acf-sprite-delete', function( e ){
		
		e.preventDefault();
		
		acf.fields.address_map.set({ $el : $(this).closest('.acf-address-map') }).clear();
		
		$(this).blur();
		
	});
	
	
	$(document).on('click', '.acf-address-map .acf-sprite-locate', function( e ){
		
		e.preventDefault();
		
		acf.fields.address_map.set({ $el : $(this).closest('.acf-address-map') }).locate();
		
		$(this).blur();
		
	});
	
	$(document).on('click', '.acf-address-map .title h4', function( e ){
		
		e.preventDefault();
		
		acf.fields.address_map.set({ $el : $(this).closest('.acf-address-map') }).edit();
			
	});
	
	$(document).on('keydown', '.acf-address-map .search', function( e ){
		
		// prevent form from submitting
		if( e.which == 13 )
		{
		    return false;
		}
			
	});
	
	$(document).on('blur', '.acf-address-map .search', function( e ){
		
		// vars
		var $el = $(this).closest('.acf-address-map');
		
		
		// has a value?
		if( $el.find('.input-lat').val() )
		{
			$el.addClass('active');
		}
			
	});
	
	acf.add_action('show_field', function( $field ){
		
		// validate
		if( ! acf.fields.address_map.ready )
		{
			return;
		}
		
		
		// validate
		if( acf.is_field($field, {type : 'address_map'}) )
		{
			acf.fields.address_map.set({ $el : $field.find('.acf-address-map') }).refresh();
		}
		
	});
	

})(jQuery);