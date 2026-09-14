{* A sortable column heading inside a form.

   The list page sorts with links; this one cannot, because it sits inside the
   edit form and a link would leave the page taking everything typed into it
   along. The button carries the column and the direction in one value, so no
   javascript is needed to send them together. *}

{default key='' label='' sort=false() cell_class=''}

{let sorted=eq( $sort.field, $key )}
<th class="sortable{if $:sorted} sorted sorted-{$sort.direction}{/if}{if ne( $cell_class, '' )} {$cell_class}{/if}"{if $:sorted} aria-sort="{if eq( $sort.direction, 'asc' )}ascending{else}descending{/if}"{/if}><button
    type="submit" class="sort-button" name="FeedBrowserSetSort"
    value="{$key}|{$:sorted|choose( 'asc', $sort.opposite )}"
    title="{'Sort by %column'|i18n( 'design/admin/rss/edit_export',, hash( '%column', $label ) )}">{$label}<span
    class="sort-arrow">{if $:sorted}{if eq( $sort.direction, 'asc' )}&#9650;{else}&#9660;{/if}{/if}</span></button></th>
{/let}
