---
title: /user/login
audience: developer, admin
category: examples
summary: "Die Ansicht user/login: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, user, login, user/login, module, view, URL
type: generated
generated: exp:help:examples
---
# /user/login

Eine Ansicht des Moduls `user`, definiert in `kernel/user/module.php` und ausgeführt von `kernel/user/login.php`.

## Adresse

```text
/user/login
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Der Benutzer braucht diese Richtlinienfunktion:

- [`user/login`](../../richtlinien/user.md#login)

Eine Richtlinie auf `user/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| UI-Kontext | `authentication` |
| Einzelne POST-Aktionen | LoginButton: Login |
| Parameter der POST-Aktionen | Login (UserLogin: Login, UserPassword: Password, UserRedirectURI: RedirectURI) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/user/login'|ezurl}>user/login</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/user/login' );
```

## Siehe auch

- [Alle Ansichten von user](README.md)
- [Die Richtlinien von user](../../richtlinien/user.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/user/login.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
