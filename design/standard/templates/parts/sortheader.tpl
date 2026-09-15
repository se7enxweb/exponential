{* One column heading that sorts the list it belongs to.

   Shared: the rss list and the locations tab of an item both use it, so the
   headings look and behave the same in both places rather than each page
   growing its own.

   The sorting is done by the database, not in the browser: the list is fetched
   a page at a time, and reordering the twenty five rows on screen would only
   shuffle the page you are already looking at. The heading is a plain link, so
   it works with javascript switched off; the script on the page only widens the
   click target to the whole cell. *}

{* sort is hash( 'field', <the column sorted now>,
                 'direction', 'asc'|'desc',
                 'opposite', the other one ).
   suffix is appended to the address as it is, for a page that has to keep view
   parameters of its own - the locations tab keeps (tab)/locations that way. *}
{default key=''
         label=''
         sort=false()
         page_uri='/rss/list'
         sort_name='sort'
         dir_name='dir'
         suffix=''
         cell_class=''}

{let sorted=eq( $sort.field, $key )}
{* Clicking the column already sorted turns it round; any other column starts
   ascending. *}
<th class="sortable{if $:sorted} sorted sorted-{$sort.direction}{/if}{if ne( $cell_class, '' )} {$cell_class}{/if}"{if $:sorted} aria-sort="{if eq( $sort.direction, 'asc' )}ascending{else}descending{/if}"{/if}><a
    href={concat( $page_uri, '/(', $sort_name, ')/', $key,
                  '/(', $dir_name, ')/', $:sorted|choose( 'asc', $sort.opposite ),
                  $suffix )|ezurl}
    class="sort-link"
    title="{'Sort by %column'|i18n( 'design/admin/parts/sortheader',, hash( '%column', $label ) )}">{$label}<span
    class="sort-arrow">{if $:sorted}{if eq( $sort.direction, 'asc' )}&#9650;{else}&#9660;{/if}{/if}</span></a></th>
{/let}
