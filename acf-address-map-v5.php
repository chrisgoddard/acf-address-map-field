<?php

class acf_field_address_map extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'address_map';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Address Map', 'acf-address_map');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'jQuery';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(
			'center_lat'	=> '47.6256211',
			'center_lng'	=> '-122.3529964',
			'zoom'			=> '14'
		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('address_map', 'error');
		*/
		
		$this->l10n = array(
			'locating'			=>	__("Locating",'acf'),
			'browser_support'	=>	__("Sorry, this browser does not support geolocation",'acf'),
		);
		
				
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Latitude','acf-address_map'),
			'type'			=> 'text',
			'name'			=> 'center_lat',
			'prepend'		=> 'lat',
			'placeholder'	=> $this->default_values['center_lat']
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Longitude','acf-address_map'),
			'type'			=> 'text',
			'name'			=> 'center_lng',
			'prepend'		=> 'lng',
			'placeholder'	=> $this->default_values['center_lat']
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Zoom','acf-address_map'),
			'type'			=> 'number',
			'name'			=> 'zoom',
			'placeholder'	=> $this->default_values['zoom']
		));
	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		
		
		// default value
		if( !is_array($field['value']) )
		{
			$field['value'] = array();
		}
		
		$field['value'] = wp_parse_args($field['value'], array(
			'map_name' 	=> '',
			'name' 		=> '',
			'address'	=> array(
						'line_1' => '',
						'line_2' => '',
						'city' => '',
						'state' => '',
						'zip' => '',
						'country' => '',
				),
			'info'		=> array(
						'phone' => '',
						'website' => '',
						'email' => '',
						'google_map' => '',
				),
			'lat'		=> '',
			'lng'		=> ''
		));
		
		
/*
		// default options
		foreach( $this->default_values as $k => $v )
		{
			if( ! $field[ $k ] )
			{
				$field[ $k ] = $v;
			}	
		}
		
*/
		
		// vars
		$atts = array(
			'id'			=> $field['id'],
			'class'			=> $field['class'],
			'data-id'		=> $field['id'] . '-' . uniqid(), 
			'data-lat'		=> $field['center_lat'],
			'data-lng'		=> $field['center_lng'],
			'data-zoom'		=> $field['zoom']
		);
		
		
		// modify atts
		$atts['class'] .= ' acf-address-map';
		
		if( $field['value']['address']['line_1'] ) {
		
			$atts['class'] .= ' active';
			
		}
		
		?>
		
		<div <?php acf_esc_attr_e($atts); ?>>
			
			<div style="display:none;">
				<?php /* foreach( $field['value'] as $k => $v ): ?>
					<input type="hidden" class="input-<?php echo $k; ?>" name="<?php echo esc_attr($field['name']); ?>[<?php echo $k; ?>]" value="<?php echo esc_attr( $v ); ?>" />
				<?php endforeach; */?>
			</div>
			
			<div class="title">
				
				<div class="has-value">
					<a href="#" class="acf-icon light acf-soh-target" title="<?php _e("Clear location", 'acf'); ?>">
						<i class="acf-sprite-delete"></i>
					</a>
					<h4><?php echo $field['value']['map_name']; ?></h4>
				</div>
				
				<div class="no-value">
					<a href="#" class="acf-icon light acf-soh-target" title="<?php _e("Find current location", 'acf'); ?>">
						<i class="acf-sprite-locate"></i>
					</a>
					<input type="text" name="<?php echo $field['name']; ?>[map_name]" placeholder="<?php _e("Search for address...",'acf'); ?>" class="search" value="<?php echo $this->cond_display_value($field, array('name')); ?>"/>
				</div>
				
			</div>
			
			<div class="address">
			
				<table >				
				<tbody>
				<tr class="name">
					<td>Business Name</td>
					<td><input class="business_name" name="<?php echo esc_attr($field['name']); ?>[name]" type="text" value="<?php echo $this->cond_display_value($field, array('name'));  ?>"></td>
				</tr>
				
				<tr class="phone">
					<td>Phone</td>
					<td><input class="phone" name="<?php echo esc_attr($field['name']); ?>[info][phone]" type="text" value="<?php echo $this->cond_display_value($field, array('info', 'phone'));  ?>"></td>
				</tr>
				
				<tr class="website">
					<td>Website</td>
					<td><input class="website" name="<?php echo esc_attr($field['name']); ?>[info][website]" type="url" value="<?php echo $this->cond_display_value($field, array('info', 'website'));  ?>"></td>
				</tr>
				
				<tr class="email">
					<td>Email</td>
					<td><input class="email" name="<?php echo esc_attr($field['name']); ?>[info][email]" type="email" value="<?php echo $this->cond_display_value($field, array('info', 'email'));  ?>"></td>
				</tr>
				
				<tr class="address_line address_1">
					<td>Address Line 1</td>
					<td><input class="address_one" name="<?php echo esc_attr($field['name']); ?>[address][line_1]" type="text" value="<?php echo $this->cond_display_value($field, array('address', 'line_1'));  ?>"></td>
				</tr>
				
				<tr class="address_line address_two">
					<td>Address Line 2</td>
					<td><input class="address_two" name="<?php echo esc_attr($field['name']); ?>[address][line_2]" type="text" value="<?php echo $this->cond_display_value($field, array('address', 'line_2'));  ?>"></td>
				</tr>
				
				<tr class="city">
					<td>City</td>
					<td><input class="city" name="<?php echo esc_attr($field['name']); ?>[address][city]" type="text" value="<?php echo $this->cond_display_value($field, array('address', 'city'));  ?>"></td>
				</tr>
				
				<tr class="state">
					<td>State</td>
					<td><input class="state" name="<?php echo esc_attr($field['name']); ?>[address][state]" type="text" value="<?php echo $this->cond_display_value($field, array('address', 'state'));  ?>"></td>
				</tr>
				
				<tr class="zip">
					<td>Zip</td>
					<td><input class="zip" name="<?php echo esc_attr($field['name']); ?>[address][zip]" type="text" value="<?php echo $this->cond_display_value($field, array('address', 'zip'));  ?>"></td>
				</tr>
				
				<tr class="country">
					<td>Country</td>
					<td><input class="country" name="<?php echo esc_attr($field['name']); ?>[address][country]" type="text" value="<?php echo $this->cond_display_value($field, array('address', 'country'));  ?>"></td>
				</tr>
				
				<tr class="location">
					<td>Location</td>
					<td>
						<input name="<?php echo esc_attr($field['name']); ?>[lat]" class="latitude" type="text" value="<?php echo $this->cond_display_value($field, array('lat'));  ?>">
						<input name="<?php echo esc_attr($field['name']); ?>[lng]" class="longitude" type="text" value="<?php echo $this->cond_display_value($field, array('lng'));  ?>">
					</td>
				</tr>
				
				<tr class="google-map">
					<td>Google Map URL</td>
					<td><input class="google-map" name="<?php echo esc_attr($field['name']); ?>[info][google_map]" type="text" value="<?php echo $this->cond_display_value($field, array('info', 'google_map'));  ?>"></td>
				</tr>
				
			<!--
	<tr class="options">
					<td><a href="#" data-action="reset-fields" class="button button-secondary button-large">Reset</a></td>
					<td><?php submit_button('Update', 'primary', 'submit', false); ?></td>
				</tr>
