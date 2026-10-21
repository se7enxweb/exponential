---
title: Der Lebenszyklus des Produkts
audience: government, project-manager, admin, hosting
category: public-sector
order: 10
summary: Der Lebenszyklus von Exponential aus der Sicht einer öffentlichen Stelle - Release-Richtlinie, unterstützte Versionen, Unterstützung von PHP und Plattform, die Roadmap, Budgets für Upgrades, Sicherheitspatches, ein Jahresrhythmus und das Ende eines Vertrags.
keywords: Lebenszyklus, Release-Richtlinie, unterstützte Versionen, PHP-Unterstützung, Roadmap, Upgrades, Budget, Sicherheitsupdates, Patches, Vertragsende, öffentlicher Sektor, lifecycle, release policy, supported versions, PHP support, roadmap, upgrades, budget, security updates, patching, end of contract, public sector
type: help
---
# Der Lebenszyklus des Produkts

Dieser Teil des Abschnitts zur Beschaffung richtet sich an Projektleitungen, Vertragsmanagement und
Sicherheitsbeauftragte einer öffentlichen Stelle, die eine Exponential-Website über einen Vertrag von mehreren Jahren
planen müssen. Er erklärt, was eine Versionsnummer verspricht, welche Versionen Korrekturen erhalten, wie die Plattform
unter der Software altert und wie Sie Budget und Vertrag so gestalten, dass die Website aktuell bleibt. Die wichtigsten
Quellen sind [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md),
[`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) und [Aktualisieren](../../../../anleitungen/aktualisieren.md).

## Die Seiten

| Seite | Sie beantwortet |
|---|---|
| [Release-Richtlinie und Versionsnummern](release-richtlinie-und-versionsnummern.md) | Veröffentlichte Versionen ändern sich nie; Korrekturen kommen als nächstes Release; wo Änderungen festgehalten werden |
| [Unterstützte Versionen](unterstuetzte-versionen.md) | 6.0.x wird gepflegt; 5.x und älter nicht; wie Sie eine unterstützte Version verlangen |
| [Unterstützung von PHP und Plattform](php-und-plattformunterstuetzung.md) | PHP 8.0 bis 8.5, sechs Datenbanken, die Engines und die Planung von Plattform-Upgrades |
| [Die Roadmap als öffentliche Stelle lesen](die-roadmap-lesen.md) | Die Roadmap ist eine Absicht; das Changelog ist der Nachweis; wie Sie eine Funktion anfragen oder finanzieren |
| [Upgrades im Haushalt planen](upgrades-im-haushalt-planen.md) | Drei Arten von Upgrades, was den Aufwand treibt, eine Budgetvorlage |
| [Sicherheitsupdates und Patch-Rhythmus](sicherheitsupdates-und-patch-rhythmus.md) | Von der Meldung zur Korrektur, was das Projekt verspricht, eine Klausel zu Patches |
| [Eine Website über einen mehrjährigen Vertrag aktuell halten](eine-website-aktuell-halten.md) | Ein monatlicher, vierteljährlicher und jährlicher Rhythmus mit Verantwortlichen und einem Bericht des Lieferanten |
| [Das Ende eines Vertrags](ende-eines-vertrags.md) | Verlängern, neu ausschreiben oder aussteigen, und die Übergabe, die jede Wahl braucht |

## Lesereihenfolge

1. [Release-Richtlinie und Versionsnummern](release-richtlinie-und-versionsnummern.md) und
   [Unterstützte Versionen](unterstuetzte-versionen.md): worauf Sie sich verlassen können.
2. [Unterstützung von PHP und Plattform](php-und-plattformunterstuetzung.md): die Schichten unter der Software.
3. [Sicherheitsupdates und Patch-Rhythmus](sicherheitsupdates-und-patch-rhythmus.md) und
   [Upgrades im Haushalt planen](upgrades-im-haushalt-planen.md): die Pflichten und ihre Kosten.
4. [Eine Website über einen mehrjährigen Vertrag aktuell halten](eine-website-aktuell-halten.md): die Routine.
5. [Die Roadmap als öffentliche Stelle lesen](die-roadmap-lesen.md) und [Das Ende eines Vertrags](ende-eines-vertrags.md):
   die Zukunft und der Weg hinaus.

## Wichtige Fakten

- Ein veröffentlichter Tag wird nie geändert; eine Korrektur erreicht Sie als nächstes Release (`SECURITY.md`).
- 6.0.x ist die gepflegte Linie; 5.x, 4.x, 3.x und älter werden nicht unterstützt (`SECURITY.md`).
- Eine vertrauliche Sicherheitsmeldung erhält innerhalb eines Werktags eine erste Antwort; eine Frist für die Korrektur
  wird nicht versprochen (`SECURITY.md`).
- PHP 8.0 bis 8.5 werden unterstützt, Velocity eingeschlossen ab seinem Release 0.0.4.54 ([2. Anforderungen](../../../../installation/02-voraussetzungen.md)).
- Das Projekt veröffentlicht kein SLA und keine End-of-Life-Daten ([SLA-Optionen für das Hosting von Exponential](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).

## Verwandte Seiten

- [Exponential im öffentlichen Sektor beschaffen](../README.md)
- Verwaltungen: hier beginnen (government-start-here.md, die Einstiegsseite dieses Hilfeabschnitts)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
