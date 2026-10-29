---
title: Caching und Templates
audience: developer, designer, app-creator
category: extension-development
order: 160
summary: Die Caches, auf die ein Template trifft - kompilierte Templates, der View-Cache, cache-block, Override- und Design-Caches, HTTP- und Velocity-Caches; was jeden automatisch leert und wie Sie ihn von Hand leeren.
keywords: Cache, View-Cache, Cache leeren, ViewCaching, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, viewcache.ini, SmartCacheClear, DependentClassIdentifier, ezcache, clear-tag, clear-id, view cache, clear cache
type: help
---
# Caching und Templates

Teil 2, Kapitel 16. Eine Seite, die aus Templates und Fetches entsteht, ist teuer; Exponential speichert sie deshalb auf
mehreren Ebenen zwischen. Caches machen die Website schnell und sorgen dafür, dass die Änderungen eines Builders
„nicht erscheinen“. Dieses Kapitel erklärt jeden Cache, den ein Template berührt, und wie er geleert wird. Den
Überblick über alle Caches gibt [Caches](../../../anleitungen/caches.md).

## Die Caches einer Seite

| Cache | Enthält | Wird automatisch geleert, wenn | Von Hand leeren |
|---|---|---|---|
| Kompilierte Templates | jede `.tpl` als PHP | sich die Template-Datei ändert (`TemplateCompile=enabled` vergleicht Zeitstempel) | `--clear-tag=template` |
| Template-Override-Cache | die kompilierten Override-Regeln | nie für neue Dateien | `--clear-id=template-override` |
| Design-Basis-Cache | die Design-Kette | nie | `--clear-id=design_base` |
| Content-View-Cache | die Vollansicht eines Knotens je Benutzergruppe, Sprache und View-Parameter | der Knoten (und verwandte) veröffentlicht wird | `--clear-tag=content` |
| Template-Block-Cache | die Ausgabe von `{cache-block}` | Inhalte veröffentlicht werden (außer mit `ignore_content_expiry`) oder seine Ablaufzeit vorbei ist | `--clear-id=template-block` |
| INI-Cache | die zusammengeführten Einstellungen | `exp:ini` und die Einstellungsseite leeren ihn | `--clear-tag=ini` |
| HTTP- und Velocity-Response-Caches | ganze Antworten | Veröffentlichen, Deployments | `--clear-id=exphttpcache`, `./console exp:velocity cache clear --apply` |

Alle `--clear-...`-Optionen gehören zu `php bin/php/ezcache.php`; `--list-tags` und `--list-ids` geben aus, was es
gibt. `./console exp:cache` ist die Konsolenform mit weiteren Zielen (Static, OPcache, APCu).

## Der Content-View-Cache

Mit `site.ini [ContentSettings] ViewCaching=enabled` (der Standard) wird die Vollansicht eines Knotens nach der ersten
Anfrage gespeichert und aus dem Cache ausgeliefert, bis der Knoten erneut veröffentlicht wird. Welche View-Modi
zwischengespeichert werden, legt `CachedViewModes=full;sitemap;pdf` fest.

Wird ein Artikel veröffentlicht, werden sein eigener Cache und die Caches verwandter Seiten geleert. Welche „verwandten“
Seiten das sind, wird in `viewcache.ini` festgelegt:

```ini
[ViewCacheSettings]
SmartCacheClear=enabled

[article]
DependentClassIdentifier[]
DependentClassIdentifier[]=frontpage
ClearCacheMethod[]
ClearCacheMethod[]=object
ClearCacheMethod[]=parent
ClearCacheMethod[]=relating
```

Ist `SmartCacheClear` eingeschaltet, leert das Veröffentlichen eines `article` auch die Caches der darüberliegenden
`frontpage`-Objekte sowie seines eigenen Objekts, des Elternobjekts und der darauf verweisenden Objekte. Die möglichen
Methoden laut den Kommentaren von `settings/viewcache.ini` sind `object`, `parent`, `relating`, `keyword`, `siblings`,
`children`, `all` und `none`.

## cache-block

Teile des Pagelayouts (Menüs, Fußzeilen) werden auf jeder Seite gezeichnet und liegen nicht im View-Cache. Setzen Sie
sie in einen `cache-block`:

```text
{cache-block keys=array( $current_node_id ) expiry=3600}
    {def $menu = fetch( 'content', 'list', hash( 'parent_node_id', 2, 'sort_by', array( 'priority', true() ) ) )}
    <nav>
    {foreach $menu as $item}
        <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
    {/foreach}
    </nav>
{/cache-block}
```

| Parameter | Bedeutung |
|---|---|
| `keys` | Werte, für die eine eigene zwischengespeicherte Kopie entsteht (der aktuelle Knoten, die Sprache des Benutzers). Ohne Keys gibt es eine Kopie je Position im Template. |
| `expiry` | Sekunden, bis die Kopie abläuft |
| `subtree_expiry` | ein Knoten oder eine URL, deren Unterbaum beim Veröffentlichen die Kopie leert |
| `ignore_content_expiry` | die Kopie beim Veröffentlichen von Inhalten nicht leeren |

Gut zu wissen:

- Alles **innerhalb** des Blocks wird zwischengespeichert, auch die Ausgabe von Fetches. Ein Wert, der je Benutzer
  verschieden ist (sein Name, sein Warenkorb), darf deshalb nicht darin stehen oder muss in `keys` stehen.
- Variablen, die **innerhalb** eines Blocks definiert werden, stehen nach ihm bei einer Anfrage aus dem Cache nicht
  zur Verfügung; definieren Sie, was Sie nach dem Block brauchen, außerhalb davon.
- Ein Stylesheet-Link, den der Packer mit einem Zeitstempel im Namen schreibt, kann in einem cache-block eines
  Pagelayouts stehen: Leeren Sie nach einer Stylesheet-Änderung deshalb auch `template-block`.

## Wenn eine Änderung nicht erscheint

| Sie haben geändert | Leeren |
|---|---|
| Eine Template-Datei | nichts (wenn `TemplateCompile` Zeitstempel prüft), sonst `--clear-tag=template` |
| Eine neue Template-Datei hinzugefügt | `--clear-id=template-override` |
| Eine Override-Regel oder eine Design-Einstellung | `--clear-tag=ini`, `--clear-id=template-override,design_base` |
| Inhalte | nichts; das Veröffentlichen leert ihn |
| Ein Template innerhalb einer zwischengespeicherten Vollansicht | `--clear-tag=content` |
| Ein Template innerhalb eines `cache-block` | `--clear-id=template-block` |
| Ein Stylesheet oder Skript | den Packer: `--clear-id=ezjscore-packer`, dann `template-block` |

Auf einem Server mit Velocity führt `./console exp:velocity deploy` nach einer Änderung die richtigen Leerungen in der
richtigen Reihenfolge aus; siehe [Deployment](../../../anleitungen/bereitstellung.md).

## Verwandte Seiten

- Zurück: [Templates debuggen](templates-debuggen.md)
- Weiter: [Inhaltsklassen für Builder](inhaltsklassen-fuer-builder.md)
- [Caches](../../../anleitungen/caches.md)
- [Cache-Tags, Cache-Blöcke und das Leeren von Caches](../teil-8-referenz/cache-tags-und-leeren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
