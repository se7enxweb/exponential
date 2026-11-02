---
title: Migration step types
audience: developer, admin
category: examples
order: 0
summary: Every step type of YAML migrations with its modes, keys, reference values and an example.
keywords: Migration step types, examples, reference
type: generated
generated: exp:help:examples
---
# Migration step types

Every step type of YAML migrations the kernel registers in `settings/migration.ini` `[StepSettings] StepTypeList`, one page each, made from the type's schema: its modes, keys, the values `references:` can take, whether it undoes itself, the caches it touches and an example. An extension registers its own types the same way ([how](../../guides/migrations-yaml-steps.md)).

| Step type | Modes | What it does |
|---|---|---|
| [reference](reference.md) |  | Sets references (${ref.<name>}) for the steps and migrations after it in the run |
| [assert](assert.md) |  | Fails the migration when a condition is false |
| [content](content.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a content object, with its fields in one or more languages |
| [location](location.md) | create, update, delete, move, copy, hide, unhide | Adds, changes, removes, moves, copies, hides or shows a location (node) of a content object |
| [content_version](content_version.md) | restore, cleanup | Restores an earlier version of a content object or removes its old versions |
| [content_class](content_class.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a content class with its attributes, their datatype settings and the translations of their names |
| [content_class_group](content_class_group.md) | create, update, ensure, delete | Creates, renames, ensures or deletes a group of content classes |
| [user](user.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a user account with its groups, password and fields |
| [user_group](user_group.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a user group, with its name and description in one or more languages |
| [role](role.md) | create, update, ensure, delete, assign, unassign | Creates, updates, ensures or deletes a role with its policies and limitations, or assigns it to users and user groups or takes it from them |
| [section](section.md) | create, update, ensure, delete, assign | Creates, updates, ensures or deletes a section, or assigns a section to a part of the content tree |
| [language](language.md) | create, update, ensure, delete | Adds, renames, ensures or removes a language of content |
| [object_state_group](object_state_group.md) | create, update, ensure, delete | Creates, updates, ensures or deletes an object state group with its states and the translations of their names |
| [tag](tag.md) | create, update, ensure, delete, move, merge | Creates, updates, ensures, deletes, moves or merges a tag of eztags, with its translations and synonyms |
| [url_alias](url_alias.md) | create, ensure, delete | Creates, ensures or deletes a custom URL alias of a node, a global URL alias or a URL wildcard |
| [trash](trash.md) | move, restore, purge | Moves content to the trash, restores it from the trash, or purges it for good |
| [file](file.md) |  | Attaches a file of the migrations folder to a file attribute (ezbinaryfile, ezmedia) of an object |
| [image](image.md) |  | Puts an image of the migrations folder into an image attribute of an object, with its alternative text, focal point and crop rectangle |
| [cache](cache.md) |  | Clears caches of ezcache by id or tag and the view caches of single objects, at once or after the run |
| [search](search.md) | reindex, full | Reindexes objects in the search engine (the ones named or the ones the run changed), or every object |
| [autoload](autoload.md) |  | Regenerates the autoload array of the extensions or of the kernel classes extensions replace |
| [layout](layout.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a layout of Exponential Layouts with its zones and their links to shared layouts |
| [layout_block](layout_block.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a block in a zone of a layout of Exponential Layouts, with its parameters |
| [layout_rule](layout_rule.md) | create, update, ensure, delete | Creates, updates, ensures or deletes a rule of Exponential Layouts that maps pages to a layout |
| [http](http.md) |  | Sends one HTTP request to another system, with secrets by name and an optional signature, and keeps the answer for references |
| [mail](mail.md) |  | Sends one e-mail through the mail transport of the installation |
| [command](command.md) |  | Runs one exp:* console command of the installation that migration.ini allows, without a shell |
| [schema](schema.md) | create_table, drop_table, add_column, drop_column, add_index, drop_index | Makes or drops a table, a column or an index on every engine through the kernel's schema handlers |
| [data](data.md) | insert, ensure, update, delete | Inserts, updates or deletes rows of a table on every engine, the values quoted by the connection |
| [sql](sql.md) |  | Runs statements of the engine the installation runs on, written in the step or in files next to the migration |
| [settings](settings.md) |  | Changes one INI file of settings/override or settings/siteaccess/<name> through the INI editor, with a backup |
| [graphql](graphql.md) |  | Runs a query or mutation of the GraphQL API in process as the run's user, and keeps its answer for references |
| [code_file](code_file.md) | create, update, ensure, delete | Writes, replaces or removes one code or design file below the allowed folders, with a backup and checksums |

- [All examples](../README.md)

---

Generated by `./console exp:help:examples` from `settings/migration.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
