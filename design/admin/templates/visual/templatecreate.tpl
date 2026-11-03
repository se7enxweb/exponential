{* Design > Templates > new override of one template: extension, file name, the override keys and what the new file
   starts with. The same file is in design/admin and design/admin4; the styling is the shared admin components
   (components.css: .a4-form, .a4-formrow, .a4-check, .a4-toolbar). *}
<form method="post" action={concat( '/visual/templatecreate', $template )|ezurl}>

<div class="context-block a4-page a4-visual">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create new template override for <%template_name>'|i18n( 'design/admin/visual/templatecreate',, hash( '%template_name', $template ) )|wash}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if eq( $error, 'permission_denied' )}
<div class="a4-notice is-bad" role="alert"><p><strong>{'Could not create template, permission denied.'|i18n( 'design/admin/visual/templatecreate' )}</strong></p></div>
{/if}
{if eq( $error, 'invalid_name' )}
<div class="a4-notice is-bad" role="alert"><p><strong>{'Invalid name. You can only use the characters a-z, numbers, _ and / (for subdirectories).'|i18n( 'design/admin/visual/templatecreate' )}</strong></p></div>
{/if}
{if eq( $error, 'invalid_template' )}
<div class="a4-notice is-bad" role="alert"><p><strong>{'The selected template is not a source template and cannot be used to create an override.'|i18n( 'design/admin/visual/templatecreate' )}</strong></p></div>
{/if}

<p class="a4-intro">{'The newly created template file will be placed in %path.'|i18n( 'design/admin/visual/templatecreate',, hash( '%path', concat( 'design/', $site_design, '/override/templates/' ) ) )|wash}</p>

<div class="a4-form is-wide">

{def $extension_list=ezini('ExtensionSettings','DesignExtensions','design.ini' )}
{if ne($extension_list, array())}
<div class="a4-formrow">
    <label for="templatecreate-extension">{'Extension'|i18n( 'design/standard/visual/templatecreate' )}</label>
    <div>
    <select class="a4-input is-auto" id="templatecreate-extension" name="DesignExtension">
        {foreach $extension_list as $extensionName}
            {if eq($design_extension,$extensionName)}
            <option value="{$extensionName|wash}" selected="selected">{$extensionName|wash}</option>
            {else}
            <option value="{$extensionName|wash}">{$extensionName|wash}</option>
            {/if}
        {/foreach}
        <option value=""{if eq($design_extension,"")} selected="selected"{/if}>{'No extension'|i18n( 'design/admin/visual/templatecreate' )}</option>
    </select>
    </div>
</div>
{/if}
{undef $extension_list}

