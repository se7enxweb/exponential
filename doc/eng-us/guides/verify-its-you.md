# Verify it's you: confirming who you are before a sensitive action

This guide explains "Verify it's you" in Exponential 6.0.15. Before a sensitive action, such as changing a password,
assigning a role, clearing caches or exporting data, a signed-in user confirms who they are again: with their
password, one of their passkeys or the code of their authenticator app. A confirmation then counts for ten minutes.
A session someone else took over (a computer left unlocked, a stolen cookie) can then still read pages, but cannot
take over the account or the site.

[Guides](README.md) · Related: [Passkeys](passkeys.md) · [Two-step sign-in](two-factor-login.md) ·
[Security and audit](security-and-audit.md) · Change note:
[Passkeys and "Verify it's you"](../bc/6.0/passkeys-and-verify-its-you.md)

## In short

- Five groups of actions; four are on by default: **your own account**, **users, roles and policies**, **system
  settings and setup actions**, **exporting data**. The fifth, **removing and publishing content**, is off and is
  switched on under **Setup > Verify it's you** (`/setup/reauth`).
- The page "Verify it's you" appears instead of the action. After a right answer the action runs exactly as it was
  sent; the next ten minutes (`[ReauthSettings] Minutes`) ask nothing.
- Five wrong answers in a row sign the user out.
- Only the web pages ask (index.php and Velocity). The REST interface, SOAP, WebDAV, the `expservices` login and the
  command line never ask, and anonymous visitors are never asked.
- Every question, answer and refusal is in the audit: `access.session.reauth.ask`, `access.session.reauth`,
  `access.session.reauth.failed`.

## Contents

