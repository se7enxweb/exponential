---
title: /user/password
audience: developer, admin
category: examples
summary: "Die Ansicht user/password: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, user, password, user/password, module, view, URL
type: generated
generated: exp:help:examples
---
# /user/password

Eine Ansicht des Moduls `user`, definiert in `kernel/user/module.php` und ausgeführt von `kernel/user/password.php`.

## Adresse

```text
/user/password/<UserID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `UserID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`user/password`](../../richtlinien/user.md#password)

Eine Richtlinie auf `user/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/user/password/1'|ezurl}>user/password</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/user/password/1' );
```

## Siehe auch

- [Alle Ansichten von user](README.md)
- [Die Richtlinien von user](../../richtlinien/user.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/user/password.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
