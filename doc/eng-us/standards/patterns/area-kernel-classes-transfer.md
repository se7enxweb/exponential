---
title: Patterns in kernel/classes/transfer
audience: developer
category: standards
order: 519
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/transfer with its design patterns.
keywords: kernel/classes/transfer, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/transfer

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/transfer` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (10)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expKernelTransferCancelledException` | class extends RuntimeException | [Exception hierarchy](exception.md) | `kernel/classes/transfer/expkerneltransfercancelledexception.php:17` | Thrown by a transfer task (expKernelTransferTask) that stopped because its run was cancelled (expKernelTransferContext::throwIfCancelled()); the run… |
| `expKernelTransferContext` | class | – | `kernel/classes/transfer/expkerneltransfercontext.php:18` | What a transfer task (expKernelTransferTask) gets from the engine that runs it: the progress it reports (to the engine's job and to the run's history… |
| `expKernelTransferHistory` | class | [Utility (static helper)](utility.md) | `kernel/classes/transfer/expkerneltransferhistory.php:38` | The run history of the import and export tools (doc/eng-us/features/6.0/transfer-runs.md): the writer every tool calls for its runs, and the reader o… |
| `expKernelTransferQueueHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/transfer/expkerneltransferqueuehandler.php:37` | The expqueue handler of transfer runs (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). |
| `expKernelTransferRegistry` | class | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/transfer/expkerneltransferregistry.php:30` | The transfer registry: every import and export tool of the installation, as each declares itself in kernelservices.ini (a [Service_transfer.] block t… |
| `expKernelTransferRun` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/transfer/expkerneltransferrun.php:26` | One run of an import or export tool: a row of exp_transfer_run (doc/eng-us/features/6.0/transfer-runs.md). |
| `expKernelTransferRunner` | class | [Utility (static helper)](utility.md) | `kernel/classes/transfer/expkerneltransferrunner.php:36` | One way for every import and export tool to run its work in the background (doc/eng-us/features/6.0/transfer-runs.md), on whichever engine the instal… |
| `expKernelTransferRunSchema` | class | [Utility (static helper)](utility.md) | `kernel/classes/transfer/expkerneltransferrunschema.php:21` | The table exp_transfer_run on any engine: whether it exists, and creating it from its definition in share/db_schema.dba through the engine's schema h… |
| `expKernelTransferTask` | interface | [Interface (contract)](interface.md) | `kernel/classes/transfer/expkerneltransfertask.php:38` | The work of one background run of an import or export tool, whatever engine runs it (expKernelTransferRunner: expqueue, the kernel's content jobs, th… |
| `expKernelTransferTool` | class | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/transfer/expkerneltransfertool.php:49` | One import or export tool of the transfer registry (expKernelTransferRegistry), as it declares itself in a [Service_transfer.] block of kernelservice… |
