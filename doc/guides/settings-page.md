# The settings page: see where every INI value comes from, and change one safely

This guide is for administrators who look up or change an INI setting in the administration interface instead of a
text editor. It follows the page **Setup > Ini settings** (`settings/view`) and the form it leads to
(`settings/edit`). At the end you know which file decides a value, how to find any setting in any file, how to
compare two siteaccesses, and what happens on both web servers when you save.

For the same work from a shell, see `./console exp:ini` (`exp:ini where`, `exp:ini get`, `exp:ini set`).

## In short

- Every INI file is read in **layers**. A later file wins: it replaces a plain value, and adds to an array unless it
  empties the array first.
- The page shows, for every setting, the value **in effect**, the file it comes from, and **every file that sets
  it** in the order they are read (the override chain).
- **Secrets are never shown**: passwords, keys, tokens, salts, credentials and the password inside a DSN are masked.
  You see whether they are set or empty.
- **Search** a file or every file, show only **changed from the default**, or **compare** two siteaccesses.
- **Saving** clears the INI cache, so PHP-FPM and Velocity both use the change on their next request. A few settings
  are read by Velocity only when it starts: the page says when to run `./console exp:velocity restart`.

## 1. Open a file

1. Sign in to the administration interface and open **Setup > Ini settings**.
2. Pick an **INI file** (the most used ones are at the top) and a **Siteaccess**, then click **Show settings**.

The address becomes `settings/view/<siteaccess>/<file>`, for example
`/admin/settings/view/admin/site.ini`. You can bookmark it.

## 2. Read the summary

The figures at the top describe the file in this siteaccess:

| Figure | Meaning |
|---|---|
| Blocks, Settings, Arrays | What the file holds once every layer is read. |
| Changed from the default | Settings whose value in effect differs from `settings/<file>.ini`. Click it to list only those. |
| Without a default | Settings that `settings/<file>.ini` does not have at all (added by an extension or an override). |
| Secrets, masked | Settings whose value the page will not show. |
| Files read | How many files make up this file in this siteaccess. |
| Not in effect yet | Settings whose value on disk differs from what this server is running with (see step 6). |

**The files read, in order** opens the load order itself: `settings/<file>.ini`, the extension siteaccess
directories, `settings/siteaccess/<siteaccess>`, the active extensions, then `settings/override`. The number beside
each file is how many settings it sets. A file this server cannot read (wrong owner or permissions) is marked
**not readable**, with a warning above: its settings are missing on this server.

## 3. Read one setting

Each block is a card; the chips above the cards jump to a block. Each setting shows:

- its **name**, with `[]` for an array, and badges: **changed**, **no default**, **secret, masked**,
  **list of N** or **N keys**, **Velocity restart**, **not in effect yet**;
- its **value in effect**. `true`/`enabled` is green, `false`/`disabled` red, an empty value says *empty*;
- on the right, **where it comes from**: Default, Extension *name*, Siteaccess *name*, Extension *name* for
  *siteaccess*, or Global override. For an array, every file that adds elements is listed, and each element names
  its file when there is more than one;
- **Default:** the default value, when the value in effect differs from it;
- **Set in N files, M overridden**: open it to see the chain. Every file that sets the setting is listed in load
  order, with what it does and whether that still counts:

| Line in a file | What it does | Shown as |
|---|---|---|
| `Name=value` | Sets a plain value; earlier values no longer count. | *in effect* or *overridden* |
| `Name[]` | Empties the array: what earlier files added is dropped. | *empties the array* |
| `Name[]=value` | Adds an element at the end. | *adds an element* |
| `Name[key]=value` | Sets the element *key*, replacing an earlier one with the same key. | *sets the key* |

A file whose elements are still part of the array is marked *elements in effect*; one whose contribution was
dropped by a later `Name[]` is *overridden*. Long arrays show their first eight elements; **Show N more** opens the
rest.

## 4. Find a setting

The search box looks in block names, setting names and values. Choose **This file** or **Every file**:

- *This file* keeps the page and lists only the matching settings.
- *Every file* searches every INI file of the siteaccess (about a hundred) and lists the matches with their file, value
  and origin. A file name leads to that setting.

The values of secrets are never searched. Searching for a fragment of a password finds nothing, so the search cannot
be used to guess one.

Tick **Only settings changed from the default** to hide everything that still has its default value.

## 5. Compare two siteaccesses

Pick a siteaccess in **Compare with siteaccess** and click **Show**. The page lists only the settings of the file
whose value in effect differs between the two, with the value and origin on each side. A setting only one of them has
says *not set* on the other side. A secret only says that it differs.

## 6. Change a setting

1. Click **Edit** beside a setting, or **Add setting** on a block.
2. **Where it is set** repeats the chain, so you see which file wins before you change anything.
3. Choose the **Type** (string, numeric, true/false, enabled/disabled, array). Without JavaScript, click
   **Change type**.
