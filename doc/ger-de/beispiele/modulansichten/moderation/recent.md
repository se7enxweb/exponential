---
title: /moderation/recent
audience: developer, admin
category: examples
summary: "Die Ansicht moderation/recent: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, moderation, recent, moderation/recent, module, view, URL
type: generated
generated: exp:help:examples
---
# /moderation/recent

Eine Ansicht des Moduls `moderation`, definiert in `kernel/moderation/module.php` und ausgeführt von `kernel/moderation/recent.php`.

## Adresse

```text
/moderation/recent/(from)/<From>/(to)/<To>/(date)/<Date>/(class)/<Class>/(status)/<Status>/(hidden)/<Hidden>/(held)/<Held>/(section)/<Section>/(siteaccess)/<Siteaccess>/(creator)/<Creator>/(sort)/<Sort>/(order)/<Order>/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(from)` setzt `From`, `(to)` setzt `To`, `(date)` setzt `Date`, `(class)` setzt `Class`, `(status)` setzt `Status`, `(hidden)` setzt `Hidden`, `(held)` setzt `Held`, `(section)` setzt `Section`, `(siteaccess)` setzt `Siteaccess`, `(creator)` setzt `Creator`, `(sort)` setzt `Sort`, `(order)` setzt `Order`, `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`moderation/read`](../../richtlinien/moderation.md#read)

Eine Richtlinie auf `moderation/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/moderation/recent'|ezurl}>moderation/recent</a>
<a href={concat( '/moderation/recent/(from)/', 1 )|ezurl}>moderation/recent</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/moderation/recent' );
```

## Siehe auch

- [Alle Ansichten von moderation](README.md)
- [Die Richtlinien von moderation](../../richtlinien/moderation.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/moderation/module.php`, `kernel/moderation/recent.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
