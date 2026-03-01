{let id=$class_attribute.id
     content=$class_attribute.content
     i18n_context="extension/enhancedselection2/class/edit"}

<fieldset>
    <legend>{"Option list"|i18n($i18n_context)}</legend>

    {section show=count($content.options)|gt(0)}
        <table class="list" cellspacing="0">
            <tr>
                <th style="width: 1%;">&nbsp;</th>
                <th>{"Name"|i18n($i18n_context)}</th>
                <th>{"Identifier"|i18n($i18n_context)}</th>
                <th>{"Priority"|i18n($i18n_context)}</th>
                <th style="width: 1%;">&nbsp;</th>
            </tr>

            {section var=option_row loop=$content.options}
                <tr>
                    <td>
                        <input type="checkbox"
                               name="ContentClass_sckenhancedselection_remove_{$id}[]"
                               value="{$option_row.index}" />
                    </td>

                    <td>
                        <input type="hidden"
                               name="ContentClass_sckenhancedselection_id_{$id}[]"
                               value="{$option_row.item.id}" />

                        <input type="text"
                               name="ContentClass_sckenhancedselection_name_{$id}[{$option_row.item.id}]"
                               value="{$option_row.item.name|wash}" />
                    </td>

                    <td>
                        <input type="text"
                               name="ContentClass_sckenhancedselection_identifier_{$id}[{$option_row.item.id}]"
                               value="{$option_row.item.identifier|wash}" />
                    </td>

                    <td>
                        <input type="text"
                               name="ContentClass_sckenhancedselection_priority_{$id}[{$option_row.item.id}]"
                               value="{$option_row.item.priority|wash}"
                               size="3" />
                    </td>

                    <td>
                        <div style="white-space: nowrap;">
                            {let upEnabled=$option_row.number|eq(1)|not
                                 downEnabled=$option_row.number|eq(count($content.options))|not
                                 upImage=cond($upEnabled,"button-move_up.gif","button-move_up-disabled.gif")
                                 downImage=cond($downEnabled,"button-move_down.gif","button-move_down-disabled.gif")}
                            {* Moving the first option up and the last option down doesn't make sense *}
                            <input type="image"
                                   src={$upImage|ezimage}
                                   name="CustomActionButton[{$id}_move_up]"
                                   value="{$option_row.index}"
                                   title="{'Move up'|i18n($i18n_context)}"
                                   {section show=$upEnabled|not}disabled="disabled"{/section} />

                            <input type="image"
                                   src={$downImage|ezimage}
                                   name="CustomActionButton[{$id}_move_down]"
                                   value="{$option_row.index}"
                                   title="{'Move down'|i18n($i18n_context)}"
                                   {section show=$downEnabled|not}disabled="disabled"{/section} />
                            {/let}
                        </div>
                    </td>
                </tr>
            {/section}
        </table>
    {/section}

    <div class="block">
        <input type="submit"
               class="button"
               value="{'New option'|i18n($i18n_context)}"
               name="CustomActionButton[{$id}_new_option]" />

        <input type="submit"
               {section show=count($content.options)|gt(0)}class="button"{section-else}disabled="disabled"{/section}
               value="{'Remove selected option(s)'|i18n($i18n_context)}"
               name="CustomActionButton[{$id}_remove_optionlist]" />

        {* Sorting 1 option doesn't make sense *}
        <input type="submit"
               {section show=count($content.options)|gt(1)}class="button"{section-else}disabled="disabled"{/section}
               value="{'Sort options'|i18n($i18n_context)}"
               name="CustomActionButton[{$id}_sort_optionlist]" />

        <select {section show=count($content.options)|le(1)}disabled="disabled"{/section}
                name="ContentClass_sckenhancedselection_sort_order_{$id}">
            <option value="alpha_asc">{"A-Z"|i18n($i18n_context)}</option>
            <option value="alpha_desc">{"Z-A"|i18n($i18n_context)}</option>
            <option value="prior_asc">{"Priority"|i18n($i18n_context)}</option>
        </select>
    </div>
</fieldset>

<fieldset>
    <legend>{"Field input settings"|i18n($i18n_context)}</legend>

    <div class="block">
        <div class="element">
            <label>{"Expanded"|i18n($i18n_context)}:</label>
            <input type="checkbox"
                   name="ContentClass_sckenhancedselection_expanded_{$id}"
                   {section show=$content.is_expanded}checked="checked"{/section} />
        </div>

        <div class="element">
            <label>{"Multiple"|i18n($i18n_context)}:</label>
            <input type="checkbox"
                   name="ContentClass_sckenhancedselection_multi_{$id}"
                   {section show=$content.is_multiselect}checked="checked"{/section} />
        </div>
    </div>
    <div class="block alert alert-info">
        <label>Input formats:</label>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Element Type</th>
                <th>Expanded</th>
                <th>Multiple</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Select (single choice)</td>
                <td>false</td>
                <td>false</td>
            </tr>
            <tr>
                <td>Select (multiple choices)</td>
                <td>false</td>
                <td>true</td>
            </tr>
            <tr>
                <td>Radio buttons</td>
                <td>true</td>
                <td>false</td>
            </tr>
            <tr>
                <td>Checkboxes</td>
                <td>true</td>
                <td>true</td>
            </tr>
            </tbody>
        </table>
    </div>
</fieldset>

<fieldset>
    <legend>{"Other settings"|i18n($i18n_context)}</legend>
    <div class="block">
        <div class="element">
            <label>{"Delimiter"|i18n($i18n_context)}:</label>
            <input type="text"
                   name="ContentClass_sckenhancedselection_delimiter_{$id}"
                   value="{$content.delimiter|wash}"
                   size="5" />
        </div>

        <div class="break"></div>
    </div>

    <div class="block">
        <label>{"Database query"|i18n($i18n_context)}:</label>
        <textarea rows="5"
                  cols="80"
                  name="ContentClass_sckenhancedselection_query_{$id}">{$content.query|wash}</textarea>
    </div>
</fieldset>

{/let}
