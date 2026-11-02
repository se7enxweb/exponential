{* The pages of one target as tabs, and the siteaccesses it publishes. Variables: target (expStaticAdmin::overview(): id,
   permissions, siteaccesses), view (the current
   one). *}
<nav class="a4-section" aria-label="{'Pages of the target'|i18n( 'design/admin/staticpublish' )|wash}">
<ul class="a4-tabs">
{foreach array( hash( 'view', 'build', 'text', 'Build'|i18n( 'design/admin/staticpublish' ), 'show', $target.permissions.build ),
                hash( 'view', 'preview', 'text', 'Preview and publish'|i18n( 'design/admin/staticpublish' ), 'show', $target.permissions.read ),
                hash( 'view', 'history', 'text', 'History'|i18n( 'design/admin/staticpublish' ), 'show', $target.permissions.read ),
                hash( 'view', 'target', 'text', 'Settings'|i18n( 'design/admin/staticpublish' ), 'show', $target.permissions.setup ),
                hash( 'view', 'keys', 'text', 'Deploy key'|i18n( 'design/admin/staticpublish' ), 'show', $target.permissions.setup ) ) as $tab}
{if $tab.show}
    <li>{if eq( $tab.view, $view )}<span class="current" aria-current="page">{$tab.text|wash}</span>{else}<a href={concat( 'staticpublish/', $tab.view, '/', $target.id )|ezurl}>{$tab.text|wash}</a>{/if}</li>
{/if}
{/foreach}
</ul>
</nav>
{if $target.siteaccesses|count}<div class="a4-section"><span class="a4-meta">{'Siteaccesses'|i18n( 'design/admin/staticpublish' )}:</span> {include uri='design:staticpublish/parts/siteaccesses.tpl' members=$target.siteaccesses}</div>{/if}
