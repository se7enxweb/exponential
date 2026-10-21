---
title: "Der Rundgang durch die RAD-Werkzeuge: Setup > RAD"
audience: developer, app-creator, designer
category: extension-development
order: 10
summary: Ein Rundgang durch Setup > RAD - der Katalog der Erweiterungspunkte und seine Filter, die Übersicht, die Zustandsprüfung und wie jeder Assistent funktioniert, von den Feldern über die Vorschau bis zum Anlegen oder Herunterladen.
keywords: RAD, schnelle Anwendungsentwicklung, Katalog, Erweiterungspunkte, Übersicht, Zustandsprüfung, Assistent, Vorschau, Als ZIP herunterladen, In extension/ anlegen, rapid application development, setup/rad, catalogue, extension points, survey, radsurvey, health check, wizard, preview, download as zip, create in extension
type: help
---
# Der Rundgang durch die RAD-Werkzeuge: Setup > RAD

Tutorial 1. **Setup > RAD** (`/setup/rad`, Richtlinie `setup/setup`) ist der Werkzeugkasten der Administration für
Erweiterungsentwickler. Er besteht aus drei Dingen: einem **Katalog** aller Stellen, an denen das System erweitert werden
kann, einer **Übersicht** dessen, was diese Installation tatsächlich anbietet, und einer Reihe von **Assistenten**, die
funktionierende Erweiterungen schreiben. Dieser Rundgang zeigt jedes davon; die Angaben stammen aus
`kernel/setup/expradcatalogue.php`, `expradsurvey.php`, `expradhealth.php` und den Assistentenklassen daneben.

## 1. Der Katalog

Öffnen Sie `/setup/rad`. Die Seite trägt den Titel „Werkzeuge zur schnellen Anwendungs-Entwicklung“ und sagt, was sie
ist: „Unten ist jeder Punkt aufgeführt, an dem dieses System erweitert werden kann: wofür er gedacht ist, wohin der Code
gehört und was ihn registriert.“

Jeder Eintrag zeigt:

| Feld | Bedeutung |
|---|---|
| Code | wohin der Code in Ihrer Erweiterung gehört |
| Registriert durch | die Einstellung oder Konvention, die ihn anschließt |
| Vertrag | die zu erweiternde Klasse oder die zu implementierende Schnittstelle |
| Kernel | die Kernel-Datei, in der der Mechanismus liegt |
| Werkzeug verfügbar / noch kein Werkzeug | ob ein Assistent ihn schreibt |

Die Einträge stehen in sieben Gruppen: Inhalt; Templates und Design; Module und Views; Workflow, Ereignisse und Jobs;
Speicherung und Infrastruktur; Pakete und Shop; Benutzer, Zugriff und Sprache. Drei Filter (aus
`expRADCatalogue::filters()`):

| Filter | Adresse |
|---|---|
| Alles | `/setup/rad` |
| Mit einem Werkzeug | `/setup/rad/(show)/tools` |
| Nur Dokumentation | `/setup/rad/(show)/docs` |

Das Feld **Suchen** grenzt die Liste beim Tippen ein (ein Name, eine Einstellung, eine Klasse, eine Datei). Derselbe
Katalog als Text ist [Erweiterungspunkte](../../../../bc/6.0/rad-erweiterungspunkte.md).

## 2. Die Übersicht

Der Katalog ist von Hand geschrieben; die **Übersicht der Erweiterungspunkte** (`/setup/radsurvey`) wird bei jeder
Anfrage von der Festplatte gelesen. Sie zählt, mit einem Link zu den Einträgen hinter jeder Zahl:

| Gruppe | Was sie auflistet |
|---|---|
| Einstellungen, die eine Klasse benennen | jeden INI-Wert, der eine Klasse nennt, über alle INI-Dateien hinweg |
| Orte, an denen der Kernel sucht | Verzeichnisse, in denen nach Handlern, Datentypen, Ereignissen ... gesucht wird |
| Schnittstellen und abstrakte Klassen | die Verträge |
| Module und ihre Views, Einschränkungen von Richtlinien | jede View und jede Einschränkung |
| Was ein Template aufrufen kann | alles, was ein Template aufrufen kann (Operatoren und Funktionen) |
| Ereignisse, auf die etwas hören kann | alles, was ein Ereignis ankündigt |
| Bereits ersetzte Templates | jede Override-Regel |
| Vollständig ersetzte Kernel-Klassen | wirksame Kernel-Overrides |
| Kommandos, Cronjob-Teile und Views als Klassen | die ausführbaren Klassen, die eine Unterklasse ersetzen kann |
| Registries | Einträge der in `rad.ini` deklarierten Registries |

