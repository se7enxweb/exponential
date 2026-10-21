---
title: "Modul 3: Stylesheets und Skripte"
audience: trainer, designer, developer
category: training
order: 30
summary: Das dritte Modul von Theme-Design. Die Teilnehmenden führen ihre Stylesheets und Skripte in design.ini auf, sehen, was der ezjscore-Packer damit macht, lernen die Routine nach einer Änderung und machen ihr Theme bereit für eine Content Security Policy. 2 Stunden 55 Minuten.
keywords: theme-design, modul 3, stylesheets, skripte, design.ini, FrontendCSSFileList, ezcss_load, ezscript_load, ezjscore-packer, cache leeren, hartes neuladen, Content Security Policy, theme design, module 3, scripts, ezjscore packer, cache clear, hard reload
type: help
---
# Modul 3: Stylesheets und Skripte

Das Aussehen eines Themes steckt in seinen Stylesheets, sein Verhalten in seinen Skripten. In Exponential werden beide
in `design.ini` benannt und vom ezjscore-Packer ausgeliefert, nie von Hand verlinkt. In diesem Modul fügen die
Teilnehmenden `training_theme` auf die Exponential-Art Dateien hinzu, vergleichen gepackte und ungepackte Seiten,
lernen die drei Caches kennen, die sich an ein altes Stylesheet erinnern, und beseitigen jeden Grund für Inline-Code,
damit ihr Theme unter der mitgelieferten Content Security Policy laufen kann.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Dauer | 2 Stunden 55 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Einsteiger bis Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 3.1: Stylesheets und Skripte in design.ini aufführen](dateien-in-design-ini-auflisten.md) | 45 Minuten | `training.css` zur Frontend-Liste hinzufügen, `article.css` aus einem Template anfordern |
| [Lektion 3.2: Der ezjscore-Packer](der-ezjscore-packer.md) | 45 Minuten | Den Packer für einen Siteaccess aus- und einschalten, eine gepackte Datei finden und ihren Namen lesen |
| [Lektion 3.3: Nach einer Änderung am Stylesheet](nach-einer-stylesheet-aenderung.md) | 30 Minuten | Die Routine zum Leeren ausführen, hart neu laden, die gepackten Namen vergleichen |
| [Lektion 3.4: Ein Theme, bereit für eine Content Security Policy](bereit-fuer-eine-content-security-policy.md) | 45 Minuten | Einen Verstoß im Nur-Bericht-Modus lesen, Inline-Code durch Klassen, Data-Attribute und eine Skriptdatei ersetzen |

## Dieses Modul vorbereiten

- Das `training_theme` der Teilnehmenden aus Modul 2 funktioniert auf ihrem öffentlichen Siteaccess, mit Sprunglink
  und Breadcrumb aus Lektion 2.2.
- Ein Browser mit Entwicklerwerkzeugen (Konsole und Netzwerkanalyse) auf jedem Computer der Teilnehmenden; zeigen Sie
  vor Lektion 3.4, wo sie zu finden sind.
- Prüfen Sie auf einer Kopie der Übungsumgebung, dass `Packer` in `extension/ezjscore/settings/ezjscore.ini`
  auskommentiert ist und `DevelopmentMode` auf `disabled` steht, sodass der Packer zu Beginn von Lektion 3.2
  eingeschaltet ist.
- Wenn die Übungsumgebung auf Velocity läuft, schreiben Sie für Lektion 3.3
  `php bin/php/console exp:velocity cache clear` an die Tafel.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 3.1 | 45 |
| Lektion 3.2 | 45 |
| Pause | 10 |
| Lektion 3.3 | 30 |
| Lektion 3.4 | 45 |
| **Gesamt** | **175** |

## Verwandte Seiten

- [Modul 2: Templates, Overrides und das Pagelayout](../modul-02-templates-overrides-und-das-pagelayout/README.md)
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
