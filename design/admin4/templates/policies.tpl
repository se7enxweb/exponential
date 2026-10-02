{* Available policies.
   
   Bounded. This is a summary of what the user's roles allow, and it used to be
   drawn by reading all of it: fetch( user, user_role ) builds the user's entire
   access array in php, and it was asked for only to put a number in the
   heading, and then every policy of every assigned role was listed, each row
   asking the database for its limitations. On an installation whose roles carry
   policies in the millions the window cannot be drawn at all, and it is on
   every view of a user or user group - the tabs are all rendered, whichever one
   is on top.
   
   So each role shows its first few and says how many more there are, with the
   role's own page - which is paged - for the rest. The count comes from the
   database rather than from a list nobody wanted. *}
{let assigned_roles=fetch( user, member_of, hash( id, $node.contentobject_id ) )
     policy_preview=ezini( 'RoleSettings', 'PolicyPreviewPerRole', 'site.ini' )|int()
     policy_total=0}

{foreach $assigned_roles as $policy_role}
    {set policy_total=sum( $policy_total,
                           fetch( 'role', 'policy_count', hash( 'role_id', $policy_role.id ) ) )}
{/foreach}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h2 class="context-title">{'Available policies [%policy_count]'|i18n( 'design/admin/node/view/full',, hash( '%policy_count', $policy_total ) )}</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

{section show=$policy_total}

<table class="list" cellspacing="0">
<tr>
    <th>{'Role'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Module'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Function'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Limitation'|i18n( 'design/admin/node/view/full' )}</th>
</tr>

{* For all roles... *}
{section var=AssignedRoles loop=$assigned_roles}

{* For the first few policies of that role... *}
{let role_policy_count=fetch( 'role', 'policy_count', hash( 'role_id', $AssignedRoles.item.id ) )
     role_policies=fetch( 'role', 'policies', hash( 'role_id', $AssignedRoles.item.id,
                                                    'offset', 0,
                                                    'limit', $policy_preview ) )}
{section var=Policy loop=$:role_policies sequence=array( bglight, bgdark )}

<tr class="{$Policy.sequence}">

    {* Role name + limitation (if any). *}
    <td>
    {$AssignedRoles.item.name|wash}
    &nbsp;
    {if $AssignedRoles.item.limit_identifier}
        ({'limited to %limitation_identifier %limitation_value'|i18n( 'design/admin/node/view/full',, hash( '%limitation_identifier', $AssignedRoles.item.limit_identifier|downcase, '%limitation_value', $AssignedRoles.item.limit_value ) )})
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
    {section show=ne( $Policy.item.limitations|count, 0 )}
        {section var=Limitation loop=$Policy.item.limitations}
            {$Limitation.identifier|wash}(
            {section var=LimitationValues loop=$Limitation.values_as_array_with_names}
                {$LimitationValues.Name|wash}
                {delimiter}, {/delimiter}
        {/section})
        {delimiter}, {/delimiter}
        {/section}
    {section-else}
        <i>{'No limitations'|i18n( 'design/admin/node/view/full' )}</i>
    {/section}
    </td>

</tr>
{/section}

{* What is not shown, and where it is. The role's own page is paged. *}
{if $:role_policy_count|gt( $policy_preview )}
<tr class="bglight">
    <td colspan="4">
        <a href={concat( '/role/view/', $AssignedRoles.item.id )|ezurl}>{'%count more in the %role_name role'|i18n( 'design/admin/node/view/full',, hash( '%count', sub( $:role_policy_count, $policy_preview ), '%role_name', $AssignedRoles.item.name ) )|wash}</a>
    </td>
</tr>
{/if}
{/let}
{/section}

</table>

{section-else}
<div class="block">
    <p>{'There are no available policies.'|i18n( 'design/admin/node/view/full' )}</p>
</div>
{/section}

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>

{/let}
