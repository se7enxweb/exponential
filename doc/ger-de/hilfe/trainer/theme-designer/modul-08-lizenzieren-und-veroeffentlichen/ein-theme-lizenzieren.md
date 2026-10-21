---
title: "Lektion 8.1: Ein Theme lizenzieren"
audience: trainer, designer, developer
category: training
order: 81
summary: Die Teilnehmenden treffen die vier Lizenzentscheidungen eines Themes (Code, Dokumentation, eigene Bilder, Assets von Dritten), erstellen eine Aufstellung ihres eigenen Themes mit der Lizenz jedes Teils und tragen sie in LICENSE, die README und die Metadaten ein. 45 Minuten.
keywords: theme-design, lizenz, lizenzieren, GPL-2.0-or-later, GFDL 1.3, Creative Commons, MIT, schriften von dritten, icons, bibliotheken, datei LICENSE, SPDX, package.ini LicenseSettings, keine rechtsberatung, theme design, license, licensing, third-party fonts, libraries, not legal advice
type: help
---
# Lektion 8.1: Ein Theme lizenzieren

Ein Theme ohne klare Lizenz kann niemand sicher verwenden, und ein Theme, das die Schriften oder Icons anderer ohne
deren Lizenz kopiert, kann seine Benutzer in Schwierigkeiten bringen. Diese Lektion gibt den Teilnehmenden die vier
Entscheidungen, die jedes Theme braucht, und lässt sie diese auf die Dateien anwenden, die sie in diesem Kurs erstellt
haben.

> **Hinweis** Diese Lektion erklärt Lizenzentscheidungen so, wie die Dokumentation sie beschreibt. Sie ist keine
> Rechtsberatung; bei einem Vertrag oder Zweifeln an der Vereinbarkeit fragen Sie eine Anwältin oder einen Anwalt, die
> oder der sich mit Softwarelizenzen auskennt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 8: Lizenzieren und veröffentlichen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Themes veröffentlichen oder verkaufen |
| Voraussetzungen | [Modul 7](../modul-07-ein-theme-paketieren/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Lizenzen nennen, die Exponential selbst für seine Software, seine Dokumentation und seine Themes verwendet;
- die vier Lizenzentscheidungen für ein Theme treffen;
- jeden Teil ihres Themes mit seinem Urheber und seiner Lizenz auflisten;
- die Lizenz einheitlich in `LICENSE`, der README, `composer.json`, `ezinfo.php` und `extension.xml` angeben.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Lizenzentscheidungen: ein Überblick](../../../designer/veroeffentlichung/lizenzwahl-ueberblick.md) und die
dort verlinkten Seiten.

1. **Was Exponential verwendet** (4 Minuten). Die Software: GNU GPL Version 2 oder später (`GPL-2.0-or-later`). Die
   Dokumentation: GNU FDL 1.3 oder später. Die veröffentlichten 7x-Themes: `GPL-2.0-or-later`.
2. **Die vier Entscheidungen** (8 Minuten).

   | Teil | Übliche Wahl | Seite |
   |---|---|---|
   | Templates, CSS, JavaScript, PHP | `GPL-2.0-or-later` | [Die GPL für Theme-Code](../../../designer/veroeffentlichung/gpl-fuer-theme-code.md) |
   | Die Dokumentation des Themes | GFDL 1.3 oder eine Creative-Commons-Lizenz | [Die GFDL für die Dokumentation eines Themes](../../../designer/veroeffentlichung/gfdl-fuer-theme-dokumentation.md) |
   | Eigene Bilder und Grafiken | Eine Creative-Commons-Lizenz | [Creative Commons für Bilder](../../../designer/veroeffentlichung/creative-commons-fuer-bilder.md) |
   | Schriften, Icons und Bibliotheken anderer | Was ihre Urheber gewählt haben; Sie müssen sich daran halten | [Schriften, Icons und Bibliotheken von Dritten](../../../designer/veroeffentlichung/schriften-icons-und-bibliotheken-dritter.md) |

3. **Was die Assistenten anbieten** (3 Minuten). Der Assistent für Design-Erweiterungen und der Paket-Assistent listen
   die Lizenzen aus `package.ini [LicenseSettings]` auf: die Familien GPL, LGPL und AGPL, MIT, die GFDL,
   Creative-Commons-Lizenzen und weitere.
4. **Wo sie steht** (5 Minuten). `LICENSE` für den Code; eigene Lizenzdateien neben Assets unter anderen Bedingungen;
   jede Lizenz in der README mit dem, was sie abdeckt; die Lizenz des Codes in `composer.json` (SPDX), `ezinfo.php` und
   `extension.xml`.

### Häufige Fragen

**Darf ich ein GPL-Theme verkaufen?** Ja; die GPL erlaubt den Verkauf, unter Bedingungen. Siehe
[Die GPL und der Verkauf](../../../designer/verkauf/gpl-und-verkauf.md) und
[Kommerzielle Bedingungen neben der GPL](../../../designer/veroeffentlichung/kommerzielle-bedingungen-neben-der-gpl.md).

**Die Icons von admin4 stehen unter MIT. Liefert mein Theme sie mit?** Nein: Sie gehören zur Installation
(`share/icons/bootstrap-icons/`, mit ihrer Lizenz daneben). Ihr Theme liefert nur, was in seinem eigenen Ordner liegt.

