---
title: Sicherungspläne und Aufbewahrung
audience: government, hosting, admin, project-manager
category: public-sector
order: 50
summary: Für öffentliche Stellen, die eine Sicherungsrichtlinie für eine Exponential-Website schreiben. Die Entscheidungen, die eine Richtlinie treffen muss, die Regeln, die die Quellen nennen, die Aufbewahrungsfristen, die Exponential selbst einhält, und wie die Aufbewahrung von Datensicherungen Pflichten der Schriftgutverwaltung und des Datenschutzes erfüllt.
keywords: Sicherungsplan, Aufbewahrung, Generationen, Sicherungsrichtlinie, Schriftgutverwaltung, Aufbewahrungsplan, Aufbewahrung des Prüfprotokolls, LiveDays, ArchiveDays, Löschung, personenbezogene Daten in Datensicherungen, Kontinuität, backup schedule, retention, generations, backup policy, records management, retention schedule, audit retention, erasure, personal data in backups, continuity
type: help
---
# Sicherungspläne und Aufbewahrung

Diese Seite richtet sich an IT-Leitungen, Verantwortliche für das Schriftgut und Datenschutzbeauftragte in
öffentlichen Stellen, die die Sicherungsrichtlinie einer Website schreiben. Sie führt die Entscheidungen auf, die die
Richtlinie treffen muss, die Regeln, die die Quellen nennen, welche Einträge Exponential selbst für eine festgelegte
Zeit aufbewahrt und wo die Aufbewahrung von Datensicherungen auf Ihre Pflichten zu Schriftgut und Datenschutz trifft.
Sie nennt keine eigenen festen Zahlen; die Hosting-Seite
[Sicherungspläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md) hat denselben Aufbau
für Dienstleister.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Regeln, die die Quellen nennen](#regeln-die-die-quellen-nennen)
2. [Entscheidungen einer Sicherungsrichtlinie](#entscheidungen-einer-sicherungsrichtlinie)
3. [Aufbewahrung innerhalb von Exponential](#aufbewahrung-innerhalb-von-exponential)
4. [Schriftgutverwaltung und Datensicherungen](#schriftgutverwaltung-und-datensicherungen)
5. [Personenbezogene Daten in Datensicherungen](#personenbezogene-daten-in-datensicherungen)
6. [Checkliste](#checkliste)

## Regeln, die die Quellen nennen

| Regel | Quelle |
|---|---|
| Eine Datensicherung besteht aus der Datenbank und den Dateien, zum selben Zeitpunkt erstellt | [10.9](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) |
| Bewahren Sie Datensicherungen getrennt vom Server auf und halten Sie mehrere Generationen vor | [10.9](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) |
| Sichern Sie vor jeder Aktualisierung | [11 Aktualisieren](../../../../installation/11-aktualisieren.md) |
| Eine Datensicherung aus den letzten sieben Tagen existiert und wurde mindestens einmal auf einer anderen Maschine wiederhergestellt | [Eine Website betreiben, wöchentliche Checkliste](../../../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste) |

## Entscheidungen einer Sicherungsrichtlinie

| Entscheidung | Optionen | Zu bedenken |
|---|---|---|
| Häufigkeit der Datenbanksicherung | nächtlich; mehrmals täglich; fortlaufend mit der eigenen logbasierten Wiederherstellung der Engine | wie viel redaktionell gearbeitet wird; Ihr Wiederherstellungspunkt ([RPO und RTO als Entscheidungen](rpo-und-rto-als-entscheidungen.md)) |
| Häufigkeit der Dateisicherung | mit jeder Datenbanksicherung oder inkrementell | eine Wiederherstellung braucht beide Hälften vom selben Zeitpunkt |
| Generationen | tägliche, wöchentliche, monatliche, jährliche Kopien | wie spät ein Fehler bemerkt werden kann |
| Speicherort | ein anderes Gebäude, eine andere Region, ein anderer Dienstleister | [Kopien außer Haus und verschlüsselte Kopien](externe-und-verschluesselte-kopien.md) |
| Verschlüsselung | im Ruhezustand und bei der Übertragung | die Datensicherung enthält Zugangsdaten und personenbezogene Daten |
| Wer wiederherstellen darf | benannte Rollen | Funktionstrennung |
| Wiederherstellungstests | wie oft, auf welcher Maschine | [Wiederherstellung testen](wiederherstellungstests.md) |
| Zusätzliche Datensicherungen | vor Aktualisierungen, Engine-Wechseln, Wiederherstellungen, Massenimporten | ein Wiederherstellungspunkt direkt vor riskanten Arbeiten |

## Aufbewahrung innerhalb von Exponential

Einige Einträge bewahrt Exponential selbst für eine festgelegte Zeit auf. Eine Datensicherung bewahrt sie länger auf,
stimmen Sie die beiden also aufeinander ab
([Aufbewahrung von Logs und Daten](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)):

| Eintrag | Einstellung | Ausgeliefert |
|---|---|---|
| Prüfprotokoll, live (content, access, system, commerce) | `audit.ini [AuditChannel_*] LiveDays` | 90 Tage |
| Prüfprotokoll, Archive | `ArchiveDays` | 730 Tage |
| Prüfprotokoll, Lesekanal | `LiveDays` / `ArchiveDays` | 30 / 90 Tage |
| E-Mail-Einwilligungsprotokoll von Personen, die nicht mehr da sind | `mailpreferences.ini [ConsentSettings] RetentionDays` | 1095 Tage |
| Archive von `404.log` / `security.log` | `logviewer.ini [RotationSettings]` | nach 30 / 90 Tagen bereinigt |
| Archive anderer Logs | `site.ini [FileSettings] LogFileRotateCount`, `LogFileMaxAge` | 3 Archive, keine Altersgrenze |

## Schriftgutverwaltung und Datensicherungen

Datensicherungen sind ein Werkzeug für die Kontinuität, kein Archiv. Viele Schriftgutrichtlinien unterscheiden
beides: Das Archiv bewahrt Schriftgut für die vorgeschriebene Dauer in verwalteter Form auf; Datensicherungen halten
das System für kürzere Zeit wiederherstellbar. Für eine Exponential-Website bedeutet das meist:

- veröffentlichte Inhalte, die als Schriftgut aufbewahrt werden müssen, gehen über Ihren Schriftgutprozess in Ihr
  Schriftgutsystem oder Archiv, statt sich auf alte Datensicherungen zu verlassen;
- das Prüfprotokoll kann jahrelang in seinen eigenen signierten Archiven (die Quellen zeigen eine Referenzkonfiguration
  mit `ArchiveDays=3650`) auf Speicher aufbewahrt werden, den der Webserver nicht ändern kann
  ([Archive des Prüfprotokolls und Datensicherung](audit-trail-archive-und-datensicherungen.md));
- Sicherungsgenerationen werden nur so lange aufbewahrt, wie die Kontinuität es erfordert.

Schriftgutnormen wie [ISO 15489-1](https://www.iso.org/standard/62542.html) beschreiben die Seite des Schriftguts;
stimmen Sie die Aufteilung mit Ihren Verantwortlichen für das Schriftgut ab.

## Personenbezogene Daten in Datensicherungen

Wenn die Daten einer Person auf der Live-Website gelöscht werden (zum Beispiel
`./console exp:mail:preferences erase --email=<address> --yes` für E-Mail-Einstellungen), enthalten ältere
Datensicherungen sie weiterhin, bis diese ablaufen
([Sicherungspläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)). Gängige Praxis:
Halten Sie die Aufbewahrung von Datensicherungen kurz und in der Datenschutzerklärung dokumentiert, und wenden Sie
nach einer Wiederherstellung die seit dem Sicherungsdatum vorgenommenen Löschungen erneut an. Welche Praxis
vorgeschrieben ist, ist eine Rechtsfrage für Ihre Organisation.

## Checkliste

- [ ] Die Richtlinie nennt Häufigkeit, Generationen, Speicherort, Verschlüsselung, wer wiederherstellen darf und
  Wiederherstellungstests.
- [ ] Zusätzliche Datensicherungen vor Aktualisierungen und anderen riskanten Änderungen sind Teil des
  Änderungsverfahrens.
- [ ] Die eigenen Aufbewahrungseinstellungen von Exponential sind bekannt und stimmen mit der Richtlinie überein.
- [ ] Schriftgutverwaltung und Datensicherungen sind getrennt und mit den Verantwortlichen für das Schriftgut
  abgestimmt.
- [ ] Der Umgang mit gelöschten personenbezogenen Daten nach einer Wiederherstellung ist entschieden.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Datenschutzstandards](../../../../standards/datenschutz.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
