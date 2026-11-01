---
title: url_alias (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt einen eigenen URL-Alias eines Knotens, einen globalen URL-Alias oder einen URL-Platzhalter an, sichert oder löscht ihn.
keywords: url_alias, migration, YAML, step, expMigrationStepUrlAlias
type: generated
generated: exp:help:examples
---
# url_alias

Legt einen eigenen URL-Alias eines Knotens, einen globalen URL-Alias oder einen URL-Platzhalter an, sichert oder löscht ihn.

Ein Schritt einer YAML-Migration mit `type: url_alias`. Die Klasse `expMigrationStepUrlAlias` (`kernel/classes/migration/steps/expmigrationstepurlalias.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[url_alias]` registriert.

## Modi

`mode:` ist einer von `create`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `path` | `string` | alle |  | die Adresse ab der Wurzel (about-us/team); jeder Teil wird bereinigt, wie es die URL-Umwandlung von site.ini vorgibt |
| `node` | `identifier` (node) | alle |  | der Knoten, zu dem die Adresse führt (create, ensure); delete: der Knoten, dessen eigene Adressen entfernt werden (which: all) |
| `destination` | `string` | create, ensure |  | wohin eine globale Adresse führt (eine Modul-URL wie content/search oder eine andere Adresse); mit wildcard: das Ziel, {1} der Text des ersten * |
| `wildcard` | `string` | alle |  | ein URL-Platzhalter statt eines Alias: das Muster, * steht für beliebigen Text (old-news/*) |
| `language` | `string` | alle |  | die Sprache der Adresse (create, ensure: Standard die Standardsprache der Inhalte; delete: Standard jede Sprache, die sie hat) |
| `always_available` | `bool` | create, ensure | `false` | true: die Adresse gilt in jeder Sprache eines Siteaccess |
| `redirect` | `bool` | create, ensure | `true` | true (Standard): die Adresse leitet (301) dorthin weiter, wohin sie führt; false: sie zeigt die Seite unter der eigenen Adresse |
| `below_parent` | `bool` | create, ensure | `false` | true: path: liegt unterhalb der Adresse des übergeordneten Knotens, wie es die Administration standardmäßig macht |
| `which` | `string`: `all`, `selected` | delete |  | all: jede eigene Adresse von node: (ohne node: jede globale Adresse); selected: die Adresse von path: |
| `missing` | `string`: `fail`, `skip` | delete | `"fail"` | was geschieht, wenn die Adresse oder der Platzhalter fehlt: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `path` | die Adresse, wie sie gespeichert ist (bereinigt), oder das Muster des Platzhalters |
| `action` | wohin sie führt, wie der Kernel es schreibt (eznode:59, module:content/search), oder das Ziel des Platzhalters |
| `node_id` | der Knoten, zu dem sie führt, oder null |
| `removed` | wie viele Adressen ein delete entfernt hat |
| `created` | true, wenn der Schritt die Adresse oder den Platzhalter angelegt hat |
| `changed` | true, wenn der Schritt etwas geändert hat |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`, `urlalias`.

## Beispiel

```yaml
- type: url_alias
  mode: ensure
  path: about-us
  node: "path:company/about"
- type: url_alias
  mode: ensure
  wildcard: "old-news/*"
  destination: "news/{1}"
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepurlalias.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
