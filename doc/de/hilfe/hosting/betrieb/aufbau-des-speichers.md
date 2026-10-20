---
title: Aufbau des Speichers
audience: hosting, admin
category: hosting
order: 20
summary: Was eine Exponential-Installation auf der Festplatte hält, welche Verzeichnisse beschreibbar sein müssen, welche Kundendaten enthalten und welche weggeworfen werden können, und wie eine Installation mit mehreren Websites Logs und Caches verteilt.
keywords: Speicher, var, VarDir, CacheDir, LogDir, StorageDir, Dateisystem, Festplatte, Berechtigungen, beschreibbar, Cache, Logs, mehrere Websites, tmpfs, open_basedir, storage, file system, disk, permissions, writable, multi-site
type: help
---
# Aufbau des Speichers

Diese Seite ist für Systemadministratoren, die Festplatten dimensionieren, Backup-Jobs schreiben und Dateiberechtigungen für
gehostete Exponential-Websites setzen. Sie beantwortet: was im Installationsverzeichnis liegt, wohin hochgeladene Dateien, Caches
und Logs gehen, welche davon Kundendaten sind, die gesichert werden müssen, welche sich neu erzeugen lassen und welche Einstellungen
sie verlegen.

Jeder Pfad und jede Einstellung unten stammt aus `settings/site.ini [FileSettings]` und dem Installationsbuch; folgen Sie den Links,
um sie zu prüfen.

## Inhalt

