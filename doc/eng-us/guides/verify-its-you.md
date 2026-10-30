# Verify it's you: confirming who you are before a sensitive action

This guide explains "Verify it's you" in Exponential 6.0.15. Before a sensitive action, such as changing a password,
adding a passkey, assigning a role, clearing caches, exporting data or emptying the trash, a signed-in user confirms
who they are again: with their password **and** one of their passkeys, or, without a passkey, their password and the
code of their authenticator app. A confirmation then counts for ten minutes. A session someone else took over (a
computer left unlocked, a stolen cookie) can then still read pages, but cannot take over the account or the site.

[Guides](README.md) · Related: [Passkeys](passkeys.md) · [Two-step sign-in](two-factor-login.md) ·
[Security and audit](security-and-audit.md) · Change note:
[Passkeys and "Verify it's you"](../bc/6.0/passkeys-and-verify-its-you.md)

## In short

- Six groups of actions; five are on by default: **your own account**, **users, roles and policies**, **system
  settings and setup actions**, **exporting data**, **deleting data**. The sixth, **removing, publishing and saving
  content**, is off and is switched on under **Setup > Verify it's you** (`/setup/reauth`), for every siteaccess or
  one, and for every user or the members of some user groups.
- The ways to confirm follow `[ReauthSettings] Factors[]`: by default the password and a passkey; without a passkey
  the password and the app's code; without either the password alone; an account without a password uses a passkey.
  A way of the same strength is offered beside the first one, never a weaker one.
- The page appears instead of the action. After a right answer the action runs exactly as it was sent; the next ten
  minutes (`[ReauthSettings] Minutes`) ask nothing. A sign-in that asked for as much counts as a confirmation.
- Five wrong answers in a row sign the user out.
- Only the web pages ask (index.php and Velocity). The REST interface, SOAP, WebDAV, the `expservices` login and the
  command line never ask, and anonymous visitors are never asked.
- Every question, answer and refusal is in the audit: `access.session.reauth.ask`, `access.session.reauth`,
  `access.session.reauth.failed`.

## Contents

