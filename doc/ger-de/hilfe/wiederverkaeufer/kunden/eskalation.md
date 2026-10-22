---
title: Eskalation für Wiederverkäufer
audience: reseller, partner, hosting
category: reselling
order: 410
summary: Wie ein Wiederverkäufer ein Kundenproblem eskaliert, das er allein nicht lösen kann - zuerst im eigenen Unternehmen, dann über Partner-Support-Tickets, das Partnerteam, bezahlten Support von 7x, den öffentlichen Issue-Tracker für Fehler und security@se7enx.com für Sicherheitslücken -, mit dem, was Sie senden, und dem, was Sie dem Kunden in der Zwischenzeit sagen.
keywords: Eskalation, eskalieren, Support-Ticket, Partner-Support, Partnerteam, bezahlter Support, Issue-Tracker, Fehlermeldung, Sicherheit, security@se7enx.com, Sicherheitslücke, Kundenkommunikation, escalation, escalate, support ticket, partner support, partner team, paid support, issue tracker, bug report, security, vulnerability, customer communication
type: help
---
# Eskalation für Wiederverkäufer

Diese Seite ist für Support-Leiter bei Wiederverkäufern. Ihr Kunde eskaliert an Sie; Sie eskalieren weiter. Der
Kunde muss nie erfahren, dass es den Third-Level gibt: Ihr Vertrag macht Sie verantwortlich
([Verträge mit Kunden](vertraege-mit-kunden.md)).

## Inhalt

1. [Die Eskalationsleiter](#die-eskalationsleiter)
2. [Fehler in Exponential](#fehler-in-exponential)
3. [Sicherheitslücken](#sicherheitslücken)
4. [Was Sie dem Kunden in der Zwischenzeit sagen](#was-sie-dem-kunden-in-der-zwischenzeit-sagen)
5. [Blatt mit Eskalationskontakten](#blatt-mit-eskalationskontakten)

## Die Eskalationsleiter

| Schritt | Wann | Wie |
|---|---|---|
| 1. Im eigenen Unternehmen | Der First-Level kann es nicht lösen | Second-Level: der Entwickler oder Administrator, der die Website kennt; Runbooks ([Runbooks](../../hosting/runbooks/README.md)) |
| 2. Partner-Support-Ticket | Der Second-Level kann es nicht lösen | Ein Ticket als GitHub-Issue im Rahmen Ihres Partnertarifs ([Ein Partner-Support-Ticket eröffnen](../../partner/support-ticket-eroeffnen.md)) |
| 3. Erinnern | Die Reaktionszeit Ihrer Stufe ist überschritten | Kommentieren Sie das Ticket mit dem Zeitpunkt der Eröffnung und Ihrer Stufe |
| 4. Das Partnerteam | Die Auswirkung auf den Produktivbetrieb dauert an | Schreiben Sie an <partners@exponential.earth> mit dem Link zum Ticket |
| 5. Bezahlter Support | Sie brauchen jetzt garantierte Hilfe | Kaufen Sie bezahlten Support bei 7x (<https://exponential.earth/support>) |

Die Schritte 3 bis 5 folgen der Leiter der Partner ([Eskalationen und Sicherheitsprobleme](../../partner/eskalationen-und-sicherheitsprobleme.md)).
Bleiben Sie bei Eskalationen sachlich und höflich: was kaputt ist, seit wann, was es kostet, was Sie brauchen.

## Fehler in Exponential

Ein reproduzierbarer Fehler in Exponential selbst gehört in den Issue-Tracker der Software, wie unter
[Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md) beschrieben. Bevor Sie ihn melden:

- [ ] Reproduzieren Sie ihn auf einer Testinstallation des neuesten Release 6.0.x.
- [ ] Notieren Sie die Version, die PHP-Version, die Engine und die Datenbank.
- [ ] Fügen Sie die Fehlerreferenz und die passenden Log-Zeilen bei ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).
- [ ] Entfernen Sie Passwörter, Geheimnisse und personenbezogene Daten aus Logs und Screenshots.

Fehlerbehebungen kommen mit einem Release; Sie spielen es dann ein ([Kunden aktuell halten](kunden-aktuell-halten.md)).
Suchen Sie in der Zwischenzeit nach einer Umgehung, die Sie selbst anwenden können.

## Sicherheitslücken

- Melden Sie vertraulich an **security@se7enx.com**, nie in einem öffentlichen Issue, Forum, Chat oder einem Ticket,
  das andere lesen können.
- „You can expect a first answer within one business day“ (Sie können eine erste Antwort innerhalb eines Werktags
  erwarten, `SECURITY.md`).
- Geben Sie die Version, die PHP-Version, die Engine und die Datenbank an, was ein Angreifer tun kann und die Schritte
  zur Reproduktion; lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg.
- Informieren Sie die Öffentlichkeit nicht, bevor eine Fehlerbehebung veröffentlicht ist. Schützen Sie betroffene
  Kunden mit Maßnahmen, die Sie selbst anwenden können ([Sicherheitshärtung](../../sicherheitshaertung.md)).
- Aktualisieren Sie nach dem Release jede betroffene Website und sagen Sie den Kunden, was getan wurde, ohne
  Details, die Angreifern helfen.

Ein vermuteter Einbruch in eine Kunden-Website hat Schweregrad 1: Folgen Sie [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md).

## Was Sie dem Kunden in der Zwischenzeit sagen

```text
Status update <date, time>
What we see: <symptom, in plain words>
What we have done: <steps>
Workaround: <if any>
Next update: <time>
```

Senden Sie Updates im zugesagten Rhythmus, auch wenn sich nichts geändert hat. Geben Sie gegenüber dem Kunden nie
7x, dem Projekt oder einem anderen Lieferanten die Schuld; Sie sind für die Dienstleistung verantwortlich.

## Blatt mit Eskalationskontakten

```text
Our first line: <channel, hours>
Our second line: <names/roles, phone for S1>
Partner plan: <level>, tickets used this month: ____ of ____
Partner team: partners@exponential.earth
Paid support: https://exponential.earth/support
Security: security@se7enx.com (private)
Bug tracker: as described on "When something goes wrong"
Hosting supplier (if any): <contact, contract number>
```

Die eigene Fassung eines solchen Blatts für den Kunden ist die [Vorlage für Eskalationskontakte](../../kunden/partner-und-hoster/vorlage-eskalationskontakte.md).

## Verwandte Seiten

- [Eskalationen und Sicherheitsprobleme](../../partner/eskalationen-und-sicherheitsprobleme.md)
- [Sicherheitsupdates und Offenlegung](../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md)
- [Support-Stufen, die Sie anbieten](../angebote/support-stufen-die-sie-anbieten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
