{* The items I follow: the subtree notifications, filtered and paged, with a confirmation before anything is removed.
   Form fields as before: NewRule_ezsubtree, RemoveRule_ezsubtree, SelectedRuleIDArray_ezsubtree[]; UseConfirm asks first. *}
{def $base = 'notification/settings'
     $q = first_set( $filter_query, '' )
     $cls = first_set( $filter_class, '' )}
<section class="nf-card" id="nf-follow">
    <h2>{'Items I follow'|i18n( 'design/admin/notification/settings' )} <span class="nf-tag">{$subscription_all_total}</span></h2>
    <p class="nf-lead">{'You get an e-mail when something is published below one of these items, if you may read it. Open any item in the content structure and choose Notify me to add more.'|i18n( 'design/admin/notification/settings' )}</p>

{if $confirm_remove}
    <form class="nf-confirm" method="post" action={$base|ezurl} role="alertdialog" aria-labelledby="nf-confirm-title">
        <h2 id="nf-confirm-title">{'Remove %count items from your notifications?'|i18n( 'design/admin/notification/settings',, hash( '%count', $confirm_remove|count ) )}</h2>
        <ul>
    {foreach $confirm_remove as $row}
            <li>{if $row.missing}{'Content that no longer exists'|i18n( 'design/admin/notification/settings' )} (#{$row.node_id}){else}{$row.name|wash}{/if}
                <input type="hidden" name="SelectedRuleIDArray_{$handler.id_string}[]" value="{$row.id}" /></li>
    {/foreach}
        </ul>
        <p class="nf-hint">{'You stop getting e-mail about them. The items themselves are not changed.'|i18n( 'design/admin/notification/settings' )}</p>
        <input type="hidden" name="RemoveRule_{$handler.id_string}" value="1" />
        <input type="hidden" name="UseConfirm" value="1" />
        <div class="nf-actions">
            <input class="nf-btn danger" type="submit" name="ConfirmRemoveRule" value="{'Remove'|i18n( 'design/admin/notification/settings' )}" />
            <input class="nf-btn" type="submit" name="CancelRemoveRule" value="{'Cancel'|i18n( 'design/admin/notification/settings' )}" />
        </div>
    </form>
{/if}

{if or( $subscription_all_total|gt( 0 ), $q|ne( '' ) )}
    <div class="nf-filters">
        <form method="post" action={$base|ezurl}>
            <input type="text" name="Query" value="{$q|wash}" maxlength="100" placeholder="{'Name contains'|i18n( 'design/admin/notification/settings' )|wash}" aria-label="{'Name contains'|i18n( 'design/admin/notification/settings' )|wash}" />
    {if $subscription_classes|count|gt( 1 )}
            <select name="ClassFilter" aria-label="{'Type'|i18n( 'design/admin/notification/settings' )|wash}">
                <option value="">{'Any type'|i18n( 'design/admin/notification/settings' )}</option>
        {foreach $subscription_classes as $identifier => $name}
                <option value="{$identifier|wash}"{if $cls|eq( $identifier )} selected="selected"{/if}>{$name|wash}</option>
        {/foreach}
            </select>
    {/if}
            <input class="nf-btn" type="submit" name="FilterSubscriptions" value="{'Filter'|i18n( 'design/admin/notification/settings' )}" />
    {if or( $q|ne( '' ), $cls|ne( '' ) )}
            <input class="nf-btn" type="submit" name="ClearFilter" value="{'Show all'|i18n( 'design/admin/notification/settings' )}" />
    {/if}
        </form>
    </div>
{/if}

    <form method="post" action={$base|ezurl} id="nf-rules">
        <input type="hidden" name="UseConfirm" value="1" />
{if $subscriptions|count|gt( 0 )}
        <label class="nf-selectall"><input type="checkbox" onclick="var b=document.querySelectorAll('#nf-rules .nf-pick');for(var i=0;i&lt;b.length;i++)b[i].checked=this.checked;" /> {'Select all on this page'|i18n( 'design/admin/notification/settings' )}</label>
        <ul class="nf-list">
    {foreach $subscriptions as $row}
            <li class="nf-row{if $row.missing} gone{/if}">
                <input class="nf-pick" type="checkbox" name="SelectedRuleIDArray_{$handler.id_string}[]" value="{$row.id}" aria-label="{'Select item for removal.'|i18n( 'design/admin/notification/settings' )|wash}" />
                <div>
        {if $row.missing}
                    <div class="nf-title"><span class="nf-badge warn">{'Content no longer exists'|i18n( 'design/admin/notification/settings' )}</span> #{$row.node_id}</div>
                    <div class="nf-meta"><span>{'Nothing can be sent for this item. Remove it.'|i18n( 'design/admin/notification/settings' )}</span></div>
        {else}
                    <div class="nf-title"><a href={concat( 'content/view/full/', $row.node_id )|ezurl}>{$row.name|wash}</a> <span class="nf-badge">{$row.class_name|wash}</span></div>
                    <div class="nf-meta">
            {if $row.path|count|gt( 0 )}<span>{'In'|i18n( 'design/admin/notification/settings' )} {foreach $row.path as $part}{$part|wash}{delimiter} &rsaquo; {/delimiter}{/foreach}</span>{/if}
                        <span>{if $row.last_change}{'Last change below it'|i18n( 'design/admin/notification/settings' )}: {$row.last_change|l10n( 'shortdatetime' )}{else}{'No content below it yet'|i18n( 'design/admin/notification/settings' )}{/if}</span>
                    </div>
        {/if}
                </div>
            </li>
    {/foreach}
        </ul>
        {include name=navigator uri='design:navigator/google.tpl' page_uri='/notification/settings' item_count=$subscription_total
                 view_parameters=$view_parameters item_limit=$subscription_limit}
{elseif or( $q|ne( '' ), $cls|ne( '' ) )}
        <div class="nf-empty">
            <h3>{'No followed item matches the filter'|i18n( 'design/admin/notification/settings' )}</h3>
        </div>
{else}
        <div class="nf-empty">
            <h3>{'You do not follow any items yet'|i18n( 'design/admin/notification/settings' )}</h3>
            <p>{'Choose Add items to pick content, or open an item and choose Notify me in its menu. You are then told by e-mail when something new is published below it.'|i18n( 'design/admin/notification/settings' )}</p>
        </div>
{/if}
        <div class="nf-actions">
            <input class="nf-btn primary" type="submit" name="NewRule_{$handler.id_string}" value="{'Add items'|i18n( 'design/admin/notification/settings' )}" title="{'Add items to your personal notification list.'|i18n( 'design/admin/notification/settings' )|wash}" />
{if $subscriptions|count|gt( 0 )}
            <input class="nf-btn danger" type="submit" name="RemoveRule_{$handler.id_string}" value="{'Remove selected'|i18n( 'design/admin/notification/settings' )}" title="{'Remove selected items.'|i18n( 'design/admin/notification/settings' )|wash}" />
{/if}
        </div>
    </form>
</section>
