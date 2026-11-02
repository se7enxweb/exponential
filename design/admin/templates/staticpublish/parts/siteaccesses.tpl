{* The siteaccesses of a target with their folder in the copy, the one at the root marked. Variables: members
   (expStaticAdmin::memberList(): name, folder, default). *}
<ul class="a4-badges" aria-label="{'Siteaccesses'|i18n( 'design/admin/staticpublish' )|wash}">
{foreach $members as $m}
    <li class="a4-tag{if $m.default} is-info{/if}"><strong>{$m.name|wash}</strong> <code class="a4-code">{$m.folder|wash}</code>{if $m.default} <span>{'At the root'|i18n( 'design/admin/staticpublish' )}</span>{/if}</li>
{/foreach}
</ul>
