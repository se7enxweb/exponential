{* The page of the signed links of the administrator's "New user registered" mail (user/registration/<token>,
   kernel/private/classes/views/user/registration.php).

   registration_mode: refused (registration_problem says why: malformed, signature, expired, action, used), view
   (the account, with Approve and Reject when the user may act and the account waits: registration_actions),
   confirm (the question for an approve or reject token, a POST to the same address with ConfirmButton or
   CancelButton; the form token is added to the form by the kernel), nothing (the account no longer waits, was
   deactivated by an administrator, or is protected from a reject) and done (registration_result: result activated,
   approved, removed or refused, state, state_before; reason protected when a reject was refused for a protected
   account). registration_protected: the account is the anonymous user, the administrator account or the signed-in
   user, which are never removed.
   registration_account: id, name, login, email, created, published, state, collaboration_uri, node_uri, version_uri;
   false when there is no such account. Used by every administration design: shared component classes only, no
   inline styles. Guide: doc/features/6.0/registration-admin-links.md *}

{def $mode = $registration_mode
     $account = $registration_account
     $state = $registration_state
     $is_approve = eq( $registration_action, 'approve' )}

<div class="context-block a4-page">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if eq( $mode, 'refused' )}{'This link cannot be used'|i18n( 'design/standard/user/registration' )}{elseif eq( $mode, 'confirm' )}{if $is_approve}{'Approve this registration?'|i18n( 'design/standard/user/registration' )}{else}{'Reject this registration?'|i18n( 'design/standard/user/registration' )}{/if}{elseif eq( $mode, 'done' )}{'Registration handled'|i18n( 'design/standard/user/registration' )}{else}{'New registration'|i18n( 'design/standard/user/registration' )}{/if}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

