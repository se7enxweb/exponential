# Signed links in the "New user registered" mail

When someone registers on the site, the administrator gets the mail "New user registered at <site>"
(`site.ini [UserSettings] RegistrationFeedback=email`, sent to `RegistrationEmail`, else `[MailSettings] AdminEmail`).
Since 6.0.15 this mail is an HTML mail with a plain-text part. It says what state the new account is in and carries
signed links into the administration: view the account, approve it and reject it. Each link opens the page
`user/registration/<token>`, which asks the signed-in administrator to confirm before anything changes.

Before 6.0.15 the mail was plain text and had one link, `http://<host>/content/view/full/<main node>`. That link
could not work: when the mail goes out the account is usually not published yet, so it has no node, and the link
used `http`. It is gone.

## In short

- The mail lists the account (name, login, e-mail address, registration time, its state) and up to five links.
  The text part carries the same content as "Label: URL" lines, for mail clients that do not show HTML.
- **View account** opens the account's page in the administration. **Activate account** (or **Approve**) and
  **Reject and remove** open a confirmation page. **Open approval item** opens the approval item when a workflow holds
  the account. **All unactivated users** opens Users > Unactivated users.
- Every link is **signed** (HMAC-SHA-256 under a key derived from the site secret), works for **7 days** by default,
  and names one account and one action. Approve and reject links work **once**.
- Opening a link never changes anything. You must be **signed in** and hold the **policy** for it, and the change
  happens only when you press the button on the confirmation page (a POST with the form token). Mail scanners that
  open every link in a mail do no harm.
- Approving and rejecting, and every refused link, are recorded in the audit trail.
- An active account is never changed through these links.
- The mail's look comes from one layout template, `design:mail/layout.tpl`, which a design can override with its own
  colors and logo. The media design does.

## What the mail contains

| Part | Content |
|---|---|
| Subject | "New user registered at <SiteURL>" |
| Heading and intro | "New user registered", "A new user has registered." |
| Table | Name, Username, Email, Registered at, Account status |
| Notice | for an account that waits: what the administrator is expected to do |
| Buttons | the links below, depending on the state |
| Small print | "These links open the administration. You need to sign in, and you are asked to confirm before anything changes. Links that change the account work once. All links stop working on <date>." |
| Footer | "This mail was sent by <SiteURL> because a new user registered." |

### The links

| Link | Shown when | Opens |
|---|---|---|
| **Activate account** | the account waits for e-mail confirmation or for an administrator | the confirmation to approve |
| **Approve** | the account waits for an approval item (workflow) | the confirmation to approve |
| **View account** | always (the main button when the account does not wait) | the account's page |
| **Reject and remove** | the account waits | the confirmation to reject |
| **Open approval item** | an approval item waits for the account | `collaboration/item/full/<id>` |
| **All unactivated users** | always | `user/unactivated` |

The first four are signed links to `user/registration/<token>`. The last two are ordinary administration addresses.

### The states

The mail and the page name the account's state. It is worked out when the mail is sent and again each time the page
is opened, so the page always shows the current state.

| State | Meaning | In the mail |
|---|---|---|
| `email` | The account is disabled and has an activation key: it waits for the user to click the link of the activation mail (`VerifyUserType=email`). | "Waiting for the user to confirm the email address." You can activate it now or wait. |
| `administrator` | The account is disabled, has no activation key and has never signed in: only an administrator can activate it. | "Disabled until an administrator activates it." |
| `workflow` | The publication of the user object waits for an approval item (an *Approve* event in the publish workflow). | "Waiting for approval before it is published." |
| `pending` | The account is enabled, but its user object is not published yet: a workflow is still running. | "Enabled, but not yet published." No approve or reject. |
| `disabled` | The account is disabled and has no activation key, but it has signed in before (`ezuservisit.login_count` > 0): an administrator deactivated it. It is not a registration that waits, so an old Activate or Reject link from the mail does not enable or remove it. | (page only: "Deactivated by an administrator") |
| `active` | Enabled and published. | "Active." Only View account. |
| `missing` | The account no longer exists (removed, or rejected). | (page only) |

