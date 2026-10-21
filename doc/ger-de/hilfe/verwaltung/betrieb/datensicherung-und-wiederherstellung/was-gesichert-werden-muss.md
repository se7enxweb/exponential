---
title: Was zu sichern ist
audience: government, hosting, admin
category: public-sector
order: 20
summary: Für IT-Teams im öffentlichen Sektor, die die Datensicherung einer Exponential-Website festlegen. Die zwei Hälften jeder Datensicherung (Datenbank und Dateien), die Dateien mit Schlüsseln, die sich nicht neu erzeugen lassen, was weggelassen werden kann und wie Sie ein konsistentes Paar erstellen.
keywords: Datensicherung, was zu sichern ist, Datenbank, var/storage, settings/override, settings/siteaccess, Audit-Trail, Schlüssel, konsistente Datensicherung, Wartungsmodus, Schriftgut, Kontinuität, backup, what to back up, database, audit trail, keys, consistent backup, maintenance mode, records, continuity
type: help
---
# Was zu sichern ist

Diese Seite richtet sich an IT- und Schriftgutteams in öffentlichen Stellen, die die Datensicherung einer
Exponential-Website festlegen. Sie beantwortet, ohne welche Daten die Website nicht leben kann, welche kleinen
Dateien Schlüssel enthalten, die sich nicht neu erzeugen lassen, was weggelassen werden kann, weil die Website es neu
aufbaut, und wie Sie eine Datenbank und Dateien erstellen, die zueinander passen. Die Hosting-Seite mit denselben
Fakten ist [Datensicherung: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md); die Referenz
im Installationsbuch ist [10.9 Datensicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

## Inhalt

1. [Zwei Hälften](#zwei-hälften)
2. [Die Dateien](#die-dateien)
3. [Schlüssel, die sich nicht neu erzeugen lassen](#schlüssel-die-sich-nicht-neu-erzeugen-lassen)
4. [Was Sie weglassen](#was-sie-weglassen)
5. [Ein konsistentes Paar](#ein-konsistentes-paar)
6. [Warum das für eine öffentliche Stelle wichtig ist](#warum-das-für-eine-öffentliche-stelle-wichtig-ist)
7. [Checkliste](#checkliste)

## Zwei Hälften

Exponential hält seinen Zustand in der **Datenbank** (Inhalte, Versionen, Benutzer, Rollen, in Tabellen gespeicherte
Einstellungen) und in **Dateien** (Uploads, Einstellungen, der Audit-Trail). „Eine Datensicherung besteht aus der
Datenbank und den Dateien, zum selben Zeitpunkt erstellt“
([10.9](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)). Die Datenbankhälfte hängt von der Engine
ab: [Datensicherung je Datenbank-Engine](datensicherungen-je-datenbank-engine.md).

## Die Dateien

| Pfad | Warum |
|---|---|
| `var/storage/`, `var/<site>/storage/` | hochgeladene Bilder und Dateien und ihre Varianten |
| `var/storage/sqlite3/` | die Datenbank einer SQLite-Website; sichern Sie sie mit der eigenen Sicherung von SQLite, nie durch Kopieren einer Datei in Gebrauch |
| `settings/override/` | die Einstellungen, Zugangsdaten und erzeugten Schlüssel der Installation |
| `settings/siteaccess/` | die Einstellungen jedes Siteaccess |
| `var/<site>/log/audit/` | der Audit-Trail: Live-Dateien, Archive und ihre signierten Manifeste |
| Ihre eigenen Extensions und Designs | wenn sie nicht in der Versionsverwaltung liegen |
| `config.php`, `config.cluster.php`, `config.env.php` | sofern vorhanden |
| `composer.json`, `composer.lock` | um `vendor/` mit denselben Versionen neu aufzubauen |
| `var/log/` | optional; nützlich, um einen Vorfall später zu untersuchen |

Einzelheiten zu jedem Punkt: [Sicherung von Dateien und Einstellungen](sicherung-von-dateien-und-einstellungen.md).

## Schlüssel, die sich nicht neu erzeugen lassen

| Datei | Enthält | Bei Verlust |
|---|---|---|
| `settings/override/audit.ini.append.php` | Signatur- und Pseudonymschlüssel des Audit-Trails | vorhandene Audit-Einträge lassen sich nicht mehr als unversehrt prüfen |
| `settings/override/mailpreferences.ini.append.php` | das Website-Geheimnis der E-Mail-Einstellungen | jeder bereits versandte Abmeldelink funktioniert nicht mehr |
| Konfigurationsbaum von Velocity | private Schlüssel der Zertifikate | HTTPS muss neu eingerichtet werden |

Siehe [Schlüssel und Geheimnisse in Datensicherungen](schluessel-und-geheimnisse-in-datensicherungen.md).

## Was Sie weglassen

| Pfad | Warum |
|---|---|
| `var/cache/`, `var/<site>/cache/` | wird neu erzeugt |
| `var/autoload/` | wird von `php bin/php/ezpgenerateautoloads.php -e` neu aufgebaut |
| `vendor/` | wird aus `composer.lock` neu aufgebaut |
| `var/vc/*/log/`, `var/vc/*/run/` | Logs und PID-Dateien von Velocity |
| `var/tmp/`, `var/log/mail/` | temporäre Arbeit, Testmails |

Der Kernel und die ausgelieferten Einstellungen kommen mit dem Release. Sichern Sie sie nur, wenn jemand sie vor Ort
geändert hat; **Setup > Upgrade-Prüfung** listet Dateien auf, die vom Release abweichen
([Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)).

## Ein konsistentes Paar

Der Wartungsmodus sorgt dafür, dass Datenbank und Dateien zueinander passen, weil zwischen beiden niemand schreibt:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# 1. the database, with the tool of its engine
# 2. the files
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

Fügen Sie den Speicher je Website, das Audit-Verzeichnis und Ihre Extensions hinzu, wie es Ihr Aufbau verlangt. Unter
SQLite und MySQL mit InnoDB ist die Datenbanksicherung allein auch ohne Wartungsmodus konsistent; eine Website, die
nicht jede Nacht offline gehen kann, kann die Datenbank im laufenden Betrieb und die Dateien direkt danach sichern und
dabei ein kleines Zeitfenster in Kauf nehmen, in dem ein neuer Upload nur in einer Hälfte enthalten sein kann
([Datensicherung: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).

## Warum das für eine öffentliche Stelle wichtig ist

- **Pflichten zur Aufrechterhaltung des Betriebs.** Eine Wiederherstellung muss den Dienst zurückbringen, nicht nur
  den Text: Formulare, Konten, Rollen und Einstellungen gehören dazu.
- **Schriftgut und Nachweise.** Der Audit-Trail kann belegen, wer was veröffentlicht hat; der Verlust seiner
  Schlüssel macht ihn unprüfbar. Stimmen Sie mit Ihren Verantwortlichen für das Schriftgut ab, wie lange
  Datensicherungen und Audit-Archive aufbewahrt werden
  ([Archive des Audit-Trails und Datensicherung](audit-trail-archive-und-datensicherungen.md)).
- **Personenbezogene Daten.** Datensicherungen enthalten Konten und Formulareingaben; sie unterliegen denselben
  Schutzregeln wie die Live-Website ([Kopien außer Haus und verschlüsselte Kopien](externe-und-verschluesselte-kopien.md)).

## Checkliste

- [ ] Die Datenbank und die oben aufgeführten Dateien sind in jeder Datensicherung enthalten.
- [ ] Die Schlüsseldateien sind enthalten und geschützt.
- [ ] Caches, Autoloads und `vendor/` sind weggelassen.
- [ ] Datenbank und Dateien werden in einem Zeitfenster erstellt, oder die kleine Lücke ist schriftlich akzeptiert.
- [ ] Eine Wiederherstellung der Datensicherung wurde getestet ([Wiederherstellung testen](wiederherstellungstests.md)).

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md)
- [Speicherort der Daten und was wo gespeichert wird](../hosting/datenstandort-und-was-wo-gespeichert-wird.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
