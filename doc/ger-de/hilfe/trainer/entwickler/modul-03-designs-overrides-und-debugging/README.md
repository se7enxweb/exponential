---
title: "Modul 3: Designs, Overrides und Fehlersuche"
audience: trainer, developer
category: training
order: 30
summary: Das dritte Modul des Kurses Entwickler. Die Teilnehmenden verfolgen die Design-Kette, schreiben Override-Regeln von Hand und im Template-Editor, führen jeden Teil einer Seite auf sein Template zurück und leeren genau den Cache, den eine Änderung braucht. 4 Stunden 15 Minuten.
keywords: entwickler, modul 3, design-kette, override.ini, template-editor, template-pfadkommentare, ShowUsedTemplates, eztemplatecheck, cache-block, view-cache, ezcache, fehlersuche, developers course, module 3, design chain, Template Editor, template path comments, view cache, debugging
type: help
---
# Modul 3: Designs, Overrides und Fehlersuche

Modul 2 hat die Sprache vermittelt; dieses Modul bringt Templates dorthin, wo eine Website sie verwendet. Die
Teilnehmenden geben ihrer Erweiterung einen Design-Ordner, ersetzen ein Template für eine Klasse und für ein Objekt,
lernen, die Datei hinter jedem Teil einer Seite zu finden, und lernen die Caches kennen, die entscheiden, ob eine
Änderung sichtbar wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 4 Stunden 15 Minuten (mit zwei Pausen von je 10 Minuten) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 3.1: Die Design-Kette](die-design-kette.md) | 45 Minuten | Die Kette mit `exp:ini where` lesen, den Design-Ordner der Erweiterung hinzufügen |
| [Lektion 3.2: Override-Regeln in override.ini](override-regeln.md) | 60 Minuten | Einen Override für eine Klasse in der Erweiterung schreiben, die Caches leeren |
| [Lektion 3.3: Overrides im Template-Editor](overrides-im-template-editor.md) | 40 Minuten | Unter **Design** > **Template-Editor** einen Override für ein Objekt anlegen, umordnen und entfernen |
| [Lektion 3.4: Das Template hinter einer Seite finden](das-template-hinter-einer-seite-finden.md) | 45 Minuten | Pfadkommentare, verwendete Templates, `exp:ini where`, `eztemplatecheck.php` |
| [Lektion 3.5: Caches für Template-Entwickler](caches-fuer-template-entwickler.md) | 45 Minuten | Kompilierte Templates, View-Cache, `{cache-block}`, Leeren nach Tag und ID |

## Dieses Modul vorbereiten

- Die Installation jeder Person hat `extension/mysite_hello` aus Modul 1 und mindestens zwei veröffentlichte Artikel
  und einen Ordner im öffentlichen Siteaccess.
- Schreiben Sie den Namen des öffentlichen Siteaccess und sein Website-Design an die Tafel (`exp:ini where
  site.ini/DesignSettings/SiteDesign <sa>`).
- Lektion 3.3 braucht ein Administratorkonto auf jeder Installation und das Design **admin4** für die Administration;
  geben Sie auf einer gemeinsamen Installation allen Teilnehmenden einen anderen Artikel, auf den ihr Override passt.
- Proben Sie Lektion 3.5 einmal: Ob eine Template-Änderung schon vor dem Leeren des Inhalts-Caches erscheint, hängt
  vom Zustand des View-Caches der Seite ab.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 3.1 | 45 |
| Lektion 3.2 | 60 |
| Pause | 10 |
| Lektion 3.3 | 40 |
| Lektion 3.4 | 45 |
| Pause | 10 |
| Lektion 3.5 | 45 |
| **Gesamt** | **255** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
