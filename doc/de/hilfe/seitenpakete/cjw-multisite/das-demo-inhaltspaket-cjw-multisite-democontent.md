---
title: Das Demo-Inhaltspaket cjw_multisite_democontent
audience: developer, admin, designer
category: install-upgrade
order: 77
summary: Was cjw_multisite_democontent mitbringt und wie jeder Teil genutzt wird. Die 40 Inhaltsklassen, die 138 Objektdateien, die Simple Files, die in cjw_themes_jumper zeigen, und die vier Nebendateien cjw-class-additions.xml, cjw-explayouts.json, cjw-paths.json und cjw-starter-import.json.
keywords: cjw_multisite_democontent, Paket contentobject, cjw-class-additions.xml, cjw-explayouts.json, cjw-paths.json, cjw-starter-import.json, simple-file, extension-path, Remote-ID, cjw-starter-o, cjw-starter-n, cjw-starter-t, Layoutband 900000, Klassen ng_, Klassen tmv_, contentobject package, remote id, layouts band, ng_ classes, tmv_ classes
type: help
---
# Das Demo-Inhaltspaket cjw_multisite_democontent

`cjw_multisite_democontent` enthält die Inhalte der Startseite von CJW. Es ist ein Paket vom Typ `contentobject` mit
`install_type="install"`, verlangt von [cjw_multisite](README.md). Neben dem, was die Pakethandler des Kernels installieren (Klassen und
Objekte), bringt es vier Nebendateien mit, die nur der Installer des Seitenpakets liest. Diese Seite erklärt jeden Teil, für Entwickler, die
die Inhalte ändern oder ein ähnliches Paket bauen.

## Inhalt

