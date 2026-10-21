---
title: "Lektion 1.1: Die Landkarte einer Installation für Entwickler"
audience: trainer, developer
category: training
order: 11
summary: Die Teilnehmenden finden sich in den Ordnern einer Installation zurecht, unterscheiden den Kernel von den Erweiterungen und lernen die zwei Regeln kennen, auf denen jede spätere Lektion aufbaut. 45 Minuten.
keywords: entwickler, kernel, erweiterung, ordnerübersicht, var, settings, design, bin/php, autoload, den kernel lesen, erweiterungsentwickler, developers course, extension, folder map, read the kernel, extension developer
type: help
---
# Lektion 1.1: Die Landkarte einer Installation für Entwickler

Bevor die Teilnehmenden eine Zeile Code schreiben, müssen sie wissen, wo was liegt und welche Ordner ihnen gehören.
Diese Lektion geht durch die Ordner der obersten Ebene einer Installation, legt die zwei Regeln fest, denen der ganze
Kurs folgt, und zeigt, wie eine Seitenadresse zu dem Code führt, der die Seite erzeugt.

> **Hinweis**
> Verwenden Sie für jede Übung in diesem Kurs eine Entwicklungs- oder Schulungsinstallation, niemals eine
> Live-Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 1: Die Entwicklungsinstallation](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | PHP-Entwickler, die neu bei Exponential sind |
| Voraussetzungen | Eine Shell auf einem Rechner mit einer Installation; Grundkenntnisse in PHP und Git |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Zweck von `kernel/`, `lib/`, `settings/`, `design/`, `extension/`, `var/`, `bin/php/` und `autoload/` nennen;
- die zwei Regeln wiedergeben: Den Kernel lesen, nicht bearbeiten; die eigene Arbeit liegt in `extension/`;
- das Modul und die View hinter einer Adresse der Administration wie `/content/view/full/2` finden;
- die Datei einer Kernel-Klasse über die Autoload-Tabelle finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Den Code lesen, den Sie schon haben](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md)
und [Was eine Erweiterung ist](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md).

1. **Die Geschichte in einem Satz** (2 Minuten). Exponential führt die Codebasis von eZ Publish fort, die ihr
   Hersteller nach fast 18 Jahren eingestellt hat. Die Bezeichner des Kernels behalten ihre historischen Namen
   (`eZINI`, `eZContentObject`, `ezinfo.php`); neuer Code der Teilnehmenden verwendet ein eigenes Präfix, niemals
   `ez`.
2. **Die Landkarte** (8 Minuten). Führen Sie `ls` im Wurzelverzeichnis der Installation am Projektor aus und gehen Sie
   die folgende Tabelle durch. Betonen Sie die dritte Spalte.

   | Ordner | Was darin liegt | Ändern Sie ihn? |
   |---|---|---|
   | `kernel/` | Ein Ordner pro Kernel-Modul und `kernel/classes/`, das Inhaltsmodell | Niemals. Lesen Sie ihn. |
   | `lib/` | Die Bibliotheken: `ezdb/`, `eztemplate/`, `ezutils/`, `ezfile/` ... | Niemals. Lesen Sie ihn. |
   | `settings/` | Standarddateien `.ini`; `settings/override/` und `settings/siteaccess/<name>/` enthalten die eigenen dieser Installation | Nur über Overrides, Siteaccess-Einstellungen oder Erweiterungen |
   | `design/` | Die Designs des Kernels: `standard`, `admin4`, `editor` ... | Niemals. Überschreiben Sie in Ihrer eigenen Design-Erweiterung |
   | `extension/` | Alle Erweiterungen, je ein Ordner | Ihre eigenen Erweiterungen: ja |
   | `var/` | Caches, kompilierte Templates, Logs, hochgeladene Dateien | Niemals von Hand, außer um Logs zu lesen |
   | `bin/php/` | Kommandozeilenskripte, über die Konsole beim Namen aufgerufen | Niemals. Ihre gehören nach `extension/<yours>/bin/php/` |
   | `autoload/` | Erzeugte Klassentabellen | Niemals; erzeugen Sie sie neu |
   | `tests/` | Die Testsuiten des Kernels | Lesen Sie sie: Sie sind ausführbare Beispiele |
   | `doc/` | Die Dokumentation | Verbessern Sie sie mit einem Pull Request |

3. **Die zwei Regeln** (3 Minuten). Der Kernel wird bei jedem Upgrade ersetzt, Änderungen darin gehen also verloren.
   Eine Erweiterung klinkt sich an den Stellen ein, an denen der Kernel nachsehen soll: Einstellungen, Verzeichnisse,
   die Design-Kette und Ereignisse.
4. **Von einer Adresse zum Code** (5 Minuten). `/content/view/full/2` ist das Modul `content`, die View `view`, mit
   den Parametern `full` und `2`. Zeigen Sie `grep -n "ViewList\['view'\]" -A6 kernel/content/module.php`.
5. **Von einem Klassennamen zu seiner Datei** (2 Minuten). `grep -n "'eZContentObjectTreeNode'" autoload/ezp_kernel.php`.

