---
title: Was eine Erweiterung ist
audience: user, developer, designer, app-creator
category: extension-development
order: 10
summary: Ein Ordner unter extension/, der Einstellungen, Templates, Module, Datentypen, Workflow-Ereignisse und mehr hinzufügt, ohne den Kernel anzufassen, und der nichts tut, bis er eingeschaltet wird.
keywords: Erweiterung, Ordner, Kernel, Plugin, Add-on, Modul, Datentyp, Template-Operator, Design, Einstellungen, was kann ich bauen, extension, folder, ActiveExtensions, kernel, plugin, add-on, module, datatype, template operator, design, settings, what can I build
type: help
---
# Was eine Erweiterung ist

Teil 1, Kapitel 1. Dieses Kapitel ist für alle, die das Wort „Erweiterung“ gehört haben und genau wissen möchten, was
es in Exponential bedeutet: was sie auf der Festplatte ist, wie sie eingeschaltet wird und welche Arten von Dingen sie
hinzufügen kann. Alles, was Sie später in diesem Buch bauen, ist eine Erweiterung; dies ist also das Fundament.

## In einem Satz

Eine Erweiterung ist **ein Ordner unter `extension/`**, der Exponential Einstellungen, Templates, Module, Inhaltstypen,
Befehle, Cronjobs und vieles andere hinzufügt, **ohne den Kernel zu verändern**, und der nichts tut, bis er
**eingeschaltet** wird.

## Der Ordner

Sehen Sie sich Ihre eigene Installation an:

```bash
ls extension/
```

Sie sehen einen Ordner pro Erweiterung, zum Beispiel `ezjscore`, `ezoe`, `eztags`, `explayouts`,
`sevenx_themes_simple`. Der Ordnername ist überall der Name der Erweiterung: in Einstellungen, im Block `extra` von
Composer, in der Erweiterungsliste der Administration. Die Einstellung, die den Ordner benennt, ist `ExtensionDirectory`
im Block `[ExtensionSettings]` von `settings/site.ini`, und ihr Standardwert ist `extension`.

Darin verwendet eine Erweiterung dieselben Verzeichnisnamen wie der Kernel; wenn Sie eine kennen, können Sie also alle
lesen:

| Ordner oder Datei | Was darin liegt |
|---|---|
| `ezinfo.php`, `extension.xml` | Wer sie gemacht hat, welche Version, welche Lizenz (siehe [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md)) |
| `settings/` | Dateien `.ini.append.php`, die die Einstellungen des Kernels ergänzen oder ändern |
| `classes/` | PHP-Klassen, die über das Autoload-Array gefunden werden |
| `autoloads/` | Registrierungen von Template-Operatoren (`eztemplateautoload.php`) |
| `modules/<name>/` | Ein Modul mit Views: Seiten mit eigener Adresse |
| `design/<design>/templates/` | Templates sowie `stylesheets/`, `javascript/`, `images/` |
| `datatypes/<name>/` | Ein Datentyp: eine neue Art von Feld für Inhaltsklassen |
| `eventtypes/event/<name>/` | Ein Workflow-Ereignistyp |
| `cronjobs/` | Cronjob-Skripte |
| `bin/php/` | Kommandozeilenskripte |
| `translations/<locale>/` | Übersetzungen der Texte der Erweiterung |
| `share/`, `sql/` | Datenbankschema und -daten, Pakete |

Die vollständige Karte steht in [Der Verzeichnisaufbau einer Erweiterung](../teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md).

## Der Schalter

Eine Erweiterung auf der Festplatte tut nichts. Sie wirkt, sobald sie in `ActiveExtensions[]` von `[ExtensionSettings]`
(für die ganze Installation) oder in `ActiveAccessExtensions[]` (für einen Siteaccess, also eine Website oder die
Administration) aufgeführt ist. So sehen Sie, welche eingeschaltet sind:

```bash
./console exp:ini list site.ini/ExtensionSettings --allow-root-user
```

Sie sehen eine Zeile `ActiveExtensions[]=<name>` pro aktiver Erweiterung, in Ladereihenfolge. Die Reihenfolge ist
wichtig: Eine früher geladene Erweiterung gewinnt bei Einstellungen und Design-Templates gegen eine spätere. Der Weg im
Browser ist **Setup > Extensions** (`/setup/extensions`); beides wird in
[Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../anleitungen/extensions.md) erklärt.

## Warum der Kernel unangetastet bleibt

Der Kernel ist der Code unter `kernel/`, `lib/`, `settings/` und `design/` im Wurzelverzeichnis der Installation. Er
wird bei jedem Upgrade ersetzt. Was Sie dort ändern, geht verloren oder wird, schlimmer noch, schlecht in die nächste
Version eingemischt. Eine Erweiterung lebt neben dem Kernel und steckt sich an Stellen ein, an denen der Kernel gebaut
wurde nachzusehen:

- eine **Einstellung**, die Ihren Ordner oder Ihre Klasse nennt (zum Beispiel `ExtensionRepositories[]` für Module,
  `DesignExtensions[]` für Templates, `ExtensionDirectories[]` für Datentypen),
- ein **Verzeichnis**, das der Kernel nach einem bekannten Dateinamen durchsucht,
- ein **Design** in der Design-Kette, sodass Ihr Template vor dem des Kernels gefunden wird,
- ein **Event**, das der Kernel ankündigt und auf das Ihr Listener antwortet.

