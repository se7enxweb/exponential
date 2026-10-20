---
title: "Lektion 4.4: Ein eigener Datentyp"
audience: trainer, developer
category: training
order: 44
summary: Die Teilnehmenden lesen den Aufbau eines Datentyps im Kernel, erzeugen auf ihrer Schulungsinstallation mit dem Datentyp-Assistenten von RAD eine funktionierende Datentyp-Erweiterung, schalten sie ein und fügen sie einer Klasse hinzu. 60 Minuten.
keywords: entwickler, datentyp, eigener datentyp, eZDataType, register, DATA_TYPE_STRING, content.ini, DataTypeSettings, ExtensionDirectories, AvailableDataTypes, datentyp-assistent, RAD, setup/datatype, fromString, toString, sortKey, developers course, datatype, Datatype wizard
type: help
---
# Lektion 4.4: Ein eigener Datentyp

Ein Datentyp ist eine neue Art von Feld für Inhaltsklassen: eine Bewertung, eine Farbe, ein Buchungszeitraum. Er ist
der tiefste Erweiterungspunkt des Inhaltsmodells, mit einer PHP-Klasse, Templates und zwei Einstellungen. In dieser
Lektion lesen die Teilnehmenden den Aufbau eines Datentyps aus dem Kernel, lassen den **Datentyp-Assistent** von RAD
einen funktionierenden Datentyp in eine Erweiterung schreiben und fügen ihn einer Klasse hinzu.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 4: Inhaltsklassen und Datentypen](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 4.2](datentypen-und-ihre-templates.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Teile eines Datentyps nennen: die Klasse, ihre Registrierung, ihre Templates und ihre Speicherspalten;
- `kernel/classes/datatypes/ezstring/ezstringtype.php` lesen und herausfinden, wofür jede Gruppe von Methoden da ist;
- mit **Setup** > **RAD**, **Datentyp-Assistent**, eine Datentyp-Erweiterung erzeugen und die geschriebenen Dateien
  lesen;
- die Erweiterung einschalten, die Autoloads neu erzeugen und den neuen Datentyp einer Klasse hinzufügen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md), `kernel/classes/datatypes/ezstring/ezstringtype.php`
und `settings/content.ini [DataTypeSettings]`.

1. **Die Klasse** (5 Minuten). Öffnen Sie `ezstringtype.php`: `class eZStringType extends eZDataType`, die Konstante
   `DATA_TYPE_STRING = 'ezstring'`, der Konstruktor, der `parent::__construct()` mit dem im Klasseneditor angezeigten
   Namen aufruft, und am Ende der Datei `eZDataType::register( eZStringType::DATA_TYPE_STRING, 'eZStringType' );`.
   Gehen Sie die Methodengruppen durch: das von einem Redakteur Eingegebene prüfen und übernehmen, speichern,
   `objectAttributeContent()` (was `content` zurückgibt), `toString()`/`fromString()`, `sortKey()`, `isIndexable()`.
2. **Registrierung** (3 Minuten). Der Kernel findet Datentypen in `RepositoryDirectories[]` und bei Erweiterungen in
   `extension/<ext>/datatypes/<string>/`, wenn die Erweiterung in `ExtensionDirectories[]` steht; der String muss
   außerdem in `AvailableDataTypes[]` stehen. Zeigen Sie `extension/eztags/settings/content.ini.append.php` als echtes
   Beispiel.
3. **Speicherung** (3 Minuten). Eine Attributzeile hat `data_text`, `data_int`, `data_float`, `sort_key_int` und
   `sort_key_string`; die meisten Datentypen passen dort hinein. Ein Datentyp, der mehr braucht, führt eine eigene
   Tabelle und räumt sie auf.
4. **Der Assistent** (6 Minuten). **Setup** > **RAD**, dann der **Datentyp-Assistent** (`/setup/datatype`).
   Abschnitte: **Der Datentyp** (Name der Erweiterung, Bezeichner des Datentyps, Klassenname, Name im Klasseneditor,
   Gruppe, Zusammenfassung, Autor, Composer-Vendor, Version, Lizenz), **Was er leisten muss** (Fähigkeiten wie
   **Bearbeiten**, **Standardwert**, **Sortierbar**, **Text ein und aus**), **Wo der Wert gespeichert wird** und
   **Was geschrieben wird**, eine Liste aller Dateien, bevor etwas geschrieben wird.
5. **Einschalten** (3 Minuten). Aktivieren Sie die Erweiterung, führen Sie `php bin/php/ezpgenerateautoloads.php -e`
   aus, leeren Sie die Caches; der Datentyp erscheint in der Attributliste des Klasseneditors.

### Häufige Fragen

**Brauche ich immer einen neuen Datentyp?** Nein. Oft genügt eine Klasse mit vorhandenen Datentypen oder ein
Template-Override. Schreiben Sie einen Datentyp, wenn der Wert eine eigene Bearbeitung, Prüfung oder Speicherung
braucht.

**Wofür sind `toString()` und `fromString()` da?** Einzeiliger Text hinein und heraus: das, was CSV-Import und
-Export, Pakete und `createAndPublishObject()` verwenden (Lektion 4.3).

**Warum fragt der Assistent nach einer Lizenz?** Er schreibt `ezinfo.php`, `extension.xml`, `composer.json` und eine
Datei `LICENSE`; eine Erweiterung ohne Lizenz sagt nichts darüber, wie sie verwendet werden darf (Modul 7).

### Stolperfallen

