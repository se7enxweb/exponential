{* Policy list window.

   Bounded. This listed every policy of every role assigned to the user, and
   each row then asked the database for its limitations and resolved their
   value names - a list of lists with a query per row. A role is free to carry
   policies in the hundreds of thousands, and this window is rendered on every
   view of a user or a user group whichever tab is on top, so at that size no
   page of the user tree could be opened at all.

   Each role now shows its first few and says how many more there are, with the
   role's own page - which is paged - for the rest. *}
{def $policy_preview = ezini( 'RoleSettings', 'PolicyPreviewPerRole', 'site.ini' )|int()}
{if $assigned_policy_count}

<table class="list" cellspacing="0" summary="{'Policy list and the Role that are assignet to current node.'|i18n( 'design/admin/node/view/full' )}">
<tr>
    <th>{'Role'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Limited to'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Module'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Function'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Limitation'|i18n( 'design/admin/node/view/full' )}</th>
</tr>

{* For all roles... *}
{section var=AssignedRoles loop=$assigned_roles}

{* For the first few policies of that role... *}
{let role_policy_count = fetch( 'role', 'policy_count', hash( 'role_id', $AssignedRoles.item.id ) )
     role_policies     = fetch( 'role', 'policies', hash( 'role_id', $AssignedRoles.item.id,
                                                          'offset', 0,
                                                          'limit', $policy_preview ) )}
{section var=Policy loop=$:role_policies sequence=array( bglight, bgdark )}

<tr class="{$Policy.sequence}">

    {* Role name  *}
    <td>
    {$AssignedRoles.item.name|wash}
    </td>

    {* limitation (if any). *}
    <td>
    {if $AssignedRoles.item.limit_identifier}
        {'%limitation_identifier %limitation_value'|i18n( 'design/admin/node/view/full',, hash( '%limitation_identifier', $AssignedRoles.item.limit_identifier|downcase, '%limitation_value', $AssignedRoles.item.limit_value ) )}
    {/if}
    </td>

    {* Module. *}
    <td>
    {if eq( $Policy.item.module_name, '*' )}
        <i>{'all modules'|i18n( 'design/admin/node/view/full' )}</i>
    {else}
        {$Policy.item.module_name}
    {/if}
    </td>

    {* Policy. *}
    <td>
    {if eq( $Policy.item.function_name, '*' )}
        <i>{'all functions'|i18n( 'design/admin/node/view/full' )}</i>
    {else}
        {$Policy.item.function_name}
    {/if}
    </td>

    {* Limitations. *}
    <td>
    {if ne( $Policy.item.limitations|count, 0 )}
        {section var=Limitation loop=$Policy.item.limitations}
            {$Limitation.identifier|wash}(
            {section var=LimitationValues loop=$Limitation.values_as_array_with_names}
                {$LimitationValues.Name|wash}
                {delimiter}, {/delimiter}
        {/section})
        {delimiter}, {/delimiter}
        {/section}
    {else}
        <i>{'No limitations'|i18n( 'design/admin/node/view/full' )}</i>
    {/if}
    </td>

</tr>
{/section}

{* What is not shown, and where it is. The role's own page is paged. *}
{if $:role_policy_count|gt( $policy_preview )}
<tr class="bglight">
    <td colspan="5">
        <a href={concat( '/role/view/', $AssignedRoles.item.id )|ezurl}>{'%count more in the %role_name role'|i18n( 'design/admin/node/view/full',, hash( '%count', sub( $:role_policy_count, $policy_preview ), '%role_name', $AssignedRoles.item.name ) )|wash}</a>
    </td>
</tr>
{/if}
{/let}
{/section}

</table>

{else}
<div class="block">
    <p>{'There are no available policies.'|i18n( 'design/admin/node/view/full' )}</p>
</div>
{/if}
