# Check user accounts

Read this page if you look after the user accounts of an installation: which accounts nobody uses, which are
disabled or locked, which have lost their user object, which share an e-mail address, and how to tidy them up
without opening each one. The page is **Users > Check user accounts** (`user/hygiene`), also a tile of the Setup
overview in the group *Users and messages*. Added 2026-10-15.

## In short

- One list of every user account, or of those below a user group, with a filter: **All accounts**, **Never signed
  in**, **Not seen for N days** (180 by default), **Disabled**, **Locked**, **Orphaned** and **Shared e-mail
  address** (only with the right to see addresses). Each filter shows how many accounts it holds; a search (name, login and, with the right to see them,
  e-mail address) and the page size (25, 50, 100) narrow it further. The columns name, login, e-mail address and
  last visit, and the number of sign-ins, order the list either way.
- Each row: the name (a link to the user's node), login, e-mail address (only with the right, below), the states,
  the last visit with the days since, the number of sign-ins, the groups (the parents of its locations) and the
  roles it has, its own and those of the groups above it.
- **Disable**, **Enable** and **Unlock** the ticked accounts. Each button shows a confirmation with the number of
  accounts it will change and the number it leaves out, and why; **Confirm** does it.
- **Export the list as CSV** sends the whole filtered list, every page, as a UTF-8 file with a byte order mark.
- Nothing here removes an account. A user object never goes to the trash in Exponential
  (`eZContentObjectTreeNode::isNodeTrashAllowed()`): removing it is final, so it stays with the node's own
  Remove, one account at a time.
- Instead of removing accounts: **Deactivate and move** (opt-in, `[UserSettings] DeactivateAndMove=enabled`)
  disables accounts and brings all their locations together in one hidden group without roles, remembering where
  they were; **Reactivate and move back** puts them back. See [Deactivate and move](#deactivate-and-move).

## The filters

| Filter | Accounts |
|---|---|
| All accounts | every account whose object is not in the trash |
| Never signed in | no sign-in counted (`ezuservisit.login_count` 0, or no row) |
| Not seen for N days | signed in at least once, and the last visit is more than N days ago (1 to 36500) |
| Disabled | `ezuser_setting.is_enabled` 0; a disabled account with an activation key is marked *Not activated* |
| Locked | more failed sign-ins than `site.ini [UserSettings] MaxNumberOfFailedLogin`, as `eZUser::isLocked()` decides; none while the setting is 0 |
| Orphaned | no user object, an object that was never published, or a published object without a location |
| Shared e-mail address | an address (any case) that another listed account has as well; ordered by address, so the accounts of one address are side by side. Only with `role/assign` |

The **last visit** is `ezuservisit.current_visit_timestamp`, the time of the last sign-in or of the first request
after a longer pause (`site.ini [Session] ActivityTimeout`); a sign-out stores it negated, which the page reads as
the same time. It is not `eZUser::lastVisit()`, which gives the visit before that.

The **states** of a row, the most pressing first: *Disabled* or *Not activated*, *Locked*, *No user object*,
*Never published* or *No location*, *Never signed in*; *Active* when none applies.

## The actions

| Action | What it does | Left out |
|---|---|---|
| Disable | the operation `user/setsettings` with *enabled* off, as **Edit user settings** does: the account cannot sign in and its sessions end. Content, groups and roles stay. | accounts already disabled |
| Enable | the same operation with *enabled* on | accounts already enabled; accounts that never activated (they have an activation key): activate those on **Unactivated users**, which finishes the registration |
| Unlock | `eZUser::setFailedLoginAttempts( id, 0, true )`, as **Reset failed logins** does | accounts without failed sign-ins |

Every action, for every account, also leaves out:

- **the anonymous user** (`[UserSettings] AnonymousUserID`), **the user creator** (`[UserSettings] UserCreatorID`),
  **the administrator account of a clean installation** (id 14), **the account with the login `admin`** and **you**.
  Their boxes cannot be ticked, and an id sent by hand is refused as well;
- accounts whose user object you may not edit (`content/edit` on it, with its limitations);
- accounts without a user object (there is nothing to check the access on).

The confirmation decides for each ticked account; **Confirm** decides again for each one just before it changes
it, so a list that is out of date changes nothing it should not. One action takes at most 100 accounts, the largest
page.

Each change is recorded by the kernel's own audit events, one per account: `access.user.disable`,
`access.user.enable` and `access.user.unlock` (channel *access*). The CSV is recorded as `data.export.csv` with the
number of rows, the filter and whether e-mail addresses were in it, never the values.

## Who may use it

| What | Needs |
|---|---|
| The page, its actions and the CSV | the function **user/hygiene** (`kernel/user/module.php`). It is in no role of a clean installation but *Administrator* (`*/*`); give it to the people who look after accounts, not to editors. |
| Changing an account | also `content/edit` on that account's user object |
| E-mail addresses (column, search, order, the filter *Shared e-mail address*, CSV) | `role/assign`, the same right as the E-mail column of the sub-items table (`settings/subitemscolumns.ini [Column_useremail]`) |

Every change and the CSV are POST requests with the form token of `ezformtoken`; a link cannot change an account
or start a download. Everyone who may open the page sees the name, login, states, groups and roles of every account
(there is no read check per account), so give `user/hygiene` only to the people who look after accounts.

## Deactivate and move

Editorial teams remove user accounts because there is no other way to retire them, and a removed user object is gone
for good: users never go to the trash. **Deactivate and move** retires an account instead: it is disabled, and all
its locations are brought together in one user group for deactivated accounts, which has no roles and is hidden.
Where it was is remembered, so **Reactivate and move back** enables it and puts it back. Nothing is removed: its
content, its versions, the roles assigned to the account itself and the relations stay.

**Off by default (opt-in).** `site.ini [UserSettings] DeactivateAndMove=disabled` ships; with it nothing is shown
(no button, no menu entry, no bulk action), `user/deactivate` answers *not found* and nothing is created.

```ini
# settings/override/site.ini.append.php, or the admin siteaccess's site.ini.append.php
[UserSettings]
DeactivateAndMove=enabled
# optional: a group of your own instead of the one found by its remote id
DeactivatedUserGroupNodeID=
```

### The group

- `DeactivatedUserGroupNodeID` set: that node is the group. A node id that does not exist stops the action with a
  message; it is never replaced by a new group.
- Empty (the default): the user group with the remote id `exp_deactivated_users_group`. When there is none yet,
  the first **Deactivate and move** creates it below `content.ini [NodeSettings] UserRootNode` (the Users node):
  a user group (`[UserSettings] UserGroupClassID`) named *Deactivated users* in the language of the
  administration, hidden, without roles, in one transaction, recorded as `access.user.deactivated_group.create`. It
  is created only by a user who may create a user group there (`content/create`); anyone else gets a message saying
  so, and nothing changes.
- Or create it beforehand, for instance on a site whose editors may not create groups:

  ```bash
  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>             # dry run: what it would do
  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit    # creates it
  #   --name="Former staff"   the group's name      --section-id=N   a section of its own
  ```

  `-s` is required (the group belongs to that siteaccess's database). The script publishes as
  `[UserSettings] UserCreatorID`, prints the node id and the settings lines, and never writes an INI file. The
  installer creates nothing.

The group is hidden so that deactivated accounts do not show on the site, in its lists of users or in a search of
it (`ShowHiddenNodes=false`); the administration shows them, marked *Hidden by a parent*. Give the group no roles:
a role assigned to it would be a role of every deactivated account.

### Deactivate and move

For each account, in one transaction (nothing stays when a step fails):

1. its locations are remembered: the parent of the main location and the parents of the others;
2. it is disabled through the operation `user/setsettings` (as **Disable**; its sessions end; `access.user.disable`);
3. the locations other than the main one are removed (operation `content/removelocation`);
4. the main location is moved into the group (operation `content/move`); the node keeps its id.

Recorded as `access.user.deactivate_move` with the locations before and after, beside the kernel's own events of
the steps. An account already disabled and only in the group is left out (*already in that state*).

### Reactivate and move back

For each account, in one transaction:

1. it is enabled (operation `user/setsettings`, `access.user.enable`);
2. when its main location is in the group, it is moved back to the remembered main parent; when that one no longer
   exists, or you may not place the account there, it goes to `[UserSettings] DefaultUserPlacement` instead;
3. the other remembered parents that still exist and may take it get a location again (operation
   `content/addlocation`); the others are left out;
4. the remembered locations are forgotten.

The confirmation and the result say how many former locations are left out, *gone or not yours to use* together,
so the number tells nothing about nodes you may not see. Recorded as `access.user.reactivate_move`. An account that
never activated is left out (activate it on **Unactivated users**); one with nowhere to go (no remembered main
parent and no default user group it may enter) is left out too.

### Where the locations are remembered

One row of `ezsite_data` (`eZSiteData`) per deactivated account, named `exp_user_deactivation_<user id>`, its
value JSON: `main` (parent node of the main location), `parents` (parents of the other locations), `group`, `at`,
`by`. Chosen over a table of its own because it needs no database update, works on every engine the kernel
supports through `eZPersistentObject` (measured on MySQL/MariaDB; MongoDB is not tested), and an installation
can switch the feature on without running anything. The row holds node ids only, no names; it is removed on
reactivation. **Setup > Site data** lists it (for `setup/system_info`). A row of an account that was removed later
stays behind and does no harm.

### Where

| Where | What |
|---|---|
| A user's page (node view of admin4 and admin) | **Deactivate and move** for an enabled account or one outside the group, **Reactivate and move back** for a disabled one, one in the group or one with remembered locations; next to Remove |
| The context menu, *Advanced* | **Deactivate and move (user accounts)** and **Reactivate and move back (user accounts)**; the menu does not know the class, so for an item that is no user account the confirmation says so and offers nothing |
| Users > Check user accounts | **Deactivate and move the ticked accounts** and **Reactivate and move back the ticked accounts** beside Disable and Enable (which stay as they are); an account in the group carries a badge *deactivated* in its groups |

Every entry leads to the confirmation page `user/deactivate`: each account with the groups it is in now and where it
goes, the others counted by why they are left out; **Confirm** decides again for each account just before it is
changed. At most 100 accounts at once.

### Who may use it

| What | Needs |
|---|---|
| The buttons, the menu entries and `user/deactivate` | the function **user/deactivate** (in no role of a clean installation but *Administrator*, `*/*`); with `user/hygiene` for the bulk actions on Check user accounts |
| Each account | `content/edit` on its user object, and `content/read` on each of its locations |
| Deactivate | at the main location `content/edit` and `content/remove` (moving it, `canMoveFrom()`), `content/create` of the user class in the group (`canMoveTo()`); at every other location `content/remove` or the right to remove a location |
| Reactivate | moving the main location out of the group (`canMoveFrom()`), and `content/create` of the user class or the right to add a location where it goes |
| Creating the group on first use | `content/create` of the user group class below the Users node |

Never touched: the anonymous user, the user creator, the administrator account (id 14, login `admin`) and you (as
for the other actions). An account whose object you may not read is answered as *not found, or not visible to
you*, the same as an id that does not exist, so the page tells nothing about objects you may not see. Names are
shown only of accounts you may change and of groups you may read. Every change is a POST with the form token of
`ezformtoken`; the way back after the result is the page you came from (`user/hygiene` or the user's node), never
an address from outside.

## The CSV

Columns: *User ID*, *Name*, *Login*, *E-mail* (only with `role/assign`), *Status* (the states, comma separated),
*Last visit* (`YYYY-MM-DD HH:MM UTC`, empty when never), *Sign-ins*, *Groups*, *Roles*. The headers are in the
language of the administration.

UTF-8 with a byte order mark, comma separated, CRLF line ends, as the other CSV files of the administration
(`expSubitemsCSVExport`). A cell that starts with `=`, `+`, `-`, `@`, a tab or a carriage return (and is no number)
gets a leading apostrophe, so a spreadsheet shows it as text instead of running it as a formula. The file holds at
most 100,000 accounts; they are read 500 at a time by id.

## Many accounts

The list and its count are one query each over `ezuser` and its one-row companions (`ezuser_setting`,
`ezuservisit`, `ezcontentobject`); the groups, roles, activation keys and shared addresses of a page are five
queries for the whole page, whatever its size; a view of the page adds one count per filter for the tabs (13
queries in all). Measured with 50,000 fixture accounts in SQLite on a laptop (PHP 8.5): a page of 100 with its count
in 26 to 146 ms depending on the filter (shared addresses the slowest), a whole view of the page with the counts of
every filter in 216 ms, a page at offset 49,000 in 108 ms, the CSV of all 50,000 accounts in 1.6 s with 12 MB of
memory. An action reads each ticked account on its own (its object, setting, key and access), at most 100.

The page needs an SQL database (MySQL, MariaDB, PostgreSQL, SQLite, Oracle); on MongoDB it says so and lists nothing.

## In the menus

- Users tab, left menu: `menu.ini [Leftmenu_user] Links[user_hygiene]`, with its icon and description, and in the
  Users tab's mega menu (`[Leftmenu_user_mega] MegaMenuLinks[]=access-user_hygiene`, group *Access control*).
- Setup overview and Setup menu: `menu.ini [Leftmenu_setup] Links[user_hygiene]` in `GroupLinks_users[]`, with the
  icon `person-badge` (`LinkIcons[user_hygiene]`, added to `design.ini [AdminDesignSettings] IconList[]`) and its
  description (`LinkDescriptions[user_hygiene]`).

Both use the link key `user_hygiene`, so a menu that lists links by key can name it as well.

## Files

| File | What |
|---|---|
| `kernel/user/hygiene.php`, `kernel/private/classes/views/user/hygiene.php` | the view |
| `kernel/classes/user/expuserhygiene.php` | `expUserHygiene`: the filters, the queries, the decisions of the actions, the CSV |
| `design/admin4/templates/user/hygiene.tpl`, `design/admin/templates/user/hygiene.tpl` | the page, the same file in both designs |
| `tests/tests/kernel/classes/user/expUserHygieneTest.php` | the tests: every filter against an SQLite database built in the test, the decisions, the CSV, the page |
| `kernel/classes/user/expuserdeactivation.php` | `expUserDeactivation`: Deactivate and move, the group, the remembered locations, the decisions |
| `kernel/user/deactivate.php`, `kernel/private/classes/views/user/deactivate.php` | the view `user/deactivate` (confirmation and result) |
| `design/admin4/templates/user/deactivate.tpl`, `design/admin/templates/user/deactivate.tpl` | its page, the same file in both designs |
| `bin/php/ezcreatedeactivatedusergroup.php`, `kernel/private/classes/commands/ezcreatedeactivatedusergroup.php` | creates the group beforehand |
| `tests/tests/kernel/classes/user/expUserDeactivationTest.php`, `expUserDeactivationLiveTest.php` | the decisions, the plan of the way back, the page and its entry points; the kernel paths against the installation's database, each test rolled back |

## Related pages

- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [Security and audit](../../guides/security-and-audit.md)
- [The admin4 design](admin4-design.md)
