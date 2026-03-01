{* Product - Line view *}

<div class="content-view-line">
    <div class="class-product float-break">

        <h2><a href={$node.url_alias|ezurl}>{$node.name|wash()}</a></h2>

        {if $node.data_map.images.has_content}
{if $node.data_map.images.content.relation_list}
<div class="attribute-image">
{* $node.data_map.images.content.relation_list.0|attribute(show,1)}
{$node.data_map.images.object|attribute(show,1) *}
            {foreach $node.data_map.images.content.relation_list as $item sequence array( 'bglight', 'bgdark' ) as $style}
              {def $object = fetch( content, object, hash( object_id, $item.contentobject_id ) )}
              {*$object.data_map|attribute(show,1)*}
              {attribute_view_gui image_class=large attribute=$object.data_map.image}
              {undef $object}
            {/foreach}
{/if}
            {if $node.data_map.caption.has_content}
            <div class="caption">
                {attribute_view_gui attribute=$node.data_map.caption}
            </div>
            {/if}
        </div>
        {else}
        {if $node.data_map.image.has_content}
        <div class="attribute-image">
            {attribute_view_gui image_class=large attribute=$node.data_map.image}
            {if $node.data_map.caption.has_content}
            <div class="caption">
                {attribute_view_gui attribute=$node.data_map.caption}
            </div>
            {/if}
        </div>
        {/if}
        {/if}

    {*section show=$node.data_map.image.content}
        <div class="attribute-image">
            {attribute_view_gui href=$node.url_alias|ezurl image_class=medium attribute=$node.data_map.image}
        </div>
    {/section *}

        {if $node.data_map.rating.has_content}
        <div class="attribute-short">
           {attribute_view_gui attribute=$node.object.data_map.rating}
        </div>
        {/if}

        <div class="attribute-short">
           {attribute_view_gui attribute=$node.object.data_map.short_description}
        </div>

        {if $node.data_map.product_size.has_content}
        <div class="attribute-product_size">
          <p>
           Size: {attribute_view_gui attribute=$node.object.data_map.product_size}
          </p>
        </div>
        {/if}

        <div class="attribute-price">
          <p>
           {attribute_view_gui attribute=$node.object.data_map.price}
          </p>
{*          <p class="ex-vat">(price ex. vat {$node.object.data_map.price.content.ex_vat_price})</p> *}
        </div>

        <form method="post" action={"content/action"|ezurl}>
        <div class="content-action">
	{if and( is_set( $node.data_map.product_inventory_count ), $node.data_map.product_inventory_count.content|le(0) )}<div class="warning-out-of-stock">Out of stock!</div>{/if}
            <input {if and( is_set( $node.data_map.product_inventory_count ), $node.data_map.product_inventory_count.content|le(0) )}disabled{/if} type="submit" class="defaultbutton" name="ActionAddToBasket" value="{"Add to basket"|i18n("design/ezwebin/full/product")}" />
{*            <input class="button" type="submit" name="ActionAddToWishList" value="{"Add to wish list"|i18n("design/ezwebin/full/product")}" />*}
            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
            <input type="hidden" name="ViewMode" value="full" />
        </div>
        </form>

   </div>
</div>
