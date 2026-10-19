---
title: Ini settings in the administration
audience: admin, website-creator, designer, developer
category: build-two-ways
order: 410
summary: The Setup > Ini settings pages - how they show the layers of an INI file, which file wins, how an array is edited, where a change is saved - the no-code tool behind the header and footer menus.
keywords: Ini settings, settings view, settings edit, INI layers, override, siteaccess settings, extension settings, array, reset line, Save it in, menu.ini
type: help
---
# Ini settings in the administration

**Setup > Ini settings** (section *System*) shows and changes the settings of any siteaccess without a terminal. It
is the administration's counterpart of `./console exp:ini`.

## Contents

1. [Pick a file](#pick-a-file)
2. [Read the layers](#read-the-layers)
3. [Edit a setting](#edit-a-setting)
4. [Arrays and the reset line](#arrays-and-the-reset-line)
5. [Where the change is saved](#where-the-change-is-saved)

## Pick a file

Choose the **INI file** and the **Siteaccess**, then **Show settings**. The address becomes
`/settings/view/<siteaccess>/<file>`, so the page can be bookmarked.

![The Settings page with the INI file and Siteaccess choices and the Show settings button](images/hm-11-settings-pick-file.webp)

The page says how settings work: *Every INI file is read in layers: settings/&lt;file&gt;.ini first, then the files of
the active extensions and of the siteaccess, then settings/override. A later file wins: it replaces a plain value, and
adds to an array unless it empties the array first.* Secrets (passwords, keys, tokens) are never shown.

## Read the layers

Each setting shows its value in effect and, under **Set in N files**, every file that sets it, in reading order. A
file whose values are dropped is marked **overridden**, the one in effect **elements in effect**.

![The MainMenuID setting: the extension sevenx_themes_media for site is overridden, the siteaccess site file is in effect](images/hm-03-settings-mainmenuid.webp)

Badges: **no default** (the kernel's `settings/<file>.ini` does not set it), **list of 6** (an array with six
elements). The page can also search a file, list the settings changed from the default and compare with another
siteaccess.

## Edit a setting

**Edit** on a row opens `/settings/edit/<siteaccess>/<file>/<Block>/<Setting>/<placement>`: the chain again, then
**New value** with **Type** (Array, True/False, Enabled/Disabled, String, Numeric) and **Value**.

## Arrays and the reset line

The page's own help: *One element per line: =value adds an element, [key]=value sets a key. Leave the first line empty
to empty the array before these elements; without it they are added to what the earlier files give.*

![The Value field of MainMenuID with an empty first line and one =id line per menu item](images/hm-04-settings-edit-add-192.webp)

## Where the change is saved

**Save it in** offers:

| Choice | File |
|---|---|
| Siteaccess site only | `settings/siteaccess/site/<file>.ini.append.php` |
| Every siteaccess (global override) | `settings/override/<file>.ini.append.php` |
| In an extension | `extension/<name>/settings/...` |

Saving writes only that file, clears the INI caches and returns to the file's settings with a *Saved: ...* message.
A value used in templates also needs **Template & content** cleared ([Caches after a change](caches-after-a-change.md)).

## Related pages

- [Header menu in the administration](header-menu-in-the-admin.md)
- [exp:ini for site builders](exp-ini-for-site-builders.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
