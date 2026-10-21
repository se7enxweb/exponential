---
title: "Backups: was gesichert werden muss"
audience: hosting, admin
category: hosting
order: 50
featured: true
summary: Die vollständige Liste dessen, was ein Exponential-Backup enthalten muss (die Datenbank, hochgeladene Dateien, Einstellungen, Schlüssel und das Prüfprotokoll), was weggelassen werden kann, weil es neu erzeugt wird, und wie Sie Datenbank und Dateien im selben Moment sichern.
keywords: Backup, Backups, was gesichert werden muss, Datenbank, Dateien, var/storage, settings/override, Schlüssel, Geheimnisse, Prüfprotokoll, Wartungsmodus, Konsistenz, git_manager, backup, what to back up, database, files, keys, secrets, audit trail, maintenance mode, consistency
type: help
---
# Backups: was gesichert werden muss

Diese Seite ist für Hosting-Anbieter, die einen Backup-Dienst für Exponential-Websites gestalten, und für die Administratoren,
die ihn betreiben. Sie beantwortet: ohne welche Daten eine Exponential-Website nicht leben kann, welche Dateien Schlüssel
enthalten, die sich nicht neu erzeugen lassen, was weggelassen werden kann, weil die Website es neu aufbaut, und wie Sie ein
Backup erstellen, dessen Datenbank und Dateien übereinstimmen.

![Backup in der Administration: Backups der Installation erstellen, herunterladen und entfernen](../../../../eng-us/help/images/hosting/git-manager-backup.webp)

Die Fakten stammen aus [10.9 Backups und Wiederherstellung](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung),
[Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung) und den unten verlinkten Seiten zu den Funktionen.

## Inhalt

