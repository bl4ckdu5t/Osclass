<?php
    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */

    $maxPHPsize    = View::newInstance()->_get('max_size_upload') ;
    $imagickLoaded = extension_loaded('imagick') ;
    $aGD           = @gd_info() ;
    $freeType      = array_key_exists('FreeType Support', $aGD) ;

    //customize Head
    function customHead(){
        echo '<script type="text/javascript" src="'.osc_current_admin_theme_js_url('jquery.validate.min.js').'"></script>';
        ?>
        <link rel="stylesheet" media="screen" type="text/css" href="<?php echo osc_current_admin_theme_js_url('colorpicker/css/colorpicker.css') ; ?>" />
        <script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('colorpicker/js/colorpicker.js') ; ?>"></script>
<script type="text/javascript">
$(document).ready(function(){
    // Code for form validation
    $.validator.addMethod('regexp', function(value, element, param) {
        return this.optional(element) || value.match(param);
    },
    '<?php echo osc_esc_js( __('Size is not in the correct format') ); ?>');

    $("form[name=media_form]").validate({
        rules: {
            dimThumbnail: {
                required: true,
                regexp: /^[0-9]+x[0-9]+$/i
            },
            dimPreview: {
                required: true,
                regexp: /^[0-9]+x[0-9]+$/i
            },
            dimNormal: {
                required: true,
                regexp: /^[0-9]+x[0-9]+$/i
            },
            maxSizeKb: {
                required: true,
                digits: true
            }
        },
        messages: {
            dimThumbnail: {
                required: "<?php echo osc_esc_js( __("Thumbnail size: this field is required")); ?>",
                regexp: "<?php echo osc_esc_js( __("Thumbnail size: is not in the correct format")); ?>"
            },
            dimPreview: {
                required: "<?php echo osc_esc_js( __("Preview size: this field is required")); ?>",
                regexp: "<?php echo osc_esc_js( __("Preview size: is not in the correct format")); ?>"
            },
            dimNormal: {
                required: "<?php echo osc_esc_js( __("Normal size: this field is required")); ?>",
                regexp: "<?php echo osc_esc_js( __("Normal size: is not in the correct format")); ?>"
            },
            maxSizeKb: {
                required: "<?php echo osc_esc_js( __("Maximun size: this field is required")); ?>",
                digits: "<?php echo osc_esc_js( __("Maximun size: this field has to be numeric only")); ?>"
            }
        },
        wrapper: "li",
        errorLabelContainer: "#error_list",
        invalidHandler: function(form, validator) {
            $('html,body').animate({ scrollTop: $('h1').offset().top }, { duration: 250, easing: 'swing'});
        }
    });

    $('#colorpickerField').ColorPicker({
        onSubmit: function(hsb, hex, rgb, el) { },
        onChange: function (hsb, hex, rgb) {
            $('#colorpickerField').val(hex) ;
        }
    }) ;

    $('#watermark_none').bind('change', function() {
        if( $(this).attr('checked') ) {
            $('#watermark_text_box').hide() ;
            $('#watermark_image_box').hide() ;
        }
    }) ;

    $('#watermark_text').bind('change', function() {
        if( $(this).attr('checked') ) {
            $('#watermark_text_box').show() ;
            $('#watermark_image_box').hide() ;
            if( !$('input[name="keep_original_image"]').attr('checked') ) {
                alert('<?php echo addslashes( __("It's highly recommended to have 'Keep original image' option active when you use watermarks.") ) ; ?>') ;
            }
        }
    }) ;

    $('#watermark_image').bind('change', function() {
        if( $(this).attr('checked') ) {
            $('#watermark_text_box').hide() ;
            $('#watermark_image_box').show() ;
            if( !$('input[name="keep_original_image"]').attr('checked') ) {
                alert('<?php echo addslashes( __("It's highly recommended to have 'Keep original image' option active when you use watermarks.") ) ; ?>') ;
            }
        }
    }) ;

    $('input[name="keep_original_image"]').change(function() {
        if( !$(this).attr('checked') ) {
            if( !$('#watermark_none').attr('checked') ) {
                alert('<?php echo addslashes( __("It's highly recommended to have 'Keep original image' option active when you use watermarks.") ) ; ?>') ;
            }
        }
    }) ;
}) ;
</script>
        <?php
    }
    osc_add_hook('admin_header','customHead');

    function render_offset(){
        return 'row-offset';
    }

    osc_add_hook('admin_page_header','customPageHeader');
    function customPageHeader(){ ?>
        <h1><?php _e('Settings') ; ?></h1>
    <?php
    }

    function customPageTitle($string) {
        return sprintf(__('Media Settings &raquo; %s'), $string);
    }
    osc_add_filter('admin_title', 'customPageTitle');

    osc_current_admin_theme_path( 'parts/header.php' ) ; ?>
<!--los input tienen una class para el tamaño ...-->
<div id="general-settings">
    <h2 class="render-title"><?php _e('Media Settings'); ?></h2>
    <ul id="error_list" style="display: none;"></ul>
    <form name="media_form" action="<?php echo osc_admin_base_url(true) ; ?>" method="post">
        <input type="hidden" name="page" value="settings" />
        <input type="hidden" name="action" value="media_post" />
        <fieldset>
            <div class="form-horizontal">
                <h2 class="render-title"><?php _e('Images sizes') ; ?></h2>
                <div class="form-row">
                    <p><?php _e('The sizes listed below determine the maximum dimensions in pixels to use when uploading a image. Format: <b>Width</b> x <b>Height</b>.') ; ?></p>
                    <div class="form-label"><?php _e('Thumbnail size') ; ?></div>
                    <div class="form-controls"><input type="text" class="input-medium" name="dimThumbnail" value="<?php echo osc_esc_html( osc_thumbnail_dimensions() ); ?>" /></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Preview size') ; ?></div>
                    <div class="form-controls"><input type="text" class="input-medium" name="dimPreview" value="<?php echo osc_esc_html( osc_preview_dimensions() ) ; ?>" /></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Normal size') ; ?></div>
                    <div class="form-controls"><input type="text" class="input-medium"  name="dimNormal" value="<?php echo osc_esc_html( osc_normal_dimensions() ) ; ?>" /></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Normal size') ; ?></div>
                    <div class="form-controls">
                        <div class="form-label-checkbox">
                            <input type="checkbox" id="keep_original_image" name="keep_original_image" value="1" <?php echo ( osc_keep_original_image() ? 'checked="checked"' : '' ) ; ?> />
                            <label for="keep_original_image"><?php _e('Keep original image, unaltered after uploading.') ; ?></label>
                            <span class="help-box"><?php _e('It might occupy more space than usual.') ; ?></span>
                        </div>
                    </div>
                </div>
                
                <h2 class="render-title"><?php _e('Restrictions') ; ?></h2>

                <div class="form-row">
                    <div class="form-label"><?php _e('Maximum size') ; ?></div>
                    <div class="form-controls">
                        <input type="text" class="input-medium" name="maxSizeKb" value="<?php echo osc_esc_html( osc_max_size_kb() ) ; ?>" />
                        <span class="help-box"><?php _e('Size in KB') ; ?></span>
                        <div class="flashmessage flashmessage-warning flashmessage-inline">
                            <p><?php printf( __('Maximum size PHP configuration allows: %d KB'), $maxPHPsize ) ; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-label"><?php _e('Allowed formats') ; ?></div>
                    <div class="form-controls">
                        <input type="text" class="input-medium" name="allowedExt" value="<?php echo osc_esc_html( osc_allowed_extension() ) ; ?>" />
                        <span class="help-box"><?php _e('For example: jpg, png, gif') ; ?></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-label"><?php _e('ImageMagick') ; ?></div>
                    <div class="form-controls">
                        <div class="form-label-checkbox">
                            <input type="checkbox" name="use_imagick" value="1" <?php echo ( osc_use_imagick()?'checked="checked"':'' ); ?> <?php if( !$imagickLoaded ) echo 'disabled="disabled"'; ?> />
                            <label for="use_imagick"><?php _e('Use ImageMagick instead of GD library') ; ?></label>
                        </div>
                        <?php if( !$imagickLoaded ) { ?>
                        <div class="flashmessage flashmessage-error flashmessage-inline">
                            <p><?php _e('ImageMagick library is not loaded') ; ?></p>
                        </div>
                        <?php } ?>
                        <div class="help-box"><?php _e("It's faster and consumes less resources than GD library.") ; ?></div>
                    </div>
                </div>
                
                <h2 class="render-title"><?php _e('Watermark') ; ?></h2>
                
                <div class="form-row">
                    <div class="form-label"><?php _e('Watermark type') ; ?></div>
                    <div class="form-controls">
                        <div class="form-label-checkbox">
                            <input type="radio" id="watermark_none" name="watermark_type" value="none" <?php echo ( ( !osc_is_watermark_image() && !osc_is_watermark_text() ) ? 'checked="checked"' : '' ); ?> />
                            <label for="watermark_none"><?php _e('None') ; ?></label>
                        </div>
                        <div class="form-label-checkbox">
                            <input type="radio" id="watermark_text" name="watermark_type" value="text" <?php echo ( osc_is_watermark_text() ? 'checked="checked"' : '' ) ; ?> <?php echo ( $freeType ? '' : 'disabled="disabled"' ); ?> />
                            <label for="watermark_text"><?php _e('Text') ; ?></label>
                            <?php if( !$freeType ) { ?>
                            <div class="flashmessage flashmessage-inline error">
                                <p><?php printf( __('Freetype library is required. How to <a target="_blank" href="%s">install/configure</a>') , 'http://www.php.net/manual/en/image.installation.php' ) ; ?></p>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="form-label-checkbox">
                            <input type="radio" id="watermark_image" name="watermark_type" value="image" <?php echo ( osc_is_watermark_image() ? 'checked="checked"' : '' ) ; ?> />
                            <label for="watermark_image"><?php _e('Image') ; ?></label>
                        </div>
                    </div>
                </div>
                
                <h2 class="render-title"><?php _e('Regenerate images') ; ?></h2>

                <div class="form-row">
                    <div class="form-controls">
                    <p>    
                        <?php _e("You can regenerate your different image dimensions. If you have changed the dimension of thumbnails, preview or normal images, you might want to regenerate your images.") ; ?>
                    </p>
                    <a class="btn" href="<?php echo osc_admin_base_url(true) . '?page=settings&action=images_post'; ?>"><?php  _e('Regenerate') ; ?></a>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="form-actions">
                    <input type="submit" value="<?php echo osc_esc_html( __('Save changes') ) ; ?>" class="btn btn-submit" />
                </div>

            </div>
        </fieldset>
    </form>
    
       
</div>
<?php osc_current_admin_theme_path( 'parts/footer.php' ) ; ?>                