{def    $image_variation=ezini( 'LightboxSettings' , 'FullImageSize' , 'lightbox.ini' )}

<a href={$object.data_map.image.content[$image_variation].full_path|ezroot( 'double' , 'full' )} rel="lightbox" title="{if $object.data_map.caption.has_content}{$object.data_map.caption.content.output.output_text|strip_tags|trim}{else}{$object.name|wash}{/if}">{attribute_view_gui attribute=$object.data_map.image image_class=$object_parameters.size}</a>
