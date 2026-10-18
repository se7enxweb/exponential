{* The facts under the title of a help page (exphelp/viewer.tpl): who it is for (the audience pills of the dashboard),
   its category and type, the source file in the repository, when it last changed, about how long it takes to read,
   and the link that opens the file on GitHub to edit it.
   Variables: meta_page (document), meta_audiences (list of editor|admin|developer|user), meta_edit_url (may be ''),
   meta_category (id, name, icon of the page's category, or false).
   The same file is in design/admin and design/admin4. *}
{def $meta_minutes = cond( is_set( $meta_page.size ), $meta_page.size|div( 1200 )|ceil, 0 )
     $meta_changed = cond( is_set( $meta_page.changed ), $meta_page.changed, cond( is_set( $meta_page.mtime ), $meta_page.mtime, 0 ) )
     $meta_cat_name = ''
     $meta_cat_icon = ''}
{if $meta_category}{if is_set( $meta_category.name )}{set $meta_cat_name = $meta_category.name}{/if}{if is_set( $meta_category.icon )}{set $meta_cat_icon = $meta_category.icon}{/if}{/if}
<ul class="a4-help-meta" role="list">
    {if $meta_audiences|count}
    <li class="a4-help-meta-item is-audience">
        <span class="a4-sr-only">{'For:'|i18n( 'kernel/exphelp' )}</span>
        {foreach $meta_audiences as $meta_audience}
        <span class="a4-tag a4-help-pill is-{$meta_audience|wash}">{switch match=$meta_audience}{case match='editor'}{'Editors'|i18n( 'kernel/exphelp' )}{/case}{case match='admin'}{'Administrators'|i18n( 'kernel/exphelp' )}{/case}{case match='developer'}{'Developers'|i18n( 'kernel/exphelp' )}{/case}{case match='user'}{'Users'|i18n( 'kernel/exphelp' )}{/case}{case}{$meta_audience|wash}{/case}{/switch}</span>
        {/foreach}
    </li>
    {/if}
    {if and( is_set( $meta_page.category ), $meta_page.category|ne( '' ) )}
    <li class="a4-help-meta-item"><span class="a4-tag">{if $meta_cat_icon|ne( '' )}<span class="a4-icon a4-bi-{$meta_cat_icon|wash}" aria-hidden="true"></span> {/if}<span class="a4-sr-only">{'Category:'|i18n( 'kernel/exphelp' )} </span>{cond( $meta_cat_name|ne( '' ), $meta_cat_name, $meta_page.category )|wash}</span></li>
    {/if}
    {if and( is_set( $meta_page.path ), $meta_page.path|ne( '' ) )}
    <li class="a4-help-meta-item"><span class="a4-icon a4-bi-file-earmark-code" aria-hidden="true"></span><span class="a4-sr-only">{'Source file:'|i18n( 'kernel/exphelp' )} </span><code class="a4-help-meta-path" dir="ltr">{$meta_page.path|wash}</code></li>
    {/if}
    {if $meta_changed|gt( 0 )}
    <li class="a4-help-meta-item"><span class="a4-icon a4-bi-clock-history" aria-hidden="true"></span>{'Last changed'|i18n( 'kernel/exphelp' )} <time datetime="{$meta_changed|datetime( 'custom', '%Y-%m-%d' )}">{$meta_changed|l10n( 'shortdate' )}</time></li>
    {/if}
    {if $meta_minutes|gt( 0 )}
    <li class="a4-help-meta-item"><span class="a4-icon a4-bi-hourglass-split" aria-hidden="true"></span>{'%count min read'|i18n( 'kernel/exphelp',, hash( '%count', $meta_minutes ) )|wash}</li>
    {/if}
    {if $meta_edit_url|ne( '' )}
    <li class="a4-help-meta-item is-edit"><a class="a4-btn a4-btn-small a4-btn-ghost" href="{$meta_edit_url|wash}" rel="noopener noreferrer" target="_blank"><span class="a4-icon a4-bi-git" aria-hidden="true"></span> {'Edit on GitHub'|i18n( 'kernel/exphelp' )}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( 'kernel/exphelp' )}</span></a></li>
    {/if}
</ul>
{undef $meta_minutes $meta_changed $meta_cat_name $meta_cat_icon}
