{* One setting of Setup > Image aliases (setup/aliases.tpl): its name and what it does, the field (a choice, a number, a
   list one per line, an address, or the NAME of a secret, never its value) and, when settings/override sets it, the
   box that removes it there so the inherited value applies again. Variables: row (Aliases::settingsRows()),
   can_manage. The same file is in design/admin and design/admin4. *}
{def $ia_id = concat( 'ia-set-', $row.block, '-', $row.name )
     $ia_field = concat( 'Setting[', $row.block, '][', $row.name, ']' )}
<tr{if $row.error|ne( '' )} class="is-selected"{/if}>
    <th scope="row"><label for="{$ia_id|wash}"><code>{$row.name|wash}</code></label>
        <span class="a4-help" id="{$ia_id|wash}-help">{switch match=$row.name}
        {case match='AliasGeneration'}{'on-demand: templates get the address, the alias is made when it is asked for. render: the template makes it while the page renders (the original behavior).'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='OnDemandURL'}{'The address of an alias that is not made yet: token (signed, needs no database), ids-token, ids, node or storage.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='Delivery'}{'auto: a redirect to the file when the web server serves the storage, else the file is streamed.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='MaxWait'}{'Seconds a request may spend making or fetching an alias; then it answers the placeholder and a job finishes the work.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='PreferredConverter'}{'The converter tried first for the WebP and AVIF variants.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='Negotiate'}{'Answer WebP or AVIF to browsers that ask for it, for aliases that list Formats[].'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='Placeholder'}{'A neutral image of the right size while an image is missing.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='SwapScript'}{'A small script that replaces placeholders with the real image when it is there.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='PreGenerate'}{'After publishing, a job makes the aliases of the new images for every related siteaccess.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='PreGenerateAliasList'}{'Only these aliases are made after publishing, one per line. Empty: all of them.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='PreGenerateFormats'}{'Make the WebP and AVIF variants after publishing too.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='MaxParallel'}{'Conversions at once on one server.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='BulkNice'}{'The process priority (nice) of bulk runs, 0 to 19.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='CacheMaxAge'}{'Seconds browsers and caches may keep a made alias.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='DraftTokenLifetime'}{'Seconds the address of a draft or preview image stays valid.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='SigningKeyGrace'}{'Seconds the previous signing key stays valid after a rotation.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='Environment'}{'The environment of this installation.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='SyncFrom'}{'The environment missing images are fetched from; none: nothing is fetched.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='PathMap'}{'One per line: local storage prefix;source prefix or address, for storage paths that differ.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='Timeout'}{'Seconds one request to the source may take.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='MaxFileSize'}{'The largest file taken from the source, in bytes.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='FailuresBeforePause'}{'Failures in a row after which the source is paused.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='PauseSeconds'}{'How long a failing source is paused.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='AlertEmail'}{'Mail the administrators when the source is paused (at most once an hour).'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='AlertRecipients'}{'One e-mail address per line; empty: the AdminEmail of site.ini.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case match='BaseURL'}{'The address of the source installation %block, e.g. https://www.example.org.'|i18n( 'design/admin/setup/aliases',, hash( '%block', concat( '[', $row.block, ']' ) ) )|wash}{/case}
        {case match='Credential'}{'The name of the secret with the credential, or env:VARIABLE. Never the credential itself.'|i18n( 'design/admin/setup/aliases' )}{/case}
        {case}{/case}
        {/switch}</span></th>
    <td>
    {switch match=$row.type}
    {case in=array( 'enum', 'switch' )}
        <select class="a4-input" id="{$ia_id|wash}" name="{$ia_field|wash}" aria-describedby="{$ia_id|wash}-help"{if $can_manage|not} disabled="disabled"{/if}>
        {foreach $row.values as $ia_value}<option value="{$ia_value|wash}"{if eq( $row.value, $ia_value )} selected="selected"{/if}>{if eq( $ia_value, '' )}{'(none)'|i18n( 'design/admin/setup/aliases' )}{else}{$ia_value|wash}{/if}</option>{/foreach}
        </select>
    {/case}
    {case match='list'}
        <textarea class="a4-input is-code" id="{$ia_id|wash}" name="{$ia_field|wash}" rows="3" spellcheck="false" aria-describedby="{$ia_id|wash}-help"{if $can_manage|not} readonly="readonly"{/if}>{$row.text|wash}</textarea>
    {/case}
    {case match='secret'}
        <input type="text" class="a4-input" id="{$ia_id|wash}" name="{$ia_field|wash}" value="{$row.value|wash}" list="ia-secret-names" autocomplete="off" spellcheck="false" maxlength="100" aria-describedby="{$ia_id|wash}-help"{if $can_manage|not} readonly="readonly"{/if} />
    {/case}
    {case}
        <input type="text" class="a4-input" id="{$ia_id|wash}" name="{$ia_field|wash}" value="{$row.value|wash}"{if or( eq( $row.type, 'int' ), eq( $row.type, 'number' ) )} inputmode="decimal"{/if} maxlength="500" aria-describedby="{$ia_id|wash}-help"{if $can_manage|not} readonly="readonly"{/if} />
    {/case}
    {/switch}
        {if $row.error|ne( '' )}<span class="a4-field-error" role="alert">{$row.error|wash}</span>{/if}
        <span class="a4-meta">{'Default: %value'|i18n( 'design/admin/setup/aliases',, hash( '%value', cond( eq( $row.default, '' ), '-', $row.default ) ) )|wash}</span>
    </td>
    <td>{if $row.overridden}
        <label class="a4-check"><input type="checkbox" name="Inherit[{$row.block|wash}][{$row.name|wash}]" value="1"{if $can_manage|not} disabled="disabled"{/if} /> <span>{'Set here; tick to remove it'|i18n( 'design/admin/setup/aliases' )}</span></label>
    {else}<span class="a4-muted">{'inherited'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</td>
</tr>
{undef $ia_id $ia_field}
