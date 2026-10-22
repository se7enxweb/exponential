---
title: Inhaltsklassen und Attribute
audience: cms-developer
category: cms-development
order: 10
summary: Für CMS-Entwickler jede Eigenschaft einer Inhaltsklasse und eines Klassenattributs, was jede auf der Website ändert, und die Regeln für Bezeichner, Namensmuster, Behälter und das Ändern einer Klasse, die bereits Inhalte hat.
keywords: Inhaltsklasse, Klassenattribut, Bezeichner, Muster für den Objektnamen, Muster für den URL-Alias, Behälter, immer verfügbar, Sortierung, erforderlich, durchsuchbar, übersetzbar, Informationssammler, Kategorie, Klassengruppe, Klasse ändern, content class, class attribute, identifier, object name pattern, URL alias pattern, container, always available, information collector, class group
type: help
---
# Inhaltsklassen und Attribute

Diese Seite richtet sich an Entwickler, die die Inhaltstypen einer Website festlegen. Sie geht die Eigenschaften einer
Klasse und ihrer Attribute einzeln durch und sagt, was jede für Redakteure, Templates, URLs und die Suche ändert. Die
Klickfolge zum Anlegen einer Klasse steht unter [Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md).

## Inhalt

1. [Eigenschaften der Klasse](#eigenschaften-der-klasse)
2. [Eigenschaften der Attribute](#eigenschaften-der-attribute)
3. [Bezeichner sind ein Vertrag](#bezeichner-sind-ein-vertrag)
4. [Namensmuster](#namensmuster)
5. [Klassengruppen](#klassengruppen)
6. [Eine Klasse ändern, die Inhalte hat](#eine-klasse-ändern-die-inhalte-hat)
7. [Klassen unter Versionsverwaltung](#klassen-unter-versionsverwaltung)

## Eigenschaften der Klasse

| Eigenschaft | Gespeichert als | Was sie ändert |
|---|---|---|
| Name, Beschreibung | `serialized_name_list`, übersetzbar | was Redakteure im Menü zum Anlegen und in Listen sehen |
| Bezeichner | `identifier` | was Templates, Overrides, Fetch-Filter und Importe verwenden |
| Muster für den Objektnamen | `contentobject_name` | den Namen jedes Objekts: im Baum, in Listen, in der Standard-URL |
| Muster für den URL-Alias | `url_alias_name` | den URL-Teil jedes Knotens, wenn er vom Namen abweichen soll |
| Behälter | `is_container` | ob Objekte der Klasse Unterelemente haben dürfen |
| Standardsortierung | `sort_field`, `sort_order` | die Reihenfolge der Unterelemente neuer Knoten |
| Standardverfügbarkeit der Objekte | `always_available` | ob Objekte in Sprachen angezeigt werden, in die sie nicht übersetzt sind |
| Remote-ID | `remote_id` | eine stabile ID für die Klasse selbst, von Paketen verwendet |

Die Spalten sind in `kernel/classes/ezcontentclass.php` festgelegt. Eine Klasse wird als temporäre Version bearbeitet
und ersetzt die festgelegte Klasse erst, wenn Sie **OK** drücken; eine halbfertige Bearbeitung erreicht die Redakteure
also nie.

## Eigenschaften der Attribute

| Eigenschaft | Gespeichert als | Was sie ändert |
|---|---|---|
| Name, Beschreibung | übersetzbar | die Beschriftung und den Hilfetext im Bearbeitungsformular |
| Bezeichner | `identifier` | `$node.data_map.<identifier>` in Templates, Schlüssel von `attributes` in PHP |
| Datentyp | `data_type_string` | Speicherung, Prüfung, Bearbeitungsformular, View-Template, Suche |
| Erforderlich | `is_required` | das Objekt kann ohne Wert nicht veröffentlicht werden |
| Durchsuchbar | `is_searchable` | ob der Wert in den Suchindex aufgenommen wird |
| Übersetzbar | `can_translate` | ob jede Übersetzung ihren eigenen Wert hat oder alle den der Hauptsprache teilen |
| Informationssammler | `is_information_collector` | Besucher füllen dieses Feld auf der Website aus, als Formular ([Formulare und Informationssammlung](formulare-und-informationssammlung.md)) |
| Kategorie | `category` | die Gruppe, in der das Feld beim Bearbeiten erscheint; `content.ini [ClassAttributeSettings] CategoryList[]` liefert `content` und `meta` mit |
| Position | `placement` | die Reihenfolge der Felder im Bearbeitungsformular und in Schleifen über `data_map` |

Die meisten Datentypen bringen eigene Einstellungen mit: eine Höchstlänge für `ezstring`, die erlaubten Klassen und den
Standardort für `ezobjectrelationlist`, die Optionen von `ezselection`, die Zeilenzahl von `eztext`. Sie werden in
den Datenfeldern des Klassenattributs gespeichert und bei jedem Datentyp beschrieben
([Datentypen und ihre Templates](../trainer/entwickler/modul-04-inhaltsklassen-und-datentypen/datentypen-und-ihre-templates.md)).

## Bezeichner sind ein Vertrag

Templates, Override-Regeln, Fetches, Importe, Pakete und die REST-Schnittstelle verweisen alle auf Klassen- und
Attributbezeichner. Behandeln Sie sie wie eine öffentliche API:

- Kleinbuchstaben, Ziffern und Unterstriche, englische Wörter, die sagen, was das Feld ist (`title`, `intro`, `body`,
  `image`, `start_date`);
- derselbe Bezeichner für dieselbe Bedeutung in jeder Klasse, sodass ein Template-Teil das `image` eines Artikels,
  einer Veranstaltung und eines Produkts anzeigen kann;
- benennen Sie nie einen Bezeichner um, den Code verwendet; benennen Sie stattdessen den Anzeigenamen um, das ist
  folgenlos;
- versehen Sie die Bezeichner von Klassen Ihres Projekts mit einem Präfix, wenn sie mit mitgelieferten kollidieren
  könnten (`exp_event` statt eines allgemeinen Namens, den ein Seitenpaket schon verwendet).

Das Kapitel für Website-Bauer sagt dasselbe aus Sicht der Templates:
[Inhaltsklassen für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md#bezeichner-sind-der-vertrag).

## Namensmuster

Das Muster für den Objektnamen bildet den Namen aus Attributwerten. Jedes `<identifier>` wird durch den Text dieses
Attributs ersetzt; `<a|b>` verwendet `a` oder `b`, wenn `a` leer ist (`kernel/classes/eznamepatternresolver.php`):

| Muster | Ergebnis |
|---|---|
| `<title>` | der Titel |
| `<short_title\|title>` | der Kurztitel, sonst der Titel |
| `<first_name> <last_name>` | der volle Name einer Person |

Aus dem Namen werden die URL-Aliase gebildet, sofern die Klasse kein eigenes Muster für den URL-Alias hat; ein gutes
Muster ergibt also auch gute Adressen. Muss ein Name von Code berechnet werden, kann ein Muster eine PHP-Klasse als
Resolver nennen (`content.ini [NamePatternSettings] ResolverClasses[]`, Schnittstelle
`expNamePatternResolverInterface`); siehe
[Namen, die eine PHP-Klasse bildet](../../anleitungen/inhaltsmodell-und-bearbeitung.md#von-einer-php-klasse-erzeugte-namen-entwickler).

## Klassengruppen

Klassen werden in **Klassengruppen** abgelegt (Content, Users, Media, Setup in einer neuen Installation). Gruppen
ordnen nur die Klassenliste in der Administration und die Klassen, die in jedem ihrer Teile angeboten werden; sie
ändern keine Berechtigungen. [Klassengruppen](../../anleitungen/klassengruppen.md) erklärt die Seiten und wie Sie eine
Gruppe sicher entfernen.

## Eine Klasse ändern, die Inhalte hat

| Änderung | Wirkung auf vorhandene Objekte |
|---|---|
| Ein Attribut hinzufügen | jedes Objekt erhält das Attribut mit einem leeren oder Standardwert; ein erforderliches wird erst bei der nächsten Bearbeitung durchgesetzt |
| Ein Attribut entfernen | seine Werte werden aus jedem Objekt und jeder Version entfernt; es gibt kein Rückgängig |
| Name, Beschreibung oder Kategorie ändern | nur die Beschriftungen ändern sich |
| Einen Bezeichner ändern | gespeicherte Werte bleiben, aber jedes Template, jede Einstellung und jeder Import, der den alten Bezeichner verwendet hat, bricht |
| Den Datentyp ändern | nicht angeboten: ein neues Attribut hinzufügen, die Werte mit einem Skript kopieren, das alte entfernen |
| Das Namensmuster ändern | Namen werden neu gebildet, wenn Objekte erneut veröffentlicht werden |
| Objekte in eine andere Klasse überführen | nicht im Kernel; die Erweiterung `expchangeclass` erledigt das mit einer Attributzuordnung ([Fallstudie: expchangeclass](../erweiterungsentwicklung/teil-4-aktualisieren/fallstudie-expchangeclass.md)) |

Bevor Sie eine Klasse auf einer Live-Website ändern, erstellen Sie eine Datenbanksicherung, probieren die Änderung an
einer Kopie aus und prüfen die Templates, die die Klasse verwenden, mit `grep -rn "data_map.<identifier>" extension/`.

## Klassen unter Versionsverwaltung

Eine in der Administration angelegte Klasse existiert nur in der Datenbank. Um sie in eine andere Installation zu
bringen:

- exportieren Sie sie in einem **Paket** (Klassenexport) und installieren Sie das Paket dort ([Pakete](../../anleitungen/pakete.md));
- oder schreiben Sie ein Skript, das sie anlegt: Der RAD-Assistent **Content extension** (`/setup/contentextension`)
  erzeugt eines ([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)).

Bewahren Sie das Ergebnis in jedem Fall in der Erweiterung Ihres Projekts auf, damit die nächste Installation aus
Dateien gebaut wird und nicht von Hand. [Seitenpakete und Installer](seitenpakete-und-installer.md) zeigt, wie ganze
Websites so gebaut werden.

## Verwandte Seiten

- [Datentypen wählen](datentypen-waehlen.md)
- [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md)
- [Referenz zu eZContentClass](../erweiterungsentwicklung/teil-8-referenz/ezcontentclass.md)
- [Lektion 4.1: Klassen, Attribute und Bezeichner](../trainer/entwickler/modul-04-inhaltsklassen-und-datentypen/klassen-attribute-und-bezeichner.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
