---
title: Settings in the administration
audience: developer, designer, app-creator, admin
category: extension-development
order: 60
summary: Setup > Ini settings shows every setting with its value in effect, the file it comes from and the whole override chain; compares siteaccesses; changes values safely with masked secrets. How a builder uses it.
keywords: settings page, ini settings, settings/view, settings/edit, override chain, compare siteaccesses, changed from default, masked secrets, debug bar settings tab
type: help
---
# Settings in the administration

Part 2, chapter 6. Not every builder works in a terminal. The administration has a settings page that shows the same
facts as `exp:ini where` and writes through the same engine. This chapter shows how a builder uses it; the complete
guide is [The settings page](../../../guides/settings-page.md).

## Open it

**Setup > Ini settings** (`settings/view`). Pick an INI file and a siteaccess and press **Show settings**. The address
becomes `settings/view/<siteaccess>/<file>`, for example `/settings/view/admin/site.ini` under the administration's
path, which you can bookmark.

## What it shows

For every setting:

- the value **in effect** for the chosen siteaccess,
- the **file it comes from** (its placement: default, extension, siteaccess, override),
- the **override chain**: every file that sets it, in the order they are read.

Above the list you can **search** one file or every file, show only settings **changed from the default**, and
**compare** two siteaccesses side by side. Comparing is the fastest way to answer "why does the German site behave
differently from the English one?"

Secrets are **never shown**: passwords, keys, tokens, salts, credentials and the password inside a database address
are masked; you only see whether they are set.

## Changing a value

The **edit** form (`settings/edit`) asks for the scope to write (global override, a siteaccess, an extension), shows
the change, and on save:

- writes the file through the `exp:ini` engine, keeping comments and order,
- keeps a backup copy,
- clears the INI cache, so both PHP-FPM and Velocity use the new value on their next request.

A few settings are read by Velocity only when it starts; for those the page says to run
`./console exp:velocity restart`.

## The debug bar's settings tab

When debug output is on, the **Settings** tab of the Exp Debug bar shows every debug setting with its value, its source
file, and a control to change it in a scope you choose. Every change goes through the same engine, is logged, and can
be undone from the bar. It needs the `setup/setup` policy. See [The Exp Debug bar](../../../bc/6.0/debug-bar.md).

## Who may use it

The settings pages need the `setup` module's policies. Give them only to people who are allowed to change how the
whole installation behaves; a settings change can switch off security features as easily as it turns on a design.
Roles are explained in [Sections, roles and access for builders](sections-roles-and-access-for-builders.md).

## Browser or terminal?

| Task | Better in |
|---|---|
| Looking up where a value comes from | either |
| Comparing two siteaccesses | the browser |
| Changing one value once | either |
| Changing many values, repeatably, or in a deployment | the terminal (`exp:ini`, scriptable with `--json`) |
| Moving settings into an extension | the terminal (`exp:ini move`, `move-all`) |

## Related pages

- Previous: [Changing settings with exp:ini](the-exp-ini-command.md)
- Next: [.ini.append.php files and keeping settings safe](append-php-files-and-safety.md)
- [The settings page](../../../guides/settings-page.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
