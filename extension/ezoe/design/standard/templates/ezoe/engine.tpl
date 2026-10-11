{* The editor engine of the current user, ezoe/engine *}
<div class="context-block">
<div class="box-header"><h1 class="context-title">{'Online editor'|i18n( 'design/standard/ezoe' )}</h1></div>
<div class="box-content">
{if $saved}
    <div class="message-feedback"><h2>{'The editor of your user was saved.'|i18n( 'design/standard/ezoe' )}</h2></div>
{/if}
{if $error}
    <div class="message-warning"><h2>{'This editor is not available.'|i18n( 'design/standard/ezoe' )}</h2></div>
{/if}
<table class="list ezoe-engines" cellspacing="0" summary="{'Editor engines'|i18n( 'design/standard/ezoe' )}">
    <tr>
        <th>{'Editor'|i18n( 'design/standard/ezoe' )}</th>
        <th>{'Version'|i18n( 'design/standard/ezoe' )}</th>
        <th>{'Description'|i18n( 'design/standard/ezoe' )}</th>
        <th>{'Status'|i18n( 'design/standard/ezoe' )}</th>
    </tr>
    {foreach $details as $engine sequence array( 'bglight', 'bgdark' ) as $style}
    <tr class="{$style}" data-engine="{$engine.identifier|wash}" data-status="{$engine.status|wash}">
        <td>{$engine.label|wash} <code>{$engine.identifier|wash}</code></td>
        <td>{$engine.version|wash}</td>
        <td>{$engine.description|wash}</td>
        <td>
        {if eq( $engine.status, 'available' )}
            {if eq( $engine.identifier, $resolved )}{'Available, in use now'|i18n( 'design/standard/ezoe' )}{else}{'Available'|i18n( 'design/standard/ezoe' )}{/if}
        {elseif eq( $engine.status, 'unavailable' )}
            {'Not available: its files are missing'|i18n( 'design/standard/ezoe' )}
        {else}
            {'Cannot be used: %reason'|i18n( 'design/standard/ezoe',, hash( '%reason', $engine.why ) )|wash}
        {/if}
        </td>
    </tr>
    {/foreach}
</table>
{if $switch_enabled}
    <p>{'The edit form also has a button to switch to each of the other available editors.'|i18n( 'design/standard/ezoe' )}</p>
{/if}
<form method="post" action={'/ezoe/engine'|ezurl}>
    <div class="block">
        <label for="ezoe-engine">{'Editor for text fields'|i18n( 'design/standard/ezoe' )}</label>
        <select id="ezoe-engine" name="Engine">
            <option value=""{if eq( $preference, '' )} selected="selected"{/if}>{'Default of this site (%engine)'|i18n( 'design/standard/ezoe',, hash( '%engine', $engines[$configured] ) )}</option>
            {foreach $engines as $id => $label}
            <option value="{$id|wash}"{if eq( $preference, $id )} selected="selected"{/if}>{$label|wash}</option>
            {/foreach}
        </select>
        <p>{'In use now: %engine. Saved only for your user, the text of your content is the same with every editor.'|i18n( 'design/standard/ezoe',, hash( '%engine', $engines[$resolved] ) )}</p>
    </div>
    <div class="controlbar"><input class="button" type="submit" name="SaveEngineButton" value="{'Save'|i18n( 'design/standard/ezoe' )}" /></div>
</form>
</div>
</div>
