---
title: Klassenbenennung und das Präfix exp
audience: php-developer, developer
category: php-development
order: 150
summary: Wie Sie PHP-Klassen, Dateien, Funktionen, Einstellungen und Tabellen in Exponential benennen - Ihr eigenes Präfix, exp für Code, den Sie zum Projekt beitragen, sevenx für 7x, warum Kernel-Bezeichner eZ nie umbenannt werden, Dateinamen, Namespaces der Runnable-Klassen und worin der alte Produktname erscheinen darf und worin nicht.
keywords: Benennung, Klassennamen, Präfix, Dateinamen, Namespaces, Schichtung, Umbenennung, Bezeichner, naming, class names, prefix, exp prefix, sevenx prefix, eZ prefix, ezp, file names, namespaces, Exponential\Command, Exponential\View, Exponential\Cronjob, layering, rename, identifiers
type: help
---
# Klassenbenennung und das Präfix exp

Diese Seite richtet sich an PHP-Entwickler, die Klassen, Funktionen, Einstellungen oder Tabellen hinzufügen. Namen in
Exponential folgen einigen Regeln, die verhindern, dass Tausende von Klassen in einer Klassen-Map kollidieren, und die
vorhandene Erweiterungen funktionsfähig halten. Die maßgebliche Darstellung ist
[Namenspräfixe: sevenx und exp](../../standards/namenspraefixe-sevenx-und-exp.md).

## Inhalt

