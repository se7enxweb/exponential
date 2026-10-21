# Die Unterelementliste: 129 Spalten, Voreinstellungen und CSV-Export

Diese Seite ist für Redakteure, die mehr über die Elemente in einem Ordner sehen möchten, und für Administratoren und
Entwickler, die Standards festlegen oder Spalten hinzufügen. Die Unterelementliste in der Knotenansicht bot früher
fünfzehn feste Spalten. In 6.0.15 sind Spalten eine Registry: die fünfzehn alten, 114 neue in Gruppen und jedes Attribut
der Inhaltsklassen in der Liste, ohne jede Einstellung. Jeder Benutzer wählt, ordnet und speichert seine Spalten;
Administratoren legen Standards und Voreinstellungen fest. Hinzugefügt am 02.10.2026.

Der vollständige Katalog, Leistungszahlen und wie Sie eine Spalte schreiben, stehen in der Anleitung
[Optionen der Unterelementtabelle](../../../eng-us/bc/6.0/subitems-table-options.md).

## Verwenden

1. Öffnen Sie einen beliebigen Knoten mit Unterelementen und klicken Sie auf **Einstellungen** (Table options).
2. Filtern Sie die gruppierte Liste (Basic, Node, Object, Version, Location, URLs, SEO, Dates, People, Relations,
   Translations, Workflow, Users, Media, Technical, Custom), haken Sie Spalten an und ziehen Sie sie, um sie zu ordnen.
3. Wählen Sie eine Voreinstellung oder eine Seitengröße (10, 25, 50, 100).
4. Exportieren Sie die sichtbaren Spalten als CSV, wenn Sie sie in einer Tabellenkalkulation brauchen. Klicken Sie auf
   eine Zelle mit dem Kopierzeichen, um ihren Wert zu kopieren.

Die Wahl wird pro Benutzer auf dem Server gespeichert. `admin` und `admin4` laden die Spaltenliste vom Server; `admin2`,
`admin3` und `sevenx_site_admin` verwenden das Template von `admin`. Ohne die neuen Serverfunktionen funktioniert die
Tabelle wie bisher.

## Eigene Seitengröße

Neben den voreingestellten Seitengrößen aus `PageSizes[]` hat der Bereich **Einstellungen** (Table options) ein Feld
**Individuell** (seit Juni 2026; die Beschriftung kommt aus `design/admin/templates/children_detailed.tpl`).

1. Wählen Sie unter Einträge pro Seite **Individuell**.
2. Geben Sie eine ganze Zahl von 1 bis 10000 ein und drücken Sie die Eingabetaste.

Die Liste lädt sofort mit genau so vielen Zeilen neu. Alles andere wird mit einer Meldung abgelehnt, und die bisherige
Größe bleibt. Verwenden Sie es für Massenarbeit an genau den Zeilen auf dem Bildschirm (alle auswählen, dann verschieben,
kopieren oder verbergen), für Exportprüfungen, die einen genauen Ausschnitt brauchen, oder um zu testen, wie sich ein
Ordner bei einer bestimmten Größe verhält. Die Seitengröße ist eine Benutzereinstellung; siehe
[Einstellungen zum Blättern](../../../eng-us/bc/6.0/pagination-settings.md). Die Seite „Eigene Anzahl von Einträgen pro Seite“ ist
in dieser aufgegangen.

## In drei Minuten eine Spalte hinzufügen

1. Fügen Sie `settings/override/subitemscolumns.ini.append.php` einen Block hinzu, der eine aufrufbare Funktion, eine
   Klasse oder ein Template nennt:

   ```ini
   [Column_myprefix_nodeid_hex]
   Name=Node ID (hex)
   Group=Custom
   Type=text
   Handler=MyColumns::nodeHex
   Copy=true
   ```

2. Schreiben Sie den Handler im Ordner `classes/` Ihrer Extension:

   ```php
   class MyColumns
   {
       public static function nodeHex( $node, $settings, $column )
       {
           return dechex( (int)$node->attribute( 'node_id' ) );
       }
   }
   ```

3. Erzeugen Sie die Autoloads neu und leeren Sie den INI-Cache:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