1. [Das Paket in Kürze](#das-paket-in-kürze)
2. [Die Inhaltsklassen](#die-inhaltsklassen)
3. [Die Inhaltsobjekte](#die-inhaltsobjekte)
4. [Simple Files, die in die Erweiterung zeigen](#simple-files-die-in-die-erweiterung-zeigen)
5. [cjw-class-additions.xml](#cjw-class-additionsxml)
6. [cjw-starter-import.json](#cjw-starter-importjson)
7. [cjw-explayouts.json](#cjw-explayoutsjson)
8. [cjw-paths.json](#cjw-pathsjson)
9. [Verwandte Seiten](#verwandte-seiten)

## Das Paket in Kürze

Aus `package.xml`:

```text
The content of the CJW Multisite starter site (design starter of the cjw_themes_jumper extension): pages,
articles, news, galleries, banners, forms and images, in English and German, with the content classes it uses.
The image and file contents are read from extension/cjw_themes_jumper/share/content-images and content-files.
```

| Teil | Anzahl | Installiert von |
|---|---|---|
| Einträge `<provide type="ezcontentclass">` und Installationselemente `ezcontentclass` | 40 | dem Handler des Kernels für Inhaltsklassen |
| In `ezcontentobject/contentobjects.xml` aufgeführte Objektdateien | 138 | dem Handler des Kernels für Inhaltsobjekte |
| Einträge `<simple-file>` | 158 | dem Kernel, nachdem der Installer sie verlinkt hat |
| Nebendateien | 4 | dem Installer des Seitenpakets (`cjw-starter-install.php`) |

Jeder Verweis zwischen Objekten, Knoten, Tags und Layouts nutzt Remote-IDs, nie IDs einer anderen Installation: Objekte sind
`cjw-starter-o-<id>`, Knoten `cjw-starter-n-<id>`, Tags `cjw-starter-t-<id>`. Die Wurzel der Startseite ist `cjw-starter-n-root` (ein
Objekt namens „Starter“, unterhalb der Inhaltswurzel platziert), die Startseite `cjw-starter-n-779`.

## Die Inhaltsklassen

Die 40 Klassen, alle mit einem Installationselement `ezcontentclass`:

| Gruppe | Klassen |
|---|---|
| Seiten und Teile `ng_*` (31) | `ng_accordion_item`, `ng_article`, `ng_audio`, `ng_banner`, `ng_blog_post`, `ng_category`, `ng_component_about`, `ng_component_features`, `ng_component_hero`, `ng_component_lead`, `ng_component_logos`, `ng_component_quote`, `ng_contact_form`, `ng_container`, `ng_cookie_policy`, `ng_frontpage`, `ng_gallery`, `ng_htmlbox`, `ng_job_application_form`, `ng_job_position`, `ng_landing_page`, `ng_lead_form`, `ng_logo`, `ng_menu_item`, `ng_news`, `ng_person`, `ng_recipe`, `ng_shortcut`, `ng_site_info`, `ng_topic`, `ng_video` |
| Formulare und Ordner (3) | `cjw_feedback_form`, `cjw_folder_management`, `ng_feedback_form` |
| Der Veranstaltungs-Feed von TMV (6) | `tmv_container`, `tmv_container_categories`, `tmv_categorie`, `tmv_date`, `tmv_event`, `tmv_image` |

Die Klassen `image` und `file`, die 63 der Objekte nutzen, kommen aus dem verlangten `sevenx_classes`.

## Die Inhaltsobjekte

`contentobjects.xml` führt die 138 Objektdateien in der Reihenfolge der Installation auf und nennt einen Wurzelknoten
(`cjw-starter-n-root`, „Starter“) und die Siteaccesses `site` und `admin`. `cjw-starter-import.json` führt 137 der Objekte mit Klasse,
Sprachen und Remote-ID des Knotens auf; nach Klasse:

| Klasse | Objekte | Klasse | Objekte |
|---|---|---|---|
| `image` | 61 | `ng_article` | 23 |
| `ng_news` | 9 | `ng_banner` | 6 |
| `ng_container`, `ng_gallery`, `ng_landing_page`, `ng_shortcut` | je 5 | `file`, `ng_category`, `ng_htmlbox` | je 2 |
| `cjw_feedback_form`, `cjw_folder_management`, `ng_audio`, `ng_blog_post`, `ng_cookie_policy`, `ng_feedback_form`, `ng_frontpage`, `ng_recipe`, `ng_site_info`, `ng_video`, `tmv_container`, `tmv_container_categories` | je 1 | | |

Sprachen: 111 Objekte gibt es nur in `ger-DE`, 22 nur in `eng-US` und 4 in beiden. Jede Objektdatei behält ihre ursprünglichen Zeitpunkte
der Veröffentlichung und Änderung (`ezremote:published`, `ezremote:modified`), die der Installer wiederherstellt, sodass nach Datum
sortierte Listen wie auf der Quellseite herauskommen.

## Simple Files, die in die Erweiterung zeigen

Ein Eintrag `<simple-file>` dieses Pakets hat neben den üblichen `key`, `original-path` und `package-path` ein Attribut `extension-path`:

```xml
<simple-file key="..." original-path="var/site/storage/images/..."
             package-path="simplefiles/2559185f95d63921.jpg"
             extension-path="extension/cjw_themes_jumper/share/content-images/2559185f95d63921.jpg"/>
```

Das Archiv hat keinen Ordner `simplefiles/`. Bevor die Pakete installiert werden, legt `cjwLinkStarterFilesFromExtension()` für jeden
Eintrag einen symbolischen Link in `simplefiles/` des Pakets an (eine Kopie, wo sich keine Links anlegen lassen), sodass der Kernel jede
Datei dort findet, wo `eZPackage::simpleFilePath()` sie erwartet. Nach der Installation entfernt `cjwUnlinkStarterFilesFromExtension()`
diese Links wieder (Kopien bleiben). 155 Einträge zeigen in `share/content-images/`, 3 in `share/content-files/`, insgesamt 77 verschiedene
Dateien.

## cjw-class-additions.xml

Achtzehn Attribute in neun Klassen, die die Startinhalte zusätzlich zu den Klassen `ng_*` von `sevenx_classes` brauchen:

| Klasse | Attribute (Datentyp) |
|---|---|
| `ng_article` | `additional_image`, `icon_title` (`ezimage`) |
| `ng_banner` | `header_text` (`ezxmltext`) |
| `ng_category` | `image` (`ezimage`) |
| `ng_container` | `full_intro`, `teaser_intro` (`ezxmltext`), `image` (`ezimage`), `short_title` (`ezstring`) |
| `ng_frontpage` | `teaser_title` (`ezstring`), `teaser_intro`, `full_intro` (`ezxmltext`), `image` (`ezimage`), `tags` (`eztags`) |
| `ng_gallery` | `column_count` (`ezinteger`) |
| `ng_landing_page` | `full_intro` (`ezxmltext`), `image` (`ezimage`) |
| `ng_news` | `location` (`ezgmaplocation`) |
| `ng_shortcut` | `icon_title` (`ezimage`) |

`cjwEnsureStarterClassAdditions()` fügt ein Attribut nur hinzu, wenn die Klasse keines mit diesem Bezeichner hat: nie als Pflichtfeld, nach
dem letzten Attribut, mit einem leeren Wert in jedem vorhandenen Objekt. Die Klassendefinitionen dieses Pakets bringen sie bereits mit,
sodass auf einer neuen Installation alle als vorhanden gemeldet werden; eine Installation, deren Klassen von anderswo kamen, bekommt, was
fehlt. Ein vorhandenes Attribut wird nie geändert; eines mit einem anderen Datentyp wird gemeldet.

## cjw-starter-import.json

Die Begleitdatei für den Import. Ihre Schlüssel:

| Schlüssel | Inhalt | Genutzt von |
|---|---|---|
| `source` | Die Website und der Zeitpunkt des Schnappschusses, aus dem die Inhalte stammen | nur zur Information |
| `root_node_remote_id`, `home_node_remote_id` | `cjw-starter-n-root`, `cjw-starter-n-779` | zur Information; der Installer hat sie als Konstanten |
| `hidden_node_remote_ids` | `cjw-starter-n-807`, der Konfigurationsordner (Seiteninformationen, Cookie-Richtlinie, Links im Kopf) | `cjwHideStarterNodes()` |
| `tags` | 5 Tags mit Elternelement, Schlagwort, Remote-ID und Locale | `cjwFixStarterTags()` |
| `link_resolution` | Wie Werte `ngenhancedlink` mit einer `remote_id` die `id` des installierten Objekts bekommen | `cjwResolveStarterLinks()` |
| `objects` | 137 Objekte mit Remote-ID, Klasse, Sprachen, Remote-ID des Knotens und MD5 | Prüfungen |
| `files` | 158 Schlüssel von Simple Files mit `extension-path` und SHA-1 | Prüfungen |

Die Tags werden unterhalb eines Tags „Starter“ (`cjw-starter-t-root`) angelegt; die Tag-IDs in den Objektdateien sind die der Quellseite und
werden nicht genutzt.

## cjw-explayouts.json

Die Layouts der Startseite als Zeilen für die Tabellen von explayouts:

```json
{ "band": 900000, "node_references": { ... }, "rows": { "explayouts_layout": [ ... ], ... } }
```

| Tabelle | Zeilen | Tabelle | Zeilen |
|---|---|---|---|
| `explayouts_layout` | 23 (6 geteilt) | `explayouts_collection` | 59 |
| `explayouts_zone` | 113 | `explayouts_collection_item` | 5 |
| `explayouts_block` | 269 | `explayouts_collection_query` | 42 |
| `explayouts_block_parameter` | 2.043 | `explayouts_rule` | 11 |
| `explayouts_rule_target` | 11 (7 Teilbaum, 4 Knoten) | `explayouts_rule_condition` | 19 (11 Siteaccess, 8 Klasse) |

Alle IDs sind 900000 oder höher (das Band). `node_references` nennt pro Zeile von `explayouts_collection_item`,
`explayouts_collection_query` und `explayouts_rule_target` das Feld, das eine Knoten-ID enthält, und die Remote-ID des Knotens.
`cjwSeedStarterLayouts()` ersetzt diese durch die Knoten-IDs der neuen Installation, setzt jede Siteaccess-Bedingung auf die
Benutzer-Siteaccesses der Installation, löscht die vorhandenen Zeilen des Bands, fügt die Zeilen der Datei ein und leert den Cache des
Layout-Resolvers. Nichts unterhalb des Bands wird angefasst.

> **Hinweis**
> Anders als bei `sevenx_multisite`, dessen Zeilen der Layouts aus den Dateien `share/*.dba` von Erweiterungen kommen, reisen die Layouts
> der Startseite im Inhaltspaket. Siehe [sevenx_multisite und cjw_multisite im Vergleich](sevenx-multisite-und-cjw-multisite-im-vergleich.md).

## cjw-paths.json

Der URL-Pfad jedes Knotens der Quellseite, pro Sprachansicht: 138 Einträge, 137 nach der Remote-ID des Knotens geschlüsselt und `_root` für
die Wurzel der Startseite selbst. Ein Eintrag:

```json
"cjw-starter-n-176": { "node": 176, "class": "ng_landing_page", "languages": ["eng-US","ger-DE"],
                       "hidden": 0, "ger": "showcase", "name_ger": "Showcase", "eng": "showcase", "name_eng": "Showcase" }
```

`cjwApplyStarterPaths()` geht den Baum von oben nach unten durch und macht das letzte Pfadsegment jedes Knotens zum gewünschten, sodass die
installierte Website dieselben Adressen beantwortet wie die Quellseite. Siehe [Die Installerskripte](die-installerskripte-von-cjw-multisite.md).

## Verwandte Seiten

- [cjw_multisite](README.md)
- [Woraus cjw_multisite besteht](woraus-cjw-multisite-besteht.md)
- [Die Installerskripte](die-installerskripte-von-cjw-multisite.md)
- [Die Erweiterung CJW Themes Jumper](die-erweiterung-cjw-themes-jumper.md)
- [Installationselemente und Handler](../grundlagen/installationselemente-und-handler.md)
- [Die Demo-Inhaltspakete von sevenx_multisite](../sevenx-multisite/die-demo-inhaltspakete.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
