<form name="eZExtract" method="post" action={'xrowextract/csv'|ezurl}>

{def $types = array()}
<div class="context-block">

    {* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

    <h1 class="context-title">{'Extract settings'|i18n('design/standard/xrowextract')}</h1>

    {* DESIGN: Mainline *}<div class="header-mainline"></div>

    {* DESIGN: Header END *}</div></div></div></div></div></div>

    {* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

    <div class="context-attributes">

    {def $subtree_node=fetch(content,node,hash(node_id,$Subtree))}


        <fieldset>
            <legend>{'Data selection'|i18n('design/standard/xrowxrowextract')}</legend>
            {if $has_prefilledata|not}
            <div class="block">
                <div class="element">
                    <label for="BrowseSubtree">{'Node'|i18n('design/standard/xrowxrowextract')}</label>
                    {node_view_gui view=line content_node=$subtree_node}
                    <input class="button" type="submit" name="BrowseSubtree" value="{'Change'|i18n('design/standard/xrowextract')}" />
                    <input name="Subtree" type="hidden" id="Subtree" value="{$Subtree}" />
                </div>
                <div class="element">
                    {set $types=array( hash( 'id', 'tree', 'name', ' Tree (full subtree)'), hash( 'id', 'list', 'name', 'List (only children)' ) )}
                <label>{'Depth'|i18n('design/standard/xrowextract')}</label>
                    <select name="type">

                    {section loop=$types}
                        <option value="{$:item.id}" {section show=$:item.id|eq($Type)} selected{/section}>{$:item.name|wash}</option>
                        {/section}
                    </select>
                </div>
                <div class="element">
                {set $types=array( hash( 'id', '0', 'name', 'No'), hash( 'id', '1', 'name', 'Yes' ) )}
                <label>{'Mainnode only'|i18n('design/standard/xrowextract')}</label>
                    <select name="mainnodeonly">
                    {section loop=$types}
                        <option value="{$:item.id}" {section show=$:item.id|eq($Mainnodeonly)} selected{/section}>{$:item.name|wash}</option>
                        {/section}
                    </select>
                </div>
            </div>
            <div class="block">
                <div class="element">
                    <label>{'Limit'|i18n('design/standard/xrowextract')} ( max {$max_count} )</label>
                    <input name="Limit" type="text" id="Limit" value="{$Limit}" />
                </div>
                <div class="element">
                    <label>{'Offset'|i18n('design/standard/xrowextract')}</label>
                    <input name="Offset" type="text" id="Offset" value="{$Offset}" />
                </div>
            </div>
        {else}
        <input class="button" type="submit" name="RemoveData" value="{'Remove current pre filled selection'|i18n('design/standard/xrowextract')}" />
        {/if}
        </fieldset>

        <div class="break"></div>
        <fieldset>
            <legend>{'Export format options'|i18n('design/standard/xrowextract')}</legend>
            <div class="block">
                <div class="element">
                    <label>{'Column separator'|i18n('design/standard/xrowextract')}</label>
                    <input name="Separator" type="text" id="Separator" value="{$Separator}" />
                    <p>
                        {"Info: Excel likes a semicolon as separator."|i18n('design/standard/xrowextract')}
                    </p>
                </div>
                <div class="element">
                    <label>{'Row separator'|i18n('design/standard/xrowextract')}</label>
                    <select name="LineSeparator">
                        {section loop=$LineSeparatorArray}
                        <option value="{$:item.id}" {section show=$:item.id|eq($LineSeparator)} selected{/section}>{$:item.name|wash}</option>
                        {/section}
                    </select>
                </div>
                <div class="element">
                {set $types=array( hash( 'id', '1', 'name', 'Yes'), hash( 'id', '0', 'name', 'No' ) )}
                <label>{'Escape'|i18n('design/standard/xrowextract')}</label>
                    <select name="Escape">
                    {section loop=$types}
                        <option value="{$:item.id}" {section show=$:item.id|eq( $Escape )} selected{/section}>{$:item.name}</option>
                        {/section}
                    </select>
                    <p>"No" is not <a href="http://tools.ietf.org/html/rfc4180">RFC</a> conform, <br /> removal of CR and LF from field value</p>
                </div>
            </div>
        </fieldset>
        <div class="break"></div>
        <fieldset>
            <legend>{'Selected class'|i18n('design/standard/xrowextract')}</legend>
            <div class="block">
                <div class="element">
                    <select name="Class_id"{if $has_prefilledata} disabled{/if}>
                    {if ezini('ExportSettings','ExportClasses','export.ini')}
                    {def $classes=fetch('class', 'list', hash('class_filter', ezini('ExportSettings','ExportClasses','export.ini')))}
                    {else}
                    {def $classes=fetch( 'class', 'list' )}
                    {/if}
                        {foreach $classes as $class }
                        <option value="{$class.id}" {if $class.id|eq($Class_id)} selected{/if}>{$class.name|wash}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="element">
                    <input class="button" name="Update" type="submit" value="{'Update'|i18n('design/standard/xrowextract')}" />
                </div>
                <div class="element">
                    <select name="AddAttributeID">
                        {section loop=fetch('class', 'attribute_list', hash('class_id', $Class_id)) }
                        <option value="{$:item.id}" {section show=$:item.id|eq(0)} selected{/section}>{$:item.name}</option>
                        {/section}
                        <option disabled>--- Special Attributes ---</option>
                        {section loop=$ExtraAttributes}
                        <option value="{$:item.id}">{$:item.name|wash}</option>
                        {/section}
                    </select>
                </div>
                <div class="element">
                    <input class="button" name="AddAttribute" type="submit" value="{'Add attribute'|i18n('design/standard/xrowextract')}" />
                </div>
            </div>
        </fieldset>
        {if $Attributes|gt(0)}
        <div class="break"></div>
        <fieldset>
            <legend>{'Selected attribute(s)'|i18n('design/standard/xrowextract')}</legend>
            <div class="content-navigation-childlist">
                <table class="list" cellspacing="0">
                    <tr>
                        <th class="tight">{'Position'|i18n('design/standard/xrowextract')}</th>
                        <th>{'Name'|i18n('design/standard/xrowextract')}</th>
                        <th class="tight">{'Identifier'|i18n('design/standard/xrowextract')}</th>
                        <th class="tight">&nbsp;</th>
                    </tr>
                    {section loop=$Attributes}
                    <tr>
                        <th scope="row">{1|sum($:index)}</th>
                        <td>
                            <input name="Attributes[{$:index}][id]" type="hidden" value="{$:item.id}" />
                            <input name="Attributes[{$:index}][name]" type="hidden" value="{$:item.name|wash}" />
                            {$:item.name}
                        </td>
                        <td><input name="Attributes[{$:index}][exportname]" type="text" value="{$:item.exportname|wash}" /></td>
                        <td><input type="checkbox" name="RemoveIDArray[]" value="{$:index}" /></td>
                    </tr>
                    {/section}
                </table>
            </div>
            <div class="block">
                <div class="right">
                    <input class="button" name="Remove" type="submit" value="{'Remove selected attribute(s)'|i18n('design/standard/xrowextract')}" />
                </div>
            </div>
        </fieldset>
        {/if}
    </div>

    {* DESIGN: Content END *}</div></div></div>

    <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
        <div class="block">
            <input class="button" name="Download" type="submit" value="{'Download'|i18n('design/standard/xrowextract')}" />
        </div>
    {* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>
</div>

</form>