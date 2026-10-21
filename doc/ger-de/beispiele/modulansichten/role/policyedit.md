---
title: /role/policyedit
audience: developer, admin
category: examples
summary: "Die Ansicht role/policyedit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Rolle, Modul, Ansicht, role, policyedit, role/policyedit, module, view, URL
type: generated
generated: exp:help:examples
---
# /role/policyedit

Eine Ansicht des Moduls `role`, definiert in `kernel/role/module.php` und ausgeführt von `kernel/role/policyedit.php`.

## Adresse

```text
/role/policyedit/<PolicyID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `PolicyID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `role/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezusernavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/role/policyedit/1'|ezurl}>role/policyedit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/role/policyedit/1' );
```

## Siehe auch

- [Alle Ansichten von role](README.md)
- [Die Richtlinien von role](../../richtlinien/role.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/module.php`, `kernel/role/policyedit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
