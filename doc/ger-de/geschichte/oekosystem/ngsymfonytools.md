# ngsymfonytools: Geschichte des Plattform-Repositorys

Die Geschichte von `ngsymfonytools`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Legacy Bridge und Site-Bundles). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 7 Änderungen von 2026-03-02 bis 2026-04-16, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Legacy-Erweiterung, die Twig-Templates und Symfony-Unteranfragen aus Legacy-Templates einbindet.

## Wie es mit Exponential zusammenhängt

Auf die Service-ID von Twig und einen Alias für das Repository-Interface von Ibexa aktualisiert.

## Was Anwender davon haben

Ein Legacy-Template (.tpl) kann ein Twig-Template oder eine Symfony-Route einbetten.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ngsymfonytools
```

## Template-Operatoren

Registriert in `autoloads/eztemplateautoload.php` der Erweiterung; jeder ist mit einem Beispiel in der `doc/USAGE.md` der Erweiterung beschrieben. Sie funktionieren nur in einem Projekt, das die Symfony-Plattform mit der Legacy Bridge betreibt, nicht in dieser eigenständigen Installation.

| Operator | Verwendung |
|---|---|
| `symfony_include` | Bindet ein Twig-Template aus einem `.tpl`-Template ein; Inhaltsobjekte und Knoten in den Parametern werden in die Value Objects der Plattform umgewandelt. |
| `symfony_render` | Stellt einen Symfony-Controller (oder eine Route) in einem Legacy-Template dar. |
| `symfony_render_esi`, `symfony_render_hinclude` | Gibt einen ESI- oder Hinclude-Tag für einen Controller oder eine URL aus (stellt ohne erkannten Reverse Proxy einfach dar). |
| `symfony_controller` | Nennt den darzustellenden Controller; dient als Argument von `symfony_render`. |
| `symfony_path`, `symfony_url` | Relative oder absolute URL einer Route, wie die Twig-Funktionen `path` und `url`. |
| `symfony_is_granted` | Fragt die Sicherheitsschicht von Symfony, ob der aktuelle Benutzer ein Attribut hat. |

```smarty
{symfony_include( 'NetgenTestBundle:Test:test.html.twig', hash( 'theAnswer', 42 ) )}
```

Zur Kontrolle: Lesen Sie `doc/USAGE.md` und `classes/ngsymfonytools*operator.php` in der Erweiterung.

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 4 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 1 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-04-05 | 4.0.0.0, 4.x | `1e1a0c6` | Added replace section to ensure clean override with dependencies. Bugfix. |

## Änderungen des se7enxweb-Teams, nach Thema

### Design und Templates (3)

- 2026-04-05 `979547e` Funktion: replace removed 'templating' service with 'twig' in symfony_include operator
- 2026-04-16 `8a0c480` Funktion: use Twig\Environment::class instead of 'twig' service ID
- 2026-04-16 `07b2d1c` Funktion: Revert: restore 'twig' string ID in include operator

### Composer-Abhängigkeiten (1)

- 2026-04-05 `6ba9dc1` Werkzeuge: Update package name and license in composer.json

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-05 `1e1a0c6` BC: Added replace section to ensure clean override with dependencies. Bugfix.

### Marke Exponential (1)

- 2026-04-14 `f7e6d9a` Funktion: class_alias shim for eZ→Ibexa Repository interface (Ibexa DXP 5.0)

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- [Seite der Erweiterung](../../../eng-us/features/6.0/extensions/ngsymfonytools.md)
- [Release-Changelog](../../../changelogs/extensions/ngsymfonytools.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ngsymfonytools.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
