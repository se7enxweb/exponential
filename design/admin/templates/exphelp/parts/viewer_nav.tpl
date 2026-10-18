{* The navigation tree of the help viewer (exphelp/viewer.tpl): one folder node of expHelpDocumentIndex::tree()
   (name, path = folder below doc/, title, index_id, children[], documents[id, title ...]): its sub-folders as
   <details>, open on the way to the current page, and its pages as links, the current one with aria-current="page".
   Each level is included with a name= namespace, so its loop variables are not overwritten by the level below.
   Variables: nav_node (folder node), nav_current_id (id of the current page), nav_depth (0 for the top level).
   The same file is in design/admin and design/admin4. *}
<ul class="a4-help-nav-list{if $:nav_depth|eq( 0 )} is-top{/if}" role="list">
{foreach $:nav_node.children as $nav_folder}
    {def $nav_open = $:nav_current_id|begins_with( concat( $nav_folder.path, '/' ) )
         $nav_index_id = cond( and( is_set( $nav_folder.index_id ), $nav_folder.index_id ), $nav_folder.index_id, false() )}
    <li class="a4-help-nav-folder">
        <details class="a4-help-nav-details"{if $nav_open} open{/if}>
            <summary class="a4-help-nav-summary"><span class="a4-icon a4-bi-folder" aria-hidden="true"></span><span class="a4-help-nav-text" dir="auto">{$nav_folder.title|wash}</span></summary>
            {if $nav_index_id}
            <a class="a4-help-nav-link is-index" href={concat( 'exphelp/viewer/', $nav_index_id )|ezurl}{if $nav_index_id|eq( $:nav_current_id )} aria-current="page"{/if}><span class="a4-help-nav-text">{'Overview'|i18n( 'kernel/exphelp' )}</span></a>
            {/if}
            {include name=NavLevel uri='design:exphelp/parts/viewer_nav.tpl' nav_node=$nav_folder nav_current_id=$:nav_current_id nav_depth=$:nav_depth|inc}
        </details>
    </li>
    {undef $nav_open $nav_index_id}
{/foreach}
{foreach $:nav_node.documents as $nav_doc}
    <li class="a4-help-nav-page"><a class="a4-help-nav-link" href={concat( 'exphelp/viewer/', $nav_doc.id )|ezurl}{if $nav_doc.id|eq( $:nav_current_id )} aria-current="page"{/if}><span class="a4-help-nav-text" dir="auto">{$nav_doc.title|wash}</span></a></li>
{/foreach}
</ul>
