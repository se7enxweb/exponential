---
title: /audit/console
audience: developer, admin
category: examples
summary: "Die Ansicht audit/console: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Konsole, Modul, Ansicht, audit, console, audit/console, module, view, URL
type: generated
generated: exp:help:examples
---
# /audit/console

Eine Ansicht des Moduls `audit`, definiert in `kernel/audit/module.php` und ausgeführt von `kernel/audit/console.php`.

## Adresse

```text
/audit/console/(channel)/<Channel>/(name)/<Name>/(user)/<User>/(login)/<Login>/(object)/<Object>/(target)/<Target>/(result)/<Result>/(severity)/<Severity>/(request)/<Request>/(job)/<Job>/(run)/<Run>/(ip)/<IP>/(from)/<From>/(to)/<To>/(q)/<Q>/(legacy_file)/<LegacyFile>/(parent)/<Parent>/(offset)/<Offset>/(limit)/<Limit>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(channel)` setzt `Channel`, `(name)` setzt `Name`, `(user)` setzt `User`, `(login)` setzt `Login`, `(object)` setzt `Object`, `(target)` setzt `Target`, `(result)` setzt `Result`, `(severity)` setzt `Severity`, `(request)` setzt `Request`, `(job)` setzt `Job`, `(run)` setzt `Run`, `(ip)` setzt `IP`, `(from)` setzt `From`, `(to)` setzt `To`, `(q)` setzt `Q`, `(legacy_file)` setzt `LegacyFile`, `(parent)` setzt `Parent`, `(offset)` setzt `Offset`, `(limit)` setzt `Limit`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`audit/read`](../../richtlinien/audit.md#read)

Eine Richtlinie auf `audit/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `expauditnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/audit/console'|ezurl}>audit/console</a>
<a href={concat( '/audit/console/(channel)/', 1 )|ezurl}>audit/console</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/audit/console' );
```

## Siehe auch

- [Alle Ansichten von audit](README.md)
- [Die Richtlinien von audit](../../richtlinien/audit.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/module.php`, `kernel/audit/console.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
