---
title: Backup-Zeitpläne und Aufbewahrung
audience: hosting
category: hosting
order: 60
summary: Die Entscheidungen, die ein Hoster trifft, wenn er aus Exponential-Backups einen Dienst macht (wie oft, wie viele Generationen, wo sie aufbewahrt werden, was vor einer Änderung gesichert wird), und die Aufbewahrungseinstellungen in Exponential, mit denen eine Backup-Richtlinie übereinstimmen muss.
keywords: Backup-Zeitplan, Aufbewahrung, Generationen, externer Standort, vor dem Upgrade, Wiederherstellung zu einem Zeitpunkt, Aufbewahrung des Audits, Aufbewahrung der Einwilligungen, Aufbewahrung der Logs, Hosting-Tarif, Backup-Richtlinie, backup schedule, retention, generations, off-site, before upgrade, point-in-time recovery, hosting plan, backup policy
type: help
---
# Backup-Zeitpläne und Aufbewahrung

Diese Seite ist für Produktmanager, die einen Backup-Dienst für Exponential-Hosting festlegen, und für die
Systemadministratoren, die ihn umsetzen. Sie beantwortet: welche Entscheidungen eine Backup-Richtlinie treffen muss, was die
Quellen von Exponential empfehlen, welche Aufbewahrungseinstellungen es in Exponential selbst gibt und wie Sie das Ergebnis
Kunden beschreiben, ohne zu versprechen, was die Plattform nicht halten kann.

Was in ein Backup gehört, steht auf [Was gesichert werden muss](backups-was-gesichert-werden-muss.md); die Befehle stehen auf
[Datenbank-Backups je Engine](datenbank-backups-je-engine.md). Diese Seite nennt keine Preise und keine eigenen festen
Zahlen: Jede Zahl unten ist aus dem Repository zitiert, und der Rest sind Optionen, die Sie entscheiden.

## Inhalt

