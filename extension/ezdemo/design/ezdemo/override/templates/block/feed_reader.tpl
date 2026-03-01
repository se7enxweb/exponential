{cache-block expiry=300 keys=array($block.custom_attributes.source, $block.custom_attributes.limit, $block.custom_attributes.offset)}

{def $source = $block.custom_attributes.source
     $limit = $block.custom_attributes.limit
     $offset = $block.custom_attributes.offset
     $res = feedreader( $source, $limit, $offset )}

<div class="block-type-feed-reader item-list">
    <div class="attribute-header">
        <h2>
            <a href="{$res.links[0]}" title="{$res.title|wash()}">{$res.title|wash()}</a>
        </h2>
    </div>
    <article>
        <ul>
        {foreach $res.items as $item}
            <li>
                <a href="{$item.links[0]}" title="{$item.title|wash()}">{$item.title|wash()}</a>
            </li>
        {/foreach}
        </ul>
    </article>
</div>
{/cache-block}
