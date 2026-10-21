---
title: Sicherung von Dateien und Einstellungen
audience: government, hosting, admin
category: public-sector
order: 40
summary: Für Betriebsteams in öffentlichen Stellen, die die Dateihälfte einer Exponential-Website sichern. Welche Verzeichnisse Uploads, Einstellungen und den Audit-Trail enthalten, wie Sie Speicher je Website und den Cluster-Mount einbeziehen, Eigentümer und Rechte bei der Wiederherstellung und was einem Unternehmens-Sicherungsagenten mitgeteilt werden muss.
keywords: Dateisicherung, var/storage, settings/override, settings/siteaccess, Audit-Verzeichnis, tar, Sicherungsagent, Ausschlüsse, Speicher je Website, DFS-Mount, Eigentümer, Berechtigungen, Wiederherstellung, file backup, audit directory, backup agent, exclusions, per-site storage, DFS mount, owners, permissions, restore
type: help
---
# Sicherung von Dateien und Einstellungen

Diese Seite richtet sich an Betriebsteams in öffentlichen Stellen, die die Dateien einer Exponential-Website sichern,
oft mit einem Unternehmens-Sicherungsagenten, der Verzeichnisse planmäßig kopiert. Sie beantwortet, welche
Verzeichnisse einzuschließen und welche auszuschließen sind, wie Multi-Site- und Cluster-Aufbauten die Liste ändern
und worauf Sie achten müssen, wenn Dateien zurückkommen. Der Aufbau wird unter
[Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md) erklärt.

## Inhalt

1. [Die einzuschließenden Verzeichnisse](#die-einzuschließenden-verzeichnisse)
2. [Ausschlüsse für Ihren Sicherungsagenten](#ausschlüsse-für-ihren-sicherungsagenten)
3. [Multi-Site- und Cluster-Aufbauten](#multi-site--und-cluster-aufbauten)
4. [Eine manuelle Dateisicherung](#eine-manuelle-dateisicherung)
5. [Eigentümer und Rechte bei der Wiederherstellung](#eigentümer-und-rechte-bei-der-wiederherstellung)
6. [Checkliste](#checkliste)

## Die einzuschließenden Verzeichnisse

| Pfad | Enthält |
|---|---|
| `var/storage/` | hochgeladene Bilder und Dateien und ihre Varianten (`original/`, `reference/`, `variations/`) |
| `var/<site>/storage/` | dasselbe je Website, wenn Siteaccesses `VarDir=var/<site>` setzen |
| `var/<site>/log/audit/` | den Audit-Trail (`audit.ini [AuditSettings] LogDir=log/audit` innerhalb des var-Verzeichnisses) und seine Archive, sofern `ArchiveDir` nicht woandershin zeigt |
| `settings/override/` | Einstellungen, Zugangsdaten, erzeugte Schlüssel |
| `settings/siteaccess/` | Siteaccess-Einstellungen |
| `extension/`, `design/` | wenn Ihr eigener Code nicht in der Versionsverwaltung liegt |
| `config.php`, `config.env.php`, `config.cluster.php` | sofern vorhanden |

Eine SQLite-Datenbank in `var/storage/sqlite3/` ist ein Sonderfall: Sichern Sie sie zuerst mit `.backup` von SQLite
([Datensicherung je Datenbank-Engine](datensicherungen-je-datenbank-engine.md)).

## Ausschlüsse für Ihren Sicherungsagenten

| Ausschließen | Warum |
|---|---|
| `var/cache/`, `var/<site>/cache/` | wird neu erzeugt |
| `var/autoload/` | wird neu erzeugt |
| `var/tmp/` | temporäre Arbeit |
| `var/vc/*/log/`, `var/vc/*/run/` | Logs und PID-Dateien von Velocity |
| `var/log/mail/` | Testmails des Datei-Transports |
| `vendor/` | wird aus `composer.lock` neu aufgebaut |
| `var/storage/sqlite3/*` (die Live-Dateien) | kopieren Sie stattdessen die Ausgabe von `.backup` |

Caches können aus vielen kleinen Dateien bestehen; wenn Sie sie weglassen, werden Sicherungen und Wiederherstellungen
kürzer ([Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md)).

## Multi-Site- und Cluster-Aufbauten

- **Mehrere Websites in einer Installation**: Jede kann ihr eigenes `VarDir` haben, und `LogVarDir` oder
  `CacheVarDir` können Logs und Caches in separate Verzeichnisbäume verlegen
  ([Logs, INI-Cache und Ablaufzeitstempel je Website](../../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)).
  Führen Sie den Speicher und das Audit-Verzeichnis jeder Website auf.
- **DFS-Cluster**: Die Referenzkopie der hochgeladenen Dateien liegt auf dem NFS-Export, ihre Metadaten in der
  Tabelle `ezdfsfile`. Sichern Sie den Export und die Metadatendatenbank gemeinsam, im selben Zeitfenster wie die
  Datenbank der Website ([Gemeinsamer Speicher und der DFS-Cluster](../verfuegbarkeit/gemeinsamer-speicher-und-der-dfs-cluster.md)).
- **Audit-Archive an anderer Stelle**: Wenn `[AuditArchiveSettings] ArchiveDir` auf einen anderen Mount zeigt,
  schließen Sie diesen ein.

## Eine manuelle Dateisicherung

```bash
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess var/site/log/audit
```

Passen Sie `var/site` an Ihr var-Verzeichnis an. Führen Sie die Sicherung im selben Zeitfenster wie die
Datenbanksicherung aus ([Was zu sichern ist](was-gesichert-werden-muss.md)).

## Eigentümer und Rechte bei der Wiederherstellung

Als root wiederhergestellte Dateien können später für den Webserver nicht beschreibbar sein, und die Website scheitert
dann an merkwürdigen Stellen. Übergeben Sie die Dateien nach der Wiederherstellung dem Benutzer der Website (oder der
gemeinsamen Gruppe) und leeren Sie dann die Caches
([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)):

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
```

Audit-Archivdateien werden mit dem Modus `0440` und Verzeichnisse mit `0750` geschrieben; behalten Sie diese Rechte
bei, sie schützen die Nachweise.

## Checkliste

- [ ] Der Speicher und das Audit-Verzeichnis jeder Website sind im Sicherungsumfang enthalten.
- [ ] Caches, Autoloads, temporäre Dateien und `vendor/` sind ausgeschlossen.
- [ ] SQLite wird über `.backup` gesichert, nicht durch Kopieren.
- [ ] Bei einem Cluster: Der NFS-Export und die Metadatendatenbank liegen im selben Zeitfenster.
- [ ] Das Wiederherstellungsverfahren korrigiert die Eigentümer und leert die Caches.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Beschreibbare Verzeichnisse und Berechtigungen](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
