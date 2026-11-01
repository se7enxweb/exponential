{* A state or an action as a badge. Variables: state (see parts/state_text.tpl), kind (ok, warn, bad, info:
   expMigrationAdmin::stateKind()). *}
<span class="a4-badge is-{$kind|wash}">{include uri='design:migration/parts/state_text.tpl' state=$state}</span>
