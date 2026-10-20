{* The confirmation of an action of the log viewer (setup/logsaction; doc/eng-us/features/6.0/log-viewer.md, "Actions"):
   rotate now, empty the file, remove an archived rotation. Variables: log_action (rotate | truncate | delete),
   log_file (id, name, size, mtime, archive ...), log_rotations (the rotations kept, for rotate), log_token_field,
   log_token. Confirm posts the action again with ConfirmButton; Cancel goes back to the file.

   The same file is in design/admin and design/admin4. *}
<div class="context-block a4-page a4-setup-logs">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{switch match=$log_action}
    {case match='rotate'}{'Rotate %name now?'|i18n( 'design/admin/setup/logs',, hash( '%name', $log_file.name ) )|wash}{/case}
    {case match='truncate'}{'Empty %name?'|i18n( 'design/admin/setup/logs',, hash( '%name', $log_file.name ) )|wash}{/case}
    {case}{'Remove %name?'|i18n( 'design/admin/setup/logs',, hash( '%name', $log_file.name ) )|wash}{/case}
{/switch}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-notice {if $log_action|eq( 'rotate' )}is-warn{else}is-bad{/if}" role="alert">
{switch match=$log_action}
    {case match='rotate'}{'The file becomes %name.1 and the older rotations move one number up; the oldest beyond %count is removed. The next line written starts a new file.'|i18n( 'design/admin/setup/logs',, hash( '%name', $log_file.name, '%count', $log_rotations ) )|wash}{/case}
    {case match='truncate'}{'Every line of the file (%size) is removed for good. Download it first if it may still be needed.'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_file.size|si( byte ) ) )|wash}{/case}
    {case}{'The archived rotation (%size) is removed for good.'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_file.size|si( byte ) ) )|wash}{/case}
{/switch}
{'The action is recorded in the audit.'|i18n( 'design/admin/setup/logs' )}
</div>

<ul class="a4-setup-inline a4-setup-log-facts">
    <li><code>{$log_file.id|wash}</code></li>
    <li>{$log_file.size|si( byte )}</li>
    <li>{'Changed %time'|i18n( 'design/admin/setup/logs',, hash( '%time', $log_file.mtime|l10n( 'shortdatetime' ) ) )|wash}</li>
</ul>

<form method="post" action={'setup/logsaction'|ezurl} class="a4-row a4-setup-log-confirm">
    <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
    <input type="hidden" name="File" value="{$log_file.id|wash}" />
    <input type="hidden" name="LogAction" value="{$log_action|wash}" />
    <button type="submit" class="a4-btn {if $log_action|eq( 'rotate' )}a4-btn-primary{else}a4-btn-danger{/if}" name="ConfirmButton" value="1">{switch match=$log_action}
        {case match='rotate'}{'Rotate now'|i18n( 'design/admin/setup/logs' )}{/case}
        {case match='truncate'}{'Empty the file'|i18n( 'design/admin/setup/logs' )}{/case}
        {case}{'Remove this rotation'|i18n( 'design/admin/setup/logs' )}{/case}
    {/switch}</button>
    <button type="submit" class="a4-btn" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/setup/logs' )}</button>
</form>

</div></div></div>
</div>
