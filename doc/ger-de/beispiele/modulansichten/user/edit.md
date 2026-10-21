---
title: /user/edit
audience: developer, admin
category: examples
summary: "Die Ansicht user/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, user, edit, user/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /user/edit

Eine Ansicht des Moduls `user`, definiert in `kernel/user/module.php` und ausgeführt von `kernel/user/edit.php`.

## Adresse

```text
/user/edit/<UserID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `UserID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`user/login`](../../richtlinien/user.md#login)

Eine Richtlinie auf `user/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | ChangePasswordButton: ChangePassword, ChangeSettingButton: ChangeSetting, CancelButton: Cancel, EditButton: Edit |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/user/edit/1'|ezurl}>user/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/user/edit/1' );
```

## Siehe auch

- [Alle Ansichten von user](README.md)
- [Die Richtlinien von user](../../richtlinien/user.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/user/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
