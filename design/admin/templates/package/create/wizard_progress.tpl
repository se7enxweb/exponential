{* Where the package creation wizard stands: the steps of this wizard (the steps its checks leave in,
   $creator.current_steps), the current one marked aria-current="step", the step's heading and what it asks for, and
   the errors of the last Continue.

   Parameters: intro (the step's explanation, optional), field_ids (optional hash: the field name an error names, as
   the creator writes it, => the id of the control it belongs to; such an error links to its control), field_labels
   (optional hash: such a field name => the label shown, for a creator that names a field by its POST name). The same file
   is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $wz_steps = $creator.current_steps
     $wz_number = 1
     $wz_state = 'done'
     $wz_ids = first_set( $field_ids, hash() )
     $wz_labels = first_set( $field_labels, hash() )}
{foreach $wz_steps as $wz_index => $wz_step}{if $wz_step.id|eq( $current_step.id )}{set $wz_number = $wz_index|inc}{/if}{/foreach}

<nav class="a4-package-wizard-nav" aria-label="{'Steps of the wizard'|i18n( 'design/admin/package' )}">
<ol class="a4-steps a4-package-wizard-steps">
{foreach $wz_steps as $wz_step}
    {if $wz_step.id|eq( $current_step.id )}
    <li class="is-current" aria-current="step"><span class="a4-package-step-name">{$wz_step.name|wash}</span></li>
    {set $wz_state = 'todo'}
    {elseif $wz_state|eq( 'done' )}
    <li class="is-done"><span class="a4-package-step-name">{$wz_step.name|wash}<span class="a4-sr-only"> ({'done'|i18n( 'design/admin/package' )})</span></span></li>
    {else}
    <li><span class="a4-package-step-name">{$wz_step.name|wash}</span></li>
    {/if}
{/foreach}
</ol>
</nav>

<div class="a4-section-head a4-package-wizard-step">
    <p class="a4-meta">{'Step %number of %total'|i18n( 'design/admin/package',, hash( '%number', $wz_number, '%total', $wz_steps|count ) )}</p>
    <h2 class="a4-h2" id="a4-package-step-title">{$current_step.name|wash}</h2>
    {if is_set( $intro )}<p class="a4-intro">{$intro}</p>{/if}
</div>

{if $error_list|count|gt( 0 )}
<div class="a4-notice is-bad a4-error-summary" id="a4-error-summary" role="alert" tabindex="-1" aria-labelledby="a4-error-summary-title">
    <p class="a4-notice-title" id="a4-error-summary-title">{'Please correct the following before you continue:'|i18n( 'design/admin/package' )}</p>
    <ul>
    {foreach $error_list as $wz_error}
        <li>{if and( $wz_error.field, is_set( $wz_ids[$wz_error.field] ) )}<a href="#{$wz_ids[$wz_error.field]|wash}">{first_set( $wz_labels[$wz_error.field], $wz_error.field )|wash}</a>: {elseif $wz_error.field}<em class="field">{$wz_error.field|wash}</em>: {/if}{$wz_error.description|wash}</li>
    {/foreach}
    </ul>
</div>
{* the summary takes the focus, so a screen reader and the keyboard start at the errors *}
{literal}<script type="text/javascript">(function(){ var s = document.getElementById( "a4-error-summary" ); if ( s ) s.focus(); })();</script>{/literal}
{/if}
{undef $wz_steps $wz_number $wz_state $wz_ids $wz_labels}
