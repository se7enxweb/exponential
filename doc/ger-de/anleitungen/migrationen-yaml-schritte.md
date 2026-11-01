# Schritte von YAML-Migrationen

Diese Seite ist die Anleitung zu den Schritttypen von YAML-Migrationen: was die Schritttypen des Kernels tun, wie sie
finden, was sie ändern, wie ein Schritt in mehreren Sprachen arbeitet, welche Schritte sich selbst zurücknehmen und wie
eine Erweiterung einen eigenen Schritttyp hinzufügt. Die Engine um die Schritte (Platzhalter, Bedingungen, Schleifen,
die ID-Zuordnung) beschreibt [das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md#die-yaml-migration);
jeden Schlüssel jedes Schritttyps nennt seine erzeugte Referenzseite
([alle Schritttypen](../beispiele/migrationsschritte/README.md)).

[Anleitungen](README.md) · [Migrationen](migrationen.md) · Siehe auch: [Das Tutorial](migrationen-tutorial.md#12-eine-yaml-migration),
[PHP-Migrationen](migrationen-php.md), [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)

## Die Schritttypen des Kernels

| Schritttyp | Was er tut | Modi |
|---|---|---|
| [`reference`](../beispiele/migrationsschritte/reference.md) | setzt Referenzen für die Schritte nach ihm | |
| [`assert`](../beispiele/migrationsschritte/assert.md) | lässt die Migration scheitern, wenn eine Bedingung falsch ist | |
| [`content`](../beispiele/migrationsschritte/content.md) | legt ein Inhaltsobjekt an, ändert, sichert oder löscht es | create, update, ensure, delete |
| [`location`](../beispiele/migrationsschritte/location.md) | fügt einen Knoten hinzu, ändert, entfernt, verschiebt, kopiert, verbirgt oder zeigt ihn | create, update, delete, move, copy, hide, unhide |
| [`content_version`](../beispiele/migrationsschritte/content_version.md) | stellt eine frühere Version wieder her oder entfernt alte Versionen | restore, cleanup |
| [`content_class`](../beispiele/migrationsschritte/content_class.md) | legt eine Klasse mit ihren Attributen an, ändert, sichert oder löscht sie | create, update, ensure, delete |
| [`content_class_group`](../beispiele/migrationsschritte/content_class_group.md) | legt eine Klassengruppe an, benennt sie um, sichert oder löscht sie | create, update, ensure, delete |
| [`user`](../beispiele/migrationsschritte/user.md) | legt ein Benutzerkonto an, ändert, sichert oder löscht es | create, update, ensure, delete |
| [`user_group`](../beispiele/migrationsschritte/user_group.md) | legt eine Benutzergruppe an, ändert, sichert oder löscht sie | create, update, ensure, delete |
| [`role`](../beispiele/migrationsschritte/role.md) | eine Rolle mit ihren Richtlinien und Einschränkungen, und ihre Zuweisungen | create, update, ensure, delete, assign, unassign |
| [`section`](../beispiele/migrationsschritte/section.md) | eine Sektion, und ihre Zuweisung an einen Teil des Baums | create, update, ensure, delete, assign |
| [`language`](../beispiele/migrationsschritte/language.md) | fügt eine Sprache der Inhalte hinzu, benennt sie um, sichert oder entfernt sie | create, update, ensure, delete |
| [`object_state_group`](../beispiele/migrationsschritte/object_state_group.md) | eine Objektzustandsgruppe mit ihren Zuständen | create, update, ensure, delete |

Jeder Schritt geht über die eigene API des Kernels, so wie die Administration: Ein neues Objekt entsteht über seine
Klasse (`eZContentClass::instantiate()`), eine neue Version über `createNewVersionIn()`, und beide veröffentlicht die
Operation publish; Orte ändern sich über die Operationen `addlocation`, `removelocation`, `delete`, `move`, `hide`,
`sort`, `updatepriority` und `updatemainassignment`. So folgen URL-Aliase, Suchindex, Caches und Audit-Log einem Schritt
wie einer Bearbeitung in der Administration.

## Benennen, was ein Schritt ändert

Schlüssel vom Typ `identifier` nehmen eine ID, eine Remote-ID, einen Pfad oder einen Namen, und an ihrer Stelle einen
Platzhalter:

```yaml
parent: 2                          # die Knoten-ID
parent: remote:news-root           # der Knoten mit dieser Remote-ID
parent: object-remote:news-folder  # der Hauptknoten des Objekts mit dieser Remote-ID
parent: path:media/images          # der Knoten unter diesem URL-Pfad
class: article                     # eine Klasse über ihren Bezeichner (oder ihre ID, oder remote:<Remote-ID der Klasse>)
section: standard                  # eine Sektion über ihren Bezeichner oder ihre ID
parent: "${idmap.node.news}"       # die ID-Zuordnung der Umgebung
parent: "${ref.news}"              # ein Wert, den ein früherer Schritt mit references: behalten hat
```

IDs unterscheiden sich zwischen Installationen, Remote-IDs und Pfade nicht. Geben Sie den Objekten, die eine Migration
anlegt, eine `remote_id`, dann finden spätere Migrationen sie auf jeder Installation.

## Inhalte

```yaml
- type: content
  mode: ensure
  class: folder
  parent: object-remote:news-folder
  remote_id: news-archive
  language: eng-GB
  fields:
      name: Archive
      short_description: "<p>Older news.</p>"
  translations:
      ger-DE: { name: Archiv }
  references: { archive: node_id }
```

- **create** legt ein neues Objekt an und scheitert, wenn die `remote_id` vergeben ist. **ensure** sucht das Objekt
  über die `remote_id`: Es legt es an, wenn es fehlt, und ändert sonst nur die Felder, deren Text abweicht; der Schritt
  kann also immer wieder laufen. **update** ändert ein Objekt, das `object:` oder `node:` nennt; **delete** entfernt es
  mit allen Orten (`trash: true` legt es in den Papierkorb).
- `fields:` werden in der Sprache von `language:` geschrieben (Standard: die Standardsprache für Inhalte). Jeder Wert
  geht durch `fromString()` des Datentyps des Attributs, das Format des Paketimports: Text so, wie er ist, XML-Text als
  XML, eine Relationsliste als mit `-` verbundene IDs, eine Checkbox als `1` oder `0`.
- `translations:` hat einen Block je weiterer Sprache. Jeder Block wird als eigene Version veröffentlicht, wie es ein
  Übersetzer in der Administration täte; eine Sprache, die das Objekt noch nicht hat, kommt hinzu.
- Anlegen und Ändern erzeugen eine **neue Version** und veröffentlichen sie. `in_place: true` schreibt stattdessen in die
  veröffentlichte Version, ohne neue Version und ohne die Workflows der Operation publish: eine Korrektur, die der
  Versionsverlauf nicht zeigt. Name des Objekts, Caches und Suchindex werden nachgeführt.
- `missing: skip` lässt ein Ändern oder Löschen durch, wenn das Objekt nicht da ist.

## Orte

```yaml
- type: location
  mode: create
  object: remote:news-archive
  parent: path:frontpage
  priority: 10
  references: { archive_on_front: node_id }
- type: location
  mode: move
  node: object-remote:news-archive
  parent: path:media/archive
```

`create` fügt einen Ort hinzu (und ändert nichts, wenn das Objekt schon unter diesem Elternknoten liegt), `update` setzt
`priority`, `sort_field`/`sort_order` und `main`, `delete` entfernt einen Ort (mit dem letzten geht das Objekt mit,
`trash: true` in den Papierkorb), `move` und `copy` bringen einen Knoten unter einen anderen Elternknoten, `hide` und
`unhide` verbergen oder zeigen einen Knoten mit seinem Teilbaum. `copy` kopiert das Objekt des Knotens (seine
veröffentlichte Version, alle Sprachen).

## Versionen

`content_version` mit `mode: restore` veröffentlicht eine Kopie einer früheren Version als neue Version (was „Kopieren
und bearbeiten“ und Veröffentlichen im Versionsverlauf tun), `mode: cleanup` entfernt archivierte Versionen, sodass
`keep:` davon bleiben, und mit `drafts: true` auch die Entwürfe.

## Klassen und Klassengruppen

```yaml
- type: content_class_group
  mode: ensure
  name: News
- type: content_class
  mode: ensure
  identifier: news_article
  language: eng-GB
  name: News article
  names: { ger-DE: Nachrichtenartikel }
  name_pattern: "<title>"
  groups: [ News ]
  attributes:
    - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, settings: { data_int1: 255 } }
    - { identifier: body, type: ezxmltext, name: Body }
```

Eine Klasse entsteht mit ihren Namen in jeder Sprache (`name:` in der Sprache von `language:`, `names:` für die
anderen), ihren Gruppen (über den Namen) und ihren Attributen. Ein Attribut nennt seinen Datentyp (`type:`), seine
Namen und Beschreibungen, die Schalter `required`, `searchable`, `translatable` und `information_collector`, eine
`category`, eine `placement` und seine Datentyp-Einstellungen in den Spalten, in denen die Klassenbearbeitung des
Datentyps sie speichert (`settings: { data_int1: 255 }`: `data_int1` bis `data_int4`, `data_float1` bis
`data_float4`, `data_text1` bis `data_text5`; die Referenz jedes Datentyps sagt, welche Spalte was hält). `ensure` legt
eine fehlende Klasse an und ergänzt sonst die fehlenden Attribute und ändert, was abweicht; `update` ändert eine Klasse
mit `add_attributes:`, `update_attributes:` (über den Bezeichner) und `remove_attributes:`. Ein Attribut wird so
hinzugefügt, wie der Setup-Assistent eines hinzufügt (`eZSiteInstaller::addClassAttributes()`): Jedes Objekt der Klasse
bekommt es in jeder seiner Versionen und Sprachen. Ein entferntes Attribut nimmt seine Daten in jedem Objekt mit. Ein
Datentyp lässt sich nicht ändern; entfernen Sie das Attribut und fügen Sie ein neues hinzu. Eine Klasse mit Objekten
wird nur mit `with_objects: true` gelöscht, und ihre Objekte mit ihr.

## Benutzer, Gruppen und Rollen

```yaml
- type: user_group
  mode: ensure
  parent: 5
  remote_id: news-editors
  name: News editors
  references: { editors: node_id }
- type: user
  mode: ensure
  login: editor
  email: editor@example.com
  password: "${ref.editor_password}"
  groups: [ "${ref.editors}" ]
  fields: { first_name: News, last_name: Editor }
- type: role
  mode: ensure
  name: News editor
  policies:
    - { module: content, function: read }
    - { module: content, function: edit, limitations: { Class: [ news_article ], Subtree: [ "path:news" ] } }
- type: role
  mode: assign
  role: News editor
  groups: [ "remote:news-editors" ]
  subtree: "path:news"
```

Eine Benutzergruppe ist ein Inhaltsobjekt der Klasse `user_group`, also legt `user_group` sie an, übersetzt und löscht
sie so wie `content`. Ein Benutzer ist ein Objekt der Klasse `user` in einer oder mehreren Gruppen (`groups:` nennt ihre
Knoten, die erste ist der Hauptort); sein Konto entsteht aus `login:` und `email:`, sein Passwort wird gehasht, wie der
Kernel es hasht. Schreiben Sie nie ein Passwort in eine Migrationsdatei: Nehmen Sie es aus einer Referenz oder der
Umgebung, oder geben Sie einen vorhandenen Hash mit `password_hash:` und `password_hash_type:`. Kein Plan, kein Log und
keine Aufzeichnung zeigt ein Passwort. `ensure` lässt das Passwort eines vorhandenen Kontos unverändert.

Richtlinien werden geschrieben wie in der Administration, und ihre Einschränkungswerte dürfen das, worauf sie
einschränken, so nennen wie der Rest der Migration: `Class` und `ParentClass` nehmen Klassenbezeichner, `Section` und
`UserSection` Sektionsbezeichner, `Node` Knotenbezeichner, `Subtree` und `UserSubtree` Knotenbezeichner (gespeichert als
Pfad des Knotens), `SiteAccess` Siteaccess-Namen, `StateGroup_<Gruppe>` Zustandsbezeichner. `mode: assign` gibt die
Rolle Benutzern (`users:`, über das Login) und Gruppen (`groups:`, Objektbezeichner), eingeschränkt auf einen Teilbaum,
eine Sektion oder gar nicht; `mode: unassign` nimmt sie zurück.

## Sektionen, Sprachen und Objektzustände

`section` legt eine Sektion an (`identifier`, `name`, `navigation_part`) und weist sie mit `mode: assign` dem Objekt
eines Knotens zu und, nach `scope:`, den Objekten darunter (`subtree`), nur denen, die dieselbe Sektion hatten
(`inherited`), oder nur dem Objekt (`object`), so wie section/assign in der Administration. `language` fügt eine Sprache
der Inhalte hinzu (das Locale muss eines sein, das die Installation kennt); welche Sprachen ein Siteaccess zeigt, bleibt
eine Einstellung. Eine Sektion oder Sprache in Gebrauch wird nicht gelöscht. `object_state_group` legt eine Gruppe mit
ihren Zuständen und deren Namen in jeder Sprache an; der erste Zustand ist der Zustand jedes Objekts, und die Objekte
eines entfernten Zustands bekommen den ersten verbleibenden. Die Gruppen des Kernels (`ez_lock` und die anderen
Bezeichner, die mit `ez` beginnen) werden nicht geändert.

## Workflows und Benachrichtigungen

Ein Schritt führt die Operation ohne die Workflows ihres Auslösers aus und veröffentlicht ohne
Benachrichtigungsereignis: Eine Migration soll keine Mails senden und auf keine Freigabe warten. `workflows: true`
führt die Workflows aus `workflow.ini` aus wie die Administration, `notify: true` erzeugt das
Benachrichtigungsereignis. Ein Workflow, der die Operation abbricht oder aufschiebt, lässt den Schritt scheitern.

## Eine Migration zurücknehmen

Eine Migration ohne `down:` lässt sich von selbst zurücknehmen, wenn jeder ihrer Schritte das kann:

| Schritt | Nimmt sich selbst zurück |
|---|---|
| `content` create mit `remote_id` | das Objekt wird wieder entfernt |
| `location` create | der Ort wird wieder entfernt |
| `location` hide / unhide | der Knoten wird wieder gezeigt / verborgen |
| `content_class`, `content_class_group`, `section`, `language`, `object_state_group`, `role`, `user` create | wird wieder entfernt (eine Klasse nur, solange sie keine Objekte hat, eine Sektion oder Sprache nur, solange sie unbenutzt ist) |
| `user_group` create mit `remote_id` | die Gruppe wird wieder entfernt |
| `role` assign / unassign | die Zuweisung wird zurückgenommen / wieder vorgenommen |
| `reference`, `assert` | nichts zurückzunehmen |

Jeder andere Schritt (ein Ändern, ein ensure, ein Löschen, ein Verschieben, eine Sektionszuweisung ...) kann nicht wissen, was vorher da war:
Geben Sie der Migration eine `down:`-Liste, oder setzen Sie den Lauf auf den Sicherungspunkt vor ihm zurück.
`exp:migration:rollback` lehnt eine Migration ab, die sich nicht zurücknehmen lässt, und sagt es.

## Transaktionen

Der Runner führt jede Migration in einer Transaktion aus, wo die Engine welche hat (PostgreSQL, SQLite, und
MySQL/MariaDB für Migrationen ohne Schemaänderungen); ein Schritt, der scheitert, rollt die ganze Migration zurück,
samt den Objekten, die ihre früheren Schritte angelegt haben. MongoDB hat keine Transaktionen: Eine gescheiterte
Migration lässt stehen, was ihre früheren Schritte getan haben, und der Sicherungspunkt vor dem Lauf ist der Weg zurück.

## Ein eigener Schritttyp

Eine Erweiterung fügt einen Schritttyp mit einer Klasse und einer Zeile Einstellungen hinzu:

```ini
# extension/news/settings/migration.ini.append.php
[StepSettings]
StepTypeList[news_import]=expNewsImportStep
```

Die Klasse erweitert `expMigrationStepTypeBase` und schreibt `name()`, `schema()` und `apply()`; `validate()`, `caches()`
und `plan()` haben Vorgaben. `schema()` nennt die Modi, die Schlüssel mit ihren Typen, die Werte, die `references:`
nehmen kann, und ein Beispiel; die Engine prüft jeden Schritt daran, bevor etwas läuft. Ein Typ, der seine Schritte
zurücknehmen kann, implementiert `expMigrationRevertibleStepType` (`revert()`), einer, der nur manche zurücknehmen
kann, `expMigrationPartlyRevertibleStepType` (`revertible()` sagt, welche).
`./console exp:help:examples --area=migration-steps` erzeugt seine Referenzseite.

## Weiterlesen

- [Alle Schritttypen](../beispiele/migrationsschritte/README.md), je eine erzeugte Seite
- [Das Dateiformat: die YAML-Migration](../spezifikationen/6.0/migrationsdateiformat.md#die-yaml-migration)
- [Migrationen: eine erste Migration, Schritt für Schritt](migrationen-tutorial.md)
