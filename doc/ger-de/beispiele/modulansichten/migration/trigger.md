---
title: /migration/trigger
audience: developer, admin
category: examples
summary: "Die Ansicht migration/trigger: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Auslöser, Modul, Ansicht, migration, trigger, migration/trigger, module, view, URL
type: generated
generated: exp:help:examples
---
# /migration/trigger

Eine Ansicht des Moduls `migration`, definiert in `kernel/migration/module.php` und ausgeführt von `kernel/migration/trigger.php`.

## Adresse

```text
/migration/trigger
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `migration/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/migration/trigger'|ezurl}>migration/trigger</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/migration/trigger' );
```

## Siehe auch

- [Alle Ansichten von migration](README.md)
- [Die Richtlinien von migration](../../richtlinien/migration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`, `kernel/migration/trigger.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