1. [The page](#1-the-page)
2. [The groups and their rules](#2-the-groups-and-their-rules)
3. [Switching groups on and off](#3-switching-groups-on-and-off)
4. [Rule syntax](#4-rule-syntax)
5. [Adding a rule for an extension's view](#5-adding-a-rule-for-an-extensions-view)
6. [What happens to the request](#6-what-happens-to-the-request)
7. [Where it is never asked](#7-where-it-is-never-asked)
8. [Codes of an authenticator app](#8-codes-of-an-authenticator-app)
9. [Audit](#9-audit)
10. [Every setting](#10-every-setting)

---

## 1. The page

The page says what the action is about ("You are about to manage users, roles or policies.") and offers the ways the
account has:

- **With your password**: the field and **Confirm**.
- **Use a passkey**: shown when the account has passkeys and the browser can use them; it asks the device to confirm.
- **With a code from your authenticator app**: shown when the account has an authenticator app set up with
  two-step sign-in.

**Cancel** goes back to the page you came from without doing the action. A wrong answer says so and how many tries
are left.

## 2. The groups and their rules

As shipped in `settings/site.ini`:

| Group | On | Rules |
|---|---|---|
| `account`: the security of one's own account | yes | `user/password self`, `user/passkeys *`, `user2fa/setup`, `apikey/list`, `content/edit self credentials`, `user/sessions self SaveMailButton RevokeOthersButton` |
| `users`: users, roles and policies, approving new user accounts | yes | `content/edit other`, `user/password other`, `user/sessions other RevokeSessionButton RevokeOthersButton`, `user/sessionreport RevokeSessionButton ForceSignOutButton`, `user/setting`, `user/unactivated`, `user/hygiene`, `user/deactivate`, `user/registration`, `content/removeuserobject ConfirmButton`, `collaboration/action other CollaborationAction_Accept CollaborationAction_Deny CollaborationAction_Defer`, `role/*`, `oauthadmin/*` |
| `system`: system settings and setup actions | yes | `setup/*` except `setup/info`, `logs`, `logshealth`, `logsdigest`, `logsfeed`, `templatelist`, `templateview`, `menu` and the three streams; `settings/edit`, `package/*`, `package/upload *`, `secrets/*`, `update/*`, `git_manager/*` |
| `export`: exporting data | yes | `infocollector/export *`, `audit/export *`, `content/subitemsexport *`, `mailpreferences/export *`, `bccie/export *`, `bccie/doexport *`, `newsletter/subscription_list_csvexport *`, `newsletter/subscriber_export *`, `newsletter/statistics_export *` |
| `content`: removing and publishing content | no | `content/removeobject ConfirmButton`, `content/removenode ConfirmButton`, `content/trash EmptyButton RemoveButton`, `content/edit PublishButton PublishNotNotifyButton`, `content/versionview PreviewPublishButton PreviewPublishNotNotifyButton`, `content/multiedit MultiPublishButton` |

So changing one's own e-mail address asks (it changes credentials), editing one's own name does not; editing another
user's account always asks. Creating an account below a user group counts as editing another user's account: it is
user management, as the login, e-mail address, password and group of an account decide who can sign in with which
rights, whether or not a field of it changed. The question comes once, when the form is published (or stored), and
the answer runs the form as it was sent (see "What happens to the request").

**Approve** and **Deny** of a new user account waiting for approval (an item of the workflow event *Approve user
accounts*, see [Approving new editor accounts](approving-new-editor-accounts.md)) ask too: the item addresses that
account, so the rule `collaboration/action other ...` of the users group matches. The decision is held until the
approver has answered and is then carried out exactly as pressed. An ordinary content approval addresses no account and
never asks here, not even when the content is a user object; the content group has no rule for approvals either, so
switching it on changes nothing for them. Adding a comment and opening the item never ask.

## 3. Switching groups on and off

**Setup > Verify it's you** (`/setup/reauth`, policy `setup/setup`) has the switch of the whole feature, the minutes a
confirmation counts and a switch per group. **Save** writes `Reauth`, `Minutes` and each `[ReauthGroup_<name>]
Enabled` to the global override `settings/override/site.ini.append.php`, line by line (the rest of the file stays as
it is, a backup goes to `var/backup/ini`), and the change is recorded as `system.setting.write`. Saving is itself a
system setting, so it asks first.

The rules themselves are not edited on the page; they are settings of `site.ini`, changed by whoever deploys the site.

## 4. Rule syntax

A rule is `module/view [token ...]`:

| Part | Meaning |
|---|---|
| `module/view` | the view; `module/*` every view of the module. A POST to it asks |
| `*` | every request asks, GET too: a page that downloads, or a form with a file upload |
| `Name` | only a POST that carries this field (one of several names), usually the action's button |
| `self`, `other` | only when the account the request addresses is the user's own, or another user's (`content/edit` of a user object, `user/password`, `user/setting`, `user/edit`, `user/sessions`; `collaboration/action` addresses the pending account of an item of *Approve user accounts*) |
| `credentials` | only when the POST changes the login, e-mail address or password of a user |
| `!module/view` | an exception: the view never asks in this group (wins over `module/*`) |

`user/login` and `user/logout` never ask, whatever a rule says.

## 5. Adding a rule for an extension's view

Add the group's rules again in an INI file of the extension or the siteaccess, for example in
`extension/myext/settings/site.ini.append.php`:

```ini
[ReauthGroup_system]
Rules[]=myext/dangerous
Rules[]=myext/download *
```

or a group of its own:

```ini
[ReauthSettings]
Groups[]=myext

[ReauthGroup_myext]
Enabled=true
Rules[]=myext/delete DeleteButton
```

Clear the INI cache afterwards. A group of its own appears on `setup/reauth` with its name.

## 6. What happens to the request

- **A POST** is not run. The page posts back to the same address with every field of the original request as hidden
  fields; after a right answer the view runs with exactly those fields.
- **A GET** (a `*` rule) is asked again after the answer: the browser is redirected to the same address.
- **A file upload** cannot be sent twice by a browser: after the answer the form opens again and the files are chosen
  once more. That is why the rule for `package/upload` asks already when the page opens (`*`). Only a file actually
  chosen counts: a form that merely has file inputs, left empty, is a POST like any other. Creating or editing another
  user's account is the usual case: the user edit form has the image of the account, and an editor who creates a
  member without choosing an image confirms once and the account is published as it was filled in, without filling in
  the form again. The page says that files have to be chosen again only when a file was chosen.
- **A script** (XMLHttpRequest, or `Accept: application/json` without HTML) gets `403` with
  `{"error": "reauth_required", "url": "..."}` instead of the page.

The page is never stored by a browser or a shared cache.

## 7. Where it is never asked

The check runs in `ezpKernelWeb`, after the policies allowed the view and before it runs, so only the web pages ask.
The REST interface, SOAP, WebDAV, the `expservices` login and console commands have no such step, and the rule that
only the web sign-in form asks for a second sign-in step stays as it is. Anonymous visitors are never asked.

## 8. Codes of an authenticator app

`[ReauthSettings] CodeHandlers[]` names the classes that check codes (`expReauthCodeHandler`). The shipped
`expReauthTotpCode` reads the authenticator app of `sevenx_authentication_2fa` when that extension is active: a code
counts once per time step (the same step is not accepted again here or at sign-in), and wrong codes count towards the
extension's lock. Accounts with e-mail codes get no code field; they use the password or a passkey.

## 9. Audit

| Event | When |
|---|---|
| `access.session.reauth.ask` | the page is shown (after: group, view, method) |
| `access.session.reauth` | a right answer (after: method, group, view, minutes) |
| `access.session.reauth.failed` | a wrong answer (reason: credentials, code, passkey_..., method; never the password or code) |

The password re-entry of the audit console counts a confirmation made here.

## 10. Every setting

`site.ini [ReauthSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `Reauth` | `enabled` | the whole feature |
| `Minutes` | `10` | minutes a confirmation counts (1 to 1440) |
| `MaxFailures` | `5` | wrong answers in a row before the user is signed out |
| `Groups[]` | `account`, `users`, `system`, `export`, `content` | the groups, each a block `[ReauthGroup_<name>]` with `Enabled` and `Rules[]` |
| `CodeHandlers[]` | `expReauthTotpCode` | classes that check codes |
