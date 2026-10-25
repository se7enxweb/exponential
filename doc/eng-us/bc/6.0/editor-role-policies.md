# Upgrade note: the everyday policies of the Editor role

A user in the **Editors** group has the policies of the **Editor** role, and on the site packages also those of the
**Member** role. This note says what an editor of a new installation could not do, what changes, and how to complete
the role of an installation that exists.

## What was wrong

The base data gives the Editor role `content/*` (with `ezoe/*`, `user/login` and the interface choices) and assigns it
to the Editors group twice, limited to the subtrees Content (`/1/2/`) and Media (`/1/43/`). Every site package except
`plain_site` removes `content/*` and adds a list of single functions instead: `content/create` limited by class and
parent class, `content/edit`, `content/read` limited to the sections Standard, Restricted and Media, `content/remove`,
`content/manage_locations`, `content/translate`, the versions, bookmarks, the pending list and the dashboard; it also
assigns the Member role (own password, own account, notifications) to the Editors group.

That list leaves out three functions an editor needs every day, and no package grants the multi-upload:

| What the editor does | Refused because | Since |
| --- | --- | --- |
| Hide or reveal a node (`content/hide/<node>`, the node menu's Hide and Reveal) | no `content/hide` | the ezwebin installers the packages are made from |
| Look into the trash (`content/trash`) and restore an object (`content/restore/<object>`) | no `content/restore` | as above: an editor could remove to the trash but not get anything back |
| See which objects use one (`content/reverserelatedlist/<node>`) before moving or removing it | no `content/reverserelatedlist` | as above |
| Upload several files at once into the media library (`ezmultiupload/upload/<node>`, the AdminUI dashboard's Upload) | no policy for the module `ezmultiupload` | the packages have installed ezmultiupload without a policy for it |
| Create an article with a map (`geo_article`) or a video (`video`) | the class lists predate these two classes of the class package | as above |
| Edit an image in the edit form (the **Edit image** button of expimageeditor, which takes the place of ezie) | no `expimageeditor/edit`: the button is drawn only for a user with it, so editors saw no image editor at all | expimageeditor (root pull request #302); ezie's module needed `ezie/*`, which no package granted either |

On `plain_site`, which keeps the base data's role and assigns no Member role, an editor could not change the own
password (`user/password`), edit the own account (`user/edit`, function *selfedit*) or open the notification settings
(`notification/use`).

## What changes

- **The setup wizard and the kickstarter** check, after the site package's roles, that the Editor role has
  `content/hide`, `content/restore`, `content/reverserelatedlist`, `ezmultiupload/*` and `expimageeditor/edit` (each
  when the installation has the extension), and `user/password`, `user/selfedit` and `notification/use` unless another role of the Editors group
  (the Member role of the site packages) has them; whatever is missing is added
  (`expEditorPolicies::ensure()`, in `kernel/setup/steps/ezstep_create_sites.php`). This holds for every site
  package and every base data, on every database engine.
- **The site packages** `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_site`, `sevenx_site_clean` and
  `cjw_multisite` add the four policies to their Editor step themselves, and let editors create `geo_article` and
  `video` below a folder (with the website toolbar for both).
- **`bin/php/addeditorpolicies.php`** completes the role on an installation that exists (below).
- **plain_site's `content/*` is narrowed** (24 October 2026). Before the policies above are checked, the setup step
  replaces an Editor role's `content/*` without limitations, which `plain_site` keeps from the base data, by the
  content functions the site packages give their editors: `create`, `edit`, `read`, `manage_locations`, `diff`,
  `versionread`, `versionremove`, `remove`, `translate`, `bookmark`, `pendinglist`, `dashboard`, `view_embed`, `hide`,
  `restore` and `reverserelatedlist` (`expEditorPolicies::narrowPlan()` and `narrow()`). They are added without
  limitations, so the role's assignment limits them to Content and Media as it limited `content/*`. What an editor of
  `plain_site` loses is what `content/*` granted beyond them: emptying everybody's trash (`cleantrash`), the
  installation's languages (`translations`), its URL aliases and wildcards (`urltranslator`), everybody's content jobs
  (`jobs`), and `move`, `publish`, `pdf`, `tipafriend`, `view_system_url` and `publish_without_notification`, which no
  view of an editor asks for. The base data is unchanged: the other packages remove `content/*` themselves and find
  nothing to narrow, and a `content/*` with limitations is the site's choice and left alone.

The policies are added to the Editor role, so they are limited by the role's assignment like the rest of it: for the
Editors group, to the Content and Media subtrees. Hiding and the reverse related list work there only.

## What stays as it is, and why

- **Emptying the trash** (`content/cleantrash`) removes everybody's trashed objects for good; editors restore, an
  administrator empties.
- **Users**: the Editor role reads and edits nothing below Users (`/1/5/`); editors change their own account through
  `user/edit` and `user/password`. Managing the site's members is the role "Member management", which the setup step
  makes from `membermanagement.ini` and assigns to the Editors group, limited to Members, so that editors never reach
  the administrators' and editors' accounts ([Member management](member-management.md)).
- **Tags, newsletters, layouts, object states**: no class of the default class package has a tags field, the
  newsletter's editorial views are cjw_newsletter's own roles, the layout editor (`explayouts_ui`) and the state module
  are switched off in the editor siteaccess, and the only state group of a new installation is the lock group.
- **The Git manager and the Updates dashboard** stay with administrators: their modules check their own policies
  (`git_manager/git_manager`, `update/ezupdate`), the dashboard shows their "Stay secure" card only to a user who can
  open them (`fetch( 'user', 'can_open', ... )`), and the editor siteaccess switches the Git manager off besides.

## Over-broad grants the script reports

`bin/php/addeditorpolicies.php` prints a `WARN` line for each function the role holds that an editor role should not,
and never removes anything:

| Function | Why it does not belong to an editor |
| --- | --- |
| content / cleantrash | empties the trash of everybody for good |
| content / translations | adds and removes the languages of the whole installation |
| content / urltranslator | the URL aliases and wildcards of the whole installation |
| content / jobs | everybody's content jobs |
| setup, class, role, section, workflow, state / administrate | the administration |
| git_manager, update | updating the installation |

The base data's `content/*` holds the first four; the subtree limitation of the role's assignment does not limit them,
since they have no node. The site packages remove `content/*`, and since 24 October 2026 the setup step and the script
replace the one `plain_site` keeps (above), so these lines appear only for a `content/*` with limitations, for one kept
with `--keep-content-wildcard`, or for a role given these functions by hand.

## What to do when you upgrade

Run the script with `--dry-run` first. It lists every policy it would add and the reason a policy is left out (the
role has it already in any form, also limited or as `module/*`; another role of the same groups has it; or the module
is not installed), then the `WARN` lines:

```bash
php bin/php/addeditorpolicies.php --dry-run
php bin/php/addeditorpolicies.php
```

It adds only what is missing, without limitations, expires the role cache, checks what was stored and prints `PASS`
or `FAIL`. A second run adds nothing.

On a role with `content/*` without limitations (an installation of `plain_site`), the script first replaces it, and the
dry run says exactly what goes and what comes:

```
would remove content/*                    content/* also grants content/cleantrash, content/translations, content/urltranslator, content/jobs
would add    content/create               in place of content/*
would add    content/edit                 in place of content/*
...
would add    content/reverserelatedlist   in place of content/*
...
PASS: content/* would be replaced by 16 content functions; 0 policies would be added to Editor (3) (dry run, nothing changed)
```

A function the role holds as a single policy already (also a limited one) is kept and not added again; the stored
role is checked afterwards (no `content/*` left, every function there). Roles without `content/*` (every other site
package) are not touched.

- `--keep-content-wildcard` leaves `content/*` as it is.
- `--skip=<module/function>` leaves a policy out (e.g. `--skip=content/restore`), repeatable; a skipped `content/hide`,
  `content/restore` or `content/reverserelatedlist` is not added in place of `content/*` either.
- `--only=<module/function>` adds only that policy, repeatable; `content/*` is then kept.
- `--role=<name or ID>` completes another role than Editor (a site's own editor role).

The script does not add `geo_article` and `video` to `content/create`: class lists are the site's choice. Add them in
Roles and policies if your editors write those.

Code: `kernel/classes/expeditorpolicies.php`, command `kernel/private/classes/commands/addeditorpolicies.php`. Test
without a database: `tests/tests/kernel/classes/expEditorPoliciesTest.php`.
