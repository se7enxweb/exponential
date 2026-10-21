---
title: /setup/staticcachestream
audience: developer, admin
category: examples
summary: "Die Ansicht setup/staticcachestream: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, staticcachestream, setup/staticcachestream, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/staticcachestream

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/staticcachestream.php`.

## Adresse

```text
/setup/staticcachestream/(siteaccess)/<SiteAccess>/(maxpages)/<MaxPages>/(maxdepth)/<MaxDepth>/(purge)/<Purge>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(siteaccess)` setzt `SiteAccess`, `(maxpages)` setzt `MaxPages`, `(maxdepth)` setzt `MaxDepth`, `(purge)` setzt `Purge`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/managecache`](../../richtlinien/setup.md#managecache)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `ajax` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/staticcachestream'|ezurl}>setup/staticcachestream</a>
<a href={concat( '/setup/staticcachestream/(siteaccess)/', 1 )|ezurl}>setup/staticcachestream</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/staticcachestream' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/staticcachestream.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
