---
title: Schritttypen von Migrationen
audience: developer, admin
category: examples
order: 0
summary: Jeder Schritttyp von YAML-Migrationen mit seinen Modi, Schlüsseln, Referenzwerten und einem Beispiel.
keywords: Beispiele, Referenz, Schritttypen von Migrationen, examples, reference
type: generated
generated: exp:help:examples
---
# Schritttypen von Migrationen

Jeder Schritttyp von YAML-Migrationen, den der Kernel in `settings/migration.ini` `[StepSettings] StepTypeList` registriert, je eine Seite, erzeugt aus dem Schema des Typs: seine Modi, Schlüssel, die Werte, die `references:` nehmen kann, ob er sich selbst zurücknimmt, die Caches, die er berührt, und ein Beispiel. Eine Erweiterung registriert ihre eigenen Typen genauso ([wie](../../anleitungen/migrationen-yaml-schritte.md)).

| Schritttyp | Modi | Was er tut |
|---|---|---|
| [reference](reference.md) |  | Setzt Referenzen (${ref.<name>}) für die Schritte und Migrationen nach ihm im Lauf |
| [assert](assert.md) |  | Lässt die Migration scheitern, wenn eine Bedingung falsch ist |
| [content](content.md) | create, update, ensure, delete | Legt ein Inhaltsobjekt an, ändert, sichert oder löscht es, mit seinen Feldern in einer oder mehreren Sprachen |
| [location](location.md) | create, update, delete, move, copy, hide, unhide | Fügt einen Ort (Knoten) eines Inhaltsobjekts hinzu, ändert, entfernt, verschiebt, kopiert, verbirgt oder zeigt ihn |
| [content_version](content_version.md) | restore, cleanup | Stellt eine frühere Version eines Inhaltsobjekts wieder her oder entfernt seine alten Versionen |
| [content_class](content_class.md) | create, update, ensure, delete | Legt eine Inhaltsklasse mit ihren Attributen, deren Datentyp-Einstellungen und den Übersetzungen ihrer Namen an, ändert, sichert oder löscht sie |
| [content_class_group](content_class_group.md) | create, update, ensure, delete | Legt eine Gruppe von Inhaltsklassen an, benennt sie um, sichert oder löscht sie |
| [user](user.md) | create, update, ensure, delete | Legt ein Benutzerkonto mit seinen Gruppen, seinem Passwort und seinen Feldern an, ändert, sichert oder löscht es |
| [user_group](user_group.md) | create, update, ensure, delete | Legt eine Benutzergruppe mit ihrem Namen und ihrer Beschreibung in einer oder mehreren Sprachen an, ändert, sichert oder löscht sie |
| [role](role.md) | create, update, ensure, delete, assign, unassign | Legt eine Rolle mit ihren Richtlinien und Einschränkungen an, ändert, sichert oder löscht sie, oder weist sie Benutzern und Benutzergruppen zu oder nimmt sie ihnen |
| [section](section.md) | create, update, ensure, delete, assign | Legt eine Sektion an, ändert, sichert oder löscht sie, oder weist einem Teil des Inhaltsbaums eine Sektion zu |
| [language](language.md) | create, update, ensure, delete | Fügt eine Sprache der Inhalte hinzu, benennt sie um, sichert oder entfernt sie |
| [object_state_group](object_state_group.md) | create, update, ensure, delete | Legt eine Objektzustandsgruppe mit ihren Zuständen und den Übersetzungen ihrer Namen an, ändert, sichert oder löscht sie |
| [tag](tag.md) | create, update, ensure, delete, move, merge | Legt einen Tag von eztags mit seinen Übersetzungen und Synonymen an, ändert, sichert, löscht, verschiebt oder führt ihn zusammen |
| [url_alias](url_alias.md) | create, ensure, delete | Legt einen eigenen URL-Alias eines Knotens, einen globalen URL-Alias oder einen URL-Platzhalter an, sichert oder löscht ihn |
| [trash](trash.md) | move, restore, purge | Verschiebt Inhalte in den Papierkorb, stellt sie aus ihm wieder her oder löscht sie endgültig |
| [file](file.md) |  | Hängt eine Datei aus dem Migrationsordner an ein Dateiattribut (ezbinaryfile, ezmedia) eines Objekts |
| [image](image.md) |  | Legt ein Bild aus dem Migrationsordner in ein Bildattribut eines Objekts, mit Alternativtext, Fokuspunkt und Zuschnitt |
| [cache](cache.md) |  | Leert Caches von ezcache nach ID oder Tag und die Ansichtscaches einzelner Objekte, sofort oder nach dem Lauf |
| [search](search.md) | reindex, full | Indiziert Objekte in der Suchmaschine neu (die genannten oder die, die der Lauf geändert hat) oder alle Objekte |
| [autoload](autoload.md) |  | Erzeugt das Autoload-Array der Erweiterungen oder der Kernel-Klassen, die Erweiterungen ersetzen, neu |
| [layout](layout.md) | create, update, ensure, delete | Legt ein Layout von Exponential Layouts mit seinen Zonen und ihren Verknüpfungen zu geteilten Layouts an, ändert, sichert oder löscht es |
| [layout_block](layout_block.md) | create, update, ensure, delete | Legt einen Block in einer Zone eines Layouts von Exponential Layouts mit seinen Parametern an, ändert, sichert oder löscht ihn |
| [layout_rule](layout_rule.md) | create, update, ensure, delete | Legt eine Regel von Exponential Layouts an, die Seiten einem Layout zuordnet, ändert, sichert oder löscht sie |
| [http](http.md) |  | Sendet eine HTTP-Anfrage an ein anderes System, mit Geheimnissen über ihren Namen und einer optionalen Signatur, und behält die Antwort für Referenzen |
| [mail](mail.md) |  | Sendet eine E-Mail über den Mailtransport der Installation |
| [command](command.md) |  | Führt einen exp:*-Konsolenbefehl der Installation aus, den migration.ini erlaubt, ohne Shell |
| [schema](schema.md) | create_table, drop_table, add_column, drop_column, add_index, drop_index | Legt eine Tabelle, eine Spalte oder einen Index auf jeder Engine über die Schema-Handler des Kernels an oder entfernt sie |
| [data](data.md) | insert, ensure, update, delete | Fügt Zeilen einer Tabelle auf jeder Engine ein, ändert oder löscht sie; die Werte setzt die Verbindung in Anführungszeichen |
| [sql](sql.md) |  | Führt Anweisungen der Engine aus, auf der die Installation läuft, im Schritt geschrieben oder in Dateien neben der Migration |
| [settings](settings.md) |  | Ändert eine INI-Datei von settings/override oder settings/siteaccess/<name> über den INI-Editor, mit Sicherungskopie |
| [graphql](graphql.md) |  | Führt eine Abfrage oder Mutation der GraphQL-API im Prozess als Benutzer des Laufs aus und behält ihre Antwort für Referenzen |
| [code_file](code_file.md) | create, update, ensure, delete | Writes, replaces or removes one code or design file below the allowed folders, with a backup and checksums |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