Verwenden Sie sie, um die Frage „Gibt es dafür schon einen Anknüpfungspunkt?“ zu beantworten, bevor Sie einen
schreiben. Ein Abschnitt lässt sich verlinken: `/setup/radsurvey/(show)/<section>`. Die erzeugte Referenz ist
[Die Erweiterungsoberfläche](../../../../bc/6.0/rad-erweiterungsoberflaeche.md).

## 3. Die Zustandsprüfung

Die RAD-Seite führt außerdem `expRADHealth::findings()` aus: Einstellungen, die eine Klasse nennen, die sich nicht laden
lässt, ein durchsuchtes Verzeichnis, das nicht vorhanden ist, eine Design-Extension ohne Design, eine View ohne Skript,
ein angebotener, aber nicht gefundener Datentyp und mehr, jeweils mit dem Weg zur Behebung. Lesen Sie sie, nachdem Sie
eine Erweiterung installiert oder geschrieben haben.

## 4. Wie jeder Assistent funktioniert

Alle Assistenten folgen demselben Ablauf (`kernel/setup/expextensionwizard.php`):

1. **Die Extension**: Name der Extension, Klassenname, Titel, Zusammenfassung, Autor, Composer-Vendor, Version, Lizenz.
2. **Was hineinkommt**: die zu schreibenden Teile (für einen Datentyp: Bearbeiten, Klasseneinstellungen, Standardwert,
   Objekte benennen, Durchsuchbar, Sortierbar, Text ein und aus, Informationssammler, Dateien, Pakete, Unterschiede
   zwischen Versionen, Beim Veröffentlichen und mehr; jeder Teil ist eine Gruppe von Methoden, die nur zusammen sinnvoll
   sind).
3. **Was geschrieben wird**: die Methoden oder Templates, jeweils mit einem Hinweis, wofür der Kernel sie aufruft.
4. **Einschalten**: die genauen Zeilen, die Sie in `settings/override/site.ini.append.php` eintragen, und **Dann** (die
   Autoloads neu erzeugen, die Caches leeren).
5. **Jede Datei, bevor sie geschrieben wird**: der vollständige Inhalt jeder Datei, mit Zeilen- und Byteanzahl.
   **Vorschau aktualisieren** nach einer Änderung.
6. **In extension/ anlegen** schreibt die Dateien, oder **Als ZIP herunterladen** gibt Ihnen dieselben Dateien als
   Archiv. Wenn der Webserver nicht in `extension/` schreiben kann, sagt die Seite das und bietet nur das Archiv an.

Schutzmechanismen, auf die Sie sich verlassen können
([RAD-Werkzeuge: Sicherheit](../../../../bc/6.0/rad-sicherheit.md)): Nichts wird außerhalb von
`extension/<name>` geschrieben, eine vorhandene Erweiterung wird nie überschrieben, eingegebene Werte können nicht aus
Kommentaren oder INI-Zeilen ausbrechen, erzeugte Templates maskieren die Ausgabe, und erzeugte Views prüfen die
Eingabe.

## 5. Die Assistenten

| Assistent | Adresse | Tutorial |
|---|---|---|
| Assistent für Template-Extensions | `/setup/templateoperator` | [Ein Template-Operator in fünf Minuten](rad-template-operator.md) |
| Datentyp-Assistent | `/setup/datatype` | [Ein Datentyp](rad-datentyp.md) |
| Modul-Assistent / Modul über vorhandenen Tabellen | `/setup/modulewizard`, `/setup/moduleextension` | [Ein Modul mit Views](rad-modul.md) |
| Assistent für Workflow-Ereignisse | `/setup/workflowevent` | [Ein Workflow-Ereignis](rad-workflow-ereignis.md) |
| Assistent für Design-Extensions | `/setup/designextension` | [Eine Design-Erweiterung](rad-design-erweiterung.md) |
| Assistent für Einstellungs-Extensions | `/setup/settingsextension` | [Eine Erweiterung nur mit Einstellungen](rad-einstellungs-erweiterung.md) |
| Assistent für Content-Extensions | `/setup/contentextension` | eine Inhaltsklasse als Installationsskript |
| Handler-Assistent | `/setup/handlerextension/<kind>` | [Handler und Kernel-Overrides](rad-handler-und-kernel-overrides.md) |
| Assistent für Kernel-Overrides | `/setup/kerneloverride` | [Handler und Kernel-Overrides](rad-handler-und-kernel-overrides.md) |

## 6. Nach jedem Assistenten

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' <name> global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
php bin/php/checkclasses.php --quiet-ok
```

Lesen Sie dann die erzeugte `README.md`: Jeder Assistent schreibt eine, die beschreibt, was er geschrieben hat und wie
es weitergeht, dazu `doc/examples.php` mit sofort ausführbaren Beispielen für die meisten Arten.

## Verwandte Seiten

- Zurück: [Tutorials: echte Erweiterungen Schritt für Schritt bauen](README.md)
- Weiter: [Mit RAD: ein Template-Operator in fünf Minuten](rad-template-operator.md)
- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