<div class="a4-formrow">
    <label for="templatecreate-name">{'Filename'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div class="a4-searchrow"><input id="templatecreate-name" class="a4-input" type="text" name="TemplateName" value="{$template_name|wash}" /> <code>.tpl</code></div>
</div>

{switch match=$template_type}
{case match='node_view'}
<div class="a4-formrow">
    <label for="templatecreate-class">{'Class'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div>
    <select class="a4-input" id="templatecreate-class" name="Match[class_identifier]">
        <option value="-1">{'All classes'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Class loop=fetch('content', 'can_instantiate_class_list')}
            {let contentClass = fetch( content, class, hash( class_id, $Class:item.id ) )}
                <option value="{$Class:contentClass.identifier|wash}" {if eq($Class:contentClass.id, $override_keys['classID'])}selected{/if}>{$Class:item.name|wash}</option>
            {/let}
        {/section}
    </select>
    </div>
</div>
<div class="a4-formrow">
    <label for="templatecreate-section">{'Section'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div>
    <select class="a4-input is-auto" id="templatecreate-section" name="Match[section]">
        <option value="-1">{'All sections'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Section loop=fetch( 'content', 'section_list' )}
            <option value="{$:item.id}">{$:item.name|wash}</option>
        {/section}
    </select>
    </div>
</div>
<div class="a4-formrow">
    <label for="templatecreate-object">{'Object ID'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div><input class="a4-input is-auto" id="templatecreate-object" type="text" size="8" value="{$override_keys['objectID']|wash}" name="Match[object]" /></div>
</div>
<div class="a4-formrow">
    <label for="templatecreate-node">{'Node ID'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div><input class="a4-input is-auto" id="templatecreate-node" type="text" size="8" value="{$override_keys['nodeID']|wash}" name="Match[node]" /></div>
</div>
<div class="a4-formrow" role="radiogroup" aria-labelledby="templatecreate-base">
    <span class="a4-label" id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}</span>
    <div class="a4-field">
        <label class="a4-check"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked" /><span>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <label class="a4-check" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" /><span>{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <select class="a4-input is-auto" name="TemplateSource" aria-labelledby="templatecreate-copy">
            <option value="">{'Automatic (class-specific override if available, otherwise source)'|i18n( 'design/admin/visual/templatecreate' )}</option>
            {foreach $default_template_sources as $source}
            <option value="{$source.path|wash}">{$source.label|wash}</option>
            {/foreach}
        </select>
        <label class="a4-check"><input type="radio" name="TemplateContent" value="ContainerTemplate" /><span>{'Container (with children)'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <label class="a4-check"><input type="radio" name="TemplateContent" value="ViewTemplate" /><span>{'View (without children)'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
    </div>
</div>
{/case}
{case match='object_view'}
<div class="a4-formrow">
    <label for="templatecreate-class">{'Class'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div>
    <select class="a4-input" id="templatecreate-class" name="Match[class_identifier]">
        <option value="-1">{'Any'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Class loop=fetch('content', 'class_list', hash('sorts', array()))}
        <option value="{$Class:item.identifier|wash}">{$Class:item.name|wash}</option>
        {/section}
    </select>
    </div>
</div>
<div class="a4-formrow">
    <label for="templatecreate-section">{'Section'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div>
    <select class="a4-input is-auto" id="templatecreate-section" name="Match[section]">
        <option value="-1">{'Any'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Section loop=fetch( 'content', 'section_list' )}
            <option value="{$:item.id}">{$:item.name|wash}</option>
        {/section}
    </select>
    </div>
</div>
<div class="a4-formrow">
    <label for="templatecreate-object">{'Object'|i18n( 'design/admin/visual/templatecreate' )}</label>
    <div><input class="a4-input is-auto" id="templatecreate-object" type="text" size="8" value="" name="Match[object]" /></div>
</div>
<div class="a4-formrow" role="radiogroup" aria-labelledby="templatecreate-base">
    <span class="a4-label" id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}</span>
    <div class="a4-field">
        <label class="a4-check"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked" /><span>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <label class="a4-check" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" /><span>{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <select class="a4-input is-auto" name="TemplateSource" aria-labelledby="templatecreate-copy">
            <option value="">{'Automatic (class-specific override if available, otherwise source)'|i18n( 'design/admin/visual/templatecreate' )}</option>
            {foreach $default_template_sources as $source}
            <option value="{$source.path|wash}">{$source.label|wash}</option>
            {/foreach}
        </select>
        <label class="a4-check"><input type="radio" name="TemplateContent" value="ViewTemplate" /><span>{'View (without children)'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
    </div>
</div>
{/case}
{case}
<div class="a4-formrow" role="radiogroup" aria-labelledby="templatecreate-base">
    <span class="a4-label" id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}</span>
    <div class="a4-field">
        <label class="a4-check"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked" /><span>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <label class="a4-check" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" /><span>{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</span></label>
        <select class="a4-input is-auto" name="TemplateSource" aria-labelledby="templatecreate-copy">
            <option value="">{'Automatic (source template)'|i18n( 'design/admin/visual/templatecreate' )}</option>
            {foreach $default_template_sources as $source}
            <option value="{$source.path|wash}">{$source.label|wash}</option>
            {/foreach}
        </select>
    </div>
</div>
{/case}
{/switch}

</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <input class="a4-btn a4-btn-primary" type="submit" name="CreateOverrideButton" value="{'OK'|i18n( 'design/admin/visual/templatecreate' )|wash}" />
        <input class="a4-btn" type="submit" name="CancelOverrideButton" value="{'Cancel'|i18n( 'design/admin/visual/templatecreate' )|wash}" />
    </div>
</div>

</div></div></div>

</div>

</form>