Approve and reject are possible only in the states `email`, `administrator` and `workflow`. Reject never removes a
protected account: the anonymous user (`[UserSettings] AnonymousUserID`), the administrator account (`UserCreatorID`
and the account with the login `admin`, as Users > Unactivated users protects it) and the signed-in user. The page
does not offer Reject for them, and a reject link for one says there is nothing to do.

## What approve and reject do

| State | Approve | Reject |
|---|---|---|
| `email`, `administrator` | Activates the account exactly as **Activate** on Users > Unactivated users does: the activation key is removed, the account is enabled and the registration completes (the user object is published, the user gets the "registration approved" mail unless `ActivationByAdministratorSendsApprovalMail=disabled` or `EmailRegistrationInfo=disabled`, and the `post_register` workflow runs). | Removes the account for good: its user object is purged, as **Remove** on Users > Unactivated users does. The user is not told. |
| `workflow` | Accepts the approval item, as its **Approve** button does, and continues the waiting workflow at once instead of on the next run of the workflow cronjob, so the account is published right away. | Denies the approval item, as its **Deny** button does, then removes the waiting workflow process, the approval item (it would name an account that is gone) and the account. |
| `pending`, `active`, `disabled`, `missing` | Nothing. The page says there is nothing to do. | Nothing. |

After the action the page shows the result: *activated*, *approved* or *removed*, and the state the account is in
now. If the account was handled by someone else between opening the confirmation and pressing the button, the page
says *Nothing was done* and the token is used up.

## The page `user/registration/<token>`

The page belongs to the administration (`ui_context` administration). It shows:

- for a **view** link: the account (name, login, e-mail address, registration time, id, state), a button to the
  approval item, to the published account or, before publication, to a preview of its current version, and
  **Approve** and **Reject** when the account waits and you may act. These buttons lead to the confirmation of a new
  approve or reject link, so a view link can be used for as long as it is valid. The page says until when.
- for an **approve** or **reject** link: the same account card and a confirmation with **Approve** (or **Reject and
  remove**) and **Cancel**. Cancel goes back to the account's view page and leaves the link unused.
- for a refused link: why it is refused, and a button to Users > Unactivated users.

| You see | Why | HTTP status |
|---|---|---|
| *The link is invalid: it is incomplete or has been changed.* | The token is malformed or its signature does not match (cut off by the mail client, edited, or signed with another site secret). | 403 |
| *The link has expired.* | It is older than `RegistrationActionTTL`. Use Users > Unactivated users instead. | 410 |
| *The link has already been used.* | An approve or reject link was confirmed before. | 403 |
| *The link is meant for another action.* | The token's action does not fit. | 403 |
| The login form | You are not signed in. After signing in you come back to the same page. | |
| *Access denied* | You are signed in but lack the policy. | 403 |

## Security

- **Signed.** A token reads `<user id>.<action>.<expiry>.<nonce>.<signature>`. The signature is an HMAC-SHA-256 over
  the other four parts under a key derived from the site secret (`expMailSecret`, `mailpreferences.ini
  [SecretSettings] TokenSecret`, generated on first use into `settings/override/mailpreferences.ini.append.php`; the
  key used here is derived from it for this purpose only, never the secret itself). A token cannot be made, changed,
  or moved to another account or action without the secret. Changing the secret makes every link already sent
  invalid. The signature is compared in constant time.
- **Expiring.** Each link ends at its expiry, `RegistrationActionTTL` seconds after the mail was sent (7 days by
  default).
- **Single-use.** When an approve or reject link is confirmed, its nonce is stored in `ezsite_data` (row
  `exp_regaction_<nonce>`, the value its expiry) before anything is done. The name is the table's primary key, so
  of two requests with the same link only one gets through. Rows of expired links are removed now and then
  automatically. A view link is not used up.
