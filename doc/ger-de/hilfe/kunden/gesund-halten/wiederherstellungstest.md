---
title: Wiederherstellungstest
audience: customer, businesses, hosting
category: customer-success
order: 7
summary: Warum ein Betreiber einen regelmäßigen Wiederherstellungstest der Backups der Website verlangen sollte, was ein guter Test prüft, wie man verhindert, dass die wiederhergestellte Kopie echten Menschen E-Mails schickt, und welches Protokoll aufzubewahren ist.
keywords: Wiederherstellungstest, Wiederherstellungsübung, Backup, Wiederherstellungszeit, Wiederherstellungspunkt, Nachweis, Testkopie, E-Mail-Transport, restore test, restore drill, backup, recovery time, recovery point, evidence, test copy, mail transport
type: help
---
# Wiederherstellungstest

„Ein Backup, das Sie nie wiederhergestellt haben, ist eine Hoffnung“ ([Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md)).
Diese Seite ist für den Betreiber, der einen Beweis statt einer Hoffnung möchte: was er verlangen soll, was ein guter
Wiederherstellungstest prüft und was als Protokoll aufzubewahren ist. Die Übung Schritt für Schritt steht unter
[Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md) und, für Hoster, unter
[Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md).

## Wie oft

Die Quellen setzen ein Minimum: wiederherstellen „mindestens einmal auf einem anderen Rechner und nach jedem größeren
Upgrade“, und die wöchentliche Checkliste des Betriebsleitfadens verlangt, dass ein aktuelles Backup „mindestens einmal
auf einem anderen Rechner wiederhergestellt wurde“ ([Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md)).
Die Backup-Checkliste für Unternehmen verlangt eine Wiederherstellung auf einem anderen Rechner „im letzten Quartal“
([Backups, die sich wiederherstellen lassen](../../unternehmen/zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)). Wählen Sie einen
Rhythmus, tragen Sie ihn in den Kalender ein und benennen Sie die Person, die Ihnen das Ergebnis meldet.

## Was Sie verlangen sollten

Bitten Sie den Hoster oder Betreiber, ein aktuelles Backup auf einem nicht öffentlichen Rechner wiederherzustellen und
Ihnen Folgendes zu schicken:

| Punkt | Warum |
|---|---|
| Das Datum und das verwendete Backup | Zeigt, welche Kopie getestet wurde |
| Wie lange die Wiederherstellung gedauert hat | Ihre tatsächliche Wiederherstellungszeit |
| Die neuesten Inhalte, die in der Kopie gefunden wurden | Ihr tatsächlicher Wiederherstellungspunkt |
| Die Ergebnisse der Prüfungen unten | Der Beweis, dass die Kopie funktioniert |
| Alles, was schiefging | Was vor einem echten Notfall zu beheben ist |

## Prüfungen, die eine Wiederherstellung beweisen

Aus [Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md):

| Prüfung | Gutes Ergebnis |
|---|---|
| Die Startseite, eine tief liegende Seite, ein Bild, ein Download | Sie laden, Bilder eingeschlossen |
| Anmeldung in der Administration | Das Dashboard öffnet sich |
| **Setup > Systeminformationen** | Das erwartete Release; keine fehlgeschlagene Statusprüfung |
| Der Befehl für den Cache-Status | Endet mit `PASS` |
| Die Verifizierung des Prüfprotokolls | Exit-Status 0 |
| Ein Testelement veröffentlichen | Es erscheint auf der Testkopie |
| Ein Formular | Es lässt sich absenden; die E-Mail wird in eine Datei geschrieben, nicht versendet |
| Die neuesten Inhalte, die Sie erwarten | Sie sind vorhanden |

Bei einem Shop zusätzlich: Die neueste Bestellung, die Sie erwarten, steht in der Bestellliste.

## Die Kopie still und vertraulich halten

Eine wiederhergestellte Kopie ist eine vollständige Website mit echten Konten und echten E-Mail-Adressen. Die Seite zur
Übung nennt vier Regeln ([Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md)):

1. E-Mails werden in Dateien geschrieben, nicht versendet (`[MailSettings] Transport=file` auf dem Testrechner).
2. Die Crontab wird auf dem Testrechner nicht installiert, sodass keine Benachrichtigungen oder Newsletter laufen.
3. Die Kopie bleibt aus dem öffentlichen Internet heraus.
4. Die Kopie wird entfernt, wenn der Test abgeschlossen ist, Datenbank und Dateien eingeschlossen.

> **Warnung**
> Eine vergessene Testkopie, die einen Newsletter an alle Mitglieder schickt oder mit echten Kundendaten online bleibt,
> macht aus einem Test einen Vorfall. Bitten Sie Ihren Hoster, alle vier Regeln im Bericht zu bestätigen.

## Ein Protokoll zum Aufbewahren

```text
Restore test record
Date:                     ____
Done by:                  ____
Backup used (date/time):  ____
Restore time:             ____ (start) to ____ (end)
Newest content found:     ____
Checks passed:            front page / deep page / image / sign-in / system information /
                          cache status / audit verify / publish / form / shop
Problems found:           ____
Actions agreed:           ____ (who, by when)
Test copy removed on:     ____
```

Bewahren Sie die Protokolle zusammen auf. Sie sind Nachweise für eine Prüfung, einen Versicherer oder einen Kunden, der
fragt, wie Sie seine Daten schützen ([Compliance-Nachweise aus dem Prüfprotokoll](../../unternehmen/sicherheit/compliance-nachweise-aus-dem-audit-trail.md)).

## Wenn ein Test fehlschlägt

Ein fehlgeschlagener Test ist ein Erfolg des Verfahrens: Sie haben es an einem ruhigen Tag gelernt. Vereinbaren Sie die
Behebung, ein Datum und einen neuen Test. Häufige Befunde laut der Seite zur Übung: fehlende Bilder, weil die Hälfte mit
den Dateien nicht gesichert wurde, Fehler über fehlende Tabellen, weil das Backup von einem anderen Release stammt,
alte Inhalte, weil Caches nicht geleert wurden, und eine Wiederherstellung, die viel länger dauert als erwartet, weil die
Website gewachsen ist ([Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md)). Beheben Sie die
Ursache, aktualisieren Sie das schriftliche Verfahren und testen Sie erneut.

## Verwandte Seiten

- [Backups](backups.md)
- [Wenn die Website nicht erreichbar ist](wenn-die-website-nicht-erreichbar-ist.md)
- [Aus einem Backup wiederherstellen (Runbook)](../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
