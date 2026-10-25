---
title: Patterns in extension/git_manager
audience: developer
category: standards
order: 465
summary: Every class, view, fetch function, operator and plugin point of extension/git_manager with its design patterns.
keywords: extension/git_manager, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/git_manager

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/git_manager` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (23)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `AGPLDumpSanitizer` | class | – | `extension/git_manager/classes/agpldumpsanitizer.php:17` | AGPLDumpSanitizer |
| `BackupManager` | class | [Facade / service](facade.md) | `extension/git_manager/classes/backupmanager.php:9` | – |
| `Exponential\Command\Extension\GitManager\BackupCreate` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/git_manager/classes/runnable/commands/php_backup-create.php:46` | The code of extension/git_manager/bin/php/backup-create.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\GitManager\BackupDelete` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/git_manager/classes/runnable/commands/php_backup-delete.php:34` | The code of extension/git_manager/bin/php/backup-delete.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\GitManager\BackupInfo` | class extends Command | [Command (console, scripts, cronjobs)](command.md), [Value object / struct](value-object.md) | `extension/git_manager/classes/runnable/commands/php_backup-info.php:28` | The code of extension/git_manager/bin/php/backup-info.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\GitManager\BackupList` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/git_manager/classes/runnable/commands/php_backup-list.php:34` | The code of extension/git_manager/bin/php/backup-list.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\GitManager\Remote` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/git_manager/classes/runnable/commands/php_remote.php:38` | The code of extension/git_manager/bin/php/remote.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\GitManager\Submodule` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/git_manager/classes/runnable/commands/php_submodule.php:38` | The code of extension/git_manager/bin/php/submodule.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\GitManager\UpgradePolicyDumpToBackup` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/git_manager/classes/runnable/commands/php_upgrade-policy-dump-to-backup.php:25` | The code of extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\GitManager\GitManager\Backup` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/git_manager/classes/runnable/views/git_manager/backup.php:34` | The view git_manager/backup: the backups, how fresh they are, and the forms that make and remove them. |
| `Exponential\View\Extension\GitManager\GitManager\CommitDetails` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/git_manager/classes/runnable/views/git_manager/commit_details.php:84` | "git log -1 -p" output as the page shows it: the header fields, the message, and each changed file with its lines typed (add, del, hunk, meta, contex… |
| `Exponential\View\Extension\GitManager\GitManager\Dashboard` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/git_manager/classes/runnable/views/git_manager/dashboard.php:19` | The code of extension/git_manager/modules/git_manager/dashboard.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\GitManager\GitManager\Download` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/git_manager/classes/runnable/views/git_manager/download.php:30` | The view git_manager/download//: one archive of a backup. |
| `Exponential\View\Extension\GitManager\GitManager\Dump` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/git_manager/classes/runnable/views/git_manager/dump.php:23` | The code of extension/git_manager/modules/git_manager/dump.php, moved into a class (#207 stage 1). |
| `GitManager` | class | [Facade / service](facade.md), [Singleton / instance()](singleton.md) | `extension/git_manager/classes/git_manager.php:9` | – |
| `gitManagerAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/git_manager/classes/gitmanagerauditbranch.php:13` | git_manager's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[git_manager]). |
| `GitManagerBackupCatalogue` | class | – | `extension/git_manager/classes/gitmanagerbackupcatalogue.php:22` | The backups (captions) in the backup folder, read from the disk only. |
| `GitManagerBackupFreshness` | class | [Factory and static factory methods](factory.md) | `extension/git_manager/classes/gitmanagerbackupfreshness.php:32` | How fresh the backups are: one answer, taken from the NEWEST backup. |
| `GitManagerBackupMessages` | class | – | `extension/git_manager/classes/gitmanagerbackupmessages.php:20` | The texts of the backup status, each chosen from the state alone. |
| `GitManagerLog` | class | [Utility (static helper)](utility.md) | `extension/git_manager/classes/gitmanagerlog.php:17` | The commit log of git_manager/dashboard, one page at a time: the view parameters that say which page and which commits, the paging maths, and the arg… |
| `GitManagerUpstream` | class | – | `extension/git_manager/classes/gitmanagerupstream.php:21` | The upstream of the installation's repository at a glance: where the checked out branch stands against its branch on the remote (origin), the commits… |
| `git_managerInfo` | class | [Value object / struct](value-object.md) | `extension/git_manager/ezinfo.php:9` | – |
| `GPGEncryption` | class | – | `extension/git_manager/classes/gpgencryption.php:9` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[git_manager]` | `gitManagerAuditBranch` | `extension/git_manager/settings/audit.ini.append.php:5` |
