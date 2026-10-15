{* The objects that point at what a removal or a hide takes away (Exponential\Service\ReverseRelationCheck,
   doc/features/6.0/reverse-relation-check.md), one list per selected item. Variables: $check (the view's
   reverse_relation_check: items, has_references, more, failed, mode, blocked, refused) and $operation (remove or
   hide). Objects and locations the user may not read are never named, nor counted: a fixed sentence says that
   there are some. *}
<div class="{if $check.blocked}message-error{else}message-warning{/if} reverse-relation-check" id="reverse-relation-check" role="{if $check.blocked}alert{else}status{/if}">
{if $check.blocked}
    <h2>{'The removal is blocked: other objects use the items'|i18n( 'design/admin/content/reverserelations' )}</h2>
    {if $check.refused}<p><strong>{'Nothing was removed.'|i18n( 'design/admin/content/reverserelations' )}</strong></p>{/if}
    <p>{'This site refuses to remove content while other objects point at it. Change or remove the references listed here first, then remove the items.'|i18n( 'design/admin/content/reverserelations' )}</p>
{elseif eq( $operation, 'hide' )}
    <h2>{'Check before hiding'|i18n( 'design/admin/content/reverserelations' )}</h2>
    {if $check.has_references}<p>{'Other objects point at what you are about to hide. After hiding, their links, embedded items and relation fields lead to content the site no longer shows.'|i18n( 'design/admin/content/reverserelations' )}</p>{/if}
{else}
    <h2>{'Other objects use the items'|i18n( 'design/admin/content/reverserelations' )}</h2>
    <p>{'After the removal, their links, embedded items and relation fields point at content that is gone.'|i18n( 'design/admin/content/reverserelations' )}</p>
{/if}
{if $check.failed}
    <p>{'The references could not be checked.'|i18n( 'design/admin/content/reverserelations' )}</p>
{/if}
{if $check.more}
    <p>{'Further references are not listed.'|i18n( 'design/admin/content/reverserelations' )}</p>
{/if}
{foreach $check.items as $item}
    <h3 id="reverse-relation-item-{$item.node_id}">{$item.name|wash}</h3>
    {if $item.relations}
    <table class="list reverse-relation-list" cellspacing="0" aria-labelledby="reverse-relation-item-{$item.node_id}">
    <tr>
        <th scope="col">{'Used by'|i18n( 'design/admin/content/reverserelations' )}</th>
        <th scope="col">{'Type'|i18n( 'design/admin/content/reverserelations' )}</th>
        <th scope="col">{'How'|i18n( 'design/admin/content/reverserelations' )}</th>
        <th scope="col">{'Points at'|i18n( 'design/admin/content/reverserelations' )}</th>
    </tr>
    {foreach $item.relations as $relation sequence array( 'bglight', 'bgdark' ) as $style}
    <tr class="{$style}">
        <td>{if $relation.from_node_id}<a href={concat( 'content/view/full/', $relation.from_node_id )|ezurl}>{$relation.from_name|wash}</a>{else}{$relation.from_name|wash}{/if}</td>
        <td>{$relation.from_class|wash}</td>
        <td>{foreach $relation.kinds as $kind}{delimiter}, {/delimiter}{if eq( $kind, 'embed' )}{'Embedded in rich text'|i18n( 'design/admin/content/reverserelations' )}{elseif eq( $kind, 'link' )}{'Linked in rich text'|i18n( 'design/admin/content/reverserelations' )}{elseif eq( $kind, 'attribute' )}{'Relation field'|i18n( 'design/admin/content/reverserelations' )}{else}{'Object relation'|i18n( 'design/admin/content/reverserelations' )}{/if}{/foreach}{if $relation.field} ({$relation.field|wash}){/if}</td>
        <td>{if $relation.is_selected}{'This item'|i18n( 'design/admin/content/reverserelations' )}{elseif $relation.to_name}{$relation.to_name|wash}{else}<em>{'An object you may not read'|i18n( 'design/admin/content/reverserelations' )}</em>{/if}</td>
    </tr>
    {/foreach}
    </table>
    {/if}
    {if $item.unreadable}
    <p class="reverse-relation-unreadable">{'There are further references that you may not see.'|i18n( 'design/admin/content/reverserelations' )}</p>
    {/if}
    {if or( $item.locations, $item.unreadable_locations )}
    <p>{'The object has further locations. Hiding this one leaves them as they are:'|i18n( 'design/admin/content/reverserelations' )}</p>
    {if $item.locations}
    <ul class="reverse-relation-locations">
    {foreach $item.locations as $location}
        <li><a href={concat( 'content/view/full/', $location.node_id )|ezurl}>{$location.path|wash}</a>{if $location.is_hidden} ({'hidden'|i18n( 'design/admin/content/reverserelations' )}){elseif $location.is_invisible} ({'hidden by a parent'|i18n( 'design/admin/content/reverserelations' )}){/if}</li>
    {/foreach}
    </ul>
    {/if}
    {if $item.unreadable_locations}<p class="reverse-relation-unreadable">{'There are further locations that you may not see.'|i18n( 'design/admin/content/reverserelations' )}</p>{/if}
    {/if}
{/foreach}
</div>
