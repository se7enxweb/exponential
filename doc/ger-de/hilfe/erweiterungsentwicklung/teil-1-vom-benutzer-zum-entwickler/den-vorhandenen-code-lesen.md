---
title: Den Code lesen, den Sie schon haben
audience: user, developer, designer, app-creator
category: extension-development
order: 60
summary: Eine Karte einer Installation und vier Lesetechniken - nach einem Namen suchen, einer Adresse bis zu ihrer View folgen, einer Seite bis zu ihrem Template folgen und Git fragen, warum eine Zeile da ist.
keywords: Code lesen, Verzeichnisstruktur, Kernel, Einstellungen, Design, Erweiterung, Modul, View, wo ist, code reading, directory layout, kernel, lib, settings, design, extension, var, bin, grep, git log, git blame, module, view, where is
type: help
---
# Den Code lesen, den Sie schon haben

Teil 1, Kapitel 6. Der beste Lehrer für Exponential ist der Code auf Ihrer Festplatte: mehr als zwanzig Jahre eines
funktionierenden Content-Management-Systems, mit seiner vollständigen Historie. Dieses Kapitel gibt Ihnen eine Karte der
Installation und vier Lesetechniken, die Sie jeden Tag verwenden werden.

## Die Karte

Vom Wurzelverzeichnis der Installation aus (dem Verzeichnis, das `index.php` enthält):

| Ordner | Was darin liegt | Ändern Sie ihn? |
|---|---|---|
| `kernel/` | Der Kern: ein Ordner pro Kernel-Modul (`content/`, `user/`, `setup/`, `exphelp/` ...) und `kernel/classes/` mit dem Inhaltsmodell (`eZContentObject`, `eZContentClass`, Datentypen, Workflow-Typen) | Nie. Lesen Sie ihn. |
| `lib/` | Die Bibliotheken, auf denen der Kernel aufbaut: `ezdb/` (Datenbank), `eztemplate/` (Template-Engine), `ezutils/` (Einstellungen, Debug, HTTP, System), `ezfile/`, `ezi18n/`, `ezimage/`, `ezlocale/` ... | Nie. Lesen Sie ihn. |
| `settings/` | Die Standarddateien `.ini`. `settings/override/` und `settings/siteaccess/<name>/` enthalten die eigenen Einstellungen dieser Installation | Nur über Overrides, Siteaccess-Einstellungen oder Erweiterungen |
| `design/` | Die Designs des Kernels: `standard`, `base`, `admin`, `admin3`, `admin4`, `admin4l`, `editor` ... | Nie. Überschreiben Sie in Ihrer eigenen Design-Erweiterung |
| `extension/` | Jede Erweiterung, jede in ihrem eigenen Ordner | Ihre eigenen Erweiterungen, ja |
| `var/` | Was die Installation schreibt: Caches, kompilierte Templates, Logs, hochgeladene Dateien (`var/<site>/storage`) | Nie von Hand, außer um Logs zu lesen |
| `bin/php/` | Kommandozeilenskripte; `./console` führt sie über ihren Namen aus | Nie. Ihre eigenen gehören in `extension/<yours>/bin/php/` |
| `share/` | Übersetzungen, Datenbankschema und -daten, Icons, das Dateimanifest `filelist.md5` | Nie |
| `autoload/` | Die erzeugten Klassenzuordnungen (`ezp_kernel.php` und die Zuordnung der Erweiterungen) | Nie; erzeugen Sie sie neu |
| `doc/` | Die Dokumentation, einschließlich dieses Buchs | Verbessern Sie sie mit einem Pull Request |
| `tests/` | Die Testsuiten des Kernels | Lesen Sie sie; sie sind ausführbare Beispiele |
| `vendor/` | Composer-Bibliotheken (Zeta Components und andere) | Nie |

Aus der Tabelle folgen zwei Regeln: **Sie lesen den Kernel, Sie bearbeiten ihn nicht**, und **Ihre Arbeit lebt in
`extension/`**. Alles in diesem Buch hält sich daran.

## Technik 1: nach dem Namen suchen

Wenn Sie einen Namen in einem Template, einer Einstellung oder einer Fehlermeldung sehen, suchen Sie danach:

```bash
grep -rn "fetchByNodeID" kernel/classes/ezcontentobjecttreenode.php
grep -rn "\[SiteAccessSettings\]" settings/site.ini
grep -rln "ShowUsedTemplates" kernel/ lib/
```

`-r` durchsucht Ordner, `-n` gibt Zeilennummern aus, `-l` gibt nur Dateinamen aus. Bei einer Klasse nennt Ihnen die
Autoload-Zuordnung die Datei direkt:

```bash
grep -n "'eZContentObjectTreeNode'" autoload/ezp_kernel.php
```

## Technik 2: von einer Adresse zum Code

Jede Seite der Administration ist ein **Modul** und eine **View**: `/content/view/full/2` ist das Modul `content`, die
View `view`, mit den Parametern `full` und `2`. So finden Sie den Code:

1. Die Definition des Moduls ist `kernel/<module>/module.php` (bei Kernel-Modulen) oder
   `extension/<ext>/modules/<module>/module.php` (bei Modulen von Erweiterungen).
2. Darin nennt `$ViewList['<view>']` das Skript (`'script' => 'view.php'`) und die Parameter.
3. Das Skript liegt neben `module.php`.

Probieren Sie es aus:

```bash
grep -n "ViewList\['view'\]" -A6 kernel/content/module.php
```

Sie sehen das Skript der View und ihre Parameter. Dieselben drei Schritte funktionieren für jedes Modul im System; das
Kapitel [Module und Views](../teil-3-erweiterungsentwickler/module-und-views.md) erklärt, was Sie sehen.

## Technik 3: von einer Seite zu ihrem Template

Schalten Sie `ShowTemplatePathComments` ein (siehe [Ihre erste Stunde als Entwickler](ihre-erste-stunde-als-entwickler.md))
und sehen Sie sich den Seitenquelltext an. Jeder Kommentar nennt eine Template-Datei, zum Beispiel
`design/standard/templates/node/view/full.tpl`. Öffnen Sie sie; das ist das HTML dieses Teils der Seite. Wenn der
Kommentar eine Datei in einer Erweiterung nennt, überschreibt die Erweiterung dort das Template des Kernels.

## Technik 4: Git fragen, warum eine Zeile da ist

Das Repository des Kernels hat mehr als 27.000 Commits. Jeder sagt, was sich geändert hat und warum. Wenn Ihnen eine
Zeile Rätsel aufgibt:

```bash
git log --oneline -5 -- kernel/classes/ezcontentobjecttreenode.php
git log -p -S "ShowTemplatePathComments" --oneline -- settings/site.ini | head -40
git blame -L 100,120 kernel/content/view.php
```

- `git log -- <file>` listet die Commits auf, die eine Datei berührt haben.
- `git log -S "<text>"` findet den Commit, der ein Stück Text hinzugefügt oder entfernt hat.
- `git blame` nennt den Commit hinter jeder Zeile; `git show <sha>` zeigt die ganze Änderung.

Jede Erweiterung, die als Klon installiert ist, hat ihre eigene Historie:

```bash
git -C extension/eztags log --oneline -10
```

Commit-Nachrichten in diesem Projekt beginnen mit `Added:`, `Updated:`, `Fixed:`, `Removed:` oder `Renamed:`, was sie
leicht überfliegbar macht: `git log --oneline | grep '^[0-9a-f]* Fixed:'` listet die Fehlerkorrekturen auf.

## Die Tests lesen

`tests/tests/` enthält die PHPUnit-Tests des Kernels, gegliedert wie der Kernel. Ein Test ist ein ausführbares
Beispiel: Er zeigt, wie eine Klasse aufgerufen werden soll und was sie zurückgibt. Wenn die Dokumentation einer Klasse
dünn ist, lesen Sie ihren Test:

```bash
ls tests/tests/kernel/classes/ | head
grep -rln "eZINI::instance" tests/tests/lib | head -5
```

## Die Dokumentation lesen

Der Ordner `doc/` ist nach Art gegliedert:

| Ordner | Was er enthält |
|---|---|
| `doc/eng-us/help/` | Seiten wie diese, für das Hilfezentrum in der Administration |
| `doc/eng-us/guides/` | Aufgabenanleitungen für Administratoren und Entwickler |
| `doc/eng-us/features/6.0/` | Eine Seite pro Funktion der Linie 6.0, mit ihren Einstellungen |
| `doc/eng-us/bc/6.0/` | Verhaltensänderungen und Upgrade-Hinweise, für Entwickler geschrieben |
| `doc/eng-us/specifications/6.0/` | Spezifikationen größerer Teilsysteme |
| `doc/eng-us/install/` | Das Installationshandbuch |
| `doc/changelogs/` | Was sich in jedem Release geändert hat |
| `doc/eng-us/history/` | Die Chronik des Projekts |

Das Hilfezentrum durchsucht sie alle; geben Sie einen Klassen- oder Einstellungsnamen in seine Suche ein.

## Übung

Wählen Sie eine Seite der Administration, die Sie oft verwenden. Finden Sie ihr Modul und ihre View (Technik 2), ihr
Template (Technik 3) und den letzten Commit, der ihr Skript geändert hat (Technik 4). Notieren Sie die drei Dateipfade;
Sie haben gerade Ihr erstes Stück Kernel gelesen.

## Verwandte Seiten

- Zurück: [Ihre erste Stunde als Entwickler](ihre-erste-stunde-als-entwickler.md)
- Weiter: [Die auf Ihrer Website installierten Erweiterungen finden](die-erweiterungen-ihrer-website-finden.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Der Verzeichnisaufbau einer Erweiterung](../teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
