---
title: Backups, die sich wiederherstellen lassen
audience: businesses, admin, hosting, project-manager
category: business-solutions
order: 70
summary: Was ein Unternehmen auf einer Exponential-Website sichern muss, wie oft, wo es die Sicherungen aufbewahrt und welche Entscheidungen eine Führungskraft treffen muss, mit Links zu den genauen Befehlen für jede Datenbank.
keywords: Backup, Backups, Sicherung, Wiederherstellung, Wiederherstellungspunkt, Wiederherstellungszeit, Aufbewahrung, Datenbank-Backup, Wartungsmodus, externe Aufbewahrung, backup, restore, recovery point, recovery time, retention, database backup, var/storage, settings/override, maintenance mode, offsite
type: help
---
# Backups, die sich wiederherstellen lassen

Diese Seite richtet sich an Inhaber und Führungskräfte, die dafür verantwortlich sind, dass Inhalte, Bestellungen und
Mitgliederdaten des Unternehmens nicht verloren gehen, und an die Person im Betrieb, die die Backups anlegt. Sie erklärt,
was ein vollständiges Backup ist, welche Entscheidungen eine Führungskraft treffen muss und woran Sie erkennen, dass die
Backups funktionieren. Die Befehle stehen unter
[Betrieb einer Site, sichern und wiederherstellen](../../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen) und
[Datenbankunterstützung, Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung).

## Inhalt

