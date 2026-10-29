---
title: Caching für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 32
summary: Für CMS-Entwickler jeder Cache, der zwischen einer Änderung und der Seite stehen kann. Die Seite behandelt den View-Cache und seine Schlüssel, Cache-Blöcke im Pagelayout, was welches Veröffentlichen leert, die Caches für Templates, Overrides und INI, die HTTP- und Antwort-Caches von Velocity, welchen Cache Sie nach welcher Änderung leeren und wie Sie besucherbezogene Ausgaben aus gemeinsamen Caches heraushalten.
keywords: Caching, Cache, View-Cache, ViewCaching, CachedViewModes, cache_ttl, ViewCacheTweaks, cache-block, keys, subtree_expiry, viewcache.ini, ClearRelationTypes, Template-Cache, Template-Override-Cache, INI-Cache, ezcache.php, exp:cache, HTTP-Cache, Antwort-Cache von Velocity, statischer Cache, veraltete Seite, caching, view cache, template cache, template-override cache, INI cache, HTTP cache, Velocity response cache, static cache, stale page
type: help
---
# Caching für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die schnelle Seiten ohne veraltete Seiten wollen. Exponential speichert auf
mehreren Ebenen zwischen; die meisten funktionieren ohne Ihr Zutun, und die übrigen brauchen eine Entscheidung in Ihren
Templates. Die beiden Fragen, die Sie in jedem Projekt beantworten, sind: *Was darf zwischen Besuchern geteilt werden*
und *welcher Cache muss nach welcher Änderung weg*.

## Inhalt

