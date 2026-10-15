{*
   The trash link under a tree menu (parts/content, parts/media, parts/user): the same icon, size and text in every
   module. Not shown while browsing. One markup for every module: include this template, do not copy it.
*}
{if ne( $ui_context, 'browse' )}
<div id="trash">
<a class="image-text" href={concat( '/content/trash/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="{'View and manage the contents of the trash bin.'|i18n( 'design/admin/parts/content/menu' )|wash}"><span class="a4-icon a4-bi-trash" aria-hidden="true"></span><span>{'Trash'|i18n( 'design/admin/parts/content/menu' )}</span></a>
</div>
{/if}
