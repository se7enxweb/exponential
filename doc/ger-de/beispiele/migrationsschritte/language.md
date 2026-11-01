---
title: language (Migrationsschritt)
audience: developer, admin
category: examples
summary: Fügt eine Sprache der Inhalte hinzu, benennt sie um, sichert oder entfernt sie.
keywords: language, migration, YAML, step, expMigrationStepLanguage
type: generated
generated: exp:help:examples
---
# language

Fügt eine Sprache der Inhalte hinzu, benennt sie um, sichert oder entfernt sie.

Ein Schritt einer YAML-Migration mit `type: language`. Die Klasse `expMigrationStepLanguage` (`kernel/classes/migration/steps/expmigrationsteplanguage.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[language]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `locale` | `string` | create, ensure |  | das Locale der Sprache (ger-DE); ensure findet die Sprache über es |
| `language` | `identifier` (language) | update, delete |  | die umzubenennende oder zu entfernende Sprache: ihr Locale oder ihre ID |
| `name` | `string` | create, update, ensure |  | der Name der Sprache (Standard: der Name des Locales) |
| `missing` | `string`: `fail`, `skip` | update, delete | `"fail"` | was geschieht, wenn die Sprache nicht da ist: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `language_id` | die ID der Sprache |
| `locale` | ihr Locale |
| `name` | sein Name |
| `created` | true, wenn der Schritt die Sprache hinzugefügt hat |
| `changed` | true, wenn der Schritt sie geändert hat (hinzugefügt, umbenannt oder entfernt) |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content_language`, `content`.

## Beispiel

```yaml
- type: language
  mode: ensure
  locale: ger-DE
  name: Deutsch
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteplanguage.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
