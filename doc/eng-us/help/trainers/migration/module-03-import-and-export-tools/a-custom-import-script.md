---
title: "Lesson 3.3: A custom import script"
audience: trainer, developer
category: training
order: 33
summary: Developers read the skeleton of a custom import script from the documentation, run it with the Exponential bootstrap on a training installation, and prove that remote ids make it repeatable; they learn the fromString() formats and the dependency order. 60 minutes.
keywords: migrating to exponential, import script, ezexec, eZContentFunctions, createAndPublishObject, updateAndPublishObject, fetchByRemoteID, fromString, eZSimplifiedXMLInputParser, storePath, remote id, dependency order
type: help
---
# Lesson 3.3: A custom import script

When no tool reads the source, a developer writes a PHP script that reads the source export and creates objects
through the kernel's API. The documentation has a skeleton that uses only kernel functions that exist in 6.0.15.
This lesson walks through it and has developers run a minimal version on their training installation.

> **Note**
> Run import scripts only on a training installation or a rehearsal copy. The skeleton is a starting point, not a
> finished importer: reading the source, the mapping and the error handling are yours.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 3: Import and export tools](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | PHP developers |
| Prerequisites | [Lesson 3.2](importing-with-xrowextract.md); PHP knowledge; shell access to a training installation and the node id of a course folder |

## Learning objectives

After this lesson, learners can:

- explain each of the three parts of the skeleton: create or find by remote id, rich text, old address;
- run a script with the Exponential bootstrap;
- show that a second run of the script updates instead of duplicating;
- give the `fromString()` format of five common datatypes;
- name the dependency order of an import and what follows it (search index, caches).

## Trainer's notes

### How to present it

Source: [Exponential's import and export tools, section 4](../../../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton).

1. **The loop** (6 minutes). Show the skeleton on the projector. For each item: a remote id made from the source id,
   `eZContentObject::fetchByRemoteID()`, then `eZContentFunctions::createAndPublishObject()` if it does not exist,
   with `false` for no notifications on imported content.
2. **Rich text** (4 minutes). HTML is reduced to the simplified input (`p`, `strong`, `em`, `a href`, headings, lists,
   tables) and parsed into XmlText with `eZSimplifiedXMLInputParser`, as the RSS import does.
3. **The old address** (3 minutes). `eZURLAliasML::storePath()` stores a redirecting alias for the node.
4. **`fromString()` formats** (4 minutes). Show the table: dates as Unix timestamps, `ezimage` as
   `path|alternative text`, `ezkeyword` comma separated, `ezobjectrelationlist` ids separated by `-`.
5. **Good practice** (3 minutes). Dependency order (media, content, relations in a second pass), a dedicated import
   user, rebuild the search index, clear the content cache, log to a file, make every run repeatable.

### Common questions

**How do I run the script?** The tools page uses `./console exp:ezexec path/to/your_import.php`; in an installation
without the `./console` wrapper, `php bin/php/ezexec.php path/to/your_import.php` runs the same bootstrap.

**Why `false` as the second argument?** No notifications for imported content; otherwise every subscriber would get a
mail per imported item.

**Can I set the original publication date?** The mapping page, section 12: the object's `published` and `modified`
can be set and stored by the import script; check the result in the admin.

### Pitfalls

- **No remote id.** Without it the second run duplicates everything. The exercise proves it the other way round.
- **Wrong language code.** The skeleton uses `eng-GB`; the training installation may use another one. Check
  **Setup > Languages** first.
- **Relations in the first pass.** The target may not exist yet; resolve by remote id in a second pass.

## Hands-on exercise

Developers, on their own training installation. The script creates three folders below your course folder.

1. In the admin, open **Setup > Languages** and note the locale code of the main language (for example `eng-US`).
2. Note the node id of your course folder; below it is `<folder>`.
3. Create `var/tmp/import/import_training.php` with the skeleton from
   [section 4 of the tools page](../../../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton).
4. Change `$parentNodeId` to `<folder>`, `$classIdentifier` to `folder` and `$language` to your locale code.
5. Replace the empty `$items` with three hard-coded items, each with a `source_id` (`1`, `2`, `3`), a `title` and an
   `html` of one paragraph; leave `old_path` empty.
6. A folder's name attribute is `name`, not `title`: change the attribute array to `array( 'name' => $item['title'] )`,
   and the rich text attribute from `body` to `short_description` (check both identifiers on the **Folder** class page
   under **Setup > Classes**).
7. Run it with the Exponential bootstrap:

   ```bash
   php bin/php/ezexec.php var/tmp/import/import_training.php
   ```

8. Open your course folder in the admin and count the new folders.
9. Run the script a second time, unchanged. Count again.
10. Change the title of item 2 in the script, run it a third time, and look at that folder's versions.

## Expected result

- After step 7 the script printed `ok src-1`, `ok src-2`, `ok src-3`, and three folders appear below the course
  folder.
- After step 9 there are still three folders: the script found each by its remote id and updated it.
- After step 10 folder 2 has the new name and a newer version.
- The trainer checks one installation in the admin and asks the learner to explain why step 9 made no duplicates.

## Quiz

1. Which kernel call finds an object that an earlier run already created?
2. In which format does `ezdatetime` read a value through `fromString()`?
   A. ISO 8601 text B. A Unix timestamp C. Day/month/year D. A PHP `DateTime` object
3. Why is the second argument of `createAndPublishObject()` set to `false` in the skeleton?
4. Give the import order the tools page recommends.
5. Which two things do you do after the import has finished?

<details>
<summary>Answers</summary>

1. `eZContentObject::fetchByRemoteID()`
   ([tools page, section 4](../../../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton)).
2. **B** (the `fromString()` table on the tools page, section 4).
3. No notifications for imported content (the comment in the skeleton).
4. Media first, then content that relates to it, then content that relates to content; relations resolved by remote
   id in a second pass (tools page, section 4, good practice).
5. Rebuild the search index and clear the content cache (tools page, section 4, good practice).

</details>

## Further reading

- [Exponential's import and export tools, section 4](../../../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton):
  the skeleton and the `fromString()` table.
- [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md): what the script maps to.
- [Chapter 17.6: PHP API](../../../../install/17-migration-reference.md#176-php-api-legacy-classes-and-public-api-services):
  the legacy classes next to their platform equivalents.

## Related pages

- [Module 3: Import and export tools](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
