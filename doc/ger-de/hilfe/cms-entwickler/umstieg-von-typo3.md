---
title: Umstieg von TYPO3
audience: cms-developer
category: cms-development
order: 6
summary: Für TYPO3-Entwickler, wie sich Seitenbaum, Inhaltselemente und Backend-Layouts, Datensätze und Ordner, TCA, TypoScript, Fluid, Extensions, Site-Konfiguration und der Scheduler auf Exponential übertragen. Die Seite sagt auch ehrlich, was die Dokumentation für eine TYPO3-Migration nicht abdeckt.
keywords: TYPO3, Umstieg von TYPO3, Seitenbaum, Inhaltselemente, tt_content, Backend-Layout, colPos, Datensätze, Sysfolder, Ordner, TCA, TypoScript, Fluid, Extbase, Extension, Site-Konfiguration, Scheduler, Workspaces, Migration, coming from TYPO3, page tree, content elements, storage folder
type: help
---
# Umstieg von TYPO3

Diese Seite richtet sich an Entwickler, die TYPO3 kennen und ein Exponential-Projekt beginnen. Beide Systeme sind um
einen Baum herum gebaut, daher fällt der Einstieg leicht; der Unterschied ist, dass in TYPO3 eine Seite ein Behälter
für Inhaltselemente ist, während in Exponential jedes Element, ob Seite oder nicht, ein typisiertes Objekt im Baum ist.
Diese Seite vergleicht nur Begriffe und bewertet die beiden Systeme nicht.

## Inhalt

