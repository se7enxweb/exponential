---
title: /setup/cronjobs
audience: developer, admin
category: examples
summary: "Die Ansicht setup/cronjobs: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, cronjobs, setup/cronjobs, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/cronjobs

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/cronjobs.php`.

## Adresse

```text
/setup/cronjobs
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/managecronjobs`](../../richtlinien/setup.md#managecronjobs)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | LaunchCronjobButton: LaunchCronjob, LaunchCronjobScriptButton: LaunchCronjobScript, StopCronjobButton: StopCronjob, ClearCronjobLogButton: ClearCronjobLog, PauseCronjobButton: PauseCronjob, ResumeCronjobButton: ResumeCronjob |
| Parameter der POST-Aktionen | LaunchCronjob (CronjobPart: LaunchCronjobButton, CronjobSiteAccess: CronjobSiteAccess), LaunchCronjobScript (CronjobTarget: LaunchCronjobScriptButton, CronjobSiteAccess: CronjobSiteAccess), PauseCronjob (CronjobPart: PauseCronjobButton, CronjobPauseNote: CronjobPauseNote), ResumeCronjob (CronjobPart: ResumeCronjobButton, CronjobPauseNote: CronjobPauseNote) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/cronjobs'|ezurl}>setup/cronjobs</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/cronjobs' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/cronjobs.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
