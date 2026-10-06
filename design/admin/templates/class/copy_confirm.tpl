{* Shown by class/copy/<id> when it is opened by a link instead of a form: a copy is made only by a POST.

   Says what the copy will be and where it lands (content.ini [CopySettings] ClassRedirect), with Copy, which posts
   ConfirmCopyButton to class/copy/<id> (the form token protects it), and the way back to the class. Variables:
   class, copy_redirect, module. The same file is in design/admin and design/admin4. Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}

<form method="post" action={concat( 'class/copy/', $class.id )|ezurl} class="exp-lists exp-classgroups exp-standalone">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title">{'Copy the <%class_name> class?'|i18n( 'design/admin/class/copy',, hash( '%class_name', $class.name ) )|wash}</h1>
<code class="exp-title-key">{$class.identifier|wash}</code>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $class.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-info">
    <p>{'A copy is a new class with the same attributes and settings, in the same class groups, named "Copy of" the class with the identifier copy_of_ and the original identifier. Its objects are not copied; the original class is not changed.'|i18n( 'design/admin/class/copy' )}</p>
    <p>{if eq( $copy_redirect, 'classlist' )}{'After copying, the class list of its first group opens.'|i18n( 'design/admin/class/copy' )}{elseif eq( $copy_redirect, 'grouplist' )}{'After copying, the class groups open.'|i18n( 'design/admin/class/copy' )}{elseif eq( $copy_redirect, 'classview' )}{'After copying, the page of the copy opens.'|i18n( 'design/admin/class/copy' )}{else}{'After copying, the copy opens for editing; Cancel there throws it away.'|i18n( 'design/admin/class/copy' )}{/if}</p>
</div>

<div class="exp-panel">
<dl class="exp-facts">
    <div><dt>{'Class'|i18n( 'design/admin/class/copy' )}</dt><dd>{$class.name|wash}</dd></div>
    <div><dt>{'Attributes'|i18n( 'design/admin/class/view' )}</dt><dd>{$class.data_map|count}</dd></div>
    <div><dt>{'Class groups'|i18n( 'design/admin/class/view' )}</dt><dd>{foreach $class.ingroup_list as $group_link}{$group_link.group_name|wash}{delimiter}, {/delimiter}{/foreach}</dd></div>
</dl>
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="ConfirmCopyButton" value="1">{'Copy'|i18n( 'design/admin/class/copy' )}</button>
        <a class="exp-btn exp-cancel" href={concat( '/class/view/', $class.id )|ezurl}>{'Cancel'|i18n( 'design/admin/class/copy' )}</a>
    </div>
    <p class="exp-meta">{'Nothing has been copied yet.'|i18n( 'design/admin/class/copy' )}</p>
</div>

</div></div></div>
</div>
</form>
