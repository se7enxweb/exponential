---
title: /package/install
audience: developer, admin
category: examples
summary: "Die Ansicht package/install: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, package, install, package/install, module, view, URL
type: generated
generated: exp:help:examples
---
# /package/install

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/install.php`.

## Adresse

```text
/package/install/<PackageName>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `PackageName`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/install`](../../richtlinien/package.md#install)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | HandleError: HandleError, InstallPackageButton: InstallPackage, PackageStep: PackageStep, SkipPackageButton: SkipPackage |
| Parameter der POST-Aktionen | InstallPackage (InstallerType: InstallerType, AsDrafts: AsDrafts, DraftsExisting: DraftsExisting, Background: Background), PackageStep (InstallerType: InstallerType, InstallStepID: InstallStepID, PreviousStep: PreviousStepButton, NextStep: NextStepButton), HandleError (ActionID: ActionID, RememberAction: RememberAction) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/install/packagename'|ezurl}>package/install</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/install/packagename' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/install.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
