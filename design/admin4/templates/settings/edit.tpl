{* Edit or add one INI setting (settings/edit/<siteaccess>/<file>/<block>[/<setting>[/<placement>]]).

   What the setting is, every file that sets it in load order with what each gives it, then the form: the type, where
   to save it (the siteaccess file, an extension's file or the global override), the name of a new setting and the
   value. A secret's value is never shown: its field starts empty, and left empty it keeps the value there. The
   field names (INIFile, Block, SiteAccess, SettingType, SettingPlacement, SettingName, Value), the buttons
   (WriteSetting, Cancel) and the variables the view has always set are unchanged.

   The same file is in design/admin and design/admin4. This is an edit view, which admin4 draws without its main
   card; .exp-standalone gives the page its own. Works without javascript (the type is changed with its own button).
   Guide: doc/guides/settings-page.md *}
{include uri='design:settings/exp_style.tpl'}

{def $secret = first_set( $is_secret, false() )
     $steps = first_set( $chain_steps, array() )
     $existing = first_set( $exists, false() )}

<form method="post" action={'settings/edit'|ezurl} class="exp-settings exp-standalone" aria-labelledby="settings-edit-title">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" id="settings-edit-title">{if $setting_name}{'Edit setting %setting'|i18n( 'design/admin/settings',, hash( '%setting', $setting_name ) )|wash}{else}{'New setting in [%block]'|i18n( 'design/admin/settings',, hash( '%block', $block ) )|wash}{/if}</h1>
<span class="exp-muted">{$ini_file|wash}, {$current_siteaccess|wash}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:settings/settings_validation.tpl'}

<div class="exp-panel">
<dl class="exp-facts">
    <div><dt>{'INI file'|i18n( 'design/admin/settings' )}</dt><dd><code>{$ini_file|wash}</code></dd></div>
    <div><dt>{'Block'|i18n( 'design/admin/settings' )}</dt><dd><code>[{$block|wash}]</code></dd></div>
    <div><dt>{'Setting'|i18n( 'design/admin/settings' )}</dt><dd>{if $setting_name}<code>{$setting_name|wash}</code>{else}<em>{'new'|i18n( 'design/admin/settings' )}</em>{/if}</dd></div>
    <div><dt>{'Siteaccess'|i18n( 'design/admin/settings' )}</dt><dd><code>{$current_siteaccess|wash}</code></dd></div>
</dl>
</div>

{if $secret}
<div class="exp-feedback is-info"><p>{'This setting holds a secret. Its value is never shown: type a new one to replace it, or leave the field empty to keep the value it has.'|i18n( 'design/admin/settings' )}{if eq( $secret_state, 'empty' )} {'At the moment it is empty.'|i18n( 'design/admin/settings' )}{elseif eq( $secret_state, 'set' )} {'At the moment it is set.'|i18n( 'design/admin/settings' )}{/if}</p></div>
{/if}
{if first_set( $restart, false() )}
<div class="exp-feedback is-warn"><p>{'Velocity reads this setting only when it starts: after saving, restart it with ./console exp:velocity restart. PHP-FPM reads it on its next request.'|i18n( 'design/admin/settings' )|wash}</p></div>
{/if}

<section class="exp-section" aria-labelledby="settings-chain-title">
<div class="exp-section-head"><h2 class="exp-h2" id="settings-chain-title">{'Where it is set'|i18n( 'design/admin/settings' )}</h2>
<p>{'The files that set it, in the order they are read; the last one wins (an array collects the elements of all of them unless one empties it first).'|i18n( 'design/admin/settings' )}</p></div>
{if $steps}
<ol class="exp-steps">
{foreach $steps as $step}
<li class="is-{$step.status|wash}">
    <div class="exp-step-head">{include uri='design:settings/exp_origin.tpl' origin=$step.placement}
        <span class="exp-badge{if or( eq( $step.status, 'wins' ), eq( $step.status, 'adds' ) )} is-ok{/if}">{switch match=$step.status}{case match='wins'}{'in effect'|i18n( 'design/admin/settings' )}{/case}{case match='adds'}{'elements in effect'|i18n( 'design/admin/settings' )}{/case}{case match='reset'}{'empties it, adds nothing'|i18n( 'design/admin/settings' )}{/case}{case}{'overridden'|i18n( 'design/admin/settings' )}{/case}{/switch}</span>
        <span class="exp-step-path">{$step.path|wash}</span></div>
    <ul class="exp-ops">
    {foreach $step.lines as $line}
    <li>{switch match=$line.op}
        {case match='reset'}<code>{$setting_name|wash}[]</code> <span class="exp-op-what">{'empties the array: what earlier files added is dropped'|i18n( 'design/admin/settings' )}</span>{/case}
        {case match='append'}<code>{$setting_name|wash}[]={$line.text|wash}</code> <span class="exp-op-what">{'adds an element'|i18n( 'design/admin/settings' )}</span>{/case}
        {case match='hash'}<code>{$setting_name|wash}[{$line.key|wash}]={$line.text|wash}</code> <span class="exp-op-what">{'sets the key, replacing an earlier one'|i18n( 'design/admin/settings' )}</span>{/case}
        {case}<code>{$setting_name|wash}={$line.text|wash}</code>{/case}
    {/switch}</li>
    {/foreach}
    </ul>
</li>
{/foreach}
</ol>
{else}
<p class="exp-empty">{'No file sets it yet in this siteaccess.'|i18n( 'design/admin/settings' )}</p>
{/if}
</section>

<input type="hidden" name="INIFile" value="{$ini_file|wash}" />
<input type="hidden" name="Block" value="{$block|wash}" />
<input type="hidden" name="SiteAccess" value="{$current_siteaccess|wash}" />
{if $setting_name}<input type="hidden" name="SettingName" value="{$setting_name|wash}" />{/if}

<section class="exp-section" aria-labelledby="settings-form-title">
<div class="exp-section-head"><h2 class="exp-h2" id="settings-form-title">{'New value'|i18n( 'design/admin/settings' )}</h2></div>
<div class="exp-panel">
<div class="exp-toolbar is-bare">
    {if $setting_name|not}
    <div class="exp-field">
        <label for="settings-name">{'Setting name'|i18n( 'design/admin/settings' )}</label>
        <input type="text" id="settings-name" name="SettingName" value="" required="required" pattern="[A-Za-z0-9_*@\-]+" maxlength="200" spellcheck="false" autocomplete="off" aria-describedby="settings-name-help"{if eq( $validation_field, 'Name' )} aria-invalid="true"{/if} />
        <span class="exp-help" id="settings-name-help">{'Letters, digits and _ * @ -.'|i18n( 'design/admin/settings' )}</span>
    </div>
    {/if}
    <div class="exp-field">
        <label for="settings-type">{'Type'|i18n( 'design/admin/settings' )}</label>
        <select name="SettingType" id="settings-type" onchange="this.form.submit()">
        {foreach $setting_type_array as $type_key => $type_name}<option value="{$type_key|wash}"{if eq( $type_key, $setting_type )} selected="selected"{/if}>{$type_name|wash}</option>{/foreach}
        </select>
        <noscript><button type="submit" class="exp-btn exp-btn-small" name="ChangeSettingType" value="1" formnovalidate="formnovalidate">{'Change type'|i18n( 'design/admin/settings' )}</button></noscript>
    </div>
</div>

<div class="exp-field exp-form-row">
    <label for="settings-value">{'Value'|i18n( 'design/admin/settings' )}</label>
    {switch match=$setting_type}
    {case match='array'}
        <textarea id="settings-value" name="Value" rows="10" spellcheck="false" aria-describedby="settings-value-help"{if eq( $validation_field, 'Value' )} aria-invalid="true"{/if}>
{$value|wash}</textarea>
        <span class="exp-help" id="settings-value-help">{'One element per line: =value adds an element, [key]=value sets a key. Leave the first line empty to empty the array before these elements; without it they are added to what the earlier files give.'|i18n( 'design/admin/settings' )}</span>
    {/case}
    {case match='enable/disable'}
        <select id="settings-value" name="Value">
            <option value="enabled"{if eq( $value, 'enabled' )} selected="selected"{/if}>{'Enabled'|i18n( 'design/admin/settings' )}</option>
            <option value="disabled"{if ne( $value, 'enabled' )} selected="selected"{/if}>{'Disabled'|i18n( 'design/admin/settings' )}</option>
        </select>
    {/case}
    {case match='true/false'}
        <select id="settings-value" name="Value">
            <option value="true"{if eq( $value, 'true' )} selected="selected"{/if}>{'True'|i18n( 'design/admin/settings' )}</option>
            <option value="false"{if ne( $value, 'true' )} selected="selected"{/if}>{'False'|i18n( 'design/admin/settings' )}</option>
        </select>
    {/case}
    {case}
        {if $secret}
        <input type="password" id="settings-value" name="Value" value="" autocomplete="new-password" aria-describedby="settings-value-help" />
        <span class="exp-help" id="settings-value-help">{'Leave empty to keep the value it has.'|i18n( 'design/admin/settings' )}</span>
        {else}
        <input type="text" id="settings-value" name="Value" value="{$value|wash}" spellcheck="false" aria-describedby="settings-value-help"{if eq( $validation_field, 'Value' )} aria-invalid="true"{/if} />
        <span class="exp-help" id="settings-value-help">{'One line. A value cannot contain a line break or */.'|i18n( 'design/admin/settings' )|wash}</span>
        {/if}
    {/case}
    {/switch}
</div>

<fieldset class="exp-field">
    <legend>{'Save it in'|i18n( 'design/admin/settings' )}</legend>
    <label class="exp-check"><input type="radio" name="SettingPlacement" value="siteaccess"{if eq( $placement, 'siteaccess' )} checked="checked"{/if} /><span>{'Siteaccess %siteaccess only'|i18n( 'design/admin/settings',, hash( '%siteaccess', $current_siteaccess ) )|wash} <code class="exp-muted">settings/siteaccess/{$current_siteaccess|wash}/{$ini_file|wash}.append.php</code></span></label>
    <label class="exp-check"><input type="radio" name="SettingPlacement" value="override"{if and( ne( $placement, 'siteaccess' ), is_set( $values.extensions[$placement] )|not )} checked="checked"{/if} /><span>{'Every siteaccess (global override)'|i18n( 'design/admin/settings' )} <code class="exp-muted">settings/override/{$ini_file|wash}.append.php</code></span></label>
    {if $values.extensions}
    <details class="exp-chain">
        <summary>{'In an extension'|i18n( 'design/admin/settings' )}</summary>
        <p class="exp-help">{'Changes the extension’s own settings file, which an update of the extension replaces. Prefer the siteaccess or the global override.'|i18n( 'design/admin/settings' )}</p>
        {foreach $values.extensions as $extension_name => $extension_value}
        <label class="exp-check"><input type="radio" name="SettingPlacement" value="{$extension_name|wash}"{if eq( $placement, $extension_name )} checked="checked"{/if} /><span>{$extension_name|wash} <code class="exp-muted">extension/{$extension_name|wash}/settings</code></span></label>
        {/foreach}
    </details>
    {/if}
    <span class="exp-help">{'Saving clears the INI cache, so PHP-FPM and Velocity read the change on their next request.'|i18n( 'design/admin/settings' )}</span>
</fieldset>
</div>
</section>

<div class="exp-bottombar">
    <p class="exp-meta">{if $existing}{'The file chosen above gets this value; the other files are not changed.'|i18n( 'design/admin/settings' )}{else}{'The setting is added to the file chosen above.'|i18n( 'design/admin/settings' )}{/if}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="WriteSetting" value="1">{'Save'|i18n( 'design/admin/settings' )}</button>
        <button type="submit" class="exp-btn" name="Cancel" value="1" formnovalidate="formnovalidate">{'Cancel'|i18n( 'design/admin/settings' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
{undef $secret $steps $existing}
