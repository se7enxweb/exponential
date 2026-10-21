---
title: /user/unactivated
audience: developer, admin
category: examples
summary: "Die Ansicht user/unactivated: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, user, unactivated, user/unactivated, module, view, URL
type: generated
generated: exp:help:examples
---
# /user/unactivated

Eine Ansicht des Moduls `user`, definiert in `kernel/user/module.php` und ausgeführt von `kernel/user/unactivated.php`.

## Adresse

```text
/user/unactivated/<SortField>/<SortOrder>/(offset)/<Offset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SortField`, `SortOrder`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`user/activation`](../../richtlinien/user.md#activation)

Eine Richtlinie auf `user/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezusernavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | ActivateButton: ActivateUsers, RemoveButton: RemoveUsers, ResendButton: ResendActivation, RemoveAllButton: RemoveAllUsers |
| Parameter der POST-Aktionen | ActivateUsers (UserIDs: DeleteIDArray), RemoveUsers (UserIDs: DeleteIDArray), ResendActivation (UserIDs: DeleteIDArray) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/user/unactivated/sortfield/sortorder'|ezurl}>user/unactivated</a>
<a href={concat( '/user/unactivated/sortfield/sortorder/(offset)/', 1 )|ezurl}>user/unactivated</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/user/unactivated/sortfield/sortorder' );
```

## Siehe auch

- [Alle Ansichten von user](README.md)
- [Die Richtlinien von user](../../richtlinien/user.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/user/unactivated.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
