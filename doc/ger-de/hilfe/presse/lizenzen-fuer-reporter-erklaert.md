---
title: Lizenzen für Reporter erklärt
audience: press
category: press-media
order: 40
summary: Die Lizenzen rund um Exponential in einfachen Worten für Journalisten - die GNU GPL für die Software, die GNU FDL für die Dokumentation, MIT für Velocity, das gemeinsame Urheberrecht, was „kostenlos“ bedeutet, was 7x verkauft und die Formulierungen zu Lizenzen, die oft falsch berichtet werden.
keywords: Lizenz, Lizenzen, GPL, GNU General Public License, GFDL, GNU Free Documentation License, MIT, Open Source, freie Software, Urheberrecht, kostenlos, Professional License, proprietär, license, licenses, open source, free software, copyright, free of charge, professional license, proprietary
type: help
---
# Lizenzen für Reporter erklärt

Diese Seite ist für Journalisten, die erklären müssen, wie Exponential lizenziert ist. Sie gibt die Fakten aus dem
Repository und einfache Erklärungen. Sie ist keine Rechtsberatung; bei einer Rechtsfrage zu einer bestimmten Nutzung
sollten die Leser einen Anwalt fragen.

## Inhalt

1. [Die Lizenzen in einer Tabelle](#die-lizenzen-in-einer-tabelle)
2. [Die GPL in einfachen Worten](#die-gpl-in-einfachen-worten)
3. [Die GFDL in einfachen Worten](#die-gfdl-in-einfachen-worten)
4. [Wer das Urheberrecht hält](#wer-das-urheberrecht-hält)
5. [Kostenlos, und was verkauft wird](#kostenlos-und-was-verkauft-wird)
6. [Formulierungen, die oft falsch berichtet werden](#formulierungen-die-oft-falsch-berichtet-werden)

## Die Lizenzen in einer Tabelle

| Was | Lizenz | Quelle |
|---|---|---|
| Die Software (Exponential 6) | GNU General Public License, Version 2 oder jede spätere Version | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE); README, „License“ |
| Beiträge dazu | Dieselbe Lizenz: „alle am Projekt vorgenommenen Änderungen werden ebenfalls unter derselben Lizenz GPLv2 oder später veröffentlicht“ | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| Die Dokumentation, einschließlich dieser Hilfe | GNU Free Documentation License 1.3 oder jede spätere Version | [Die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md) |
| Exponential Velocity | MIT | seine `composer.json` |
| Exponential Platform DXP (Metapaket) | „GPL-2.0-or-later or proprietary“ | seine `composer.json` |
| Übernommene Dokumentation von eZ Systems | Die Bedingungen, mit denen sie kam (GPL v2) | [Ältere Seiten mit eigenen Bedingungen](../../../eng-us/README-LICENSE.md#older-pages-with-their-own-terms) |
| Jede Erweiterung | Ihre eigene Lizenz, angegeben in ihrem Paket | `composer.json` und `ezinfo.php` der Erweiterung |

## Die GPL in einfachen Worten

Jeder darf die Software für jeden Zweck nutzen, ihren Code untersuchen und ändern und Kopien weitergeben, auch
geänderte Kopien. Wer die Software oder eine geänderte Version verbreitet, muss dieselben Freiheiten und den Quellcode
weitergeben. Wer sie auf dem eigenen Server für die eigene Website betreibt, muss seine Änderungen nicht unter der
GPL v2 veröffentlichen. Diese Lizenz hat es 7x ermöglicht, den Code weiterzuführen, nachdem sein ursprünglicher
Entwickler aufgehört hatte ([Thema: Open Source und Lizenzen](thema-open-source-und-lizenzen.md)).

## Die GFDL in einfachen Worten

Jeder darf die Dokumentation kopieren, weiterverbreiten, übersetzen und bearbeiten, auch kommerziell, wenn die
Hinweise auf Urheberrecht und Lizenz erhalten bleiben und geänderte Fassungen unter derselben Lizenz stehen
([Die Dokumentation zitieren und wiederverwenden](die-dokumentation-zitieren-und-wiederverwenden.md)).

## Wer das Urheberrecht hält

„Copyright (C) 1998 - 2026 7x & Exponential Foundation“ und „Copyright (C) 1999 - 2014 eZ Systems AS“ für den
übernommenen Code ([COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md)). Beiträge werden
unter der GPL veröffentlicht, und wer zum Kernel beiträgt, überträgt das Urheberrecht an seinen Beiträgen an 7x
([Übertragung des Urheberrechts (CLA)](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md#copyright-assignment-cla)). Schreiben Sie „7x und die Exponential Foundation halten das Urheberrecht
gemeinsam“, nie „7x gehört Exponential“.

## Kostenlos, und was verkauft wird

- „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos. Die Professional License ist nicht mehr
  erhältlich“ (README, „License“).
- 7x verkauft Dienstleistungen rund um die Software: Entwicklung, Support, Hosting, Schulungen und Migrationen
  ([7x für Reporter](7x-fuer-reporter.md#was-es-verkauft-und-was-kostenlos-bleibt)).
- Das Partnerprogramm hat kostenpflichtige Stufen für Unternehmen ([Partnerpläne und Preise](../partner/partnertarife-und-preise.md)).
- Sponsoring finanziert die Arbeit ([Über Finanzierung und Sponsoren schreiben](ueber-finanzierung-und-sponsoren-schreiben.md)).

## Formulierungen, die oft falsch berichtet werden

| Falsch | Richtig |
|---|---|
| „Exponential ist Freeware.“ | Es ist freie Software (Open Source) unter der GPL; Freeware bedeutet meist kostenlos ohne die Freiheiten. |
| „Exponential ist gemeinfrei.“ | Es ist urheberrechtlich geschützt und unter der GPL lizenziert. |
| „Open Core: Die guten Funktionen kosten extra.“ | Es ist keine kostenpflichtige Ausgabe der Software dokumentiert; die Professional License „ist nicht mehr erhältlich“. |
| „Die Dokumentation steht unter Creative Commons.“ | Sie steht unter der GNU FDL 1.3 oder später. |
| „Velocity steht auch unter der GPL.“ | Velocity steht unter der MIT-Lizenz. |
| „Kostenlos heißt, niemand wird bezahlt.“ | Die Entwicklung wird von 7x und Sponsoren finanziert ([Zwei Organisationen, ein Projekt](zwei-organisationen-ein-projekt.md)). |

## Verwandte Seiten

- [Lizenzen für Käufer erklärt](../geschaeft/lizenzen-fuer-kaeufer.md)
- [Gesprächspunkte: Open Source und Lizenzen](../fuersprecher/gespraechspunkte-open-source-und-lizenzierung.md)
- [Die Dokumentation zitieren und wiederverwenden](die-dokumentation-zitieren-und-wiederverwenden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
