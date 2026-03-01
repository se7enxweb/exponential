{def $image_path = $block.custom_attributes.path
     $has_url = and( is_set( $block.custom_attributes.url ), ne( $block.custom_attributes.url, '' ) )
     $url = $block.custom_attributes.url}

<div class="block-type-banner-external">
    {if $image_path}
        {if $has_url}<a href="{$url}" title="{$block.name|wash}">{/if}<img src={$image_path} alt="{$block.name|wash}" />{if $has_url}</a>{/if}
    {/if}
</div>
{undef $image_path $has_url $url}
