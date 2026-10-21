---
title: "Was eine Theme-Erweiterung ist"
audience: designer
category: theme-extensions
order: 20
summary: Eine Theme-Erweiterung ist ein Ordner unter extension/, der ein Design und alles trägt, was es braucht; warum Designs dorthin gehören und nicht in den eigenen design/-Ordner der Installation, mit drei echten Themes als Beispiele.
keywords: Theme-Erweiterung, Design-Erweiterung, Design-Ordner, Erweiterungsordner, upgradesicher, sevenx_themes_simple, cjw_themes_jumper, sevenx_themes_media, theme extension, design extension, design folder, extension folder, upgrade safe
type: help
---
# Was eine Theme-Erweiterung ist

Diese Seite ist für Designer, die Templates und Stylesheets gebaut haben und sich fragen, wo sie sie aufbewahren sollen.

## Kurz gesagt

- Eine **Erweiterung** ist ein Ordner unter `extension/` (den Ordner legt `site.ini [ExtensionSettings]
  ExtensionDirectory` fest, Standard `extension`). Sie tut nichts, bis sie eingeschaltet wird.
- Eine **Theme-Erweiterung** ist eine Erweiterung, deren Hauptaufgabe ein Design ist: ein Ordner `design/<name>/` mit
  Templates, Stylesheets, Skripten und Bildern sowie den Einstellungen, die der Installation sagen, dass sie es verwenden
  soll.
- Weil sie ein eigener Ordner ist, lässt sich ein Theme versionieren, veröffentlichen, mit Composer installieren, als
  `.ezpkg` packen und aktualisieren, ohne den Kernel anzufassen.

## Warum nicht die eigenen Design-Ordner der Installation bearbeiten

Die Installation liefert eigene Designs mit (`design/standard`, die Designs der Administration). Dort Dateien zu ändern,
sieht schnell aus, aber:

| `design/` der Installation bearbeiten | Eine Theme-Erweiterung verwenden |
|---|---|
| Das nächste Upgrade von Exponential überschreibt Ihre Dateien | Upgrades lassen `extension/<yourtheme>/` in Ruhe |
| Niemand kann Ihre Änderungen von denen des Produkts unterscheiden | Ihr Theme ist ein Ordner mit eigener Historie |
| Es lässt sich nicht getrennt teilen oder verkaufen | Es lässt sich als Paket veröffentlichen |
| Die Dateikonsistenzprüfung listet jede geänderte Datei | Die Prüfung des Produkts bleibt sauber |

## Was darin steckt

Eine minimale Theme-Erweiterung hat:

- `design/<design name>/templates/` mit mindestens `pagelayout.tpl` (siehe [Das Pagelayout](../theme-design/das-pagelayout.md));
- `design/<design name>/stylesheets/`, `javascript/`, `images/`;
- `settings/design.ini.append.php` mit `DesignExtensions[]=<extension name>`
  ([Das Design registrieren](das-design-registrieren.md));
- `ezinfo.php` und `extension.xml` ([ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md));
- eine `LICENSE` und eine `README.md`.

## Drei echte Beispiele

Die Themes, die 7x pflegt, sind öffentliche Repositories, die Sie lesen können:

| Theme | Design-Ordner | Hinweise |
|---|---|---|
| `sevenx_themes_simple` | `design/simple` | Liefert Inhaltsklassen-Pakete in `packages/` mit ([Inhaltsklassen-Pakete mitliefern](inhaltsklassen-pakete-beilegen.md)) |
| `cjw_themes_jumper` | `design/starter`, `design/standard` | Eigene Einstellungsdatei `cjw_jumper.ini`, Siteaccess-Einstellungen für `cjw_starter`, zwei kleine Module |
| `sevenx_themes_media` | das Design `media` | Template-Operatoren, Datentypen, ein Ordner `share/` mit Startdaten |

Jedes davon ist ein GitHub-Repository unter `se7enxweb/` und ein Composer-Paket auf Packagist; sehen Sie sich
`https://github.com/se7enxweb/cjw_themes_jumper` für ein kompaktes an.

## Ein Design ist nicht immer ein ganzes Theme

Manche Erweiterungen tragen ein Design nur nebenbei: die Templates eines Moduls in `design/standard/templates/<module>/`
oder einen Icon-Satz. Alles in diesem Bereich gilt auch für sie; das Wort „Theme“ bedeutet nur, dass das Design die
Hauptsache ist.

## Nächste Schritte

- [Ihre Theme-Erweiterung benennen](ihre-theme-erweiterung-benennen.md)
- [Die Ordnerstruktur einer Theme-Erweiterung](ordnerstruktur-einer-theme-erweiterung.md)
- [Der Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md)

## Verwandte Seiten

- [Design-Erweiterungen](../theme-design/design-erweiterungen.md)
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../anleitungen/extensions.md)
- [Theme-Erweiterungen: hier beginnen](theme-erweiterungen-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
