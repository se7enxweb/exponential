{* The buttons of Design > Templates > template view, drawn above and below
   the overrides. The same names in both places: either posts the one form. *}
<div class="a4-actions">
    {if $own_overrides}
    <input class="a4-btn a4-btn-danger" type="submit" name="RemoveOverrideButton" value="{'Remove selected'|i18n( 'design/admin/visual/templateview' )|wash}" title="{'Remove selected template overrides.'|i18n( 'design/admin/visual/templateview' )|wash}" />
    {else}
    <input class="a4-btn" type="submit" name="RemoveOverrideButton" value="{'Remove selected'|i18n( 'design/admin/visual/templateview' )|wash}" disabled="disabled" />
    {/if}

    {if and( $new_override_allowed, fetch( 'user', 'can_open', hash( 'uri', 'visual/templatecreate' ) ) )}
    <input class="a4-btn" type="submit" name="NewOverrideButton" value="{'New override'|i18n( 'design/admin/visual/templateview' )|wash}" title="{'Create a new template override.'|i18n( 'design/admin/visual/templateview' )|wash}" />
    {/if}
</div>
<div class="a4-actions">
    {if $template_settings.custom_match}
    <input class="a4-btn a4-btn-primary" type="submit" name="UpdateOverrideButton" value="{'Save conditions'|i18n( 'design/admin/visual/templateview' )|wash}" title="{'Save the conditions edited above. The order is saved on its own, as you move the overrides.'|i18n( 'design/admin/visual/templateview' )|wash}" />
    {else}
    <input class="a4-btn a4-btn-primary" type="submit" name="UpdateOverrideButton" value="{'Save conditions'|i18n( 'design/admin/visual/templateview' )|wash}" disabled="disabled" />
    {/if}
</div>
