{let content=$class_attribute.content
     i18n_context="extension/enhancedselection2/class/view"
     field_input_type = "Select (single choice)"}

<label>{"Option list"|i18n($i18n_context)}:</label>
<table class="list" cellspacing="0">
    <tr>
        <th style="width: 1%;">&nbsp;</th>
        <th>{"Name"|i18n($i18n_context)}</th>
        <th>{"Identifier"|i18n($i18n_context)}</th>
    </tr>

    {section var=option loop=$content.options}
    <tr>
        <td>{$option.number}.</td>
        <td>{first_set($option.item.name|wash,"&nbsp;")}</td>
        <td>{first_set($option.item.identifier|wash,"&nbsp;")}</td>
    </tr>
    {/section}
</table>

{if $content.is_expanded}
    {if $content.is_multiselect}
        {set $field_input_type = 'Checkboxes'}
    {else}
        {set $field_input_type = 'Radio buttons'}
    {/if}
{else}
    {if $content.is_multiselect}
        {set $field_input_type = 'Select (multiple choices)'}
    {else}
        {set $field_input_type = 'Select (single choice)'}
    {/if}
{/if}
<div class="block">
    <legend>{"Field input settings"|i18n($i18n_context)}</legend>
    <p><strong>{"Selected input format"|i18n($i18n_context)}:</strong> {$field_input_type|i18n($i18n_context)}</p>

    <div class="element">
        <label>{"Expand choices"|i18n($i18n_context)}:</label>
        <p>{cond($content.is_expanded,"Yes"|i18n($i18n_context),"No"|i18n($i18n_context))}</p>
    </div>

    <div class="element">
        <label>{"Multiple choice"|i18n($i18n_context)}:</label>
        <p>{cond($content.is_multiselect,"Yes"|i18n($i18n_context),"No"|i18n($i18n_context))}</p>
    </div>
</div>

<div class="block">
    <legend>{"Other settings"|i18n($i18n_context)}</legend>
    <div class="element">
        <label>{"Delimiter"|i18n($i18n_context)}:</label>
        <p style="white-space: pre;">'{$content.delimiter|wash}'</p>
    </div>

    <div class="break"></div>

    <div class="element">
        <label>{"Database query"|i18n($i18n_context)}:</label>
        <p>{$content.query|wash|nl2br}</p>
    </div>
</div>

{/let}
