---
title: Caching
audience: developer, admin
category: examples
order: 120
summary: Die Caches von Exponential in einem Design und einer Erweiterung nutzen – der View-Cache und wann man ihn für ein einzelnes Template abschaltet, cache-block mit keys, expiry und subtree_expiry, ein eigener Cache einer Erweiterung in der Cache-Liste und das Leeren in der richtigen Reihenfolge unter PHP-FPM und Velocity.
keywords: Cache, Caching, View-Cache, Cache-Block, Template-Cache, Cache leeren, HTTP-Cache, Response-Cache von Velocity, cache, caching, view cache, ViewCaching, CachedViewModes, cache_ttl, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, template cache, TemplateCache, ezcache.php, clear-tag, clear-id, CacheItems, Cache_, HTTP cache, httpcache.ini, Velocity response cache
type: example
---
# Caching

Eine Seite von Exponential kann aus vier Schichten beantwortet werden, von außen nach innen:

| Schicht | Was sie enthält | Geleert |
|---|---|---|
| Der Response-Cache von Velocity | vollständige Antworten, für einige Sekunden | `./console exp:velocity cache clear` |
| Der HTTP-Cache (standardmäßig deaktiviert) | vollständige Seiten je Berechtigungskontext | `--clear-id=exphttpcache`; läuft beim Veröffentlichen ab |
| Der **View-Cache** | der gerenderte Inhalt einer Knotenansicht, je Benutzerrolle und Ansichtsparametern | läuft beim Veröffentlichen für den Knoten, seine Eltern und Relationen ab |
| **cache-block** | ein Teil eines Templates (ein Menü, eine Fußzeile) | über sein `expiry`, `subtree_expiry` oder den Ablauf der Inhalte |

Darunter machen kompilierte Templates, der INI-Cache sowie die Klassen- und Übersetzungscaches jede Anfrage
günstiger. Sie verwenden den View-Cache und `cache-block` in den Templates eines Designs, geben einer Erweiterung einen
eigenen Cache unter **Setup > Caches** und leeren nach einer Änderung das Richtige.

Voraussetzungen: ein Design, das Sie bearbeiten können (hier in der Beispielerweiterung,
[Ein Template-Override](template-override.md)).

## 1. Der View-Cache

`site.ini [ContentSettings] ViewCaching=enabled` (der Standard) speichert die Ausgabe von `content/view` für die
Ansichtsmodi in `CachedViewModes` (`full;sitemap;pdf`). Eine Ansicht wird je Knoten, Ansichtsmodus, Sprache, den Rollen
und Einschränkungen des Benutzers sowie den Ansichtsparametern zwischengespeichert; das Veröffentlichen eines Objekts
lässt die Ansichten seiner Knoten, ihrer Eltern und der verwandten Objekte ablaufen, die
`viewcache.ini [ViewCacheSettings]` nennt.

Ein Template, dessen Ausgabe nicht zwischengespeichert werden darf (es zeigt den Warenkorb des Besuchers oder die
Uhrzeit), schaltet den Cache für sich selbst ab:

```tpl
{* Not cached: shows what is in the visitor's basket *}
{set-block scope=root variable=cache_ttl}0{/set-block}
```

Ist `cache_ttl` auf eine Anzahl von Sekunden gesetzt, wird die Ansicht höchstens so lange zwischengespeichert. Für
einen einzelnen Knoten, ohne Templates anzufassen: `site.ini [ContentSettings] ViewCacheTweaks[<node id>]=disabled`
(oder `pr_user`, ein Cache je Benutzer).

## 2. cache-block

`cache-block` speichert zwischen, was zwischen seinen Tags steht. In einem Pagelayout oder einem eingebundenen
Template:

```tpl
{* The top menu: the same for everybody on this siteaccess, rebuilt when anything below node 2 is published *}
{cache-block keys=array( $access_type.name ) subtree_expiry=2}
    {def $menu = fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                                  'class_filter_type', 'include',
                                                  'class_filter_array', array( 'folder' ),
                                                  'sort_by', array( 'priority', true() ) ) )}
    <nav class="top-menu">
    {foreach $menu as $item}
        <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
    {/foreach}
    </nav>
    {undef $menu}
{/cache-block}

{* The footer: rebuilt every hour, not on publishing *}
{cache-block expiry=3600 ignore_content_expiry}
    {include uri='design:parts/footer.tpl'}
{/cache-block}
```

| Parameter | Bedeutung |
|---|---|
| `keys` | wovon die zwischengespeicherte Kopie abhängt; eine Kopie je unterschiedlichem Wert (ein Siteaccess, eine Knoten-ID, die Sprache des Benutzers). Ohne `keys` sind Template-Datei und Position der Schlüssel. |
| `expiry` | Sekunden, die eine Kopie lebt (Standard 7200); `0` für keine zeitliche Begrenzung |
| `subtree_expiry` | eine Knoten-ID oder ein URL-Alias: Die Kopie läuft ab, wenn unterhalb dieses Knotens etwas veröffentlicht wird, statt bei jeder Veröffentlichung |
| `ignore_content_expiry` | die Kopie läuft durch Veröffentlichen überhaupt nicht ab, nur über `expiry` |

Regeln, die einen Cache-Block korrekt halten:

- **Alles, was die Ausgabe verändert, muss in `keys` stehen**: der Knoten der Seite (`$node.node_id`), die Sprache des
  Benutzers, die Ansichtsparameter (`$view_parameters.offset`). Ein Block, dessen Ausgabe vom Benutzer abhängt (eine
  Begrüßung, der Warenkorb), gehört überhaupt nicht in einen Cache-Block.
- Variablen, die innerhalb eines Blocks definiert werden, sind nicht gesetzt, wenn die Kopie ausgeliefert wird:
  Definieren Sie, was der Rest des Templates braucht, außerhalb davon.
