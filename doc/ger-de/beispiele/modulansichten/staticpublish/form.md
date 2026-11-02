---
title: /staticpublish/form
audience: developer, admin
category: examples
summary: "Die Ansicht staticpublish/form: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, staticpublish, form, staticpublish/form, module, view, URL
type: generated
generated: exp:help:examples
---
# /staticpublish/form

Eine Ansicht des Moduls `staticpublish`, definiert in `kernel/staticpublish/module.php` und ausgeführt von `kernel/staticpublish/form.php`.

## Adresse

```text
/staticpublish/form
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `staticpublish/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/staticpublish/form'|ezurl}>staticpublish/form</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/staticpublish/form' );
```

## Siehe auch

- [Alle Ansichten von staticpublish](README.md)
- [Die Richtlinien von staticpublish](../../richtlinien/staticpublish.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/staticpublish/module.php`, `kernel/staticpublish/form.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
