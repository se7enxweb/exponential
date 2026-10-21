---
title: /audit/archives
audience: developer, admin
category: examples
summary: "Die Ansicht audit/archives: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, audit, archives, audit/archives, module, view, URL
type: generated
generated: exp:help:examples
---
# /audit/archives

Eine Ansicht des Moduls `audit`, definiert in `kernel/audit/module.php` und ausgeführt von `kernel/audit/archives.php`.

## Adresse

```text
/audit/archives/(channel)/<Channel>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(channel)` setzt `Channel`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`audit/manage`](../../richtlinien/audit.md#manage)

Eine Richtlinie auf `audit/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `expauditnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/audit/archives'|ezurl}>audit/archives</a>
<a href={concat( '/audit/archives/(channel)/', 1 )|ezurl}>audit/archives</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/audit/archives' );
```

## Siehe auch

- [Alle Ansichten von audit](README.md)
- [Die Richtlinien von audit](../../richtlinien/audit.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/module.php`, `kernel/audit/archives.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
