{* mailpreferences/admin/user without an ID: find the user whose e-mail preferences to see or change.
   Variables: query (what was typed), users (hash( id, name, login, email )), notice. *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences: a user'|i18n( 'design/admin/mailpreferences' )
         intro='Find a user by name, login, e-mail address or ID to see their preferences and consent history, or to change them on their request. Your changes are recorded as made by you.'|i18n( 'design/admin/mailpreferences' )
         crumb=false() wide=true() admin_tab='user'}

{include uri='design:mailpreferences/parts/notice.tpl' notice=first_set( $notice, false() )}

<section class="mp-card">
    <form method="get" action={'mailpreferences/admin/user'|ezurl}>
        <div class="mp-field">
            <label for="mp-user-q">{'Name, login, e-mail address or user ID'|i18n( 'design/admin/mailpreferences' )}</label>
            <input type="text" id="mp-user-q" name="q" value="{$query|wash}" required="required" />
        </div>
        <div class="mp-actions" style="margin-top:0;"><input class="mp-btn primary" type="submit" value="{'Find'|i18n( 'design/admin/mailpreferences' )}" /></div>
    </form>
{if $query|ne( '' )}
{if $users|count|eq( 0 )}
    <p class="mp-hint" style="margin-top:.8em;">{'No user found.'|i18n( 'design/admin/mailpreferences' )}</p>
{else}
    <div class="mp-scroll" style="margin-top:.8em;">
    <table class="mp-table mp-stack">
        <thead><tr>
            <th scope="col">{'Name'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Login'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'E-mail address'|i18n( 'design/admin/mailpreferences' )}</th>
        </tr></thead>
        <tbody>
{foreach $users as $found}
        <tr>
            <td data-label="{'Name'|i18n( 'design/admin/mailpreferences' )}"><a href={concat( 'mailpreferences/admin/user/', $found.id )|ezurl}>{$found.name|wash}</a></td>
            <td data-label="{'Login'|i18n( 'design/admin/mailpreferences' )}">{$found.login|wash}</td>
            <td data-label="{'E-mail address'|i18n( 'design/admin/mailpreferences' )}">{$found.email|wash}</td>
        </tr>
{/foreach}
        </tbody>
    </table>
    </div>
{/if}
{/if}
</section>

{include uri='design:mailpreferences/parts/page_end.tpl'}
