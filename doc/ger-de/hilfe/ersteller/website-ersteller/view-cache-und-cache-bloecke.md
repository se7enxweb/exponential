---
title: View-Cache und Cache-Blöcke
audience: website-creator, developer
category: website-creation
order: 630
summary: Wie der View-Cache für Inhalte und die Cache-Blöcke in Templates funktionieren - was je Seite zwischengespeichert wird, welches Veröffentlichen was leert (viewcache.ini), Schlüssel von Cache-Blöcken, Ablauf und Teilbaum-Ablauf und wie Sie veraltete Ausgaben untersuchen.
keywords: View-Cache, Cache-Block, Schlüssel, Ablauf, Teilbaum-Ablauf, veraltete Inhalte, Website-Ersteller, view cache, ViewCaching, CachedViewModes, viewcache.ini, ClearRelationTypes, cache-block, keys, subtree_expiry, expiry, ignore_content_expiry, stale content, website creator
type: help
---
# View-Cache und Cache-Blöcke

Diese Seite richtet sich an Website-Ersteller, die Templates für Seiten schreiben und diese sowohl schnell als auch
aktuell brauchen. Zwei Caches erledigen den Großteil der Arbeit: Der **View-Cache für Inhalte** hält den gerenderten
Hauptbereich jeder Seite vor, und **Cache-Blöcke** halten Teile von Templates wie Menüs vor.

## Inhalt

1. [Der View-Cache für Inhalte](#der-view-cache-für-inhalte)
2. [Was ein Veröffentlichen leert](#was-ein-veröffentlichen-leert)
3. [Cache-Blöcke](#cache-blöcke)
4. [Schlüssel, Ablauf und Teilbaum-Ablauf](#schlüssel-ablauf-und-teilbaum-ablauf)
5. [Veraltete Ausgaben untersuchen](#veraltete-ausgaben-untersuchen)

## Der View-Cache für Inhalte

`site.ini [ContentSettings]`:

| Schlüssel | Ausgeliefert | Bedeutung |
|---|---|---|
| `ViewCaching` | `enabled` | die Ausgabe von `content/view` zwischenspeichern |
| `CachedViewModes` | `full;sitemap;pdf` | die zwischengespeicherten Ansichtsmodi |
| `CachedViewPreferences[]` | | Benutzereinstellungen, die die Ausgabe verändern und Teil des Schlüssels werden |

Eine zwischengespeicherte Ansicht wird je Knoten, Ansichtsmodus, Sprache, Siteaccess, Rollen und Einschränkungen des
Benutzers, Ansichtsparametern und Layout vorgehalten. Das Pagelayout gehört nicht dazu: Es läuft jedes Mal, weshalb es
Cache-Blöcke verwendet.

## Was ein Veröffentlichen leert

Das Veröffentlichen eines Objekts leert den Cache seiner eigenen Knoten, ihrer Eltern und verwandter Objekte, so wie
`settings/viewcache.ini [ViewCacheSettings]` es festlegt: `ClearRelationTypes[]` (`common`, `reverse_common`,
`reverse_embedded`) bestimmt, welche Relationen zählen; `SmartCacheClear` ergänzt Regeln je Klasse. Eine Seite, die
Inhalte aus weit entfernten Teilen des Baums auflistet (eine Startseite mit Nachrichten), wird nicht automatisch
geleert, sofern keine Regel das festlegt; verwenden Sie in ihren Cache-Blöcken den Teilbaum-Ablauf oder eine Regel für
das gezielte Leeren des Caches.

## Cache-Blöcke

```text
{cache-block keys=array( $module_result.uri, $current_user.contentobject_id )}
  ... expensive menu ...
{/cache-block}
```

Die Ausgabe des Blocks wird gespeichert und wiederverwendet, solange seine Schlüssel passen und er nicht abgelaufen ist.
Veröffentlichte Inhalte leeren standardmäßig alle Template-Block-Caches, sodass ein neuer Ordner in den Menüs erscheint.

## Schlüssel, Ablauf und Teilbaum-Ablauf

| Parameter | Bedeutung |
|---|---|
| `keys` | Werte, von denen die Ausgabe abhängt: die Adresse, der Benutzer, die Sprache |
| `expiry` | Sekunden, bis der Block abläuft (Standard zwei Stunden); `0` bedeutet nie aufgrund der Zeit |
| `ignore_content_expiry` | der Block wird durch Veröffentlichen nicht geleert |
| `subtree_expiry` | der Block wird nur geleert, wenn unterhalb dieses Knotens etwas veröffentlicht wird |

Eine Nachrichtenliste auf der Startseite:

```text
{cache-block subtree_expiry='/News' expiry=0}
  {foreach fetch( 'content', 'list', hash( 'parent_node_id', 58, 'limit', 5, 'sort_by', array( 'published', false() ) ) ) as $item}
    {node_view_gui content_node=$item view='line'}
  {/foreach}
{/cache-block}
```

Speichern Sie niemals Ausgaben zwischen, die sich je Benutzer unterscheiden (eine Begrüßung, die Anzahl im Warenkorb),
ohne den Benutzer in den Schlüsseln.

## Veraltete Ausgaben untersuchen

| Symptom | Versuchen Sie |
|---|---|
| Eine Seite zeigt nach dem Veröffentlichen alte Inhalte | `--clear-tag=content`; ergänzen Sie dann eine Regel für das gezielte Leeren oder einen Teilbaum-Ablauf |
| Ein Menü zeigt einen alten Ordner | `--clear-id=template-block`; prüfen Sie die Schlüssel des Blocks |
| Die Daten eines Benutzers werden einem anderen angezeigt | dem Block fehlt der Benutzer in den Schlüsseln: sofort beheben |
| Angemeldete Benutzer sehen dasselbe wie Besucher | `CachedViewPreferences[]` oder den Schlüsseln fehlt, was sich unterscheidet |
| Nach einer Template-Änderung ändert sich nichts | der Template-Cache, dann der Inhalts-Cache, dann der Cache von Velocity |

Die Debug-Leiste zeigt, welche Caches für eine Seite verwendet wurden
([Exp-Debug-Leiste](../../../../eng-us/features/6.0/exp-debug-bar.md)).

## Verwandte Seiten

- [Leistung und Caching](leistung-und-caching.md)
- [Caches](../../../anleitungen/caches.md)
- [Caching in Anwendungen](../anwendungsentwickler/caching-in-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
