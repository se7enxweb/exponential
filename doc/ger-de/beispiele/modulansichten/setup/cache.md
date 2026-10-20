---
title: /setup/cache
audience: developer, admin
category: examples
summary: "Die Ansicht setup/cache: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, cache, setup/cache, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/cache

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/cache.php`.

## Adresse

```text
/setup/cache
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/managecache`](../../richtlinien/setup.md#managecache)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | ClearCacheButton: ClearCache, ClearAllCacheButton: ClearAllCache, ClearContentCacheButton: ClearContentCache, ClearINICacheButton: ClearINICache, ClearTemplateCacheButton: ClearTemplateCache, RegenerateStaticCacheButton: RegenerateStaticCache, ClearQueryCacheButton: ClearQueryCache, ClearHttpCacheButton: ClearHttpCache, ResetOPcacheButton: ResetOPcache, ClearAPCuButton: ClearAPCu |
| Parameter der POST-Aktionen | ClearCache (CacheList: CacheList), RegenerateStaticCache (StaticCacheSiteAccess: StaticCacheSiteAccess) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/cache'|ezurl}>setup/cache</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/cache' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/cache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
