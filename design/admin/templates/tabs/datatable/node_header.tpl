{* The node view's "Data tables" tab (admininterface.ini [AdditionalTab_datatable]): shown to a user with
   datatable/read when a row of a data table of an extension or of a named connection points at this node or its
   object. The module name is a variable so a server process started before the class existed shows no tab. *}
{def $datatable_tab_module = 'datatable'}
{if fetch( $datatable_tab_module, 'can', hash( 'function', 'read' ) )}
{def $datatable_tab_found = fetch( $datatable_tab_module, 'pointing_at', hash( 'object_id', $node.contentobject_id, 'node_id', $node.node_id ) )}
{if $datatable_tab_found|count}
    <li id="node-tab-datatable" class="{if $last}last{else}middle{/if}{if $node_tab_index|eq( 'datatable' )} selected{/if}">
        {if $tabs_disabled}
            <span class="disabled">{'Data tables'|i18n( 'design/admin/datatable' )}</span>
        {else}
            <a href={concat( $node_url_alias, '/(tab)/datatable' )|ezurl} title="{'Rows of data tables that point at this item'|i18n( 'design/admin/datatable' )|wash}">{'Data tables'|i18n( 'design/admin/datatable' )} ({$datatable_tab_found|count})</a>
        {/if}
    </li>
{/if}
{undef $datatable_tab_found}
{/if}
{undef $datatable_tab_module}
