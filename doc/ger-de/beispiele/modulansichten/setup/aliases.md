---
title: /setup/aliases
audience: developer, admin
category: examples
summary: "Die Ansicht setup/aliases: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, aliases, setup/aliases, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/aliases

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/aliases.php`.

## Adresse

```text
/setup/aliases/(tab)/<Tab>/(job)/<Job>/(event)/<Event>/(run)/<Run>/(alias)/<Alias>/(text)/<Text>/(lines)/<Lines>/(node)/<Node>/(object)/<Object>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(tab)` setzt `Tab`, `(job)` setzt `Job`, `(event)` setzt `Event`, `(run)` setzt `Run`, `(alias)` setzt `Alias`, `(text)` setzt `Text`, `(lines)` setzt `Lines`, `(node)` setzt `Node`, `(object)` setzt `Object`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/aliases`](../../richtlinien/setup.md#aliases)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | SaveSettingsButton: SaveSettings, PreviewSettingsButton: PreviewSettings, StartRunButton: StartRun, PauseBulkButton: PauseBulk, ResumeBulkButton: ResumeBulk, CancelJobButton: CancelJob, TestImageButton: TestImage, MakeAliasButton: MakeAlias, TestSourceButton: TestSource, ResetSourceButton: ResetSource, RotateKeyButton: RotateKey, NodeActionButton: NodeAction |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/aliases'|ezurl}>setup/aliases</a>
<a href={concat( '/setup/aliases/(tab)/', 1 )|ezurl}>setup/aliases</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/aliases' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/aliases.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
