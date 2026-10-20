---
title: /user/forgotpassword
audience: developer, admin
category: examples
summary: "Die Ansicht user/forgotpassword: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, user, forgotpassword, user/forgotpassword, module, view, URL
type: generated
generated: exp:help:examples
---
# /user/forgotpassword

Eine Ansicht des Moduls `user`, definiert in `kernel/user/module.php` und ausgeführt von `kernel/user/forgotpassword.php`.

## Adresse

```text
/user/forgotpassword/<HashKey>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `HashKey`.

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Der Benutzer braucht diese Richtlinienfunktion:

- [`user/password`](../../richtlinien/user.md#password)

Eine Richtlinie auf `user/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | GenerateButton: Generate |
| Parameter der POST-Aktionen | Generate (Login: UserLogin, Email: UserEmail) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/user/forgotpassword/hashkey'|ezurl}>user/forgotpassword</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/user/forgotpassword/hashkey' );
```

## Siehe auch

- [Alle Ansichten von user](README.md)
- [Die Richtlinien von user](../../richtlinien/user.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/user/forgotpassword.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
