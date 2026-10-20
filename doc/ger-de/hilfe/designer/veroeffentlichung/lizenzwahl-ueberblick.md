---
title: "Lizenzwahl: ein Überblick"
audience: designer, sales-marketing
category: theme-distribution
order: 240
summary: Die Lizenzentscheidungen eines Theme-Autors - Code, Dokumentation, Bilder und Bestandteile Dritter -, die Lizenzen, die Exponential und seine veröffentlichten Themes verwenden, und wo jede Wahl erklärt ist. Keine Rechtsberatung.
keywords: Lizenz, Lizenzierung, GPL, GFDL, Creative Commons, MIT, proprietär, Theme-Lizenz, Dritte, Vereinbarkeit, license, licensing, proprietary, theme license, third-party, compatibility
type: help
---
# Lizenzwahl: ein Überblick

Diese Seite ist für Designer, die entscheiden, zu welchen Bedingungen andere ihr Theme nutzen dürfen. Sie beschreibt die
Optionen und die Entscheidungen, die das Projekt Exponential getroffen hat. **Sie ist keine Rechtsberatung**: Fragen Sie
bei einem Vertrag, einer kommerziellen Lizenz oder einem Zweifel an der Vereinbarkeit einen Anwalt, der sich mit
Softwarelizenzen auskennt.

## Was Exponential verwendet

| Teil | Lizenz | Wo das steht |
|---|---|---|
| Die Software | GNU General Public License, Version 2 oder später (`GPL-2.0-or-later`) | die `composer.json` im Wurzelverzeichnis |
| Die Dokumentation (`doc/`) | GNU Free Documentation License 1.3 oder später | [Die Lizenz dieser Dokumentation](../../../../eng-us/README-LICENSE.md) |
| Die Themes von 7x (`sevenx_themes_simple`, `sevenx_themes_media`, `cjw_themes_jumper`) | `GPL-2.0-or-later` | `composer.json` und `LICENSE` jedes Themes |

## Die vier Entscheidungen

| Entscheidung | Übliche Wahl | Seite |
|---|---|---|
| Templates, CSS, JavaScript, PHP des Themes | `GPL-2.0-or-later` | [Die GPL für Theme-Code](gpl-fuer-theme-code.md) |
| Die Dokumentation des Themes | GFDL 1.3 oder eine Creative-Commons-Lizenz | [Die GFDL für Theme-Dokumentation](gfdl-fuer-theme-dokumentation.md) |
| Ihre eigenen Bilder und Grafiken | Eine Creative-Commons-Lizenz | [Creative Commons für Bilder](creative-commons-fuer-bilder.md) |
| Schriften, Icons und Bibliotheken anderer | Was ihre Urheber gewählt haben; Sie müssen sich daran halten | [Schriften, Icons und Bibliotheken Dritter](schriften-icons-und-bibliotheken-dritter.md) |

Und wenn Sie Geld verdienen wollen: [Kommerzielle Bedingungen neben der GPL](kommerzielle-bedingungen-neben-der-gpl.md) und
[Die GPL und der Verkauf](../../../../eng-us/help/designers/sales/gpl-and-selling.md).

## Die Lizenzen, die die Assistenten anbieten

Die Paket- und RAD-Assistenten bieten die Lizenzen an, die in `package.ini [LicenseSettings]` eingerichtet sind: die
Familien GPL, LGPL und AGPL, MIT, die GFDL, Creative Commons 1.0 bis 4.0, CC0, die Public Domain Mark und
`LicenseRef-Proprietary` ([Eine Lizenz im Paket-Assistenten wählen](../erweiterungen/paketlizenzen-im-assistenten.md)).

## Schritt für Schritt

1. Entscheiden Sie jede der vier Zeilen oben.
2. Legen Sie die Lizenztexte in das Theme: `LICENSE` für den Code, eigene Dateien neben Assets unter anderen
   Bedingungen ([Die LICENSE-Datei eines Themes](../erweiterungen/die-license-datei.md)).
3. Führen Sie jede Lizenz im README mit dem auf, was sie abdeckt.
4. Nennen Sie die Lizenz des Codes in `composer.json`, `ezinfo.php` und `extension.xml`.

## Verwandte Seiten

- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
