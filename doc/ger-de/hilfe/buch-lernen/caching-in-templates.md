---
title: "Caching in Templates: übergreifende Caches und Cache-Blöcke"
audience: builder, designer, developer
category: books
order: 430
summary: Die Einstellungen, die das Kompilieren von Templates und den View-Cache während der Entwicklung ein- oder ausschalten, wie Schlüssel und Ablauf von {cache-block} funktionieren, und die Liste der PHP-Operatoren, die einfache Operatoren hinzufügt.
keywords: Cache, Cache-Block, Schlüssel, Ablauf, Entwicklungsmodus, Template-Cache, Buch Learning, cache, cache-block, keys, expiry, ignore_content_expiry, ViewCaching, TemplateCompile, TemplateCache, DevelopmentMode, PHPOperatorList, template.ini, Learning book
type: help
---
# Caching in Templates: übergreifende Caches und Cache-Blöcke

## Die übergreifenden Caches

Die wichtigsten Caches werden durch Einstellungen in `site.ini` gesteuert:

```ini
[TemplateSettings]
TemplateCompile=enabled
TemplateCache=enabled
DevelopmentMode=disabled

[ContentSettings]
ViewCaching=enabled
```

(`TemplateCompile` und `TemplateCache` betreffen die kompilierten Templates; `ViewCaching` den Cache der
Inhaltsansichten.) Während Sie Templates entwickeln, wollen Sie Ihre Änderungen sofort sehen, und das Buch schlägt vor,
sie abzuschalten. Heute erreichen Sie das mit `DevelopmentMode=enabled` in `[TemplateSettings]` (nur auf einer
Entwicklungsinstallation) oder indem Sie nach jeder Änderung den kleinsten passenden Cache leeren
([Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md)). Der Tipp des Buchs, dass das Speichern eines Templates
in der Administration die richtigen Teile des Caches für Sie leert, gilt nicht für Template-Dateien auf der Festplatte;
die Caches leeren Sie selbst. Das ganze Thema steht in [Caching und Templates](../erweiterungsentwicklung/teil-2-builder/caching-und-templates.md).

## Cache-Blöcke

Ein `{cache-block}` speichert das Ergebnis eines Teils eines Templates als Datei. Die nächste Anfrage lädt die Datei,
statt den Template-Code und die Abrufe darin auszuführen. Er braucht **Schlüssel** (was die Ausgabe unterscheidet) und
einen **Ablauf**.

```
{cache-block keys=array( $module_result.node_id ) expiry=300}
    {* a comment list, rebuilt every five minutes *}
{/cache-block}
```

**Schlüssel.** Das System fügt dem Schlüssel bereits die Template-Datei, die Position des Blocks und den Siteaccess
hinzu, sodass mehrere Blöcke in einem Template nie kollidieren. Sie fügen Schlüssel für das hinzu, was die Ausgabe
verändert: bei einem Ansichtstemplate mindestens die Knoten-ID, und den Benutzer (oder die Liste der Rollen-IDs des
Benutzers, wenn viele Benutzer wenige Rollen teilen), falls die Ausgabe davon abhängt, wer hinsieht. Ein
Navigationsmenü, das für alle gleich ist, braucht keine Schlüssel.

**Ablauf.** Ohne `expiry` läuft ein Block nach zwei Stunden ab oder sobald irgendetwas veröffentlicht wird. Das ist der
Standard, und deshalb leert ein einziges Veröffentlichen jeden Block. Es gibt zwei Wege, das zu ändern:

```
{cache-block ignore_content_expiry}
    {* kept regardless of new content *}
{/cache-block}

{cache-block expiry=300}...{/cache-block}   {* seconds *}
```

Ebenfalls verfügbar: `subtree_expiry` (Ablauf, wenn sich etwas unter einem bestimmten Knoten ändert). Wenn ein
Ansichtstemplate Cache-Blöcke verwendet, wollen Sie für diese Ansicht im Allgemeinen den View-Cache abschalten; ein
Template tut das, indem es `cache_ttl` auf 0 setzt.

Leeren: `php bin/php/ezcache.php --clear-id=template-block --allow-root-user` (oder `./console exp:cache`, siehe dessen
`--help`). Denken Sie daran, nach einer Änderung an einem Template, das in einem Block sitzt, den Block zu leeren, sonst
bleibt die alte Ausgabe bis zum Ablauf stehen.

## Eigene Operatoren aus PHP-Funktionen

Einfache PHP-Funktionen mit einem Argument lassen sich mit einer Zeile in `template.ini` zu Template-Operatoren machen,
ohne eigenen Code:

```ini
[PHP]
PHPOperatorList[]
PHPOperatorList[lefttrim]=ltrim
PHPOperatorList[striptags]=strip_tags
```

Die mitgelieferte Datei führt bereits `urlencode` und `urldecode` auf. Die Regel: ein Parameter, ein Rückgabewert (nicht
per Referenz). Für echte Operatoren siehe [Einen Operator Schritt für Schritt anlegen](../template-operatoren/einen-operator-schritt-fuer-schritt-anlegen.md)
und [Operatoren aus PHP-Funktionen](../template-operatoren/php-funktionsoperatoren.md).

Weiter: [Templates überschreiben](templates-ueberschreiben.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 3, „Displaying Content with eZ publish Templates“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Syntax, Operatoren, Einstellungen und Beispiele auf Exponential 6 aktualisiert (foreach, aktuelle Einstellungen, kein Inline-Stil und kein Inline-Skript).
