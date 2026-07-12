{section show=or( $not_removed, $ini_not_saved )}
<div class="message-error">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The overrides could not be removed.'|i18n( 'design/admin/visual/templateview' )}</h2>

{section show=$not_removed}
<p>{'The following files and override rules could not be removed because of insufficient file permissions'|i18n( 'design/admin/visual/templateview' )}:</p>
<ul>

{section var=item loop=$not_removed}
    <li>{$item.filename}</li>
{/section}

</ul>
{/section}

{if $ini_not_saved}
<p>{'The override.ini file could not be modified because of insufficient permission.'|i18n( 'design/admin/visual/templateview' )}</p>
{/if}

</div>
{/section}


<form method="post" name="templateview" action={concat( '/visual/templateview', $template_settings.template )|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Overrides for <%template_name> template in <%current_siteaccess> siteaccess (%override_count)'|i18n( 'design/admin/visual/templateview',, hash( '%template_name', $template_settings.template, '%current_siteaccess', $current_siteaccess, '%override_count', $template_settings.custom_match|count ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

<div class="block">
<label>{'Default template resource'|i18n( 'design/admin/visual/templateview' )}:</label>
{if $template_settings.base_dir}
{$template_settings.base_dir}
{else}
{'No source template resource found.'|i18n( 'design/admin/visual/templateview' )}
{/if}
</div>


<div class="block">
<label>{'Siteaccess'|i18n( 'design/admin/visual/templateview' )}:</label>

<select name="CurrentSiteAccess">
{section name=SiteAccess loop=ezini('SiteAccessSettings','RelatedSiteAccessList')}
    {if eq($current_siteaccess,$:item)}
        <option value="{$SiteAccess:item}" selected="selected">{$:item}</option>
    {else}
        <option value="{$SiteAccess:item}">{$:item}</option>
    {/if}
{/section}
</select>

<input class="button" type="submit" name="SelectCurrentSiteAccessButton" value="{'Set'|i18n( 'design/admin/visual/templateview' )}" />
</div>

{section show=$template_settings.custom_match}

<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/visual/templateview' )}" title="{'Invert selection.'|i18n( 'design/admin/visual/templateview' )}" onclick="ezjs_toggleCheckboxes( document.templateview, 'RemoveOverrideArray[]' ); return false;" /></th>
    <th>{'Name'|i18n( 'design/admin/visual/templateview' )}</th>
    <th>{'File'|i18n( 'design/admin/visual/templateview' )}</th>
    <th>{'Match conditions'|i18n( 'design/admin/visual/templateview' )}</th>
    <th class="tight">{'Priority'|i18n( 'design/admin/visual/templateview' )}</th>
    <th class="tight">&nbsp;</th>
</tr>
{section var=CustomMatch loop=$template_settings.custom_match sequence=array( bglight, bgdark )}
<tr class="{$CustomMatch.sequence}">
    <td><input type="checkbox" name="RemoveOverrideArray[]" value="{$CustomMatch.item.override_name}" /></td>
    <td>{$CustomMatch.item.override_name}</td>

    {if $CustomMatch.item.match_file}
    <td>{$CustomMatch.item.match_file}</td>
    {else}
    <td><i>{'No file matched'|i18n( 'design/admin/visual/templateview' )}</i></td>
    {/if}

    <td>
        {section show=is_set( $CustomMatch.item.conditions )}
            {section name=Condition  loop=$CustomMatch.item.conditions}
            <div class="templateview-condition" style="margin-bottom: 2px;">
                <label style="display:inline-block; min-width: 120px; font-weight: bold;">{$:key}</label>
                <input type="text" name="MatchArray[{$CustomMatch.item.override_name}][{$:key}]" value="{$:item|wash()}" size="24" />
                <input type="checkbox" name="RemoveMatchArray[{$CustomMatch.item.override_name}][{$:key}]" value="1" title="Remove this condition" />
            </div>
            {/section}
        {/section}
        <div class="templateview-condition" style="margin-top: 6px;">
            <select name="NewMatch[{$CustomMatch.item.override_name}][key]" style="min-width: 124px;">
                <option value="">{'Add condition'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="class_identifier">class_identifier</option>
                <option value="class">class</option>
                <option value="node">node</option>
                <option value="object">object</option>
                <option value="section">section</option>
                <option value="section_identifier">section_identifier</option>
                <option value="remote_id">remote_id</option>
                <option value="node_remote_id">node_remote_id</option>
                <option value="parent_node">parent_node</option>
                <option value="class_group">class_group</option>
                <option value="depth">depth</option>
                <option value="url_alias">url_alias</option>
                <option value="viewmode">viewmode</option>
                <option value="navigation_part_identifier">navigation_part_identifier</option>
                <option value="persistent_variable">persistent_variable</option>
                <option value="state">state</option>
                <option value="state_identifier">state_identifier</option>
            </select>
            <input type="text" name="NewMatch[{$CustomMatch.item.override_name}][value]" value="" size="24" />
        </div>
    </td>
    <td><input type="text" name="PriorityArray[{$CustomMatch.item.override_name}]" size="2" value="{$CustomMatch.number}" /></td>

    {if $CustomMatch.item.match_file}
    <td><a href={concat( '/visual/templateedit/', $CustomMatch.item.match_file)|ezurl} title="{'Edit override template.'|i18n( 'design/admin/visual/templateview' )}"><img src={'edit.gif'|ezimage} width="16" height="16" alt="Edit" /></a></td>
    {else}
    <td><img src={'edit-disabled.gif'|ezimage} alt="" /></td>
    {/if}

</tr>
{/section}
</table>

{section-else}
<div class="block">
<p>{'There are no overrides for the <%template_name> template.'|i18n( 'design/admin/visual/templateview',, hash( '%template_name', $template_settings.template ) )|wash}</p>
</div>
{/section}

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
    <div class="block">
        <div class="button-left">
        {if $template_settings.custom_match}
        <input class="button" type="submit" name="RemoveOverrideButton" value="{'Remove selected'|i18n( 'design/admin/visual/templateview' )}" title="{'Remove selected template overrides.'|i18n( 'design/admin/visual/templateview' )}" />
        {else}
        <input class="button-disabled" type="submit" name="RemoveOverrideButton" value="{'Remove selected'|i18n( 'design/admin/visual/templateview' )}" disabled="disabled"/>
        {/if}

        {if $new_override_allowed}
        <input class="button" type="submit" name="NewOverrideButton" value="{'New override'|i18n( 'design/admin/visual/templateview' )}" title="{'Create a new template override.'|i18n( 'design/admin/visual/templateview' )}" />
        {/if}
        </div>
        <div class="button-right">
            {if $template_settings.custom_match}
            <input class="button" type="submit" name="UpdateOverrideButton" value="{'Update overrides'|i18n( 'design/admin/visual/templateview' )}" />
            {else}
            <input class="button-disabled" type="submit" name="UpdateOverrideButton" value="{'Update overrides'|i18n( 'design/admin/visual/templateview' )}" disabled="disabled"/>
            {/if}
        </div>
        <div class="break"></div>
    </div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>

<script type="text/javascript">
/* Submit the templateview form with UpdateOverrideButton when Enter/Return is
 * pressed inside a form field. This prevents the browser from accidentally
 * triggering the first submit button (Remove selected) instead of the intended
 * Update overrides action. */
(function()
{
    var form = document.forms['templateview'];
    if ( !form )
    {
        return;
    }

    var onKeyDown = function( e )
    {
        if ( e.keyCode === 13 || e.which === 13 || e.key === 'Enter' )
        {
            var target = e.target || e.srcElement;
            if ( target && ( target.tagName === 'SELECT' || target.tagName === 'TEXTAREA' ||
                             ( target.tagName === 'INPUT' && ( target.type === 'text' || target.type === 'number' ) ) ) )
            {
                if ( typeof e.preventDefault === 'function' )
                    e.preventDefault();
                else
                    e.returnValue = false;

                if ( form.UpdateOverrideButton )
                    form.UpdateOverrideButton.click();
                else
                    form.submit();

                return false;
            }
        }
    };

    if ( form.addEventListener )
    {
        form.addEventListener( 'keydown', onKeyDown, false );
    }
    else if ( form.attachEvent )
    {
        form.attachEvent( 'onkeydown', onKeyDown );
    }
})();
</script>
