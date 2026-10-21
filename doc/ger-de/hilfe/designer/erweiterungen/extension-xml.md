---
title: "extension.xml: die paketierte Beschreibung"
audience: designer, developer
category: theme-extensions
order: 60
summary: Die extension.xml eines Themes mit ihrem Metadatenblock (Name, Version, Copyright, Lizenz, Website) und optionalen Abhängigkeiten schreiben, mit xmllint prüfen und gleich mit ezinfo.php halten.
keywords: extension.xml, Metadaten, software, Version, Lizenz, info_url, Copyright, Abhängigkeiten, xmllint, Seite Über, metadata, license, dependencies, About page
type: help
---
# extension.xml: die paketierte Beschreibung

Diese Seite ist für Designer, die ein Theme für ein Release vorbereiten. `extension.xml` ist die zweite Metadatendatei
einer Erweiterung. **Die Seite Über liest sie zuerst**, wenn sie einen Block `<metadata>` hat; ein falscher Wert hier
gewinnt also über einen richtigen in [`ezinfo.php`](ezinfo-php.md).

## Ein vollständiges Beispiel

Dies ist die Datei von `cjw_themes_jumper` 0.1.6, wie veröffentlicht:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>CJW Themes Jumper</name>
        <version>0.1.6</version>
        <copyright>Copyright (C) 2007 - 2026 CJW Network, JAC Systeme GmbH and 7x</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/se7enxweb/cjw_themes_jumper</info_url>
    </metadata>
</software>
```

## Schritt für Schritt

1. Legen Sie `extension/mytheme/extension.xml` mit dem Wurzelelement `<software>` und einem Block `<metadata>` an.
2. Füllen Sie die Elemente:

   | Element | Wert | Muss gleich sein mit |
   |---|---|---|
   | `<name>` | Lesbarer Name | `Name` in `ezinfo.php` |
   | `<version>` | Release-Version ohne `v` | `Version` in `ezinfo.php`, dem Tag, `version` in `composer.json`, falls Sie eines führen |
   | `<copyright>` | Jahre und Rechteinhaber | `Copyright` in `ezinfo.php` |
   | `<license>` | Der volle Name der Lizenz | `License` in `ezinfo.php` und der Datei `LICENSE` |
   | `<info_url>` | Projektseite | `Info_url` in `ezinfo.php` |

3. Braucht das Theme eine andere Erweiterung, fügen Sie einen Block `<dependencies>` hinzu
   ([Abhängigkeiten in extension.xml deklarieren](abhaengigkeiten-in-extension-xml.md)).
4. Prüfen Sie, dass das XML wohlgeformt ist:

   ```bash
   xmllint --noout extension/mytheme/extension.xml
   ```

   Erwartet: keine Ausgabe. Jede Ausgabe nennt die fehlerhafte Zeile.
5. Laden Sie **Setup > Über** und **Setup > Extensions** neu: Die Zeile zeigt die Werte dieser Datei.

## Werte, die Sie nie schreiben

- `//autogentag//` oder `dev-master` als Version: Das waren vor langer Zeit Platzhalter von Build-Skripten, und sie sagen
  einem Leser nichts.
- Einen Lizenznamen, der von der Datei `LICENSE` abweicht.
- Eine Website, die nicht Ihre ist, zum Beispiel die Website des Produkts für ein Theme, das das Produkt nicht pflegt.

## Verwandte Seiten

- [ezinfo.php: was die Seite Über liest](ezinfo-php.md)
- [Alle Versionsfelder synchron halten](versionsfelder-synchron-halten.md)
- [Spezifikation: Metadaten von Erweiterungen](../../../spezifikationen/6.0/extension-metadaten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
