{* The links of the extensions' audit sources on a view of the audit console (audit.ini [AuditSourceSettings]
   Sources[], expAuditSource::links()): one line per source the user may see. Nothing without a registered source.
   Input: audit_sources (expAuditConsole::sourceLinks(): identifier, label, channel, links: label, url). *}
{if and( is_set( $audit_sources ), $audit_sources|count )}
<div class="a4-audit-sources">
{foreach $audit_sources as $s}
    <p class="a4-audit-source" id="au-source-{$s.identifier|wash}"><strong>{$s.label|wash}</strong>{if $s.channel} <span class="a4-audit-sub">({'channel'|i18n( 'design/admin/audit' )} <code>{$s.channel|wash}</code>)</span>{/if}:
    {foreach $s.links as $l}<a href={$l.url|ezurl}>{$l.label|wash}</a>{delimiter} &middot; {/delimiter}{/foreach}</p>
{/foreach}
</div>
{/if}
