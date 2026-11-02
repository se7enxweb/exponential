---
title: /migration/remote
audience: developer, admin
category: examples
summary: "Die Ansicht migration/remote: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, migration, remote, migration/remote, module, view, URL
type: generated
generated: exp:help:examples
---
# /migration/remote

Eine Ansicht des Moduls `migration`, definiert in `kernel/migration/module.php` und ausgeführt von `kernel/migration/remote.php`.

## Adresse

```text
/migration/remote/<RemoteID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RemoteID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`migration/remotes`](../../richtlinien/migration.md#remotes)

Eine Richtlinie auf `migration/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | ReviewButton: Review, SaveButton: Save, EditButton: Edit, TestButton: Test, RemoveButton: Remove |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/migration/remote/1'|ezurl}>migration/remote</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/migration/remote/1' );
```

## Siehe auch

- [Alle Ansichten von migration](README.md)
- [Die Richtlinien von migration](../../richtlinien/migration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`, `kernel/migration/remote.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