1. [Die Regeln in einer Tabelle](#die-regeln-in-einer-tabelle)
2. [Ihr Präfix](#ihr-präfix)
3. [exp und sevenx](#exp-und-sevenx)
4. [Kernel-Bezeichner eZ bleiben](#kernel-bezeichner-ez-bleiben)
5. [Dateien und Klassennamen](#dateien-und-klassennamen)
6. [Klassen mit Namespace](#klassen-mit-namespace)
7. [Andere Bezeichner](#andere-bezeichner)

## Die Regeln in einer Tabelle

| Sie schreiben | Präfix | Beispiel |
|---|---|---|
| Ihre eigene Erweiterung oder Ihren Projektcode | das Präfix Ihrer Organisation oder Erweiterung | `acmeReportService`, `acmereport` |
| Code, den Sie zum Projekt Exponential beitragen | `exp` | `expHelpAudience`, `expContentSecurityPolicy` |
| Code, den 7x als Unternehmen veröffentlicht | `sevenx` (oder `exp`, wie 7x entscheidet) | `sevenx_dse`, `sevenx_valkey_cache` |
| eine Änderung an einer vorhandenen Kernel-Klasse | ihren Namen beibehalten | `eZContentObject`, `eZINI`, `eZDB` |

## Ihr Präfix

Alle Klassen teilen sich eine Klassen-Map, sodass zwei gleichnamige Klassen in zwei Erweiterungen einander stören: Der
Generator warnt vor einem Duplikat, und eine von beiden gewinnt. Beginnen Sie jede Klasse, Funktion, jeden
Template-Operator, jede Fetch-Funktion, jeden Einstellungsblock und jede Tabelle Ihrer Erweiterung mit einem Präfix, das
Ihnen gehört: `acmeReportService`, `acme_report` (Tabelle), `acmereport_total` (Operator), `[AcmeReportSettings]` (Block).

## exp und sevenx

- `exp` ist das Präfix des Projekts. Neuer Code im Kernel und in Projekterweiterungen verwendet es: `explayouts`,
  `expai`, `expqueue`, `expHelpAudience`, die Konsolenbefehle `exp:`. Verwenden Sie `exp` nur für Code, den Sie zum
  Projekt beitragen.
- `sevenx` ist das offizielle öffentliche PHP-Code-Präfix von 7x; „7x“ kann keines sein, weil ein PHP-Bezeichner nicht
  mit einer Ziffer beginnen kann (`class 7xFoo` ist ein Parse-Fehler).
- Vorhandene Namen `sevenx_*` und `exp*` werden nicht umbenannt, nur damit die Präfixe einheitlich aussehen; der
  Ordnername einer Erweiterung ist ihre Identität in den Einstellungen.

## Kernel-Bezeichner eZ bleiben

`eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`, `ezpKernelWeb`, die Dateinamen `ez*`, Modulnamen,
Template-Operatoren, Einstellungen und Datenbanktabellen sind **echte Bezeichner**. Sie werden nie umbenannt: Eine
Umbenennung würde jede Erweiterung und jedes Template brechen, das für sie geschrieben wurde. `eZ` ist das Präfix dieser
Bezeichner, nicht der Name des Produkts.

Zwei Folgen für Ihren Code:

- Legen Sie keine neuen Klassen mit dem Präfix `eZ` oder `ezp` an; sie sehen aus wie Kernel-Klassen und können mit
  künftigen kollidieren.
- In Kommentaren, Meldungen und Dokumentation heißt das Produkt **Exponential**. Der alte Produktname erscheint nur als
  vergangene Geschichte; ein Klassenname wie `eZContentObject` ist nicht der Produktname und bleibt, wie er ist.

## Dateien und Klassennamen

| Element | Regel | Beispiel |
|---|---|---|
| Klassendatei | eine Klasse pro Datei, Dateiname ist der Klassenname in Kleinbuchstaben | `classes/acmereportservice.php` enthält `acmeReportService` |
| Datentyp | `<datatypestring>type.php` in `datatypes/<datatypestring>/` | `datatypes/acmecolor/acmecolortype.php` |
| Workflow-Ereignis | `<typestring>type.php` in `eventtypes/event/<typestring>/` | `eventtypes/event/acmenotify/acmenotifytype.php` |
| Modul | Verzeichnisname = Modulname | `modules/acmereport/module.php` |
| Tests | `<Class>Test.php` | `tests/acmeReportServiceTest.php` |

Der Klassen-Map-Generator findet Klassen, indem er Dateien durchsucht, sodass auch eine Klasse in einer anders benannten
Datei gefunden wird; die Regel ist für Menschen, die `acmereportservice.php` suchen, wenn sie `acmeReportService` lesen.

## Klassen mit Namespace

Der Kernel verwendet Namespaces nur für die Runnable-Klassen (Befehle, Cronjob-Teile, Modul-Views) und ihre Dienste:

| Art | Namespace |
|---|---|
| Kernel-Befehl | `Exponential\Command\Kernel\<Name>` |
| Erweiterungsbefehl | `Exponential\Command\Extension\<Ext>\<Name>` |
| Cronjob-Teil einer Erweiterung | `Exponential\Cronjob\Extension\<Ext>\<Name>` |
| Modul-View einer Erweiterung | `Exponential\View\Extension\<Ext>\<Module>\<View>` |
| Kernel-Dienst | `Exponential\Service\...` |

Alles andere sind globale Klassennamen mit Präfix ([Modul-Views als Klassen](modul-views-als-klassen.md)).

## Andere Bezeichner

- **INI-Dateien und -Blöcke**: Ihre eigene Datei (`acmereport.ini`) mit Ihren eigenen Blöcken; Änderungen an
  Kernel-Dateien gehören in `*.ini.append.php` Ihrer Erweiterung.
- **Tabellen**: `acme_*`, Kleinbuchstaben, Namen ohne Anführungszeichen, die auf jeder unterstützten Datenbank
  funktionieren ([Code für jede unterstützte Datenbank](code-fuer-jede-unterstuetzte-datenbank.md)).
- **Template-Operatoren und Fetch-Funktionen**: mit Präfix (`acmereport_money`, `fetch( 'acmereport', 'items' )`).
- **Übersetzungskontexte**: `extension/acmereport`.
- **CSS-Klassen und globale JavaScript-Variablen**: ebenfalls Ihr Präfix.

## Verwandte Seiten

- [Schichtung und Benennung](../../standards/architektur/schichten-und-benennung.md)
- [Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md)
- [Benennung und Branding bei einem Upgrade](../erweiterungsentwicklung/teil-4-aktualisieren/namen-und-marken-bei-einem-upgrade.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
