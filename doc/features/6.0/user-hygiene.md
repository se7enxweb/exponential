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

## Related pages

- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [Security and audit](../../guides/security-and-audit.md)
- [The admin4 design](admin4-design.md)
