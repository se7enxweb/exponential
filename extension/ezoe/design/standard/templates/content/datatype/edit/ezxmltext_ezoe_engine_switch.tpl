{* Buttons to switch to another editor engine of the registry (ezoe.ini [EditorSettings] Engines[]) when
   EngineSwitch=enabled. The text is kept, the choice is stored as the user preference ezoe_engine. *}
{if $input_handler.engine_switch_enabled}
    {foreach $input_handler.engine_switch_choices as $choice}
        <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_switch_engine_{$choice.identifier|wash}]" value="{'Switch to the editor %engine'|i18n( 'design/standard/ezoe',, hash( '%engine', $choice.label ) )}" title="{'The text is kept, the choice is saved for your user.'|i18n('design/standard/ezoe')}" />
    {/foreach}
{/if}
