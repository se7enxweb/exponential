---
title: /apikey/list
audience: developer, admin
category: examples
summary: "Die Ansicht apikey/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, apikey, list, apikey/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /apikey/list

Eine Ansicht des Moduls `apikey`, definiert in `kernel/apikey/module.php` und ausgeführt von `kernel/apikey/list.php`.

## Adresse

```text
/apikey/list
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `apikey/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |
| Einzelne POST-Aktionen | CreateKeyButton: CreateKey, RevokeKeyButton: RevokeKey, ConfirmRevokeKeyButton: ConfirmRevokeKey |
| Parameter der POST-Aktionen | CreateKey (Name: ApiKeyName, Scopes: ApiKeyScopes, Expiry: ApiKeyExpiry, Nonce: ApiKeyNonce), RevokeKey (KeyID: RevokeKeyButton), ConfirmRevokeKey (KeyID: RevokeKeyID) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/apikey/list'|ezurl}>apikey/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/apikey/list' );
```

## Siehe auch

- [Alle Ansichten von apikey](README.md)
- [Die Richtlinien von apikey](../../richtlinien/apikey.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/apikey/module.php`, `kernel/apikey/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
