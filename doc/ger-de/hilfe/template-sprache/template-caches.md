---
title: Template-Caches
audience: developer, designer, app-creator
category: development
order: 410
summary: Jeder Cache, den ein Template berührt - kompilierte Templates, der Template-Block-Cache von cache-block (Schlüssel, expiry, subtree_expiry, ignore_content_expiry und wie der Name der Cache-Datei gebaut wird), der Content-View-Cache und sein Schlüssel, der Cache der Template-Overrides, die Caches für Design-Basen und INI - mit den IDs von ezcache, die jeden leeren, und dem, was exp:velocity deploy leert.
keywords: Template-Cache, cache-block, Template-Block-Cache, Schlüssel, expiry, subtree_expiry, ignore_content_expiry, View-Cache, ViewCaching, CachedViewModes, template-override, design_base, INI-Cache, ezcache, clear-id, exp:velocity deploy, template cache, keys, view cache, ini cache
type: help
---
# Template-Caches

Ein Template wird auf mehrere Arten zugleich gecacht, jede mit eigenem Schlüssel, eigenem Verfall und eigener Art, sie zu leeren. Wer weiß,
welcher Cache eine Ausgabe hält, hat bei „Meine Änderung wird nicht angezeigt“ den größten Teil der Arbeit getan. Diese Seite führt sie in
der Reihenfolge auf, in der eine Anfrage auf sie trifft.

## Inhalt

