---
title: /oauth/authorize
audience: developer, admin
category: examples
summary: "Die Ansicht oauth/authorize: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, oauth, authorize, oauth/authorize, module, view, URL
type: generated
generated: exp:help:examples
---
# /oauth/authorize

Eine Ansicht des Moduls `oauth`, definiert in `kernel/private/modules/oauth/module.php` und ausgeführt von `kernel/private/modules/oauth/authorize.php`.

## Adresse

```text
/oauth/authorize
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `oauth/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/oauth/authorize'|ezurl}>oauth/authorize</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/oauth/authorize' );
```

## Siehe auch

- [Alle Ansichten von oauth](README.md)
- [Die Richtlinien von oauth](../../richtlinien/oauth.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauth/module.php`, `kernel/private/modules/oauth/authorize.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
