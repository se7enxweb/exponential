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
