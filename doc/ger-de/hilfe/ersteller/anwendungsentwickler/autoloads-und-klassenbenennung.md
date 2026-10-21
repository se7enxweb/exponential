---
title: Autoloads und Klassenbenennung
audience: app-creator, developer
category: app-creation
order: 90
summary: Wie Exponential Ihre Klassen findet - die erzeugten Autoload-Arrays, wann Sie sie neu erzeugen, die Benennung neuer Klassen mit dem Präfix exp, Klassen mit Namensraum, Autoloading mit Composer und die Fehler, die ein veralteter Autoload verursacht.
keywords: Autoload, Klassenbenennung, Präfix, Namensraum, Klasse nicht gefunden, Velocity neu starten, Anwendungsentwickler, autoload, ezpgenerateautoloads, class naming, exp prefix, namespace, ezp_extension.php, Composer autoload, class not found, Velocity restart, app creator
type: help
---
# Autoloads und Klassenbenennung

Diese Seite ist für Anwendungsentwickler, die PHP-Klassen hinzufügen. Exponential findet Klassen über erzeugte
Klassen-Maps; eine Klasse, die nicht in der Map steht, existiert für die Anwendung nicht.

## Inhalt

1. [Die Klassen-Maps](#die-klassen-maps)
2. [Neu erzeugen](#neu-erzeugen)
3. [Benennung](#benennung)
4. [Klassen mit Namensraum](#klassen-mit-namensraum)
5. [Composer-Bibliotheken](#composer-bibliotheken)
6. [Wenn eine Klasse nicht gefunden wird](#wenn-eine-klasse-nicht-gefunden-wird)

## Die Klassen-Maps

| Map | Umfasst | Erzeugt durch |
|---|---|---|
| `autoload/ezp_kernel.php` | Kernel-Klassen | mitgeliefert; von Kernel-Entwicklern neu erzeugt |
| `autoload/ezp_extension.php` | Klassen aller Erweiterungen | `php bin/php/ezpgenerateautoloads.php -e` |
| Autoloader von Composer | Bibliotheken in `vendor/` | `composer install` |

Der Generator durchsucht die Erweiterungsverzeichnisse (einschließlich `AdditionalExtensionDirectories[]`) nach
PHP-Dateien und führt jede Klasse mit ihrer Datei auf ([10.7 Autoloads der Klassen](../../../installation/10-nach-der-installation.md#107-autoloads-der-klassen)).

## Neu erzeugen

```bash
php bin/php/ezpgenerateautoloads.php -e
```

Führen Sie das aus, nachdem Sie eine Klasse hinzugefügt, umbenannt oder verschoben haben. Unter Velocity behalten die
Worker die Map, mit der sie gestartet sind: Starten Sie sie neu (`exp:velocity deploy` erzeugt neu und startet in der
richtigen Reihenfolge neu). Eine neue Klasse, die in Einstellungen genannt wird (ein Handler, ein Datentyp), muss
ausgeliefert sein, bevor die Einstellung, die sie verwendet, live geht.

## Benennung

- Neue Klassen beginnen mit einem Präfix Ihrer Organisation oder Ihrer Erweiterung: `acmeDemoList`,
  `acmeDemoListService`. `sevenx` und `exp` sind die Präfixe von 7x und des Exponential-Projekts: `sevenx` ist das
  offizielle öffentliche PHP-Code-Präfix von 7x (ein Klassenname kann nicht mit einer Ziffer beginnen, daher kann "7x"
  keines sein), und 7x verwendet `exp` bewusst für Arbeit, die es dem Exponential-Projekt und der Exponential
  Foundation überträgt. Verwenden Sie `exp` nur für Code, den Sie zum Projekt beitragen. Siehe
  [Benennungspräfixe: sevenx und exp](../../../../eng-us/standards/naming-prefixes-sevenx-and-exp.md).
- Eine Klasse pro Datei; Dateiname in Kleinbuchstaben: `classes/acmedemolist.php`.
- Verwenden Sie für eine neue Klasse nie einen Kernel-Namen (`eZ...`) wieder; benennen Sie nie einen bestehenden
  Kernel-Bezeichner um.

Siehe [Schichten und Benennung](../../../../eng-us/standards/architecture/layering-and-naming.md) und
[Coding-Standards](../../../../eng-us/standards/coding-standards.md).

## Klassen mit Namensraum

Ausführbare Klassen (Befehle, Cronjob-Teile, Modulansichten) verwenden Namensräume:
`Exponential\Command\Extension\<Ext>\<Name>`, `Exponential\Cronjob\Extension\<Ext>\<Name>`,
`Exponential\View\Extension\<Ext>\<Module>\<View>`, in `extension/<ext>/classes/runnable/...`
([Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).
Der Generator erfasst sie wie jede andere Klasse.

## Composer-Bibliotheken

Eine Erweiterung, die eine Bibliothek braucht, deklariert sie in ihrer eigenen `composer.json`; Composer der
Installation installiert sie nach `vendor/`. Liefern Sie `vendor/` nicht innerhalb der Erweiterung aus
([Composer-Pakete und Packagist](composer-und-packagist.md)).

## Wenn eine Klasse nicht gefunden wird

| Symptom | Ursache | Lösung |
|---|---|---|
| `Class "expDemoList" not found` | Map nicht neu erzeugt | `ezpgenerateautoloads.php -e` |
| Funktioniert unter Apache, scheitert unter Velocity | Worker wurden gestartet, bevor es die Klasse gab | `exp:velocity restart` |
| Eine Einstellung nennt eine Klasse, und die Website bricht | die Klasse wurde nach der Einstellung ausgeliefert | zuerst den Code ausliefern; mit `class_exists()` absichern, wo eine fehlende Klasse keine Seiten zerstören darf |
| Der Generator warnt vor einem Duplikat | zwei Dateien deklarieren dieselbe Klasse | eine davon umbenennen |

## Verwandte Seiten

- [Aufbau einer Erweiterung](../../../../eng-us/help/creators/app-creators/extension-anatomy.md)
- [Eine Entwicklungsumgebung einrichten](entwicklungsumgebung.md)
- [Zusätzliche Erweiterungsverzeichnisse](../../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
