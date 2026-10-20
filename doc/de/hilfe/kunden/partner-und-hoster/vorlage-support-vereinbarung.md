---
title: Vorlage für eine Support-Vereinbarung
audience: customer, hosting, partner
category: customer-success
order: 7
summary: Eine Vorlage für die Support-Bedingungen zwischen einem Website-Betreiber und dem Hoster oder Partner einer Exponential-Website - Kanäle, Zeiten, Schweregrade, erste Reaktionen, was enthalten und was ausgeschlossen ist, was der Kunde liefern muss, und Eskalation - mit auszufüllenden Feldern. Keine Rechtsberatung.
keywords: Support-Vereinbarung, Vorlage, Support-Bedingungen, Schweregrad, erste Reaktion, Kanäle, Zeiten, Eskalation, Anwendungs-Support, Hosting-Support, keine Rechtsberatung, support agreement, template, support terms, severity, first response, channels, hours, escalation, application support, hosting support
type: help
---
# Vorlage für eine Support-Vereinbarung

**Vorlage. Keine Rechtsberatung.** Eine Struktur für die Support-Bedingungen einer Exponential-Website, aus Sicht des
Betreibers. Sie baut auf dem Stufenblatt und den Schweregraden aus dem Bereich für Hosting auf
([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md)) und auf dem, was Partnern für ihre eigenen Support-Aussagen
geraten wird ([Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md)). Werte in
eckigen Klammern sind Felder, die zu vereinbaren sind; es werden keine Zahlen vorgeschlagen. Lassen Sie das Ergebnis
von Ihrer Rechtsberatung prüfen.

## Parteien und Umfang

| Feld | Wert |
|---|---|
| Kunde | [organization name] |
| Anbieter | [host or partner name] |
| Abgedeckte Websites | [site addresses] |
| Name der Stufe oder des Tarifs | [name] |
| Gültig ab | [date] |

## Kanäle und Zeiten

| Feld | Wert |
|---|---|
| Kanäle | [ticket / e-mail / phone / chat] |
| Zeiten | [hours], [time zone] |
| Außerhalb der Zeiten, für Schweregrad 1 | [phone number of a role, not a person's private number] |
| Sprache | [language] |

## Schweregrade

Die Definitionen folgen [Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md); passen Sie die Beispiele an Ihre Website an.

| Schweregrad | Definition | Beispiel | Erste Reaktion | Updates, solange offen |
|---|---|---|---|---|
| 1 Kritisch | Die Website ist nicht erreichbar oder Daten gehen verloren oder werden offengelegt | Jede Seite liefert einen Fehler; ein vermuteter Einbruch | [agreed time] | Alle [agreed interval] |
| 2 Hoch | Eine wichtige Funktion fällt aus, keine Umgehung | Die Redaktion kann nicht veröffentlichen; die Anmeldung schlägt fehl | [agreed time] | Alle [agreed interval] |
| 3 Normal | Eine Funktion fällt aus, eine Umgehung existiert | Eine Ansicht wird falsch dargestellt | [agreed time] | [agreed interval] |
| 4 Niedrig | Fragen, Wünsche | „Können wir eine Sprache hinzufügen?“ | [agreed time] | Wie vereinbart |

„Ein vermuteter Sicherheitsvorfall hat immer den Schweregrad 1“ (dieselbe Seite).

| Feld | Wert |
|---|---|
| „Erste Reaktion“ bedeutet | [first answer by a person / start of work / ...] |
| Gezählt in | [business hours / clock hours], [time zone] |

Partnern wird gesagt, dass das Programm nicht veröffentlicht, ob seine eigenen Reaktionszeiten die erste Antwort oder
die Lösung meinen und ob Geschäftsstunden oder Zeitstunden, und dass sie das schriftlich festhalten sollen
([Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md)). Klären Sie dieselben zwei
Punkte in Ihrer eigenen Vereinbarung.

## Enthalten und nicht enthalten

| Bereich | Enthalten? |
|---|---|
| Hosting-Support: Server, PHP, Engine, Datenbankserver, Backups, Wiederherstellungen, Zertifikate, E-Mail-Zustellung | [yes / no] |
| Updates von Exponential einspielen | [yes / no / see maintenance agreement] |
| Anwendungs-Support: Inhaltsklassen, Templates, Design | [yes / no / up to [hours] a month] |
| Eigene Erweiterungen und eigener Code des Kunden | [yes / no / as follows] |
| Redaktionelle Fragen („Wie veröffentliche ich?“) | [yes / no] |
| Datenkorrekturen innerhalb der Inhalte | [yes / no] |
| Upgrades, die Codeänderungen brauchen | [quoted separately / included] |
| Fehler in Exponential an das Projekt melden und verfolgen | [yes / no] |

Die Grenze zwischen den beiden Arten von Support ist die häufigste Quelle von Reibung
([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md)).

## Was der Kunde liefert

- Eine Meldung mit der Seitenadresse, der Uhrzeit mit Zeitzone, der Fehlerreferenz, falls angezeigt, was getan und
  was erwartet wurde und ob es alle oder ein Konto betrifft ([Probleme melden](../gesund-halten/probleme-melden.md)).
- Aktuelle Kontakte ([Vorlage für Eskalationskontakte](vorlage-eskalationskontakte.md)).
- Keine Passwörter: „Bitten Sie einen Kunden nie, ein Passwort zu schicken“; stattdessen wird ein Wegwerf-Testkonto
  verwendet ([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md)).

## Eskalation

| Schritt | Wann | An |
|---|---|---|
| 1 | Die Zeit für die erste Reaktion ist verstrichen | [role at the provider] |
| 2 | Schweregrad 1 dauert länger als [agreed time] | [manager role at the provider] |
| 3 | Die Ursache liegt in Exponential selbst | Der Anbieter meldet es beim Projekt ([GitHub-Issues](../gesund-halten/github-issues.md)) oder, bei Sicherheit, vertraulich ([Vertrauliche Sicherheitsmeldungen](../gesund-halten/vertrauliche-sicherheitsmeldungen.md)) |

## Berichte

| Feld | Wert |
|---|---|
| Ticketbericht | [monthly / quarterly]: Tickets pro Schweregrad, eingehaltene Reaktionszeiten |
| Gesendet an | [role] |

## Unterschriften

| | Kunde | Anbieter |
|---|---|---|
| Name und Rolle | [name, role] | [name, role] |
| Datum | [date] | [date] |

## Verwandte Seiten

- [SLA-Vorlage](sla-vorlage.md)
- [Wann Sie wen anrufen](wann-sie-wen-anrufen.md)
- [Verträge und SLAs](vertraege-und-slas.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
