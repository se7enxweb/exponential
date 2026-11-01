---
title: "Migrationseinstellungen"
audience: developer, admin, software-engineer, hosting
category: migrations
order: 21
summary: Jede Einstellung, die Migrationen prägt, an einer Stelle - die fünf Blöcke von migration.ini mit ihren Voreinstellungen und einem Link auf jede erzeugte Referenzseite, die Einstellungen in site.ini, dashboard.ini, audit.ini und cronjob.ini, die Migrationen nutzen, und wie Sie sie je Installation und je Umgebung überschreiben.
keywords: Einstellungen, Konfiguration, Überschreiben, Umgebung, Sicherungspunkt, Migrations-Repository, migration.ini, settings, MigrationSettings, RunSettings, CaptionSettings, RepositorySettings, AdminSettings, DirectoryList, ExtensionDirectory, RunAsUser, StopOnFailure, Before, After, ReuseUnchanged, RestoreOnFailure, RestoreParts, CodeTreeList, BackupDirectory, Mode, Remote, Branch, Commit, Tag, TagPattern, Push, PhpCliPath, JobHistory, LogBytes, AdminEnvironment, Level, EXP_ENV, override, configuration
type: help
---
# Migrationseinstellungen

Die Einstellungen der Migrationen stehen in `settings/migration.ini`. Ändern Sie diese Datei nie selbst: Ein Update
ersetzt sie. Was Sie ändern, gehört in eine Überschreibung:

