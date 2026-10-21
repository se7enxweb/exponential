---
title: Ihren Anbieter jedes Jahr überprüfen
audience: customer, businesses, project-manager
category: customer-success
order: 15
summary: Eine jährliche Überprüfung des Hosters und des Partners einer Exponential-Website - die zu sammelnden Nachweise, die zu stellenden Fragen, eine einfache Bewertung, was mit dem Ergebnis zu tun ist, und ein Protokoll zum Aufbewahren.
keywords: Anbieterprüfung, jährliche Überprüfung, Hoster, Partner, Nachweise, SLA, Scorecard, Verlängerung, Vertrag, Protokoll, provider review, yearly review, host, partner, evidence, scorecard, renewal, contract, record
type: help
---
# Ihren Anbieter jedes Jahr überprüfen

Sehen Sie sich einmal im Jahr, bevor ein Vertrag verlängert wird, an, was Ihr Hoster und Ihr Partner tatsächlich
geliefert haben. Eine Überprüfung auf Grundlage von Nachweisen ist ihnen gegenüber fairer und für Sie nützlicher als
eine, die auf dem letzten Vorfall beruht, an den sich alle erinnern. Diese Seite nennt die zu sammelnden Nachweise, die
Fragen, eine einfache Bewertung und ein Protokoll.

## Die Nachweise sammeln

| Bereich | Nachweis | Seite |
|---|---|---|
| Versionen | Der Verlauf der Versionen über das Jahr; wie schnell Releases eingespielt wurden | [Release-Linie und Support](../gesund-halten/release-linie-und-support.md) |
| Updates | Die Berichte nach jedem Upgrade | [Upgrades mit Ihrem Anbieter](../gesund-halten/upgrades-mit-ihrem-anbieter.md) |
| Verfügbarkeit | Die monatlichen Zahlen der Überwachung | [Überwachung](../gesund-halten/ueberwachung.md) |
| Vorfälle | Ihre Vorfallnotizen und die Darstellungen des Anbieters | [Vorlage für Vorfallnotizen](../gesund-halten/vorlage-vorfallnotizen.md) |
| Support | Tickets pro Schweregrad, Reaktionszeiten gemessen an der Vereinbarung | [Vorlage für eine Support-Vereinbarung](vorlage-support-vereinbarung.md) |
| Backups | Protokolle der Wiederherstellungstests | [Wiederherstellungstest](../gesund-halten/wiederherstellungstest.md) |
| Sicherheit | Ihre vierteljährlichen Sicherheitsüberprüfungen | [Sicherheits-Checkliste für Betreiber](../gesund-halten/sicherheits-checkliste-fuer-betreiber.md) |
| Änderungen | Änderungsanfragen: Schätzung gegenüber tatsächlichem Aufwand, pünktlich oder nicht | [Änderungsanfragen](aenderungsanfragen.md) |
| Deployments und Wartung | Der Kanal `system` des Prüfprotokolls listet Deployments und Wartungsfenster | [Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |

## Fragen Sie ihn

1. Was lief dieses Jahr gut, und was würden Sie ändern?
2. Welche Risiken sehen Sie für unsere Website im nächsten Jahr: Versionen, PHP, Erweiterungen ohne Betreuer?
3. Was wird das nächste Upgrade mit sich bringen?
4. Haben Mitarbeitende von Ihnen mit Zugriff auf unsere Website das Unternehmen verlassen? Wurden ihre Konten geschlossen?
5. Ist etwas in unserer Vereinbarung nicht mehr realistisch, in die eine oder andere Richtung?

## Eine einfache Bewertung

Bewerten Sie jede Zeile mit 0 (nicht erledigt), 1 (teilweise) oder 2 (erledigt, mit Nachweis). Die Idee folgt der
[Zuverlässigkeits-Scorecard](../../unternehmen/zuverlaessigkeit/bewertungsbogen-zuverlaessigkeit.md), die dieselben drei Stufen verwendet.

| Nr. | Frage | Punkte |
|---|---|---|
| 1 | Die Website lief das ganze Jahr auf einem gepflegten Release 6.0.x | [0-2] |
| 2 | Sicherheits-Releases wurden innerhalb der vereinbarten Zeit eingespielt | [0-2] |
| 3 | Jedes Upgrade kam mit den vereinbarten Nachweisen | [0-2] |
| 4 | Die Verfügbarkeit entsprach der Vereinbarung | [0-2] |
| 5 | Die Reaktionszeiten im Support entsprachen der Vereinbarung | [0-2] |
| 6 | Wiederherstellungstests wurden durchgeführt und protokolliert | [0-2] |
| 7 | Vorfälle wurden ehrlich gemeldet und ausgewertet | [0-2] |
| 8 | Änderungsanfragen wurden schriftlich geschätzt und wie vereinbart geliefert | [0-2] |
| 9 | Konten und Zugänge wurden ordentlich gehalten | [0-2] |
| 10 | Die Kommunikation war klar und rechtzeitig | [0-2] |

## Was Sie mit dem Ergebnis tun

| Ergebnis | Tun Sie |
|---|---|
| Überwiegend 2 | Verlängern; notieren, was beibehalten werden soll |
| Einige 0 oder 1 | Für jede einen Plan mit Terminen vereinbaren und ihn in einem Quartal überprüfen |
| Viele 0, oder dieselbe 0 wie im Vorjahr | Einen Wechsel erwägen ([Einen Partner auswählen](einen-partner-auswaehlen.md), [Einen Hoster auswählen](einen-hoster-auswaehlen.md)) und die [Übergabe](checkliste-uebergabe.md) planen |

Sehen Sie sich auch Ihre eigene Seite an: Waren die Meldungen vollständig, kamen Genehmigungen rechtzeitig, waren die
Kontakte aktuell? Ein Anbieter kann ein Versprechen nur einhalten, wenn auch der Teil des Kunden erledigt ist.

## Ein Protokoll zum Aufbewahren

```text
Provider review     year ____     provider ____     reviewed by ____
Score: ____ / 20
Strengths:          ____
Gaps and plan:      ____ (what, who, by when)
Agreement changes:  ____
Decision:           renew / renew with plan / change provider
Next review:        ____
```

## Verwandte Seiten

- [Verträge und SLAs](vertraege-und-slas.md)
- [Was Sie Ihren Hoster fragen sollten](../gesund-halten/was-sie-ihren-hoster-fragen-sollten.md)
- [Was Sie Ihren Partner fragen sollten](../gesund-halten/was-sie-ihren-partner-fragen-sollten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
