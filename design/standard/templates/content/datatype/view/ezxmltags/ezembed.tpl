{* Imported Ibexa docbook embed: embed_image() resolves the nexus-id href
   (+776/+554 remap) to the embedded image; renders the reference embed view. *}
{def $eb_img = embed_image(first_set($href, ''), 'i1320')}
{if $eb_img}
<div class="ez-embed-type-image">
    <div class="view-type view-type-embed image">
        <figure class="image-wrapper">
            <img
            src={$eb_img.url|ezroot}
            loading="lazy"
            alt="{$eb_img.alt|wash}"
            class="ibexa_image-field" />
        </figure>
    </div>
</div>
{/if}
{undef $eb_img}
