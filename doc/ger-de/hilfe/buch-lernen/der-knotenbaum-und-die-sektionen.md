---
title: "Der Knotenbaum und die Sektionen"
audience: user, editor, developer, builder
category: books
order: 210
summary: Inhaltsobjekte und die Knoten, die sie im Baum platzieren, wie ein Zweig seine Sektion erbt und wofür Sektionen verwendet werden (Berechtigungen und Darstellung).
keywords: Knoten, Knotenbaum, Inhaltsobjekt, Ort, Sektion, Vererbung, Hauptknoten, Baum, Struktur, Buch Learning, node, node tree, content object, location, section, inheritance, main node, tree, structure, Learning book
type: help
---
# Der Knotenbaum und die Sektionen

## Objekte und Knoten

Die Struktur einer Website ist ein **Baum**. Der Beispielbaum des Buchs enthält einen Dokumentenzweig, geordnete
Galerien und ein Forum. Der Kerngedanke dieses Kapitels ist eine Unterscheidung, die man leicht übersieht und die man
unbedingt beibehalten sollte:

- Ein **Inhaltsobjekt** ist die Information selbst: seine Attribute, Versionen und Übersetzungen. Es hat eine Objekt-ID.
- Ein **Knoten** ist eine *Platzierung* eines Objekts im Baum. Er hat eine Knoten-ID, einen Elternknoten und einen Platz
  in der Reihenfolge.

Ein Objekt kann mehr als einen Knoten haben, also an mehreren Stellen erscheinen, ohne kopiert zu werden. Löschen Sie
eine Platzierung, so bleibt das Objekt dort, wo es noch platziert ist. Der Name gehört zum Objekt und wird von den
Versionen einer Übersetzung geteilt. In der Administration sind die Stellen eines Objekts seine **Orte** (der Teil
**Orte** eines Eintrags in der **Inhaltsstruktur**), und einer davon ist der **Hauptort**: derjenige, dessen Adresse die
eigene Adresse des Objekts ist.

Die Adresse eines Knotens auf der Website ergibt sich aus seinen Namen entlang des Pfads (siehe [Links, URL-Aliase und
Platzhalter](../../anleitungen/urls-und-aliase.md)); `/content/view/full/<node id>` ist die einfache Form und funktioniert immer.

## Sektionen

Eine **Sektion** ist eine virtuelle Sammlung von Knoten, die nach Bedeutung oder Funktion zusammengehören. Sektionen
erfüllen zwei Aufgaben:

1. **Berechtigungen.** Eine Rolle kann auf eine Sektion begrenzt werden („Redakteure dürfen Artikel in der Sektion News
   bearbeiten“).
2. **Darstellung.** Ein Template kann die Sektion auswerten und für sie ein anderes Menü oder sogar ein anderes
   Seitenlayout zeigen.

Regeln, die das Buch nennt und die weiterhin gelten:

- Ein Knoten gehört zu einer Sektion.
- Ein neuer Kindknoten **erbt** die Sektion seines Elternknotens; wenn Sie also der Spitze eines Zweigs eine Sektion
  zuweisen, gilt sie für das, was später darunter angelegt wird. (Für das, was darunter bereits existiert, brauchen Sie
  **Inhalt zuweisen** auf der Seite der Sektion; das gibt die Sektion dem gewählten Eintrag und allem darunter.)
- Es gibt immer mindestens eine Sektion, die Standardsektion.

Wie Sie Sektionen anlegen und zuweisen und was die Sektion außerdem steuert, steht in [Sektionen](../../anleitungen/sektionen.md)
und [Sektionen, Rollen und Zugriff für Builder](../erweiterungsentwicklung/teil-2-builder/sektionen-rollen-und-zugriff-fuer-builder.md).

## Probieren Sie es aus

1. Öffnen Sie die **Inhaltsstruktur** und beachten Sie den Baum links.
2. Öffnen Sie einen beliebigen Eintrag und sehen Sie sich seine **Orte** an: den Hauptort und alle weiteren.
3. Fügen Sie einem Eintrag einen zweiten Ort hinzu (das Fenster **Orte** in seinem Bearbeitungsformular) und sehen Sie,
   wie derselbe Eintrag in zwei Zweigen erscheint. Ändern Sie den Titel an einer Stelle, und beide zeigen ihn.

Weiter: [Klassen, Attribute und Objekte](klassen-attribute-und-objekte.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