- **Sign-in and policy.** An anonymous visitor gets the login form. A signed-in user may act when they have
  - the policy *User / Activation* (`user/activation`, the policy of Users > Unactivated users), or
  - while an approval item waits, the role of approver of that item;
  - for a view link only, also *Content / Edit* on the account.

  Anyone else gets *Access denied*. A user who may only view sees no Approve or Reject buttons.
- **Confirm by POST.** A GET never changes anything; mail scanners and link previews open links. The account is
  changed only by a POST of the confirmation form with **Approve** or **Reject and remove**, and the form carries the
  form token of the session (`ezformtoken`), so another site cannot submit it for you.
- **Audit.** Recorded in the audit trail ([audit events](../../bc/6.0/audit.md)):

  | Event | When |
  |---|---|
  | `access.registration.approve` | an account was activated or its approval item accepted; `before.state` and `after.state` |
  | `access.registration.reject` | an account was rejected and removed; `before.state`, `after.state` is `missing` |
  | `access.registration.refused` | a link was refused; `after.reason` is `malformed`, `signature`, `expired`, `action`, `used`, `policy` (no right to act) or `state` (the account no longer waits), `after.action` the link's action |

## Settings

In `site.ini [UserSettings]`, best set in `settings/override/site.ini.append.php` or in the front siteaccess (the mail
is sent by the siteaccess where the user registers):

| Setting | Default | What it does |
|---|---|---|
| `RegistrationAdminLinks` | `enabled` | `disabled`: the mail has no links, and `user/registration` answers *not found*. |
| `RegistrationAdminBaseURL` | empty | The address of the administration the links point to, e.g. `https://admin.example.com` or `https://www.example.com/admin`. Empty: `https://` and the `SiteURL` of the administration siteaccess, else the address the siteaccess matching gives it, with `https`. A value without a scheme gets `https://`; `http://` is used only when written. |
| `RegistrationAdminSiteAccess` | empty | The administration siteaccess. Empty: the siteaccess whose `[SiteSettings] FrontSiteAccess` is the current one, else `admin`. |
| `RegistrationActionTTL` | `604800` | How long a link works, in seconds (7 days). |

```ini
[UserSettings]
RegistrationAdminBaseURL=https://admin.example.com
RegistrationAdminSiteAccess=admin
RegistrationActionTTL=259200
```

Set `RegistrationAdminBaseURL` when the administration siteaccess's `SiteURL` is not an address the administrator can
reach (an internal host name, `localhost`, a host matched by port or by a path the `SiteURL` does not include). A
`SiteURL` of `localhost`, `127.0.0.1`, `::1` or `example.com` is never used.

To switch the links off:

```ini
[UserSettings]
RegistrationAdminLinks=disabled
```

The mail is then sent with the account's details only.

## Customizing the mail

### The content: `user/registrationfeedback.tpl`

Override `design:user/registrationfeedback.tpl` in your design to change the wording or the buttons. It gets these
variables (from `kernel/private/classes/views/user/register.php`):

