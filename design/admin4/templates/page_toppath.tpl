{let name=Path
     use_urlalias=ezini('URLTranslator','Translation')|eq('enabled')
     crumb_count=$module_result.path|count}
{* The breadcrumb: an ordered list inside #path (the navigation landmark of pagelayout.tpl). The separators come
   from the stylesheet (.a4-crumbs), the last step carries aria-current. Names are no longer shortened to 18
   characters: a long name ends in an ellipsis and keeps its full name as title. On narrow windows the middle
   steps fold into the "..." step (.is-fold). *}
<p class="a4-sr-only">{'You are here:'|i18n( 'design/admin/pagelayout/path' )}</p>
<ol class="a4-crumbs">
{section loop=$module_result.path}
    <li class="a4-crumb{if and( gt( $:number, 1 ), lt( $:number, $:crumb_count ) )} is-middle{/if}">
    {if $:item.url}
        {if ne($ui_context,'edit')}
        <a class="path" href={cond( and( $:use_urlalias, is_set( $:item.url_alias ) ), $:item.url_alias,
                                    $:item.url )|ezurl} title="{$:item.text|wash}"{if eq( $:number, $:crumb_count )} aria-current="page"{/if}>{$:item.text|wash}</a>
        {else}
        <span class="disabled" title="{$:item.text|wash}">{$:item.text|wash}</span>
        {/if}
    {else}
        <span class="path" title="{$:item.text|wash}"{if eq( $:number, $:crumb_count )} aria-current="page"{/if}>{$:item.text|wash}</span>
    {/if}
    </li>
    {if and( eq( $:number, 1 ), gt( $:crumb_count, 3 ) )}
    <li class="a4-crumb is-fold" aria-hidden="true"><span>&hellip;</span></li>
    {/if}
{/section}
</ol>
{/let}
