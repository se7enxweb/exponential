---
title: Caching für Designer
audience: designer
category: theme-design
order: 640
summary: Die Caches, in denen die Templates eines Designers leben (View-Cache, Cache-Blöcke, kompilierte Templates, gepackte Assets, der Antwort-Cache von Velocity), was jeder behält, was ihn leert und die Template-Regeln, die zwischengespeicherte Seiten für jeden Besucher korrekt halten.
keywords: Caching, View-Cache, cache-block, ViewCaching, CachedViewModes, cache_ttl, ViewCacheTweaks, Template-Cache, Cache-Schlüssel, benutzerspezifische Inhalte, CSP-Nonce und Cache, veraltete Seiten, caching, view cache, template cache, cache keys, user specific content, csp nonce cache, stale pages
type: help
---
# Caching für Designer

Diese Seite ist für Designer, die schnelle Seiten möchten, die trotzdem für jeden Besucher korrekt sind. Exponential speichert auf
mehreren Ebenen zwischen; die meisten funktionieren, ohne dass der Designer etwas tut, aber Templates können sie stören oder von
ihnen gestört werden.

## Inhalt

1. [Die Ebenen](#die-ebenen)
2. [Der View-Cache](#der-view-cache)
3. [Cache-Blöcke](#cache-blöcke)
4. [Regeln für Templates](#regeln-für-templates)
5. [Leeren](#leeren)

## Die Ebenen

| Ebene | Enthält | Geleert |
|---|---|---|
| View-Cache | das HTML der Vollansicht eines Knotens (`$module_result.content`) | beim Veröffentlichen, für die betroffenen Knoten |
| Cache-Blöcke | Teile von Templates, die Sie in `{cache-block}` einschließen | wenn sich Schlüssel ändern, beim Ablauf, beim Veröffentlichen von Inhalten (sofern nicht anders angegeben) |
| Kompilierte Templates | in PHP umgewandelte Templates | wenn sich die Template-Datei ändert (mit Entwicklungsprüfungen) oder beim Leeren |
| Gepackte Assets | zusammengeführtes CSS und JS | eine neue Datei pro Änderung einer Quelle |
| Antwort-Cache von Velocity | ganze Antworten, auf Installationen mit dem Server Velocity | `./console exp:velocity cache clear` |

## Der View-Cache

`site.ini [ContentSettings]`:

```ini
ViewCaching=enabled
CachedViewModes=full;sitemap;pdf
```

Die Vollansicht eines Knotens wird pro Knoten, View-Modus, Sprache, Rollen und Einschränkungen des Benutzers, View-Parametern und
mehr zwischengespeichert (der Hash wird in `kernel/classes/eznodeviewfunctions.php` gebildet). Das Pagelayout wird **nicht**
zwischengespeichert; es läuft bei jeder Anfrage um den zwischengespeicherten Inhalt herum.

Folgen für Designer:

- Ein Vollansichts-Template darf nichts ausgeben, das für einen einzelnen Besucher spezifisch ist (seinen Namen, seinen Warenkorb),
  es sei denn, der Cache unterscheidet danach. Setzen Sie solche Dinge in das Pagelayout oder in einen Teil, der nicht
  zwischengespeichert wird.
- Eine Vollansicht kann mit der Template-Variablen `cache_ttl` ihre eigene Lebensdauer setzen, die der Kernel nach dem Rendern
  liest (`eznodeviewfunctions.php`). Der Veranstaltungskalender von ezwebin behält seine Seite 400 Sekunden:
  `{set-block scope=root variable=cache_ttl}400{/set-block}`. Ein Wert von `0` speichert nichts; verwenden Sie ihn selten, denn
  eine Seite, die nie zwischengespeichert wird, wird bei jeder Anfrage gerendert.
- Pro Knoten hat `ViewCacheTweaks[<node_id>]` Optionen wie `pr_user` (Cache pro Benutzer) und `disabled` (Kommentare in
  `settings/site.ini`).

## Cache-Blöcke

Schließen Sie aufwendige Teile des Pagelayouts (Menüs, Fußbereiche) in einen Cache-Block mit den Schlüsseln ein, die ihre Ausgabe
ändern:

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) )}
    {include uri='design:pagelayout/menu.tpl'}
{/cache-block}
```

Parameter (`lib/eztemplate/classes/eztemplatecachefunction.php`): `keys`, `expiry` (Sekunden; Standard 7200; `0` für keine
zeitliche Grenze), `ignore_content_expiry` (behalten, wenn Inhalte veröffentlicht werden), `subtree_expiry` (ablaufen, wenn unter
einem Knoten etwas veröffentlicht wird). Einzelheiten: [Cache-block](../templates/cache-block.md).

## Regeln für Templates

- **Alles, was die Ausgabe ändert, ist ein Schlüssel.** Die aktuelle Seite, die Rollen des Benutzers, die Sprache, der Zustand des
  Warenkorbs. Ein Menü, das die aktuelle Seite markiert, braucht die URI oder die Knoten-id in seinen Schlüsseln.
- **Setzen Sie nie Benutzerdaten in einen Block, ohne dass der Benutzer in den Schlüsseln steht** (oder die Rollenliste, wenn es nur
  nach Rolle variiert).
- **Geben Sie `csp_nonce()` nie in einem Cache-Block oder einem im View-Cache gespeicherten Template aus**: Die Nonce ändert sich bei
  jeder Anfrage.
- **Halten Sie Schlüssel klein.** Jeder unterschiedliche Schlüsselwert schreibt eine Cache-Datei. Das Admin-Pagelayout wandelt
  seinen Sektionsfilter deshalb in eine Zahl um („every distinct raw value would write its own cache file“).
- **Holen Sie in einem Block nichts mit fetch, was außerhalb gebraucht wird**: Ein Cache-Treffer überspringt das fetch.

## Leeren

| Nach | Leeren |
|---|---|
| einer Änderung an einem View-Template | `--clear-id=content` (und `template`, wenn kompilierte Templates nicht geprüft werden) |
| einer Änderung an einem Teil in einem Cache-Block | `--clear-id=template-block` |
| dem Hinzufügen von Templates oder Overrides | `--clear-id=template-override` |

Die vollständige Tabelle steht in [Der Packer-Cache und Deploys](packer-cache-und-deployment.md).

## Verwandte Seiten

- [Cache-block](../templates/cache-block.md)
- [Template-Kompilierung und Cache](../templates/template-kompilierung-und-cache.md)
- [Performance von Themes](theme-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
