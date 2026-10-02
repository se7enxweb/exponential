{* The licence drop-down of the RAD extension wizards (Setup > RAD): the same strict choice
   from package.ini [LicenseSettings] as the package creation wizards, grouped the same way
   (eZPackageLicense::groupedList(), handed over as $groups). Every wizard's problems() refuses
   any other value, so this list is a convenience and not the check.
   Parameters: select_id, selected (the settings' licence identifier), groups. *}
<select id="{$select_id|wash}" name="licence">
{foreach $groups as $rad_licence_group}
    <optgroup label="{$rad_licence_group.name|wash}">
    {foreach $rad_licence_group.licenses as $rad_licence}
        <option value="{$rad_licence.identifier|wash}"{if eq( $selected, $rad_licence.identifier )} selected="selected"{/if}>{$rad_licence.name|wash} ({$rad_licence.identifier|wash})</option>
    {/foreach}
    </optgroup>
{/foreach}
</select>
