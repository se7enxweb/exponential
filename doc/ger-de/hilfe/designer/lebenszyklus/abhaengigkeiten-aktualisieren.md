---
title: "Abhängigkeiten aktuell halten"
audience: designer, developer
category: theme-lifecycle
order: 210
summary: Die Bibliotheken, Schriften, Icon-Sätze und Erweiterungen, von denen ein Theme abhängt, aktuell halten - ein Bestandsverzeichnis, wo Updates angekündigt werden, ein Update testen und Lizenzen erneut prüfen.
keywords: Abhängigkeiten, Updates, Bibliotheken, Schriften, Icons, Erweiterungen, Bestandsverzeichnis, composer outdated, Lizenzprüfung, dependencies, libraries, fonts, icons, extensions, inventory, license check
type: help
---
# Abhängigkeiten aktuell halten

Diese Seite ist für Designer, die ein Theme betreuen, das andere Arbeit mitliefert oder verlangt.

## Das Bestandsverzeichnis

Führen Sie eine Liste (zum Beispiel in `THIRD-PARTY.md`) mit, für jede Abhängigkeit: Name, Version, Herkunft, Lizenz und
wie sie geladen wird.

| Art | Beispiel | Wie aktualisieren |
|---|---|---|
| Mitgelieferte Bibliothek | Ein Slider-Skript | Die Datei durch das neue Release ersetzen |
| Mitgelieferte Schrift oder Icon-Satz | Eine quelloffene Schrift | Die Dateien ersetzen; die Lizenz erneut prüfen |
| Bibliothek aus Exponential | jQuery über ezjscore | Kommt mit Exponential; Ihren Code anpassen ([JavaScript-Bibliotheken aktualisieren](javascript-bibliotheken-aktualisieren.md)) |
| Verlangte Erweiterung | Eine Layout-Engine | `composer outdated` auf der Testinstallation |

## Schritt für Schritt, in jedem Wartungszyklus

1. Führen Sie auf der Testinstallation `composer outdated` aus und lesen Sie die Liste nach den Abhängigkeiten Ihres
   Themes durch.
2. Prüfen Sie die Release Notes jeder mitgelieferten Bibliothek auf Sicherheitskorrekturen.
3. Aktualisieren Sie eine Abhängigkeit nach der anderen, testen Sie, committen Sie mit `Updated: <name> <version>`.
4. Prüfen Sie die Lizenz der neuen Version; Lizenzen ändern sich manchmal ([Schriften, Icons und Bibliotheken Dritter](../veroeffentlichung/schriften-icons-und-bibliotheken-dritter.md)).
5. Aktualisieren Sie das Bestandsverzeichnis.

## Entfernen Sie, was Sie nicht mehr verwenden

Eine Bibliothek, die niemand lädt, wird trotzdem mit jeder Installation ausgeliefert und kann bekannte Schwachstellen
tragen: Exponential hat das alte jQuery UI 1.10.3 genau aus diesem Grund entfernt. Entfernen Sie ungenutzte Dateien in
einem Release mit einem Eintrag `Removed:`.

## Verwandte Seiten

- [Wartungskalender](wartungskalender.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
