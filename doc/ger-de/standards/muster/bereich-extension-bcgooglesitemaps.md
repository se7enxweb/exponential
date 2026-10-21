---
title: Muster in extension/bcgooglesitemaps
audience: developer
category: standards
order: 403
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/bcgooglesitemaps mit den Entwurfsmustern.
keywords: extension/bcgooglesitemaps, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/bcgooglesitemaps

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/bcgooglesitemaps` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `bcGoogleSitemapsInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/bcgooglesitemaps/ezinfo.php:11` | – |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generate` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php:49` | Der Code von extension/bcgooglesitemaps/cronjobs/generate.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generatemultilingual` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php:88` | Den Benutzer darauf hinweisen, wie lange die Skriptausführung gedauert hat * |
| `Exponential\View\Extension\Bcgooglesitemaps\Googlesitemapdynamic\Sitemap` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/bcgooglesitemaps/classes/runnable/views/googlesitemapdynamic/sitemap.php:23` | Der Code von extension/bcgooglesitemaps/modules/googlesitemapdynamic/sitemap.php, in eine Klasse verschoben (#207 Stufe 1). |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/googlesitemapdynamic/sitemap` | `sitemap.php` | sitemap | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:15` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `googlesitemapdynamic/sitemap` | – | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:19` |

## Cronjob-Teile (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `generate` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php` | Der Code von extension/bcgooglesitemaps/cronjobs/generate.php, in eine Klasse verschoben (#207 Stufe 1). |
| `generatemultilingual` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php` | Der Code von extension/bcgooglesitemaps/cronjobs/generatemultilingual.php, in eine Klasse verschoben (#207 Stufe 1). |
| `generate` | `extension/bcgooglesitemaps/cronjobs/generate.php` | – |
| `generatemultilingual` | `extension/bcgooglesitemaps/cronjobs/generatemultilingual.php` | – |
