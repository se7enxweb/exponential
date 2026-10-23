{* expimageeditor: the settings and texts of the editor, once per page (included by content/datatype/edit/ezimage_editor.tpl
   and ezimage_new.tpl): the stylesheet, the frame script and #expimageeditor-config (settings and texts as JSON, read
   by expimageeditor.js); not again when the save view renders the attribute anew ($expimageeditor_response). *}
{if is_set( $expimageeditor_response )|not()}
{run-once}
{ezcss_require( 'expimageeditor.css' )}
{ezscript_require( 'expimageeditor.js' )}
{def $expimageeditor_watermarks = array()}
{foreach ezini( 'EditorSettings', 'Watermarks', 'expimageeditor.ini' ) as $expimageeditor_watermark}
    {def $expimageeditor_parts = $expimageeditor_watermark|explode( ';' )}
    {if $expimageeditor_parts|count|eq( 2 )}
        {set $expimageeditor_watermarks = $expimageeditor_watermarks|append( hash( 'label', $expimageeditor_parts[0], 'url', $expimageeditor_parts[1]|ezdesign( 'no' ) ) )}
    {/if}
    {undef $expimageeditor_parts}
{/foreach}
{def $expimageeditor_fonts = array()}
{foreach ezini( 'EditorSettings', 'Fonts', 'expimageeditor.ini' ) as $expimageeditor_font}
    {def $expimageeditor_parts = $expimageeditor_font|explode( ';' )
         $expimageeditor_outline = ''}
    {if $expimageeditor_parts|count|eq( 4 )}
        {set $expimageeditor_outline = $expimageeditor_parts[3]|ezdesign( 'no' )}
    {/if}
    {if or( $expimageeditor_parts|count|eq( 3 ), $expimageeditor_parts|count|eq( 4 ) )}
        {set $expimageeditor_fonts = $expimageeditor_fonts|append( hash( 'label', $expimageeditor_parts[0], 'family', $expimageeditor_parts[1], 'url', $expimageeditor_parts[2]|ezdesign( 'no' ), 'outline', $expimageeditor_outline ) )}
    {/if}
    {undef $expimageeditor_parts $expimageeditor_outline}
{/foreach}
<div id="expimageeditor-config" hidden
     data-conf="{json_encode( hash(
        'scripts', hash(
            'raster', array( 'javascript/cropper.min.js'|ezdesign( 'no' ), 'javascript/expimageeditor_raster.js'|ezdesign( 'no' ) ),
            'svg', array( 'javascript/svgcanvas.min.js'|ezdesign( 'no' ), 'javascript/expimageeditor_svg.js'|ezdesign( 'no' ) ),
            'opentype', array( 'javascript/opentype.min.js'|ezdesign( 'no' ) )
        ),
        'outputTypes', ezini( 'EditorSettings', 'OutputMIMETypes', 'expimageeditor.ini' ),
        'quality', ezini( 'EditorSettings', 'Quality', 'expimageeditor.ini' ),
        'newDrawingSize', ezini( 'EditorSettings', 'NewDrawingSize', 'expimageeditor.ini' ),
        'ratios', fetch( 'expimageeditor', 'aspect_ratios' ),
        'watermarks', $expimageeditor_watermarks,
        'fonts', $expimageeditor_fonts,
        'textToPaths', ezini( 'EditorSettings', 'TextToPaths', 'expimageeditor.ini' )|eq( 'enabled' )
     ) )|wash}"
     data-labels="{json_encode( hash(
        'title', 'Edit image: %1'|i18n( 'extension/expimageeditor' ),
        'title_new', 'New drawing'|i18n( 'extension/expimageeditor' ),
        'tools', 'Tools'|i18n( 'extension/expimageeditor' ),
        'save', 'Save'|i18n( 'extension/expimageeditor' ),
        'cancel', 'Cancel'|i18n( 'extension/expimageeditor' ),
        'discard', 'Discard changes'|i18n( 'extension/expimageeditor' ),
        'keep_editing', 'Keep editing'|i18n( 'extension/expimageeditor' ),
        'discard_question', 'Close the editor and discard your changes?'|i18n( 'extension/expimageeditor' ),
        'loading', 'Loading the editor ...'|i18n( 'extension/expimageeditor' ),
        'saving', 'Saving ...'|i18n( 'extension/expimageeditor' ),
        'load_failed', 'The editor could not be loaded.'|i18n( 'extension/expimageeditor' ),
        'save_failed', 'The image could not be saved (%1).'|i18n( 'extension/expimageeditor' ),
        'image_failed', 'The image could not be loaded.'|i18n( 'extension/expimageeditor' ),
        'image_size', '%1 × %2 pixels'|i18n( 'extension/expimageeditor' ),
        'undo', 'Undo'|i18n( 'extension/expimageeditor' ),
        'redo', 'Redo'|i18n( 'extension/expimageeditor' ),
        'zoom_in', 'Zoom in'|i18n( 'extension/expimageeditor' ),
        'zoom_out', 'Zoom out'|i18n( 'extension/expimageeditor' ),
        'zoom_fit', 'Fit to window'|i18n( 'extension/expimageeditor' ),
        'apply', 'Apply'|i18n( 'extension/expimageeditor' ),
        'reset', 'Reset'|i18n( 'extension/expimageeditor' ),
        'applied', '%1 applied.'|i18n( 'extension/expimageeditor' ),
        'undone', 'Undone: %1.'|i18n( 'extension/expimageeditor' ),
        'redone', 'Redone: %1.'|i18n( 'extension/expimageeditor' ),
        'group_history', 'History'|i18n( 'extension/expimageeditor' ),
        'group_view', 'View'|i18n( 'extension/expimageeditor' ),
        'group_transform', 'Crop and turn'|i18n( 'extension/expimageeditor' ),
        'group_adjust', 'Adjust'|i18n( 'extension/expimageeditor' ),
        'group_region', 'Region'|i18n( 'extension/expimageeditor' ),
        'crop', 'Crop'|i18n( 'extension/expimageeditor' ),
        'ratio', 'Aspect ratio'|i18n( 'extension/expimageeditor' ),
        'ratio_free', 'Free'|i18n( 'extension/expimageeditor' ),
        'ratio_original', 'Original'|i18n( 'extension/expimageeditor' ),
        'selection_hint', 'Drag on the image to select a region. The arrow keys move the selection.'|i18n( 'extension/expimageeditor' ),
        'rotate_left', 'Rotate left'|i18n( 'extension/expimageeditor' ),
        'rotate_right', 'Rotate right'|i18n( 'extension/expimageeditor' ),
        'rotate', 'Rotate by an angle'|i18n( 'extension/expimageeditor' ),
        'angle', 'Angle (degrees)'|i18n( 'extension/expimageeditor' ),
        'background', 'Background colour'|i18n( 'extension/expimageeditor' ),
        'flip_h', 'Flip horizontally'|i18n( 'extension/expimageeditor' ),
        'flip_v', 'Flip vertically'|i18n( 'extension/expimageeditor' ),
        'resize', 'Resize'|i18n( 'extension/expimageeditor' ),
        'width', 'Width'|i18n( 'extension/expimageeditor' ),
        'height', 'Height'|i18n( 'extension/expimageeditor' ),
        'keep_ratio', 'Keep proportions'|i18n( 'extension/expimageeditor' ),
        'brightness', 'Brightness'|i18n( 'extension/expimageeditor' ),
        'contrast', 'Contrast'|i18n( 'extension/expimageeditor' ),
        'grayscale', 'Black and white'|i18n( 'extension/expimageeditor' ),
        'effect_hint', 'The image shows the result. Apply makes it a step; another tool drops it.'|i18n( 'extension/expimageeditor' ),
        'sepia', 'Sepia'|i18n( 'extension/expimageeditor' ),
        'pixelate', 'Pixelate region'|i18n( 'extension/expimageeditor' ),
        'blur', 'Blur region'|i18n( 'extension/expimageeditor' ),
        'strength', 'Strength'|i18n( 'extension/expimageeditor' ),
        'whole_image', 'The whole image, no region is selected.'|i18n( 'extension/expimageeditor' ),
        'watermark', 'Watermark'|i18n( 'extension/expimageeditor' ),
        'watermark_text', 'Text'|i18n( 'extension/expimageeditor' ),
        'watermark_image', 'Image'|i18n( 'extension/expimageeditor' ),
        'watermark_none', 'Text only'|i18n( 'extension/expimageeditor' ),
        'opacity', 'Opacity (%)'|i18n( 'extension/expimageeditor' ),
        'colour', 'Colour'|i18n( 'extension/expimageeditor' ),
        'watermark_hint', 'The watermark is placed in the selected region, or in the lower right corner.'|i18n( 'extension/expimageeditor' ),
        'svg_select', 'Select'|i18n( 'extension/expimageeditor' ),
        'svg_rect', 'Rectangle'|i18n( 'extension/expimageeditor' ),
        'svg_ellipse', 'Ellipse'|i18n( 'extension/expimageeditor' ),
        'svg_line', 'Line'|i18n( 'extension/expimageeditor' ),
        'svg_path', 'Path'|i18n( 'extension/expimageeditor' ),
        'svg_freehand', 'Freehand'|i18n( 'extension/expimageeditor' ),
        'svg_text', 'Text'|i18n( 'extension/expimageeditor' ),
        'svg_delete', 'Delete'|i18n( 'extension/expimageeditor' ),
        'svg_duplicate', 'Duplicate'|i18n( 'extension/expimageeditor' ),
        'svg_front', 'Bring to front'|i18n( 'extension/expimageeditor' ),
        'svg_back', 'Send to back'|i18n( 'extension/expimageeditor' ),
        'svg_fill', 'Fill'|i18n( 'extension/expimageeditor' ),
        'svg_no_fill', 'No fill'|i18n( 'extension/expimageeditor' ),
        'svg_stroke', 'Stroke'|i18n( 'extension/expimageeditor' ),
        'svg_stroke_width', 'Stroke width'|i18n( 'extension/expimageeditor' ),
        'svg_opacity', 'Opacity (%)'|i18n( 'extension/expimageeditor' ),
        'svg_font_size', 'Font size'|i18n( 'extension/expimageeditor' ),
        'svg_text_content', 'Text'|i18n( 'extension/expimageeditor' ),
        'svg_group_draw', 'Draw'|i18n( 'extension/expimageeditor' ),
        'svg_group_arrange', 'Arrange'|i18n( 'extension/expimageeditor' ),
        'svg_group_style', 'Style'|i18n( 'extension/expimageeditor' ),
        'svg_nothing_selected', 'Nothing is selected.'|i18n( 'extension/expimageeditor' ),
        'svg_selected', '%1 selected.'|i18n( 'extension/expimageeditor' ),
        'svg_objects', '%1 objects'|i18n( 'extension/expimageeditor' ),
        'svg_object', 'Object'|i18n( 'extension/expimageeditor' ),
        'svg_stage', 'Drawing. Enter adds a shape of the current tool, N selects the next object, the arrow keys move the selection.'|i18n( 'extension/expimageeditor' ),
        'svg_zoom', 'Zoom: %1 %'|i18n( 'extension/expimageeditor' ),
        'font', 'Font'|i18n( 'extension/expimageeditor' ),
        'font_default', 'Default font'|i18n( 'extension/expimageeditor' ),
        'font_failed', 'This font could not be loaded, the default font is used: %1'|i18n( 'extension/expimageeditor' ),
        'svg_text_to_paths', 'Save text in added fonts as paths'|i18n( 'extension/expimageeditor' ),
        'svg_paths_failed', 'The text could not be converted to paths (%1). Save without converting, or try again.'|i18n( 'extension/expimageeditor' )
     ) )|wash}"></div>
{undef $expimageeditor_watermarks $expimageeditor_fonts}
{/run-once}
{/if}
