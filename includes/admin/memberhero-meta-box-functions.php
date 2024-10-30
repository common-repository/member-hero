<?php
/**
 * Meta Box Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set up field props prior to saving them.
 */
function memberhero_setup_field_props( $label = 'label' ) {

	// Props.
	$props['key']         			= isset( $_POST['key'] ) ? sanitize_title( wp_unslash( $_POST['key'] ) ) : '';
	$props['type']         			= isset( $_POST['type'] ) ? memberhero_clean( wp_unslash( $_POST['type'] ) ) : '';
	$props['can_view']				= isset( $_POST['can_view'] ) ? memberhero_clean( wp_unslash( $_POST['can_view'] ) ) : '';
	$props['mimes']					= isset( $_POST['mimes'] ) ? memberhero_clean( wp_unslash( $_POST['mimes'] ) ) : '';
	$props['min_num']				= isset( $_POST['min_num'] ) ? memberhero_clean( wp_unslash( $_POST['min_num'] ) ) : '';
	$props['max_num']				= isset( $_POST['max_num'] ) ? memberhero_clean( wp_unslash( $_POST['max_num'] ) ) : '';
	$props['is_private']			= ! empty( $_POST['is_private'] );
	$props['is_readonly']			= ! empty( $_POST['is_readonly'] );
	$props['is_required']			= ! empty( $_POST['is_required'] );
	$props['autolinks']				= ! empty( $_POST['autolinks'] );
	$props['enable_decimals']		= ! empty( $_POST['enable_decimals'] );
	$props['emojis']				= ! empty( $_POST['emojis'] );
	$props['show_hints']			= ! empty( $_POST['show_hints'] );
	$props['delete_file']			= isset( $_POST['delete_file'] ) ? 'yes' : 'no';
	$props['label']					= memberhero_clean( wp_unslash( $_POST[ $label ] ) );
	$props['edit_label']			= memberhero_clean( wp_unslash( $_POST['edit_label'] ) );
	$props['view_label']			= memberhero_clean( wp_unslash( $_POST['view_label'] ) );
	$props['placeholder']			= memberhero_clean( wp_unslash( $_POST['placeholder'] ) );
	$props['icon']					= memberhero_clean( wp_unslash( $_POST['icon'] ) );
	$props['custom_error']			= memberhero_clean( wp_unslash( $_POST['custom_error'] ) );
	$props['ratings']				= memberhero_clean( wp_unslash( $_POST['ratings'] ) );
	$props['helper']				= wp_kses_post( wp_unslash( $_POST['helper'] ) );
	$props['blocked_emails']		= wp_kses_post( wp_unslash( $_POST['blocked_emails'] ) );
	$props['allowed_emails']		= wp_kses_post( wp_unslash( $_POST['allowed_emails'] ) );
	$props['dropdown_options']		= wp_kses_post( wp_unslash( $_POST['dropdown_options'] ) );
	$props['checkbox_options']		= wp_kses_post( wp_unslash( $_POST['checkbox_options'] ) );
	$props['radio_options']			= wp_kses_post( wp_unslash( $_POST['radio_options'] ) );

	return apply_filters( 'memberhero_setup_field_props', $props );
}

/**
 * Get custom field settings on display.
 */
function memberhero_init_custom_field_options() {
	$metabox = new MemberHero_Meta_Box_Field_Data();
	$metabox->output( new MemberHero_Field() );
}

/**
 * Add label field for custom field setup in modal display.
 */
