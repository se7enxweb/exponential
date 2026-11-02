---
title: /moderation/action
audience: developer, admin
category: examples
summary: "Die Ansicht moderation/action: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, moderation, action, moderation/action, module, view, URL
type: generated
generated: exp:help:examples
---
# /moderation/action

Eine Ansicht des Moduls `moderation`, definiert in `kernel/moderation/module.php` und ausgeführt von `kernel/moderation/action.php`.

## Adresse

```text
/moderation/action
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`moderation/act`](../../richtlinien/moderation.md#act)

Eine Richtlinie auf `moderation/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |
| Einzelne POST-Aktionen | ApplyButton: Apply, ConfirmDestructiveButton: Confirm, CancelButton: Cancel |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/moderation/action'|ezurl}>moderation/action</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/moderation/action' );
```

## Siehe auch

- [Alle Ansichten von moderation](README.md)
- [Die Richtlinien von moderation](../../richtlinien/moderation.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/moderation/module.php`, `kernel/moderation/action.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