### Häufige Fragen

**Warum kann ich einen Fehler nicht einfach in `kernel/` beheben?** Das können Sie, als Pull Request an das Projekt
(Lektion 13.3). Auf Ihrer eigenen Website ginge die Änderung beim nächsten Update verloren; tragen Sie sie in einer
Erweiterung mit oder bringen Sie sie in den Kernel.

**Wohin kommen hochgeladene Bilder?** Unter `var/` (zum Beispiel `var/<site>/storage`). Bearbeiten Sie sie nie von
Hand.

**Gehört `vendor/` zum Kernel?** Nein. Er enthält Composer-Bibliotheken wie die Zeta Components. Ändern Sie ihn
ebenfalls nicht.

### Stolperfallen

- **Eine Datei in `design/standard/` bearbeiten.** Das funktioniert bis zum nächsten Update. Verweisen Sie die
  Teilnehmenden von Anfang an auf Overrides (Modul 3).
- **In `var/` nach Code suchen.** Kompilierte Templates in `var/` sehen wie PHP aus und verwirren Einsteiger. Schließen
  Sie `var/` von Suchen aus: `grep -rn --exclude-dir=var ...`.
- **Alte Anleitungen.** Viele Anleitungen im Netz wurden für ältere Releases geschrieben. Prüfen Sie jeden Namen, den
  sie verwenden, am Code der Installation.

## Praktische Übung

Verwenden Sie Ihre Entwicklungsinstallation aus dem Wurzelverzeichnis (dem Ordner, der `index.php` enthält).

1. Listen Sie die Ordner der obersten Ebene auf: `ls`.
2. Zählen Sie die Erweiterungen auf der Festplatte: `ls extension/ | wc -l`.
3. Zeigen Sie die Definition der View `view` des Inhaltsmoduls:

   ```bash
   grep -n "ViewList\['view'\]" -A6 kernel/content/module.php
   ```

4. Schreiben Sie den Namen des Skripts auf, das die View ausführt.
5. Suchen Sie die Datei der Klasse `eZContentObjectTreeNode` über die Autoload-Tabelle:

   ```bash
   grep -n "'eZContentObjectTreeNode'" autoload/ezp_kernel.php
   ```

6. Suchen Sie die Zeile, in der `fetchByNodeID` in dieser Datei definiert ist:

   ```bash
   grep -n "function fetchByNodeID" kernel/classes/ezcontentobjecttreenode.php
   ```

7. Zeigen Sie die letzten drei Commits, die die Datei geändert haben: `git log --oneline -3 -- kernel/classes/ezcontentobjecttreenode.php`.
8. Wählen Sie eine Seite der Administration, die Sie kennen. Schreiben Sie ihr Modul, ihre View und ihre Datei
   `module.php` auf.

## Erwartetes Ergebnis

- Schritt 3 gibt den Eintrag `$ViewList['view']` mit `'script' => 'view.php'` und seinen Parametern aus.
- Schritt 5 gibt eine Zeile aus, die die Klasse `kernel/classes/ezcontentobjecttreenode.php` zuordnet.
- Schritt 8: Alle Teilnehmenden haben drei Pfade aufgeschrieben. Der Trainer prüft die Pfade von zwei Teilnehmenden am
  Projektor, indem er die Dateien öffnet.

## Quiz

1. Welcher Ordner enthält die Arbeit eines Entwicklers einer Website?
   A. `kernel/` B. `design/standard/` C. `extension/` D. `var/`
2. Was bedeuten die Teile von `/content/view/full/2`?
3. Richtig oder falsch: Die Dateien in `autoload/` werden von Hand geschrieben.
4. Warum sollte eine Suche nach Code `var/` ausschließen?
5. Welches Präfix dürfen die Klassennamen einer neuen Erweiterung der Teilnehmenden nicht verwenden?

<details>
<summary>Antworten</summary>

1. **C.** Ihre eigenen Erweiterungen liegen in `extension/`; der Kernel wird bei jedem Upgrade ersetzt.
2. Modul `content`, View `view`, Parameter `full` und `2` (ein View-Modus und eine Knoten-ID).
3. **Falsch.** Sie werden mit `php bin/php/ezpgenerateautoloads.php` erzeugt.
4. Er enthält Caches und kompilierte Templates, die wie Code aussehen, aber erzeugt sind.
5. `ez`: Es gehört zu den bestehenden Klassen des Kernels. Verwenden Sie `exp` oder ein eigenes Präfix.

</details>

## Weiterführende Literatur

- [Den Code lesen, den Sie schon haben](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md):
  die vier Techniken, um Code zu finden.
- [Was eine Erweiterung ist](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md): die
  Ordner in einer Erweiterung und der Schalter, der sie einschaltet.
- [Die Leiter vom Benutzer zum Entwickler](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/die-leiter-vom-benutzer-zum-entwickler.md):
  wo dieser Kurs einzuordnen ist.

## Verwandte Seiten

- [Modul 1: Die Entwicklungsinstallation](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
