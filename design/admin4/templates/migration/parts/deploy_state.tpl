{* The state of a deploy to remotes, or of one remote in it, as a badge. Variables: state (see
   parts/deploy_state_text.tpl), kind (ok, warn, bad, info: expMigrationRemoteAdmin::stateKind()). *}
<span class="a4-badge is-{$kind|wash}" data-deploy-state="{$state|wash}">{include uri='design:migration/parts/deploy_state_text.tpl' state=$state}</span>
