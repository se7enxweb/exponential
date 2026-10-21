---
title: "Mit RAD: ein Datentyp"
audience: developer, app-creator
category: extension-development
order: 30
summary: Der Datentyp-Assistent schreibt einen Bewertungs-Datentyp - Formular, Speicherung und Fähigkeiten, die Dateien, was Sie noch selbst schreiben, und zwei Korrekturen von Hand, anhand der echten Ausgabe des Assistenten.
keywords: RAD, Datentyp-Assistent, Datentyp, Speicherung, Fähigkeiten, Bearbeiten, durchsuchbar, sortierbar, erzeugte Templates, datatype wizard, setup/datatype, datatype, storage, data_int, sort_key_int, capabilities, editing, searchable, sortable, generated templates, content.ini
type: help
---
# Mit RAD: ein Datentyp

Tutorial 3. Der **Datentyp-Assistent** (`/setup/datatype`) schreibt eine Datentyp-Erweiterung: die Klasse mit jeder
Methode, die die gewählten Fähigkeiten brauchen, die Einstellungen, die Templates, eine README und Beispiele. Dieses
Tutorial erstellt einen Bewertungs-Datentyp wie den in
[Speicherung, Validierung und Templates von Datentypen](../datentyp-speicherung-validierung-und-templates.md).
Die Ausgabe unten ist die echte Ausgabe des Assistenten: Sein Generator (`expDatatypeWizard::files()`) wurde mit
denselben Eingaben ausgeführt, und jede PHP-Datei hat `php -l` bestanden.

## 1. Das Formular

| Feld | Wert |
|---|---|
| Name der Extension | `mysite_rating` |
| Bezeichner des Datentyps | `mysiterating` („Nur Kleinbuchstaben und Ziffern.“; er „wird in der Datenbank bei jedem Attribut dieses Typs gespeichert“) |
| Klassenname | `mysiteRatingType` |
| Name im Klasseneditor | `Rating` |
| Gruppe, unter der er aufgeführt wird | `Custom` (die Voreinstellung; siehe den Hinweis in Schritt 2) |
| Wo der Wert gespeichert wird | `data_int` und `sort_key_int` |
| Was er leisten muss | Bearbeiten, Durchsuchbar, Sortierbar, Text ein und aus, Pakete, Übersetzbar |

Die Seite erklärt die Wahl der Speicherung: „Ein Attribut ist eine Zeile in ezcontentobject_attribute“, mit einem festen
Satz von Spalten; ein Datentyp, der mehr braucht, führt eine eigene Tabelle. Die Fähigkeiten sind Gruppen von Methoden:
„Wird eine eingeschaltet, werden alle geschrieben, jede mit einem Hinweis, wofür der Kernel sie aufruft.“ Die
vollständige Liste lautet Bearbeiten, Klasseneinstellungen, Standardwert, Objekte benennen, Durchsuchbar, Sortierbar,
Text ein und aus, Informationssammler, Dateien, Einfaches Einfügen aus Zeichenketten, Pakete, Unterschiede zwischen
Versionen, Eigene Schaltflächen, Beim Veröffentlichen, Aufräumen, Übersetzbar, Bezieht sich auf andere Objekte und
Initialisierung in einem Durchgang.

## 2. Die Dateien

```text
.gitignore
LICENSE
README.md
composer.json
datatypes/mysiterating/mysiteratingtype.php
doc/examples.php
doc/storage.md
extension.xml
ezinfo.php
settings/content.ini.append.php
settings/design.ini.append.php
templates/content/datatype/edit/mysiterating.tpl
templates/content/datatype/view/mysiterating.tpl
```

`settings/content.ini.append.php` registriert alles, mit Kommentaren:

```ini
[DataTypeSettings]
# The extension whose datatypes/ directory is searched. Without this line
# the class below is never looked for, however correctly it is named.
ExtensionDirectories[]=mysite_rating

# What the class editor offers. The file is looked for at
# datatypes/mysiterating/mysiteratingtype.php inside this extension.
AvailableDataTypes[]=mysiterating

# Which group the class editor lists it under.
DataTypeGroup[mysiterating]=Custom
```

> **Hinweis**
> Als diese Seite geschrieben wurde, las nichts im Kernel `DataTypeGroup[]` (eine Suche in `kernel/`, `lib/` und
> `settings/` findet es nur im Assistenten), daher führt der Klasseneditor den Datentyp unabhängig von der Gruppe
> zwischen den anderen auf.

## 3. Zwei Korrekturen vor der Verwendung

Als diese Seite geschrieben wurde, brauchte die erzeugte Ausgabe zwei Änderungen von Hand:

1. **Verschieben Sie die Templates in ein Design.** Der Assistent schreibt sie nach `templates/...` im Wurzelverzeichnis
   der Erweiterung, während die Design-Kette in `design/<design>/templates/` der Erweiterungen in `DesignExtensions[]`
   sucht. Verschieben Sie sie:

   ```bash
   mkdir -p extension/mysite_rating/design/standard
   mv extension/mysite_rating/templates extension/mysite_rating/design/standard/templates
   php bin/php/ezcache.php --clear-id=template-override,design_base
   ```

2. **Passen Sie das Bearbeitungsfeld an Ihre Speicherung an.** Das erzeugte Bearbeitungs-Template sendet das Feld als
   `{$attribute_base}_data_text_{$attribute.id}`, und das erzeugte `objectAttributeContent()` gibt `data_text` zurück,
   wie es der nächstverwandte Datentyp des Kernels tut. Für einen Wert in `data_int` benennen Sie das Feld um und lesen
   und schreiben `data_int`, wie in der vollständigen Klasse aus
   [Speicherung, Validierung und Templates von Datentypen](../datentyp-speicherung-validierung-und-templates.md).

## 4. Die Methoden schreiben

Jede erzeugte Methode „gibt zurück, was der Kernel erwartet, und tut sonst nichts“, mit einem Hinweis, wofür der Kernel
sie aufruft, zum Beispiel bei `validateObjectAttributeHTTPInput()`: „Geben Sie STATE_INVALID zurück und setzen Sie einen
Validierungsfehler am Attribut, um den Redakteur zum Formular zurückzuschicken; ACCEPTED ohne Prüfung zurückzugeben ist
der Weg, auf dem schlechte Inhalte hineinkommen.“ Füllen Sie sie Gruppe für Gruppe aus, beginnend mit Bearbeiten, und
nehmen Sie die vollständige Klasse aus dem vorigen Kapitel als Vorbild.

## 5. Einschalten und ausprobieren

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_rating global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

**Setup > Klassen**: Fügen Sie einer Testklasse ein Attribut vom Typ „Rating“ hinzu, legen Sie ein Objekt an, probieren
Sie gültige und ungültige Werte aus und sortieren Sie eine Liste nach dem Attribut.

## Verwandte Seiten

- Zurück: [Mit RAD: ein Template-Operator in fünf Minuten](rad-template-operator.md)
- Weiter: [Mit RAD: ein Modul mit Views](rad-modul.md)
- [Datentypen](../datentypen.md), [Von Hand: ein Datentyp](von-hand-datentyp.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
