{* The table of contents of a help page (exphelp/viewer.tpl): its h2 and h3 headings as links to their anchors.
   The copy in the side column carries data-exphelp-toc, so exphelp.js marks the heading in view (aria-current=
   "location", .is-current); the folded copy above the page on narrow windows does not.
   Variables: toc_items (level, text, anchor), toc_id, toc_spy (bool).
   The same file is in design/admin and design/admin4. *}
<nav class="a4-help-toc" id="{$toc_id|wash}" aria-label="{'On this page'|i18n( 'kernel/exphelp' )|wash}"{if $toc_spy} data-exphelp-toc{/if}>
    <ol class="a4-help-toc-list" role="list">
    {foreach $toc_items as $toc_item}
        <li class="a4-help-toc-item is-l{$toc_item.level|wash}"><a class="a4-help-toc-link" href="#{$toc_item.anchor|wash}" dir="auto">{$toc_item.text|wash}</a></li>
    {/foreach}
    </ol>
</nav>
