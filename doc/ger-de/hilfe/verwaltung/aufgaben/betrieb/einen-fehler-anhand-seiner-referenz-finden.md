---
title: Einen Fehler anhand seiner Referenz finden
audience: government, admin, developer
category: public-sector
order: 170
summary: Schritt für Schritt - wenn ein Bürger oder eine Kollegin eine Fehlerseite mit einer Referenz wie ERR-0123456789 meldet, die Log-Zeile und alle Zeilen dieser Anfrage im Log-Viewer oder mit exp:logs finden und an die Entwickler übergeben.
keywords: Fehlerreferenz, ERR-, Fehlerseite, Meldung von Bürgern, Service Desk, Log-Viewer, exp:logs --reference, Zeilen der Anfrage, Support-Ticket, error reference, error page, citizen report, service desk, log viewer, request lines, support ticket
type: help
---
# Einen Fehler anhand seiner Referenz finden

Diese Aufgabe ist für Mitarbeiter des Service Desks und Administratoren. Eine Fehlerseite von Exponential zeigt eine
Referenz wie `ERR-0123456789`; dieselbe Referenz steht in der Log-Zeile, sodass die Meldung eines Bürgers direkt zur
Ursache führt. Anleitung: [Einen Fehler anhand seiner Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [In der Administration](#in-der-administration)
3. [In der Shell](#in-der-shell)
4. [Nächste Schritte](#nächste-schritte)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Referenz aus der Meldung (fragen Sie den Bürger danach und nach der Uhrzeit; ein Screenshot hilft).
- `setup/logs` oder Shell-Zugang.

## In der Administration

1. **Setup > Logdateien**.
2. Suchen Sie die Referenz (die Suche akzeptiert eine Fehlerreferenz) in den Kernel-Logs, mit den Rotationen, wenn die
   Meldung älter ist.
3. Öffnen Sie die Zeile: Der Viewer zeigt Meldung, Datei und Zeile sowie die anderen Zeilen derselben Anfrage, verknüpft
   mit den Audit-Einträgen derselben Minute.
4. **Permalink kopieren** für das Ticket.

## In der Shell

```bash
./console exp:logs --reference=ERR-0123456789
```

Er sagt, wo die Referenz steht, und gibt die Zeilen ihrer Anfrage aus. Exit-Code 0 gefunden, 1 nicht gefunden.

## Nächste Schritte

| Die Zeile sagt | Tun |
|---|---|
| ein Fehler in Template oder Code | Ticket an die Entwickler mit dem Permalink |
| ein Datenbankfehler | [Datenbank nicht erreichbar](../../betrieb/vorfaelle/datenbank-nicht-erreichbar.md) |
| eine fehlende Datei oder Berechtigung | [Datei- und Verzeichnisberechtigungen prüfen](datei-und-verzeichnisberechtigungen-pruefen.md) |
| überhaupt nicht gefunden | die Meldung stammt vielleicht von einem anderen Server oder einer anderen Engine; suchen Sie dort, oder mit `--rotations` in älteren Logs |

Antworten Sie dem Bürger ohne technische Details; personenbezogene Daten aus der Meldung bleiben in Ihrem Ticketsystem.

## Verwandte Seiten
- [Die Fehlerübersicht lesen](die-fehleruebersicht-lesen.md)
- [Der Log-Viewer](../../betrieb/ueberwachung/der-log-viewer.md)
- [Site ausgefallen](../../betrieb/vorfaelle/website-nicht-erreichbar.md)
- [Wenn etwas schiefgeht (Hilfe)](../../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
