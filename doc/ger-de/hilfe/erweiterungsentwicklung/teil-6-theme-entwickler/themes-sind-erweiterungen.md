---
title: Themes sind Erweiterungen
audience: designer, developer
category: extension-development
order: 10
summary: Warum ein Exponential-Theme als Erweiterung mit einem Design-Ordner gepackt wird, was Ihnen das gegenüber dem Bearbeiten der Design-Ordner des Kernels bringt und wie ein Theme mit Designs, Siteaccesses und der Design-Kette zusammenhängt.
keywords: Theme, Design-Erweiterung, Design, Siteaccess, Design-Kette, Paketierung, upgradesicher, theme, design extension, design chain, DesignExtensions, packaging, upgrade-safe
type: help
---
# Themes sind Erweiterungen

Teil 6, Kapitel 1. Exponential hat keinen eigenen „Theme“-Mechanismus. Ein Theme ist eine Erweiterung, deren
wichtigster Inhalt ein Design-Ordner ist: `extension/<name>/design/<design name>/` mit Templates, Stylesheets, Skripten
und Bildern. Dieses Kapitel erklärt, warum das gut so ist, und führt die drei Begriffe ein, die dieser Teil ständig
verwendet.

## Drei Begriffe

| Begriff | Bedeutung |
|---|---|
| **Design** | ein benannter Satz von Templates und Dateien, zum Beispiel `simple`, `standard`, `admin4`. Ein Design-Name ist ein Ordnername unter einem Ordner `design/` |
| **Design-Erweiterung** | eine Erweiterung, die Design-Ordner beisteuert, registriert in `design.ini [ExtensionSettings] DesignExtensions[]` |
| **Siteaccess** | ein Einstiegspunkt der Installation (eine öffentliche Website, die Administration, eine Redaktionsseite), der in `site.ini [DesignSettings]` die Designs wählt, die er verwendet |

Ein Theme ist also eine Design-Erweiterung, die ein Design für einen oder mehrere Siteaccesses bereitstellt.

## Warum nicht die Design-Ordner des Kernels bearbeiten?

Der Kernel hat sein eigenes `design/standard/`, `design/admin/` und weitere. Sie könnten Ihre Templates dort ablegen.
Tun Sie es nicht:

- **Updates überschreiben sie.** Die Ordner des Kernels gehören zu den Releases des Kernels.
- **Sie können Ihre Dateien nicht von denen des Kernels unterscheiden.** Ein Jahr später weiß niemand mehr, welches
  Template Sie geändert haben.
- **Sie können das Theme nicht wiederverwenden.** Es ist mit einer einzigen Installation vermischt.

In einer Erweiterung hat das Theme seinen eigenen Ordner, sein eigenes Git-Repository, seine eigene Version und Lizenz
und lässt sich mit Composer in beliebig viele Websites installieren.

## Ein echtes Beispiel

se7enxweb/sevenx_themes_simple ist ein öffentliches Theme, und ein gutes zum Lesen. Auf oberster Ebene hat es genau
das, was Teil 3 dieses Buchs für jede Erweiterung beschreibt:

```text
sevenx_themes_simple/
  composer.json  extension.xml  ezinfo.php  LICENSE
  design/simple/            the design "simple"
  settings/                 design.ini and site.ini additions, and per-siteaccess settings
  packages/                 content class packages the theme's templates expect
  translations/             the theme's interface texts
```

Seine `settings/design.ini.append.php` beginnt mit der Zeile, die es zu einer Design-Erweiterung macht:

```ini
[ExtensionSettings]
DesignExtensions[]=sevenx_themes_simple
```

Das nächste Kapitel geht den Rest durch: [Der Aufbau einer Design-Erweiterung](aufbau-einer-design-erweiterung.md).

## Was ein Theme kann und was nicht

| Ein Theme kann | Ein Theme sollte nicht |
|---|---|
| jedes Template ersetzen, vom Pagelayout bis zur Ansicht eines einzelnen Attributs | PHP-Geschäftslogik enthalten (legen Sie sie in eine eigene Erweiterung) |
| View-Modi und Override-Regeln hinzufügen | Kernel-Einstellungen ändern, die nichts mit dem Aussehen zu tun haben |
| Stylesheets, Skripte, Schriften und Bilder mitbringen | Inhalte ausliefern; liefern Sie Inhaltsklassen stattdessen als Pakete aus (wie sevenx_themes_simple) |
| Template-Operatoren hinzufügen, die es braucht (in seinem eigenen `autoloads/`) | von den Knoten-IDs einer bestimmten Installation abhängen |

Der letzte Punkt ist wichtig, wenn ein Theme geteilt wird: Ein Template, das den Knoten 254 abruft, funktioniert auf
Ihrer Website und sonst nirgends. Verwenden Sie für solche Werte Einstellungen mit einem sinnvollen Standardwert.

## Verwandte Seiten

- Zurück: [Teil 6: Theme-Entwickler](README.md)
- Weiter: [Der Aufbau einer Design-Erweiterung](aufbau-einer-design-erweiterung.md)
- [Die Design-Kette](../teil-2-builder/die-design-kette.md)
- [Ein Design in einer Erweiterung](../teil-3-erweiterungsentwickler/design-in-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