4. Öffnen Sie **Einstellungen** (Table options): Die Spalte „Node ID (hex)“ steht unter **Custom**.

Weitere Schlüssel, die ein Spaltenblock tragen kann: `Class=` (eine Unterklasse von `expSubitemsColumn`),
`Template=design:...tpl`, `SortField=`, `Policy[]`, `Description=`, `Align=right`, `Order=`. Der Kopf der mitgelieferten
`settings/subitemscolumns.ini` führt jeden Schlüssel auf.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/subitems.ini` | `SubitemsSettings` | `DefaultColumns[]` | `name;published;translations;priority` | Installation |
| `settings/subitems.ini` | `SubitemsSettings` | `AttributeColumns` | enabled | Installation |
| `settings/subitems.ini` | `SubitemsSettings` | `AttributeColumnsExcludedDataTypes[]` | `ezuser` | Installation |
| `settings/subitems.ini` | `SubitemsSettings` | `CSVExport` / `CSVLimit` | enabled / 5000 | Installation |
| `settings/subitems.ini` | `SubitemsSettings` | `PageSizes[]` | 10, 25, 50, 100 | Installation |
| `settings/subitems.ini` | `Preset_<id>`, `Defaults_<id>` | `Name`, `Columns[]`, `Subtree[]`, `ParentClassIdentifiers[]` | drei Voreinstellungen werden mitgeliefert: `Preset_seo` (SEO), `Preset_editorial` (Editorial), `Preset_technical` (Technical); kein Block `Defaults_<id>` | Installation |
| `settings/subitemscolumns.ini` | `Column_<key>` | siehe die Anleitung | 129 Spalten | Installation |

Die mitgelieferten Spalten zählen:

```bash
grep -c "^\[Column_" settings/subitemscolumns.ini
```

Erwartete Ausgabe: `129`.

## Geschwindigkeit

Spalten laden ihre Daten einmal pro Seite, nicht einmal pro Zeile (`expSubitemsColumn::prefetch()`). Die Data Map, die
Versionszeilen, die lesbaren Kinder pro übergeordnetem Knoten, die Kinder pro Klasse, die Zeilen der URL-Aliase, die
Platzierungen und die Anzahlen der Beziehungen kommen jeweils aus einer Abfrage für alle Zeilen der Seite. Die Anleitung
nennt Abfrageanzahlen und Zeiten für 10, 50 und 100 Zeilen.

## Für Entwickler

Die Unterelementtabelle selbst ist `exp::datatable`. Ihr Lader ist `ezajaxsubitems_expdatatable.js`, mit einer Kopie in
`design/admin` und einer in `design/admin4`; ändern Sie beide zusammen. Dieselben Spalten werden von `expsubitems_svc` als
entfernte Dienste bereitgestellt; die Serverfunktionen sind `expSubitemsServerFunctions` (siehe
[entfernte Dienste](entfernte-dienste-expservices.md)).

## Verwandte Seiten

- [Optionen der Unterelementtabelle (Anleitung)](../../../eng-us/bc/6.0/subitems-table-options.md), [Einstellungen zum Blättern](../../../eng-us/bc/6.0/pagination-settings.md)
- [Blättern, Sortieren und Seitengrößen](seitenweise-listen-im-admin.md), [ausgewählte Unterelemente kopieren](unterelemente-auswahl-kopieren.md), [Ausgewählte ausblenden und einblenden](../../../eng-us/bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [Das responsive Admin-Design (admin3)](admin3-responsives-admin.md), [das Design admin4](admin4-design.md), [Breite und Schriftgröße der linken Seitenleiste](admin3-seitenleistenbreite-und-schriftgroesse.md)
- [Prüfprotokoll](pruefprotokoll.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Oktober 2026](../../../eng-us/history/2026/2026-10.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md), [Juni 2025](../../../eng-us/history/2025/2025-06.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md), [November 2024](../../../eng-us/history/2024/2024-11.md), [Oktober 2024](../../../eng-us/history/2024/2024-10.md), [August 2024](../../../eng-us/history/2024/2024-08.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
