---
title: Mehrere Marken in einer Installation
audience: businesses, project-manager, admin
category: business-solutions
order: 1
summary: Die Einstiegsseite für Organisationen, die mehrere Marken, Regionen oder Microsites aus einer Exponential-Installation betreiben - was der Bereich abdeckt, alle Seiten darin und eine empfohlene Lesereihenfolge.
keywords: Multisite, mehrere Marken, mehrere Websites, Marken, Regionen, Microsites, Siteaccess, eine Installation, gemeinsame Inhalte, Design pro Marke, Unternehmen, multisite, multi-brand, several sites, brands, shared content, per-brand design, business
type: help
---
# Mehrere Marken in einer Installation

Dieser Bereich richtet sich an Inhaber, Führungskräfte und Projektleiter, deren Organisation mehr als eine Website hat:
mehrere Marken, eine Website pro Region oder Land, eine Hauptwebsite mit Microsites für Kampagnen oder einen Shop neben
der Unternehmenswebsite. Er erklärt in einfacher Sprache, wie Exponential sie alle aus einer Installation bedient, was
das spart, was es miteinander verbindet und welche Entscheidungen Sie treffen sollten, bevor jemand mit dem Aufbau
beginnt. Jede Seite endet mit Links zu den technischen Seiten für diejenigen, die die Arbeit erledigen.

## Inhalt

1. [Was dieser Bereich abdeckt](#was-dieser-bereich-abdeckt)
2. [Für wen er gedacht ist](#für-wen-er-gedacht-ist)
3. [Seiten in diesem Bereich](#seiten-in-diesem-bereich)
4. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
5. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was dieser Bereich abdeckt

Die Unterstützung mehrerer Websites ist eine der wichtigsten Kernfunktionen, die das README des Projekts aufführt, und
das Standard-Seitenpaket des Installers, `sevenx_multisite`, ist selbst eine Multisite-Installation
([Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#multisite-eine-installation-mehrere-sites)).
In Exponential ist jede Website ein oder mehrere **Siteaccesses**: ein Name, ein Satz von Einstellungen in
`settings/siteaccess/<name>/` und eine Regel, welche Anfragen er beantwortet
([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

Die Seiten in diesem Ordner übersetzen das in geschäftliche Fragen:

- Sollen unsere Marken überhaupt eine Installation teilen oder getrennt laufen?
- Welche Inhalte teilen die Marken, und welche bleiben getrennt?
- Wie behält jede Marke ihr eigenes Erscheinungsbild?
- Wie funktionieren Links zwischen den Marken ohne fehlerhafte Seiten?
- Wo liegen die Einstellungen, Logs und Caches jeder Marke, und wer darf sie leeren?
- Was prüfen wir, bevor eine neue Marke online geht?

## Für wen er gedacht ist

| Leser | Was Sie hier bekommen |
|---|---|
| Inhaber oder Führungskraft | Die Abwägungen in einfacher Sprache, damit Sie entscheiden und budgetieren können |
| Marketing- oder Markenverantwortliche | Was gemeinsam bleibt, was jede Marke steuert, wie sich Designs unterscheiden |
| Projektleiter | Entscheidungen, Checklisten und die Links, die Sie dem Umsetzungsteam geben |
| Administrator | Eine kurze Zusammenfassung und die genaue technische Seite für jede Aufgabe |

Organisationen des öffentlichen Sektors haben einen eigenen Bereich:
[Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Seiten in diesem Bereich

| Seite | Was sie beantwortet |
|---|---|
| [Mehrere Marken planen](../../../../eng-us/help/businesses/multisite/multi-brand-planning.md) | Die Fragen, die zuerst zu klären sind, und wann eine Installation die falsche Wahl ist |
| [Eine Installation, viele Websites](../../../../eng-us/help/businesses/multisite/one-installation-many-sites.md) | Wie Siteaccesses, Hosts und Pfade aus einer Installation mehrere Websites machen |
| [Inhalte zwischen Marken teilen](../../../../eng-us/help/businesses/multisite/shared-content-across-brands.md) | Ein Inhaltsbaum, getrennte Ordner, Beziehungen, mehrere Platzierungen und Redakteure pro Marke |
| [Ein Design pro Marke](../../../../eng-us/help/businesses/multisite/per-brand-designs.md) | Wie jede Marke ihr eigenes Erscheinungsbild bekommt und Templates teilt, wo es hilft |
| [Links zwischen Ihren Websites](links-zwischen-ihren-websites.md) | Warum Links in die Inhalte einer anderen Marke brechen und wie `ezurlmultisite` das behebt |
| [Einstellungen, Logs und Caches pro Website](../../../../eng-us/help/businesses/multisite/per-site-settings-and-logs.md) | Einstellungen, Logs und Caches jeder Marke getrennt halten, für einen sauberen Betrieb |
| [Checkliste für mehrere Marken](../../../../eng-us/help/businesses/multisite/multi-brand-checklist.md) | Eine Checkliste für Start und Betrieb, wenn eine Marke hinzukommt |

## Empfohlene Lesereihenfolge

1. Beginnen Sie mit [Mehrere Marken planen](../../../../eng-us/help/businesses/multisite/multi-brand-planning.md), um zu entscheiden, ob eine Installation zu Ihnen
   passt.
2. Lesen Sie [Eine Installation, viele Websites](../../../../eng-us/help/businesses/multisite/one-installation-many-sites.md) für das Modell dahinter.
3. Dann [Inhalte zwischen Marken teilen](../../../../eng-us/help/businesses/multisite/shared-content-across-brands.md) und [Ein Design pro Marke](../../../../eng-us/help/businesses/multisite/per-brand-designs.md),
   die beiden Entscheidungen, die Markenverantwortlichen am wichtigsten sind.
4. Geben Sie [Links zwischen Ihren Websites](links-zwischen-ihren-websites.md) und
   [Einstellungen, Logs und Caches pro Website](../../../../eng-us/help/businesses/multisite/per-site-settings-and-logs.md) an Ihr technisches Team weiter.
5. Nutzen Sie die [Checkliste für mehrere Marken](../../../../eng-us/help/businesses/multisite/multi-brand-checklist.md) jedes Mal, wenn eine Marke hinzukommt.

> **Tipp**
> Die meisten Organisationen mit mehreren Marken veröffentlichen auch in mehreren Sprachen. Lesen Sie den
> [Bereich zur Mehrsprachigkeit](../../../../eng-us/help/businesses/multilingual/README.md) zusammen mit diesem: Eine Sprache ist oft nur ein weiterer
> Siteaccess in derselben Installation.

## Benachbarte Bereiche

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md) und der [Lesepfad](../lesepfad-fuer-unternehmen.md)
- [Verkaufen in mehreren Sprachen und Märkten](../../../../eng-us/help/businesses/multilingual/README.md)
- [Mobile Apps, Headless-Nutzung und Netzwerke](../mobil-und-headless/README.md)
- [Zuverlässigkeit](../zuverlaessigkeit/README.md) und [Kosten](../kosten/README.md) für die betriebliche Seite vieler Websites
- [Extranets](../extranets/README.md), wenn ein Partnerportal neben Ihren öffentlichen Marken läuft
- Für das technische Team: [Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)
  und, für Hosting-Unternehmen, [Multisite in einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md)

## Verwandte Seiten

- [Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)
- [Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
