<div class="zone-layout-{$zone_layout|downcase()} row">
    <div class="span8">
        <section class="content-view-block">
            {if and( is_set( $zones[0].blocks ), $zones[0].blocks|count() )}
            {foreach $zones[0].blocks as $block}
                {include uri='design:parts/zone_block.tpl' zone=$zones[0]}
            {/foreach}
            {/if}
        </section>
    </div>
    <div class="span4">
        <aside>
            <section class="content-view-block content-view-aside">
                {if and( is_set( $zones[1].blocks ), $zones[1].blocks|count() )}
                {foreach $zones[1].blocks as $block}
                    {include uri='design:parts/zone_block.tpl' zone=$zones[1]}
                {/foreach}
                {/if}
            </section>
        </aside>
    </div>
</div>
