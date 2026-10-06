{* Why settings/edit did not save: validation_error, validation_error_type, validation_field (and, for a write
   error, path and filename) as the view sets them. Included by settings/edit.tpl inside .exp-settings.
   The same file is in design/admin and design/admin4. *}
{if $validation_error}
<div class="exp-feedback is-bad" role="alert">
<p><strong>{'The setting was not saved.'|i18n( 'design/admin/settings' )}</strong></p>
    {switch match=$validation_error_type}
    {case match='empty'}
        <p>{'%valfield is empty'|i18n( 'design/admin/settings',, hash( '%valfield', $validation_field ) )|wash}</p>
    {/case}
    {case match='variable_exists'}
        <p>{'Variable %valfield already exists in section %block'|i18n( 'design/admin/settings',, hash( '%valfield', $validation_field, '%block', $block ) )|wash}</p>
        <p>{'Choose another name that is not already in use'|i18n( 'design/admin/settings' )}</p>
    {/case}
    {case match='contain_spaces'}
        <p>{'%valfield is not allowed to contain spaces'|i18n( 'design/admin/settings' ,, hash( '%valfield', $validation_field ) )|wash}</p>
    {/case}
    {case match='write_error'}
        <p>{'Writing setting: %setting_name to file: %filename failed.'|i18n( 'design/admin/settings',, hash( '%setting_name', $setting_name, '%filename', $filename ) )|wash}</p>
        <p>{'A value with a line break, a NUL byte or */ is refused, because it would change other settings or end the PHP comment that hides the file. Otherwise make sure the web server may write to %path and try again.'|i18n( 'design/admin/settings',, hash( '%path', $path ) )|wash}</p>
    {/case}
    {case match='not_valid_name'}
        <p>{'Name contains illegal character(s).'|i18n( 'design/admin/settings' )}</p>
        <p>{'A name can contain letters, digits and _ * @ -.'|i18n( 'design/admin/settings' )}</p>
    {/case}
    {case match='not_valid_placement'}
        <p>{'Choose where to save the setting: the siteaccess, the global override or an active extension.'|i18n( 'design/admin/settings' )}</p>
    {/case}
    {case match='read_only'}
        <p>{'This setting is read only (site.ini [eZINISettings] ReadonlySettingList) and cannot be changed here.'|i18n( 'design/admin/settings' )}</p>
    {/case}
    {case match='not_string'}
        <p>{'%valfield does not contain a valid string.'|i18n( 'design/admin/settings',, hash( '%valfield', $validation_field ) )|wash}</p>
        <p>{'If the string is all numbers use the numeric type instead.'|i18n( 'design/admin/settings' )}</p>
    {/case}
    {case match='not_numeric'}
        <p>{'%valfield does not contain a valid numeric'|i18n( 'design/admin/settings',, hash( '%valfield', $validation_field ) )|wash}</p>
        <p>{'A valid numeric can only contain 0-9 and one . (dot). '|i18n( 'design/admin/settings' )|wash}</p>
    {/case}
    {case match='not_array'}
        <p>{'%valfield does not contain valid array.'|i18n( 'design/admin/settings',, hash( '%valfield', $validation_field ) )|wash}</p>
        <p>{'A key cannot contain [ or ].'|i18n( 'design/admin/settings' )}</p>
    {/case}
    {case}
        <p>{$validation_error_message|wash}</p>
    {/case}
    {/switch}
</div>
{/if}