1. [Der Baum, von beiden Seiten gesehen](#der-baum-von-beiden-seiten-gesehen)
2. [Begriff für Begriff](#begriff-für-begriff)
3. [Inhaltselemente und Backend-Layouts](#inhaltselemente-und-backend-layouts)
4. [TCA, TypoScript und Einstellungen](#tca-typoscript-und-einstellungen)
5. [Fluid und die Template-Sprache](#fluid-und-die-template-sprache)
6. [Extensions und der Scheduler](#extensions-und-der-scheduler)
7. [Eine TYPO3-Website umziehen](#eine-typo3-website-umziehen)

## Der Baum, von beiden Seiten gesehen

In TYPO3 enthält der Seitenbaum Seiten, und Datensätze wie News oder Adressen liegen meist in Ordnern (Sysfolder) und
werden von Plugins auf Seiten angezeigt. In Exponential enthält der Inhaltsbaum **jedes** Objekt: Ein News-Artikel ist
ein Knoten in einem News-Ordner, und sein Knoten ist zugleich seine Seite, mit eigener URL. Eine Liste von News auf
einer anderen Seite ist ein Fetch in einem Template oder ein Block von Layouts, kein Plugin, das Datensätze aus einem
Ordner liest ([Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md)).

Inhalte, die keine eigene Seite haben sollen (etwa gemeinsam genutzte Textbausteine), können trotzdem in einem Ordner
des Baums liegen und eingebettet oder verknüpft werden; verbergen Sie diesen Ordner oder halten Sie ihn außerhalb des
Wurzelknotens der Website.

## Begriff für Begriff

| In TYPO3 | In Exponential | Lesen |
|---|---|---|
| Seite im Seitenbaum | Objekt einer Behälterklasse (Ordner, Landingpage) | [Das Inhaltsmodell](das-inhaltsmodell.md) |
| Im TCA definierter Datensatztyp | Inhaltsklasse | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md) |
| TCA-Spalte und ihr Typ | Klassenattribut und Datentyp | [Datentypen wählen](datentypen-waehlen.md) |
| Ordner (Sysfolder) | ein Ordner im Baum, oft in einer eigenen Sektion | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md) |
| Inhaltselement in einer Spalte | Rich Text im Seitenobjekt oder Blöcke in einer Zone eines Layouts | [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md) |
| Dateireferenzen (FAL) | Bild- und Dateiobjekte, verknüpft oder eingebettet | [Bilder und Bildvarianten](bilder-und-bildvarianten.md) |
| Kategorien | `eztags` oder Ordner | [Relationen und Einbettungen](relationen-und-einbettungen.md) |
| Backend-Benutzer, -Gruppen und Zugriffslisten | Benutzer und Gruppen als Inhalte, Rollen mit Richtlinien | [Benutzer und Registrierung](benutzer-und-registrierung.md) |
| Sprachen in der Site-Konfiguration | Inhaltssprachen und Siteaccesses | [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md) |
| Site-Konfiguration (Sites, Basis-URLs) | Siteaccesses und ihre Zuordnungsregeln | [Siteaccesses und Multisite](siteaccesses-und-multisite.md) |
| Workspaces | Versionen, Entwürfe, Objektzustände und Freigabe-Workflows | [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md) |
| Sprechende URLs (Slugs) | URL-Aliase | [URL-Aliase und Routing](url-aliase-und-routing.md) |

## Inhaltselemente und Backend-Layouts

Eine TYPO3-Seite ordnet Inhaltselemente in den Spalten eines Backend-Layouts an. Exponential bietet für denselben
Bedarf zwei Wege:

- **Strukturierte Klassen.** Ein Artikel hat einen Titel, eine Einleitung, einen Rich-Text-Haupttext und ein Bild als
  Attribute; sein Layout ist das Template. Die meisten Seiten einer Website werden so gebaut.
- **Exponential Layouts.** Ein Layout-Typ legt Zonen fest; Redakteure platzieren darin Blöcke (Text, Bilder, Listen von
  Inhalten, Teaser), und Regeln entscheiden, welche Seiten welches Layout verwenden. Das kommt Backend-Layouts mit
  Inhaltselementen am nächsten ([Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md),
  [Zwei Wege zum Bauen](../zwei-wege-zum-bauen/zwei-wege-zum-bauen.md)).

## TCA, TypoScript und Einstellungen

TCA legt Datensatztypen und Felder in PHP-Arrays fest; in Exponential ist diese Festlegung die Klasse, die in der
Administration angelegt und in Paketen zwischen Installationen übertragen wird
([Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md)). TypoScript konfiguriert die Ausgabe und viele
Einstellungen; in Exponential besteht die Konfiguration aus INI-Dateien, mit mitgelieferten Standardwerten in
`settings/`, globalen Overrides in `settings/override/`, Einstellungen je Siteaccess in `settings/siteaccess/<name>/`
und Erweiterungseinstellungen in `extension/<ext>/settings/`
([Einstellungen und INI für CMS-Entwickler](einstellungen-und-ini-fuer-cms-entwickler.md)). Entscheidungen zur Ausgabe, die
TypoScript treffen würde, liegen in Templates und Override-Regeln.

## Fluid und die Template-Sprache

Fluid-Templates verwenden XML-artige ViewHelper. Exponential-Templates verwenden eine Syntax mit geschweiften
Klammern, Funktionen und Operatoren (`{if}`, `{foreach}`, `{def}`, `|wash()`, `fetch()`), und das Template für einen
Inhalt wird über Override-Regeln gewählt, die auf Klasse, Knoten, übergeordneten Knoten, Sektion oder Ansichtsmodus
passen ([Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md)). Partials und
Layouts entsprechen `{include}` und dem Pagelayout, dem äußeren Template jeder Seite
([Das Pagelayout](../designer/theme-design/das-pagelayout.md)).

## Extensions und der Scheduler

| TYPO3 | Exponential |
|---|---|
| Extension (`ext_emconf.php`, Composer) | Erweiterungsordner mit `extension.xml`, `ezinfo.php` und `composer.json` ([Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md)) |
| Extbase-Plugin mit Controller | ein Modul mit Views ([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)) |
| ViewHelper | Template-Operator oder -Funktion ([Template-Operatoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/template-operatoren.md)) |
| Signals, PSR-14-Events, Hooks | Event-Listener und Workflow-Events ([Workflows und Auslöser](workflows-und-ausloeser.md)) |
| Scheduler-Tasks | Cronjob-Skripte in Cronjob-Teilen ([Cronjobs](cronjobs.md)) |
| `typo3`-Kommandozeile | `./console` und `bin/php/*.php` |
| Caches im Backend leeren | **Setup** > **Caches** oder `php bin/php/ezcache.php` ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md)) |

## Eine TYPO3-Website umziehen

Diese Dokumentation hat keine eigene Migrationsanleitung für TYPO3 und nennt kein Werkzeug, das TYPO3-Daten direkt
liest. Verwenden Sie die allgemeine Methode für andere Systeme: Exportieren Sie die Datensätze als CSV, XML oder JSON
(etwa aus der Datenbank oder mit einer TYPO3-Extension Ihrer Wahl), ordnen Sie sie mit
[Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md) zu und importieren Sie sie mit den
Werkzeugen aus [Andere Anwendungen und Datenbanken](../../installation/migration/andere-anwendungen.md) und
[Inhalte importieren](inhalte-importieren.md). Planen Sie die Weiterleitungen von den alten sprechenden URLs
([Checkliste](../../installation/migration/checkliste.md)).

## Verwandte Seiten

- [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md)
- [Umstieg von Drupal](umstieg-von-drupal.md)
- [Eine Migration planen (Kurs für Trainer)](../trainer/migration/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
