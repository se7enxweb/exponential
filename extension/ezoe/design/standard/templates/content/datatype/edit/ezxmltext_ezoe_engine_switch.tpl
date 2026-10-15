{* The editor engine switch below the editor, shown when ezoe.ini [EditorSettings] EngineSwitch=enabled and more than
   one engine of the registry ([EditorSettings] Engines[]) is available: one group named "Editor" with every engine
   in the order of the registry, a segmented control of toggle buttons (the pattern of admin4's width switch). The
   engine in use is the pressed button (aria-pressed="true", type="button": pressing it again changes nothing); each
   other one is a submit button of the edit form, CustomActionButton[<attribute id>_switch_engine_<engine>], not
   pressed. Every engine is in the Tab order, so a screen reader names the group, the engine and its state.
   content/edit stores the draft with the text typed so far, then the input handler saves the choice as the user
   preference ezoe_engine. admin4 draws the group as a segmented control (modules/content.css); other designs show
   the label and one button per engine, the one in use filled. *}
{if $input_handler.engine_switch_enabled}
{def $ezoe_engine_options = $input_handler.engine_switch_options}
{if gt( $ezoe_engine_options|count, 1 )}
<div class="ezoe-engine-switch" role="group" aria-labelledby="ezoe-engine-switch-label-{$attribute.id}">
    <span class="ezoe-engine-switch-label" id="ezoe-engine-switch-label-{$attribute.id}">{'Editor'|i18n( 'design/standard/ezoe' )|wash}</span>
    <span class="ezoe-engine-switch-options">
    {foreach $ezoe_engine_options as $ezoe_engine_option}
        {if $ezoe_engine_option.current}
        <button class="button ezoe-engine-switch-option current" type="button" aria-pressed="true">{$ezoe_engine_option.label|wash}</button>
        {else}
        <button class="button ezoe-engine-switch-option" type="submit" name="CustomActionButton[{$attribute.id}_switch_engine_{$ezoe_engine_option.identifier|wash}]" value="{$ezoe_engine_option.identifier|wash}" aria-pressed="false" title="{'Switch to the editor %engine'|i18n( 'design/standard/ezoe',, hash( '%engine', $ezoe_engine_option.label ) )|wash}: {'The text is kept, the choice is saved for your user.'|i18n( 'design/standard/ezoe' )|wash}">{$ezoe_engine_option.label|wash}</button>
        {/if}
    {/foreach}
    </span>
</div>
{/if}
{undef $ezoe_engine_options}
{/if}
