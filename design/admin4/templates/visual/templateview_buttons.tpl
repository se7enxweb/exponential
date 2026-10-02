{* The buttons of Design > Templates > template view, drawn above and below
   the overrides. The same names in both places: either posts the one form. *}
<div class="block">
    <div class="button-left">
    {if $own_overrides}
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
        <input class="button" type="submit" name="UpdateOverrideButton" value="{'Save conditions'|i18n( 'design/admin/visual/templateview' )}" title="{'Save the conditions edited above. The order is saved on its own, as you move the overrides.'|i18n( 'design/admin/visual/templateview' )}" />
        {else}
        <input class="button-disabled" type="submit" name="UpdateOverrideButton" value="{'Save conditions'|i18n( 'design/admin/visual/templateview' )}" disabled="disabled"/>
        {/if}
    </div>
    <div class="break"></div>
</div>
