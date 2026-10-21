---
title: "Der Composer-Installer und der Ordnername der Erweiterung"
audience: designer, developer
category: theme-extensions
order: 310
summary: Wie der Installer für Legacy-Erweiterungen den Ordner bestimmt, in dem ein Theme landet - extra.ezpublish-legacy-extension-name, sonst der Paketname -, warum installer-name nicht gelesen wird und wie Sie das Ergebnis vor der Veröffentlichung prüfen.
keywords: Composer-Installer, ezpublish-legacy-extension-name, installer-name, Ordnername der Erweiterung, LegacyExtensionInstaller, exponential-legacy-installer, ezpublish-legacy-installer, Installationspfad, composer installer, extension folder name, install path
type: help
---
# Der Composer-Installer und der Ordnername der Erweiterung

Diese Seite ist für Designer, die ein Theme als Composer-Paket veröffentlichen. Der Ordnername ist wichtig: Einstellungen
wie `DesignExtensions[]=mytheme` und `ActiveExtensions[]=mytheme` nennen ihn, und ein Theme, das in einen Ordner mit
anderem Namen installiert wurde, wird nicht gefunden.

## Wie der Ordner gewählt wird

Das Installer-Plugin (`se7enxweb/exponential-legacy-installer`, Klasse `LegacyExtensionInstaller`) behandelt Pakete vom
Typ `ezpublish-legacy-extension`. Seine Regel, laut Quellcode:

1. Hat `composer.json` den Eintrag `extra.ezpublish-legacy-extension-name`, ist das der Ordnername.
2. Sonst ist der Teil des Paketnamens nach dem Vendor der Ordnername.

Der Ordner ist `<legacy root>/extension/<folder name>`.

| `composer.json` | Ordner |
|---|---|
| `"name": "acme/mytheme"`, kein extra | `extension/mytheme` |
| `"name": "acme/my-theme"`, kein extra | `extension/my-theme` (ein Bindestrich: kein gültiger Erweiterungsname für die Assistenten) |
| `"name": "acme/my-theme"`, `extra.ezpublish-legacy-extension-name: my_theme` | `extension/my_theme` |

## Eine Falle in erzeugten Dateien

Der [Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md) schreibt `"name": "<vendor>/<name with dashes>"`. Ab
Exponential 6.0.15 schreibt er auch `extra.ezpublish-legacy-extension-name`, sodass der Ordner der Name der Erweiterung
ist. Ältere Assistenten schrieben nur `"extra": { "installer-name": "<name>" }`, und dieser Installer liest
`installer-name` **nicht**: Für ein Theme namens `my_theme` installiert sich das Paket nach `extension/my-theme`. Fügen
Sie in einer solchen Datei den Schlüssel hinzu, den der Installer liest:

```json
"extra": {
    "ezpublish-legacy-extension-name": "my_theme"
}
```

Sie können `installer-name` für andere Installer behalten; es schadet nicht.

## Zwei Installer-Pakete

Veröffentlichte Themes verlangen `se7enxweb/ezpublish-legacy-installer`; das Wurzelverzeichnis von Exponential verlangt
`se7enxweb/exponential-legacy-installer`. Beide sind auf Packagist, und das Wurzelverzeichnis erlaubt beide als
Composer-Plugins (`config.allow-plugins`). Eines davon in Ihrem Theme zu verlangen, genügt, damit der Typ behandelt wird.

## Schritt für Schritt: prüfen

1. Lesen Sie Ihren `extra`-Block: `grep -n -A3 '"extra"' composer.json`.
2. Installieren Sie das Theme in eine Testinstallation ([Eine Composer-Installation testen](composer-installation-lokal-testen.md)).
3. Prüfen Sie den Ordner: `ls -d extension/<expected name>` gibt den Ordner aus.
4. Mit `composer -v` gibt der Installer auch das Verzeichnis aus, das er verwendet.

## Verwandte Seiten

- [composer.json für ein Theme](composer-json-fuer-ein-theme.md)
- [Ihre Theme-Erweiterung benennen](ihre-theme-erweiterung-benennen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
