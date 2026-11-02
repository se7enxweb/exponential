---
title: layout (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt ein Layout von Exponential Layouts mit seinen Zonen und ihren Verknüpfungen zu geteilten Layouts an, ändert, sichert oder löscht es.
keywords: layout, migration, YAML, step, expMigrationStepLayout
type: generated
generated: exp:help:examples
---
# layout

Legt ein Layout von Exponential Layouts mit seinen Zonen und ihren Verknüpfungen zu geteilten Layouts an, ändert, sichert oder löscht es.

Ein Schritt einer YAML-Migration mit `type: layout`. Die Klasse `expMigrationStepLayout` (`kernel/classes/migration/steps/expmigrationsteplayout.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[layout]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `identifier` | `string` | alle | erforderlich | der Bezeichner des Layouts |
| `name` | `string` | create, update, ensure |  | der Name des Layouts |
| `layout_type` | `string` | create, update, ensure |  | ein Layouttyp aus explayouts.ini ([LayoutType_<Typ>]); die Zonen, die er festlegt, werden hinzugefügt |
| `shared` | `bool` | create, update, ensure |  | ob andere Layouts die Blöcke seiner Zonen übernehmen können (ein neues Layout: false) |
| `zones` | `map` | create, update, ensure |  | Zone => {} (eine eigene Zone), { link: "<geteiltes Layout>/<Zone>" } oder { link: none } |
| `publish` | `bool` | create, update, ensure | `true` | true: der Entwurf wird veröffentlicht; false: die Änderung bleibt im Entwurf |
| `missing` | `string`: `fail`, `skip` | alle | `"fail"` | was geschieht, wenn das Layout oder Exponential Layouts nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `layout_id` | die ID des veröffentlichten Layouts (des Entwurfs bei publish: false) |
| `identifier` | ihr Bezeichner |
| `created` | true, wenn der Schritt das Layout angelegt hat |
| `changed` | true, wenn der Schritt etwas geändert hat |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `template-block`.

## Beispiel

```yaml
- type: layout
  mode: ensure
  identifier: landing_page
  name: Landing page
  layout_type: layout_2
  zones:
    main: {}
    header: { link: "header_footer/header" }
  references: { landing: layout_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteplayout.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
