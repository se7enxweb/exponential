{* What section/assign says when it could not assign the section: to some of the chosen items (1), to any object
   at all (2), or because the user may not assign this section (3).

   The same file is in design/admin and design/admin4. Guide: doc/guides/sections.md *}
{include uri='design:section/exp_style.tpl'}

<div class="context-block exp-sections">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Assign section notification'|i18n( 'design/admin/section/assign_notification' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-warn" role="alert">
{switch match=$error_number}
{case match=1}
    <p><strong>{"The section < %1 > was not assigned to the nodes listed below because of insufficient permission:"|i18n( 'design/admin/section/assign_notification', '', hash( '%1', $section_name ) )|wash}</strong></p>
    <ul>
    {foreach $denied_node_list as $node}
        <li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
    {/foreach}
    </ul>
    <p>{'The other items were assigned. A section/assign policy decides which objects you may assign (its Class, Owner and Section limitations) and which sections you may assign them to (NewSection).'|i18n( 'design/admin/section/assign_notification' )}</p>
{/case}
{case match=2}
    <p><strong>{"There are no objects in the system that you could assign the section < %1 > to."|i18n( 'design/admin/section/assign_notification', '', hash( '%1', $section_name ) )|wash}</strong></p>
    <p>{'A section/assign policy allows this section, but no class of object it names. An administrator can widen its Class limitation.'|i18n( 'design/admin/section/assign_notification' )}</p>
{/case}
{case match=3}
    <p><strong>{"You do not have permission to assign the section < %1 > to any object."|i18n( 'design/admin/section/assign_notification', '', hash( '%1', $section_name ) )|wash}</strong></p>
    <p>{'Assigning a section needs a section/assign policy whose NewSection limitation includes it, or one without that limitation.'|i18n( 'design/admin/section/assign_notification' )}</p>
{/case}
{/switch}
</div>

<form action={"/section/list"|ezurl} method="post">
<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit">{'OK'|i18n( 'design/admin/section/assign_notification' )}</button>
    </div>
    <p class="exp-meta">{'Back to the section list.'|i18n( 'design/admin/section/assign_notification' )}</p>
</div>
</form>

</div></div></div>
</div>