-->
				</tbody>
				
				</table>
			
			</div>
			
			<div class="canvas">
				
			</div>
			
		</div>
		<?php
	}
	
	function cond_display_value($field, $ref = array() )
	{
		
		$value = $field['value'];
		
		if(count($ref) > 0 ){
			
			$next = $value;
			
			foreach($ref as $p){
				$next = $next[$p];
			}
			
			return ($next !== null && !is_array($next)) ? $next : '';
			
		} else {
			return ($value !== null && !is_array($next)) ? $value : '';
		}
		
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	
	function input_admin_enqueue_scripts() {
		
		$dir = plugin_dir_url( __FILE__ );
		
		
		// register & include JS
		wp_register_script( 'acf-input-address_map', "{$dir}js/input.v5.js" );
		wp_enqueue_script('acf-input-address_map');
		
		
		// register & include CSS
		wp_register_style( 'acf-input-address_map', "{$dir}css/input.v5.css" ); 
		wp_enqueue_style('acf-input-address_map');
		
		
	}
		
	
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed to the render_field() function
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @param	$template (boolean) true if value requires formatting for front end template function
	*  @return	$value
	*/
		
	function format_value( $value, $post_id, $field ) {
				
		// bail early if no value
		if( empty($value) ) {
		
			return $value;
			
		}
		
		$output['name'] = $value['name'];
		
		$output['test'] = 'test';
				
		$line2 = ($value['address']['line_2']) ? ', ' . $value['address']['line_2'] : '';
		
		$output['formatted_address'] = '<div itemscope itemtype="http://schema.org/PostalAddress">
										 <span itemprop="name">'.$value['name'].'</span>
										 <span itemprop="streetAddress">'.$value['address']['line_1'].$line2.'</span>
										 <span itemprop="addressLocality">'.$value['address']['city'].'</span>,
										 <span itemprop="addressRegion">'.$value['address']['state'].'</span>
										 <span itemprop="postalCode">'.$value['address']['zip'].'</span>
										 <span itemprop="addressCountry">'.$value['address']['country'].'</span>
										</div>';
		
		$output['address'] = $value['address'];
		
		$output['info'] = $value['info'];
		
		$output['position'] = array(
							'lat' => $value['lat'],
							'lng' => $value['lng']
							);
		
		
		return $output;
	}
	
		
	
}


// create field
new acf_field_address_map();

?>
