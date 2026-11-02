---
title: /migration/deploy
audience: developer, admin
category: examples
summary: "Die Ansicht migration/deploy: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, migration, deploy, migration/deploy, module, view, URL
type: generated
generated: exp:help:examples
---
# /migration/deploy

Eine Ansicht des Moduls `migration`, definiert in `kernel/migration/module.php` und ausgeführt von `kernel/migration/deploy.php`.

## Adresse

```text
/migration/deploy
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`migration/read`](../../richtlinien/migration.md#read)

Eine Richtlinie auf `migration/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | PreflightButton: Preflight, DeployButton: Deploy |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/migration/deploy'|ezurl}>migration/deploy</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/migration/deploy' );
```

## Siehe auch

- [Alle Ansichten von migration](README.md)
- [Die Richtlinien von migration](../../richtlinien/migration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`, `kernel/migration/deploy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
