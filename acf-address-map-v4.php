<?php

class acf_field_address_map extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'address_map';
		$this->label = __('Address Map');
		$this->category = __("jQuery",'acf'); // Basic, Content, Choice, etc
		$this->default_values = array(
			'center_lat'	=> '47.6256211',
			'center_lng'	=> '-122.3529964',
			'zoom'			=> '14'
		);
		$this->l10n = array(
			'locating'			=>	__("Locating",'acf'),
			'browser_support'	=>	__("Sorry, this browser does not support geolocation",'acf'),
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

	}
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
		
		// register ACF scripts
		wp_register_script( 'acf-input-address_map', $this->settings['dir'] . 'js/input.v4.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-address_map', $this->settings['dir'] . 'css/input.v4.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-address_map',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-address_map',	
		));
		
		
	}
	
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// require the googlemaps JS ( this script is now lazy loaded via JS )
		//wp_enqueue_script('acf-googlemaps');
		
		
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
		
		
		// default options
		foreach( $this->default_values as $k => $v )
		{
			if( ! $field[ $k ] )
			{
				$field[ $k ] = $v;
			}	
		}
		
		
		// vars
		$o = array(
			'class'		=>	'',
		);
		
		if( $field['value']['address'] )
		{
			$o['class'] = 'active';
		}
		
		
		$atts = '';
		$keys = array( 
			'data-id'	=> 'id', 
			'data-lat'	=> 'center_lat',
			'data-lng'	=> 'center_lng',
			'data-zoom'	=> 'zoom'
		);
		
		foreach( $keys as $k => $v )
		{
			$atts .= ' ' . $k . '="' . esc_attr( $field[ $v ] ) . '"';	
		}
		
		?>
		
		<div class="acf-address-map <?php echo $o['class']; ?>" <?php echo $atts; ?>>
			
			<div style="display:none;">
				<?php /* foreach( $field['value'] as $k => $v ): ?>
					<input type="hidden" class="input-<?php echo $k; ?>" name="<?php echo esc_attr($field['name']); ?>[<?php echo $k; ?>]" value="<?php echo esc_attr( $v ); ?>" />
				<?php endforeach; */?>
			</div>
			
			<div class="title">
				
				<div class="has-value">
					<a href="#" class="acf-sprite-remove ir" title="<?php _e("Clear location",'acf'); ?>">Remove</a>
					<h4><?php echo $field['value']['map_name']; ?></h4>
				</div>
				
				<div class="no-value">
					<a href="#" class="acf-sprite-locate ir" title="<?php _e("Find current location",'acf'); ?>">Locate</a>
					<input type="text" name="<?php echo $field['name'] ?>[map_name]" placeholder="<?php _e("Search for address...",'acf'); ?>" class="search" value="<?php echo $this->cond_display_value($field, ['name']); ?>"/>
				</div>
				
			</div>
			
			<div class="address">
			
				<table >				
				<tbody>
				<tr class="name">
					<td>Business Name</td>
					<td><input class="business_name" name="<?php echo esc_attr($field['name']); ?>[name]" type="text" value="<?php echo $this->cond_display_value($field, ['name']); ?>"></td>
				</tr>
				
				<tr class="phone">
					<td>Phone</td>
					<td><input class="phone" name="<?php echo esc_attr($field['name']); ?>[info][phone]" type="text" value="<?php echo $this->cond_display_value($field, ['info', 'phone']); ?>"></td>
				</tr>
				
				<tr class="website">
					<td>Website</td>
					<td><input class="website" name="<?php echo esc_attr($field['name']); ?>[info][website]" type="url" value="<?php echo $this->cond_display_value($field, ['info', 'website']); ?>"></td>
				</tr>
				
				<tr class="email">
					<td>Email</td>
					<td><input class="email" name="<?php echo esc_attr($field['name']); ?>[info][email]" type="email" value="<?php echo $this->cond_display_value($field, ['info', 'email']); ?>"></td>
				</tr>
				
				<tr class="address_line address_1">
					<td>Address Line 1</td>
					<td><input class="address_one" name="<?php echo esc_attr($field['name']); ?>[address][line_1]" type="text" value="<?php echo $this->cond_display_value($field, ['address', 'line_1']); ?>"></td>
				</tr>
				
				<tr class="address_line address_two">
					<td>Address Line 2</td>
					<td><input class="address_two" name="<?php echo esc_attr($field['name']); ?>[address][line_2]" type="text" value="<?php echo $this->cond_display_value($field, ['address', 'line_2']); ?>"></td>
				</tr>
				
				<tr class="city">
					<td>City</td>
					<td><input class="city" name="<?php echo esc_attr($field['name']); ?>[address][city]" type="text" value="<?php echo $this->cond_display_value($field, ['address', 'city']); ?>"></td>
				</tr>
				
				<tr class="state">
					<td>State</td>
					<td><input class="state" name="<?php echo esc_attr($field['name']); ?>[address][state]" type="text" value="<?php echo $this->cond_display_value($field, ['address', 'state']); ?>"></td>
				</tr>
				
				<tr class="zip">
					<td>Zip</td>
					<td><input class="zip" name="<?php echo esc_attr($field['name']); ?>[address][zip]" type="text" value="<?php echo $this->cond_display_value($field, ['address', 'zip']); ?>"></td>
				</tr>
				
				<tr class="country">
					<td>Country</td>
					<td><input class="country" name="<?php echo esc_attr($field['name']); ?>[address][country]" type="text" value="<?php echo $this->cond_display_value($field, ['address', 'country']); ?>"></td>
				</tr>
				
				<tr class="location">
					<td>Location</td>
					<td>
						<input name="<?php echo esc_attr($field['name']); ?>[lat]" class="latitude" type="text" value="<?php echo $this->cond_display_value($field, ['lat']); ?>">
						<input name="<?php echo esc_attr($field['name']); ?>[lng]" class="longitude" type="text" value="<?php echo $this->cond_display_value($field, ['lng']); ?>">
					</td>
				</tr>
				
				<tr class="google-map">
					<td>Google Map URL</td>
					<td><input class="google-map" name="<?php echo esc_attr($field['name']); ?>[info][google_map]" type="text" value="<?php echo $this->cond_display_value($field, ['info', 'google_map']); ?>"></td>
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
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Center",'acf'); ?></label>
		<p class="description"><?php _e('Center the initial map','acf'); ?></p>
	</td>
	<td>
		<ul class="hl clearfix">
			<li style="width:48%;">
				<?php 
			
				do_action('acf/create_field', array(
					'type'			=> 'text',
					'name'			=> 'fields['.$key.'][center_lat]',
					'value'			=> $field['center_lat'],
					'prepend'		=> 'lat',
					'placeholder'	=> $this->default_values['center_lat']
				));
				
				?>
			</li>
			<li style="width:48%; margin-left:4%;">
				<?php 
			
				do_action('acf/create_field', array(
					'type'			=> 'text',
					'name'			=> 'fields['.$key.'][center_lng]',
					'value'			=> $field['center_lng'],
					'prepend'		=> 'lng',
					'placeholder'	=> $this->default_values['center_lng']
				));
				
				?>
			</li>
		</ul>
		
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Zoom",'acf'); ?></label>
		<p class="description"><?php _e('Set the initial zoom level','acf'); ?></p>
	</td>
	<td>
		<?php 
		
		do_action('acf/create_field', array(
			'type'			=> 'number',
			'name'			=> 'fields['.$key.'][zoom]',
			'value'			=> $field['zoom'],
			'placeholder'	=> $this->default_values['zoom']
		));
		
		?>
	</td>
</tr>

		<?php
		
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/


		// Note: This function can be removed if not used
		
		$output['name'] = $value['name'];
				
		
		$line2 = ($value['address']['line_2']) ? ', ' . $value['address']['line_2'] : '';
		
		$output['formatted_address'] = '
		<div itemscope itemtype="http://schema.org/PostalAddress">
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
