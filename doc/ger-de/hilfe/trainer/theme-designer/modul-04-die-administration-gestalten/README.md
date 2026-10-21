---
title: "Modul 4: Die Administration gestalten"
audience: trainer, designer, developer
category: training
order: 40
summary: Das vierte Modul von Theme-Design. Die Teilnehmenden lernen die Gestaltungsregeln der Administration, bauen einen Dashboard-Block aus den gemeinsamen Komponenten von admin4, gestalten ihn mit Tokens, die im Dunkelmodus funktionieren, berücksichtigen erzwungene Farben und Rechts-nach-links und prüfen ihre Arbeit gegenseitig mit der Checkliste. 4 Stunden 35 Minuten.
keywords: theme-design, modul 4, admin4, gestaltungsregeln der administration, components.css, token, dunkelmodus, BackendCSSFileList, erzwungene farben, logische eigenschaften, code-review-checkliste, AdminStylingRulesTest, theme design, module 4, administration styling rules, dark mode, forced colors, logical properties, code review checklist
type: help
---
# Modul 4: Die Administration gestalten

Die Administration wird von allen Erweiterungen und allen Redakteuren gemeinsam genutzt, deshalb wird sie nach
gemeinsamen Regeln gestaltet: kein Inline-CSS, zuerst die gemeinsamen Komponenten, Tokens für jeden Wert, ein dunkler
Wert für jedes Farb-Token. In diesem Modul fügen die Teilnehmenden aus ihrer eigenen Erweiterung dem Dashboard von
admin4 einen Block hinzu, bauen ihn aus den gemeinsamen Komponenten, geben ihm ein Stylesheet, das nur mit Tokens
geschrieben ist, machen seinen Zustand im Modus mit erzwungenen Farben sichtbar und schließen mit einem Peer-Review
anhand der Code-Review-Checkliste des Projekts ab.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Dauer | 4 Stunden 35 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 4.1: Die Gestaltungsregeln der Administration](die-gestaltungsregeln-der-administration.md) | 45 Minuten | Die sieben Regeln und ihre Ausnahmen lernen; die Regelverstöße in einem Beispiel-Template finden |
| [Lektion 4.2: Mit den gemeinsamen Komponenten bauen](gemeinsame-komponenten.md) | 60 Minuten | Aus der Erweiterung einen Dashboard-Block hinzufügen, gebaut aus `.a4-table`, `.a4-badge`, `.a4-empty` und `.a4-btn` |
| [Lektion 4.3: Tokens und Dunkelmodus](tokens-und-dunkelmodus.md) | 60 Minuten | Tokens im Browser lesen, `training_admin.css` hinzufügen, eine fest codierte Farbe mit Tokens beheben |
| [Lektion 4.4: Modul- und Erweiterungs-Stylesheets](modul-und-erweiterungs-stylesheets.md) | 45 Minuten | Einen Zustand für die aktuelle Zeile mit einer Kontur für erzwungene Farben hinzufügen, nur mit logischen Eigenschaften |
| [Lektion 4.5: Die Review-Checkliste und die Design-Tests](review-checkliste-und-design-tests.md) | 45 Minuten | Den Block eines Partners mit der Checkliste in zwei Größen und beiden Modi prüfen; den Design-Test ausführen |

## Dieses Modul vorbereiten

- Der Siteaccess der Administration jeder Installation der Teilnehmenden in `admin4` (oder `admin4l`); prüfen Sie es
  mit `php bin/php/console exp:ini where site/DesignSettings/SiteDesign admin`.
- Ausgedruckte oder projizierte Kopien des Beispiel-Templates aus Lektion 4.1 und der Code-Review-Checkliste aus
  [Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md#checkliste-für-das-code-review).
- Ein Chromium-basierter Browser auf jedem Computer der Teilnehmenden für die Emulation erzwungener Farben in
  Lektion 4.4.
- Für Lektion 4.5, Schritt 9: ein Entwicklungs-Checkout mit installierten Entwicklungsabhängigkeiten
  (`vendor/bin/phpunit`), auf dem Rechner des Trainers, wenn die Installationen der Teilnehmenden ihn nicht haben.
- Bilden Sie vor Beginn des Moduls Paare für die Lektionen 4.1 und 4.5.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 4.1 | 45 |
| Lektion 4.2 | 60 |
| Pause | 10 |
| Lektion 4.3 | 60 |
| Pause | 10 |
| Lektion 4.4 | 45 |
| Lektion 4.5 | 45 |
| **Gesamt** | **275** |

## Verwandte Seiten

- [Modul 3: Stylesheets und Skripte](../modul-03-stylesheets-und-skripte/README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