1. [Ein Backup hat zwei Hälften](#ein-backup-hat-zwei-hälften)
2. [Die zu sichernden Dateien](#die-zu-sichernden-dateien)
3. [Schlüssel und Geheimnisse, die sich nicht neu erzeugen lassen](#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)
4. [Was Sie weglassen](#was-sie-weglassen)
5. [Datenbank und Dateien gemeinsam sichern](#datenbank-und-dateien-gemeinsam-sichern)
6. [Das Backup aufbewahren](#das-backup-aufbewahren)
7. [Backups aus der Administration](#backups-aus-der-administration)
8. [Checkliste](#checkliste)

## Ein Backup hat zwei Hälften

Exponential hält seinen Zustand an zwei Orten: in der **Datenbank** (Inhalte, Benutzer, Rollen, Versionen, in Tabellen
gespeicherte Einstellungen) und in den **Dateien** (hochgeladene Bilder und Dokumente, die Einstellungen der Installation, Ihre
eigenen Erweiterungen und Designs). Ein Backup besteht aus der Datenbank und den Dateien, im selben Moment erstellt
([10.9](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)). Ein Bild, das in der Datenbank existiert, aber
nicht auf der Festplatte, erscheint als defektes Bild; eine Datei ohne ihre Datenbankzeile ist Ballast.

Die Datenbankhälfte hängt von der Engine ab; die Befehle für jede der sechs unterstützten Datenbanken stehen auf
[Datenbank-Backups je Engine](datenbank-backups-je-engine.md).

## Die zu sichernden Dateien

| Pfad | Warum es zählt |
|---|---|
| `var/storage/` | hochgeladene Bilder und Dateien und ihre Varianten |
| `var/<site>/storage/` | dasselbe, wenn eine Website ein eigenes var-Verzeichnis hat |
| `var/storage/sqlite3/` | die Datenbankdatei einer SQLite-Website; sichern Sie sie mit dem Online-Backup von SQLite, nie durch Kopieren einer Datei in Benutzung |
| `settings/override/` | die Einstellungen der Installation, einschließlich der Zugangsdaten zur Datenbank und der unten genannten erzeugten Schlüssel |
| `settings/siteaccess/` | die Einstellungen jedes Siteaccess |
| Ihre eigenen Erweiterungen und Designs | wenn sie nicht in einem Repository liegen |
| `config.php`, `config.cluster.php`, `config.env.php` | Konfiguration auf PHP-Ebene, Cluster-Konstanten und der Name der Umgebung, falls vorhanden |
| `var/<site>/log/audit/` | das Prüfprotokoll: aktuelle Dateien, Archive und ihre signierten Manifeste |
| `var/log/` | optional: nützlich, wenn ein Vorfall später untersucht werden muss |

Wenn die Installation des Kunden mit Composer verwaltet wird, bewahren Sie auch `composer.json` und `composer.lock` des
Projekts auf: Sie bauen `vendor/` in denselben Versionen neu auf.

> **Warnung**
> `settings/override/` enthält das Datenbankpasswort und andere Zugangsdaten. Ein Backup, das es enthält, ist so
> schützenswert wie der Server selbst: Verschlüsseln Sie es, beschränken Sie, wer es lesen darf, und geben Sie es nie an einen
> Kunden weiter, ohne zu prüfen, was es enthält.

## Schlüssel und Geheimnisse, die sich nicht neu erzeugen lassen

Manche Dateien enthalten Schlüssel, die die Software bei der ersten Verwendung erzeugt hat. Wenn sie verloren gehen, geht
etwas kaputt, das keine Wiederherstellung von Inhalten reparieren kann:

| Datei | Enthält | Wenn sie verloren geht |
|---|---|---|
| `settings/override/audit.ini.append.php` | die Signatur- und Pseudonymschlüssel des Prüfprotokolls | die vorhandenen Aufzeichnungen lassen sich nicht mehr als unversehrt prüfen ([Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)) |
| `settings/override/mailpreferences.ini.append.php` | `[SecretSettings] TokenSecret`, das Website-Geheimnis der E-Mail-Einstellungen | ein neues Geheimnis macht jeden Link in jeder bereits versandten Mail ungültig, und kein Eintrag der Unterdrückungsliste passt mehr ([E-Mail-Einstellungen](../../../anleitungen/mail-einstellungen-administrator.md)) |
| Der Konfigurationsbaum von Velocity (`/etc/vc` oder `var/vc/qbix/etc`, wenn `/etc/vc` nicht genutzt wird) | die Konfiguration der Website und, in seinem Verzeichnis `ssl/`, private Schlüssel von Zertifikaten (Modus `0600`) | HTTPS auf Velocity muss neu eingerichtet werden ([8.3.10](../../../installation/08-die-website-ausliefern.md#8310-der-konfigurationsbaum-etcvc-im-debian-stil)) |

Diese Dateien sind klein. Nehmen Sie sie in jedes Backup auf, und halten Sie sie so vertraulich wie die Zugangsdaten der Website.

## Was Sie weglassen

Diese werden neu erzeugt und kosten in einem Backup nur Platz und Zeit:

| Pfad | Warum es wegfallen kann |
|---|---|
| `var/cache/`, `var/<site>/cache/` | Caches; werden neu erzeugt ([10.9](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)) |
| `var/autoload/` | Klassenzuordnungen; `php bin/php/ezpgenerateautoloads.php -e` baut sie neu auf |
| `vendor/` | Composer-Pakete; werden aus `composer.lock` neu aufgebaut |
| `var/vc/*/log/`, `var/vc/*/run/` | die Logs und PID-Dateien von Velocity |
| `var/log/mail/` | Mails, die der Datei-Transport auf einer Test-Website schreibt |
| `var/tmp/` | temporäre Arbeit |

Der Kernel, `lib/`, `bin/` und die mitgelieferten `settings/*.ini` kommen mit dem Release und lassen sich in derselben Version
erneut holen. Sichern Sie sie nur, wenn jemand sie an Ort und Stelle geändert hat; Setup > Upgrade-Prüfung listet Dateien auf,
die vom Release abweichen ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).

## Datenbank und Dateien gemeinsam sichern

Der Wartungsmodus macht das Paar konsistent: Kein Besucher und kein Redakteur schreibt zwischen dem Datenbank-Dump und der
Kopie der Dateien ([10.9, Vorgehen](../../../installation/10-nach-der-installation.md#vorgehen)).

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# 1. the database, with the tool of its engine (see Database backups per engine)
# 2. the files
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

Fügen Sie der `tar`-Zeile `var/<site>/storage`, das Audit-Verzeichnis und Ihre eigenen Erweiterungen hinzu, wie Ihr Aufbau es
verlangt.

> **Hinweis**
> Für die Datenbank allein ist der Wartungsmodus nicht nötig: Auf SQLite und MySQL mit InnoDB ist das Datenbank-Backup ohne
> Wartungsmodus konsistent; der Wartungsmodus lässt Datenbank und Dateien übereinstimmen. Ein Hoster, der Kunden-Websites
> nicht jede Nacht vom Netz nehmen kann, kann die Datenbank online sichern und die Dateien direkt danach, und ein kleines
> Fenster hinnehmen, in dem ein neuer Upload nur in einer Hälfte sein kann.

## Das Backup aufbewahren

Aus dem Installationsbuch: Bewahren Sie Backups getrennt vom Server auf, und behalten Sie mehrere Generationen. Wie viele und
wie lange, ist eine Produktentscheidung; [Backup-Zeitpläne und Aufbewahrung](backup-zeitplaene-und-aufbewahrung.md) stellt die
Optionen dar.

## Backups aus der Administration

Für Kunden ohne Shell fügt die Erweiterung `git_manager` **Setup > Backup** (`/git_manager/backup`) hinzu: Backups mit
Zeitstempel („Captions“) von Datenbank und Dateien, optionale Verschlüsselung mit GnuPG und bereinigte SQL-Dumps. Für den
Datenbankteil braucht sie `mysqldump`, sie deckt also Websites auf MySQL und MariaDB ab
([git_manager](../../../../eng-us/features/6.0/extensions/git_manager.md)). Ein Backup jeder Engine auf Seiten des Hosters bleibt die
eigene Aufgabe des Hosters.

## Checkliste

- [ ] Die Datenbank wird mit dem Werkzeug ihrer Engine gesichert, nicht durch Kopieren der Datendateien eines laufenden Servers.
- [ ] `var/storage` (und jedes `var/<site>/storage`) ist im Backup.
- [ ] `settings/override/` und `settings/siteaccess/` sind im Backup, im Ruhezustand verschlüsselt.
- [ ] Die Schlüsseldateien für Audit und E-Mail-Einstellungen sind in jedem Backup.
- [ ] Caches, `var/autoload`, `vendor/` und die Logs von Velocity sind ausgeschlossen.
- [ ] Datenbank und Dateien werden in einem Fenster gesichert, im Wartungsmodus, wo der Kunde es erlaubt.
- [ ] Backups verlassen den Server, und mehrere Generationen werden behalten.
- [ ] Eine Wiederherstellung wurde getestet ([Wiederherstellung und Wiederherstellungstests](wiederherstellung-und-wiederherstellungstests.md)).

## Verwandte Seiten

- [Datenbank-Backups je Engine](datenbank-backups-je-engine.md)
- [Backup-Zeitpläne und Aufbewahrung](backup-zeitplaene-und-aufbewahrung.md)
- [Wiederherstellung und Wiederherstellungstests](wiederherstellung-und-wiederherstellungstests.md)
- [Aus einem Backup wiederherstellen](../runbooks/aus-einem-backup-wiederherstellen.md) (Runbook)
- [Aufbau des Speichers](aufbau-des-speichers.md)
- [Sicherheitshärtung: Backups und Wiederherstellung](../../sicherheitshaertung.md#sicherungen-und-wiederherstellung)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
