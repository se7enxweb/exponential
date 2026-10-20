---
title: Patterns in kernel/classes/contentcheck
audience: developer
category: standards
order: 487
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/contentcheck with its design patterns.
keywords: kernel/classes/contentcheck, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/contentcheck

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/contentcheck` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (13)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expContentCheck` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheck.php:19` | What the two content checks share (doc/features/6.0/content-checks.md): their settings, where they answer, the policy check, the form tokens of the t… |
| `expContentCheckBrokenRelations` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckbrokenrelations.php:38` | Visible, published content that points at something a visitor cannot reach (doc/features/6.0/content-checks.md, "Broken relations"). |
| `expContentCheckFileFix` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckfilefix.php:25` | The resolutions of the missing files list (doc/features/6.0/content-checks.md, "Resolving"): each for one field version or for many, always planned f… |
| `expContentCheckFileHandler` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/contentcheck/expcontentcheckfilehandler.php:15` | Says which storage files a file-backed field stands for (doc/features/6.0/content-checks.md, "Missing files"). |
| `expContentCheckFileImage` | class implements expContentCheckFileHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/contentcheck/expcontentcheckfileimage.php:13` | The files of an image field (ezimage): the original and the aliases its stored XML names (doc/features/6.0/content-checks.md, "Missing files"). |
| `expContentCheckFiles` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckfiles.php:26` | Missing files and image aliases (doc/features/6.0/content-checks.md, "Missing files"): the file-backed fields of the content below a node (images, fi… |
| `expContentCheckFileTable` | class implements expContentCheckFileHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/contentcheck/expcontentcheckfiletable.php:16` | The file of a field whose datatype keeps it in a table with the columns of ezbinaryfile: contentobject_attribute_id, version, filename, original_file… |
| `expContentCheckQuarantine` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckquarantine.php:25` | The actions of the storage check (doc/features/6.0/content-checks.md, "Quarantine"). |
| `expContentCheckReferences` | class | [Factory and static factory methods](factory.md) | `kernel/classes/contentcheck/expcontentcheckreferences.php:37` | The reference index of the unreferenced list (doc/features/6.0/content-checks.md, "What counts as a reference"): every object id, node id, remote id… |
| `expContentCheckScope` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckscope.php:24` | The start location of the content checks (doc/features/6.0/content-checks.md, "Where a check starts"): any node, from the whole tree (node 1) to one… |
| `expContentCheckStorage` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckstorage.php:28` | The storage check (doc/features/6.0/content-checks.md, "Storage files"): the files below var//storage that no content uses (orphan files), and the fi… |
| `expContentCheckTrash` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentchecktrash.php:23` | Moves unreferenced objects to the trash (doc/features/6.0/content-checks.md, "Moving to the trash"): the list posts the ticked objects, a confirmatio… |
| `expContentCheckUnreferenced` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentcheck/expcontentcheckunreferenced.php:31` | The assets below a node that nothing uses (doc/features/6.0/content-checks.md, "Unreferenced objects"): published objects of an asset class ([Unrefer… |