| Variable | Content |
|---|---|
| `$user` | the new `eZUser` (`login`, `email`, `contentobject_id`) |
| `$object` | the user's content object (`name`, `id`, `published`) |
| `$hostname` | the host name of the site |
| `$registration_links` | `false` when the links are off, else a hash: `view`, `approve`, `reject` (the signed links), `pending` (the approval item's address or empty), `unactivated` (the list of unactivated users), `expires` (timestamp) |
| `$registration_state` | `false`, or `email`, `administrator`, `workflow`, `pending`, `active`, `disabled` |

It may set, in a `set-block` with `scope=root`: `subject`, `content_type`, `body_text`, `email_sender` and
`email_receiver`. When `content_type` contains `html` and `body_text` is set, the mail is sent as
`multipart/alternative`, with `body_text` as the plain-text part and the template's output as the HTML part. That
works with every mail transport (sendmail, file and SMTP). Without `body_text`, the template's output is the whole
body, as before, so an older override that writes plain text keeps working.

The shipped template passes only text and URLs to the layout; it writes no HTML of its own.

### The look: `mail/layout.tpl`

All HTML of the mail is in `design:mail/layout.tpl`; its plain-text twin is `design:mail/layout_text.tpl`. A design
that has its own `templates/mail/layout.tpl` gives every mail that uses the layout its colors and logo. The media
design does: `extension/sevenx_themes_media/design/media/templates/mail/layout.tpl` uses its yellow accent, white
card and its site logo as a PNG (`images/site-logo-mail.png`; mail clients do not show SVG).

The layout's inputs, all optional:

| Input | Content |
|---|---|
| `mail_title` | the heading |
| `mail_intro` | the first paragraph (text) |
| `mail_rows` | an array of `hash( 'label', ..., 'value', ... )`, shown as a table |
| `mail_notice` | a highlighted sentence (text) |
| `mail_buttons` | an array of `hash( 'label', ..., 'url', ..., 'kind', 'primary'\|'secondary'\|'danger' )` |
| `mail_links` | an array of `hash( 'label', ..., 'url', ... )`, shown as text links below the buttons |
| `mail_links_note` | small print below the buttons (text) |
| `mail_footer` | the footer text; default: the site name, linked |
| `mail_logo_url` | the absolute `https` address of a PNG or JPG logo; none: the site name as text |
| `mail_logo_alt` | the logo's alternative text; default: the site name |
| `mail_logo_link` | where the logo links to; default: the site |
| `mail_logo_width`, `mail_logo_height` | the logo's display size in pixels |

The layout washes every value; pass plain text, not HTML. An override should keep what makes the shipped layout work
in mail clients (Gmail, Outlook, Apple Mail, phones): tables with `role="presentation"`, inline styles with `bgcolor`
fallbacks, at most 600 pixels wide, no external stylesheet, no flexbox or grid. The palette is a set of `$mx_*`
variables at the top of the file, so a design usually changes only those and the logo.

Other mails can use the same layout: include `design:mail/layout.tpl` and `design:mail/layout_text.tpl` with these
inputs, as `registrationfeedback.tpl` does.

### The page

`design:user/registration.tpl` draws the page for every administration design with the shared component classes. Its
variables are described at the top of the file.

## The mails the new user gets

The user's own registration mails use the same layout and are HTML mails with a plain-text part too:

| Mail | Template | Sent by |
|---|---|---|
| Activation (confirm the e-mail address) | `user/registrationinfo.tpl` | `eZUserOperationCollection::sendActivationEmail()` |
| Activation sent again (Users > Unactivated users, Resend) | `user/unactivated_resend_mail.tpl` | `expUnactivatedUsers::resend()` |
| Registration approved | `user/registrationapproved.tpl` | `eZUserOperationCollection::sendUserNotification()` |

Their links use https. The activation mail no longer links to `content/view/full/` (the account had no node yet). Any
sender gets the multipart mail from a template through `expMailTemplateBody::apply( $mail, $tpl, $output )`: when the
template sets `content_type` to text/html and `body_text`, the mail has both parts; otherwise it is what the template
says, as before.

## Files

| File | Role |
|---|---|
| `kernel/classes/user/expregistrationadminaction.php` | tokens, states, policy, approve and reject |
| `kernel/classes/mail/expmailtemplatebody.php` | the body of a mail from a template (the multipart mail) |
| `kernel/user/registration.php`, `kernel/private/classes/views/user/registration.php` | the page `user/registration` |
| `kernel/private/classes/views/user/register.php` | sends the mail with the links |
| `design/standard/templates/user/registrationfeedback.tpl` | the mail's content |
| `design/standard/templates/mail/layout.tpl`, `mail/layout_text.tpl` | the mail layout (HTML and text) |
| `design/standard/templates/user/registration.tpl` | the page |
| `settings/site.ini [UserSettings]` | the four settings |

## Related pages

- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [E-mail preferences](mail-preferences.md) (the site secret)
- [Audit trail](audit-trail.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