**Darf ich eine Schrift von einem Schriftendienst verwenden?** Nur unter ihrer Lizenz und unter Beachtung der
Datenschutzfrage, wenn sie von einem anderen Host geladen wird; liefern Sie eine Kopie mit, wenn die Lizenz es erlaubt.

### Stolperfallen

- **Dateien aus einem mitgelieferten Theme oder Iconsatz kopieren, ohne ihre Lizenz zu prüfen.** Die Icon-Themes
  crystal zum Beispiel haben eine eigene Datei `COPYING`.
- **Eine Lizenz in `composer.json`, die von `LICENSE` abweicht.** Prüfen Sie alle fünf Stellen.
- **„Kostenlos herunterladbar“ mit „frei wiederverwendbar“ verwechseln.** Lesen Sie die Lizenz, nicht die
  Download-Seite.

## Praktische Übung

Auf Ihrer Schulungsinstallation, in `extension/training_theme/`. Arbeiten Sie allein und vergleichen Sie dann mit
Ihrer Sitznachbarin oder Ihrem Sitznachbarn.

1. Listen Sie die Dateien des Themes auf: `find . -type f -not -path './.git/*' | sort`.
2. Legen Sie eine Tabelle mit den Spalten **Datei oder Ordner**, **Urheber**, **Lizenz** und **Wo die Lizenz steht**
   an.
3. Füllen Sie eine Zeile für die Templates, die Stylesheets, das Skript, `ezinfo.php` und `extension.xml` aus (Ihr
   eigener Code).
4. Füllen Sie eine Zeile für die SVG-Fahne aus, die Sie in Lektion 6.2 gezeichnet haben, und eine für die PNG-Icons
   aus Lektion 6.1. Wenn Sie die PNGs aus `share/icons/crystal-admin/` kopiert haben, lesen Sie
   `share/icons/crystal-admin/COPYING` und notieren Sie, was dort über die Wiederverwendung steht.
5. Füllen Sie eine Zeile für die README aus (Dokumentation).
6. Legen Sie die Lizenz jeder Zeile anhand der vier Entscheidungen fest. Wo ein Teil unter einer anderen Lizenz steht
   als der Code, notieren Sie den Namen der eigenen Lizenzdatei, die Sie daneben ergänzen würden.
7. Prüfen Sie die fünf Stellen für die Lizenz des Codes: `LICENSE`, README, `composer.json`, `ezinfo.php`,
   `extension.xml`. Korrigieren Sie jede, die abweicht.
8. Ergänzen Sie in der README einen Abschnitt „Lizenzen“, der jede Lizenz und das, was sie abdeckt, aufführt.

## Erwartetes Ergebnis

- Jede Datei des Themes hat in der Tabelle einen Urheber und eine Lizenz; nichts ist „unbekannt“.
- Kopierte Icons sind entweder durch eigene ersetzt oder mit ihrer Lizenz und deren Datei aufgeführt.
- Die fünf Stellen nennen dieselbe Lizenz für den Code; die README führt jede Lizenz auf.
- Der Trainer prüft die Tabellen und den README-Abschnitt von zwei Teilnehmenden.

## Quiz

1. Unter welcher Lizenz wird die Software Exponential veröffentlicht?
   A. MIT B. `GPL-2.0-or-later` C. GFDL 1.3 D. CC BY 4.0
2. Nennen Sie die vier Lizenzentscheidungen eines Themes.
3. Unter welcher Lizenz wird diese Dokumentation veröffentlicht?
4. Ihr Theme verwendet eine Schrift einer anderen Designerin. Was entscheidet, ob Sie sie mitliefern dürfen?
5. Nennen Sie die fünf Stellen, die die Lizenz des Codes eines Themes angeben müssen.

<details>
<summary>Antworten</summary>

1. **B.** ([Lizenzentscheidungen](../../../designer/veroeffentlichung/lizenzwahl-ueberblick.md#was-exponential-verwendet))
2. Der Code, die Dokumentation, die eigenen Bilder sowie Schriften, Icons und Bibliotheken von Dritten.
3. Die GNU Free Documentation License 1.3 oder später.
4. Die Lizenz der Schrift, die ihre Urheberin gewählt hat; Sie müssen sich daran halten.
5. `LICENSE`, die README, `composer.json`, `ezinfo.php` und `extension.xml`.

</details>

## Weiterführende Literatur

- [Lizenzentscheidungen: ein Überblick](../../../designer/veroeffentlichung/lizenzwahl-ueberblick.md).
- [Assets unter eigenen Lizenzen](../../../designer/verkauf/bilder-und-schriften-unter-eigenen-lizenzen.md).
- [Urheberrechtshinweise in Dateien](../../../designer/veroeffentlichung/copyright-hinweise-in-dateien.md) und
  [Copyright- und Autorenzeilen](../../../designer/erweiterungen/copyright-und-autorenzeilen.md).
- [Eine Lizenz im Paket-Assistenten wählen](../../../designer/erweiterungen/paketlizenzen-im-assistenten.md).

## Verwandte Seiten

- [Modul 8: Lizenzieren und veröffentlichen](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
