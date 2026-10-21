---
title: Die Roadmap als öffentliche Stelle lesen
audience: government, project-manager, sales-marketing
category: public-sector
order: 50
summary: Wo die Roadmap von Exponential zu finden ist, warum eine öffentliche Stelle sie als Absichtserklärung statt als Zusage lesen sollte, wie Sie Punkte der Roadmap mit dem Changelog abgleichen und wie Sie eine Funktion anfragen oder finanzieren, die Ihre Organisation braucht.
keywords: Roadmap, Pläne, künftige Funktionen, Absichtserklärung, Funktionswunsch, Sponsoring, Changelog, Community-Roadmap, Beschaffung, öffentlicher Sektor, roadmap, plans, future features, statement of intent, feature request, sponsorship, changelog, community roadmap, procurement, public sector
type: help
---
# Die Roadmap als öffentliche Stelle lesen

Diese Seite richtet sich an Projektleitungen und Bewertungsgremien, die wissen möchten, wohin sich Exponential
entwickelt. Sie erklärt, wo die Roadmap steht, wie viel Gewicht sie in einer Beschaffung verdient und wie eine
öffentliche Stelle sie beeinflussen kann. Die Quelle ist [Die Roadmap: was geplant ist und wie Sie sie mitgestalten](../../../projekt/roadmap.md).

## Inhalt

1. [Wo die Roadmap steht](#wo-die-roadmap-steht)
2. [Absicht, keine Zusage](#absicht-keine-zusage)
3. [Einen Punkt mit dem Repository abgleichen](#einen-punkt-mit-dem-repository-abgleichen)
4. [Eine Funktion anfragen](#eine-funktion-anfragen)
5. [In einer Bewertung](#in-einer-bewertung)

## Wo die Roadmap steht

Das Projekt führt zwei öffentliche Roadmaps auf Share Exponential, beide von exponential.earth aus verlinkt
([Die Roadmap](../../../projekt/roadmap.md)):

| Roadmap | Was sie ist |
|---|---|
| Offizielle Roadmap | die Pläne von 7x und des Projekts, Produkt für Produkt |
| Community-Roadmap | Funktionswünsche der Nutzer, öffentlich diskutiert, moderiert |

Die im Oktober 2026 gelesene offizielle Roadmap deckte 2025/Q4 bis 2026/Q2 ab. Für Exponential 6.0.x nannte sie unter
anderem Stabilisierung, mehr Templates im Standarddesign, Videoanleitungen, ein Partnerportal und die Unterstützung von
PHP 8.6, geschätzt für 2026/Q3. Die vollständige Liste steht unter [Die Roadmap](../../../projekt/roadmap.md#was-die-offizielle-roadmap-aufführt).

## Absicht, keine Zusage

Die Hilfe sagt es deutlich: „Das Changelog ist der maßgebliche Nachweis; die Roadmap ist eine Absichtserklärung“
([Die Roadmap](../../../projekt/roadmap.md#was-bereits-geliefert-wurde)). Die Roadmap hinkte beim Lesen auch den
Releases hinterher: Sie nannte 6.0.10 als aktuell, während 6.0.14 bereits getaggt war.

Für eine öffentliche Stelle heißt das:

- **Kaufen Sie nie auf Grundlage eines Roadmap-Punkts.** Bewerten Sie, was heute existiert und vorgeführt werden kann.
- Hängt eine Anforderung von einer künftigen Funktion ab, machen Sie ihre Lieferung zu einer Vertragspflicht Ihres
  Lieferanten, mit Abnahmekriterien, nicht zu einer Annahme über das Open-Source-Projekt.
- Lesen Sie die Roadmap, um Richtung und Aktivität zu beurteilen, nicht Termine.

## Einen Punkt mit dem Repository abgleichen

Bevor Sie sich auf eine Aussage über eine Funktion verlassen, prüfen Sie sie:

1. Durchsuchen Sie das Changelog der aktuellen Linie ([Changelog 6.0.15](../../../../../changelogs/6.0/6.0.15.md)).
2. Suchen Sie eine Funktionsseite in `doc/eng-us/features/6.0/` (zum Beispiel [Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md)).
3. Verlangen Sie eine Vorführung auf einer Testinstallation; siehe den Teil zur Bewertung dieses Abschnitts zur
   Beschaffung.

Die Roadmap-Seite selbst zeigt diese Methode: Sie listet Punkte der Roadmap mit dem auf, was das Repository zeigt
([Die Roadmap](../../../projekt/roadmap.md#was-bereits-geliefert-wurde)).

## Eine Funktion anfragen

| Weg | Wie |
|---|---|
| Fragen | fügen Sie sie der Community-Roadmap auf Share Exponential hinzu; erklären Sie das Problem, nicht nur die Lösung |
| Eine Lücke melden | eröffnen Sie ein Issue im Issue-Tracker des Projekts auf GitHub |
| Diskutieren | die Foren und Chats ([Kanäle der Community](../../../projekt/community-kanaele.md)) |
| Bauen | lassen Sie Ihren Lieferanten sie als Pull Request beitragen ([Zum Projekt beitragen](../../../projekt/zum-projekt-beitragen.md)) |
| Finanzieren | sponsern Sie die Arbeit ([Geben und spenden](../../../projekt/geben-und-spenden.md)); Partner haben ihren eigenen Weg ([Mitwirkung an der Roadmap](../../../partner/an-der-roadmap-mitwirken.md)) |

Eine öffentliche Stelle, die einen Lieferanten für den Bau einer allgemein nützlichen Funktion bezahlt, kann im Vertrag
verlangen, dass sie unter der GPL an das Projekt zurückgegeben wird. Dann pflegen alle sie, nicht nur Sie.

## In einer Bewertung

Fragen, die ein Gremium Bietern stellen kann:

- Von welchen Punkten der Roadmap hängt Ihr Angebot ab? (Idealerweise: von keinem.)
- Welche Funktionen, die Sie bauen wollen, würden Sie zurückgeben, und wie?
- Wie verfolgen Sie die Releases und das Changelog des Projekts?

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Die Roadmap: was geplant ist und wie Sie sie mitgestalten](../../../projekt/roadmap.md)
- [Release-Richtlinie und Versionsnummern](release-richtlinie-und-versionsnummern.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
