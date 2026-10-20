---
title: "Lesson 7.1: The anatomy of an extension"
audience: trainer, developer
category: training
order: 71
summary: Learners map every folder of a real extension to the extension point it serves, compare a small, a medium and a settings-only extension, and review what their own mysite_hello holds after six modules. 40 minutes.
keywords: developers course, extension, folder structure, settings, classes, autoloads, modules, design, datatypes, eventtypes, cronjobs, bin/php, translations, share, settings-only extension, mysite_hello
type: help
---
# Lesson 7.1: The anatomy of an extension

Learners have been building `mysite_hello` piece by piece for six modules. This lesson steps back and looks at an
extension as a whole: which folders the kernel looks into, which setting makes it look, and how small and large
extensions differ. It is the map for the rest of module 7.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 7: Extensions](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Module 6](../module-06-modules-and-views/README.md) |

## Learning objectives

After this lesson, learners can:

- name the standard folders of an extension and what the kernel finds in each;
- name the setting that makes the kernel look into each folder;
- tell a settings-only, a design-only and a code extension apart;
- explain why an extension on disk does nothing until it is switched on.

## Trainer's notes

### How to present it

Source: [What an extension is](../../../extension-development/part-1-from-user-to-developer/what-an-extension-is.md).

1. **The folders** (6 minutes). Project the table and connect each row to a lesson of this course:

   | Folder or file | What it holds | How the kernel finds it | Lesson |
   |---|---|---|---|
   | `ezinfo.php`, `extension.xml` | Name, version, license | Always read for active extensions | 7.2 |
   | `settings/` | `.ini.append.php` files | Active extension | 3.2, 8.4 |
   | `classes/` | PHP classes | The autoload array | 7.3 |
   | `autoloads/` | Template operator registrations | `[TemplateSettings] ExtensionAutoloadPath[]` | 7.5 |
   | `modules/<name>/` | Modules and views | `module.ini [ModuleSettings] ExtensionRepositories[]` | 5.4, 6.2 |
   | `design/<design>/` | Templates, stylesheets, images | `design.ini [ExtensionSettings] DesignExtensions[]` | 3.1 |
   | `datatypes/<name>/` | Datatypes | `content.ini [DataTypeSettings] ExtensionDirectories[]` | 4.4 |
   | `eventtypes/event/<name>/` | Workflow event types | `workflow.ini` | 10.2 |
   | `cronjobs/` | Cronjob parts | `cronjob.ini` | 10.3 |
   | `bin/php/` | Command line scripts | The console finds them as `ext:<ext>:<name>` | 9.2 |
   | `translations/<locale>/` | Translations | Active extension | |
   | `share/`, `sql/` | Database schema and data, packages | Install scripts | |

2. **Small to large** (4 minutes). `str_replace` (one operator, two PHP files and one settings file), `eztags` (a
   datatype, modules, templates, fetch functions, tables), `explayouts` (a page builder). A settings-only extension
   is the safest way to keep a site's configuration under version control.
3. **The switch** (3 minutes). `ActiveExtensions[]` for the whole installation, `ActiveAccessExtensions[]` for one
   siteaccess. Lesson 7.3 goes deeper.
4. **What an extension is not** (2 minutes). Not a change in `kernel/`, not a Composer library (though Composer can
   install it, with the package type `ezpublish-legacy-extension`), not content.

### Common questions

**Can an extension replace a kernel class?** As a last resort, through the override autoload path; the RAD **Kernel
override** wizard writes it. Prefer settings, events and overrides.

**Is the folder name important?** It is the extension's name everywhere: settings, Composer's `extra` block, the
extension list.

**Do extensions slow a site down?** Each active extension adds folders the kernel searches for settings and
templates; inactive extensions on disk cost nothing.

### Pitfalls

- **Assuming an extension is safe.** It runs with the kernel's rights. Read [Evaluating an old extension](../../../extension-development/part-1-from-user-to-developer/evaluating-an-old-extension.md)
  before installing an unknown one.
- **Folders the kernel never reads.** A folder with a name of your own is ignored unless a setting points to it.

## Hands-on exercise

On the development installation.

1. List your extension: `find extension/mysite_hello -type f | sort`.
2. Next to each file, write the lesson that created it and the setting that makes the kernel use it.
3. Compare with a small extension: `find extension/str_replace -type f | sort` (if installed).
4. Compare with a larger one: `ls extension/eztags/` (if installed) and match its folders to the table.
5. Count extensions on disk and active ones:

   ```bash
   ls extension/ | wc -l
   php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions
   ```

6. Find the extensions that are active for one siteaccess only:
   `php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveAccessExtensions <sa>`.
7. In the extension's Git repository, check that every file is committed: `git -C extension/mysite_hello status`.

## Expected result

- Step 2: a table with every file of `mysite_hello` mapped to a lesson and a setting.
- Step 5: two different numbers; the difference is the installed but inactive extensions.
- Step 7: `nothing to commit, working tree clean`.
- The trainer checks two learners' tables from step 2.

## Quiz

1. Which setting makes the kernel look for modules in an extension?
2. Which folder holds template operator registrations?
   A. `classes/` B. `autoloads/` C. `modules/` D. `settings/`
3. What is the difference between `ActiveExtensions[]` and `ActiveAccessExtensions[]`?
4. Name one advantage of a settings-only extension.
5. True or false: an extension on disk is used as soon as it exists.

<details>
<summary>Answers</summary>

1. `module.ini [ModuleSettings] ExtensionRepositories[]` (with the module in `ModuleList[]`).
2. **B.** `autoloads/eztemplateautoload.php`.
3. The first activates an extension for the whole installation, the second for one siteaccess.
4. The site's configuration lives under version control and can be deployed like code.
5. **False.** It does nothing until it is switched on.

</details>

## Further reading

- [What an extension is](../../../extension-development/part-1-from-user-to-developer/what-an-extension-is.md).
- [The folder structure of a theme extension](../../../designers/extensions/extension-folder-structure.md).
- [Extensions: find, install, configure, build and release](../../../../guides/extensions.md).

## Related pages

- [Module 7: Extensions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
