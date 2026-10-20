---
title: /setup/systemupgrade
audience: developer, admin
category: examples
summary: "Die Ansicht setup/systemupgrade: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, systemupgrade, setup/systemupgrade, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/systemupgrade

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/systemupgrade.php`.

## Adresse

```text
/setup/systemupgrade
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/setup`](../../richtlinien/setup.md#setup)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | MD5CheckButton: MD5Check, DBCheckButton: DBCheck, DownloadFileReportButton: DownloadFileReport, DownloadSchemaReportButton: DownloadSchemaReport |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/systemupgrade'|ezurl}>setup/systemupgrade</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/systemupgrade' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/systemupgrade.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
