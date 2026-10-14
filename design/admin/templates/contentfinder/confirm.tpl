{* The confirmation page of a content finder action (contentfinder/action). Variables: plan
   (expContentFinderAction::plan()), reason ('' or confirm_permanent: the box was not ticked), confirm_token,
   confirm_field, form_token, token_field, restore_allowed. The page says exactly what will happen; nothing is done
   until ConfirmButton is posted with the confirmation token. *}
{def $titles = hash( 'trash', 'Move to trash'|i18n( 'kernel/contentfinder' ),
                     'delete', 'Delete permanently'|i18n( 'kernel/contentfinder' ),
                     'purge', 'Purge from the trash'|i18n( 'kernel/contentfinder' ),
                     'removeversion', 'Remove version'|i18n( 'kernel/contentfinder' ) )
     $version_names = hash( 'draft', 'Draft'|i18n( 'kernel/contentfinder' ), 'archived', 'Archived'|i18n( 'kernel/contentfinder' ),
                            'rejected', 'Rejected'|i18n( 'kernel/contentfinder' ), 'internal_draft', 'Internal draft'|i18n( 'kernel/contentfinder' ) )}
<div class="context-block a4-page a4-contentfinder" id="exp-contentfinder-confirm">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if is_set( $titles[$plan.action] )}{$titles[$plan.action]|wash}{else}{'Content finder'|i18n( 'kernel/contentfinder' )}{/if}{if $plan.name}: {$plan.name|wash}{/if}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $plan.allowed|not}
<div class="message-warning" role="alert"><h2>{'This action is not possible.'|i18n( 'kernel/contentfinder' )} {include uri='design:contentfinder/reason.tpl' reason=$plan.reason}</h2></div>
{if and( $plan.reason|eq( 'too_many' ), $plan.nodes|count )}
<p>{'%count nodes are below it; at most %limit can be removed here.'|i18n( 'kernel/contentfinder',, hash( '%count', $plan.children, '%limit', $plan.limit ) )}</p>
{/if}
{else}
{if $reason|eq( 'confirm_permanent' )}
<div class="message-warning" role="alert"><h2>{include uri='design:contentfinder/reason.tpl' reason=$reason}</h2></div>
{/if}

<div class="block">
<p>{'Object %id, %class.'|i18n( 'kernel/contentfinder',, hash( '%id', $plan.object_id, '%class', $plan.class_name ) )|wash}</p>
{switch match=$plan.action}
{case in=array( 'trash', 'delete' )}
    <p>{if eq( $plan.action, 'trash' )}{'The object goes to the trash from every location listed here, with everything below them. It can be restored from the trash.'|i18n( 'kernel/contentfinder' )}{else}{'The object is deleted from every location listed here, with everything below them. This cannot be undone: nothing goes to the trash.'|i18n( 'kernel/contentfinder' )}{/if}</p>
    <ul class="a4-contentfinder-plan">
    {foreach $plan.nodes as $node}
        <li>{'Node %id'|i18n( 'kernel/contentfinder',, hash( '%id', $node.node_id ) )} {if $node.is_main}<span class="a4-badge a4-contentfinder-badge main">{'Main'|i18n( 'kernel/contentfinder' )}</span>{/if}
            <code>{$node.url_alias|wash}</code>:
            {if $node.children|gt( 0 )}<strong>{'%count nodes below it'|i18n( 'kernel/contentfinder',, hash( '%count', $node.children ) )}</strong>{else}{'nothing below it'|i18n( 'kernel/contentfinder' )}{/if}</li>
    {/foreach}
    </ul>
    <p>{'Locations: %locations. Nodes below them: %children. Versions of the object: %versions.'|i18n( 'kernel/contentfinder',, hash( '%locations', $plan.locations, '%children', $plan.children, '%versions', $plan.versions ) )}
    {if $plan.reverse_related|gt( 0 )}<strong>{'%count other objects relate to what is removed.'|i18n( 'kernel/contentfinder',, hash( '%count', $plan.reverse_related ) )}</strong>{/if}</p>
{/case}
{case match='purge'}
    <p>{'The object is purged from the trash with all its %versions versions. This cannot be undone.'|i18n( 'kernel/contentfinder',, hash( '%versions', $plan.versions ) )}</p>
    <ul class="a4-contentfinder-plan">
    {foreach $plan.trash as $entry}
        <li>{'Node %id, trashed from'|i18n( 'kernel/contentfinder',, hash( '%id', $entry.node_id ) )} <code>{$entry.path|wash}</code></li>
    {/foreach}
    </ul>
{/case}
{case match='removeversion'}
    <p>{'Version %version (%status, %language) is removed. This cannot be undone. The object keeps its other %count versions.'|i18n( 'kernel/contentfinder',, hash( '%version', $plan.version.version, '%status', first_set( $version_names[$plan.version.status_name], $plan.version.status_name ), '%language', $plan.version.language, '%count', sub( $plan.versions, 1 ) ) )|wash}</p>
{/case}
{/switch}
</div>

<form method="post" action={'contentfinder/action'|ezurl}>
    <input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
    <input type="hidden" name="{$confirm_field|wash}" value="{$confirm_token|wash}" />
    <input type="hidden" name="Action" value="{$plan.action|wash}" />
    <input type="hidden" name="ObjectID" value="{$plan.object_id}" />
    {if $plan.version}<input type="hidden" name="VersionID" value="{$plan.version.id}" />{/if}
    {if $plan.permanent}
    <p class="a4-contentfinder-permanent"><label><input type="checkbox" name="ConfirmPermanent" value="1" /> {'I understand that this cannot be undone.'|i18n( 'kernel/contentfinder' )}</label></p>
    {/if}
    <div class="block a4-contentfinder-actions">
        <input type="submit" class="defaultbutton{if $plan.permanent} a4-contentfinder-danger{/if}" name="ConfirmButton" value="{if is_set( $titles[$plan.action] )}{$titles[$plan.action]|wash}{else}OK{/if}" />
        <input type="submit" class="button" name="CancelButton" value="{'Cancel'|i18n( 'kernel/contentfinder' )}" />
    </div>
</form>
{/if}

<p><a href={concat( 'contentfinder/search?SearchType=object&SearchText=', $plan.object_id )|ezurl}>{'Back to the finder'|i18n( 'kernel/contentfinder' )}</a></p>

{* DESIGN: Content END *}</div></div></div>
</div>
{undef $titles $version_names}
