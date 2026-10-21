---
title: "Thema: jQuery 4 und das Ende von YUI"
audience: press
category: press-media
order: 86
summary: Ein Themenbeitrag darüber, wie die Administration von Exponential im Oktober 2026 auf jQuery 4 umgestiegen ist und die eingestellte Bibliothek YUI durch eigene Komponenten von Exponential UI ersetzt hat - was sich geändert hat, warum es für Sicherheit und Wartung wichtig ist, und die Aussagen, die Sie vermeiden sollten.
keywords: Thema, jQuery 4, YUI, JavaScript, Exponential UI, expui, Modernisierung, technische Schulden, Sicherheit, Administration, ezjscore, story, modernization, technical debt, security, administration
type: help
---
# Thema: jQuery 4 und das Ende von YUI

Diese Seite ist für technische Journalisten, die über die Wartung langlebiger Software berichten. Eine alte
JavaScript-Bibliothek in einer großen Administration zu ersetzen, ist unspektakuläre Arbeit und ein gutes Beispiel
dafür, wie eine Codebasis aus den 2000er-Jahren aktuell gehalten wird.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Die Administration von Exponential nutzte YUI, eine JavaScript-Bibliothek aus den 2000er-Jahren, für Tabellen, Dialoge,
Uploads und Datumsfelder. Am 1. und 2. Oktober 2026 stieg die Administration auf jQuery 4 um, und Exponential UI
(Komponenten `exp::*`) ersetzte YUI in der Tabelle der Unterelemente, den Upload-Dialogen, den Datumsfeldern, den
einklappbaren Menüs, der mitlaufenden Bearbeitungsleiste, dem automatischen Speichern und der Vorschau. YUI wurde aus
den Designs der Administration und aus dem mitgelieferten ezjscore entfernt; „Die Administration lädt keine
YUI-Anfrage mehr“ ([jQuery 4 in der Administration, und YUI ist weg](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md)).
Ältere Dateien von jQuery und jQuery UI wurden am 14. Oktober 2026 entfernt; die Seite vermerkt, dass jQuery UI 1.10.3
„bekannte Sicherheitslücken hat“.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| jQuery 3.7.1 in der Administration | August 2026 | [Wie es begann](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md#wie-es-begann-jquery-371-im-august) |
| jQuery 4, Exponential UI, YUI entfernt | 1. und 2. Oktober 2026 | [jQuery 4 in der Administration, und YUI ist weg](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md) |
| Alte Dateien von jQuery und jQuery UI entfernt | 14. Oktober 2026 | [Was Sie bekommen](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md#was-sie-bekommen) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| jQuery 4.0.0, jQuery Migrate 4.0.2, jQuery UI 1.14.2 | [Was Sie bekommen](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md#was-sie-bekommen) |
| YUI aus den Designs admin, admin3, admin4 und standard entfernt | ebenda |
| Komponenten von Exponential UI ersetzen YUI-Widgets | ebenda |
| Eine Prüfung in zwei Minuten für Administratoren ist dokumentiert | [Eine Installation in zwei Minuten prüfen](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md#eine-installation-in-zwei-minuten-prüfen) |
| Für Websites und Erweiterungen, die noch YUI nutzen, gibt es Upgrade-Schritte | [Entfernung von YUI](../../bc/6.0/yui-entfernung.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Von Grund auf neu geschrieben“ | Die Bibliotheken wurden ersetzt; die Templates der Administration bleiben |
| „Keine JavaScript-Sicherheitslücken mehr“ | Alte Dateien mit bekannten Sicherheitslücken wurden entfernt |
| „Jede Erweiterung funktioniert unverändert“ | Erweiterungen, die YUI nutzten, brauchen die dokumentierten Upgrade-Schritte |

## Blickwinkel und Ideen für Überschriften

- „Zwanzig Jahre technische Schulden abbauen, ein Widget nach dem anderen.“
- „Warum ein altes CMS YUI aufgegeben hat.“

## Verwandte Seiten

- [Thema: admin4, das Design der Administration](thema-admin4.md)
- [Thema: eine lebendig gehaltene Codebasis](thema-eine-lebendig-gehaltene-codebasis.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
