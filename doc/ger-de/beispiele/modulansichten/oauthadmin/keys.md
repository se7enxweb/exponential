---
title: /oauthadmin/keys
audience: developer, admin
category: examples
summary: "Die Ansicht oauthadmin/keys: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, oauthadmin, keys, oauthadmin/keys, module, view, URL
type: generated
generated: exp:help:examples
---
# /oauthadmin/keys

Eine Ansicht des Moduls `oauthadmin`, definiert in `kernel/private/modules/oauthadmin/module.php` und ausgeführt von `kernel/private/modules/oauthadmin/keys.php`.

## Adresse

```text
/oauthadmin/keys/(status)/<Status>/(user)/<UserID>/(offset)/<Offset>/(q)/<Query>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(status)` setzt `Status`, `(user)` setzt `UserID`, `(offset)` setzt `Offset`, `(q)` setzt `Query`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `oauthadmin/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/oauthadmin/keys'|ezurl}>oauthadmin/keys</a>
<a href={concat( '/oauthadmin/keys/(status)/', 1 )|ezurl}>oauthadmin/keys</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/oauthadmin/keys' );
```

## Siehe auch

- [Alle Ansichten von oauthadmin](README.md)
- [Die Richtlinien von oauthadmin](../../richtlinien/oauthadmin.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauthadmin/module.php`, `kernel/private/modules/oauthadmin/keys.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
