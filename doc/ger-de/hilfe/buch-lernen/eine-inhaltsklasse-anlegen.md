---
title: "Eine Inhaltsklasse anlegen"
audience: admin, builder, developer
category: books
order: 250
summary: Die Schritte und Entscheidungen beim Anlegen einer Klasse: Gruppen, Name, Bezeichner, das Muster für den Objektnamen, Attribute und was mit vorhandenen Objekten geschieht, wenn Sie die Klasse ändern.
keywords: Inhaltsklasse, Klassengruppe, Bezeichner, Muster für Objektnamen, Muster für URL-Aliasnamen, Attribut, Klasse bearbeiten, Buch Learning, content class, class group, identifier, object name pattern, URL alias pattern, attribute, class edit, Learning book
type: help
---
# Eine Inhaltsklasse anlegen

In einer Klasse bekommt eine Art von Inhalt ihre Form. Die Schritte des Buchs gelten weiterhin; die Bildschirme sind
umgezogen.

1. **Setup > Klassen** (die Klassengruppen). Klassen werden in **Klassengruppen** aufbewahrt, die nach Inhaltsart oder
   nach Funktion gruppieren, zum Beispiel die Gruppe *Benutzer* mit den Klassen für Benutzer und Benutzergruppen. Siehe
   [Klassengruppen](../../anleitungen/klassengruppen.md).
2. Öffnen oder erstellen Sie eine Gruppe, dann **Neue Klasse**.
3. Eine Klasse hat einige allgemeine Eigenschaften:
   - **Name**: das, was Redakteure sehen.
   - **Bezeichner**: ein kurzer, stabiler Maschinenname (`article`, `product`). Templates verweisen darauf, also wählen
     Sie ihn einmal und ändern Sie ihn nicht.
   - **Muster für Objektnamen**: wie der Name jedes Objekts aus seinen Attributen gebildet wird, etwa `<title>` oder
     `<first_name> <last_name>`. Das ist wichtig: Der Name ist die Grundlage der Adresse des Objekts auf der Website.
   - **Muster für URL-Aliasnamen**: seit dem Buch ein eigenes Muster für die Adresse; lassen Sie es leer, um den Namen zu
     verwenden.
   - **Container**: ob Objekte dieser Klasse Unterelemente haben dürfen.
   - **Immer verfügbar**: ob ein Objekt dieser Klasse in einer anderen Sprache gezeigt wird, wenn es in der angeforderten
     keine Übersetzung hat.
4. Fügen Sie Attribute hinzu. Wählen Sie einen Datentyp aus der Liste ([Rundgang durch die Datentypen](rundgang-durch-die-datentypen.md)),
   geben Sie jedem einen Namen und einen Bezeichner und setzen Sie die eigenen Optionen des Datentyps (eine Höchstlänge,
   einen Standardwert, eine Größengrenze).
5. Markieren Sie Attribute nach Bedarf als **Erforderlich**, **Durchsuchbar**, **Informationssammler** und
   **Übersetzbar**. Ein Attribut, das nicht übersetzbar ist (ein Foto), ist in jeder Sprache gleich.
6. **Speichern**. Legen Sie die Klasse in mehr als eine Gruppe, wenn sie zu mehreren gehört.

Die neue Klasse steht in der Liste **Hier erstellen** an den Stellen zur Verfügung, an denen eine Rolle dem Benutzer
erlaubt, sie anzulegen.

## Eine Klasse später ändern

- Ein geändertes **Muster für Objektnamen** benennt veröffentlichte Objekte nicht um. Das neue Muster wird beim nächsten
  Veröffentlichen eines Objekts verwendet; eine Massenänderung braucht also ein erneutes Veröffentlichen.
- Wenn Sie ein Attribut entfernen, entfernen Sie seinen Inhalt aus allen Objekten der Klasse. Erstellen Sie zuerst eine
  Sicherung.
- Ein Attribut hinzuzufügen ist sicher: Vorhandene Objekte zeigen es leer, bis sie bearbeitet werden.
- Um ein Objekt in eine andere Klasse zu verschieben, siehe [Die Inhaltsklasse eines Objekts ändern](../handbuch/wie-aendere-ich-die-inhaltsklasse-eines-objekts.md).

Mehr: [Inhaltsklassen für Builder](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md),
[Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) und
[Datentypen wählen](../ersteller/website-ersteller/datentypen-waehlen.md).

Weiter: [Rundgang durch die Datentypen](rundgang-durch-die-datentypen.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