function memberhero_add_title_option() {
	global $the_field;
	if ( get_post_type() != 'memberhero_field' ) {
		memberhero_wp_text_input(
			array(
				'id'          		=> 'label',
				'value'       		=> $the_field->label,
				'label'       		=> __( 'Label', 'memberhero' ),
				'description' 		=> __( 'Please enter a title for this custom field.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);
	}
}
add_action( 'memberhero_before_general_field_options', 'memberhero_add_title_option', 10 );

/**
 * Output a switch box.
 */
function memberhero_wp_switch( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : '';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : 'toggle_wrap';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

	switch( $field['value'] ) :
		case 'yes' :
			$field['state'] = 1;
			break;
		case 'no' :
			$field['state'] = 0;
			break;
		default :
			$field['value'] = (bool) $field['value'];
			$field['state'] = (bool) $field['value'];
			break;
	endswitch;

	// This will disable toggles for administrator.
	$name = get_post_meta( $thepostid, 'name', true );
	if ( $name == 'administrator' && ( strstr( $field['id'], 'memberhero_' ) || $field['id'] == 'manage_memberhero' ) ) {
		$field['class'] = $field['class'] . ' disabled';
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<div class="form-field form-field-switch ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-type="switch">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	echo '<div class="memberhero-field-area">';
	echo '<input type="checkbox" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';
	echo '<div class="memberhero-toggle ' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" data-toggle-on="' . $field['state'] . '"></div>';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo memberhero_help_tip( $field['description'] );
	}

	echo '</div>';
	echo '</div>';
}

/**
 * Output a text input box.
 */
function memberhero_wp_text_input( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
	$field['unit']         	= isset( $field['unit'] ) ? $field['unit'] : '';
	$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

	switch ( $data_type ) {
		default :
			break;
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<div class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-type="text">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	echo '<div class="memberhero-field-area">';
	echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( ! empty( $field['unit'] ) ) {
		echo '<span class="memberhero-unit">' . wp_kses_post( $field['unit'] ) . '</span>';
	}

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description description-inline">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo memberhero_help_tip( $field['description'] );
	}

	echo '</div>';
	echo '</div>';
}

/**
 * Output a select input box.
 */
function memberhero_wp_select( $field ) {
	global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
	$field     = wp_parse_args(
		$field, array(
			'class'             => 'memberhero-select short',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => get_post_meta( $thepostid, $field['id'], true ),
			'name'              => $field['id'],
			'placeholder'		=> '',
			'desc_tip'          => false,
			'no_items'			=> false,
			'custom_attributes' => array(),
		)
	);

	$wrapper_attributes = array(
		'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
	);

	$label_attributes = array(
		'for' => $field['id'],
	);

	$field_attributes          			= (array) $field['custom_attributes'];
	$field_attributes['style'] 			= $field['style'];
	$field_attributes['id']    			= $field['id'];
	$field_attributes['name']  			= $field['name'];
	$field_attributes['placeholder'] 	= $field['placeholder'];
	$field_attributes['class'] 			= $field['class'];

	// Multi.
	if ( isset( $field_attributes['multiple'] ) ) {
		if ( $field_attributes['class'] != 'memberhero-native' ) {
			$field_attributes['class'] 	= 'memberhero-select-multi';
		}
		$field_attributes['name'] 	= $field['name'] . '[]';
	}

	$tooltip     = ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '';
	$description = ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '';

	if ( $field['value'] && is_array( $field['value'] ) && ( $field_attributes['class'] != 'memberhero-native' ) ) {
		$field['options'] = array_replace( array_flip( $field['value'] ), $field['options'] );
	}
	?>
	<div <?php echo memberhero_implode_html_attributes( $wrapper_attributes ); ?> data-type="select">
		<label <?php echo memberhero_implode_html_attributes( $label_attributes ); ?>><?php echo wp_kses_post( $field['label'] ); ?></label>
		<div class="memberhero-field-area">
			<?php if ( empty( $field['options'] ) && $field['no_items'] ) : ?>
				<?php echo esc_html( $field['no_items'] ); ?>
			<?php else : ?>

			<select <?php echo memberhero_implode_html_attributes( $field_attributes ); ?>>
				<?php
				foreach ( $field['options'] as $key => $value ) {
					if ( empty( $value ) ) {
						$value = __( 'Untitled', 'memberhero' );
					}
					echo '<option value="' . esc_attr( $key ) . '"' . memberhero_selected( $key, $field['value'] ) . '>' . esc_html( $value ) . '</option>';
				}
				?>
			</select>
			<?php endif; ?>

			<?php if ( $tooltip ) : ?>
				<?php echo memberhero_help_tip( $tooltip ); ?>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<span class="description"><?php echo wp_kses_post( $description ); ?></span>
			<?php endif; ?>

			<div style="flex-basis: 100%;height: 0;"></div>

			<?php do_action( 'memberhero_wp_select_field', $field['id'] ); ?>

		</div>

	</div>
	<?php
}

/**
 * Output a textarea input box.
 */
function memberhero_wp_textarea( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 2;
	$field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<div class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-type="textarea">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo memberhero_help_tip( $field['description'] );
	}

	echo '</div>';
}

/**
 * Output drag-n-drop tool bar.
 */
function memberhero_dragdrop_topbar() {
	global $the_form;
	?>
	<div class="memberhero-bld-topbar">
		<div class="memberhero-bld-left"><span class="description"></span></div>
		<div class="memberhero-bld-right">
			<a href="#" class="button button-primary disabled save_form" data-id="<?php echo absint( $the_form->id ); ?>"><?php echo memberhero_svg_icon( 'upload-cloud' ); ?><span><?php esc_html_e( 'Save changes', 'memberhero' ); ?></span></a>
		</div><div class="clear"></div>
	</div>
	<?php
}

/**
 * Output drag-n-drop default row.
 */
function memberhero_dragdrop_default() {
	global $the_form;
	?>
	<div class="memberhero-bld-row hidden">
		<?php memberhero_dragdrop_row_head(); ?>
		<div class="memberhero-bld-cols">
			<div class="memberhero-bld-col">
				<div class="memberhero-bld-elems">
					<div class="memberhero-bld-elem hidden">
						<div class="memberhero-bld-left">
							<span class="memberhero-bld-action"><a href="#" class="memberhero-move-field"><?php echo memberhero_svg_icon( 'move' ); ?></a></span>
							<span class="memberhero-bld-icon"><?php echo memberhero_svg_icon( 'default' ); ?></span>
							<span class="memberhero-bld-label">{label}</span>
							<span class="memberhero-bld-helper">{key}</span>
						</div>
						<?php memberhero_dragdrop_field_actions(); ?>
					</div>
				</div>
				<?php memberhero_dragdrop_add(); ?>
			</div>
		</div>

	</div>
	<?php
}

/**
 * Output drag-n-drop rows loop.
 */
function memberhero_dragdrop_rows() {
	global $the_form;
	?>
	<?php if ( $the_form->row_count > 0 ) : ?>
	<?php for( $i = 1; $i <= $the_form->row_count; $i++ ) : ?>

	<div class="memberhero-bld-row">
		<?php memberhero_dragdrop_row_head( $i ); ?>
		<?php memberhero_dragdrop_columns( $i ); ?>
	</div>

	<?php endfor; ?>
	<?php endif; ?>
	<?php
}

/**
 * Output drag-n-drop columns loop.
 */
function memberhero_dragdrop_columns( $i ) {
	global $the_form;
	?>
	<div class="memberhero-bld-cols">

	<?php for( $c = 0; $c < $the_form->cols[$i]['count']; $c++ ) : ?>

	<div class="memberhero-bld-col">
		<div class="memberhero-bld-elems">
			<?php foreach( (array) $the_form->fields_in( $i, $c ) as $k => $field ) : 
				if ( ! isset( $field['data']['type'] ) ) {
					continue;
				}
				$r = $field['data'];
			?>
			<div class="memberhero-bld-elem" <?php echo memberhero_get_data_attributes( $r ); ?>>
				<div class="memberhero-bld-left">
					<span class="memberhero-bld-action"><a href="#" class="memberhero-move-field"><?php echo memberhero_svg_icon( 'move' ); ?></a></span>
					<span class="memberhero-bld-icon"><?php echo memberhero_svg_icon( ! empty( $r['icon'] ) ? esc_attr( $r['icon'] ) : 'default' ); ?></span>
					<span class="memberhero-bld-label"><?php echo ! empty( $r[ 'label' ] ) ? esc_html( $r['label'] ) : __( 'No label', 'memberhero' ); ?></span>
					<span class="memberhero-bld-helper"><?php echo esc_attr( $r['key'] ); ?></span>
				</div>
				<?php memberhero_dragdrop_field_actions(); ?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php memberhero_dragdrop_add(); ?>
	</div>

	<?php endfor; ?>

	</div>
	<?php
}

/**
 * Output drag-n-drop add row.
 */
function memberhero_dragdrop_addrow() {
	global $the_form;
	?>
	<div class="memberhero-bld-new">
		<a href="#" class="memberhero-add-row">
			<?php echo memberhero_svg_icon( 'plus' ); ?>
		</a>
	</div>
	<?php
}

/**
 * Output drag-n-drop add element.
 */
function memberhero_dragdrop_add() {
	global $the_form;
	?>
	<div class="memberhero-bld-add">
		<a href="#memberhero-add-element" class="memberhero-add-element" rel="modal:open">
			<?php echo memberhero_svg_icon( 'plus' ); ?>
		</a>
	</div>
	<?php
}

/**
 * Output drag-n-drop row header.
 */
function memberhero_dragdrop_row_head( $i = null ) {
	global $the_form;
	?>
	<div class="memberhero-bld-elem">
		<div class="memberhero-bld-left">
			<span class="memberhero-bld-action"><a href="#" class="memberhero-move-row"><?php echo memberhero_svg_icon( 'move' ); ?></a></span>
			<span class="memberhero-bld-icon"><a href="#" class="memberhero-toggle-row"><?php echo memberhero_svg_icon( 'chevron-up' ); ?></a></span>
			<span class="memberhero-bld-label" data-default="<?php esc_attr_e( 'Untitled Row', 'memberhero' ); ?>"><?php echo isset( $the_form->rows[ $i - 1 ][ 'title' ] ) ? $the_form->rows[ $i - 1 ][ 'title' ] : ''; ?></span>
		</div>
		<div class="memberhero-bld-right">
			<span class="memberhero-bld-action"><a href="#memberhero-edit-row" class="memberhero-edit-row" rel="modal:open"><?php echo memberhero_svg_icon( 'edit-2' ); ?></a></span>
			<span class="memberhero-bld-action"><a href="#" class="memberhero-duplicate-row"><?php echo memberhero_svg_icon( 'copy' ); ?></a></span>
			<span class="memberhero-bld-action"><a href="#" class="memberhero-delete-row"><?php echo memberhero_svg_icon( 'trash-2' ); ?></a></span>
		</div><div class="clear"></div>
	</div>
	<?php
}

/**
 * Output drag-n-drop field actions.
 */
function memberhero_dragdrop_field_actions() {
	global $the_form;
	?>
	<div class="memberhero-bld-right">
		<span class="memberhero-bld-action"><a href="#memberhero-add-field" class="memberhero-edit-field" rel="modal:open"><?php echo memberhero_svg_icon( 'edit-2' ); ?></a></span>
		<span class="memberhero-bld-action"><a href="#" class="memberhero-duplicate-field"><?php echo memberhero_svg_icon( 'copy' ); ?></a></span>
		<span class="memberhero-bld-action"><a href="#" class="memberhero-delete-field"><?php echo memberhero_svg_icon( 'trash-2' ); ?></a></span>
	</div>
	<div class="clear"></div>
	<?php
}