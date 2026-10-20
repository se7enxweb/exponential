---
title: /content/copysubtree
audience: developer, admin
category: examples
summary: "Die Ansicht content/copysubtree: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, copysubtree, content/copysubtree, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/copysubtree

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/copysubtree.php`.

## Adresse

```text
/content/copysubtree/<NodeID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `NodeID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/create`](../../richtlinien/content.md#create)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | CopyButton: Copy, CancelButton: Cancel |
| POST-Aktionen | BrowseActionName |
| Parameter der POST-Aktionen | Copy (VersionChoice: VersionChoice, CreatorChoice: CreatorChoice, TimeChoice: TimeChoice) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/copysubtree/1'|ezurl}>content/copysubtree</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/copysubtree/1' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/copysubtree.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
