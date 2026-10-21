---
title: Inhaltssprachen und Oberflächensprachen
audience: government, admin, editor
category: public-sector
order: 30
summary: Die zwei Arten von Sprache in Exponential, erklärt für öffentliche Stellen - Inhaltssprachen (in denen Seiten geschrieben sind) und Oberflächensprachen (in denen die Administration und die festen Texte der Website sprechen), wo jede eingestellt wird und wie sie zusammenwirken.
keywords: Inhaltssprache, Oberflächensprache, Locale, SiteLanguageList, TextTranslation, translation.ts, Sprache der Administration, Texte der Website, content language, interface language, administration language, site texts
type: help
---
# Inhaltssprachen und Oberflächensprachen

Diese Seite richtet sich an Administratoren und leitende Redakteure, die eine Website in mehreren Sprachen einrichten.
Exponential kennt zwei voneinander unabhängige Arten von Sprache, die an verschiedenen Stellen eingestellt werden. Sie
zu verwechseln ist die häufigste Ursache für Verwirrung
([Inhaltssprachen, Abschnitt 1](../../../anleitungen/inhaltssprachen.md#1-inhaltssprachen-und-oberflächensprachen)).

## Inhalt

1. [Die zwei Arten](#die-zwei-arten)
2. [Locales, das dritte Stück](#locales-das-dritte-stück)
3. [Typische Kombinationen](#typische-kombinationen)
4. [Woher die festen Texte einer öffentlichen Website kommen](#woher-die-festen-texte-einer-öffentlichen-website-kommen)

## Die zwei Arten

| | Inhaltssprache | Oberflächensprache |
|---|---|---|
| Was sie ist | eine Sprache, in der Objekte (Seiten, Dokumente, Benutzer) geschrieben sein können | die Sprache von Schaltflächen, Menüs und Meldungen |
| Wo die Texte liegen | in der Datenbank, eine Übersetzung pro Objekt und Sprache | in Übersetzungsdateien, `share/translations/<locale>/translation.ts` und im Verzeichnis `translations/` jeder Erweiterung |
| Wo sie verwaltet wird | **Setup > Sprachen** (`content/translations`) | `site.ini [RegionalSettings] Locale` pro Siteaccess, und die eigene Wahl der Administratoren |
| Was sie für eine Seite auswählt | `SiteLanguageList[]` des Siteaccess | `Locale` des Siteaccess, mit `TextTranslation=enabled` |

Ein Siteaccess kann deutsche Inhalte mit einer englischen Oberfläche zeigen und umgekehrt.

## Locales, das dritte Stück

Eine **Locale** (`share/locale/<code>.ini`) enthält die Namen einer Sprache und eines Landes, die HTTP-Sprachangabe,
die Schreibrichtung sowie Datums- und Zahlenformate. Eine Inhaltssprache ist ein Locale-Code, deshalb lässt sich nur
eine Locale mit Datei hinzufügen; die `Locale` des Siteaccess entscheidet, wie Datum und Zahlen geschrieben werden
([Sprachen in Exponential, Abschnitt 1](../../../../eng-us/languages.md#1-locales-translations-content-languages-and-siteaccesses)).

## Typische Kombinationen

| Situation | Öffentlicher Siteaccess | Administration |
|---|---|---|
| Zweisprachige Website eines Landes | ein Siteaccess pro Amtssprache, jeder mit seiner `Locale` und `SiteLanguageList[]` | eine Administration; Mitarbeitende wählen ihre Oberflächensprache |
| Mitarbeitende arbeiten in einer Sprache ohne vollständige Oberflächenübersetzung | | die Oberfläche zeigt für unübersetzte Texte Englisch, während Inhalte in der Sprache der Mitarbeitenden bearbeitet werden |
| Ein Websitebereich in einer Minderheitensprache | ein eigener Siteaccess, der die Minderheitensprache zuerst und die Hauptsprache an zweiter Stelle aufführt | |

## Woher die festen Texte einer öffentlichen Website kommen

Texte, die in Templates stehen („Suchen“, „Kontakt“, Beschriftungen in der Fußzeile), sind Oberflächentexte: Sie
laufen durch `|i18n( 'design/<your design>' )` und werden in `.ts`-Dateien Ihrer Design-Erweiterung übersetzt. Texte,
die Redakteure schreiben (Menüs, als Objekte gepflegte Inhalte der Fußzeile), sind Inhalte und werden im Editor
übersetzt ([Eine mehrsprachige Website bauen](../../../../eng-us/help/creators/website-creators/multilingual-sites.md#templates-and-interface-texts)).
Wenn Sie möglichst viele feste Texte als Inhalte halten, können Redakteure sie ohne Entwickler übersetzen.

## Verwandte Seiten

- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md)
- [Sprachen (für Redakteure)](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
