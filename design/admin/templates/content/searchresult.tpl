{* The result table of the search page (content/search.tpl): type, name with the location and the words found,
   modified, content type, translations and the edit button; a checkbox per row for "Edit selected" (the multi
   item editor).

   No <form> here on purpose. This template is included inside the search form, and a form inside a form is
   dropped by every browser. So "Edit selected" (page/content_searchresult.js) builds a form of its own and submits
   that, carrying the ticked node ids and the request token. The checkboxes have no name: the search form does not
   send them.

   $search_text: the words searched for, for the pages of a PDF that hold them (eZ Find with ezfindpdfdeepsearch).
   The same file is in design/admin and design/admin4 (design/standard has a front-end one of this name). *}
{def $result_query = first_set( $search_text, '' )
     $result_page_hits = and( $result_query|trim|ne( '' ), ezini( 'SearchSettings', 'SearchEngine' )|eq( 'ezsolr' ), ezini( 'ExtensionSettings', 'ActiveExtensions' )|contains( 'ezfindpdfdeepsearch' ) )}

{if $search_result}
<div class="a4-table-wrap">
<table class="a4-table is-compact a4-search-table">
<thead>
<tr>
    <th scope="col" class="a4-search-select"><input aria-label="{'Select every result on this page.'|i18n( 'design/admin/content/search' )|wash}" type="checkbox" id="ezsearch-select-all" title="{'Select every result on this page.'|i18n( 'design/admin/content/search' )|wash}" /></th>
    <th scope="col" class="a4-search-type"><span class="a4-sr-only">{'Type'|i18n( 'design/admin/content/search' )}</span></th>
    <th scope="col">{'Name'|i18n( 'design/admin/content/search' )}</th>
    <th scope="col">{'Modified'|i18n( 'design/admin/content/search' )}</th>
    <th scope="col">{'Content type'|i18n( 'design/admin/content/search' )}</th>
    <th scope="col">{'Translations'|i18n( 'design/admin/content/search' )}</th>
    <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/content/search' )}</span></th>
</tr>
</thead>
<tbody>
{foreach $search_result as $result}
{def $result_object = $result.object
     $result_language = first_set( $result.language_code, $result_object.current_language )}
<tr>
<td class="a4-search-select">
    <input aria-label="{'Select this item for editing.'|i18n( 'design/admin/content/search' )|wash} {$result.name|wash}" type="checkbox" class="ezsearch-select" value="{$result.node_id}" title="{'Select this item for editing.'|i18n( 'design/admin/content/search' )|wash}" />
</td>
<td class="a4-search-type">{$result.class_identifier|class_icon( small, $result.class_name|wash )}</td>
<td class="a4-search-name">
<a href={$result.url_alias|ezurl} dir="auto">{$result.name|wash}</a>
{* Where it is: objects of the same name are told apart by their location *}
{if $result.depth|gt( 2 )}
<div class="a4-meta a4-search-path">{foreach $result.path as $path_node}<bdi>{$path_node.name|wash}</bdi>{delimiter} / {/delimiter}{/foreach}</div>
{/if}
{* eZ Find's highlight (Solr sizes it, [HighLighting] FragmentSize; no shorten here, it could cut a mark in half): the text washed, only the marks of the words found (Solr's <em>) put back, as <mark> *}
{if is_set( $result.highlight )}{if $result.highlight|trim|ne( '' )}
<div class="a4-meta a4-search-snippet">{$result.highlight|wash|explode( '&lt;em&gt;' )|implode( '<mark>' )|explode( '&lt;/em&gt;' )|implode( '</mark>' )}</div>
{/if}{/if}
{* the pages of a PDF that hold the words, with a link to each page (only with ezfindpdfdeepsearch) *}
{if and( $result_page_hits, $result.class_identifier|eq( 'file' ) )}
{include uri='design:ezfindpdfdeepsearch/child_hits.tpl' object_id=$result.contentobject_id query=$result_query is_file=true()}
{/if}
</td>
<td class="a4-search-date"><time datetime="{$result_object.modified|datetime( 'custom', '%Y-%m-%dT%H:%i' )}">{$result_object.modified|l10n( shortdatetime )}</time></td>
<td>{$result.class_name|wash}</td>
<td class="a4-search-languages">
    <a href={concat( '/content/view/full/', $result.node_id, '/(language)/', $result_language )|ezurl} lang="{$result_language|wash}" title="{$result_language|wash}">{$result_language|wash}</a>
    {foreach $result_object.languages as $translation}{if ne( $translation.locale, $result_language )}
    <a class="a4-meta" href={concat( '/content/view/full/', $result.node_id, '/(language)/', $translation.locale )|ezurl} title="{$translation.name|wash}">{$translation.locale|wash}</a>
    {/if}{/foreach}
</td>
<td class="a4-search-actions">
    {if $result_object.can_edit}
    <a class="a4-btn a4-btn-small" href={concat( '/content/edit/', $result.contentobject_id, '/f/', $result_language )|ezurl} title="{'Edit %name'|i18n( 'design/admin/content/search',, hash( '%name', $result.name ) )|wash}"><span class="a4-icon a4-bi-pencil" aria-hidden="true"></span> {'Edit'|i18n( 'design/admin/content/search' )}</a>
    {/if}
</td>
</tr>
{undef $result_object $result_language}
{/foreach}
</tbody>
</table>
</div>

<div class="a4-actions a4-search-selection">
    {* data-multiedit-url: built with ezurl so it carries this installation's siteaccess and index file. *}
    <input class="a4-btn a4-btn-small" type="button" id="ezsearch-multiedit" data-multiedit-url="{'content/multiedit'|ezurl('no')|wash}" value="{'Edit selected'|i18n( 'design/admin/content/search' )}" title="{'Edit every ticked result in one form.'|i18n( 'design/admin/content/search' )|wash}" />
</div>

{ezscript( array( 'page/content_searchresult.js' ) )}
{/if}
{undef $result_query $result_page_hits}
