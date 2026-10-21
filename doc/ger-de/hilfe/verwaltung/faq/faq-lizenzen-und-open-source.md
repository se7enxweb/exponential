---
title: "FAQ: Lizenzierung und Open Source"
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 60
summary: Antworten auf die Lizenzfragen öffentlicher Stellen zu Exponential - die GPL, Lizenzgebühren, das Eigentum an eigens entwickeltem Code, Weitergabe an andere Behörden, Veröffentlichung eigener Erweiterungen, die Lizenz der Dokumentation und der Quellcode -, auf Grundlage der FAQ zur Lizenzierung.
keywords: FAQ Lizenzierung, GPL, GPL-2.0-or-later, Open Source, Lizenzgebühr, Quellcode, eigens entwickelter Code, Eigentum, Nachnutzung zwischen Behörden, GFDL, Dokumentationslizenz, öffentlicher Sektor, licensing FAQ, open source, license fee, source code, custom code, ownership, reuse between agencies, documentation license, public sector
type: help
---
# FAQ: Lizenzierung und Open Source

Diese Seite beantwortet die Lizenzfragen öffentlicher Stellen zu Exponential. Sie fasst
[Fragen zur Lizenzierung: die GPL und Exponential](../../projekt/lizenz-faq.md) zusammen, die der eigenen
Lizenz-FAQ des Projekts folgt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie Ihren
> eigenen Fall mit Ihrer Rechtsberatung.

## Inhalt

1. [Unter welcher Lizenz steht Exponential?](#unter-welcher-lizenz-steht-exponential)
2. [Gibt es eine Lizenzgebühr?](#gibt-es-eine-lizenzgebühr)
3. [Ist es Open Source nach der üblichen Definition?](#ist-es-open-source-nach-der-üblichen-definition)
4. [Wo ist der Quellcode?](#wo-ist-der-quellcode)
5. [Wem gehört der Code, den unser Dienstleister eigens entwickelt?](#wem-gehört-der-code-den-unser-dienstleister-eigens-entwickelt)
6. [Müssen wir den Code unserer Website veröffentlichen?](#müssen-wir-den-code-unserer-website-veröffentlichen)
7. [Können wir unsere Erweiterungen mit anderen öffentlichen Stellen teilen?](#können-wir-unsere-erweiterungen-mit-anderen-öffentlichen-stellen-teilen)
8. [Welche Lizenz hat die Dokumentation?](#welche-lizenz-hat-die-dokumentation)

## Unter welcher Lizenz steht Exponential?

Unter der GNU General Public License, Version 2 oder jeder späteren Version (`GPL-2.0-or-later` in `composer.json`)
([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md#unter-welcher-lizenz-steht-exponential);
[GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)).

## Gibt es eine Lizenzgebühr?

Nein. Die GPL erlaubt jede Nutzung, auch die kommerzielle und öffentliche, ohne Gebühr. Sie bezahlen für Leistungen:
Umsetzung, Hosting, Support, Schulung
([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md#darf-ich-exponential-für-kommerzielle-arbeit-nutzen)).

## Ist es Open Source nach der üblichen Definition?

Die GPL ist eine Lizenz für freie Software, die die [Open Source Definition](https://opensource.org/osd) erfüllt. Sie
dürfen den Code ausführen, untersuchen, ändern und weitergeben.

## Wo ist der Quellcode?

Auf GitHub, im Repository `se7enxweb/exponential` und in den Repositorys der Erweiterungen derselben Organisation, sowie
als Composer-Pakete auf Packagist ([3. Den Code beziehen](../../../installation/03-den-code-beschaffen.md)). Jede Installation
enthält den Quellcode.

## Wem gehört der Code, den unser Dienstleister eigens entwickelt?

Das Urheberrecht folgt Ihrem Vertrag. Die Lizenz-FAQ ergänzt: Erweiterungen und Designs sind abgeleitete Werke, deshalb
werden sie, **wenn sie verbreitet werden**, unter der GPL Version 2 oder später verbreitet, und der Empfänger erhält den
Quellcode. Eine Agentur, die für einen Kunden eine Website baut, handelt als Beauftragte des Kunden, was das Projekt
nicht als Verbreitung ansieht ([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md#agenturen-und-kunden)). Schreiben
Sie in den Vertrag, dass Sie den gesamten Quellcode erhalten und ihn nutzen, ändern und weitergeben dürfen.

## Müssen wir den Code unserer Website veröffentlichen?

Nein. Eine Website zu betreiben ist keine Verbreitung; Sie müssen den Code weder Besuchern noch sonst jemandem geben
([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md#muss-ich-den-code-meiner-website-an-ihre-besucher-herausgeben)).

## Können wir unsere Erweiterungen mit anderen öffentlichen Stellen teilen?

Ja. Sie einer anderen Stelle zu geben ist Verbreitung, geschieht also unter der GPL, und genau das macht das Teilen
einfach: Die andere Stelle darf sie nutzen und ändern. Wenn Sie sie dem Projekt zurückgeben, können alle sie pflegen
([Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)).

## Welche Lizenz hat die Dokumentation?

Die GNU Free Documentation License 1.3 oder später. Sie dürfen diese Hilfe kopieren, anpassen, übersetzen und neu
veröffentlichen, auch in Ihren eigenen Handbüchern, wenn Sie die Hinweise beibehalten
([Freie Dokumentation](../../freie-dokumentation.md), [Die Lizenz dieser Dokumentation](../../../README-LIZENZ.md)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Fragen zur Lizenzierung: die GPL und Exponential](../../projekt/lizenz-faq.md)
- [Lizenzierung für Partner](../../partner/lizenzierung-fuer-partner.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