1. [Die Ebenen](#die-ebenen)
2. [Der View-Cache](#der-view-cache)
3. [Cache-Blöcke](#cache-blöcke)
4. [Was das Veröffentlichen leert](#was-das-veröffentlichen-leert)
5. [Caches für Code und Einstellungen](#caches-für-code-und-einstellungen)
6. [Ganze Seiten: HTTP-Cache und Velocity](#ganze-seiten-http-cache-und-velocity)
7. [Welcher Cache nach welcher Änderung](#welcher-cache-nach-welcher-änderung)
8. [Besucherbezogene Ausgaben](#besucherbezogene-ausgaben)
9. [Sehen, was zwischengespeichert ist](#sehen-was-zwischengespeichert-ist)

## Die Ebenen

| Ebene | Enthält | Mitgeliefert |
|---|---|---|
| View-Cache | die gerenderte Ansicht eines Knotens (`full` und andere zwischengespeicherte Modi) | an: `site.ini [ContentSettings] ViewCaching=enabled` |
| Cache-Blöcke | Teile von Templates, die Sie in `{cache-block}` einschließen | wo Ihre Templates sie verwenden |
| Template-Kompilier-Cache | zu PHP kompilierte Templates | an |
| Override-, INI- und Übersetzungs-Caches | aufgelöste Einstellungen und Template-Zuordnungen | an |
| Bildvarianten | skalierte Bilder | bei der ersten Verwendung erzeugt |
| SQL-Abfrage-Cache | Ergebnisse wiederholter Abfragen | an |
| HTTP-Cache | ganze Seiten, rollenbewusst, für jede Engine | siehe seinen Status |
| Antwort-Cache von Velocity | ganze Antworten auf dem Velocity-Server | `[CacheSettings]` von `velocity.ini` |
| Statischer Cache | Seiten, die als Dateien für den Webserver geschrieben werden | aus |

Jede Ebene, mit ihrem Ort und wie sie geleert wird: [Caching-Ebenen](../hosting/leistung/cache-schichten.md).

## Der View-Cache

`CachedViewModes=full;sitemap;pdf`: Diese Ansichtsmodi werden, als Seiten geöffnet, gespeichert. Das Pagelayout ist
**nicht** im View-Cache; es läuft bei jeder Anfrage um den zwischengespeicherten Inhalt herum. Eine zwischengespeicherte
Ansicht ist nach Knoten, Ansichtsmodus, Sprache, Rollen und Einschränkungen des Benutzers, View-Parametern und mehr
geschlüsselt (`kernel/classes/eznodeviewfunctions.php`), sodass Besucher mit unterschiedlichen Rechten unterschiedliche
Kopien erhalten.

Ein Full-View-Template kann seine eigene Lebensdauer setzen: `{set-block scope=root variable=cache_ttl}400{/set-block}`
hält diese Seite 400 Sekunden, nützlich für eine Seite, die mit `currentdate()` vergleicht. `ViewCacheTweaks[<node id>]`
in `site.ini` hat Optionen je Knoten, etwa ein Caching je Benutzer
([Caching für Designer](../designer/theme-design/caching-fuer-designer.md)).

## Cache-Blöcke

Schließen Sie teure Teile des Pagelayouts (Menüs, Fußzeilen) in einen Cache-Block ein, mit jedem Wert, der die Ausgabe
ändert, in seinen Schlüsseln:

```smarty
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=$menu_root_node_id}
    {include uri='design:parts/main_menu.tpl'}
{/cache-block}
```

Ohne `keys` gibt es eine Kopie für jede Seite und jeden Besucher. Standardmäßig verfällt ein Block nach 7200 Sekunden
und bei jedem Veröffentlichen; `subtree_expiry` lässt ihn nur bei Veröffentlichungen unter einem Knoten verfallen,
`ignore_content_expiry` nie beim Veröffentlichen ([Cache-Block](../designer/vorlagen/cache-block.md)).

## Was das Veröffentlichen leert

Das Veröffentlichen leert den View-Cache der Knoten des Objekts, ihrer übergeordneten Knoten und verknüpfter Objekte
nach den Relationstypen in `viewcache.ini [ViewCacheSettings] ClearRelationTypes[]` (mitgeliefert `common`,
`reverse_common`, `reverse_embedded`). `SmartCacheClear=disabled` wird mitgeliefert; eingeschaltet leeren Regeln je
Klasse mehr, zum Beispiel die Startseite, wenn ein Artikel veröffentlicht wird. Eine Startseite, die die neuesten
Artikel listet, wird beim Veröffentlichen eines davon nur geleert, wenn eine Regel oder eine Relation sie verbindet;
geben Sie Listenseiten also eine kurze `cache_ttl` oder eine Regel zum intelligenten Leeren.

## Caches für Code und Einstellungen

| Sie haben geändert | Leeren Sie |
|---|---|
| eine INI-Datei | `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` |
| ein vorhandenes Template | `--clear-id=template` (nichts, solange auf einem Entwicklungsrechner `TemplateCompile=disabled` gilt) |
| ein neues Template oder eine neue Override-Regel | `--clear-tag=ini` und `--clear-id=template-override`, dann `content` |
| eine `.ts`-Übersetzung | `--clear-tag=i18n` |
| eine Bildvariante | `--clear-tag=ini` und `--clear-id=imagealias` |
| ein Stylesheet oder Skript im Packer | die Caches für Packer und Template-Blöcke ([Der Packer](../designer/theme-design/der-packer.md)) |
| eine PHP-Klasse | Autoloads neu erzeugen und die PHP-Worker neu starten ([Eine Website bereitstellen](eine-website-bereitstellen.md)) |

Velocity hält Einstellungen und Klassen in seinen Workern: Starten Sie es nach INI- oder PHP-Änderungen neu
([Caches: was jeder enthält](../../anleitungen/caches.md)).

## Ganze Seiten: HTTP-Cache und Velocity

Der HTTP-Cache und der Antwort-Cache von Velocity halten ganze Antworten; eine Seite, die aus ihnen kommt, erreicht PHP
nie. Leeren Sie den von Velocity nach Template-Änderungen mit `./console exp:velocity cache clear --apply`, und zwar immer
**nachdem** die PHP-Worker den neuen Code ausführen, sonst wird eine vom alten Code gerenderte Seite erneut
zwischengespeichert ([HTTP-Cache](../../bc/6.0/httpcache.md),
[Antwort-Cache von Velocity](../../funktionen/6.0/velocity-antwort-cache.md)). Der statische Cache ist in neuen
Installationen aus; lassen Sie ihn aus, sofern Sie keinen Bedarf gemessen haben.

## Welcher Cache nach welcher Änderung

**Setup** > **Caches** in der Administration listet jeden Cache mit seiner ID, seiner Größe und dem Befehl, der ihn
leert, und `./console exp:cache clear --id=<id> --dry-run` zeigt, was wegfallen würde. Lesen Sie im Zweifel
[Warum erscheint meine Änderung nicht?](../zwei-wege-zum-bauen/warum-erscheint-meine-aenderung-nicht.md) und
[Caches nach einer Änderung](../zwei-wege-zum-bauen/caches-nach-einer-aenderung.md), bevor Sie alles leeren: `--clear-all` auf
einer viel besuchten Website lässt jeden Besucher warten, während die Caches neu aufgebaut werden.

## Besucherbezogene Ausgaben

Der häufigste Caching-Fehler ist der Name, der Warenkorb oder die Schaltfläche "Bearbeiten" eines Besuchers, die
zwischengespeichert und dem nächsten Besucher gezeigt werden.

- Legen Sie besucherbezogene Teile in das Pagelayout (nicht im View-Cache), in einem Cache-Block nach Benutzer
  geschlüsselt, oder lassen Sie sie ungecacht.
- Geben Sie in einer Full View nur aus, was für alle mit denselben Rollen gleich ist.
- Schlüsseln Sie einen Cache-Block nie nach etwas, das ein Besucher ohne Grenzen steuert (einem freien Textparameter):
  Das füllt die Festplatte.

## Sehen, was zwischengespeichert ist

```bash
./console exp:cache status --allow-root-user
./console exp:cache clear --id=content --dry-run --allow-root-user
```

`status` gibt eine Zeile je Ebene aus; die Debug-Ausgabe zeigt, ob eine Ansicht aus dem Cache kam. Die Arbeit an der
Leistung beginnt hier: [Leistung](leistung.md).

## Verwandte Seiten

- [Template-Kompilierung und Cache](../designer/vorlagen/template-kompilierung-und-cache.md)
- [Cache-Tags und Leeren](../erweiterungsentwicklung/teil-8-referenz/cache-tags-und-leeren.md)
- [Caching und Templates für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/caching-und-templates.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
