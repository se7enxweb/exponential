{* The class groups a class is in, on the class page: remove it from the ticked groups, or add it to another one.
   Field and button names (group_id_checked[], RemoveGroupButton, ContentClass_group, AddGroupButton) are unchanged.
   A class must stay in at least one group; class/view refuses to take it out of its last one. The same file is in
   design/admin and design/admin4. *}
<form action={concat( $module.functions.view.uri, '/', $class.id )|ezurl} method="post">
<section class="exp-section" aria-labelledby="class-view-groups">
<div class="exp-section-head">
    <h2 class="exp-h2" id="class-view-groups">{'Member of class groups (%group_count)'|i18n( 'design/admin/class/view',, hash( '%group_count', $class.ingroup_list|count ) )}</h2>
    <p>{'Groups only file the class; they change nothing about its objects.'|i18n( 'design/admin/class/view' )}</p>
</div>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col"><span class="exp-sr">{'Select'|i18n( 'design/admin/class/classlist' )}</span></th>
    <th scope="col">{'Class group'|i18n( 'design/admin/class/view' )}</th>
</tr></thead>
<tbody>
{foreach $class.ingroup_list as $group_link}
<tr>
    <td><input type="checkbox" name="group_id_checked[]" value="{$group_link.group_id}" aria-label="{'Select class group for removal.'|i18n( 'design/admin/class/view' )}" /></td>
    <td>{$group_link.group_name|wash|classgroup_icon( small, $group_link.group_name|wash )}&nbsp;<a href={concat( '/class/classlist/', $group_link.group_id )|ezurl}>{$group_link.group_name|wash}</a></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="RemoveGroupButton" value="1" title="{'Remove the <%class_name> class from the selected class groups.'|i18n( 'design/admin/class/view',, hash( '%class_name', $class.name ) )|wash}">{'Remove from selected'|i18n( 'design/admin/class/view' )}</button>
    {if sub( count( $class.group_list ), count( $class.ingroup_list ) )}
        <label class="exp-sr" for="classViewAddGroup">{'Class group'|i18n( 'design/admin/class/view' )}</label>
        <select id="classViewAddGroup" class="exp-select-inline" name="ContentClass_group" title="{'Select a group that the <%class_name> class should be added to.'|i18n( 'design/admin/class/view',, hash( '%class_name', $class.name ) )|wash}">
        {foreach $class.group_list as $all_group}
            {if $class.ingroup_id_list|contains( $all_group.id )|not}
            <option value="{$all_group.id}/{$all_group.name|wash}">{$all_group.name|wash}</option>
            {/if}
        {/foreach}
        </select>
        <button class="exp-btn" type="submit" name="AddGroupButton" value="1" title="{'Add the <%class_name> class to the group specified in the menu on the left.'|i18n( 'design/admin/class/view',, hash( '%class_name', $class.name ) )|wash}">{'Add to class group'|i18n( 'design/admin/class/view' )}</button>
    {else}
        <span class="exp-meta">{'The <%class_name> class already exists within all class groups.'|i18n( 'design/admin/class/view',, hash( '%class_name', $class.name ) )|wash}</span>
    {/if}
    </div>
</div>
</section>
</form>
