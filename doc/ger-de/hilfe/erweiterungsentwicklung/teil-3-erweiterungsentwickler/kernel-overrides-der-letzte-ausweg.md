---
title: "Kernel-Klassen-Overrides: der letzte Ausweg"
audience: developer, app-creator
category: extension-development
order: 300
summary: Eine Kernel-Klasse über die Override-Autoload-Zuordnung ersetzen - wie es funktioniert, was es kostet, der Assistent und seine Abweichungsprüfung, und die leichteren Mechanismen, die Sie zuerst versuchen sollten.
keywords: Kernel-Override, Override-Autoload, letzter Ausweg, Klasse ersetzen, Abweichungsprüfung, kernel override, override autoload, EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE, ezp_override.php, ezpgenerateautoloads -o, kernel_override, checkdrift, last resort, replace class
type: help
---
# Kernel-Klassen-Overrides: der letzte Ausweg

Teil 3, Kapitel 30. Wenn nichts anderes das Verhalten erreicht, das Sie ändern müssen, kann eine Erweiterung eine
**Kernel-Klasse** durch ihre eigene Kopie unter demselben Namen ersetzen. Das funktioniert, und es ist das Teuerste,
was Sie einer Installation antun können. Dieses Kapitel erklärt, wie es funktioniert und warum Sie es vermeiden
sollten.

## Wie es funktioniert

1. `config.php` erlaubt Overrides: `define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );` (`config.php-RECOMMENDED`
   liefert die Zeile auskommentiert mit, auf `false`).
2. Ihre Erweiterung enthält eine Datei, die die Kernel-Klasse deklariert, zum Beispiel
   `extension/mysite_override/kernel_override/ezcontentobjecttreenode.php` mit der Deklaration
   `class eZContentObjectTreeNode`.
3. `php bin/php/ezpgenerateautoloads.php -o` schreibt `var/autoload/ezp_override.php`, eine Zuordnung der Klassen,
   die Ihre Erweiterungen deklarieren und die auch der Kernel deklariert.
4. Der Autoloader (`autoload.php`) legt diese Zuordnung über die des Kernels, wenn die Konstante auf true steht, sodass
   Ihre Datei statt der des Kernels geladen wird.

## Was es kostet

Die README des Assistenten für Kernel-Overrides sagt es deutlich: "An override is not a subclass. There is no parent
to call and nothing is inherited: this file **is** the class as far as the rest of the system is concerned." Ein
Override ist also keine Unterklasse; es gibt keine Elternklasse, die man aufrufen könnte, und nichts wird geerbt: Für
den Rest des Systems **ist** diese Datei die Klasse. Deshalb:

- ist jede Korrektur, die der Kernel an dieser Klasse vornimmt, nachdem Sie sie kopiert haben, eine Korrektur, die
  Ihre Website nicht bekommt, bis es jemandem auffällt,
- lassen sich zwei Erweiterungen, die dieselbe Klasse überschreiben, nicht kombinieren; es gewinnt still diejenige,
  die der Generator zuletzt findet,
- kann ein Upgrade ändern, was andere Klassen von ihr erwarten, und Ihre Kopie bricht ohne jedes Anzeichen, bis sie
  verwendet wird.

## Der Assistent

**Setup > RAD > Kernel override** (`/setup/kerneloverride`, `kernel/setup/expkerneloverridewizard.php`) schreibt das
Override als vollständige Erweiterung: die Kopie in `kernel_override/`, eine README und `bin/checkdrift.php`, das die
aktuelle Datei des Kernels mit der kopierten vergleicht. Führen Sie es nach jedem Upgrade aus:

```bash
php extension/<name>/bin/checkdrift.php
```

Der Exit-Status `0` bedeutet, dass die Datei des Kernels unverändert ist, `1`, dass sie sich geändert hat und jemand
entscheiden muss, welche Änderungen das Override braucht, `2`, dass die Datei nicht mehr existiert. Der Assistent
weigert sich, ein zweites Override einer bereits überschriebenen Klasse zu schreiben.

## Bevor Sie es tun

Die README des Assistenten endet mit dem richtigen Rat: "Almost everything in this system has a lighter way in - a
handler named by a setting, an event listener, a filter, a template override." Fast alles in diesem System hat einen
leichteren Zugang. In der Reihenfolge der Vorliebe:

| Statt zu überschreiben | Versuchen Sie |
|---|---|
| ein Template | ein Template-Override ([Template-Overrides: die Grundlagen](../teil-2-builder/grundlagen-der-template-overrides.md)) |
| eine Klasse für Mail, Anmeldung, Session, Suche ... | einen Handler ([Handler, die über Einstellungen benannt werden](handler-ueber-einstellungen-benennen.md)) |
| Verhalten rund um Veröffentlichen, Caches, Ausgabe | einen Ereignis-Listener oder Filter ([Ereignisse, Listener und Filter](ereignisse-und-filter.md)) |
| die View eines Kernel-Moduls | eine Neuimplementierung der View (`site.ini [RunnableSettings] Implementation[]`) oder ein Modul einer Erweiterung, das das Modul ersetzt ([Eine Erweiterung kann ein Kernel-Modul ersetzen](../../../../eng-us/features/6.0/extension-module-override.md)) |
| das Verhalten eines Datentyps | Ihren eigenen Datentyp |
| einen Fehler im Kernel | einen Pull Request an den Kernel ([Mit Pull Requests zurückgeben](../teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)) |

Eine Korrektur, die Sie upstream einreichen, hilft allen und kostet Sie beim nächsten Upgrade nichts. Ein Override ist
nur für die Zeit die Antwort, bis die Korrektur veröffentlicht ist, und sollte danach entfernt werden.

## Overrides sehen

Die RAD-Übersicht (`/setup/radsurvey`) zählt auf einer Installation die "kernel classes replaced" (ersetzten
Kernel-Klassen), und
["Overriding kernel classes within an extension"](https://ezpedia.exponential.earth/en/solution/overriding_kernel_classes_within_an_extension)
in eZpedia beschreibt den Mechanismus aus den früheren Jahren.

## Verwandte Seiten

- Zurück: [Handler, die über Einstellungen benannt werden](handler-ueber-einstellungen-benennen.md)
- Weiter: [Sicherheit in Erweiterungen](../../../../eng-us/help/extension-development/part-3-extension-developer/security-in-extensions.md)
- [Mit RAD: Handler und Kernel-Overrides](../../../../eng-us/help/extension-development/part-3-extension-developer/tutorials/rad-handlers-and-kernel-overrides.md)
- [RAD-Werkzeuge](../../../../eng-us/features/6.0/rad-tools.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
