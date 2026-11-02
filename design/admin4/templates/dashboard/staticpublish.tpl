{* The dashboard block "staticpublish" (dashboard.ini [DashboardBlock_staticpublish], shown only with staticpublish/read):
   the targets with their stale pages, a build to check, the job going on and the last publish, with links to Setup >
   Static publishing. Without a target, one line that invites to the wizard and the tutorial; each user can hide it
   (the preference staticpublish_invite). Drawn with the shared components: no CSS in the template. *}
{* The module name is a variable, so the fetch is not compiled into a direct class call: a server process started
   before the class existed (a Velocity worker before its restart) gets an empty block, not an error. *}
{def $staticpublish_module = 'staticpublish'
     $sp = fetch( $staticpublish_module, 'summary' )}
{if $sp|count}
{if $sp.invite}
{if ne( ezpreference( 'staticpublish_invite' ), '0' )}
<div>
<h2>{'Static publishing'|i18n( 'design/admin/dashboard/staticpublish' )}</h2>
<p class="a4-notice is-info" role="status">{'Publish a free static copy of your site on GitHub Pages:'|i18n( 'design/admin/dashboard/staticpublish' )}
    {if $sp.can_setup}<a href={'staticpublish/target/new'|ezurl}>{'make a target'|i18n( 'design/admin/dashboard/staticpublish' )}</a> &middot; {/if}<a href={'exphelp/viewer/guides/host-static-sites-on-github-pages.md'|ezurl}>{'read the tutorial'|i18n( 'design/admin/dashboard/staticpublish' )}</a>
    &middot; <a href={'user/preferences/set/staticpublish_invite/0'|ezurl}>{'hide this'|i18n( 'design/admin/dashboard/staticpublish' )}</a></p>
</div>
{/if}
{elseif or( $sp.targets|count, $sp.problem )}
<div>
<h2>{'Static publishing'|i18n( 'design/admin/dashboard/staticpublish' )}</h2>
{if $sp.problem}<p class="a4-notice is-bad" role="alert">{'The targets could not be read: %problem'|i18n( 'design/admin/dashboard/staticpublish',, hash( '%problem', $sp.problem ) )|wash}</p>{/if}
{if $sp.enabled|not}<p class="a4-notice is-warn" role="status">{'Static publishing is switched off.'|i18n( 'design/admin/dashboard/staticpublish' )}</p>{/if}
<div class="a4-table-wrap" role="region" aria-label="{'Static publishing'|i18n( 'design/admin/dashboard/staticpublish' )|wash}" tabindex="0">
<table class="a4-table">
    <thead><tr>
        <th scope="col">{'Target'|i18n( 'design/admin/dashboard/staticpublish' )}</th>
        <th scope="col">{'Stale pages'|i18n( 'design/admin/dashboard/staticpublish' )}</th>
        <th scope="col">{'Last publish'|i18n( 'design/admin/dashboard/staticpublish' )}</th>
    </tr></thead>
    <tbody>
    {foreach $sp.targets as $t}
    <tr>
        <td><a href={concat( 'staticpublish/preview/', $t.id )|ezurl}>{$t.name|wash}</a>
            {if $t.job}<br /><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'a job is going on'|i18n( 'design/admin/dashboard/staticpublish' )}</span>
            {elseif $t.pending_build}<br /><span class="a4-badge is-warn">{'build to check'|i18n( 'design/admin/dashboard/staticpublish' )}</span>{/if}</td>
        <td>{if $t.stale}<span class="a4-badge is-warn">{$t.stale|wash}</span>{else}0{/if}</td>
        <td>{if $t.last_run}{$t.last_run.started_at|l10n( 'shortdatetime' )} {include uri='design:staticpublish/parts/run_state.tpl' run=$t.last_run}{else}<span class="a4-muted">{'never'|i18n( 'design/admin/dashboard/staticpublish' )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
</div>
<p class="a4-card-body"><a href={'staticpublish/dashboard'|ezurl}>{'All targets'|i18n( 'design/admin/dashboard/staticpublish' )}</a></p>
</div>
{/if}
{/if}
{undef $staticpublish_module $sp}