1. [Das Installationsverzeichnis auf einen Blick](#das-installationsverzeichnis-auf-einen-blick)
2. [Das Verzeichnis var](#das-verzeichnis-var)
3. [Die Einstellungen, die Dateien platzieren](#die-einstellungen-die-dateien-platzieren)
4. [Was beschreibbar sein muss](#was-beschreibbar-sein-muss)
5. [Mehrere Websites in einer Installation](#mehrere-websites-in-einer-installation)
6. [Worauf Sie achten](#worauf-sie-achten)
7. [Checkliste](#checkliste)

## Das Installationsverzeichnis auf einen Blick

| Pfad | Was es ist | Für PHP beschreibbar | Sichern |
|---|---|---|---|
| `index.php`, `index_rest.php`, `index_treemenu.php` | die Front-Controller | nein | kommt mit dem Code |
| `kernel/`, `lib/`, `bin/`, `share/`, `update/` | der Kernel, Bibliotheken, Skripte, Übersetzungen, Datenbank-Updates | nein | kommt mit dem Code |
| `vendor/` | die Composer-Pakete | nein (Composer schreibt es) | von Composer aus `composer.lock` neu aufgebaut |
| `extension/` | Erweiterungen, aus Composer oder von Hand geschrieben | nur, solange Pakete aus der Administration installiert werden | ja, wenn nicht in einem Repository |
| `design/` | Designs | nur, solange Pakete aus der Administration installiert werden | ja, wenn nicht in einem Repository |
| `settings/` | mitgelieferte Einstellungen (`settings/*.ini`) | ja, wenn Administratoren Einstellungen im Browser bearbeiten | `settings/override/` und `settings/siteaccess/` immer |
| `var/` | alles, was die laufende Website schreibt | ja | teilweise, siehe unten |
| `config.php`, `config.env.php` | Konfiguration auf PHP-Ebene, der Name der Umgebung | nein | ja, falls vorhanden |

`config.env.php` nennt die Umgebung einer Maschine (`EXP_ENV`), und `.gitignore` hält sie aus der Versionsverwaltung heraus
([Einstellungen je Umgebung](../../../../en/us/features/6.0/environment-settings.md)).

## Das Verzeichnis var

`var/` ist der eine Baum, den die laufende Website schreibt. Sein Aufbau, mit den mitgelieferten Einstellungen:

| Pfad | Enthält | Kundendaten? |
|---|---|---|
| `var/storage/` und `var/<site>/storage/` | hochgeladene Bilder und Dateien und ihre Bildvarianten (`original/`, `reference/`, `variations/`) | **ja** |
| `var/storage/sqlite3/` | die Datenbankdatei einer SQLite-Website, mit ihren Dateien `-wal` und `-shm` | **ja** (mit dem Datenbankwerkzeug sichern, nie durch Kopieren) |
| `var/cache/`, `var/<site>/cache/` | kompilierte Templates, INI-Cache, View-Cache, Template-Blöcke, Übersetzungen, gepackte Skripte und Styles | nein: wird neu erzeugt |
| `var/<site>/cache/exphttpcache/` | der HTTP-Cache | nein: wird neu erzeugt |
| `var/log/`, `var/<site>/log/` | die Logs des Kernels | betrieblich, im Backup optional |
| `var/<site>/log/audit/` | das Prüfprotokoll (`audit.ini [AuditSettings] LogDir=log/audit`, innerhalb des var-Verzeichnisses) | **ja**: eine Aufzeichnung, die Sie vielleicht vorlegen müssen |
| `var/autoload/` | erzeugte Klassenzuordnungen | nein: `php bin/php/ezpgenerateautoloads.php -e` |
| `var/vc/` | Exponential Velocity: Logs, PID-Dateien, seine eigene Konfiguration, wenn `/etc/vc` nicht genutzt wird | nein, außer einer hier gehaltenen Konfiguration |
| `var/log/mail/` | Mails, die der Datei-Transport schreibt | nein |
| `var/<site>/cronjobs/` | Ausgabe und Verlauf von Jobs, die aus **Setup > Cronjobs** gestartet wurden | nein |
| `var/maintenance.json` | nur vorhanden, solange der Wartungsmodus an ist | nein |
| `var/tmp/` | temporäre Arbeit, zum Beispiel der vom Wartungsmodus zur Seite verschobene HTTP-Cache | nein |

`<site>` steht für das var-Verzeichnis der Website, wenn ihre Siteaccesses `VarDir=var/<site>` setzen; viele Installationen halten
alles in `var/` allein. Die Pfade der Cronjobs folgen dem var-Verzeichnis des Siteaccess der Administration
([Cronjobs, Abschnitt 14](../../../anleitungen/cronjobs.md#14-protokolle-und-die-dateien-die-die-seite-führt)); der Baum von Velocity ist in
[8.3.12 Logs](../../../installation/08-die-website-ausliefern.md#8312-logs) beschrieben.

Dateien unter `storage/` werden nach den ersten Zeichen ihres Namens auf Unterverzeichnisse verteilt (`[FileSettings] DirDepth=3`),
sodass kein einzelnes Verzeichnis zu groß wird.

> **Hinweis**
> Caches können jederzeit gelöscht werden; die Website baut sie bei den nächsten Anfragen neu auf, auf Kosten langsamerer erster
> Seiten. `php bin/php/ezcache.php --clear-all` ist der unterstützte Weg, weil er auch die Ablaufzeitstempel behandelt
> ([Caches](../../../anleitungen/caches.md)).

## Die Einstellungen, die Dateien platzieren

Alle in `site.ini [FileSettings]`; setzen Sie sie in `settings/override/site.ini.append.php` oder pro Siteaccess.

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `VarDir` | `var` | die Wurzel von allem Geschriebenen; nur relative Pfade werden unterstützt |
| `StorageDir` | `storage` | hochgeladene Dateien, unterhalb von `VarDir` |
| `CacheDir` | `cache` | Caches, unterhalb von `VarDir`; ein absoluter Pfad wird nicht unterstützt (seine öffentlichen Caches würden nicht ausgeliefert) |
| `LogDir` | `log` | Logs, unterhalb von `VarDir`; ein absoluter Pfad wird unterstützt |
| `TemporaryDir` | `/tmp/` | temporäre Dateien von Uploads und Konvertierungen |
| `DirDepth` | `3` | Verzeichnistiefe im Speicher |
| `StorageDirPermissions`, `StorageFilePermissions` | `0777`, `0666` | Modi neuer Verzeichnisse und Dateien im Speicher |
| `TemporaryPermissions` | `0777` | Modus temporärer Dateien |
| `LogFilePermissions` | `0666` | Modus neuer Logdateien |

Die mitgelieferten Modi sind für alle beschreibbar. `site.ini` selbst empfiehlt `0770` und `0660` mit dem richtigen Eigentümer und
der richtigen Gruppe, was die gehärtete Wahl ist ([8.7.2 Ein Benutzer oder eine Gruppe](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)).

> **Warnung**
> `TemporaryDir=/tmp/` liegt außerhalb der Installation. Unter einer Beschränkung mit `open_basedir` (auf Shared-Hosting-Panels
> verbreitet) erlauben Sie entweder `/tmp/`, oder Sie richten `TemporaryDir` auf ein Verzeichnis innerhalb der Installation, das
> PHP schreiben darf.

## Was beschreibbar sein muss

Der Setup-Assistent prüft die Liste in `settings/setup.ini [directory_permissions] CheckList`: `design`, `extension`, `settings`
mit `settings/override` und `settings/siteaccess` und `var` mit seinen Verzeichnissen für Cache, Log, Speicher und Autoload. Nach
der Installation können `design/` und `extension/` wieder schreibgeschützt werden, wenn nichts Pakete aus der Administration
installiert ([8.7.1](../../../installation/08-die-website-ausliefern.md#871-was-beschreibbar-sein-muss)).

Die häufigste Ursache seltsamer Fehler sind **zwei Systembenutzer, die dieselben Dateien schreiben**: der PHP-FPM-Pool, die Worker
von Velocity, Cronjobs und Skripte der Kommandozeile. Führen Sie alle als Benutzer der Website aus, oder geben Sie ihnen eine
gemeinsame Gruppe mit setgid-Verzeichnissen ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)). Die
Details für eine Hosting-Plattform stehen auf [Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md).

## Mehrere Websites in einer Installation

Eine Installation kann viele Websites ausliefern, jede mit eigenem `VarDir`. Sechs Einstellungen in `[FileSettings]` entscheiden,
wohin ihre Logs und Caches gehen ([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../en/us/features/6.0/multi-site-log-and-cache-paths.md)):

| Einstellung | Mitgeliefert | Wirkung |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | `disabled` schreibt die Debug-Logs jeder Website in ihr eigenes Log-Verzeichnis |
| `LogVarDir` | leer | ein getrennter Baum für alle Logs: `var/example` schreibt nach `var_log/example/log` |
| `CacheVarDir` | leer | ein getrennter Baum für alle Caches: `var_cache/example/cache`, zum Beispiel ein Dateisystem im Arbeitsspeicher |
| `ExpiryDir` | leer | hält `expiry.php` aus einem Cache-Verzeichnis heraus, das bei einem Neustart geleert wird |
| `INICacheDir` | `global` | `site` speichert die INI-Dateien jeder Website in ihrem eigenen Cache-Verzeichnis zwischen |
| `AllowedDeletionDirs[]` | leer | zusätzliche absolute Verzeichnisse, in denen das Leeren eines Caches löschen darf |

Die Seite der Funktion zeigt ein vollständiges Beispiel mit einem `tmpfs`-Cache für alle Websites. Ein Cache im Arbeitsspeicher ist
schnell und geht bei jedem Neustart verloren; halten Sie `ExpiryDir` auf der Festplatte, damit Bildvarianten, die vor einem Leeren
erzeugt wurden, nicht wieder als aktuell gelten.

## Worauf Sie achten

- **Wachstum auf der Festplatte**: Der Speicher wächst mit den Uploads des Kunden; Caches wachsen mit Verkehr und Templates; Logs
  werden rotiert (siehe [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md)). `./console exp:systeminfo --sizes` und
  **Setup > Systeminformationen > Größen messen** melden die Größe von `var/` und jedes Cache-Verzeichnisses
  ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
- **Inodes**: Der View-Cache und die Bildvarianten sind viele kleine Dateien; beobachten Sie auf dem Dateisystem, das `var/`
  enthält, neben den Bytes auch die Nutzung der Inodes.
- **Eigentümer**: Prüfen Sie nach jedem als `root` ausgeführten Befehl den Eigentümer neuer Dateien in `var/`.

## Checkliste

- [ ] `var/` liegt auf einem Dateisystem mit Platz für Caches, Logs und ein lokales Backup.
- [ ] Der PHP-FPM-Pool, die Worker von Velocity und die Cronjobs laufen als ein Benutzer oder teilen eine Gruppe mit setgid-Verzeichnissen.
- [ ] `StorageDirPermissions` und `StorageFilePermissions` sind auf `0770` und `0660` gesetzt, wo der Eigentümer es erlaubt.
- [ ] `TemporaryDir` ist unter dem `open_basedir` des Kontos erreichbar.
- [ ] Backups enthalten `var/storage`, den Speicher pro Website, das Audit-Verzeichnis und die Überschreibungen der Einstellungen.
- [ ] Caches, `var/autoload` und die Logs in `var/vc` sind von Backups ausgeschlossen, um Platz zu sparen.

## Verwandte Seiten

- [Was gesichert werden muss](backups-was-gesichert-werden-muss.md)
- [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md)
- [Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
- [Speicher, CPU und Festplatte dimensionieren](../grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [8.7 Dateiberechtigungen und Eigentum](../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
