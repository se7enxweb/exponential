{* The node view's "Audit" tab (admininterface.ini [AdditionalTab_audit]), only for a user who may read the content
   channel of the audit. The module name is a variable so the fetch is not compiled into a direct class call (a
   server process started before the audit classes existed shows no tab instead of an error). *}
{def $audit_tab_module = 'audit'}
{if fetch( $audit_tab_module, 'can_read', hash( 'channel', 'content' ) )}
    <li id="node-tab-audit" class="{if $last}last{else}middle{/if}{if $node_tab_index|eq( 'audit' )} selected{/if}">
        {if $tabs_disabled}
            <span class="disabled">{'Audit'|i18n( 'design/admin/audit' )}</span>
        {else}
            <a href={concat( $node_url_alias, '/(tab)/audit' )|ezurl} title="{'The audit trail of this node'|i18n( 'design/admin/audit' )|wash}">{'Audit'|i18n( 'design/admin/audit' )}</a>
        {/if}
    </li>
{/if}
{undef $audit_tab_module}
