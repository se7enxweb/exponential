---
title: /secrets/list
audience: developer, admin
category: examples
summary: "Die Ansicht secrets/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, secrets, list, secrets/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /secrets/list

Eine Ansicht des Moduls `secrets`, definiert in `kernel/secrets/module.php` und ausgeführt von `kernel/secrets/list.php`.

## Adresse

```text
/secrets/list/(scope)/<Scope>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(scope)` setzt `Scope`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`secrets/manage`](../../richtlinien/secrets.md#manage)

Eine Richtlinie auf `secrets/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/secrets/list'|ezurl}>secrets/list</a>
<a href={concat( '/secrets/list/(scope)/', 1 )|ezurl}>secrets/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/secrets/list' );
```

## Siehe auch

- [Alle Ansichten von secrets](README.md)
- [Die Richtlinien von secrets](../../richtlinien/secrets.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/secrets/module.php`, `kernel/secrets/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
