{* Create or rename a class group (class/groupedit/<id>).

   Field and button names (Group_name, StoreButton, DiscardButton, RedirectIfDiscarded) are unchanged; Cancel goes
   back to the page the form was opened from. This is an edit view, which admin4 draws without its main card;
   .exp-standalone gives the page its own. The same file is in design/admin and design/admin4.
   Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}

<form name="GroupEdit" method="post" action={concat( $module.functions.groupedit.uri, '/', $classgroup.id )|ezurl} class="exp-lists exp-classgroups exp-standalone">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title">{$classgroup.name|wash|classgroup_icon( normal, $classgroup.name|wash )}&nbsp;{'Edit <%group_name> [Class group]'|i18n( 'design/admin/class/groupedit',, hash( '%group_name', $classgroup.name ) )|wash}</h1>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $classgroup.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A class group files classes so they are easy to find. Its name is shown on Setup > Classes and in the class lists; renaming it changes nothing for the classes or their objects.'|i18n( 'design/admin/class/groupedit' )}</p>

<div class="exp-panel">
<div class="exp-form-fields">
    <div class="exp-field">
        <label for="classGroupName">{'Name'|i18n( 'design/admin/class/groupedit' )}</label>
        <input id="classGroupName" type="text" name="Group_name" value="{$classgroup.name|wash}" maxlength="255" aria-describedby="classGroupName-help" />
        <span class="exp-help" id="classGroupName-help">{'For example Content, Media or Users.'|i18n( 'design/admin/class/groupedit' )}</span>
    </div>
</div>
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="StoreButton" value="1">{'OK'|i18n( 'design/admin/class/groupedit' )}</button>
        <button class="exp-btn" type="submit" name="DiscardButton" value="1">{'Cancel'|i18n( 'design/admin/class/groupedit' )}</button>
    </div>
    <p class="exp-meta">{'Cancel goes back without saving.'|i18n( 'design/admin/class/groupedit' )}</p>
</div>

</div></div></div>
</div>

{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>

<script type="text/javascript">
(function () {ldelim} var f = document.getElementById( 'classGroupName' ); if ( f ) {ldelim} f.focus(); f.select(); {rdelim} {rdelim})();
</script>
