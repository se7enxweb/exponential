---
title: Wiederverkäufer von Supportverträgen
audience: reseller, partner
category: reselling
order: 120
summary: Wie Sie Supportverträge für Exponential-Websites verkaufen - was ein Vertrag abdeckt, Schweregrade, die drei Arten von Support hinter Ihnen (Community, Partner-Tickets, bezahlter Support von 7x), wie Sie ihn besetzen, damit Ihre Zusagen halten, und eine Vertragsübersicht zum Kopieren.
keywords: Supportvertrag, Supportplan, Schweregrad, Reaktionszeit, Erstreaktion, Lösung, Bereitschaft, Partner-Tickets, bezahlter Support, Community-Support, Eskalation, SLA, support contract, support plan, severity, response time, first response, resolution, on call, partner tickets, paid support, community support, escalation
type: help
---
# Wiederverkäufer von Supportverträgen

Diese Seite ist für Wiederverkäufer, die Support verkaufen: Antworten, Fehlerbehebungen und Beratung für eine
Exponential-Website, nach Plan, gegen eine Gebühr. Ein Supportvertrag kann für sich stehen oder zusammen mit einem
verwalteten Dienst angeboten werden ([Wiederverkäufer verwalteter Dienste](wiederverkaeufer-verwalteter-dienste.md)).

## Inhalt

1. [Was ein Supportvertrag abdeckt](#was-ein-supportvertrag-abdeckt)
2. [Der Support hinter Ihnen](#der-support-hinter-ihnen)
3. [Schweregrade](#schweregrade)
4. [Personalplanung, damit Zusagen halten](#personalplanung-damit-zusagen-halten)
5. [Vertragsübersicht zum Kopieren](#vertragsübersicht-zum-kopieren)

## Was ein Supportvertrag abdeckt

| Meist enthalten | Meist nicht enthalten oder gesondert abgerechnet |
|---|---|
| Fragen von Redakteuren und Administratoren beantworten | Neue Funktionen und Redesigns |
| Fehler anhand der Fehlerreferenz und der Logs diagnostizieren | Inhaltseingabe |
| Fehler in der Konfiguration und dem Code beheben, die Sie geliefert haben | Fehler in Code, den andere geschrieben haben, sofern nicht vereinbart |
| Releases von Exponential und Erweiterungen einspielen, falls vereinbart | Upgrades, die Codeänderungen brauchen |
| Fehler in Exponential an das Projekt melden | Eine Garantie, dass das Projekt sie bis zu einem Datum behebt |

Schreiben Sie die Grenze zwischen Hosting-Support und Anwendungs-Support in den Vertrag
([Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md#hosting-support-und-anwendungs-support)).

## Der Support hinter Ihnen

| Art | Wer | Was er Ihnen verspricht | Quelle |
|---|---|---|---|
| Community-Support | Jeder | Nichts: „its developers are not obligated to provide free support“ (seine Entwickler sind nicht verpflichtet, kostenlosen Support zu leisten) | <https://exponential.earth/support> |
| Partner-Support | Partner, je nach Stufe | Tickets als GitHub-Issues und eine Reaktionszeit je Stufe | [Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md) |
| Bezahlter Support von 7x | Jeder | Mit 7x vereinbart | <https://exponential.earth/support> |
| Sicherheitsmeldungen | Jeder | „a first answer within one business day“ (eine erste Antwort innerhalb eines Werktags) | `SECURITY.md` |

Nichts davon ist ein Versprechen an **Ihren** Kunden. Ihr Vertrag verspricht, was Ihr eigenes Personal liefert; der
Support hinter Ihnen ist Ihre Rückendeckung ([Service-Levels gestalten](../angebote/service-levels-gestalten.md)).

## Schweregrade

Ein übliches Schema, aus dem Hosting-Bereich ([Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md#schweregrade)):

| Schweregrad | Definition | Beispiel |
|---|---|---|
| 1 Kritisch | Die Website ist nicht erreichbar oder Daten gehen verloren oder werden offengelegt | Jede Seite liefert einen Fehler; ein vermuteter Einbruch |
| 2 Hoch | Eine wichtige Funktion versagt, keine Umgehung | Redakteure können nicht veröffentlichen; die Anmeldung schlägt fehl |
| 3 Normal | Eine Funktion versagt, eine Umgehung existiert | Ein View-Modus wird falsch dargestellt |
| 4 Niedrig | Fragen, Wünsche | „Können wir eine Sprache hinzufügen?“ |

Ein vermuteter Sicherheitsvorfall hat immer Schweregrad 1 ([Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)).

## Personalplanung, damit Zusagen halten

| Zusage | Braucht |
|---|---|
| Nächster Werktag für alle Anfragen | Eine geschulte Person und eine Vertretung |
| Derselbe Werktag für Schweregrad 1 und 2 | Zwei oder mehr Personen, die die Websites kennen; ein schriftliches Runbook |
| Außerhalb der Geschäftszeiten für Schweregrad 1 | Einen bezahlten Bereitschaftsplan mit Zugängen und Runbooks ([Runbooks](../../hosting/runbooks/README.md)) |

Fragen Sie Kunden jedes Mal nach den richtigen Informationen: Adresse der Seite und Uhrzeit, was sie getan haben,
die **Fehlerreferenz** von der Fehlerseite ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)),
ob alle betroffen sind und ob es kürzliche Änderungen gab. Fragen Sie einen Kunden nie nach einem Passwort.

## Vertragsübersicht zum Kopieren

```text
Support contract for <Customer>, site <example.com>
Covered: <sites, siteaccesses, extensions we delivered>
Hours: <days, hours, time zone>    Channels: <ticket / e-mail / phone for severity 1>
First response: S1 <time>, S2 <time>, S3 <time>, S4 <time>
Included hours per month: <n>; extra hours at <rate>
Releases: <we apply Exponential and extension releases within <n> days / on request>
Not covered: <list>
Escalation: <our support lead> -> <our manager>; upstream issues reported to the Exponential project
Reports: <monthly summary of tickets and work done>
```

## Verwandte Seiten

- [Support-Stufen, die Sie anbieten](../angebote/support-stufen-die-sie-anbieten.md)
- [Vorlage für einen Supportvertrag](../../kunden/partner-und-hoster/vorlage-support-vereinbarung.md)
- [Eskalation für Wiederverkäufer](../kunden/eskalation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