Deshalb lässt ein Upgrade des Kernels Ihre Erweiterung in Ruhe, und eine Erweiterung lässt sich teilen: Kopieren Sie den
Ordner in eine andere Installation, schalten Sie ihn ein, und er funktioniert auch dort.

## Was eine Erweiterung hinzufügen kann

Der Kernel kennt mehr als siebzig Stellen, an denen sich eine Erweiterung einstecken kann. **Setup > RAD**
(`/setup/rad`) listet jede davon in sieben Gruppen auf, entnommen aus dem Katalog in `kernel/setup/expradcatalogue.php`.
Als diese Seite geschrieben wurde, enthielt dieser Katalog 73 Einträge:

| Gruppe | Einträge | Beispiele |
|---|---|---|
| Inhalte | 9 | Datentyp, Inhaltsklasse, Custom Tag für Rich Text, Verhalten der Informationssammlung |
| Templates und Design | 8 | Template-Operator, Fetch-Funktion, Template-Funktion, Design-Erweiterung, Override-Satz |
| Module und Views | 11 | Modul, View eines vorhandenen Moduls, Richtlinienfunktion, REST-Anbieter, Ajax-Funktion |
| Workflow, Events und Jobs | 9 | Workflow-Ereignistyp, Trigger, Benachrichtigungs-Handler, Cronjob-Teil, Event-Listener |
| Speicherung und Infrastruktur | 12 | Datenbank-Handler, Cluster-Datei-Handler, Suchmaschine, Sitzungs-Handler, Mail-Transport |
| Pakete und Shop | 9 | Paket-Handler, Zahlungsanbieter, Handler für Mehrwertsteuer und Versand |
| Benutzer, Zugriff und Sprache | 15 | Login-Handler, Richtlinienbeschränkung für Inhalte, Übersetzung, Icon-Theme, Siteaccess-Einstellungen |

Zählen Sie die Einträge auf Ihrer eigenen Installation unter `/setup/rad`; die Zahl wächst, wenn der Kernel eine neue
Stelle lernt. Die kuratierte Beschreibung jedes Eintrags ist [Extension points](../../../bc/6.0/rad-erweiterungspunkte.md).

## Klein oder groß, beides sind Erweiterungen

- **Sehr klein**: `extension/str_replace` besteht aus zwei PHP-Dateien und einer Einstellungsdatei. Sie fügt einen
  Template-Operator hinzu.
- **Mittel**: `extension/eztags` fügt einen Datentyp, Module, Templates, Fetch-Funktionen und eigene Datenbanktabellen
  hinzu.
- **Sehr groß**: `extension/explayouts` und seine Schwesterpakete fügen einen visuellen Seitenbaukasten mit einem
  Editor, einer API und eigener Speicherung hinzu.
- **Nur Einstellungen**: Eine Erweiterung kann nichts als Dateien `.ini.append.php` enthalten, zum Beispiel die
  Einstellungen einer Website. Das ist eine vollkommen gute Erweiterung und der sicherste Weg, die Konfiguration einer
  Website unter Versionskontrolle zu halten.
- **Nur Design**: Ein Theme ist eine Erweiterung mit einem Ordner `design/` (siehe
  [Themes sind Erweiterungen](../teil-6-theme-entwickler/themes-sind-erweiterungen.md)).

## Was eine Erweiterung nicht ist

| Sie ist nicht | Weil |
|---|---|
| Eine Änderung an einer Datei in `kernel/` | Die geht beim nächsten Upgrade verloren. Verwenden Sie stattdessen eine Einstellung, einen Override oder ein Event. |
| Eine Composer-Bibliothek | Eine Bibliothek unter `vendor/` ist reiner PHP-Code. Eine Erweiterung *kann* von Composer installiert werden (ihr Pakettyp ist `ezpublish-legacy-extension`), landet aber trotzdem in `extension/`. |
| Inhalt | Inhalte liegen in der Datenbank. Eine Erweiterung kann Inhaltsklassen *anlegen* oder Beispielinhalte in einem Paket mitliefern, aber die Inhalte selbst gehören der Website. |
| Automatisch sicher | Eine Erweiterung läuft mit denselben Rechten wie der Kernel. Lesen Sie [Eine alte Erweiterung bewerten](eine-alte-erweiterung-bewerten.md), bevor Sie eine installieren, die Sie nicht kennen. |

## Probieren Sie es aus

1. Listen Sie Ihre Erweiterungen auf: `ls extension/ | wc -l` zählt die Ordner auf der Festplatte.
2. Zählen Sie die aktiven: `./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions --allow-root-user`.
3. Wählen Sie eine aktive Erweiterung und listen Sie ihre Ordner auf: `ls extension/<name>/`. Ordnen Sie jeden Ordner
   der Tabelle oben zu.

Der Unterschied zwischen den beiden Zahlen ist normal: Installationen bringen Erweiterungen mit, die sie nicht
verwenden, damit das Einschalten einer davon eine Änderung der Einstellungen ist und keine Installation.

## Verwandte Seiten

- Zurück: [Teil 1: Vom Benutzer zum Entwickler](README.md)
- Weiter: [Die Leiter: Benutzer, Builder, Erweiterungsentwickler, Theme-Entwickler](die-leiter-vom-benutzer-zum-entwickler.md)
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../anleitungen/extensions.md)
- [Die Extensions-Seite](../../../anleitungen/extensions-seite.md)
- [RAD-Werkzeuge](../../../../eng-us/features/6.0/rad-tools.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
