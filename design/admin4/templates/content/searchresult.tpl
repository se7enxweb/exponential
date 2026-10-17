{let use_url_translation=ezini( 'URLTranslator', 'Translation' )|eq( 'enabled' )}

{section show=$search_result}
{* Results are selectable so that a search can feed the multi item editor: pick
   what you want out of the whole site, then edit it in one form.

   No <form> here on purpose. This template is included inside the search form,
   and a form inside a form is dropped by every browser - the inner one simply
   does not exist, and its button posts to the outer action instead. So the
   button builds a form of its own and submits that, carrying the ticked node
   ids and the request token. *}
<table class="list a4-search-results" cellspacing="0" role="table">
<tr role="row">
    <th scope="col" class="tight" role="columnheader"><input aria-label="{'Select every result on this page.'|i18n( 'design/admin/content/search' )|wash}" type="checkbox" id="ezsearch-select-all" title="{'Select every result on this page.'|i18n( 'design/admin/content/search' )|wash}" /></th>
    <th scope="col" role="columnheader">{'Name'|i18n( 'design/admin/content/search' )}</th>
    <th scope="col" role="columnheader">{'Type'|i18n( 'design/admin/content/search' )}</th>
</tr>

{section var=SearchResult loop=$search_result sequence=array( bglight, bgdark )}
<tr class="{$SearchResult.sequence}" role="row">
<td class="tight" role="cell">
    <input aria-label="{'Select this item for editing.'|i18n( 'design/admin/content/search' )|wash}" type="checkbox" class="ezsearch-select" value="{$SearchResult.item.node_id}" title="{'Select this item for editing.'|i18n( 'design/admin/content/search' )|wash}" />
</td>
<td role="cell">
{node_view_gui view=line content_node=$SearchResult.item}
{* Where it is: objects of the same name are told apart by their location (.a4-search-path: under the name, breaking anywhere on a phone) *}
{if $SearchResult.item.depth|gt( 2 )}
<div class="a4-meta a4-search-path">{foreach $SearchResult.item.path as $path_node}<bdi>{$path_node.name|wash}</bdi>{delimiter} / {/delimiter}{/foreach}</div>
{/if}
</td>
<td role="cell">
{$SearchResult.item.class_name|wash}
</td>
</tr>
{/section}

</table>

<div class="block">
    {* data-multiedit-url: built with ezurl so it carries this installation's siteaccess and index file. *}
    <input class="button" type="button" id="ezsearch-multiedit" data-multiedit-url="{'content/multiedit'|ezurl('no')|wash}" value="{'Edit selected'|i18n( 'design/admin/content/search' )}" title="{'Edit every ticked result in one form.'|i18n( 'design/admin/content/search' )|wash}" />
</div>

{ezscript( array( 'page/content_searchresult.js' ) )}
{/section}

{/let}
