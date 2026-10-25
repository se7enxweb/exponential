---
title: /expimageeditor/save
audience: developer, admin
category: examples
summary: "Die Ansicht expimageeditor/save: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, expimageeditor, save, expimageeditor/save, module, view, URL
type: generated
generated: exp:help:examples
---
# /expimageeditor/save

Eine Ansicht des Moduls `expimageeditor`, definiert in `extension/expimageeditor/modules/expimageeditor/module.php` und ausgeführt von `extension/expimageeditor/modules/expimageeditor/save.php`.

## Adresse

```text
/expimageeditor/save/<attribute_id>/<version>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `attribute_id`, `version`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`expimageeditor/edit`](../../richtlinien/expimageeditor.md#edit)

Eine Richtlinie auf `expimageeditor/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/expimageeditor/save/attribute-id/version'|ezurl}>expimageeditor/save</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/expimageeditor/save/attribute-id/version' );
```

## Siehe auch

- [Alle Ansichten von expimageeditor](README.md)
- [Die Richtlinien von expimageeditor](../../richtlinien/expimageeditor.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expimageeditor/modules/expimageeditor/module.php`, `extension/expimageeditor/modules/expimageeditor/save.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
