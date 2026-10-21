---
title: Muster in kernel/classes/subitems
audience: developer
category: standards
order: 507
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/subitems mit den Entwurfsmustern.
keywords: kernel/classes/subitems, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/subitems

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/subitems` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (24)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expSubitemsAttributeColumn` | class extends expSubitemsColumn | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/subitems/expsubitemsattributecolumn.php:16` | Eine automatische Unterelement-Spalte für ein Klassenattribut, Schlüssel attr:/. |
| `expSubitemsBuiltinColumn` | class extends expSubitemsColumn | – | `kernel/classes/subitems/expsubitemsbuiltincolumn.php:16` | Eine der 15 eingebauten Unterelemente-Spalten (thumbnail, name, visibility, type, modifier, modified, published, translations, section, nodeid, noderemotei… |
| `expSubitemsCallableColumn` | class extends expSubitemsColumn | – | `kernel/classes/subitems/expsubitemscallablecolumn.php:14` | Eine Unterelemente-Spalte, deren Wert von einem PHP-Callable kommt, das Handler=class::method in ihrem Block [Column_] nennt. |
| `expSubitemsColumn` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/classes/subitems/expsubitemscolumn.php:19` | Die Basisklasse einer Spalte der Admin-Unterelementliste ("Table options"). |
| `expSubitemsColumnHandlers` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/subitems/columns/expsubitemscolumnhandlers.php:16` | Beispiel-Handler=-Spalten der Unterelementliste der Administrationsoberfläche: einfache statische Methoden, die ein INI-Block als Handler=expSubitemsColumnHandlers:: nennt. |
| `expSubitemsColumnRegistry` | class | [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/subitems/expsubitemscolumnregistry.php:23` | Die Registry der Spalten der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“). |
| `expSubitemsCSVExport` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/subitems/expsubitemscsvexport.php:14` | Das CSV der Admin-Unterelementliste (content/subitemsexport/). |
| `expSubitemsDateColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsdatecolumn.php:16` | Spalten der Unterelementliste dazu, wann etwas geschah, in den Formen, die die eingebauten Spalten Published und Modified nicht zeigen: relativ ("vor 3 Tagen"), in T… |
| `expSubitemsFieldColumn` | abstract class extends expSubitemsColumn | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/classes/subitems/columns/expsubitemsfieldcolumn.php:22` | Die gemeinsame Basis der mitgelieferten Spaltenfamilien der Admin-Unterelementliste. |
| `expSubitemsMediaColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsmediacolumn.php:20` | Spalten der Unterelementliste zu den Bildern und Dateien des Inhaltsobjekts. |
| `expSubitemsNodeColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsnodecolumn.php:18` | Spalten der Unterelementliste, gelesen aus dem Ort (der Zeile von ezcontentobject_tree) und seinen Kindern. |
| `expSubitemsObjectColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsobjectcolumn.php:18` | Spalten der Unterelementliste, gelesen aus dem Inhaltsobjekt: Klasse, Sektion, Besitzer, Sprachen, Zustände und die Größe seines Haupttextes. |
| `expSubitemsPermissionColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemspermissioncolumn.php:18` | Spalten der Unterelementliste, die sagen, was der aktuelle Benutzer mit jedem Element tun darf, und technische Fakten dazu. |
| `expSubitemsPreference` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/subitems/expsubitemspreference.php:20` | Die benutzerspezifische Auswahl der Unterelementliste der Admin: sichtbare Spalten in Reihenfolge und das gewählte Preset je Navigationsteil, die Seitengröße und die eigenen … |
| `expSubitemsReadingTimeColumn` | class extends expSubitemsColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsreadingtimecolumn.php:14` | Die Spalte "Lesezeit" der Admin-Unterelementliste: die Minuten, die ein Leser für den Haupttext des Elements braucht, bei WordsPerMinute= Wörtern pro Minute (Standard… |
| `expSubitemsRelationColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsrelationcolumn.php:20` | Spalten der Unterelementliste dazu, womit das Objekt verknüpft ist und was darauf verweist: Objektrelationen, Tags (eztags) und Bewertungen (ezstarrating). |
| `expSubitemsSearchWordLinkRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/subitems/columns/expsubitemscolumntables.php:31` | Eine Zeile von ezsearch_object_word_link: eine indizierte Wortposition eines Objekts. |
| `expSubitemsSEOColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsseocolumn.php:32` | Spalten der Unterelementliste für Suchmaschinen und Besucher: der Seitentitel im Browser, seine Länge sowie Meta-Titel, Beschreibung und Schlüsselwörter, die ein xro… |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | Die ezjscore-Serverfunktionen der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“), registriert als [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… |
| `expSubitemsTemplateColumn` | class extends expSubitemsColumn | – | `kernel/classes/subitems/expsubitemstemplatecolumn.php:14` | Eine Unterelemente-Spalte, ausgegeben von einem Template, das Template=design:subitems/columns/.tpl in ihrem Block [Column_] nennt. |
| `expSubitemsURLColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsurlcolumn.php:20` | Spalten der Unterelementliste zu den Adressen eines Knotens: sein URL-Alias, seine System-URL, die Adresse, die Besucher auf der öffentlichen Site verwenden, eigene und historische… |
| `expSubitemsUserColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsusercolumn.php:19` | Spalten der Unterelementliste für Benutzerkonten: die Zeilen unterhalb einer Benutzergruppe. |
| `expSubitemsUserVisitRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/subitems/columns/expsubitemscolumntables.php:15` | Eine Zeile von ezuservisit: die Besuche, Anmeldungen und fehlgeschlagenen Anmeldungen eines Benutzers. |
| `expSubitemsVersionColumn` | class extends expSubitemsFieldColumn | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/subitems/columns/expsubitemsversioncolumn.php:17` | Spalten der Unterelementliste zu den Versionen des Inhaltsobjekts. |
