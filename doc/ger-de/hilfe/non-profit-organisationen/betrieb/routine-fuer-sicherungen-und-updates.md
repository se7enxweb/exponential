---
title: Routine für Sicherungen und Updates
audience: non-profit, admin, hosting, businesses
category: non-profits
order: 50
summary: Die kurze Routine, die die Exponential-Website einer Non-Profit-Organisation vor teuren Ausfällen bewahrt - Sicherungen von Datenbank und Speicher mit getesteter Wiederherstellung (sqlite3 .backup für SQLite), Updates von Exponential, Erweiterungen und PHP zuerst auf einer Kopie, Cron, Logs, die Upgrade-Prüfung und ein Kalender mit einem Namen neben jeder Aufgabe.
keywords: Sicherungen, Backups, Wiederherstellungstest, sqlite3 .backup, Speicher, Updates, Upgrades, PHP-Updates, Sicherheitskorrekturen, Cron, Logs, Log-Viewer, Upgrade-Prüfung, Wartungsmodus, Routine, Kalender, Non-Profit-Organisation, backups, restore test, storage, security fixes, log viewer, upgrade check, maintenance mode, routine, calendar, non-profit
type: help
---
# Routine für Sicherungen und Updates

Diese Seite richtet sich an alle, die sich um die Website kümmern: technisch versierte Ehrenamtliche, einen Hoster
oder einen Partner, und an die Person unter den Mitarbeitenden, die prüft, dass es erledigt wird. „Most expensive
incidents on small sites come from missing backups, unapplied updates and nobody watching“: Die meisten teuren
Vorfälle auf kleinen Websites entstehen durch fehlende Sicherungen, nicht eingespielte Updates und dadurch, dass
niemand hinsieht ([Betrieb mit kleinem Budget](../../unternehmen/gemeinnuetzige/betrieb-mit-kleinem-budget.md#die-routine-die-teure-probleme-verhindert)).
Die folgende Routine verhindert alle drei.

## Inhalt

1. [Sicherungen](#sicherungen)
2. [Der Wiederherstellungstest](#der-wiederherstellungstest)
3. [Updates](#updates)
4. [Cron und Logs](#cron-und-logs)
5. [Der Kalender](#der-kalender)
6. [Wenn Sie einen Hoster oder Partner haben](#wenn-sie-einen-hoster-oder-partner-haben)

## Sicherungen

Eine Sicherung besteht aus zwei Teilen: der **Datenbank** und dem **Speicherverzeichnis** (hochgeladene Bilder und
Dateien). Beides, sonst ist die Sicherung nutzlos.

| Teil | Wie | Quelle |
|---|---|---|
| SQLite-Datenbank | `sqlite3 <file> ".backup <target>"`, keine Dateikopie, während die Website ausgelastet ist | [SQLite-Datenbankunterstützung](../../../funktionen/6.0/sqlite-datenbank.md#grenzen-und-gute-praxis) |
| Andere Datenbanken | Das eigene Dump-Werkzeug der Datenbank | [Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md) |
| Speicher | Kopie von `var/<site>/storage` | ebenda |
| Einstellungen | `settings/override/` einschließlich der Audit-Schlüssel | [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md) |

Bewahren Sie mindestens eine Kopie außerhalb des Servers auf und schützen Sie sie: Sicherungen enthalten dieselben
personenbezogenen Daten wie die Website ([Datenschutz für Spender und Ehrenamtliche](datenschutz-fuer-spender-und-ehrenamtliche.md)).

Die Sicht des Eigentümers: [Sicherungen](../../kunden/gesund-halten/backups.md).

## Der Wiederherstellungstest

Eine Sicherung, die nie wiederhergestellt wurde, ist eine Hoffnung, keine Sicherung. Jedes Quartal:

1. Stellen Sie die neueste Sicherung an einem Testort wieder her (einem Laptop oder einem Testhost).
2. Öffnen Sie die Website; melden Sie sich an; öffnen Sie eine Seite mit Bildern und einer Datei.
3. Notieren Sie das Datum, die benötigte Zeit und was schiefgegangen ist.

Vorlage: [Wiederherstellungstest](../../kunden/gesund-halten/wiederherstellungstest.md).

## Updates

| Was | Warum | Lesen |
|---|---|---|
| Releases von Exponential | Korrekturen, auch Sicherheitskorrekturen; eine veröffentlichte Version ändert sich nie, eine Korrektur kommt als nächstes Release | [Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md), [Release-Linie und Support](../../kunden/gesund-halten/release-linie-und-support.md) |
| Erweiterungen | Sie werden zusammen mit Exponential über Composer aktualisiert | [Updates und Upgrades](../../ersteller/website-ersteller/updates-und-aktualisierungen.md) |
| PHP und der Server | Aufgabe des Hosters, oder Ihre auf einem virtuellen Server | [PHP-Versionen und -Erweiterungen](../../hosting/grundlagen/php-versionen-und-erweiterungen.md) |

Bewährte Praxis:

- Testen Sie ein Update zuerst auf einer Kopie; spielen Sie es danach auf der Live-Website ein.
- Verwenden Sie für längere Arbeiten den Wartungsmodus ([Wartungsmodus und geplante Arbeiten](../../kunden/gesund-halten/wartungsmodus-und-geplante-arbeiten.md)).
- Testen Sie nach einem Update die wichtigsten Wege: Startseite, Spenden, Ehrenamtsformular, Newsletter-Anmeldung und,
  falls genutzt, den Bestellabschluss im Shop.
- Die Upgrade-Prüfung der Administrationsoberfläche hilft zu sehen, was sich geändert hat ([Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- Checkliste: [Checkliste für den Upgrade-Tag](../../kunden/gesund-halten/checkliste-upgrade-tag.md).

## Cron und Logs

- Cron führt Newsletter, zeitgesteuertes Veröffentlichen, Veröffentlichen nach Freigabe, Aufrufzählungen und die
  Rotation des Audit-Protokolls aus. Prüfen Sie monatlich, dass es läuft ([Cronjobs](../../../anleitungen/cronjobs.md)).
- Überfliegen Sie die Logs monatlich im Log-Viewer ([Log-Viewer](../../../funktionen/6.0/log-betrachter.md),
  [Logs für Eigentümer](../../kunden/gesund-halten/logs-fuer-betreiber.md)).

## Der Kalender

| Wie oft | Aufgabe | Wer (Name) |
|---|---|---|
| Täglich (automatisch) | Sicherung läuft | |
| Monatlich | Sicherung vorhanden und aktuell; Updates geprüft; Cron gelaufen; Logs überflogen | |
| Vierteljährlich | Wiederherstellungstest; Überprüfung der Zugänge | |
| Bei jedem Release | Update auf einer Kopie getestet, eingespielt, wichtigste Wege getestet | |
| Jährlich | Hoster, Partner und Budget überprüfen; `exp:audit verify` | |

Tragen Sie dies in den gemeinsamen Kalender ein. Die [Monatliche Checkliste](../checklisten/monatliche-checkliste.md) und
die [Jährliche Checkliste](../checklisten/jaehrliche-checkliste.md) enthalten dieselben Aufgaben.

## Wenn Sie einen Hoster oder Partner haben

Fragen Sie schriftlich nach ([Was Sie Ihren Hoster fragen sollten](../../kunden/gesund-halten/was-sie-ihren-hoster-fragen-sollten.md),
[Was Sie Ihren Partner fragen sollten](../../kunden/gesund-halten/was-sie-ihren-partner-fragen-sollten.md)):

- [ ] Wie oft werden Sicherungen erstellt, und wo werden sie aufbewahrt?
- [ ] Wann haben Sie zuletzt eine Wiederherstellung unserer Website getestet? Können wir das Ergebnis sehen?
- [ ] Wie schnell nach einem Release werden Updates eingespielt?
- [ ] Wer teilt uns mit, wenn etwas erledigt wurde?

Ein Wartungsvertrag hält das ausdrücklich fest ([Vorlage für einen Wartungsvertrag](../../kunden/partner-und-hoster/vorlage-wartungsvereinbarung.md)).

## Verwandte Seiten

- [Betrieb für Non-Profit-Organisationen](README.md)
- [Ihre Website pflegen](../../ersteller/website-ersteller/ihre-website-pflegen.md)
- [Sicherungen, die sich wiederherstellen lassen](../../unternehmen/zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