- `site.ini [TemplateSettings] TemplateCache=enabled` (der Standard) aktiviert cache-block; ist er deaktiviert, werden
  die Blöcke jedes Mal gerendert.

## 3. Ein eigener Cache einer Erweiterung

Eine Erweiterung, die Dateien unterhalb des Cache-Verzeichnisses ablegt, registriert sie, damit **Setup > Caches** den
Cache auflistet und `ezcache.php` ihn leert. In `extension/expexample/settings/site.ini.append.php`:

```ini
[Cache]
CacheItems[]=expexample

[Cache_expexample]
name=Example extension cache
id=expexample
tags[]=content
path=expexample
```

`path` ist relativ zum Cache-Verzeichnis der Website (`var/<site>/cache/expexample`); der Tag entscheidet, welche
Gruppe ihn ebenfalls leert (`--clear-tag=content` leert ihn nun mit). Ein Cache mit eigener Art des Leerens nennt
stattdessen eine `class` mit einer statischen Methode `clearCache()` (`[Cache_ezjscore]` von ezjscore mit
`class=ezjscCacheManager` ist ein Beispiel in dieser Version).

## 4. Leeren, und in welcher Reihenfolge

```bash
php bin/php/ezcache.php --clear-id=template-block,content --allow-root-user   # one cache or several, by id
php bin/php/ezcache.php --clear-tag=template --allow-root-user                 # every cache with a tag
php bin/php/ezcache.php --clear-all --allow-root-user                          # everything
./console exp:cache clear --id=content --dry-run --allow-root-user             # what would go, with sizes
./console exp:velocity cache clear                                            # Velocity's response cache
```

Nach einer **Template**-Änderung: nichts, solange der Template-Cache deaktiviert ist; ist er aktiviert,
`--clear-tag=template`; eine **neue** Template-Datei oder Override-Regel braucht zusätzlich
`--clear-id=template-override`. Nach einer **INI**-Änderung: `--clear-tag=ini` und ein Neustart von Velocity. Nach
einer **PHP**-Änderung kommt es auf die Reihenfolge an, denn eine Seite, die zwischen dem Leeren und dem Neuladen noch
vom alten Code gerendert wird, landet erneut im Cache: Laden Sie zuerst PHP-FPM neu und starten Sie Velocity neu,
leeren Sie danach Inhalte, Template-Blöcke und den Response-Cache. `exp:velocity deploy` tut genau das
([Deployment mit Velocity](velocity-deployment.md)).

## 5. Prüfen

- Ein Cache-Block funktioniert, wenn die zweite Anfrage einer Seite schneller ist und die Dateien des Blocks nach der
  ersten unterhalb von `var/<site>/cache/template-block/` erscheinen.
- `curl -sI https://example.com/ | grep -i x-exp-cache` zeigt die Antwort des HTTP-Caches, sobald er aktiviert ist
  (`HIT`, `MISS`, `BYPASS` mit dem Grund).
- **Setup > Caches** listet *Example extension cache* in seiner Gruppe auf, mit seiner Größe.

Für diese Seite geprüft (2026-10-18): Die Parameter von cache-block sind die, die
`lib/eztemplate/classes/eztemplatecachefunction.php` liest; `cache_ttl` wird von
`kernel/classes/eznodeviewfunctions.php` gelesen; die genannten Einstellungen stehen in den ausgelieferten Dateien
`settings/site.ini` und `settings/viewcache.ini`; die Registrierung des Caches folgt
`extension/ezjscore/settings/site.ini.append.php`. `bin/php/eztemplatecheck.php` meldet das Template aus Abschnitt 2
als gültig (Syntax).

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Ein Besucher sieht den Namen oder Warenkorb eines anderen | persönliche Ausgabe in einem Cache-Block oder in einer Ansicht ohne `cache_ttl` 0 |
| Ein Menü ist nach dem Veröffentlichen veraltet | `ignore_content_expiry`, oder `subtree_expiry` nennt einen Knoten, unterhalb dessen nichts veröffentlicht wurde |
| Jede Seite zeigt in einem Block denselben Inhalt | der Knoten der Seite fehlt in `keys` |
| Eine Änderung erscheint erst nach einigen Sekunden | der Response-Cache von Velocity; leeren Sie ihn oder warten Sie |
| Das Log meldet *Expiring all view cache* | eine Veröffentlichung betraf mehr als `[ContentSettings] CacheThreshold` Knoten; siehe [Caches](../../anleitungen/caches.md) |

## Abgeleitet aus

- `lib/eztemplate/classes/eztemplatecachefunction.php` (`keys`, `expiry`, `subtree_expiry`, `ignore_content_expiry`)
- `kernel/classes/eznodeviewfunctions.php` (`cache_ttl`), `settings/site.ini` (`[ContentSettings]`, `[TemplateSettings]`),
  `settings/viewcache.ini`
- `extension/ezjscore/settings/site.ini.append.php` (`[Cache]`, `[Cache_ezjscore]`)
- `doc/eng-us/guides/caches.md`, `doc/eng-us/bc/6.0/httpcache.md`, `doc/eng-us/install/08-serving-the-site.md` (8.3.14)

## Verwandte Seiten

- [Kochbuch](README.md)
- [Caches: was jeder enthält und wie man ihn leert](../../anleitungen/caches.md),
  [Cache-Konsole](../../../eng-us/bc/6.0/cache-console.md)
- [Der rollenbewusste HTTP-Cache](../../../eng-us/bc/6.0/httpcache.md),
  [Response-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md),
  [Generator für den statischen Cache](../../../eng-us/features/6.0/static-cache-generator.md)
- [INI-Einstellungen](../ini-einstellungen/README.md) für jede Cache-Einstellung
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
