# Behaviour changes of 1 and 2 October 2026 (6.0.15 line): what to check when you upgrade

Read this page before you update an installation to the changes of 1 and 2 October 2026. It is a checklist for
operators and extension authors: each item says what changed, how to check it and what to do. The features are
described on the linked pages; the story of the month is in the [October 2026 chronicle](../../history/2026/2026-10.md).

## Deploy order

PHP classes, INI files and template paths all changed. After pulling the changes, run these in order:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezpgenerateautoloads.php -k
php bin/php/ezcache.php --clear-all --allow-root-user
```

`-k` walks the whole installation. If you keep working copies of the installation inside it, list them in
`.autoloadignore` or pass `--exclude=<dir>`, or every kernel class is mapped into the copy.

Then:

1. Reload the PHP-FPM that serves the site.
2. Restart Exponential Velocity if you use it. Its workers keep the classes they loaded at warm-up.
3. Clear the content view cache and Velocity's response cache last.

## Access and security

| Change | How to check | What to do |
|---|---|---|
| Poll votes (`expPoll::vote`), form submissions (`expInfoCollection::submit`) and the VAT country choice (`expVat::setUserCountry`) of the remote services need `content/read`; they were open to everyone. | An anonymous visitor without `content/read` can no longer vote or submit through the services. | Give the Anonymous role `content/read` (a public site already has it). |
| A read of a user's roles, of the policies of a role, of the limitations of a policy or of their values that fails (the database answers with an error) denies what it could not read: the role grants nothing, the policy is left out. Before, a policy whose limitations could not be read was unlimited (`*`). Such an access array is used for that request only and not kept in the user cache; one error per build is logged (`eZRole::accessArrayByUserID`). | `eZAccessArrayFailClosedTest`; with a healthy database nothing changes. | Nothing. An extension that reads `eZPolicy::limitationList()` sees a policy whose limitations could not be read as disabled (`Disabled`) and a limitation whose values could not be read with `ReadFailed` set. |
| The debug bar's settings, change log, IP test and cache list need `setup/setup` or `setup/managecache`. | Before, a visitor who saw the debug report could read every debug setting. | Give the policy to the roles that debug. |
| The admin dashboard, menus and top tabs show a link only to a user who can open it. | Editors no longer see Design, Newsletter, Export tabs, Users, Upload files, Tags, Layouts, Trash entries or "Change password" without the policy. | Nothing; check your custom roles. |
| `ezoe/upload` refuses a file whose type is not in `UploadFileExtensions[]`, and any name with an executable extension anywhere (`shell.php.jpg`), when the user's editor engine is not TinyMCE 3 (`UploadExtensionCheck=engine`). | Upload a `.zip` or `.pdf` with the TinyMCE 8 engine. | Add the type to `ezoe.ini [EditorSettings] UploadFileExtensions[]`. |
| Request rules exist and `Enabled=true`, but `RuleList[]` is empty. | Nothing changes until you list a rule. | See [request rules](../../features/6.0/request-rules.md); check with `php bin/php/ezrequestrules.php -s <siteaccess> --check`. |
| `content/history` opens for who may edit the object, not only for who may read it; its policy functions are `read or edit` (they were `read` and `edit`). A reader still needs an edit policy for some content, as before. Added 6 October 2026. | A role with an edit policy and no read policy for some content now lists the versions of that content (number, status, translation, creator, dates). | Nothing, unless such a role must not see versions; then limit its edit policy as its read policy. See [the versions of a draft](draft-edit-access.md). |
| The history compares, copies and links to the version view only for versions whose content the user may see: published, archived and own versions, a rejected version for its editors, and someone else's draft or pending version only with `content/versionread`. Removing takes only versions of the object in the statuses the page offers. Added 6 October 2026. | A reader with `content/diff` and without `content/versionread` no longer compares other users' drafts; a crafted request to remove a pending version is refused with a message. | Give `content/versionread` to roles that must compare or copy other users' drafts. A template override of `content/history.tpl` should use `$content_versions` (see [the versions of a draft](draft-edit-access.md)); the view refuses either way. |
| A content policy limitation the kernel does not know, without a handler (`site.ini [RoleSettings] LimitationHandlers[]`), gives its policy no access in list and tree fetches too; fetches ignored it and listed objects whose pages `checkAccess()` refused. Added 6 October 2026. | Look in **Users > Roles and policies** (or the table `ezpolicy_limitation`) for limitations other than Class, Section, Owner, Group, Node, Subtree, Language, Status and StateGroup_. | Remove them, or install the extension that handles them. See [content policy limitations of extensions](../../features/6.0/content-limitation-handlers.md). |
| `versionread` and `versionremove` stop at the first limitation of a policy that denies (a `Language` that does not match, a limitation the kernel does not know); the next limitation could allow the policy again. Subtree notifications check the `Group` limitation of `content/read`. Added 6 October 2026. | A role with such a policy reads fewer versions; a subscriber outside the owner's groups gets no mail through a "Self group" policy. | Nothing, unless a role relied on it. |
| The role editor and the policy editor store only the values they offered for a limitation; a value made up in the request is left out and logged. A content limitation handler that throws denies instead of ending the request, and a string condition from `permissionSQL()` that is not self-contained (unbalanced quotes or parentheses, a `;` or a comment outside quoted strings) gives its policy no access in fetches. `eZUser::accessUser()` gives no user for a disabled account. Added 6 October 2026. | A handler whose SQL closed its own parentheses, or whose exceptions were relied on to stop a request, now denies. Checks for another user refuse a disabled account. | Return a self-contained condition or the column form (`array( 'column' => ..., 'values' => ... )`); check a user with `./console exp:access:check`. See [content policy limitations of extensions](../../features/6.0/content-limitation-handlers.md). |
| `role/view` lists the users and groups of a role a page at a time (`site.ini [RoleSettings] AssignmentsPerPage`, default 50). `$user_array` holds one page; every assignment is `$assignment_total`, those the name filter keeps `$assignment_count`. `$user_array` entries carry `user_name` and a `null` `user_object` for a gone object. Added 6 October 2026. | A design that overrides `role/view.tpl` shows only the first 50 assignments and counts `$user_array`. | Add a pager on `(assignment_offset)` as `design/admin/templates/role/view.tpl` does ([role assignment paging](../../features/6.0/role-assignment-paging.md)), or set `AssignmentsPerPage=0` to list all. |
| New policy function `content/publish_without_notification` and `notification.ini [NotificationSettings] PublishWithoutNotification` (`disabled`). The publish operation takes `notify` (default `true`); the filter `content/notification/create` can get `false`. With asynchronous publishing, a publication without notification is published at once, not queued. | Nothing changes until the setting is enabled. A listener of `content/notification/create` that returns `true` whatever it gets overrides the editor. A role with `content/*` includes the new function. | Enable the setting where wanted and grant the function; pass on `$create` in listeners. See [Publish without notification](../../features/6.0/publish-without-notification.md). |
| A user who may edit roles can no longer give more than they have (`site.ini [RoleSettings] PreventPrivilegeEscalation=enabled`, new). The role editor, the policy wizard, the policy editor, `role/copy` and `role/assign` refuse a policy, a copy or an assignment that the editor's own access does not cover, with a message naming the policies. Users with every function of every module are not affected. Added 6 October. | Sign in as a user whose roles include the `role` module but not full access, and add a policy they do not hold: the editor says "The policy was not added". | Give role editors the access they are meant to hand out, or let administrators with full access do it. `PreventPrivilegeEscalation=disabled` gives back the old behaviour. See [roles and policies](../../guides/roles-and-policies.md#who-may-do-this). |
| Activating an account (`user/activate`, and Activate on `user/unactivated`) no longer restarts the registration when none is pending: such an account was disabled again and mailed a new activation link. A pending registration resumes as before. Activate on `user/unactivated` sends the "registration approved" mail again, as the user's own link does (`site.ini [UserSettings] ActivationByAdministratorSendsApprovalMail=enabled`, new). The publish step of an activation compares the object's version as a number: on SQLite it was cancelled, so no activation published the user object or sent the approval mail. Added 6 October. | Activate a registration by hand: the user gets the approval mail (unless `EmailRegistrationInfo=disabled`). | Set `ActivationByAdministratorSendsApprovalMail=disabled` to activate by hand without mail. |
| A user without any `content/read` policy (access word `no`), which is also what a user gets when every read of the access array fails, is listed no node by list, tree, count and calendar fetches, search, trash, the keyword lists and the child counts of the admin: `eZContentObjectTreeNode::createPermissionCheckingSQL( false )` gives `0 = 1`. Before it gave no condition, and such a user was listed every node (whose pages `checkAccess()` then refused). Full access (`yes`, an empty list) still gives no condition. Added 6 October 2026. | On alpha three users in a group without any role were listed all 332 nodes and are now listed none; every other user, anonymous included, gets the same lists as before. | Give `content/read` to the roles of users who must see lists. An extension that calls `createPermissionCheckingSQL( false )` to mean "no limitation" must pass an empty array. |

## Audit trail

The [audit trail](../../features/6.0/audit-trail.md) is **on by default** in every installation (4.x had it off).

- Records go to `var/<site>/log/audit/`.
- On an existing installation, create the index tables once:
  `php update/common/scripts/6.0/createaudittables.php`
- Run the `frequent` cronjob group.
- Old `eZAudit::writeAudit()` calls keep working.
- Only the Administrator role holds `audit/read` and `audit/manage`.

Details: [Audit trail upgrade](audit.md).

## Front end and designs

| Change | What to do |
|---|---|
| YUI is removed from the admin designs and from ezjscore 1.5.0 (`ezjsc::yui2`, `ezjsc::yui3` and `ezjsc::yui3io` load nothing). | Read [YUI removal](yui-removal.md); move the code to jQuery 4 or Exponential UI. |
| `ezjsc::jquery` is jQuery 4.0.0 with Migrate 4.0.2; `ezjsc::jqueryUI` is jQuery UI 1.14.2. | Old templates that use jQuery 1.x features work through Migrate. To see what they use, set `LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.js` (the reporting build) in `ezjscore.ini`. |
| New admin design `admin4`; new `editor` design and siteaccess for new installations. | Opt in with `SiteDesign=admin4` ([admin4](../../features/6.0/admin4-design.md)). An existing installation gets no editor siteaccess. |
| The kernel designs show the Exponential logo; the debug output is headed "Exp Debug". | Change custom CSS or tests that match the old heading text. |
| Removing RSS exports or imports (`rss/list`) and workflow groups (`workflow/grouplist`) shows a confirmation first; only a second post with `ConfirmRemoveButton` and the same ids removes. Removing a workflow group keeps a workflow that is also in another group. | A script or an overridden `rss/list.tpl` or `workflow/grouplist.tpl` that posts `RemoveExportButton`, `RemoveImportButton` or `DeleteGroupButton` now lands on the confirmation (`rss/confirmremove.tpl`, `workflow/confirmremovegroup.tpl`); add `ConfirmRemoveButton=1` to remove at once. See [RSS feeds](../../guides/rss-feeds.md) and [Workflows](../../guides/workflows.md#12-the-workflow-group-list-page). |
| A change to an admin `.css` file needs the template-block cache cleared: the packed `_<mtime>_all.css` link is cached in the page head. `exp:velocity deploy --packer` does it. | `php bin/php/ezcache.php --clear-tag=template --allow-root-user`, plus the content and template-block caches. |

## Code structure

Every kernel command, cronjob part and module view is now a class
([specification](../../specifications/6.0/runnable-commands-cronjobs-views.md)). Entry points keep their paths.

- Code that read the old files for their logic must look in `kernel/private/classes/`.
- An extension can replace a class with its own subclass:
  `site.ini [RunnableSettings] Implementation[<class>]=<subclass>`.
- `checkAccess()` of `eZContentObject`, `eZContentObjectTreeNode` and `eZContentObjectVersion` takes the user to check
  for as a sixth argument, `$userID = false` (added 6 October 2026). A class that overrides one of them must accept it,
  or PHP 8 stops with a fatal error when the class is loaded.

## Data and jobs

| Change | What to check or do |
|---|---|
| "Empty trash" purges in batches of 100, each in its own transaction, with a one second pause. | Large trashes take longer in the browser but no longer time out. Use `bin/php/trashpurge.php` for the largest. |
| "Remove timed out sessions" also removes the shop baskets of those sessions. | Nothing. |
| The `clusterpurge` cronjob part purges files that expired 30 days ago (it used 30 seconds). | On a clustered installation, expired cache files now live their grace period. |
| A copied object with an image owns its own image files; removing a draft removes all of its image files. | Nothing. |
| Removing a media attribute deletes the file only when no other media row names it. | Nothing. |
| Removing a subtree that holds every location of an object removes the object. | Nothing. |
| Removing, copying or moving more than 50 nodes offers a [content job](../../features/6.0/content-jobs.md) first. | Tune `content.ini [ContentJobSettings] SynchronousLimit` (default `50`). |
| The static cache refresh on publish fetches each page once, in parallel. | See the [static cache generator](../../features/6.0/static-cache-generator.md); tune `staticcache.ini [CacheSettings] FetchConcurrency` (default `8`, not listed in the shipped file). |
| SQLite transactions queue for the write lock. | See [SQLite transactions](sqlite-transactions.md). |

## Missing libraries

A missing `vendor/` directory now shows a page with a repair procedure instead of PHP's raw error output. See
[repair from the browser](../../features/6.0/repair-from-the-browser.md) and [the repair page upgrade](repair.md).

Since 9 October 2026, `eZSOAPClient` refuses a call over HTTPS when the PHP extension curl is not loaded: `send()`
returns `0`, `ErrorString` says that HTTPS needs the PHP extension curl, and the error log has
"No SOAP call to <server>: HTTPS needs the PHP extension curl, which is not loaded". Before, such a call went as plain
text to the TLS port, which no HTTPS server answers with a SOAP response. Check with `php -m | grep curl` (and in the
PHP of the web server) that curl is loaded wherever SOAP calls over HTTPS are made. Calls over HTTP are unchanged.
See [SOAP calls over HTTPS](../../features/6.0/soap-client-tls.md).

## Related pages

- [October 2026 chronicle](../../history/2026/2026-10.md)
- [Behaviour changes of 16 to 30 September 2026](behaviour-changes-2026-09b.md)
- [Behaviour changes of July and August 2026](behaviour-changes-2026-07-08.md)
- [Extensions: behaviour changes](extensions-behaviour-changes.md)
- [Upgrading guide](../../guides/upgrading.md)
