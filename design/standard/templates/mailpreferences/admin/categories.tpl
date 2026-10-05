{* mailpreferences/admin/categories: the categories of e-mail. Categories from mailpreferences.ini and from extensions
   are shown as they are; their name and description can be changed here, which is stored as an administrator's row
   that takes precedence. New categories made here are always optional and always off until a person turns them on.
   Variables: categories (hash( identifier, name, description, essential, frequencies, double_opt_in, source,
   editable, removable )), edit (the category being edited, the same hash, or false), form (the values typed, after an
   error), notice, frequency_names (hash( immediate, daily, weekly => label )). *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences: categories'|i18n( 'design/admin/mailpreferences' )
         intro='Each e-mail the system sends belongs to a category. People turn optional categories on and off on their preference page; essential ones are always sent and are listed there with their description.'|i18n( 'design/admin/mailpreferences' )
         crumb=false() wide=true() admin_tab='categories'}
{def $source_names = hash( 'ini', 'Settings file'|i18n( 'design/admin/mailpreferences' ),
                           'admin', 'Made here'|i18n( 'design/admin/mailpreferences' ),
                           'extension', 'Extension'|i18n( 'design/admin/mailpreferences' ) )}

{include uri='design:mailpreferences/parts/notice.tpl' notice=first_set( $notice, false() )}

