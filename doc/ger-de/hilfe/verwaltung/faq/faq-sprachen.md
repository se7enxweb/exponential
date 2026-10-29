---
title: "FAQ: Sprachen und Schreibrichtung von rechts nach links"
audience: government, editor, admin, project-manager
category: public-sector
order: 90
summary: Antworten auf die Sprachfragen öffentlicher Stellen zu Exponential - mehrsprachige Inhalte, Amts- und Minderheitensprachen, Schriften von rechts nach links, die Administration in anderen Sprachen, Übersetzungsablauf, fehlende Übersetzungen und maschinell unterstützte Entwürfe.
keywords: FAQ Sprachen, mehrsprachig, Übersetzung, Amtssprachen, Minderheitensprachen, von rechts nach links, RTL, Arabisch, Hebräisch, Oberflächensprache, Locales, Übersetzungsablauf, öffentlicher Sektor, languages FAQ, multilingual, translation, official languages, minority languages, right to left, interface language, locales, translation workflow, public sector
type: help
---
# FAQ: Sprachen und Schreibrichtung von rechts nach links

Diese Seite beantwortet die Sprachfragen öffentlicher Stellen zu Exponential. Die Antworten stammen aus
[Inhaltssprachen](../../../anleitungen/inhaltssprachen.md),
[Übersetzungen und Sprachen](../../../funktionen/6.0/uebersetzungen-und-sprachen.md),
[Sprachen mit Schreibrichtung von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md), [Sprachen](../../sprachen.md)
und den [Standards zur Internationalisierung](../../../standards/internationalisierung.md).

## Inhalt

1. [Können wir in mehreren Sprachen veröffentlichen?](#können-wir-in-mehreren-sprachen-veröffentlichen)
2. [Welche Sprachen sind möglich?](#welche-sprachen-sind-möglich)
3. [Werden Sprachen mit Schreibrichtung von rechts nach links unterstützt?](#werden-sprachen-mit-schreibrichtung-von-rechts-nach-links-unterstützt)
4. [Können Mitarbeitende die Administration in ihrer Sprache nutzen?](#können-mitarbeitende-die-administration-in-ihrer-sprache-nutzen)
5. [Was geschieht, wenn eine Übersetzung fehlt?](#was-geschieht-wenn-eine-übersetzung-fehlt)
6. [Können Übersetzungen vor dem Veröffentlichen freigegeben werden?](#können-übersetzungen-vor-dem-veröffentlichen-freigegeben-werden)
7. [Gibt es maschinelle Übersetzung?](#gibt-es-maschinelle-übersetzung)

## Können wir in mehreren Sprachen veröffentlichen?

Ja. Jedes Inhaltsobjekt kann pro Inhaltssprache eine Übersetzung haben, und jede Sprache kann ihren eigenen Siteaccess,
ihre eigene Adresse und eigene Designeinstellungen haben ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md),
[Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md)).

## Welche Sprachen sind möglich?

Jede Sprache mit einer Locale. Das Projekt zählt 368 Locale-Dateien, 43 davon mit Schreibrichtung von rechts nach links
([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit)). Mehr
als 30 Sprachen auf einer Website brauchen ein 64-Bit-PHP, weil die Sprachen eines Objekts als Bits einer einzigen
Ganzzahl gespeichert werden ([2. Anforderungen](../../../installation/02-voraussetzungen.md#versionen)).

## Werden Sprachen mit Schreibrichtung von rechts nach links unterstützt?

Ja, in Inhalten und in der Administration: Sprachen mit Schreibrichtung von rechts nach links setzen `dir="rtl"` auf der
Seite, Namen werden isoliert, damit gemischter Text seine Reihenfolge behält
([Sprachen mit Schreibrichtung von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md)). Ihr öffentliches Design
muss außerdem ein gespiegeltes Layout beherrschen
([Websites mit Schreibrichtung von rechts nach links](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md)).

## Können Mitarbeitende die Administration in ihrer Sprache nutzen?

Ja, wo es eine Übersetzung gibt. Die Seite der Foundation zählt Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../../sprachen.md)) ([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit));
andere Seiten nennen leicht abweichende Zahlen, prüfen Sie deshalb den aktuellen Stand in der Sprachenliste, die zeigt,
wie vollständig jede Übersetzung ist ([Sprachen](../../sprachen.md)). Mitarbeitende wählen ihre Oberflächensprache
selbst ([Einstellungen zum Umschalten der Oberfläche](../../../anleitungen/ui-umschalter-einstellungen.md)).

## Was geschieht, wenn eine Übersetzung fehlt?

Ihre Einstellungen entscheiden, ob Besucher das Objekt in einer anderen Sprache sehen oder gar nicht
([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)). Entscheiden Sie das pro Website und teilen Sie es der
Redaktion mit.

## Können Übersetzungen vor dem Veröffentlichen freigegeben werden?

Ja, mit einem Freigabe-Workflow beim Veröffentlichen, der für Übersetzungen wie für jede Version gilt
([Workflows](../../../anleitungen/workflows.md)).

## Gibt es maschinelle Übersetzung?

Nicht im Kernel. Exponential Intelligence, eine optionale Erweiterung, kann Entwürfe schreiben, die eine Redakteurin
oder ein Redakteur prüft; sie veröffentlicht nie selbstständig
([Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md)). Jeder maschinell unterstützte Text sollte von
einer Übersetzerin oder einem Übersetzer geprüft werden, besonders rechtliche und amtliche Inhalte.

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Checkliste Mehrsprachigkeit](../checklisten/checkliste-mehrsprachigkeit.md)
- [Standards zur Internationalisierung](../../../standards/internationalisierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
