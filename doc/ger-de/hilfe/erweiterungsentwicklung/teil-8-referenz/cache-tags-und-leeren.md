---
title: Cache-Tags, Cache-Blöcke und das Leeren von Caches
audience: developer
category: extension-development
order: 260
summary: Die Caches, denen ein Erweiterungsautor begegnet - IDs und Tags von ezcache.php und exp:cache, was nach welcher Änderung zu leeren ist, cache-block in Templates und das Leeren des View-Caches eines Objekts aus PHP.
keywords: Cache, Caches leeren, Cache-Tags, Cache-Block, View-Cache, Inhalts-Cache, cache, ezcache.php, exp:cache, clear-tag, clear-id, ini, template, template-block, template-override, design_base, content cache, view cache, cache-block, eZContentCacheManager, clearContentCacheIfNeeded
type: help
---
# Cache-Tags, Cache-Blöcke und das Leeren von Caches

Teil 8, Referenz 22. Exponential speichert Einstellungen, kompilierte Templates, gerenderte Seiten und mehr in Caches.
Wenn eine Änderung „nicht funktioniert“, ist meist ein Cache der Grund. Diese Seite führt die Caches danach auf, was
Sie geändert haben, und zeigt, wie Ihr eigener Code Caches richtig leert.

## Die Befehle

```bash
php bin/php/ezcache.php --clear-tag=ini            # by tag: every cache with that tag
php bin/php/ezcache.php --clear-id=template,template-override
php bin/php/ezcache.php --clear-all
php bin/php/ezcache.php --list-tags
./console exp:cache status                          # every group, and the newer caches
./console exp:cache clear --tag=ini --dry-run       # what it would do
```

`exp:cache` tut dasselbe wie die Cache-Seite der Administration und deckt zusätzlich den statischen Cache, den
HTTP-Cache, den SQL-Abfrage-Cache und Velocity ab ([Cache-Steuerung von der Konsole](../../../../eng-us/bc/6.0/cache-console.md)).
Geben Sie `--allow-root-user` an, wenn Sie als root arbeiten, führen Sie die Befehle aber besser als Benutzer des
Webservers aus.

## Die Cache-IDs und ihre Tags

Aus `kernel/classes/ezcache.php`:

| ID | Tags | Enthält |
|---|---|---|
| `ini`, `global_ini` | ini | eingelesene Einstellungen |
| `active_extensions` | ini | die Liste der aktiven Erweiterungen |
| `template` | template | kompilierte Templates |
| `template-override` | template | die Nachschlagetabelle der Override-Regeln |
| `template-block` | template, content | die Ausgabe von `cache-block` |
| `design_base` | template | die Design-Kette |
| `content` | content | gerenderte Inhalts-Views |
| `exphttpcache` | content, template | der HTTP-Antwort-Cache |
| `querycache` | content, ini | der SQL-Abfrage-Cache |
| `classid`, `sortkey`, `urlalias`, `content_tree_menu`, `state_limitations`, `content_language` | content | Nachschlagedaten zu Inhalten |
| `translation`, `chartrans` | i18n | Übersetzungen |
| `imagealias` | image | Bildvarianten |
| `user_info_cache` | user | Benutzer- und Rollendaten |

## Was nach welcher Änderung zu leeren ist

| Sie haben geändert | Leeren |
|---|---|
| eine INI-Datei | `--clear-tag=ini` (das Tag, nicht `--clear-id=ini`, das den globalen INI-Cache stehen lässt) |
| eine Erweiterung aktiviert | `--clear-tag=ini`, dann `--clear-id=design_base,template-override`, und die Autoloads neu erzeugen |
| ein Template | meist nichts; `--clear-id=template`, wenn kompilierte Templates gecacht werden |
| eine Template-Datei hinzugefügt, die eine Override-Regel verwendet | `--clear-id=template-override` |
| ein Stylesheet oder Skript in einer Packer-Liste | `--clear-id=template-block` (die gepackten Links stehen in Cache-Blöcken), danach ein hartes Neuladen |
| eine Übersetzung | `--clear-tag=i18n` |
| eine Klasse oder eine Richtlinie | `--clear-tag=content` und `--clear-id=user_info_cache` |

## cache-block in Templates

```text
{cache-block keys=array( $node.node_id, $current_user.contentobject_id ) expiry=600}
    {* expensive fetches and markup *}
{/cache-block}
```

| Parameter | Bedeutung |
|---|---|
| `keys` | wodurch sich der Inhalt unterscheidet: der Knoten, der Benutzer, die Sprache. Lassen Sie nichts weg, sonst sieht ein Benutzer den Block eines anderen |
| `expiry` | Sekunden, bis er neu aufgebaut wird |
| `subtree_expiry` | der Block verfällt, wenn unter diesem Knoten Inhalt veröffentlicht wird |
| `ignore_content_expiry` | nicht bei jeder Veröffentlichung verfallen |

Cachen Sie benutzerbezogene Inhalte nie ohne den Benutzer in `keys`. Die Hilfe für Designer erklärt die
Template-Seite ausführlich ([cache-block](../../../../eng-us/help/designers/templates/cache-block.md)).

## Aus Ihrem PHP-Code

Wenn Ihr Code Inhalte über die Funktionen des Kernels ändert, leert der Kernel die View-Caches selbst. Wenn Ihre
Erweiterung etwas ändert, das die Anzeige eines Objekts beeinflusst (eine eigene Tabelle, die ein Template liest),
leeren Sie die Caches dieses Objekts:

```php
<?php
eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
```

`clearContentCacheIfNeeded( $objectID, $versionNum = true, $additionalNodeList = false )` folgt den
View-Cache-Einstellungen der Website (welche verwandten Knoten zu leeren sind). Ihre eigenen Datei-Caches unter
`var/` löschen oder erneuern Sie über den Cluster-Datei-Handler
([eZFile, eZDir und Cluster-Datei-Handler](ezfile-ezdir-und-cluster-dateien.md)).

## Verwandte Seiten

- Zurück: [E-Mails mit eZMail versenden](ezmail.md)
- Weiter: [Eingebaute Template-Operatoren](referenz-der-template-operatoren.md)
- [Caching und Templates](../teil-2-builder/caching-und-templates.md)
- [Leistung in Erweiterungen](../teil-3-erweiterungsentwickler/leistung-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
