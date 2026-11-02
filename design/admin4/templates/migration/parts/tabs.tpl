{* The views of Setup > Migrations as tabs. Variables: view (the current one), can_run, can_rollback; Recording and Editor show for
   migration/edit; Remotes and Deploys (the deploys to remotes) for everyone who may see the migrations. *}
<nav class="a4-section" aria-label="{'Migrations'|i18n( 'design/admin/migration' )|wash}">
<ul class="a4-tabs">
{foreach array( hash( 'view', 'status', 'text', 'Status'|i18n( 'design/admin/migration' ), 'show', true() ),
                hash( 'view', 'history', 'text', 'History'|i18n( 'design/admin/migration' ), 'show', true() ),
                hash( 'view', 'dryrun', 'text', 'Dry run'|i18n( 'design/admin/migration' ), 'show', true() ),
                hash( 'view', 'run', 'text', 'Run'|i18n( 'design/admin/migration' ), 'show', $can_run ),
                hash( 'view', 'rollback', 'text', 'Rollback'|i18n( 'design/admin/migration' ), 'show', $can_rollback ),
                hash( 'view', 'recording', 'text', 'Recording'|i18n( 'design/admin/migration' ), 'show', fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'edit' ) ) ),
                hash( 'view', 'remotes', 'text', 'Remotes'|i18n( 'design/admin/migration' ), 'show', true() ),
                hash( 'view', 'deploy', 'text', 'Deploys'|i18n( 'design/admin/migration' ), 'show', true() ),
                hash( 'view', 'editor', 'text', 'Editor'|i18n( 'design/admin/migration' ), 'show', fetch( 'user', 'has_access_to', hash( 'module', 'migration', 'function', 'edit' ) ) ) ) as $tab}
{if $tab.show}
    <li>{if eq( $tab.view, $view )}<span class="current" aria-current="page">{$tab.text|wash}</span>{else}<a href={concat( 'migration/', $tab.view )|ezurl}>{$tab.text|wash}</a>{/if}</li>
{/if}
{/foreach}
</ul>
</nav>
