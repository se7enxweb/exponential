{* The dashboard block "moderation" (dashboard.ini [DashboardBlock_moderation], shown only with moderation/read): how many
   objects are held for review and how many were created today, with links to Dashboard > Recent content (moderation/recent)
   filtered on them. Drawn with the administration's shared components (components.css): no CSS in the template. *}
{* The module name is a variable, so the fetch is not compiled into a direct class call: a server process started
   before the class existed (a Velocity worker before its restart) gets an empty block, not an error. *}
{def $moderation_module = 'moderation'
     $moderation = fetch( $moderation_module, 'summary' )}
{if $moderation|count}
<div>
<h2>{'Recent content'|i18n( 'design/admin/dashboard/moderation' )}</h2>

{if $moderation.problem}
<p class="a4-notice is-bad" role="alert">{'The counts could not be read: %problem'|i18n( 'design/admin/dashboard/moderation',, hash( '%problem', $moderation.problem ) )|wash}</p>
{/if}

<dl class="a4-facts">
    <div><dt>{'Held for review'|i18n( 'design/admin/dashboard/moderation' )}</dt>
        <dd>{if $moderation.held}<a href={'/moderation/recent/(held)/yes'|ezurl}><span class="a4-badge is-warn">{$moderation.held|wash}</span></a>{else}0{/if}</dd></div>
    <div><dt>{'New today'|i18n( 'design/admin/dashboard/moderation' )}</dt>
        <dd>{if $moderation.new_today}<a href={concat( '/moderation/recent/(from)/', $moderation.today, '/(date)/published' )|ezurl}>{$moderation.new_today|wash}</a>{else}0{/if}</dd></div>
</dl>
{if $moderation.ready|not}<p class="a4-meta">{'The review hold is not set up yet (the upgrade step 6.0.15-review-hold).'|i18n( 'design/admin/dashboard/moderation' )}</p>{/if}
<p class="a4-card-body"><a href={'/moderation/recent'|ezurl}>{'All recent content'|i18n( 'design/admin/dashboard/moderation' )}</a></p>
</div>
{/if}
{undef $moderation_module $moderation}