1. [Was die Quellen empfehlen](#was-die-quellen-empfehlen)
2. [Die Entscheidungen einer Backup-Richtlinie](#die-entscheidungen-einer-backup-richtlinie)
3. [Backups vor jeder Änderung](#backups-vor-jeder-änderung)
4. [Aufbewahrung in Exponential](#aufbewahrung-in-exponential)
5. [Backups und gelöschte personenbezogene Daten](#backups-und-gelöschte-personenbezogene-daten)
6. [Den Dienst Kunden beschreiben](#den-dienst-kunden-beschreiben)
7. [Checkliste](#checkliste)

## Was die Quellen empfehlen

Das Installationsbuch und die Anleitungen nennen eine kleine Zahl von Regeln, und sie sind die Untergrenze jeder Richtlinie:

| Regel | Quelle |
|---|---|
| Ein Backup besteht aus der Datenbank und den Dateien, im selben Moment erstellt | [10.9](../../../../en/us/install/10-after-installing.md#109-backups-and-restore) |
| Bewahren Sie Backups getrennt vom Server auf, und behalten Sie mehrere Generationen | [10.9](../../../../en/us/install/10-after-installing.md#109-backups-and-restore) |
| Sichern Sie vor jedem Upgrade: zuerst sichern, jedes Mal | [11.1](../../../../en/us/install/11-upgrading.md#111-before-you-start) |
| Testen Sie eine Wiederherstellung auf einer anderen Maschine mindestens einmal und nach jedem größeren Upgrade | [10.9, Wiederherstellung](../../../../en/us/install/10-after-installing.md#restore) |
| Wöchentlich: Ein Backup der letzten sieben Tage existiert und wurde mindestens einmal auf einer anderen Maschine wiederhergestellt | [Eine Website betreiben, wöchentliche Checkliste](../../../../en/us/guides/operating-a-site.md#10-a-weekly-checklist) |

## Die Entscheidungen einer Backup-Richtlinie

Jede Zeile ist eine Wahl, die Sie für einen Tarif oder einen Kunden treffen; die rechte Spalte sagt, was zu bedenken ist.

| Entscheidung | Optionen | Was zu bedenken ist |
|---|---|---|
| Häufigkeit des Datenbank-Backups | nächtlich, mehrmals täglich, fortlaufend | wie viel der Kunde bearbeitet; das Online-Backup von SQLite (`.backup`) und Dumps von InnoDB mit `--single-transaction` laufen, ohne die Website vom Netz zu nehmen |
| Häufigkeit des Datei-Backups | mit jedem Datenbank-Backup oder seltener mit inkrementellen Kopien | Uploads ändern sich seltener als Inhalte, aber eine Wiederherstellung braucht beide Hälften aus demselben Moment |
| Konsistenz | Wartungsfenster, oder Datenbank online und Dateien direkt danach | ein Fenster lässt Datenbank und Dateien übereinstimmen; es kostet den Kunden eine kurze Seite 503 |
| Generationen | wie viele tägliche, wöchentliche und monatliche Kopien | je mehr Generationen, desto weiter kann ein Kunde nach einem spät bemerkten Fehler zurückgehen |
| Ort | ein anderer Server, ein anderes Rechenzentrum, ein anderer Anbieter | „getrennt vom Server“ ist das Minimum |
| Verschlüsselung | im Ruhezustand, bei der Übertragung | das Backup enthält `settings/override/`, das Zugangsdaten enthält |
| Wiederherstellung zu einem Zeitpunkt | keine, oder die eigene logbasierte Wiederherstellung der Datenbank | die Quellen nennen sie für PostgreSQL (eigene Wiederherstellung zu einem Zeitpunkt, [Datenbankunterstützung](../../../../en/us/help/database-support.md#2-postgresql)); bei anderen Engines prüfen Sie deren Dokumentation |
| Wer wiederherstellen darf | nur der Hoster, oder der Kunde aus einem Panel | eine Wiederherstellung durch den Kunden darf nie die Daten eines anderen Kunden überschreiben |
| Wiederherstellungstests | wie oft, auf welcher Maschine | ein Backup, das nie wiederhergestellt wurde, ist eine Hoffnung, kein Backup |

> **Tipp**
> Schreiben Sie die Richtlinie pro Tarif in eine Tabelle wie diese, mit Ihren Werten in einer dritten Spalte, und nehmen Sie
> sie in die Beschreibung des Tarifs auf. Support-Mitarbeitende beantworten die Frage „Wie weit können Sie zurückgehen?“
> dann aus derselben Tabelle, die der Kunde gesehen hat.

## Backups vor jeder Änderung

Neben dem Zeitplan erstellen Sie direkt vor jeder riskanten Änderung ein Backup. Die Quellen verlangen eines vor:

- **einem Upgrade** von Exponential: Der Datenbankschritt lässt sich nicht durch Zurücklegen von Dateien rückgängig machen
  ([11.1](../../../../en/us/install/11-upgrading.md#111-before-you-start));
- **einem Composer-Update oder einer Paketinstallation** aus der Administration: ezupdate bittet Sie zu bestätigen, dass ein
  Backup existiert, bevor Sie **Install updates** wählen ([ezupdate](../../../../en/us/features/6.0/extensions/ezupdate.md));
- **einem Wechsel der Datenbank-Engine** ([Eine Website auf eine andere Datenbank umziehen](../../../../en/us/help/database-support.md#moving-a-site-to-another-database));
- **einer Wiederherstellung**: Sichern Sie zuerst den aktuellen Zustand, damit eine falsche Wiederherstellung rückgängig
  gemacht werden kann.

Ein Hoster kann das als Schaltfläche oder Ticket anbieten („Snapshot vor der Änderung“); siehe
[Kunden-Websites aktualisieren](kunden-websites-aktualisieren.md).

## Aufbewahrung in Exponential

Exponential bewahrt manche Aufzeichnungen von sich aus für eine festgelegte Zeit auf. Ein Backup bewahrt sie länger auf;
stimmen Sie also beides aufeinander ab:

| Aufzeichnung | Einstellung | Mitgelieferter Wert |
|---|---|---|
| Prüfprotokoll, aktuelle Dateien (Inhalte, Zugriff, System, Handel) | `audit.ini [AuditChannel_*] LiveDays` | 90 Tage |
| Prüfprotokoll, Archive | `ArchiveDays` | 730 Tage |
| Prüfprotokoll, Lesekanal | `LiveDays` / `ArchiveDays` von `AuditChannel_read` | 30 / 90 Tage |
| Einwilligungsprotokoll für E-Mails von Personen, die nicht mehr da sind | `mailpreferences.ini [ConsentSettings] RetentionDays` | 1095 Tage |
| Nicht bestätigte E-Mail-Anmeldungen | `[ConsentSettings] PendingDays` | 7 Tage |
| Einträge der Unterdrückungsliste | `[SuppressionSettings] RetentionDays` | 0 (für immer behalten; nur ein Hash wird gespeichert) |
| Archive von `404.log` / `security.log` | `logviewer.ini [RotationSettings]` | nach 30 / 90 Tagen bereinigt |
| Archive anderer Logs | `site.ini [FileSettings] LogFileRotateCount`, `LogFileMaxAge` | 3 Archive, keine Altersgrenze |

Quellen: [Das Prüfprotokoll](../../../../en/us/features/6.0/audit-trail.md),
[E-Mail-Einstellungen, Aufbewahrung](../../../../en/us/guides/mail-preferences-administrator.md#9-retention-and-the-cronjob),
[Anfrage-Logs](../../../../en/us/features/6.0/request-logs.md), [Log-Rotation](../../../../en/us/features/6.0/log-rotation.md).
Die Sicherheitsseiten besprechen, was diese Werte für eine Datenschutzrichtlinie bedeuten:
[Aufbewahrung von Logs und Daten](../sicherheit/aufbewahrung-von-logs-und-daten.md).

## Backups und gelöschte personenbezogene Daten

Wenn eine Person verlangt, dass ihre Daten gelöscht werden, kann Exponential sie von der Live-Website entfernen (zum Beispiel
`./console exp:mail:preferences erase --email=<address> --yes` für die E-Mail-Einstellungen). Die Kopien in älteren Backups
enthalten sie noch, bis diese Backups ablaufen. Wie Sie damit umgehen (Aufbewahrung verkürzen, den Backup-Zeitraum in der
Datenschutzerklärung dokumentieren, mit Sorgfalt wiederherstellen), ist eine rechtliche Frage.

> **Hinweis**
> Diese Seite beschreibt, was die Software bereitstellt. Sie ist keine Rechtsberatung. Der Hoster und jeder Kunde müssen zu
> Aufbewahrungsfristen und zu personenbezogenen Daten in Backups ihren eigenen rechtlichen Rat einholen.

## Den Dienst Kunden beschreiben

Sagen Sie, was gesichert wird, wie oft, wie lange, wo, und wie eine Wiederherstellung angefordert wird. Vermeiden Sie
Versprechen, die die Plattform nicht von selbst hält:

- „Jedes Backup enthält die Datenbank und die hochgeladenen Dateien aus demselben Moment“ ist wahr, wenn der Job so gebaut
  ist wie auf [Was gesichert werden muss](backups-was-gesichert-werden-muss.md).
- Eine Wiederherstellung bringt die ganze Website auf diesen Moment zurück; das Wiederherstellen einer gelöschten Seite ist
  ein anderer Dienst (Inhalte lassen sich in Exponential oft stattdessen aus dem Papierkorb oder aus älteren Versionen
  zurückholen).
- Nennen Sie die Wiederherstellungszeit, die Sie in einem Test gemessen haben, nicht eine, die Sie erhoffen.

## Checkliste

- [ ] Die Richtlinie nennt pro Tarif Häufigkeit, Generationen, Ort, Verschlüsselung und wer wiederherstellen darf.
- [ ] Backups verlassen den Server und sind verschlüsselt.
- [ ] Vor jedem Upgrade, Composer-Update, Wechsel der Engine und jeder Wiederherstellung wird ein Backup erstellt.
- [ ] Die Aufbewahrungseinstellungen für Audit, Einwilligungen und Logs sind bekannt und stimmen mit dem Backup-Zeitraum überein.
- [ ] Wiederherstellungen werden nach Zeitplan getestet, und die gemessene Zeit wird festgehalten.
- [ ] Löschanfragen und die Aufbewahrung von Backups sind durch die Rechtsberatung von Hoster und Kunde abgedeckt.

## Verwandte Seiten

- [Was gesichert werden muss](backups-was-gesichert-werden-muss.md)
- [Wiederherstellung und Wiederherstellungstests](wiederherstellung-und-wiederherstellungstests.md)
- [SLA-Optionen](../geschaeft/sla-optionen-fuer-exponential-hosting.md), [Hosting-Tarife gestalten](../geschaeft/hosting-tarife-gestalten.md)
- [Compliance und DSGVO](../sicherheit/compliance-und-dsgvo.md)
- [Monatlicher Betrieb](../checklisten/checkliste-fuer-den-monatlichen-betrieb.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
