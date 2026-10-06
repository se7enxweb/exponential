# The Extensions page: activate, deactivate and order extensions

This guide is for administrators. **Setup > Extensions** (`/setup/extensions`) lists every extension the installation
can see, shows which ones are active and in which order, and changes that list. After this page you can read a card,
move, activate and deactivate extensions safely, and know what happens when you apply. About 10 minutes.
For building, installing and releasing extensions, see the developer guide [Extensions](extensions.md).

## 1. One list, in the loading order

The page has a single list. The active extensions come first, in the order of `[ExtensionSettings] ActiveExtensions[]`
in `settings/override/site.ini.append.php`, numbered from 1; the inactive ones follow, by name. An extension higher in
the list has the higher priority: when two extensions ship the same setting or the same design template, the one
loaded earlier wins. That is why, for example, an installation's own settings extension is listed first.

Each card shows:

| On the card | What it means |
|---|---|
| The number | Its position in `ActiveExtensions`, or a dash when it is not active |
| Name, title, version, description | From the extension's `extension.xml`, else its `ezinfo.php` |
| License, Website | The same sources; the website only when it is an `http(s)` address |
| Active / Inactive / Only for siteaccesses | Whether it is in `ActiveExtensions`, or only in a siteaccess's `ActiveAccessExtensions` |
| Access extension: *siteaccess* | A siteaccess that switches it on in its own `site.ini.append.php` |
| git | The extension folder is a git checkout: update it with git, not with a package |
| Requires, Uses, Extends | Its declared dependencies in `extension.xml` `<dependencies>` |
| Needed by | Active extensions that require or use it |
| Loads as number *n* | Where the system really loads it (see section 4), when that differs from the written position |
| Problems | A required extension that is not active, a missing folder, an order the dependencies do not allow |
| Details | Designs of the extension that siteaccesses use, author, copyright, last change, downloads, and notes |

Above the list: counts (active, inactive, only for siteaccesses, git checkouts, with problems), a search (name,
description, license), the order (loading order, or all by name) and a filter: all, active, inactive, problems.

## 2. Change the list

Nothing is written while you work. Every button only changes the plan the page carries, and the bar **Not applied
yet** says how many extensions will be activated, deactivated and moved.

- **Move**: the up and down arrows on an active card. With JavaScript you can also drag a card by its grip under the
  number, or focus the grip and press the up and down arrow keys.
- **Activate**: an inactive card's button. The extension is placed after the extensions it requires or uses and before
  the ones it extends; with no declared dependencies it goes to the end. Move it afterwards if you want it elsewhere.
- **Deactivate**: an active card's button. An extension listed in `ActiveExtensions` whose folder is missing says
  **Remove from the list** instead.
- **Discard changes** goes back to the list as the file has it.

Everything works without JavaScript: each button reloads the page with the plan, and **Show** applies the search and
the filter. With JavaScript the arrows, the drag and drop, the search, the filter and the order work in place.

## 3. Review and apply

**Review changes** shows, before anything is written:

- the extensions activated, deactivated and moved;
- the risks, for example:
  - deactivating an extension that another active extension requires;
  - deactivating the only active extension that provides a design a siteaccess uses;
  - deactivating `ezformtoken` (the CSRF protection of every form) or `ezjscore` (the administration's scripts);
  - a move that lets another extension's full settings file win over an extension's own changes to it (for example an
    installation settings extension moved after `ezoracle`, whose `ezoracle.ini` then wins);
  - a reorder that changes nothing because the declared dependencies decide (section 4);
- the resulting `ActiveExtensions[]` lines, with the changed ones marked.

Risks marked red must be confirmed with the box under them before **Apply changes** is accepted. Apply then:

1. keeps a copy of `settings/override/site.ini.append.php` in `var/<site>/backups/settings-override/`;
2. rewrites only the `ActiveExtensions[]` lines. Every other line stays as it was, including comments: a comment
   directly above an extension's line moves with it, and one of an extension that was deactivated stays in the file.
   The file is read again afterwards and the copy is put back if anything else changed;
3. clears the INI, template override, design and active extension caches (and the user info cache when the modules
   changed), and regenerates the extensions' autoload arrays;
4. shows what changed. **If the site is also served by Velocity, restart it** (`./console exp:velocity restart`): its
   workers keep the extension list they started with. The page says whether Velocity is running.

If someone changed the active extensions in the meantime (another tab, another administrator, an edit by hand), the
page says so and starts again from the file instead of writing over that change.

## 4. Written order and loading order

With `[ExtensionSettings] ExtensionOrdering=enabled` (the default in `settings/site.ini`) the system does not load the
extensions in the written order: it sorts them by their declared dependencies (an extension loads after the extensions
it requires or uses and before the ones it extends), and the written order only counts where nothing is declared. On a
typical installation most extensions therefore load at another position than written; each card says where
(**Loads as number n**) and the note above the list says how many. Moving an extension whose place the dependencies
fix changes the file but not what is loaded; the review says so.

## 5. Common problems

| You see | Do this |
|---|---|
| *Requires X, which is not active* | Activate X, or deactivate the extension that needs it |
| *Listed in ActiveExtensions, but its directory is missing* | Install the extension again, or use **Remove from the list** |
| *Changes F, which X ships in full and loads earlier* (a note in Details) | Usually harmless for list settings; when a single value of this extension must win, move it above X |
| The page says the list changed elsewhere | Your plan was not written; make the change again on the list now shown |
| A change does not show on the Velocity port | `./console exp:velocity restart` |
| *Problems detected during autoload generation* | Two extensions declare the same class; remove or rename one |

## For developers

- View: `kernel/private/classes/views/setup/extensions.php` (thin); templates `design/admin{,4}/templates/setup/extensions.tpl`
  and `extensions_exp_style.tpl`.
- `expExtensionCatalogue` (`kernel/classes/expextensioncatalogue.php`) collects the facts; `expExtensionChangePlan`
  (`kernel/classes/expextensionchangeplan.php`) moves, activates, deactivates, diffs and finds problems and risks; both
  are pure where it matters and tested without a database in `tests/tests/kernel/classes/setup/ExtensionChangePlanTest.php`.
- `ezpActiveExtensions::replaceInText()` edits the file as text; `write()` keeps the copy, checks the result and audits
  the change (`system.extension.change`).
- The form posts `ExtensionPlanForm`, `ExtensionBase` (a fingerprint of the list it started from), `ExtensionPlan[]`,
  `ExtensionAction` (`up|down|top|bottom|activate|deactivate:<name>`), `ExtensionReviewButton`, `ExtensionApplyButton`,
  `ExtensionAcknowledgeRisks` and `ExtensionDiscardButton`. The posts of the page before the redesign still work:
  `ActivateExtensionsButton` with `ActiveExtensionList[]` (and `ShownExtensionList[]`) writes at once as before, and the
  JSON reorder `ReorderExtensions` with `ExtensionOrder[]` changes only the order.
- Related: [loading order and safe saving](../features/6.0/extension-loading-order.md),
  [the extension list and downloads](../features/6.0/extension-list-and-downloads.md).
