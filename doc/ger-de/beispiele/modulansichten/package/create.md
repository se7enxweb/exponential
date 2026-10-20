---
title: /package/create
audience: developer, admin
category: examples
summary: "Die Ansicht package/create: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, package, create, package/create, module, view, URL
type: generated
generated: exp:help:examples
---
# /package/create

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/create.php`.

## Adresse

```text
/package/create/(creator)/<Creator>/(node)/<NodeID>/(subtree)/<Subtree>/(classes)/<IncludeClasses>/(versions)/<Versions>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(creator)` setzt `Creator`, `(node)` setzt `NodeID`, `(subtree)` setzt `Subtree`, `(classes)` setzt `IncludeClasses`, `(versions)` setzt `Versions`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/create`](../../richtlinien/package.md#create)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | CreatePackageButton: CreatePackage, PackageStep: PackageStep |
| Parameter der POST-Aktionen | CreatePackage (CreatorItemID: CreatorItemID), PackageStep (CreatorItemID: CreatorItemID, CreatorStepID: CreatorStepID, PreviousStep: PreviousStepButton, NextStep: NextStepButton) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/create'|ezurl}>package/create</a>
<a href={concat( '/package/create/(creator)/', 1 )|ezurl}>package/create</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/create' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/create.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