1. [The page](#1-the-page)
2. [The ways to confirm](#2-the-ways-to-confirm)
3. [The groups and their rules](#3-the-groups-and-their-rules)
4. [Setup > Verify it's you](#4-setup--verify-its-you)
5. [Rule syntax](#5-rule-syntax)
6. [For extensions](#6-for-extensions)
7. [What happens to the request](#7-what-happens-to-the-request)
8. [Where it is never asked](#8-where-it-is-never-asked)
9. [Audit](#9-audit)
10. [Every setting](#10-every-setting)

---

## 1. The page

The page says what the action is about ("You are about to manage users, roles or policies.") and shows one card per
way of confirming offered to the account, the first the one to use:

- **Your password and a passkey**: type the password and choose **Continue with a passkey**; the browser then asks
  for one of your passkeys (fingerprint, face, screen lock or security key).
- **Or: Your password and a code from your authenticator app**: offered beside it when the account also has an
  authenticator app, for a browser that cannot use passkeys.
- **With your password**: an account with neither a passkey nor an app.
- **With a passkey**: an account without a password.

**Cancel** goes back to the page you came from without doing the action. A wrong answer says so and how many tries
are left. On a browser without passkeys the passkey card's button is disabled and says to use another way.

## 2. The ways to confirm

`[ReauthSettings] Factors[]` lists combinations of `password`, `passkey` and `code` (the authenticator app, see
`CodeHandlers[]`), strongest first:

```ini
[ReauthSettings]
Factors[]
Factors[]=password+passkey
Factors[]=password+code
Factors[]=password
Factors[]=passkey
```

The first combination whose every way the account has is asked; the later ones with as many ways are offered beside
it. So an account with a password, a passkey and an app gets "password and passkey" with "password and code" beside
it, never "password" alone: a stolen password cannot choose its way around the second step. The password and the
code are checked before the passkey. Accounts with e-mail codes of two-step sign-in have no `code`: they use the
password and a passkey, or the password.

**A sign-in counts.** A web sign-in that asked for one of the combinations the account would be offered counts as a
confirmation: the password of an account that has nothing more, the password and the app's code of two-step
sign-in, the password and a passkey as the second step, a passkey of an account without a password. The password
alone of an account that has a passkey counts for nothing.

## 3. The groups and their rules

As shipped in `settings/site.ini`:

| Group | On | Rules |
|---|---|---|
| `account`: the security of one's own account | yes | `user/password self`, `user/passkeys *`, `user2fa/setup` (setting up and removing two-step sign-in, new recovery codes, app passwords), `apikey/list`, `content/edit self credentials`, `user/sessions self SaveMailButton RevokeOthersButton` |
| `users`: users, roles and policies, approving new user accounts | yes | `content/edit other`, `user/password other`, `user/sessions other *` (another user's devices in full), `user/sessionreport *`, `user/setting`, `user/unactivated`, `user/hygiene`, `user/deactivate`, `user/registration`, `content/removeuserobject ConfirmButton`, `collaboration/action other CollaborationAction_Accept CollaborationAction_Deny CollaborationAction_Defer`, `role/*`, `oauthadmin/*` |
| `system`: system settings and setup actions | yes | `setup/*` except `setup/info`, `logs`, `logshealth`, `logsdigest`, `logsfeed`, `templatelist`, `templateview`, `menu` and the three streams; `settings/edit`, `package/*`, `package/upload *`, `secrets/*`, `update/*`, `git_manager/*` |
| `export`: exporting data | yes | `infocollector/export *`, `audit/export *`, `content/subitemsexport *`, `mailpreferences/export *`, `user/security DownloadButton`, `user/audit ExportButton`, `bccie/export *`, `bccie/doexport *`, `newsletter/subscription_list_csvexport *`, `newsletter/subscriber_export *`, `newsletter/statistics_export *` |
| `delete`: deleting data | yes | `content/trash EmptyButton RemoveButton`, `content/removeobject * bulk` (removing two or more items at once, `BulkCount`) |
| `content`: removing, publishing and saving content | no | `content/removeobject ConfirmButton`, `content/removenode ConfirmButton`, `content/edit PublishButton PublishNotNotifyButton StoreButton StoreExitButton`, `content/versionview PreviewPublishButton PreviewPublishNotNotifyButton`, `content/multiedit MultiPublishButton` |

So changing one's own e-mail address asks (it changes credentials), editing one's own name does not; editing another
user's account always asks. Signing out one other device of one's own does not wait for a question (someone who sees a
strange device acts at once); signing out all of them does. Clearing the session list is `setup/session` (system); the
audit log has no page that clears it.

**Approve** and **Deny** of a new user account waiting for approval (an item of the workflow event *Approve user
accounts*, see [Approving new editor accounts](approving-new-editor-accounts.md)) ask too: the item addresses that
account, so the rule `collaboration/action other ...` of the users group matches. An ordinary content approval
addresses no account and never asks here.

**The content group** asks when the edit form is sent with Publish, Store draft or Store and exit, or when a removal is
confirmed, before the page goes on to the content: a hijacked session or a script cannot publish or delete. The
freshness window applies, so an editor confirms once and then saves for ten minutes without being asked.

## 4. Setup > Verify it's you

**Setup > Verify it's you** (`/setup/reauth`, policy `setup/setup`) has the switch of the whole feature, the minutes a
confirmation counts, a switch per group and, for the content group, the user groups it asks (none ticked: every
user). **Settings for** chooses where the values apply:

- **Every siteaccess**: written to the global override `settings/override/site.ini.append.php`.
- **Siteaccess <name>** (`/setup/reauth/<name>`): written to `settings/siteaccess/<name>/site.ini.append.php`, for
  example to ask on the editors' siteaccess only. A value in `settings/override` wins over it.

Values are written line by line (the rest of the file stays as it is, a backup goes to `var/backup/ini`), and the
change is recorded as `system.setting.write`. Saving is itself a system setting, so it asks first. The rules
themselves are not edited on the page; they are settings of `site.ini`, changed by whoever deploys the site.

## 5. Rule syntax

A rule is `module/view [token ...]`:

| Part | Meaning |
|---|---|
| `module/view` | the view; `module/*` every view of the module. A POST to it asks |
| `*` | every request asks, GET too: a page that downloads or shows something in full, or a form with a file upload |
| `Name` | only a POST that carries this field (one of several names), usually the action's button |
| `self`, `other` | only when the account the request addresses is the user's own, or another user's (`content/edit` of a user object, `user/password`, `user/setting`, `user/edit`, `user/sessions`; `collaboration/action` addresses the pending account of an item of *Approve user accounts*) |
| `credentials` | only when the POST changes the login, e-mail address or password of a user |
| `bulk` | only when several items are removed at once (`[ReauthSettings] BulkCount`, 2 by default) |
| `!module/view` | an exception: the view never asks in this group (wins over `module/*`) |

A group may also name `UserGroups[]` (content object ids of user groups): it then asks only their members.
`user/login` and `user/logout` never ask, whatever a rule says.

## 6. For extensions

**A rule.** Add the group's rules again in an INI file of the extension or the siteaccess, for example in
`extension/myext/settings/site.ini.append.php`:

```ini
[ReauthGroup_system]
Rules[]=myext/dangerous
Rules[]=myext/download *
```

or a group of its own, which appears on `setup/reauth` with its name:

```ini
[ReauthSettings]
Groups[]=myext

[ReauthGroup_myext]
Enabled=true
Rules[]=myext/delete DeleteButton
```

**A trigger handler** decides where a rule cannot, from the extension's own data. A class implementing
`expReauthTriggerHandler`, listed in `[ReauthSettings] TriggerHandlers[]`, gets
`reauthGroup( $module, $view, $method, $post, $params, $user )` for every request the rules did not match and names
a group, or null. It asks only while that group is on and applies to the user.

**From a view.** `expReauth::requireFresh( $Params['Module'], 'myview', 'system' )` returns null when the user
confirmed within the window, else the module result to return instead (the page, which posts the request again after
the answer):

```php
if ( ( $result = expReauth::requireFresh( $Params['Module'], 'wipe', 'delete' ) ) !== null )
    return $result;
```

**Codes.** `[ReauthSettings] CodeHandlers[]` names the classes that check codes (`expReauthCodeHandler`). The
shipped `expReauthTotpCode` reads the authenticator app of `sevenx_authentication_2fa` when that extension is active:
a code counts once per time step, and wrong codes count towards the extension's lock.

Clear the INI cache after changing settings.

## 7. What happens to the request

- **A POST** is not run. The page posts back to the same address with every field of the original request as hidden
  fields; after a right answer the view runs with exactly those fields.
- **A GET** (a `*` rule) is asked again after the answer: the browser is redirected to the same address.
- **A file upload** cannot be sent twice by a browser: after the answer the form opens again and the files are chosen
  once more. Only a file actually chosen counts: a form that merely has file inputs, left empty, is a POST like any
  other.
- **A script** (XMLHttpRequest, or `Accept: application/json` without HTML) gets `403` with
  `{"error": "reauth_required", "url": "..."}` instead of the page.

The page is never stored by a browser or a shared cache.

## 8. Where it is never asked

The check runs in `ezpKernelWeb`, after the policies allowed the view and before it runs, so only the web pages ask.
The REST interface, SOAP, WebDAV, the `expservices` login and console commands have no such step. Anonymous visitors
are never asked.

## 9. Audit

| Event | When |
|---|---|
| `access.session.reauth.ask` | the page is shown (after: group, view, method) |
| `access.session.reauth` | a right answer (after: method such as `password+passkey`, group, view, minutes) |
| `access.session.reauth.failed` | a wrong answer (reason: credentials, code, passkey_..., method; never the password or code) |

A passkey used here is also recorded as `access.passkey.use` with the purpose `reauth`. The password re-entry of the
audit console counts a confirmation made here.

## 10. Every setting

`site.ini [ReauthSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `Reauth` | `enabled` | the whole feature |
| `Minutes` | `10` | minutes a confirmation counts (1 to 1440) |
| `MaxFailures` | `5` | wrong answers in a row before the user is signed out |
| `Factors[]` | `password+passkey`, `password+code`, `password`, `passkey` | the ways to confirm, strongest first (section 2) |
| `Groups[]` | `account`, `users`, `system`, `export`, `delete`, `content` | the groups, each a block `[ReauthGroup_<name>]` with `Enabled`, `Rules[]` and optionally `UserGroups[]` |
| `CodeHandlers[]` | `expReauthTotpCode` | classes that check codes |
| `TriggerHandlers[]` | empty | classes of extensions that name a group for a request (section 6) |
| `BulkCount` | `2` | the number of items at which a removal counts as `bulk` |
