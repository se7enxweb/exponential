---
title: Muster in extension/git_manager
audience: developer
category: standards
order: 461
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/git_manager mit den Entwurfsmustern.
keywords: extension/git_manager, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/git_manager

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/git_manager` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (23)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `AGPLDumpSanitizer` | class | – | `extension/git_manager/classes/agpldumpsanitizer.php:17` | AGPLDumpSanitizer |
| `BackupManager` | class | [Fassade / Dienst](fassade.md) | `extension/git_manager/classes/backupmanager.php:9` | – |
| `Exponential\Command\Extension\GitManager\BackupCreate` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/git_manager/classes/runnable/commands/php_backup-create.php:46` | Der Code von extension/git_manager/bin/php/backup-create.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\GitManager\BackupDelete` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/git_manager/classes/runnable/commands/php_backup-delete.php:34` | Der Code von extension/git_manager/bin/php/backup-delete.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\GitManager\BackupInfo` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md), [Wertobjekt / Struktur](wertobjekt.md) | `extension/git_manager/classes/runnable/commands/php_backup-info.php:28` | Der Code von extension/git_manager/bin/php/backup-info.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\GitManager\BackupList` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/git_manager/classes/runnable/commands/php_backup-list.php:34` | Der Code von extension/git_manager/bin/php/backup-list.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\GitManager\Remote` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/git_manager/classes/runnable/commands/php_remote.php:38` | Der Code von extension/git_manager/bin/php/remote.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\GitManager\Submodule` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/git_manager/classes/runnable/commands/php_submodule.php:38` | Der Code von extension/git_manager/bin/php/submodule.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\GitManager\UpgradePolicyDumpToBackup` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/git_manager/classes/runnable/commands/php_upgrade-policy-dump-to-backup.php:25` | Der Code von extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\GitManager\GitManager\Backup` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/git_manager/classes/runnable/views/git_manager/backup.php:34` | Die Ansicht git_manager/backup: die Backups, wie aktuell sie sind, und die Formulare, die sie anlegen und entfernen. |
| `Exponential\View\Extension\GitManager\GitManager\CommitDetails` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/git_manager/classes/runnable/views/git_manager/commit_details.php:84` | Ausgabe von „git log -1 -p“, wie die Seite sie zeigt: die Kopffelder, die Nachricht und jede geänderte Datei mit typisierten Zeilen (add, del, hunk, meta, contex… |
| `Exponential\View\Extension\GitManager\GitManager\Dashboard` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/git_manager/classes/runnable/views/git_manager/dashboard.php:19` | Der Code von extension/git_manager/modules/git_manager/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\GitManager\GitManager\Download` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/git_manager/classes/runnable/views/git_manager/download.php:30` | Die Ansicht git_manager/download//: ein Archiv eines Backups. |
| `Exponential\View\Extension\GitManager\GitManager\Dump` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/git_manager/classes/runnable/views/git_manager/dump.php:23` | Der Code von extension/git_manager/modules/git_manager/dump.php, in eine Klasse verschoben (#207 Stufe 1). |
| `GitManager` | class | [Fassade / Dienst](fassade.md), [Singleton / instance()](singleton.md) | `extension/git_manager/classes/git_manager.php:9` | – |
| `gitManagerAuditBranch` | class implements expAuditTaxonomyBranch | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/git_manager/classes/gitmanagerauditbranch.php:13` | Der Zweig von git_manager in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[git_manager]). |
| `GitManagerBackupCatalogue` | class | – | `extension/git_manager/classes/gitmanagerbackupcatalogue.php:22` | Die Sicherungen (Beschriftungen) im Sicherungsordner, nur von der Festplatte gelesen. |
| `GitManagerBackupFreshness` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/git_manager/classes/gitmanagerbackupfreshness.php:32` | Wie aktuell die Backups sind: eine Antwort, ermittelt aus dem NEUESTEN Backup. |
| `GitManagerBackupMessages` | class | – | `extension/git_manager/classes/gitmanagerbackupmessages.php:20` | Die Texte des Sicherungsstatus, jeweils allein aus dem Zustand gewählt. |
| `GitManagerLog` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/git_manager/classes/gitmanagerlog.php:17` | Das Commit-Log von git_manager/dashboard, seitenweise: die Ansichtsparameter, die festlegen, welche Seite und welche Commits, die Blätterrechnung und die Arg… |
| `GitManagerUpstream` | class | – | `extension/git_manager/classes/gitmanagerupstream.php:21` | Der Upstream des Repositorys der Installation auf einen Blick: wo der ausgecheckte Branch gegenüber seinem Branch auf dem Remote (origin) steht, die Commits… |
| `git_managerInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/git_manager/ezinfo.php:9` | – |
| `GPGEncryption` | class | – | `extension/git_manager/classes/gpgencryption.php:9` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[git_manager]` | `gitManagerAuditBranch` | `extension/git_manager/settings/audit.ini.append.php:5` |