1. [Ein Backup hat zwei Hälften](#ein-backup-hat-zwei-hälften)
2. [Was Sie sichern](#was-sie-sichern)
3. [Die Entscheidungen einer Führungskraft](#die-entscheidungen-einer-führungskraft)
4. [Ein Backup anlegen](#ein-backup-anlegen)
5. [Backups vor jeder Änderung](#backups-vor-jeder-änderung)
6. [Backups sicher aufbewahren](#backups-sicher-aufbewahren)
7. [Kleine Fehler brauchen keine Wiederherstellung](#kleine-fehler-brauchen-keine-wiederherstellung)
8. [Checkliste](#checkliste)

## Ein Backup hat zwei Hälften

„Exponential hält seinen Zustand an zwei Orten: in der **Datenbank** und in den **Dateien**“ … „Ein Backup umfasst
beides, zur selben Zeit angelegt“ ([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)).
Eine Datenbank ohne die hochgeladenen Bilder oder Bilder ohne die Datenbank, die auf sie verweist, bringen keine Website
zurück.

Die zweite Regel ist genauso wichtig: „Testen Sie mindestens einmal eine Wiederherstellung auf einem anderen Server; ein
Backup, das Sie nie wiederhergestellt haben, ist eine Hoffnung“ (dieselbe Quelle). Darum geht es in
[Wiederherstellungsübungen](wiederherstellungsuebungen.md).

## Was Sie sichern

Aus [Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md#what-to-back-up):

| Was | Warum |
|---|---|
| Die Datenbank | Alle Inhalte, Benutzer, Bestellungen und viele Einstellungen |
| `var/storage` und das `var/<site>/storage` jeder Website | Hochgeladene Bilder und Dateien |
| `settings/override` und `settings/siteaccess` | Die Einstellungen der Installation; sie enthalten Passwörter, halten Sie die Backups also vertraulich |
| Ihre eigenen Erweiterungen und Designs | Falls sie nicht in einem Git-Repository liegen |
| `settings/override/audit.ini.append.php` | Enthält die Signaturschlüssel des Audit-Trails ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#try-it-in-two-minutes)) |

Caches werden automatisch neu aufgebaut und brauchen kein Backup.

## Die Entscheidungen einer Führungskraft

Das sind geschäftliche Entscheidungen, keine technischen. Schreiben Sie die Antworten auf; der Betrieb macht daraus
einen Zeitplan. Der Hosting-Bereich erklärt jede Option ausführlich:
[Backup-Zeitpläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md#die-entscheidungen-einer-backup-richtlinie).

| Entscheidung | Zu beantwortende Frage |
|---|---|
| Wiederherstellungspunkt | Wie viel Arbeit (Bestellungen, Bearbeitungen, Anmeldungen) können wir uns leisten zu verlieren? Das bestimmt, wie oft die Datenbank gesichert wird. |
| Wiederherstellungszeit | Wie lange darf die Website während der Wiederherstellung ausfallen? Nur eine [Wiederherstellungsübung](wiederherstellungsuebungen.md) sagt Ihnen, wie lange es wirklich dauert. |
| Generationen | Wie weit müssen wir zurückgehen können, wenn ein Fehler spät bemerkt wird? |
| Ort | Wo werden Kopien abseits des Servers aufbewahrt? Auf einem anderen Server, in einem anderen Rechenzentrum oder bei einem anderen Anbieter? |
| Verschlüsselung | Sind Backups im Ruhezustand und bei der Übertragung verschlüsselt? Sie enthalten Zugangsdaten und personenbezogene Daten. |
| Wer wiederherstellen darf | Nur der Hoster oder auch unsere eigenen Mitarbeiter? |
| Aufbewahrung und Löschung | Wie lange werden Backups aufbewahrt, und wie erfüllen wir Löschanfragen zu personenbezogenen Daten? |

> **Hinweis**
> Das Projekt veröffentlicht keine empfohlene Häufigkeit und keine Aufbewahrungsdauer. Die Anleitungen geben nur ein
> Beispiel: „etwa täglich für eine Woche, wöchentlich für einen Monat“
> ([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md#retention)). Wählen Sie
> Ihre eigenen Werte anhand Ihrer Antworten oben.

## Ein Backup anlegen

Der Betrieb folgt der Anleitung für Ihre Datenbank. In Grundzügen
([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)):

1. Schalten Sie optional den Wartungsmodus ein, damit Datenbank und Dateien zueinander passen:
   `php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user`.
2. Sichern Sie die Datenbank mit ihrem eigenen Werkzeug (der Befehl jeder Engine steht unter
   [Datenbankunterstützung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)).
3. Archivieren Sie die Dateien: `var/storage`, `settings/override`, `settings/siteaccess`.
4. Schalten Sie die Wartung aus: `php bin/php/maintenance.php off --allow-root-user`.

Unter SQLite (mit seinem Online-`.backup`) und unter MySQL mit InnoDB (mit `--single-transaction`) ist das
Datenbank-Backup allein auch ohne Wartungsmodus konsistent
([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md#taking-a-backup)).
PostgreSQL hat eine eigene zeitpunktgenaue Wiederherstellung ([Datenbankunterstützung](../../datenbankunterstuetzung.md#2-postgresql)).

> **Warnung**
> Schreiben Sie ein Datenbankpasswort nie auf die Kommandozeile, wo es in der Shell-Historie landet und von anderen
> Benutzern des Rechners gesehen werden kann. Verwenden Sie die Passwortabfrage oder die Optionsdatei des Werkzeugs
> ([Datenbankunterstützung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)).

## Backups vor jeder Änderung

Legen Sie neben dem Zeitplan direkt vor jeder riskanten Änderung ein Backup an
([Backup-Zeitpläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md#backups-vor-jeder-änderung)):

- vor einer **Aktualisierung** von Exponential: „Zuerst sichern, jedes Mal“;
- vor einem **Composer-Update** oder einer Paketinstallation aus der Administration;
- vor einem **Wechsel der Datenbank-Engine**;
- vor einer **Wiederherstellung**, damit sich eine falsche Wiederherstellung rückgängig machen lässt.

## Backups sicher aufbewahren

- **Abseits des Servers.** Ein Backup auf derselben Festplatte geht mit der Festplatte verloren.
- **Vertraulich.** Backups enthalten `settings/override` (Zugangsdaten) und personenbezogene Daten. Beschränken Sie, wer
  sie lesen kann.
- **Mehrere Generationen.** Eine einzige Kopie, die nach dem Schaden angelegt wurde, hilft nicht.
- **Überwacht.** Ein fehlgeschlagener Backup-Job muss jemanden alarmieren, genau wie eine fehlgeschlagene
  Zustandsprüfung ([Health-Checks und Monitoring](health-checks-und-monitoring.md)).
- **Datenschutz.** Für Backups gelten dieselben Regeln wie für die Live-Daten. Siehe
  [Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md); dies ist keine Rechtsberatung.

## Kleine Fehler brauchen keine Wiederherstellung

Eine vollständige Wiederherstellung setzt die ganze Website auf den Zeitpunkt des Backups zurück, und alles, was seitdem
veröffentlicht wurde, geht verloren
([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#bevor-sie-wiederherstellen)).
Nutzen Sie bei einem einzelnen Fehler zuerst die Werkzeuge in Exponential:

| Fehler | Nutzen Sie stattdessen |
|---|---|
| Ein Element wurde entfernt | Den Papierkorb: Stellen Sie es wieder her ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#entfernen-und-wiederherstellen)) |
| Ein Text wurde überschrieben | **Versionen verwalten**: Erstellen Sie einen neuen Entwurf aus einer früheren Version ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#zu-einer-früheren-version-zurückkehren)) |
| Unklar, wer es geändert hat | Den Audit-Trail ([Audit-Trail für den Betrieb](audit-trail-fuer-den-betrieb.md)) |

## Checkliste

- [ ] Datenbank und Dateien werden gemeinsam gesichert, nach einem Zeitplan, der zum gewählten Wiederherstellungspunkt
      passt.
- [ ] Kopien werden abseits des Servers, verschlüsselt und in mehreren Generationen aufbewahrt.
- [ ] Ein fehlgeschlagenes Backup alarmiert eine Person.
- [ ] Vor jeder Aktualisierung, jedem Composer-Update, jedem Engine-Wechsel und jeder Wiederherstellung wird ein Backup
      angelegt.
- [ ] Im letzten Quartal wurde ein Backup auf einem anderen Rechner wiederhergestellt
      ([Wiederherstellungsübungen](wiederherstellungsuebungen.md)).
- [ ] Redakteure kennen den Papierkorb und die Versionen, sodass kleine Fehler nie eine Wiederherstellung brauchen.

## Verwandte Seiten

- [Wiederherstellungsübungen](wiederherstellungsuebungen.md)
- [Backups und Wiederherstellungen (Website-Ersteller)](../../../../eng-us/help/creators/website-creators/backups-and-restores.md)
- [Backups: was gesichert werden muss (Hosting)](../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
