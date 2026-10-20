---
title: /user/setting
audience: developer, admin
category: examples
summary: "Die Ansicht user/setting: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Einstellung, Modul, Ansicht, user, setting, user/setting, module, view, URL
type: generated
generated: exp:help:examples
---
# /user/setting

Eine Ansicht des Moduls `user`, definiert in `kernel/user/module.php` und ausgeführt von `kernel/user/setting.php`.

## Adresse

```text
/user/setting/<UserID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `UserID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`user/preferences`](../../richtlinien/user.md#preferences)

Eine Richtlinie auf `user/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezusernavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/user/setting/1'|ezurl}>user/setting</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/user/setting/1' );
```

## Siehe auch

- [Alle Ansichten von user](README.md)
- [Die Richtlinien von user](../../richtlinien/user.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/user/setting.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
