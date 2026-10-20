---
title: /audit/alerts
audience: developer, admin
category: examples
summary: "Die Ansicht audit/alerts: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, audit, alerts, audit/alerts, module, view, URL
type: generated
generated: exp:help:examples
---
# /audit/alerts

Eine Ansicht des Moduls `audit`, definiert in `kernel/audit/module.php` und ausgeführt von `kernel/audit/alerts.php`.

## Adresse

```text
/audit/alerts/(offset)/<Offset>/(state)/<State>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(state)` setzt `State`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`audit/read`](../../richtlinien/audit.md#read)

Eine Richtlinie auf `audit/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `expauditnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/audit/alerts'|ezurl}>audit/alerts</a>
<a href={concat( '/audit/alerts/(offset)/', 1 )|ezurl}>audit/alerts</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/audit/alerts' );
```

## Siehe auch

- [Alle Ansichten von audit](README.md)
- [Die Richtlinien von audit](../../richtlinien/audit.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/module.php`, `kernel/audit/alerts.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
