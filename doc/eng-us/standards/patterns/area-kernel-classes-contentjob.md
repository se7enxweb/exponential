---
title: Patterns in kernel/classes/contentjob
audience: developer
category: standards
order: 494
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/contentjob with its design patterns.
keywords: kernel/classes/contentjob, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/contentjob

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/contentjob` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (20)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expContentJob` | class | [Factory and static factory methods](factory.md) | `kernel/classes/contentjob/expcontentjob.php:32` | A content job: a large subtree remove or copy that runs in batches in a background worker instead of in the web request, so it never fails from a req… |
| `expContentJobAddLocation` | class extends expContentJobBatchType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobaddlocation.php:22` | The content job 'addlocation': gives the objects of many selected nodes a new location under one target node, per object through the kernel's own ope… |
| `expContentJobAuditExport` | class implements expContentJobType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobauditexport.php:28` | The content job 'auditexport': the export of the audit console (audit/export) in the background, for a filter too large for a download in the browser… |
| `expContentJobBatchType` | abstract class implements expContentJobType | [Template Method / abstract base class](template-method.md) | `kernel/classes/contentjob/expcontentjobbatchtype.php:28` | The common ground of the content job types that work on a list of nodes: move, hide, reveal, section, state, addlocation and removelocation. |
| `expContentJobCopySubtree` | class implements expContentJobType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobcopysubtree.php:34` | The content job 'copy': copies a subtree under a new parent in batches, level by level from the top, with the semantics of content/copysubtree and bi… |
| `expContentJobException` | class extends Exception | [Exception hierarchy](exception.md), [Factory and static factory methods](factory.md) | `kernel/classes/contentjob/expcontentjobexception.php:13` | The error a content job refuses or fails with; its message is shown to the user. |
| `expContentJobHelpManuals` | class implements expContentJobType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobhelpmanuals.php:27` | The content job 'helpmanuals': builds the help's manuals from the Markdown help in the background (doc/eng-us/features/6.0/help-content.md, "Manuals… |
| `expContentJobHideSubtree` | class extends expContentJobBatchType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobhidesubtree.php:25` | The content job 'hide' (and, through expContentJobRevealSubtree, 'reveal'): hides or reveals a node and its subtree with the semantics of eZContentOb… |
| `expContentJobLock` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentjob/expcontentjoblock.php:30` | Subtree locks of the content jobs. |
| `expContentJobMoveSubtree` | class extends expContentJobBatchType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobmovesubtree.php:24` | The content job 'move': moves a subtree under a new parent. |
| `expContentJobPackageInstall` | class implements expContentJobType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobpackageinstall.php:26` | The content job 'packageinstall': installs a package of the package repository in the background, as the user who asked for it, with the choices made… |
| `expContentJobRemoveLocation` | class extends expContentJobBatchType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobremovelocation.php:23` | The content job 'removelocation': removes many selected locations (nodes), keeping their objects, through the kernel's own operation, as content/acti… |
| `expContentJobRemoveSubtree` | class implements expContentJobType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobremovesubtree.php:33` | The content job 'remove': removes one or more subtrees in batches, deepest nodes first (leaves before their parents), each batch through the kernel's… |
| `expContentJobRevealSubtree` | class extends expContentJobHideSubtree | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobrevealsubtree.php:16` | The content job 'reveal': reveals a hidden node and its subtree with the semantics of eZContentObjectTreeNode::unhideSubTree() (a node hidden on its… |
| `expContentJobSectionSubtree` | class extends expContentJobBatchType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobsectionsubtree.php:29` | The content job 'section': assigns a section to every object of a subtree, with the semantics of eZContentObjectTreeNode::assignSectionToSubTree() (e… |
| `expContentJobStateSubtree` | class extends expContentJobBatchType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobstatesubtree.php:22` | The content job 'state': sets an object state (of one state group) on every object of a subtree, per object through the kernel's own operation, as co… |
| `expContentJobStore` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentjob/expcontentjobstore.php:18` | The store of the content jobs: one JSON file per job in var//jobs/content/, written atomically (a temporary file in the same directory, flushed to di… |
| `expContentJobTransfer` | class implements expContentJobType | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/contentjob/expcontentjobtransfer.php:25` | The content job 'transfer': one background run of an import or export tool (Export, CIE ...) when the installation has no job queue (expKernelTransfe… |
| `expContentJobType` | interface | [Interface (contract)](interface.md) | `kernel/classes/contentjob/expcontentjobtype.php:22` | A kind of content job, registered in content.ini [ContentJobSettings] JobTypes[]=. |
| `expContentJobWorker` | class | – | `kernel/classes/contentjob/expcontentjobworker.php:28` | Runs a content job in batches: bin/php/expcontentjob.php run , started detached by expContentJob::spawn() (or by the cronjob part 'contentjobs', or i… |

## Event hooks (1)

| Event | Kind | Called in |
|---|---|---|
| `content/cache` | notify | `kernel/classes/contentjob/expcontentjobhidesubtree.php:112` |
