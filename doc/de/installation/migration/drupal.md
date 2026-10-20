---
title: Drupal und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 41
summary: Eine Drupal-Site nach Exponential 6 umziehen und zurück – JSON:API, die Migrate API und Views Data Export, die Abbildung von Inhaltstypen, Feldern, Taxonomie, Medien, Paragraphs, Benutzern und Pfad-Aliasen, Weiterleitungen und der Rückweg über die Migrate API von Drupal.
keywords: Drupal, JSON:API, Migrate API, migrate_drupal, Drush, Views Data Export, Inhaltstypen, Paragraphs, Taxonomie, Medien, Pfad-Alias, pathauto, Weiterleitung, Migration, content types, taxonomy, media, path alias, redirect, migration
type: install
---
# Drupal und Exponential

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Abbildung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## Inhalt

- [1. Was Drupal exportieren kann](#1-was-drupal-exportieren-kann)
- [2. Drupal auf Exponential abbilden](#2-drupal-auf-exponential-abbilden)
- [3. Von Drupal zu Exponential, Schritt für Schritt](#3-von-drupal-zu-exponential-schritt-für-schritt)
- [4. Adressen und Weiterleitungen](#4-adressen-und-weiterleitungen)
- [5. Von Exponential zu Drupal](#5-von-exponential-zu-drupal)
- [6. Risiken und Checkliste](#6-risiken-und-checkliste)
- [Quellen](#quellen)

## 1. Was Drupal exportieren kann

Drupal hat keine einzelne Datei „die Site exportieren“. Laut drupal.org:

| Werkzeug | Was Sie erhalten | Hinweise |
|---|---|---|
| **JSON:API** (Kernmodul) | jede Entität unter `/jsonapi/{entity_type_id}/{bundle_id}[/{uuid}]`, Medientyp `application/vnd.api+json` | standardmäßig nur lesend; höchstens 50 Ressourcen pro Anfrage, folgen Sie dem `next`-Link; eine Seite kann wegen Zugriffsprüfungen weniger Einträge enthalten |
| **Migrate API** (Kernmodule `migrate`, `migrate_drupal`; Drush-Befehle `migrate:*`) | ein Framework, um Inhalte *nach* Drupal zu bringen; seine Quell-Plugins lesen ältere Drupal-Datenbanken | nützlich in der Gegenrichtung ([5](#5-von-exponential-zu-drupal)) |
| **Views Data Export** (Contrib-Modul) | CSV, XLS, JSON oder XML aus jeder View, in Stapeln | braucht im Stapelmodus eine Sortierung nach einer eindeutigen ID |
| Die Datenbank | alles, in den Entitäts- und Feldtabellen von Drupal (`node_field_data`, `node__<field>` ...) | direktes Auslesen ist möglich, bindet Sie aber an die Schemaversion |
| `sites/default/files` | die verwalteten Dateien | kopieren |

JSON:API ist die praktischste Quelle: Es liefert jeden Inhaltstyp mit seinen Feldern, Beziehungen (Taxonomie,
Medien, Autoren) und dem Pfad-Alias, in einem dokumentierten Format, ohne dass Sie Drupal-Code schreiben müssen.

## 2. Drupal auf Exponential abbilden

| Drupal | Exponential 6 |
|---|---|
| Inhaltstyp (Node-Bundle) | Klasse |
| Feld | Attribut (Text zu `ezstring`/`eztext`, formatierter Text zu `ezxmltext`, Bild zu `ezimage` oder einer Relation, Entitätsreferenz zu `ezobjectrelationlist`, Liste zu `ezselection`, Datum zu `ezdatetime`, Link zu `ezurl`) |
| Body (Text mit Zusammenfassung) | `body` und `intro` (`ezxmltext`), HTML umgewandelt |
| Paragraphs (Contrib) | Unterobjekte kleiner Klassen oder Blöcke von Exponential Layouts für Landingpages |
| Taxonomie-Vokabular und -Begriffe | Ordner oder `eztags` (Hierarchie, Synonyme) |
| Medienentitäten und Dateien | Bild-/Datei-/Medienobjekte unterhalb von Medienordnern |
| Menü (Menülinks) | der Inhaltsbaum und die Sortierung |
| Blockinhalte | Layout-Blöcke oder Objekte, die von Templates angezeigt werden |
| Benutzer, Rollen | Benutzerobjekte in Gruppen; Rollen neu aufgebaut. Die Passwort-Hashes von Drupal haben ein eigenes Format; planen Sie das Zurücksetzen ein |
| Pfad-Alias (`/about-us`) | der URL-Alias des Knotens oder ein weiterleitender eigener Alias |
| Sprache und Übersetzungen | Übersetzungen desselben Objekts |
| Revisionen | nicht importiert; die aktuelle Revision wird Version 1 |
| Webform-Einsendungen | archiviert; Formulare mit der Informationssammlung neu aufgebaut |
| Metatag (Contrib) | `xrowmetadata` |

## 3. Von Drupal zu Exponential, Schritt für Schritt

1. **Bestandsaufnahme** der Inhaltstypen und Felder (Struktur > Inhaltstypen > Felder verwalten), Vokabulare, Medientypen,
   Sprachen und der Contrib-Module, die Inhalte speichern (Paragraphs, Webform, Metatag, Redirect).
2. **Aktivieren Sie JSON:API** auf einer Kopie der Site, nur lesend (die Standardeinstellung), und ein Konto mit Lesezugriff auf alles,
   was Sie migrieren.
3. **Legen Sie die Klassen** und den Baum in Exponential an.
4. **Export**: Ein Skript blättert für jeden Typ durch `/jsonapi/node/<bundle>` (mit `include=` für die Beziehungen,
   die Sie brauchen), dann durch Taxonomie, Medien, Dateien und Benutzer, und schreibt JSON mit Remote-IDs wie `drupal-node-<uuid>`.
   Kopieren Sie `sites/default/files`.
5. **Import** in der Reihenfolge der Abhängigkeiten: Dateien und Medien, Taxonomie, dann Nodes, dann Übersetzungen; Relationen über die
   Remote-ID aufgelöst. Der Importer von xrowextract mit `--map` oder das [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).
6. **Weiterleitungen** aus den Pfad-Aliasen und der Tabelle des Redirect-Moduls ([4](#4-adressen-und-weiterleitungen)).
7. Suche und Aliase neu aufbauen, testen, proben, umstellen ([Checkliste](checkliste.md)).

## 4. Adressen und Weiterleitungen

- **Pfad-Aliase** (`path_alias`-Entitäten, mit jedem Node als dessen Feld `path` exportiert) werden zu Knoten-Aliasen, wenn der
  Baum passt, sonst zu weiterleitenden eigenen Aliasen.
- Auch auf **`/node/123`**, den Systempfad, wird verlinkt: Legen Sie für jede Node-ID einen Alias oder eine Weiterleitung im Webserver an.
- **Die bestehenden Weiterleitungen des Redirect-Moduls**: Übernehmen Sie sie als Aliase oder Webserver-Regeln.
- **`/taxonomy/term/45`**: auf die Ordner- oder Schlagwortansicht.
- **Sprachpräfixe** (`/de/...`): Siteaccesses von Exponential pro Sprache, mit Aliasen pro Übersetzung.

## 5. Von Exponential zu Drupal

- Exportieren Sie mit xrowextract (CSV/JSON pro Klasse mit URL-Alias, Remote-ID und Attributen) und kopieren Sie den Speicher
  ([Exponential verlassen](exponential-verlassen.md)).
- Auf der Drupal-Seite ist die **Migrate API** der dokumentierte Weg hinein: Schreiben Sie Migrationen, deren Quelle die
  xrowextract-Dateien liest (Quell-Plugins für CSV oder JSON stammen aus Contrib-Modulen; prüfen Sie das gewählte), und führen Sie sie
  mit Drush aus (`migrate:import`, `migrate:rollback`, `migrate:status`; Drush 10.4 und neuer enthält diese Befehle).
- XmlText wird zu HTML (rendern oder transformieren); der Baum wird zu Menülinks oder Taxonomie; Aliase werden zu Pfad-Aliasen.
- Benutzer: Zurücksetzen der Passwörter.

## 6. Risiken und Checkliste

| Risiko | Gegenmaßnahme |
|---|---|
| Paragraphs und verschachtelte Entitäten | zuerst Unterklassen oder Layouts entwerfen; die tiefste Verschachtelung testen |
| 50 Einträge pro JSON:API-Seite | durch die ganze Sammlung blättern; mit der Bestandsaufnahme abgleichen |
| Durch Zugriffsrechte gefilterte Seiten | mit einem Konto exportieren, das alles sieht |
| Links auf `/node/<id>` im Text | in einem zweiten Durchlauf auf die neuen Knoten auflösen |

- [ ] Inhaltstypen, Felder, Vokabulare, Medien erfasst; Abbildungstabelle geschrieben.
- [ ] JSON:API-Export vollständig (Anzahlen stimmen); Dateien kopiert.
- [ ] Import in der Reihenfolge der Abhängigkeiten mit Remote-IDs; Übersetzungen denselben Objekten zugeordnet.
- [ ] Pfad-Aliase, `/node/<id>` und Einträge des Redirect-Moduls weitergeleitet und getestet.
- [ ] Metatag-Daten in `xrowmetadata`; Sitemap; Webanalyse.

## Quellen

- Paginierung in JSON:API: <https://www.drupal.org/docs/core-modules-and-themes/core-modules/jsonapi-module/pagination>
- Sicherheitsaspekte von JSON:API (Nur-Lese-Modus): <https://www.drupal.org/docs/core-modules-and-themes/core-modules/jsonapi-module/security-considerations>
- Upgrade mit Drush (Migrate): <https://www.drupal.org/docs/upgrading-drupal/upgrade-using-drush>
- Views Data Export: <https://www.drupal.org/project/views_data_export>

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
