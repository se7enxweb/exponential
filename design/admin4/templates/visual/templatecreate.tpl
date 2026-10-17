{if eq( $error, 'permission_denied' )}
<div class="message-error" role="alert">
<h2>{'Could not create template, permission denied.'|i18n( 'design/admin/visual/templatecreate' )}</h2>
</div>
{/if}

{if eq( $error, 'invalid_name' )}
<div class="message-error" role="alert">
<h2>{'Invalid name. You can only use the characters a-z, numbers, _ and / (for subdirectories).'|i18n( 'design/admin/visual/templatecreate' )}</h2>
</div>
{/if}

{if eq( $error, 'invalid_template' )}
<div class="message-error" role="alert">
<h2>{'The selected template is not a source template and cannot be used to create an override.'|i18n( 'design/admin/visual/templatecreate' )}</h2>
</div>
{/if}

<form method="post" action={concat( '/visual/templatecreate', $template )|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create new template override for <%template_name>'|i18n( 'design/admin/visual/templatecreate',, hash( '%template_name', $template ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

<p>{'The newly created template file will be placed in %path.'|i18n( 'design/admin/visual/templatecreate',, hash( '%path', concat( 'design/', $site_design, '/override/templates/' ) ) )}</p>

{def $extension_list=ezini('ExtensionSettings','DesignExtensions','design.ini' )}
{if ne($extension_list, array())}
    <div class="block">
    <label for="templatecreate-extension">{'Extension'|i18n( 'design/standard/visual/templatecreate' )}:</label>

    <select id="templatecreate-extension" name="DesignExtension">
        {foreach $extension_list as $extensionName}
            {if eq($design_extension,$extensionName)}
                <option value="{$extensionName|wash}" selected="selected">{$extensionName|wash}</option>
            {else}
                <option value="{$extensionName|wash}">{$extensionName|wash}</option>
            {/if}
        {/foreach}

        {if eq($design_extension,"")}
            <option value="" selected="selected">{'No extension'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {else}
            <option value="">{'No extension'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {/if}
    </select>
    </div>
{/if}

<div class="block">
    <label for="templatecreate-name">{'Filename'|i18n( 'design/admin/visual/templatecreate' )}:</label>
    <input id="templatecreate-name" class="halfbox" type="text" name="TemplateName" value="{$template_name|wash}" />.tpl
</div>

{switch match=$template_type}
{case match='node_view'}
<div class="block">
<label>{'Override keys'|i18n( 'design/admin/visual/templatecreate' )}:</label>

<table>
<tr>
    <td><label class="a4-inline-label" for="templatecreate-class">{'Class'|i18n( 'design/admin/visual/templatecreate' )}</label>:</td>
    <td>
    <select id="templatecreate-class" name="Match[class_identifier]">
        <option value="-1">{'All classes'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Class loop=fetch('content', 'can_instantiate_class_list')}
            {let contentClass = fetch( content, class, hash( class_id, $Class:item.id ) )}
                <option value="{$Class:contentClass.identifier}" {if eq($Class:contentClass.id, $override_keys['classID'])}selected{/if}>{$Class:item.name|wash}</option>
            {/let}
        {/section}
    </select>
    </td>
</tr>
<tr>
    <td><label class="a4-inline-label" for="templatecreate-section">{'Section'|i18n( 'design/admin/visual/templatecreate' )}</label>:</td>
    <td>
    <select id="templatecreate-section" name="Match[section]">
        <option value="-1">{'All sections'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Section loop=fetch( 'content', 'section_list' )}
            <option value="{$:item.id}">{$:item.name|wash}</option>
        {/section}
    </select>
    </td>
</tr>
<tr>
    <td><label class="a4-inline-label" for="templatecreate-object">{'Object ID'|i18n( 'design/admin/visual/templatecreate' )}</label>:</td>
    <td><input id="templatecreate-object" type="text" size="5" value="{$override_keys['objectID']}" name="Match[object]" /></td>
</tr>
<tr>
    <td><label class="a4-inline-label" for="templatecreate-node">{'Node ID'|i18n( 'design/admin/visual/templatecreate' )}</label>:</td>
    <td><input id="templatecreate-node" type="text" size="5" value="{$override_keys['nodeID']}" name="Match[node]" /></td>
</tr>
</table>
</div>


<div class="block" role="radiogroup" aria-labelledby="templatecreate-base">
<label id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}:</label>
<label class="a4-inline-label"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked"/>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
<label class="a4-inline-label" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" />{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
<select name="TemplateSource" aria-labelledby="templatecreate-copy">
    <option value="">{'Automatic (class-specific override if available, otherwise source)'|i18n( 'design/admin/visual/templatecreate' )}</option>
    {foreach $default_template_sources as $source}
    <option value="{$source.path}">{$source.label}</option>
    {/foreach}
</select><br />
<label class="a4-inline-label"><input type="radio" name="TemplateContent" value="ContainerTemplate" />{'Container (with children)'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
<label class="a4-inline-label"><input type="radio" name="TemplateContent" value="ViewTemplate" />{'View (without children)'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
</div>

{/case}
{case match='object_view'}
<div class="objectheader">
<h2>{'Override keys'|i18n( 'design/admin/visual/templatecreate' )}</h2>
</div>

<div class="object">
<table>
<tr>
    <td>
    <p>
    <label class="a4-inline-label" for="templatecreate-class">{'Class'|i18n( 'design/admin/visual/templatecreate' )}</label>
    </p>
    </td>
    <td>
    <select id="templatecreate-class" name="Match[class_identifier]">
        <option value="-1">{'Any'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Class loop=fetch('content', 'class_list', hash('sorts', array()))}
        <option value="{$Class:item.identifier}">{$Class:item.name|wash}</option>
        {/section}
    </select>
    </td>
</tr>
<tr>
    <td>
    <p>
    <label class="a4-inline-label" for="templatecreate-section">{'Section'|i18n( 'design/admin/visual/templatecreate' )}</label>
    </p>
    </td>
    <td>
    <select id="templatecreate-section" name="Match[section]">
        <option value="-1">{'Any'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {section name=Section loop=fetch( 'content', 'section_list' )}
            <option value="{$:item.id}">{$:item.name|wash}</option>
        {/section}
    </select>
    </td>
</tr>
<tr>
    <td>
    <p>
    <label class="a4-inline-label" for="templatecreate-object">{'Object'|i18n( 'design/admin/visual/templatecreate' )}</label>
    </p>
    </td>
    <td>
    <input id="templatecreate-object" type="text" size="5" value="" name="Match[object]" />
    </td>
</tr>
</table>
</div>


<div class="objectheader">
<h2 id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}</h2>
</div>
<div class="object" role="radiogroup" aria-labelledby="templatecreate-base">

<label class="a4-inline-label"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked"/>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
<label class="a4-inline-label" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" />{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
<select name="TemplateSource" aria-labelledby="templatecreate-copy">
    <option value="">{'Automatic (class-specific override if available, otherwise source)'|i18n( 'design/admin/visual/templatecreate' )}</option>
    {foreach $default_template_sources as $source}
    <option value="{$source.path}">{$source.label}</option>
    {/foreach}
</select><br />
<label class="a4-inline-label"><input type="radio" name="TemplateContent" value="ViewTemplate" />{'View (without children)'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
</div>

{/case}
{case match='pagelayout'}
    <div class="objectheader">
    <h2 id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}</h2>
    </div>
    <div class="object" role="radiogroup" aria-labelledby="templatecreate-base">
    <label class="a4-inline-label"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked"/>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
    <label class="a4-inline-label" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" />{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
    <select name="TemplateSource" aria-labelledby="templatecreate-copy">
        <option value="">{'Automatic (source template)'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {foreach $default_template_sources as $source}
        <option value="{$source.path}">{$source.label}</option>
        {/foreach}
    </select><br />
    </div>
{/case}
{case}
    <div class="objectheader">
    <h2 id="templatecreate-base">{'Base template on'|i18n( 'design/admin/visual/templatecreate' )}</h2>
    </div>
    <div class="object" role="radiogroup" aria-labelledby="templatecreate-base">
    <label class="a4-inline-label"><input type="radio" name="TemplateContent" value="EmptyFile" checked="checked"/>{'Empty file'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
    <label class="a4-inline-label" id="templatecreate-copy"><input type="radio" name="TemplateContent" value="DefaultCopy" />{'Copy of default template'|i18n( 'design/admin/visual/templatecreate' )}</label><br />
    <select name="TemplateSource" aria-labelledby="templatecreate-copy">
        <option value="">{'Automatic (source template)'|i18n( 'design/admin/visual/templatecreate' )}</option>
        {foreach $default_template_sources as $source}
        <option value="{$source.path}">{$source.label}</option>
        {/foreach}
    </select><br />
    </div>
{/case}
{/switch}

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
    <div class="block">
        <input class="button" type="submit" name="CreateOverrideButton" value="{'OK'|i18n( 'design/admin/visual/templatecreate' )}" />
        <input class="button" type="submit" name="CancelOverrideButton" value="{'Cancel'|i18n( 'design/admin/visual/templatecreate' )}" />
    </div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>