<section class="mp-card">
    <h2>{'Categories'|i18n( 'design/admin/mailpreferences' )}</h2>
    <div class="mp-scroll">
    <table class="mp-table mp-stack">
        <thead><tr>
            <th scope="col">{'Name'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Kind'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'How often'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Confirmation'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Defined in'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col"><span class="mp-sr">{'Actions'|i18n( 'design/admin/mailpreferences' )}</span></th>
        </tr></thead>
        <tbody>
{foreach $categories as $category}
        <tr>
            <td data-label="{'Name'|i18n( 'design/admin/mailpreferences' )}"><b>{$category.name|wash}</b> <code>{$category.identifier|wash}</code><br /><span class="mp-wording">{$category.description|wash}</span></td>
            <td data-label="{'Kind'|i18n( 'design/admin/mailpreferences' )}">{if $category.essential}<span class="mp-badge">{'Essential, always sent'|i18n( 'design/admin/mailpreferences' )}</span>{else}<span class="mp-badge on">{'Optional, off until turned on'|i18n( 'design/admin/mailpreferences' )}</span>{/if}</td>
            <td data-label="{'How often'|i18n( 'design/admin/mailpreferences' )}">{if $category.frequencies|count|gt( 0 )}{foreach $category.frequencies as $frequency}{if is_set( $frequency_names[$frequency] )}{$frequency_names[$frequency]}{else}{$frequency|wash}{/if}{delimiter}, {/delimiter}{/foreach}{else}&mdash;{/if}</td>
            <td data-label="{'Confirmation'|i18n( 'design/admin/mailpreferences' )}">{if $category.double_opt_in}{'Double opt-in'|i18n( 'design/admin/mailpreferences' )}{else}&mdash;{/if}</td>
            <td data-label="{'Defined in'|i18n( 'design/admin/mailpreferences' )}">{if is_set( $source_names[$category.source] )}{$source_names[$category.source]}{else}{$category.source|wash}{/if}</td>
            <td>
{if $category.editable}
                <a class="mp-btn small" href={concat( 'mailpreferences/admin/categories/', $category.identifier )|ezurl}>{'Edit'|i18n( 'design/admin/mailpreferences' )}<span class="mp-sr"> {$category.name|wash}</span></a>
{/if}
            </td>
        </tr>
{/foreach}
        </tbody>
    </table>
    </div>
</section>

{def $values = cond( $edit, $edit, first_set( $form, false() ), first_set( $form, false() ), hash( 'identifier', '', 'name', '', 'description', '', 'essential', false(), 'frequencies', array(), 'double_opt_in', false() ) )}
<section class="mp-card" id="mp-category-form">
    <h2>{if $edit}{'Edit "%name"'|i18n( 'design/admin/mailpreferences',, hash( '%name', $edit.name ) )|wash}{else}{'New category'|i18n( 'design/admin/mailpreferences' )}{/if}</h2>
{if and( $edit, $edit.source|ne( 'admin' ) )}
    <p class="mp-lead">{'This category is defined in %source. What you change here is stored in the database and shown instead; the identifier and the kind stay as they are defined.'|i18n( 'design/admin/mailpreferences',, hash( '%source', cond( is_set( $source_names[$edit.source] ), $source_names[$edit.source], $edit.source ) ) )|wash}</p>
{elseif $edit|not}
    <p class="mp-lead">{'A new category is optional: nobody receives it until they turn it on. Code sends e-mail in it with eZMail::setCategory( identifier ).'|i18n( 'design/admin/mailpreferences' )}</p>
{/if}
    <form method="post" action={'mailpreferences/admin/categories'|ezurl}>
{if $edit}
        <input type="hidden" name="Identifier" value="{$edit.identifier|wash}" />
        <p class="mp-hint">{'Identifier:'|i18n( 'design/admin/mailpreferences' )} <code>{$edit.identifier|wash}</code></p>
{else}
        <div class="mp-field">
            <label for="mp-cat-identifier">{'Identifier'|i18n( 'design/admin/mailpreferences' )}</label>
            <input type="text" id="mp-cat-identifier" name="Identifier" value="{$values.identifier|wash}" pattern="[a-z][a-z0-9_]*" maxlength="64" required="required" aria-describedby="mp-cat-identifier-hint" />
            <span class="mp-hint" id="mp-cat-identifier-hint">{'Lower case letters, digits and underscores; it cannot be changed later.'|i18n( 'design/admin/mailpreferences' )}</span>
        </div>
{/if}
        <div class="mp-field">
            <label for="mp-cat-name">{'Name'|i18n( 'design/admin/mailpreferences' )}</label>
            <input type="text" id="mp-cat-name" name="Name" value="{$values.name|wash}" maxlength="255" required="required" />
        </div>
        <div class="mp-field">
            <label for="mp-cat-description">{'Description, as people see it on their preference page'|i18n( 'design/admin/mailpreferences' )}</label>
            <textarea id="mp-cat-description" name="Description" rows="3">{$values.description|wash}</textarea>
        </div>
{if or( $edit|not, $edit.source|eq( 'admin' ) )}
        <fieldset class="mp-field" style="border:0;padding:0;">
            <legend class="mp-label">{'How often people may choose to get it'|i18n( 'design/admin/mailpreferences' )}</legend>
{foreach $frequency_names as $frequency => $label}
            <label class="mp-check"><input type="checkbox" name="Frequencies[]" value="{$frequency|wash}"{if $values.frequencies|contains( $frequency )} checked="checked"{/if} /> <span>{$label|wash}</span></label>
{/foreach}
            <span class="mp-hint">{'None ticked: it is sent when it happens, with no choice of frequency.'|i18n( 'design/admin/mailpreferences' )}</span>
        </fieldset>
        <label class="mp-check"><input type="checkbox" name="DoubleOptIn" value="1"{if $values.double_opt_in} checked="checked"{/if} /> <span>{'Ask for a confirmation by e-mail before the first message (double opt-in; use it for newsletters and marketing)'|i18n( 'design/admin/mailpreferences' )}</span></label>
{/if}
        <div class="mp-actions">
            <input class="mp-btn primary" type="submit" name="{if $edit}StoreCategoryButton{else}CreateCategoryButton{/if}" value="{if $edit}{'Save'|i18n( 'design/admin/mailpreferences' )}{else}{'Create category'|i18n( 'design/admin/mailpreferences' )}{/if}" />
{if $edit}
            <a class="mp-btn" href={'mailpreferences/admin/categories'|ezurl}>{'Cancel'|i18n( 'design/admin/mailpreferences' )}</a>
{if $edit.source|ne( 'admin' )}
            <input class="mp-btn" type="submit" name="ResetCategoryButton" value="{'Use the defined name and description again'|i18n( 'design/admin/mailpreferences' )}" />
{elseif $edit.removable}
            <input class="mp-btn" type="submit" name="RemoveCategoryButton" value="{'Remove this category'|i18n( 'design/admin/mailpreferences' )}" />
{/if}
{/if}
        </div>
    </form>
</section>
{undef $values $source_names}

{include uri='design:mailpreferences/parts/page_end.tpl'}
