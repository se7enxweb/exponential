---
title: Backups
audience: customer, businesses, hosting
category: customer-success
order: 6
summary: Was ein vollständiges Backup einer Exponential-Website ist, die Entscheidungen, die nur der Betreiber treffen kann (wie viel Verlust hinnehmbar ist, wie schnell eine Wiederherstellung sein muss, wo Kopien aufbewahrt werden), was Sie Ihren Hoster fragen sollten und wie Sie bei kleinen Fehlern eine Wiederherstellung vermeiden.
keywords: Backup, Sicherung, Wiederherstellung, Datenbank, Dateien, var/storage, Einstellungen, Wiederherstellungspunkt, Wiederherstellungszeit, Aufbewahrung, externe Aufbewahrung, Verschlüsselung, backup, restore, database, files, settings, recovery point, recovery time, retention, offsite, encryption
type: help
---
# Backups

Diese Seite ist für den Betreiber, der dafür verantwortlich ist, dass keine Inhalte, Bestellungen und
Mitgliederdaten verloren gehen. Das Backup selbst erstellt meist der Hoster oder ein Betreiber; die Befehle stehen unter
[Eine Website betreiben, sichern und wiederherstellen](../../../../en/us/guides/operating-a-site.md) und
[Backups und Wiederherstellungen](../../../../en/us/help/creators/website-creators/backups-and-restores.md). Ihre Aufgabe ist es zu
entscheiden, was gut genug ist, und sicherzustellen, dass es wirklich geschieht.

## Ein Backup hat zwei Hälften

„Exponential hält seinen Zustand an zwei Orten: der **Datenbank** und den **Dateien**“ ... „Ein Backup umfasst beides,
zum selben Zeitpunkt erstellt“ ([Eine Website betreiben](../../../../en/us/guides/operating-a-site.md)). Die Datenbank ohne die
hochgeladenen Bilder oder die Bilder ohne die Datenbank bringen eine Website nicht zurück.

| Was | Warum es für Sie wichtig ist |
|---|---|
| Die Datenbank | Alle Inhalte, Benutzer, Bestellungen und viele Einstellungen |
| `var/storage` und der Speicher jeder Website | Hochgeladene Bilder und Dateien |
| `settings/override` und `settings/siteaccess` | Die Einstellungen der Installation; sie enthalten Passwörter, Backups müssen daher vertraulich bleiben |
| Ihre eigenen Erweiterungen und Designs | Sofern sie nicht in einem Git-Repository liegen |
| `settings/override/audit.ini.append.php` | Die Signaturschlüssel des Prüfprotokolls |

Quelle: [Backups, die sich wiederherstellen lassen](../../../../en/us/help/businesses/reliability/backups-that-restore.md). Caches
bauen sich von selbst neu auf und brauchen kein Backup.

## Die Entscheidungen, die nur Sie treffen können

Schreiben Sie die Antworten auf und geben Sie sie Ihrem Hoster; er macht daraus einen Plan
([Backup-Pläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).

| Entscheidung | Frage |
|---|---|
| Wiederherstellungspunkt | Wie viel Arbeit (Bestellungen, Bearbeitungen, Anmeldungen) können wir uns leisten zu verlieren? Das bestimmt, wie oft die Datenbank gesichert wird. |
| Wiederherstellungszeit | Wie lange darf die Website während einer Wiederherstellung ausfallen? Nur ein [Wiederherstellungstest](wiederherstellungstest.md) sagt Ihnen die tatsächliche Zeit. |
| Generationen | Wie weit müssen wir zurückgehen können, für einen spät bemerkten Fehler? |
| Ort | Wo werden Kopien aufbewahrt, getrennt vom Server? |
| Verschlüsselung | Sind die Kopien verschlüsselt? Sie enthalten Passwörter und personenbezogene Daten. |
| Wer wiederherstellen darf | Nur der Hoster, oder auch unser eigenes Personal? |
| Aufbewahrung | Wie lange werden Kopien aufbewahrt, und wie gehen wir mit einer Anfrage um, die Daten einer Person zu löschen? |

> **Hinweis**
> Das Projekt veröffentlicht keine empfohlene Häufigkeit oder Aufbewahrungsdauer; die Leitfäden geben nur ein
> Beispiel, „täglich für eine Woche, wöchentlich für einen Monat“ ([Backups und Wiederherstellungen](../../../../en/us/help/creators/website-creators/backups-and-restores.md)).
> Wählen Sie anhand Ihrer eigenen Antworten.

## Was Sie Ihren Hoster fragen sollten

- [ ] Werden Datenbank und Dateien zusammen gesichert, nach welchem Plan?
- [ ] Wo werden Kopien aufbewahrt, und ist das getrennt vom Server und seinem Rechenzentrum?
- [ ] Sind die Kopien verschlüsselt, und wer kann sie lesen?
- [ ] Wie viele Generationen werden aufbewahrt?
- [ ] Alarmiert ein fehlgeschlagenes Backup eine Person?
- [ ] Wann wurde zuletzt ein Backup auf einem anderen Rechner wiederhergestellt, wie lange hat es gedauert, und was wurde geprüft?
- [ ] Wird direkt vor jedem Upgrade, jedem Composer-Update und jeder Datenbankänderung ein Backup erstellt?

Die letzte Frage ist wichtig: Der Hosting-Leitfaden verlangt ein Backup „vor einem Upgrade“, „vor einem
Composer-Update“, „vor einem Wechsel der Datenbank-Engine“ und „vor einer Wiederherstellung“
([Backup-Pläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).

## Kleine Fehler brauchen keine Wiederherstellung

Eine vollständige Wiederherstellung versetzt die ganze Website auf den Zeitpunkt des Backups zurück, und alles, was
seitdem geschah, geht verloren. Schulen Sie Ihre Redakteure darin, zuerst die Werkzeuge innerhalb von Exponential zu
nutzen ([Backups, die sich wiederherstellen lassen](../../../../en/us/help/businesses/reliability/backups-that-restore.md)):

| Fehler | Verwenden Sie stattdessen |
|---|---|
| Ein Element wurde entfernt | Den Papierkorb: wiederherstellen ([Veröffentlichen und Versionen](../../../../en/us/help/publishing-and-versions.md)) |
| Ein Text wurde überschrieben | Versionen: einen neuen Entwurf aus einer früheren machen (dieselbe Seite) |
| Unklar, wer was geändert hat | Das Prüfprotokoll ([Prüfprotokoll für den Betrieb](../../../../en/us/help/businesses/reliability/audit-trail-for-operations.md)) |

## Ihre Checkliste

- [ ] Die obigen Entscheidungen sind aufgeschrieben und mit dem Hoster vereinbart.
- [ ] Datenbank und Dateien werden zusammen gesichert.
- [ ] Kopien liegen außerhalb des Servers, vertraulich und in mehreren Generationen.
- [ ] Ein fehlgeschlagenes Backup erreicht eine Person.
- [ ] Im letzten Quartal wurde ein Wiederherstellungstest durchgeführt ([Wiederherstellungstest](wiederherstellungstest.md)).
- [ ] Die Redaktion kennt den Papierkorb und die Versionen.

## Verwandte Seiten

- [Wiederherstellungstest](wiederherstellungstest.md)
- [Backups, die sich wiederherstellen lassen](../../../../en/us/help/businesses/reliability/backups-that-restore.md)
- [Was gesichert werden muss (für Hoster)](../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
- [Datenaufbewahrung und Löschung](../../../../en/us/help/businesses/security/data-retention-and-deletion.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
