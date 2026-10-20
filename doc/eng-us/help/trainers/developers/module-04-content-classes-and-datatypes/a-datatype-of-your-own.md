---
title: "Lesson 4.4: A datatype of your own"
audience: trainer, developer
category: training
order: 44
summary: Learners read the anatomy of a datatype in the kernel, generate a working datatype extension with the RAD Datatype wizard on their training installation, switch it on and add it to a class. 60 minutes.
keywords: developers course, datatype, eZDataType, register, DATA_TYPE_STRING, content.ini, DataTypeSettings, ExtensionDirectories, AvailableDataTypes, Datatype wizard, RAD, setup/datatype, fromString, toString, sortKey
type: help
---
# Lesson 4.4: A datatype of your own

A datatype is a new kind of field for content classes: a rating, a color, a booking period. It is the deepest
extension point of the content model, with a PHP class, templates and two settings. In this lesson learners read
the anatomy of a kernel datatype, let the RAD **Datatype wizard** write a working one into an extension, and add it
to a class.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 4: Content classes and datatypes](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 4.2](datatypes-and-their-templates.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- name the parts of a datatype: the class, its registration, its templates and its storage columns;
- read `kernel/classes/datatypes/ezstring/ezstringtype.php` and find what each group of methods is for;
- generate a datatype extension with **Setup** > **RAD**, **Datatype wizard**, and read the files it wrote;
- switch the extension on, regenerate the autoloads and add the new datatype to a class.

## Trainer's notes

### How to present it

Sources: [RAD tools](../../../../features/6.0/rad-tools.md), `kernel/classes/datatypes/ezstring/ezstringtype.php`
and `settings/content.ini [DataTypeSettings]`.

1. **The class** (5 minutes). Open `ezstringtype.php`: `class eZStringType extends eZDataType`, the constant
   `DATA_TYPE_STRING = 'ezstring'`, the constructor calling `parent::__construct()` with the name shown in the class
   editor, and at the end of the file `eZDataType::register( eZStringType::DATA_TYPE_STRING, 'eZStringType' );`.
   Walk through the method groups: validating and fetching what an editor typed, storing, `objectAttributeContent()`
   (what `content` returns), `toString()`/`fromString()`, `sortKey()`, `isIndexable()`.
2. **Registration** (3 minutes). The kernel finds datatypes in `RepositoryDirectories[]` and, for extensions, in
   `extension/<ext>/datatypes/<string>/` when the extension is in `ExtensionDirectories[]`; the string must also be
   in `AvailableDataTypes[]`. Show `extension/eztags/settings/content.ini.append.php` as a real example.
3. **Storage** (3 minutes). An attribute row has `data_text`, `data_int`, `data_float`, `sort_key_int` and
   `sort_key_string`; most datatypes fit there. A datatype that needs more keeps a table of its own and cleans it
   up.
4. **The wizard** (6 minutes). **Setup** > **RAD**, then the **Datatype wizard** (`/setup/datatype`). Sections:
   **The datatype** (extension name, datatype identifier, class name, name in the class editor, group, summary,
   author, Composer vendor, version, license), **What it has to do** (capabilities such as **Editing**,
   **Default value**, **Sortable**, **Text in and out**), **Where the value lives**, and **What will be written**,
   a list of every file before anything is written.
5. **Switching it on** (3 minutes). Activate the extension, run `php bin/php/ezpgenerateautoloads.php -e`, clear the
   caches; the datatype appears in the class editor's attribute list.

### Common questions

**Do I always need a new datatype?** No. Often a class with existing datatypes, or a template override, is enough.
Write a datatype when the value needs its own editing, validation or storage.

**What are `toString()` and `fromString()` for?** One-line text in and out: what CSV import and export, packages and
`createAndPublishObject()` use (lesson 4.3).

**Why does the wizard ask for a license?** It writes `ezinfo.php`, `extension.xml`, `composer.json` and a `LICENSE`
file; an extension without a license says nothing about how it may be used (module 7).

### Pitfalls

- **Identifier with the `ez` prefix.** New datatypes and classes use your own prefix; `ez` belongs to the kernel.
- **Missing autoloads.** The class is not found until `ezpgenerateautoloads.php -e` has run.
- **Templates not found.** Without the design registration the datatype loads and draws nothing; the wizard writes
  it, a hand-written datatype must too.
- **Removing a datatype that is in use.** Objects with the attribute then break. Remove the attribute from classes
  first.

## Hands-on exercise

On the training installation, signed in to the administration as an administrator, and in a shell in the
installation root.

1. Read the head and the last lines of the kernel's text line datatype:
   `sed -n 30,50p kernel/classes/datatypes/ezstring/ezstringtype.php` and
   `tail -5 kernel/classes/datatypes/ezstring/ezstringtype.php`.
2. Read a real registration: `cat extension/eztags/settings/content.ini.append.php` (if eztags is installed).
3. Open **Setup** > **RAD** and choose **Datatype** in the catalogue; the page **Datatype wizard** opens.
4. Fill in **The datatype**: extension name `mysite_rating`, datatype identifier `mysiterating`, a class name and a
   name in the class editor of your choice; add yourself as author and choose a license.
5. Under **What it has to do**, keep **Editing** and add **Text in and out** and **Sortable**.
6. Press **Refresh preview** and read **What will be written**: the list of files.
7. Write the extension with **Create in extension/**.
8. In a shell, list the files: `find extension/mysite_rating -type f | sort`. Open the datatype class and its
   `settings/content.ini.append.php`.
9. Switch it on and refresh:

   ```bash
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveExtensions mysite_rating global
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

10. Edit your **Note** class from lesson 4.1, add an attribute of the new datatype and press **OK**.
11. Edit a note, fill in the new field and publish.
12. Put the new extension under Git: `git -C extension/mysite_rating init` and a first commit.

## Expected result

- Step 6 lists the datatype class, templates, the registration in `content.ini` and `design.ini`, `ezinfo.php`,
  `extension.xml`, `composer.json`, `README.md`, `.gitignore` and `LICENSE`.
- Step 8: the class file in a folder below `datatypes/`, and `[DataTypeSettings]` with `ExtensionDirectories[]`
  and `AvailableDataTypes[]`.
- Step 10: the new datatype is offered in the attribute list of the class editor.
- Step 11: the note shows the value on the site through the generated view template.
- The trainer checks one learner's note on the projector.

## Quiz

1. Which call at the end of a datatype file makes it known to the kernel?
2. Which two settings must name an extension's datatype?
   A. `ActiveExtensions[]` and `DesignExtensions[]` B. `ExtensionDirectories[]` and `AvailableDataTypes[]`
   C. `RepositoryDirectories[]` and `ExtensionRepositories[]` D. `ModuleList[]` and `ViewList[]`
3. Which methods turn a value into one line of text and back?
4. Which column does the database sort on for a text datatype?
5. What must happen after the extension is switched on before the class is found?

<details>
<summary>Answers</summary>

1. `eZDataType::register( <DATA_TYPE_STRING>, '<class name>' );`.
2. **B.** `[DataTypeSettings] ExtensionDirectories[]` and `AvailableDataTypes[]` in `content.ini`.
3. `toString()` and `fromString()`.
4. `sort_key_string`, written by `sortKey()`.
5. `php bin/php/ezpgenerateautoloads.php -e` and clearing the caches.

</details>

## Further reading

- [RAD tools](../../../../features/6.0/rad-tools.md): the wizards, the catalogue and the health check.
- [Extension points](../../../../bc/6.0/rad-extension-points.md): every place the kernel can be extended.
- [RAD tools security](../../../../bc/6.0/rad-security.md): what the wizards write and never write.

## Related pages

- [Module 4: Content classes and datatypes](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
