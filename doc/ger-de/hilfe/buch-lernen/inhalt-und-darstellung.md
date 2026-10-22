---
title: "Inhalt und Darstellung"
audience: user, developer, designer, builder
category: books
order: 230
summary: Warum Inhalt, Logik und Darstellung getrennt gehalten werden, und die beiden Stellen, an denen eine Seite entsteht: das Seitenlayout und die Inhaltsansicht.
keywords: Trennung, Darstellung, Seitenlayout, Inhaltsansicht, Vollansicht, Template, Navigation, Buch Learning, separation, presentation, page layout, pagelayout, content view, full view, template, navigation, Learning book
type: help
---
# Inhalt und Darstellung

Eine der Grundeigenschaften des Systems ist die Trennung von **Inhalt**, der **Anwendungslogik**, die ihn verarbeitet,
und seiner **Darstellung** für einen Browser oder für andere Ausgaben (ein PDF, einen Feed, eine API). Dasselbe Objekt
kann als ganze Seite, als Zeile in einer Liste, als Teaser, als Feed-Eintrag oder als JSON gezeigt werden, jeweils durch
ein Template für diesen Zweck.

## Zwei Bereiche einer Seite

Eine Seite hat zwei Hauptteile:

- Das **Seitenlayout** (`pagelayout.tpl`): der Rahmen um alles. Menüs, Kopf, Fußzeile, Skripte und Stile liegen hier. In
  der Abbildung des Buchs sind das die mit P markierten Teile.
- Die **Inhaltsansicht**: der Teil für das Angeforderte (ein Artikel, eine Ordnerliste, ein Formular, ein Bildschirm
  der Administration). Inhaltsansichten kommen aus den Kernel-Modulen für die angeforderte Adresse. Das sind die mit C
  markierten Teile. Die Inhaltsansicht wird als `$module_result.content` in das Seitenlayout eingesetzt.

Beispiele für Inhaltsansichten: ein Artikel, ein Ordner mit seinen Unterelementen, ein Suchformular, das Bearbeiten
einer Rolle, die Ausgabe des Moduls einer Erweiterung.

## Struktur ist nicht Layout, aber beide können übereinstimmen

Der Baum ist vom Seitenlayout unabhängig. Doch ein gut geplanter Baum kann auch das Layout steuern: Die Knoten der
obersten Ebene können das Hauptmenü sein und ihre Unterelemente (meist Ordner) die Einträge eines Untermenüs. Templates
können überall dieselben Objekte auf dieselbe Weise lesen. Siehe [Menüs](../template-operatoren/menue-und-baum-operatoren.md).

## Wo Sie die Templates lernen

- [Templates für Builder](../erweiterungsentwicklung/teil-2-builder/templates-fuer-builder.md)
- [Ansichtsmodi](../erweiterungsentwicklung/teil-2-builder/view-modi-fuer-builder.md)
- [Die Design-Kette](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md)
- [Die Template-Sprache](../template-sprache/was-die-template-sprache-ist.md)

Weiter: [Rollen und Richtlinien, Schritt für Schritt](rollen-und-richtlinien-schritt-fuer-schritt.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