- `settings/override/migration.ini.append.php` für die Installation,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<erweiterung>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, solange sie aktiv ist).

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php auf einer Entwicklungsinstallation
[RepositorySettings]
Push=disabled
*/ ?>
```

## Inhalt

1. [migration.ini](#migrationini)
2. [Einstellungen in anderen Dateien](#einstellungen-in-anderen-dateien)
3. [Typische Einstellungen je Umgebung](#typische-einstellungen-je-umgebung)
4. [Verwandte Seiten](#verwandte-seiten)

## migration.ini

| Block und Einstellung | Voreinstellung | Was sie bewirkt |
|---|---|---|
| **[MigrationSettings]** ([Referenz](../../beispiele/ini-einstellungen/migration/MigrationSettings.md)) | | wo Migrationen gefunden werden |
| `Enabled` | `enabled` | `disabled` findet gar keine Migrationen |
| `DirectoryList[]` | `migrations` | die Migrationsordner der Site; in den ersten schreibt `exp:migration:generate` |
| `ExtensionDirectory` | `migrations` | der Ordner in jeder aktiven Erweiterung und jedem installierten Paket |
| **[RunSettings]** ([Referenz](../../beispiele/ini-einstellungen/migration/RunSettings.md)) | | wie sich ein Lauf verhält |
| `RunAsUser` | `admin` | der Login, unter dem ein Konsolenlauf läuft (ein Lauf in der Administration läuft unter dem angemeldeten Benutzer) |
| `StopOnFailure` | `enabled` | beim ersten Fehler anhalten; `disabled` macht mit den Migrationen weiter, die die gescheiterte nicht brauchen |
| `Throttle` | leer | leer ausgeliefert; der Runner von 6.0.15 liest sie nicht (pausieren Sie in einer langen Migration selbst, siehe [Große Datenmigrationen](rezepte/grosse-datenmigrationen.md)) |
| **[CaptionSettings]** ([Referenz](../../beispiele/ini-einstellungen/migration/CaptionSettings.md)) | | die Sicherungspunkte um einen Lauf |
| `Before` | `enabled` | ein Sicherungspunkt (Art `migration-pre`) vor jedem Lauf |
| `After` | `enabled` | ein Sicherungspunkt (Art `migration-post`) nach einem erfolgreichen Lauf |
| `ReuseUnchanged` | `enabled` | den neuesten Sicherungspunkt wiederverwenden, solange sich seitdem nichts geändert hat |
| `ReuseIgnoreTableList[]` | `exp_migration`, `exp_migration_run`, `expupgrade_step`, `ezsession`, `expaudit_*` | Tabellen, die die Prüfung für die Wiederverwendung auslässt |
| `RestoreOnFailure` | `enabled` | den Sicherungspunkt von vorher nach einem Fehler wiederherstellen |
| `RestoreParts[]` | `database`, `storage`, `settings` | die Teile eines Sicherungspunkts, die die Wiederherstellung zurücksetzt |
| `CodeTreeList[]` | `settings`, `extension`, `design` | Verzeichnisbäume, die vor `--apply` auf nicht committete Änderungen geprüft werden |
| `BackupDirectory` | leer (`var/backup/upgrade`) | wo Sicherungspunkte ohne git_manager liegen |
| **[RepositorySettings]** ([Referenz](../../beispiele/ini-einstellungen/migration/RepositorySettings.md)) | | die Aufzeichnung in Git |
| `Mode` | `site` | `site`: der eigene Checkout der Installation; `separate`: ein eigenes Repository unter `Path` |
| `Path` | `migrations` | der Ordner der Statusdateien und Laufprotokolle |
| `Remote` | `origin` | das Remote, auf das gepusht wird; leer: kein Push |
| `Branch` | leer | der Branch, auf dem committet wird; leer: der aktuelle |
| `Commit`, `Tag`, `Push` | `enabled` | die Aufzeichnung committen, den Commit taggen, beides pushen |
| `TagPattern` | `migrations/<environment>/<run-id>` | der Name des Tags; Platzhalter `<environment>`, `<run-id>`, `<date>` |
| **[AdminSettings]** ([Referenz](../../beispiele/ini-einstellungen/migration/AdminSettings.md)) | | in Setup > Migrationen gestartete Läufe |
| `PhpCliPath` | leer | das PHP-Kommandozeilenprogramm, das sie im Hintergrund ausführt |
| `JobHistory` | `50` | wie viele Läufe ihre Dateien in `var/<site>/migration/jobs/` behalten |
| `LogBytes` | `262144` | wie viel vom Ende eines Laufprotokolls eine Seite zeigt |

Jede Einstellung mit ihrem vollständigen Kommentar: [die Blöcke von migration.ini](../../beispiele/ini-einstellungen/migration/README.md).
Eine Erweiterung kann `ExtensionDirectory` woanders hin zeigen lassen:
[die Erweiterungspunkte von migration.ini](../../beispiele/erweiterungspunkte/migration.md).

## Einstellungen in anderen Dateien

| Datei und Block | Einstellung | Für Migrationen |
|---|---|---|
| `site.ini [AdminEnvironment]` ([Referenz](../../beispiele/ini-einstellungen/site/AdminEnvironment.md)) | `Level` | die Umgebung der Installation (`development`, `test`, `staging`, `production`; `auto` liest `EXP_ENV`), die die Kopfzeile `environments:` und der Name der Statusdatei nutzen |
| `site.ini [ReauthGroup_system]` ([Referenz](../../beispiele/ini-einstellungen/site/ReauthGroup_system.md)) | | „Identität bestätigen“ vor einem Lauf, einer Fortsetzung, einer Simulation und einer Rücknahme in der Administration |
| `dashboard.ini [DashboardBlock_migrations]` ([Referenz](../../beispiele/ini-einstellungen/dashboard/DashboardBlock_migrations.md)) | `PolicyList[]` | der Dashboard-Block, für Benutzer mit `migration/read` |
| `audit.ini [AuditEventSettings]` ([Referenz](../../beispiele/ini-einstellungen/audit/AuditEventSettings.md)) | `Enabled[]` | die Audit-Ereignisse `system.migration.run`, `.apply`, `.fail`, `.rollback`, `.caption`, `.push` |
| `cronjob.ini [CronjobPart-migrationpush]` ([Referenz](../../beispiele/ini-einstellungen/cronjob/CronjobPart-migrationpush.md)) | `Scripts[]` | der Teil, der Läufe mit gescheitertem Push erneut pusht ([der Cronjob-Teil](../../beispiele/cronjobs/migrationpush.md)) |
| `upgrade.ini [UpgradeSettings]` ([Referenz](../../beispiele/ini-einstellungen/upgrade/UpgradeSettings.md)) | `ExtensionStepList[]` | die Upgrade-Schritte einer Erweiterung, die unter den Migrationen als Quelle `kernel` gelistet werden |

## Typische Einstellungen je Umgebung

| | Entwicklung | Staging | Produktion |
|---|---|---|---|
| `[AdminEnvironment] Level` | `development` | `staging` | `production` |
| `[RepositorySettings] Push` | `disabled` (oder `--no-push`) | `enabled` | `enabled` |
| `[CaptionSettings] Before`, `After` | `enabled` (günstig auf SQLite) | `enabled` | `enabled` |
| `[CaptionSettings] RestoreOnFailure` | `enabled` | `enabled` | `enabled` |
| `[RunSettings] RunAsUser` | `admin` | ein eigener Administrator-Login | ein eigener Administrator-Login |

Eine Einstellung, die sich je Umgebung unterscheidet, gehört in die eigene Überschreibung der Umgebung (zum Beispiel die
Variantendateien von `EXP_ENV`, `settings/override/site.ini.prod.append.php`), nie in eine Migration: Migrationen sind
auf jeder Installation dieselben Dateien ([Migrationen je Umgebung](rezepte/migrationen-je-umgebung.md)).

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](in-der-administration.md), [Befehle](befehle.md),
  [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
