---
title: Minderheiten- und Regionalsprachen
audience: government, project-manager, admin, editor
category: public-sector
order: 130
summary: Wie eine öffentliche Stelle mit Exponential Inhalte in Minderheiten-, Regional- oder indigenen Sprachen anbietet - welche Locales mitgeliefert werden, was zu tun ist, wenn eine fehlt, teilweise Übersetzung mit Ersatzsprache, Oberflächentexte, die in einer anderen Sprache bleiben, und die Zusammenarbeit mit Sprechern.
keywords: Minderheitensprachen, Regionalsprachen, indigene Sprachen, Walisisch, Baskisch, Irisch, Schottisch-Gälisch, Samisch, Maori, Friesisch, Sorbisch, Locale, teilweise Übersetzung, Übersetzer aus der Sprachgemeinschaft, minority languages, regional languages, indigenous languages, Welsh, Basque, Irish, Scottish Gaelic, Sami, Frisian, locale, partial translation, community translators
type: help
---
# Minderheiten- und Regionalsprachen

Diese Seite richtet sich an öffentliche Stellen, die in Minderheiten-, Regional- oder indigenen Sprachen
veröffentlichen, oft neben einer oder mehreren Mehrheitssprachen. Exponential behandelt jede Sprache gleich: Jede
Sprache mit einer Locale-Datei kann eine Inhaltssprache sein. Die Frage ist meist weniger die Software als die
Kapazität für Übersetzungen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Ist die Locale vorhanden?](#ist-die-locale-vorhanden)
2. [Teilweise Übersetzung mit Ersatzsprache](#teilweise-übersetzung-mit-ersatzsprache)
3. [Oberflächentexte](#oberflächentexte)
4. [Schriften und Zeichen](#schriften-und-zeichen)
5. [Zusammenarbeit mit Sprechern](#zusammenarbeit-mit-sprechern)

## Ist die Locale vorhanden?

`share/locale/` liefert 359 Locale-Dateien mit, darunter viele Regional- und Minderheitensprachen, zum Beispiel
Walisisch (`cym-GB`), Baskisch (`eus-ES`), Galicisch (`glg-ES`), Irisch (`gle-IE`), Schottisch-Gälisch (`gla-GB`),
Bretonisch (`bre-FR`), Okzitanisch (`oci-FR`), Westfriesisch (`fry-NL`), Nordsamisch (`sme-NO`), Rätoromanisch
(`roh-CH`), Luxemburgisch (`ltz-LU`), Maori (`mri-NZ`), Hawaiisch (`haw-US`) und Grönländisch (`kal-GL`). Die
vollständige Liste mit den Namen in der jeweiligen Sprache steht in
[Sprachen in Exponential, Abschnitt 3](../../../sprachen.md#3-alle-locales); noch fehlende Sprachen stehen in
[Abschnitt 6](../../../sprachen.md#6-verbreitete-sprachen-die-noch-fehlen). Eine fehlende Locale lässt sich als Datei
hinzufügen (siehe „Eigene Locales für nicht gelistete Sprachen“).

## Teilweise Übersetzung mit Ersatzsprache

Die meisten Stellen veröffentlichen einen Teil ihrer Inhalte in einer Minderheitensprache. Ein Siteaccess, der die
Minderheitensprache zuerst und die Hauptsprache an zweiter Stelle aufführt, zeigt übersetzte Seiten in der
Minderheitensprache und den Rest in der Hauptsprache:

```ini
[RegionalSettings]
SiteLanguageList[]
SiteLanguageList[]=cym-GB
SiteLanguageList[]=eng-GB
```

Kennzeichnen Sie Seiten in der Ersatzsprache für Besucher (siehe „Ersatzregeln für unübersetzte Inhalte“) und legen
Sie fest, welche Bereiche zuerst übersetzt werden: Kontakt, die meistgenutzten Leistungen, Rechte und Beschwerden.

## Oberflächentexte

Für die meisten Minderheitensprachen gibt es keine Übersetzung der Administration; die Oberfläche bleibt in einer
anderen Sprache, während Inhalte in der Minderheitensprache geschrieben werden können und Datumsangaben ihrer Locale
folgen ([Sprachen in Exponential, Abschnitt 2](../../../sprachen.md#2-unterstützungsstufen)). Die festen Texte Ihres
öffentlichen Designs sind Ihre eigenen `.ts`-Übersetzungen, die ein Übersetzer für die wenigen Texte einer
öffentlichen Website liefern kann.

## Schriften und Zeichen

Prüfen Sie, ob die Schriften des Designs jeden Buchstaben der Sprache enthalten (zum Beispiel Buchstaben mit Akzenten
und Sonderbuchstaben). Zeichen oberhalb von U+FFFF brauchen unter MySQL und MariaDB `utf8mb4`
([Standards zur Internationalisierung](../../../standards/internationalisierung.md#4-byte-zeichen-unter-mysql-und-mariadb)).

## Zusammenarbeit mit Sprechern

- Geben Sie Übersetzern aus der Sprachgemeinschaft oder beauftragten Übersetzern Konten mit einer auf ihre Sprache
  beschränkten Rolle (siehe „Rollen und Rechte für Übersetzer“) und bei Bedarf einen Freigabeschritt.
- Führen Sie ein Glossar der vereinbarten Begriffe (siehe „Begriffsglossare“).
- Bitten Sie vor dem Start eine Sprecherin oder einen Sprecher, die Tages- und Monatsnamen und die Formate der
  Locale-Datei durchzusehen.
- Geben Sie durchgesehene Locale-Dateien und Übersetzungen an das Projekt zurück (siehe „Beiträge als öffentliche
  Stelle“ im Bereich Souveränität).

## Verwandte Seiten

- [Sprachen in Exponential](../../../sprachen.md)
- [Eigene Locales für nicht gelistete Sprachen](eigene-locales-fuer-nicht-gelistete-sprachen.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
