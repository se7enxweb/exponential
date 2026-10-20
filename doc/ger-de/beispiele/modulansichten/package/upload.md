---
title: /package/upload
audience: developer, admin
category: examples
summary: "Die Ansicht package/upload: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, package, upload, package/upload, module, view, URL
type: generated
generated: exp:help:examples
---
# /package/upload

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/upload.php`.

## Adresse

```text
/package/upload
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/import`](../../richtlinien/package.md#import)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | UploadPackageButton: UploadPackage, UploadCancelButton: UploadCancel |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/upload'|ezurl}>package/upload</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/upload' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/upload.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
