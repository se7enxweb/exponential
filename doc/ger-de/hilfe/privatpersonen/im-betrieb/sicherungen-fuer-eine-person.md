---
title: Sicherungen für eine Person
audience: individual, customer, website-creator
category: personal-sites
order: 1641
summary: Eine Sicherungsroutine, die eine Person für eine Exponential-Website durchhalten kann - was zu kopieren ist (Datenbank und Dateien zusammen), Sicherungen aus der Administration mit git_manager, der Weg über die Kommandozeile für SQLite und andere Datenbanken, Kopien außerhalb des Servers, Verschlüsselung und der Wiederherstellungstest, der beweist, dass es funktioniert.
keywords: Sicherung, Backup, Wiederherstellung, Datenbank, SQLite, var/storage, Einstellungen, git_manager, Setup Backup, Verschlüsselung, außer Haus, Wiederherstellungstest, Wartungsmodus, Aufbewahrung, Privatperson, backup, restore, encryption, offsite, restore test, maintenance mode, retention
type: help
---
# Sicherungen für eine Person

Diese Seite ist für Privatpersonen, die ihre eigene Website betreiben und bei Sicherungen auf niemanden sonst bauen
können. Eine Sicherung besteht aus zwei Hälften, die zusammen angelegt werden, der **Datenbank** und den
**Dateien**, und sie zählt erst, wenn Sie sie irgendwo wiederhergestellt haben
([Sicherungen](../../kunden/gesund-halten/backups.md), [Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).

## Inhalt

1. [Was zu kopieren ist](#was-zu-kopieren-ist)
2. [Weg 1: aus der Administration](#weg-1-aus-der-administration)
3. [Weg 2: auf der Kommandozeile](#weg-2-auf-der-kommandozeile)
4. [Weg 3: die Sicherungen Ihres Hosters](#weg-3-die-sicherungen-ihres-hosters)
5. [Kopien vom Server wegbringen](#kopien-vom-server-wegbringen)
6. [Der Wiederherstellungstest](#der-wiederherstellungstest)
7. [Eine Routine, die Sie durchhalten können](#eine-routine-die-sie-durchhalten-können)

## Was zu kopieren ist

| Teil | Warum |
|---|---|
| Die Datenbank (bei SQLite die Datei unter `var/storage/sqlite3/`) | Alle Inhalte, Benutzer und Einstellungen, die dort gespeichert sind |
| `var/storage` und `var/<site>/storage` | Hochgeladene Bilder und Dateien |
| `settings/override`, `settings/siteaccess` | Ihre Einstellungen; sie enthalten Passwörter, halten Sie Sicherungen also privat |
| Ihre eigenen Erweiterungen und Designs | Wenn sie nicht in einem Git-Repository liegen |

Caches bauen sich von selbst neu auf und brauchen keine Sicherung.

## Weg 1: aus der Administration

Die Erweiterung `git_manager` (in der `composer.json` im Wurzelverzeichnis) hat unter **Setup > Backup**
(`/git_manager/backup`) einen Sicherungsmanager ([git_manager](../../../funktionen/6.0/erweiterungen/git_manager.md)):

| Typ | Enthält | Verwendung |
|---|---|---|
| Full Site Backup | SQL-Dump, `var/`-Archiv, Website-Dateien (`extension/`, `settings/`, `config.php`) | Neuaufbau auf einem neuen Server |
| DB + Files caption | SQL-Dump und `var/`-Archiv | Die regelmäßige Sicherung |

Wahlweise kreuzen Sie **Encrypt backup files** an (Sicherungsdateien verschlüsseln; braucht GnuPG auf dem Server).
Seine Anforderungen nennen `mysqldump` für Datenbanksicherungen; prüfen Sie bei SQLite oder PostgreSQL, dass der
Datenbankteil im Archiv ist, oder nutzen Sie für die Datenbank Weg 2.

## Weg 2: auf der Kommandozeile

Wenn Sie eine Shell haben, nennt die Seite für Website-Ersteller die genauen Befehle
([Eine Sicherung anlegen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md#ein-backup-erstellen)). Die Kurzfassung:

1. Schalten Sie wahlweise den Wartungsmodus ein, damit Datenbank und Dateien übereinstimmen.
2. Sichern Sie die Datenbank mit ihrem eigenen Werkzeug. Für SQLite:
   `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/db.sqlite'"` (verwenden Sie den Namen Ihrer
   Datenbankdatei). Kopieren Sie eine laufende SQLite-Datei nie mit einer einfachen Dateikopie
   ([SQLite für Hoster](../../hosting/grundlagen/sqlite-fuer-hoster.md)).
3. Archivieren Sie `var/storage`, `settings/override` und `settings/siteaccess`.
4. Wartungsmodus aus.

Legen Sie das in ein kleines Skript und einen Cronjob, damit es nicht davon abhängt, dass Sie daran denken.

## Weg 3: die Sicherungen Ihres Hosters

Viele Hoster sichern Konten. Finden Sie heraus ([Was Sie Ihren Hoster fragen sollten](../../kunden/gesund-halten/was-sie-ihren-hoster-fragen-sollten.md)):
wie oft, wie lange sie aufbewahrt werden, ob Sie eine herunterladen können und wie eine Wiederherstellung beantragt
wird. Die Sicherung eines Hosters ist eine gute zweite Kopie; machen Sie sie nicht zu Ihrer einzigen.

## Kopien vom Server wegbringen

Eine Sicherung auf demselben Server geht mit ihm unter. Kopieren Sie mindestens eine Generation woandershin: auf
Ihren Computer, eine externe Festplatte, einen Speicherdienst. Verschlüsseln Sie sie, wenn sie Ihre Kontrolle
verlässt; eine Sicherung einer Familien- oder Vereinsseite ist eine Kopie der Daten aller und der Passwörter in den
Einstellungen.

| Aufbewahren | Beispiel |
|---|---|
| Aktuelle | Eine pro Tag oder Woche, ein paar Generationen |
| Ältere | Eine pro Monat, für ein paar Monate |
| Vor jedem Update | Eine, aufbewahrt, bis sich das Update bewährt hat |

## Der Wiederherstellungstest

Stellen Sie mindestens einmal und nach jedem größeren Update eine Sicherung auf einem anderen Rechner wieder her (Ihr
Laptop genügt) und prüfen Sie die Startseite, eine tief liegende Seite, ein Bild, einen Download, eine Anmeldung und
ein Formular ([Der Wiederherstellungstest](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md#der-wiederherstellungstest),
[Wiederherstellungstest (Kunden)](../../kunden/gesund-halten/wiederherstellungstest.md)). Notieren Sie, wie lange es gedauert hat.

## Eine Routine, die Sie durchhalten können

- [ ] Automatische Sicherung (Weg 1 oder 2) in einem festen Abstand.
- [ ] Eine manuelle Sicherung vor jedem Update oder jeder großen Änderung.
- [ ] Monatlich: die neueste Sicherung vom Server wegkopieren; alte Generationen löschen.
- [ ] Zweimal im Jahr: Wiederherstellungstest.
- [ ] Ihre Vertretung (ein Familienmitglied, eine ehrenamtliche Person im Verein) weiß, wo die Sicherungen liegen.

## Verwandte Seiten

- [Allein betreiben](README.md)
- [Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)
- [Sicherungen, die sich wiederherstellen lassen](../../unternehmen/zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)
- [Monatliche Pflegecheckliste](../checklisten/monatliche-pflegecheckliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
