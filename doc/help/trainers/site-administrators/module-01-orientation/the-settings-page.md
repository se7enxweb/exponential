---
title: "Lesson 1.4: The settings page"
audience: trainer, admin
category: training
order: 14
summary: Learners read where an INI value comes from on the settings page, compare two siteaccesses, and change and remove one setting safely on their training installation. 40 minutes.
keywords: site administration, settings, ini settings, settings/view, override chain, siteaccess, compare, global override, masked secrets, velocity restart
type: help
---
# Lesson 1.4: The settings page

Exponential is configured with INI files, read in layers: the defaults, extensions, the siteaccess, and the global
override. The settings page (**Setup > Ini settings**) shows for each setting the value in effect, the file it comes
from and every file that sets it. It is the safest way for a new administrator to look at configuration, and it can
also change a setting.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 1: Getting oriented as a site administrator](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 1.3](system-information-and-health-checks.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- open an INI file for a siteaccess and read the summary figures;
- explain the load order and which file wins for a plain value and for an array;
- find a setting in every file and list only the settings changed from the default;
- compare two siteaccesses;
- change a setting for one siteaccess and remove the change again.

## Trainer's notes

### How to present it

Source: [The settings page](../../../../guides/settings-page.md).

1. **Layers** (4 minutes). Draw the load order: `settings/<file>.ini` (the default), the extensions' siteaccess
   directories, `settings/siteaccess/<siteaccess>`, the active extensions, `settings/override`. A later file wins:
   it replaces a plain value and adds to an array unless it empties the array first with `Name[]`.
2. **Open a file** (3 minutes). **Setup > Ini settings**, choose an **INI file** and a **Siteaccess**, press
   **Show settings**. The address becomes `settings/view/<siteaccess>/<file>`, so it can be bookmarked.
3. **One setting** (4 minutes). Show the value in effect, where it comes from (Default, Extension, Siteaccess, Global
   override), the badges (**changed**, **no default**, **secret, masked**, **Velocity restart**) and the chain under
   "Set in N files".
4. **Search and compare** (2 minutes). Search **This file** or **Every file**; tick
   **Only settings changed from the default**; pick a siteaccess in **Compare with siteaccess**.
5. **Change** (2 minutes). **Edit**, choose **Save it in** (one siteaccess, or every siteaccess as the global
   override), **Save**. Saving clears the INI cache. Settings marked **Velocity restart** need
   `exp:velocity restart` before Velocity uses them.

### Common questions

**Why are some values shown as masked?** Passwords, keys, tokens, salts and credentials are never shown; the page
says only whether they are set. The search does not look into them either, so it cannot be used to guess a secret.

**Should I save in an extension?** Prefer the siteaccess or the global override: an update of the extension replaces
its own settings file.

**A colleague changed a file with an editor and nothing happened.** The installation does not check file times; the
INI cache must be cleared. The page marks such settings **not in effect yet**. Clear the INI cache with
**Setup > Cache management** or `php bin/php/ezcache.php --clear-tag=ini`.

**Why can I not remove a setting?** Only settings in the installation's own files (`settings/override` or
`settings/siteaccess/<siteaccess>`) can be removed. Defaults and extension settings have no tick box.

### Pitfalls

- **Saving in the wrong scope.** A change saved as the global override affects every siteaccess, including the
  administration. Read **Where it is set** before **Save**.
- **Arrays.** The edit field for an array starts with an empty first line, which empties the array before your
  elements. Removing it adds to the elements earlier files gave instead. Show both on the projector.
- **Changing the admin's own settings.** A wrong value in the `admin` siteaccess can lock you out of the
  administration. Practise on the `site` siteaccess.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Setup > Ini settings**. Choose **INI file** `site.ini` and **Siteaccess** `site`, then press
   **Show settings**.
2. Read the summary figures. How many settings are changed from the default? How many files are read?
3. Open **The files read, in order** and write down the files in their order.
4. Find `SiteName` in the block `SiteSettings`. Note its value and where it comes from.
5. Search **Every file** for `AdminEmail`. Note the file and the value.
6. Tick **Only settings changed from the default** and look at what remains.
7. Choose `admin` in **Compare with siteaccess** and press **Show**. Note two settings that differ.
8. Back on `site.ini` for `site`, press **Edit** beside `SiteName`. Read **Where it is set**.
9. Type `Training site of <your name>`, choose **Save it in** the siteaccess `site` only, and press **Save**.
10. Read the notice: which file was written, which caches were cleared?
11. Open the public site and check the new name in the page title or header.
12. On the settings page, tick `SiteName` and press **Remove selected**. Check that the earlier value is back.

## Expected result

- Step 3 lists the default file first and `settings/override` last.
- Step 9 writes `settings/siteaccess/site/site.ini.append.php`; the notice names the INI caches it cleared.
- The public site shows the new name after step 9 and the earlier name after step 12.
- The trainer checks by asking learners where `SiteName` comes from before and after step 9.

## Quiz

1. Two files set the same plain value. Which one wins?
2. What does a line `Name[]` do in an INI file?
   A. Adds an empty element B. Empties the array C. Comments the setting out D. Marks it as secret
3. Which file does **Save it in** "Every siteaccess (global override)" write?
4. What does the badge **Velocity restart** tell you?
5. Why does the page never show the value of a password, not even to administrators?

<details>
<summary>Answers</summary>

1. The one read later in the load order.
2. **B.** What earlier files added is dropped ([The settings page](../../../../guides/settings-page.md#3-read-one-setting)).
3. `settings/override/<file>.append.php`.
4. Velocity reads that setting only when it starts: run `exp:velocity restart` after saving it.
5. So that the page, a screenshot or a shared screen cannot leak it; the page only says whether a secret is set.

</details>

## Further reading

- [The settings page](../../../../guides/settings-page.md): every figure, badge and button.
- [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md): the same work in a
  terminal (lesson 1.5).
- [Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md): settings for several sites in one
  installation.

## Related pages

- [Module 1: Getting oriented as a site administrator](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
