{* Product - Line view *}

{def $content_size = '8'}

<div class="content-view-line">
    <article class="class-product row">

    {if $node.data_map.image.content}
        <div class="span2">
            <div class="attribute-image">
                {attribute_view_gui href=$node.url_alias|ezurl image_class=productthumbnail attribute=$node.data_map.image}
            </div>
        </div>
        {set $content_size = '6'}
    {/if}

        <div class="span{$content_size}">
            <div class="attribute-header with-product-number">
                <h2>
                    <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link" title="{$node.name|wash()}">{$node.name|wash()}</a>
                </h2>
                <div class="product-number">
                    {attribute_view_gui attribute=$node.data_map.product_number}
                </div>
            </div>

            <div class="attribute-short">
               {attribute_view_gui attribute=$node.data_map.short_description}
            </div>

            <form method="post" action={"content/action"|ezurl}>
                <fieldset class="row">
                    <div class="span{$content_size|div( '2' )}">
                        {attribute_view_gui attribute=$node.data_map.additional_options}
                    </div>
                    <div class="span{$content_size|div( '2' )}">
                        <div class="item-price">
                            {attribute_view_gui attribute=$node.data_map.price}
                        </div>
                        <div class="item-buying-action form-inline">
                            <label>
                                <span class="hidden">{'Amount'|i18n("design/ezdemo/line/product")}</span>
                                <input class="span1" type="text" name="Quantity" />
                            </label>
                            <button class="btn btn-warning" type="submit" name="ActionAddToBasket">
                                {'Buy'|i18n("design/ezdemo/line/product")}
                            </button>
                        </div>
                    </div>
                </fieldset>
                <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                <input type="hidden" name="ViewMode" value="full" />
            </form>
        </div>

   </article>
</div>

{undef $content_size}