- **Bezeichner mit dem Präfix `ez`.** Neue Datentypen und Klassen verwenden ein eigenes Präfix; `ez` gehört dem
  Kernel.
- **Fehlende Autoloads.** Die Klasse wird erst gefunden, wenn `ezpgenerateautoloads.php -e` gelaufen ist.
- **Templates werden nicht gefunden.** Ohne die Registrierung des Designs lädt der Datentyp und zeichnet nichts; der
  Assistent schreibt sie, ein von Hand geschriebener Datentyp muss es ebenfalls tun.
- **Einen Datentyp entfernen, der verwendet wird.** Objekte mit dem Attribut gehen dann kaputt. Entfernen Sie zuerst
  das Attribut aus den Klassen.

## Praktische Übung

Auf der Schulungsinstallation, als Administrator an der Administration angemeldet, und in einer Shell im
Wurzelverzeichnis der Installation.

1. Lesen Sie den Kopf und die letzten Zeilen des Textzeilen-Datentyps des Kernels:
   `sed -n 30,50p kernel/classes/datatypes/ezstring/ezstringtype.php` und
   `tail -5 kernel/classes/datatypes/ezstring/ezstringtype.php`.
2. Lesen Sie eine echte Registrierung: `cat extension/eztags/settings/content.ini.append.php` (falls eztags
   installiert ist).
3. Öffnen Sie **Setup** > **RAD** und wählen Sie im Katalog **Datentyp**; die Seite **Datentyp-Assistent** öffnet sich.
4. Füllen Sie **Der Datentyp** aus: Name der Erweiterung `mysite_rating`, Bezeichner des Datentyps `mysiterating`,
   einen Klassennamen und einen Namen im Klasseneditor nach Wahl; tragen Sie sich als Autor ein und wählen Sie eine
   Lizenz.
5. Behalten Sie unter **Was er leisten muss** **Bearbeiten** bei und fügen Sie **Text ein und aus** und **Sortierbar**
   hinzu.
6. Klicken Sie auf **Vorschau aktualisieren** und lesen Sie **Was geschrieben wird**: die Liste der Dateien.
7. Schreiben Sie die Erweiterung mit **In extension/ anlegen**.
8. Listen Sie in einer Shell die Dateien auf: `find extension/mysite_rating -type f | sort`. Öffnen Sie die
   Datentyp-Klasse und ihre `settings/content.ini.append.php`.
9. Schalten Sie sie ein und aktualisieren Sie:

   ```bash
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveExtensions mysite_rating global
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

10. Bearbeiten Sie Ihre Klasse **Note** aus Lektion 4.1, fügen Sie ein Attribut des neuen Datentyps hinzu und klicken
    Sie auf **OK**.
11. Bearbeiten Sie eine Notiz, füllen Sie das neue Feld aus und veröffentlichen Sie.
12. Stellen Sie die neue Erweiterung unter Git: `git -C extension/mysite_rating init` und ein erster Commit.

## Erwartetes Ergebnis

- Schritt 6 listet die Datentyp-Klasse, die Templates, die Registrierung in `content.ini` und `design.ini`,
  `ezinfo.php`, `extension.xml`, `composer.json`, `README.md`, `.gitignore` und `LICENSE`.
- Schritt 8: die Klassendatei in einem Ordner unterhalb von `datatypes/` und `[DataTypeSettings]` mit
  `ExtensionDirectories[]` und `AvailableDataTypes[]`.
- Schritt 10: Der neue Datentyp wird in der Attributliste des Klasseneditors angeboten.
- Schritt 11: Die Notiz zeigt den Wert auf der Website über das erzeugte Ansichts-Template.
- Der Trainer prüft die Notiz einer Person am Projektor.

## Quiz

1. Welcher Aufruf am Ende einer Datentyp-Datei macht ihn dem Kernel bekannt?
2. Welche zwei Einstellungen müssen den Datentyp einer Erweiterung nennen?
   A. `ActiveExtensions[]` und `DesignExtensions[]` B. `ExtensionDirectories[]` und `AvailableDataTypes[]`
   C. `RepositoryDirectories[]` und `ExtensionRepositories[]` D. `ModuleList[]` und `ViewList[]`
3. Welche Methoden machen aus einem Wert eine Textzeile und zurück?
4. Nach welcher Spalte sortiert die Datenbank bei einem Text-Datentyp?
5. Was muss nach dem Einschalten der Erweiterung geschehen, bevor die Klasse gefunden wird?

<details>
<summary>Antworten</summary>

1. `eZDataType::register( <DATA_TYPE_STRING>, '<class name>' );`.
2. **B.** `[DataTypeSettings] ExtensionDirectories[]` und `AvailableDataTypes[]` in `content.ini`.
3. `toString()` und `fromString()`.
4. `sort_key_string`, geschrieben von `sortKey()`.
5. `php bin/php/ezpgenerateautoloads.php -e` und das Leeren der Caches.

</details>

## Weiterführende Literatur

- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md): die Assistenten, der Katalog und die Zustandsprüfung.
- [Erweiterungspunkte](../../../../../eng-us/bc/6.0/rad-extension-points.md): jede Stelle, an der sich der Kernel erweitern
  lässt.
- [Sicherheit der RAD-Werkzeuge](../../../../../eng-us/bc/6.0/rad-security.md): was die Assistenten schreiben und niemals
  schreiben.

## Verwandte Seiten

- [Modul 4: Inhaltsklassen und Datentypen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