{if eq( $mode, 'refused' )}
    <div class="a4-notice is-bad" role="alert">
        <p>{switch match=$registration_problem}
            {case match='expired'}{'The link has expired. Links in the mail work for a limited time; open the account from the list of unactivated users instead.'|i18n( 'design/standard/user/registration' )}{/case}
            {case match='used'}{'The link has already been used. Each approve or reject link works once.'|i18n( 'design/standard/user/registration' )}{/case}
            {case match='action'}{'The link is meant for another action.'|i18n( 'design/standard/user/registration' )}{/case}
            {case}{'The link is invalid: it is incomplete or has been changed. Copy the whole link from the mail.'|i18n( 'design/standard/user/registration' )}{/case}
        {/switch}</p>
    </div>
    <div class="a4-toolbar is-bottom">
        <div class="a4-actions">
            <a class="a4-btn" href={$registration_unactivated_uri|ezurl}>{'Unactivated users'|i18n( 'design/standard/user/registration' )}</a>
        </div>
    </div>
{else}

    {if eq( $mode, 'done' )}
        {def $res = $registration_result}
        <div class="a4-notice {if eq( $res.result, 'refused' )}is-warn{else}is-ok{/if}" role="status">
            <p>{switch match=$res.result}
                {case match='activated'}{if eq( $res.state, 'active' )}{'The account has been activated and published. The user can sign in now.'|i18n( 'design/standard/user/registration' )}{else}{'The account has been activated; its publication is still waiting.'|i18n( 'design/standard/user/registration' )}{/if}{/case}
                {case match='approved'}{if eq( $res.state, 'active' )}{'The registration has been approved and the account published. The user can sign in now.'|i18n( 'design/standard/user/registration' )}{else}{'The registration has been approved; its publication is still waiting.'|i18n( 'design/standard/user/registration' )}{/if}{/case}
                {case match='removed'}{'The registration has been rejected and the account removed.'|i18n( 'design/standard/user/registration' )}{/case}
                {case}{if eq( first_set( $res.reason, '' ), 'protected' )}{'Nothing was done: this account cannot be removed here.'|i18n( 'design/standard/user/registration' )}{elseif eq( $res.state, 'missing' )}{'Nothing was done: the account no longer exists.'|i18n( 'design/standard/user/registration' )}{else}{'Nothing was done: the account is not waiting for a decision any more.'|i18n( 'design/standard/user/registration' )}{/if}{/case}
            {/switch}</p>
        </div>
        {undef $res}
    {elseif eq( $mode, 'nothing' )}
        <div class="a4-notice is-warn" role="status">
            <p>{if eq( $state, 'missing' )}{'There is nothing to do: the account no longer exists.'|i18n( 'design/standard/user/registration' )}{elseif and( eq( $registration_action, 'reject' ), first_set( $registration_protected, false() ) )}{'There is nothing to do: this account cannot be removed here.'|i18n( 'design/standard/user/registration' )}{elseif eq( $state, 'disabled' )}{'There is nothing to do: an administrator has deactivated this account; it is not a registration that waits.'|i18n( 'design/standard/user/registration' )}{else}{'There is nothing to do: the account is not waiting for a decision any more.'|i18n( 'design/standard/user/registration' )}{/if}</p>
        </div>
    {elseif eq( $mode, 'confirm' )}
        <div class="a4-notice {if $is_approve}is-info{else}is-warn{/if}">
            <p>{if $is_approve}{'Approving activates the account and completes the registration: the account is published and the user can sign in.'|i18n( 'design/standard/user/registration' )}{else}{'Rejecting removes the account for good. The user is not told.'|i18n( 'design/standard/user/registration' )}{/if}</p>
        </div>
    {/if}

    {if $account}
    <div class="a4-card">
        <div class="a4-card-head">
            <h2 class="a4-h2">{$account.name|wash}</h2>
            <span class="a4-tag {switch match=$account.state}{case match='active'}is-ok{/case}{case match='missing'}is-bad{/case}{case}is-warn{/case}{/switch}">{switch match=$account.state}
                {case match='email'}{'Waiting for the user to confirm the e-mail address'|i18n( 'design/standard/user/registration' )}{/case}
                {case match='administrator'}{'Waiting for an administrator to activate it'|i18n( 'design/standard/user/registration' )}{/case}
                {case match='workflow'}{'Waiting for approval'|i18n( 'design/standard/user/registration' )}{/case}
                {case match='pending'}{'Activated, publication still waiting'|i18n( 'design/standard/user/registration' )}{/case}
                {case match='active'}{'Active'|i18n( 'design/standard/user/registration' )}{/case}
                {case match='disabled'}{'Deactivated by an administrator'|i18n( 'design/standard/user/registration' )}{/case}
                {case}{'Removed'|i18n( 'design/standard/user/registration' )}{/case}
            {/switch}</span>
        </div>
        <dl class="a4-facts">
            <div><dt>{'Login'|i18n( 'design/standard/user/registration' )}</dt><dd>{$account.login|wash}</dd></div>
            <div><dt>{'E-mail address'|i18n( 'design/standard/user/registration' )}</dt><dd>{$account.email|wash}</dd></div>
            <div><dt>{'Registered'|i18n( 'design/standard/user/registration' )}</dt><dd>{if $account.created|gt( 0 )}{$account.created|l10n( 'shortdatetime' )}{else}-{/if}</dd></div>
            <div><dt>{'ID'|i18n( 'design/standard/user/registration' )}</dt><dd>{$account.id}</dd></div>
        </dl>
        {if ne( $account.state, 'missing' )}
        <div class="a4-card-foot">
            {if $account.collaboration_uri}<a class="a4-btn" href={$account.collaboration_uri|ezurl}>{'Open the approval item'|i18n( 'design/standard/user/registration' )}</a>{/if}
            {if $account.node_uri}<a class="a4-btn" href={$account.node_uri|ezurl}>{'Open the account'|i18n( 'design/standard/user/registration' )}</a>
            {elseif $account.version_uri}<a class="a4-btn" href={$account.version_uri|ezurl}>{'Preview the account (not published yet)'|i18n( 'design/standard/user/registration' )}</a>{/if}
        </div>
        {/if}
    </div>
    {elseif ne( $mode, 'done' )}
    <div class="a4-notice is-warn" role="status"><p>{'The account no longer exists. It was removed, or the registration was rejected.'|i18n( 'design/standard/user/registration' )}</p></div>
    {/if}

    {if eq( $mode, 'confirm' )}
    <form method="post" action={$registration_uri|ezurl}>
        <div class="a4-toolbar is-bottom">
            <div class="a4-actions">
                <button class="a4-btn {if $is_approve}a4-btn-primary{else}a4-btn-danger{/if}" type="submit" name="ConfirmButton" value="1">{if $is_approve}{'Approve'|i18n( 'design/standard/user/registration' )}{else}{'Reject and remove'|i18n( 'design/standard/user/registration' )}{/if}</button>
                <button class="a4-btn" type="submit" name="CancelButton" value="1">{'Cancel'|i18n( 'design/standard/user/registration' )}</button>
            </div>
            <p class="a4-meta">{'Nothing has been changed yet.'|i18n( 'design/standard/user/registration' )}</p>
        </div>
    </form>
    {else}
    <div class="a4-toolbar is-bottom">
        <div class="a4-actions">
            {if is_set( $registration_actions.approve )}<a class="a4-btn a4-btn-primary" href={$registration_actions.approve|ezurl}>{'Approve'|i18n( 'design/standard/user/registration' )}</a>{/if}
            {if is_set( $registration_actions.reject )}<a class="a4-btn a4-btn-outline-danger" href={$registration_actions.reject|ezurl}>{'Reject'|i18n( 'design/standard/user/registration' )}</a>{/if}
            {if and( $registration_view_uri, $account, ne( $account.state, 'missing' ) )}<a class="a4-btn" href={$registration_view_uri|ezurl}>{'Back to the account'|i18n( 'design/standard/user/registration' )}</a>{/if}
            <a class="a4-btn" href={$registration_unactivated_uri|ezurl}>{'Unactivated users'|i18n( 'design/standard/user/registration' )}</a>
        </div>
        {if and( eq( $mode, 'view' ), $registration_expiry|gt( 0 ) )}<p class="a4-meta">{'This link works until %date.'|i18n( 'design/standard/user/registration',, hash( '%date', $registration_expiry|l10n( 'shortdatetime' ) ) )}</p>{/if}
    </div>
    {/if}
{/if}

</div></div></div>
</div>
{undef}