4. Type the **Value**:
   - An **array** is one element per line: `=value` adds an element, `[key]=value` sets a key. The field starts with
     an empty first line: it empties the array in the file you save to before your elements, so saving what is shown
     replaces the array instead of adding the same elements again. Remove the empty line to add to what earlier files
     give.
   - A **secret** starts empty. Type a new value to replace it; leave it empty to keep the value it has.
5. Choose **Save it in**:
   - **Siteaccess *name* only** writes `settings/siteaccess/<siteaccess>/<file>.append.php`;
   - **Every siteaccess (global override)** writes `settings/override/<file>.append.php`;
   - **In an extension** writes the extension's own `settings/<file>.append.php`. An update of the extension replaces
     that file, so prefer the first two.
6. Click **Save**. You return to the setting on the settings page.

The notice at the top says what was written to which file, and which caches were cleared:
*Query cache (SQL results), Global INI cache, INI cache, Active extensions cache, SSL Zones cache*.

### When the change takes effect

`config.php` switches off eZINI's check of file times (`EZP_INI_FILEMTIME_CHECK`). Without that check a changed
file is only read again once the INI cache is cleared. Saving on this page clears it, so:

- **PHP-FPM** (the site on port 443) uses the new value on its next request;
- **Velocity** (port 8080) reads the INI cache again on every request, so its workers use it on their next request too;
- settings listed in `site.ini [SettingsViewSettings] RestartSettingList[]` (by default all of `velocity.ini`,
  `site.ini [DatabaseSettings]`, `[ExtensionSettings]`, `[FileSettings] VarDir` and `file.ini [ClusteringSettings]`)
  are read by Velocity only when it starts. They carry the badge **Velocity restart**, and after saving one the
  notice tells you to run `./console exp:velocity restart` when Velocity is running.

If a file was changed some other way (an editor, `git pull`) and the INI cache was not cleared, the page marks the
settings **not in effect yet** and says what this server still runs with. The check covers the siteaccess the page
runs in (`admin`), on the server that answered: open the page on port 8080 to check Velocity. Clear the INI cache
with **Setup > Cache management** (INI caches) or `php bin/php/ezcache.php --clear-tag=ini`.

## 7. Remove a setting

Tick settings and click **Remove selected**. Each one is taken out of the highest file of the installation's own
settings that sets it (`settings/override` or `settings/siteaccess/<siteaccess>`), and the next file down, or the
default, takes over. Settings that only `settings/<file>.ini` or an extension sets have no tick box. Removing clears
the INI cache like saving does.

## What is masked, and how to change it

A setting is a secret when its name matches `site.ini [SettingsViewSettings] MaskedNameList[]` and none of
`UnmaskedNameList[]`, or when it is one `exp:ini` and the audit log mask (names with Password, Secret, Token, Salt,
Credential, PrivateKey, ApiKey or ending in Key). That last rule cannot be switched off here.

- An entry without `*` is found anywhere in the name, ignoring case: `password` matches `TransportPassword`.
- A capitalised single word must be the whole name: `Key`, `DSN`.
- An entry with `*` is a pattern for the whole name and keeps case: `*Key` matches `LicenseKey`, not `Monkey`.

In any setting, the password of `scheme://user:password@host` and `user:password@host`, and the value of
`password=`, `pwd=`, `secret=`, `token=`, `apikey=` in a connection string are masked too.

`MaskRevealLastCharacters` (0 by default, at most 4) shows the last characters of a long secret, so two values can be
told apart. Add a name to `MaskedNameList[]` in `settings/override/site.ini.append.php`:

```ini
[SettingsViewSettings]
MaskedNameList[]=Pin
```

## Safety rules the page follows

- The file and the siteaccess must be ones the installation lists; the place to save must be the siteaccess, the
  global override or an active extension. A name with `../`, a path or anything else is refused, so nothing can be
  written outside the settings tree.
- A block or setting name must be one eZINI reads back as the same name. A value with a line break, a NUL byte or
  `*/` is refused, because it would add other settings or end the PHP comment that hides an `.ini.append.php` file.
- Settings in `site.ini [eZINISettings] ReadonlySettingList[]` cannot be changed or removed.
- Every form needs the session's form token, and the module needs the `settings` policy.

## Checking it yourself

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/expSettingsPageTest.php
```

Expected: `OK (13 tests, ...)`. The tests need no database; they cover the chain, the masking and the checks of
the request.

## Related pages

- [Operating a site](operating-a-site.md)
- [Upgrading](upgrading.md)
- [Safe redirects](../features/6.0/safe-redirects.md)
- [INI save file permissions](../bc/6.0/ini-save-file-permissions.md)
