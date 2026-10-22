{* One header item of an extension (design.ini [AdminHeaderSettings] HeaderItems[], page_topmenu.tpl): a round 36px
   tool as Help and the site menu, with a count on its corner and a panel of entries.

   Input:
     $item_id     a short id, unique in the header (the ids are a4-hi-<id>-...)
     $item_label  the name: "Notifications"; the button's name is this, or the feed's label ("Notifications: 3 new")
     $item_icon   an icon of design.ini [AdminDesignSettings] IconList[] (bell)
     $item_url    the page the item stands for: the link without the script, and the panel's last entry
     $item_all    the name of that last entry ("All notifications")
     $item_feed   optional: the address of a JSON feed { "count": 3, "label": "Notifications: 3 new",
                  "items": [ { "text": "...", "url": "...", "meta": "..." } ] } (or the same in "content", as an
                  ezjscore call answers); admin4.js loads it when the page is drawn and each time the panel opens
     $item_empty  the text of an empty panel ("Nothing new")
     $item_count  optional: the count when the page was drawn (only for an item outside a cache block)

   Without the script the link stays and opens the page; with it the button takes its place (as Help and the site
   menu do): aria-expanded and aria-controls, Arrow keys, Home, End and Escape. The count is in the button's name, not
   only drawn: a screen reader hears "Notifications: 3 new". Entries are made with textContent, never as HTML. *}
{def $a4_hi_id = concat( 'a4-hi-', $item_id|wash )
     $a4_hi_count = first_set( $item_count, 0 )|int
     $a4_hi_feed = first_set( $item_feed, '' )}
<div class="header-item a4-header-item" id="{$a4_hi_id}">
    <a class="a4-header-btn a4-header-item-link" href={$item_url|ezurl} title="{$item_label|wash}" aria-label="{$item_label|wash}">
        <span class="a4-icon a4-bi-{$item_icon|wash}" aria-hidden="true"></span>
    </a>
    <div class="a4-site-menu-wrap a4-header-item-wrap" hidden>
        <button type="button" class="a4-header-btn a4-header-item-toggle" aria-expanded="false" aria-controls="{$a4_hi_id}-panel" title="{$item_label|wash}" aria-label="{$item_label|wash}" data-a4-header-label="{$item_label|wash}"{if ne( $a4_hi_feed, '' )} data-a4-header-feed="{$a4_hi_feed|wash}"{/if}>
            <span class="a4-icon a4-bi-{$item_icon|wash}" aria-hidden="true"></span>
            <span class="a4-header-count" aria-hidden="true"{if eq( $a4_hi_count, 0 )} hidden{/if}>{if gt( $a4_hi_count, 99 )}99+{else}{$a4_hi_count}{/if}</span>
        </button>
        <div id="{$a4_hi_id}-panel" class="a4-site-menu a4-header-panel" hidden>
            <p class="a4-site-menu-heading a4-header-panel-heading">{$item_label|wash}</p>
            <ul class="a4-site-menu-list a4-header-panel-list" aria-label="{$item_label|wash}"></ul>
            <p class="a4-header-panel-empty">{first_set( $item_empty, 'Nothing new'|i18n( 'design/admin/pagelayout' ) )|wash}</p>
            <a class="a4-site-menu-front a4-header-panel-all" href={$item_url|ezurl}><span class="a4-site-menu-name">{first_set( $item_all, $item_label )|wash}</span></a>
        </div>
    </div>
</div>
{undef $a4_hi_id $a4_hi_count $a4_hi_feed}