1. [Überblick](#überblick)
2. [Kompilierte Templates](#kompilierte-templates)
3. [Der Cache der Template-Overrides und der Cache der Design-Basen](#der-cache-der-template-overrides-und-der-cache-der-design-basen)
4. [Der Content-View-Cache](#der-content-view-cache)
5. [Der Template-Block-Cache (cache-block)](#der-template-block-cache-cache-block)
6. [Der INI-Cache](#der-ini-cache)
7. [Leeren, und was ein Deploy leert](#leeren-und-was-ein-deploy-leert)
8. [Verwandte Seiten](#verwandte-seiten)

## Überblick

Die Caches, die der Kernel kennt, stehen in `eZCache::fetchList()` (`kernel/classes/ezcache.php`); jeder hat eine ID für
`php bin/php/ezcache.php --clear-id=<id>` und Tags für `--clear-tag=<tag>`:

| Cache | ID | Tags | Ordner unter `var/<site>/cache/` | Eingeschaltet durch |
|---|---|---|---|---|
| Kompilierte Templates | `template` | template | `template/` | `[TemplateSettings] TemplateCompile` |
| Template-Block (cache-block) | `template-block` | template, content | `template-block/` | `[TemplateSettings] TemplateCache` |
| Template-Override | `template-override` | template | `override/` | immer |
| Content-View | `content` | content | `[ContentSettings] CacheDir` (`content`) | `[ContentSettings] ViewCaching` |
| Liste der Design-Basen | `design_base` | template | Cache-Dateien der Design-Basen | `[DesignSettings] DesignLocationCache` |
| INI | `ini`, `global_ini` | ini | `ini/` und `var/cache/ini` | immer |
| Übersetzung | `translation` | i18n | | immer |

`php bin/php/ezcache.php --list-ids` und `--list-tags` geben die vollständige Liste einer Installation aus, mit den Caches der Erweiterungen.
Die template-bezogenen Einstellungen auf dem Siteaccess `site` von alpha:

```tpl
TemplateCompile:  {ezini( 'TemplateSettings', 'TemplateCompile' )}
DevelopmentMode:  {ezini( 'TemplateSettings', 'DevelopmentMode' )}
TemplateCache:    {ezini( 'TemplateSettings', 'TemplateCache' )}
NodeTreeCaching:  {ezini( 'TemplateSettings', 'NodeTreeCaching' )}
ViewCaching:      {ezini( 'ContentSettings', 'ViewCaching' )}
CachedViewModes:  {ezini( 'ContentSettings', 'CachedViewModes' )}
```

Ausgabe auf alpha:

```text
TemplateCompile:  enabled
DevelopmentMode:  disabled
TemplateCache:    enabled
NodeTreeCaching:  disabled
ViewCaching:      enabled
CachedViewModes:  full;sitemap;pdf;googlesitemap
```

## Kompilierte Templates

Die PHP-Dateien des [Template-Compilers](der-template-compiler.md), eine pro Template, Siteaccess und Locale. Ein kompiliertes Template wird neu
gebaut, wenn seine eigene Datei neuer ist; ein eingebundenes Template wird nur mit `DevelopmentMode=enabled` auf eine neuere Datei geprüft.
Leeren mit `--clear-id=template`.

## Der Cache der Template-Overrides und der Cache der Design-Basen

Die [Design-Ressource](template-ressourcen-und-overrides.md) hält zwei Nachschlagelisten:

- den **Override-Cache** (`override/`): die Match-Regeln von `override.ini` pro Template, gebaut von
  `eZTemplateDesignResource::createOverrideCache()`. Eine **neue** Override-Regel oder eine neue Template-Datei wird erst gesehen, wenn er geleert
  wird (`--clear-id=template-override`); ein geändertes vorhandenes Template schon.
- den **Cache der Design-Basen**: die geordnete Liste der Design-Ordner des Siteaccess, geschrieben bei `DesignLocationCache=enabled`
  (`eZTemplateDesignResource::allDesignBases()`). Leeren Sie ihn (`--clear-id=design_base`), nachdem Sie einer Erweiterung ein Design
  hinzugefügt oder eine Erweiterung mit Design eingeschaltet haben.

## Der Content-View-Cache

`content/view` speichert das ganze `$Result` einer Knotenansicht (HTML, `content_info`, die persistente Variable, die Liste der genutzten
Templates) für die Ansichtsmodi in `[ContentSettings] CachedViewModes` (`full;sitemap;pdf;googlesitemap` auf alpha). Der Name der
Cache-Datei ist ein Hash aus (`eZNodeviewfunctions::generateViewCacheFile()`):

- Knoten-ID, Ansichtsmodus, Sprache, Offset, Layout;
- den Rollen-IDs des Benutzers, den Werten beschränkter Zuweisungen und den Rabattregeln (jedes lässt sich mit einem Wert von
  `ViewCacheTweaks` weglassen: `ignore_userroles`, `ignore_userlimitedlist`, `ignore_discountlist`);
- der Index-Datei, dem Siteaccess (außer bei `ignore_siteaccess_name`) und, bei URI-Siteaccesses, der Zugriffsart;
- den [Ansichtsparametern](ansichtsparameter.md) (außer bei `ignore_viewparameters`);
- in `CachedViewPreferences` für den Ansichtsmodus aufgeführten Benutzereinstellungen;
- Schlüsseln, die eine Erweiterung über den Event-Filter `content/view/cachekeys` hinzufügt.

Er verfällt, wenn Inhalte veröffentlicht werden (der Cache-Manager leert den Knoten und die mit ihm verwandten Knoten nach den Regeln von
`viewcache.ini`), wenn sich der globale Verfallszeitstempel `content-view-cache` verschiebt und wenn eines der Templates in `template_list`
neuer ist als die Cache-Datei. Ein Knoten-Template kann seine Lebensdauer mit `{set-block scope=root variable=cache_ttl}0{/set-block}` verkürzen
(wie es die Templates von cjw_newsletter tun): Auf `0` gesetztes `$cache_ttl` beendet das Caching dieser Seite, ein positiver Wert ist eine
Lebensdauer in Sekunden. Leeren mit `--clear-id=content`.

## Der Template-Block-Cache (cache-block)

`cache-block` speichert die Ausgabe eines Teils eines Templates (`lib/eztemplate/classes/eztemplatecachefunction.php`,
`eztemplatecacheblock.php`). Nicht auf alpha ausgeführt: Das Rendern eines cache-block schreibt eine Cache-Datei, dieses Beispiel wird also ohne
Ausgabe gezeigt:

```tpl
{cache-block keys=array( $node.node_id, $view_parameters.offset ) subtree_expiry=$node.node_id expiry=3600}
    {foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 10 ) ) as $child}
        {node_view_gui content_node=$child view='line'}
    {/foreach}
{/cache-block}
```

| Parameter | Wirkung |
|---|---|
| `keys` | Werte, nach denen sich der Cache unterscheidet; beliebig, Arrays werden durchlaufen |
| `expiry` | Lebensdauer in Sekunden; Standard 7200 (`eZTemplateCacheFunction::DEFAULT_TTL`); `0` bedeutet keine Zeitgrenze |
| `ignore_content_expiry` | nicht verfallen, wenn irgendein Inhalt veröffentlicht wird |
| `subtree_expiry` | eine Knoten-ID, ein URL-Alias oder `content/view/full/<id>`; der Block verfällt, wenn Inhalte unter diesem Knoten veröffentlicht werden; schließt `ignore_content_expiry` ein |

Wie der Cache gefunden wird:

- Der Schlüssel enthält immer die **Position** des Tags (`eZTemplateCacheBlock::placementString()`: Anfangszeile und -spalte, Endzeile und
  -spalte, Template-Datei) und den Namen des **Siteaccess**, plus den Wert von `keys`, wenn angegeben. Zwei cache-blocks teilen nie eine Datei,
  und das Verschieben eines cache-block in seinem Template legt eine neue Datei an.
- Der Dateiname ist der md5 dieses Schlüssels, gespeichert in `template-block/<a>/<b>/<c>/<md5>.cache`, oder bei `subtree_expiry` unter dem
  Teilbaumordner des Knotens.
- Ohne `ignore_content_expiry` lässt jede Veröffentlichung jeden solchen Block verfallen: Das Veröffentlichen setzt den Zeitstempel
  `template-block-cache` (`eZContentObject::expireTemplateBlockCache()`). Der globale Zeitstempel `global-template-block-cache`, den das Leeren
  des Cache verschiebt, lässt alle Blöcke verfallen.
- Mit `TemplateCache=disabled` wird der Inhalt des Blocks jedes Mal gerendert, als gäbe es das Tag nicht.

Was daraus folgt:

- Alles, was sich innerhalb eines Blocks pro Besucher, pro Rolle oder pro Anfrage unterscheidet, muss in `keys` stehen, sonst wird es auch dem
  nächsten Besucher gezeigt. `$current_user.contentobject_id` in den Schlüsseln ergibt einen Block pro Benutzer.
- `rand`, `currentdate` und ähnliche Werte frieren ein, bis der Block verfällt.
- Ein `def` innerhalb eines Blocks läuft bei einem Cache-Treffer nicht: Variablen, die der Rest des Templates braucht, müssen außerhalb
  definiert werden.

Die Anleitung für Designer ist [Cache-Block](../../../eng-us/help/designers/templates/cache-block.md).

## Der INI-Cache

Einstellungsdateien werden als PHP in `var/cache/ini` (`global_ini`) und im Ordner `ini` des Website-Cache gecacht. Eine Änderung an einer Datei
`.ini` einer Erweiterung oder in `settings/` wird nach `--clear-tag=ini` gesehen; die Templates, die Einstellungen mit `ezini()` lesen,
bekommen dann den neuen Wert.

## Leeren, und was ein Deploy leert

```text
php bin/php/ezcache.php --clear-id=template,template-override --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
```

`./console exp:velocity deploy` (`kernel/classes/expvelocitydeploy.php`) leert in dieser Reihenfolge: die Caches `template`, `template-override`,
`translation` und `design_base`, dann, nach dem Neuladen von PHP und dem Neustart von Velocity, `content`, `exphttpcache`, `ezjscore-packer`
(mit `--packer`) und `template-block`. Die Reihenfolge zählt: Eine Seite, die zwischen einem Leeren und einem Neuladen mit dem alten Code
gerendert würde, käme wieder in den Cache.

Was Sie nach welcher Änderung leeren:

| Sie haben geändert | Leeren |
|---|---|
| ein vorhandenes Template | nichts bei `DevelopmentMode=enabled`; sonst `template`, und `content` / `template-block` für gecachte Ausgabe |
| eine neue Template-Datei oder eine neue Override-Regel | `template-override` (und `template`) |
| einen Design-Ordner einer Erweiterung | `design_base`, `template-override` |
| eine Einstellung | Tag `ini` |
| Inhalte | nichts: Das Veröffentlichen lässt den View-Cache und den Template-Block-Cache verfallen |

## Verwandte Seiten

- [Der Template-Compiler](der-template-compiler.md)
- [Kompilierung und Cache von Templates](../../../eng-us/help/designers/templates/template-compilation-and-cache.md)
- [Cache-Block](../../../eng-us/help/designers/templates/cache-block.md)
- [Template-Ressourcen und Overrides](template-ressourcen-und-overrides.md)
- [Caching in Anwendungen](../../../eng-us/help/creators/app-creators/caching-in-applications.md)
- [ezcache](../../../eng-us/examples/commands/ezcache.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
