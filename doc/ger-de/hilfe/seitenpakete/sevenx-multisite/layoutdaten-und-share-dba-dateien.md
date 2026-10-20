---
title: Layoutdaten und share-DBA-Dateien
audience: developer, admin, hosting
category: install-upgrade
order: 68
summary: Wie die Layoutzeilen explayouts_*, Tags und die Demo-Bewertung in eine Datenbank von sevenx_multisite gelangen - nicht aus den Paketen, sondern aus den Dateien share/*.dba von sevenx_themes_media und explayouts - und wie die Nachinstallation sie auf die neue Installation abbildet.
keywords: explayouts, Layouts, db_data.dba, db_schema.dba, share, sevenx_themes_media, insertDBFile, extensionSchemas, dropExtensionTables, eztags, ezstarrating, admin_layouts_db_data.dba, geteiltes Layout, verknüpfte Zone, Abbildung, layouts, shared layout, linked zone, remap
type: help
---
# Layoutdaten und share-DBA-Dateien

Diese Seite ist für Entwickler, die die Layouts der Standard-Websites ändern, und für Administratoren und Hoster, die sich fragen, woher die
Layouts einer frischen Installation kommen. Sie beantwortet: welche Dateien die Zeilen `explayouts_*` mitbringen, wie der Installer sie lädt
und wie er sie an die Inhalte der neuen Installation anpasst.

## Inhalt

1. [Nicht in den Paketen](#nicht-in-den-paketen)
2. [Die Dateien](#die-dateien)
3. [Was die Grunddaten des Themes enthalten](#was-die-grunddaten-des-themes-enthalten)
4. [Wie der Installer sie lädt](#wie-der-installer-sie-lädt)
5. [Die Zeilen an die neue Installation anpassen](#die-zeilen-an-die-neue-installation-anpassen)
6. [Die Standardlayouts der Administration](#die-standardlayouts-der-administration)
7. [Die Grunddaten ändern](#die-grunddaten-ändern)

## Nicht in den Paketen

Keines der fünf Pakete von `sevenx_multisite` bringt ein Layout mit. Ein Inhaltspaket installiert Klassen und Objekte; die Layouts, Zonen,
Blöcke, Sammlungen und Regeln der Erweiterungen `explayouts` liegen in eigenen Tabellen. Der Installer legt diese Tabellen in `preInstall()`
an und füllt sie, bevor die Inhaltspakete installiert werden, aus Dateien `.dba`, die die Erweiterungen in ihren Ordnern `share/` mitbringen.

> **Hinweis**
> Die Datei `extension/expsite_data_media/data/data.sql` ist veraltet und ungenutzt. Die Quelle der Layouts einer neuen Installation sind die
> hier beschriebenen Dateien `share/*.dba`.

## Die Dateien

| Datei | Inhalt |
|---|---|
| `extension/sevenx_themes_media/share/db_schema.dba` | die Tabellen `explayouts_block`, `explayouts_block_parameter`, `explayouts_collection`, `explayouts_collection_item`, `explayouts_collection_query`, `explayouts_layout`, `explayouts_rule`, `explayouts_rule_condition`, `explayouts_rule_target`, `explayouts_zone`, `eztags`, `eztags_attribute_link`, `eztags_keyword`, `ezurl`, `ezurl_object_link`, `ezstarrating`, `ezstarrating_data`, `ezpdf_export` |
| `extension/sevenx_themes_media/share/db_data.dba` | die Zeilen dieser Tabellen (alle außer `ezpdf_export`) |
| `extension/explayouts/share/db_schema.dba` | dieselben zehn Tabellen `explayouts_*` plus `explayouts_share`, die drei Tabellen `eztags*`, `ezurl`, `ezurl_object_link` und `exp_info_collection` |
| `extension/explayouts/share/db_data.dba` | Zeilen für ihre Tabellen |
| `extension/explayouts/share/fragments/admin_layouts_db_data.dba` | die Standardlayouts der Administration, siehe unten |
| `extension/explayouts/sql/<engine>/` | `schema.sql` für `mysql`, `pgsql`, `postgresql`, `sqlite` und `oracle`, `schema.json` für `mongodb` und `data.sql` für `oracle` |

Eine Datei `.dba` ist eine PHP-Datei mit einem per `var_export()` geschriebenen Array (`$schema` oder `$data`), das `eZDbSchema::read()`
lädt; jede Tabelle hat eine Liste `fields` und nummerierte `rows`.

## Was die Grunddaten des Themes enthalten

Gezählt in `extension/sevenx_themes_media/share/db_data.dba`, wie sie in 6.0.15 ist:

| Tabelle | Zeilen |
|---|---|
| `explayouts_layout` | 21, alle mit `status` 2 (veröffentlicht) |
| `explayouts_zone` | 101, davon 38 mit einem geteilten Layout verknüpft (36 mit Layout 2, 2 mit Layout 7) |
| `explayouts_block` | 342 |
| `explayouts_block_parameter` | 3648 |
| `explayouts_collection`, `_item`, `_query` | 70, 59, 48 |
| `explayouts_rule`, `_condition`, `_target` | 20, 7, 22 |
| `eztags`, `eztags_keyword`, `eztags_attribute_link` | 31, 31, 264 |
| `ezurl`, `ezurl_object_link` | 321, 495 |
| `ezstarrating`, `ezstarrating_data` | 1, 0 |

Die zwei geteilten Layouts (`shared` = 1) sind `Header / Footer` (ID 2) und `Prefooter` (ID 7). Zu den anderen Layouts gehören
`Default Layout`, die Layouts `Full / ...` für Artikel, Rezepte, Themen und Videos, die Landingpages von Fit & Healthy, `Showcase` und die
Layouts `Bold Agency - ...`. Wie geteilte Layouts und verknüpfte Zonen im Editor funktionieren, steht auf
[Der Layout-Editor](../../layout-editor.md).

## Wie der Installer sie lädt

`preInstall()` führt `dropExtensionTables()` aus und dann `insertDBFile( package, extension, loadData )` für jeden Eintrag von
`extensionSchemas()` (siehe [Erweiterungen, die es aktiviert](erweiterungen-die-sevenx-multisite-aktiviert.md#tabellen-der-erweiterungen)). Für die Layouts zählt die
Reihenfolge:

1. **`sevenx_themes_media`, Schema und Daten.** Es kommt zuerst, weil sein Schema und seine Daten die Layouts, Tags und Bewertungen der
   Demo-Inhalte mitbringen. Seine Tabellen werden angelegt und seine Zeilen eingefügt.
2. `ezstarrating`, `ezgmaplocation`, `eztags`, nur Schema.
3. **`explayouts`, Schema und Daten.** `insertDBFile()` lässt jede Tabelle aus, die bereits existiert, mitsamt ihren Daten, statt mit „already
   exists“ zu scheitern. So behalten die zehn Tabellen `explayouts_*`, die Tags und die URL-Tabellen die Zeilen des Themes, und nur die
   Tabellen, die das Theme nicht angibt (`explayouts_share`, `exp_info_collection`), werden hier angelegt.

Details von `insertDBFile()`:

- Die Dateien der Erweiterung werden in `<package path>/ezextension/<extension>` gesucht, wenn ein Paket dieses Namens im Paket-Repository
  liegt, sonst in `extension/<extension>` (`extensionBasePath()`).
- Vor dem Einfügen löscht `dropExtensionTables()` jede Tabelle, die eines dieser Schemas angibt. Weil das Schema des Themes `ezurl` und
  `ezurl_object_link` angibt, werden auch diese beiden Tabellen mit den Zeilen des Themes neu angelegt.
- Wenn `eZDbSchema` keinen Handler für die Datenbank hat, werden die Dateien der Engine in `sql/<engine>/` genutzt: `mysql.sql` oder
  `schema.sql`, `postgresql.sql` oder `schema.sql`, `sqlite.sql` oder `schema.sql`, `schema.sql` auf Oracle, `schema.json` auf MongoDB
  (Verzeichnis `mongodb`), dann `data.sql` oder `democontent.sql` aus `sql/<engine>/` oder `sql/common/`.
- `createMissingSchemaTables()` legt dann jede angegebene Tabelle an, die das Einfügen des Schemas ausgelassen hat, und meldet die, die es
  nicht anlegen kann.

## Die Zeilen an die neue Installation anpassen

Die Grunddaten wurden gegen die Knoten- und Objekt-IDs der Referenzinhalte geschrieben. Nachdem die Inhalte installiert sind, bildet Schritt
35 der Nachinstallation (`sevenxFixPackageNodesAndExplayouts()` in `post-install-fix.php`) jede Paket-ID auf die ID ab, die die neue
Installation diesem Knoten oder Objekt gegeben hat, und ordnet jeden Verweis ein als **abgebildet** (umgeschrieben), **Basis** (Basisdaten
wie Knoten 1, behalten) oder **fehlend** (Inhalte, die das Paket nicht mitbringt, entfernt):

| Tabelle / Spalte | Abgebildet | Fehlend |
|---|---|---|
| `explayouts_rule_target.target_value` (Typen `content_node`, `ibexa_subtree`, `subtree`, `node`) | umgeschrieben | Zeile gelöscht; eine Regel ohne Ziele wird ausgeschaltet |
| `explayouts_collection_item.value_id` (`ez_location`, `ez_content`) | umgeschrieben | Zeile gelöscht |
| `explayouts_collection_query.parameters` (`parent_location_id`, `topic_content_id`) | umgeschrieben | auf 0 gesetzt |
| `explayouts_block_parameter` `content` | umgeschrieben | geleert |
| `eztags_attribute_link.object_id` | umgeschrieben | Zeile gelöscht |

Mit den vollständigen Demo-Inhalten fehlt nichts; mit `sevenx_multisite_democontent_clean` sind es diese Entfernungen, die verhindern, dass
die Layouts auf fremde Objekte zeigen. Spätere Schritte vollenden die Arbeit: Schritt 40 schaltet Links in Titelblöcken
(`explayouts_block_parameter` `link`, `use_link`) auf Pfade aus, die sich nicht auflösen lassen, und der Cache des Resolvers wird geleert
(`expLayoutsResolver::clearCache()`). Die Zeile `ezstarrating` des „Test Product“ wird in Schritt 5 neu verschlüsselt, mit wiederhergestelltem
Durchschnitt von 4,3, weil eine `.dba` keinen Bruchwert mitbringen kann.

## Die Standardlayouts der Administration

Schritt 36, `postInstallImportAdminLayouts()`, lädt `extension/explayouts/share/fragments/admin_layouts_db_data.dba`: drei Layouts mit IDs ab
100001, `Admin: left, main, right` (`admin_3col`), `Admin: left, main` (`admin_2col`) und `Admin: main only` (`admin_full`), mit ihren Zonen,
Blöcken und sechs Regeln. Es tut nichts, wenn bereits ein Layout der Administration existiert oder das Fragment fehlt. Das Design `admin4l`
baut die Seiten der Administration aus diesen Layouts; siehe [admin4l](../../../../eng-us/features/6.0/admin4l.md).

> **Hinweis**
> Der eigene Kopfkommentar des Fragments sagt noch, es sei ein vorbereitetes Fragment, das der Installer nicht lese. Dieser Kommentar ist
> älter als Schritt 36; der Installer liest es.

## Die Grunddaten ändern

- Bearbeiten Sie die Layouts auf einer laufenden Installation und exportieren Sie die Tabellen dann zurück in die `share/db_data.dba` des
  Themes. Die Grunddaten von 6.0.15 enthalten nur veröffentlichte Layouts (jede Zeile `explayouts_layout` hat `status` 2); ein Entwurf ist
  die laufende Arbeit eines Redakteurs und gehört nicht hinein.
- Prüfen Sie eine Änderung an einer `.dba` nie über ihren Text-Diff: Das Entfernen einer Zeile nummeriert jede folgende Zeile um. Vergleichen
  Sie die Dateien stattdessen Zeile für Zeile.
- Testen Sie das Ergebnis mit einer vollständigen Installation in eine Wegwerf-Datenbank; siehe
  [In einer Sandbox testen](../eigene/in-einer-sandbox-testen.md).

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md)
- [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md)
- [Klassen und Inhalte ändern](../eigene/klassen-und-inhalte-aendern.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
