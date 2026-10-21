---
title: "Vom Ergebnis des Assistenten zum ersten Commit"
audience: designer
category: theme-extensions
order: 270
summary: Die erste Stunde nach dem Erzeugen eines Themes - jede Datei prüfen, Metadaten und composer.json korrigieren, das Theme für einen Test-Siteaccess einschalten, es dargestellt sehen und den ersten Commit machen.
keywords: erster Commit, Ergebnis des Assistenten, Checkliste, Test-Siteaccess, composer.json korrigieren, Metadaten, git init, first commit, wizard output, checklist, test siteaccess, metadata
type: help
---
# Vom Ergebnis des Assistenten zum ersten Commit

Diese Seite ist für Designer, die gerade mit dem [Design-Erweiterungs-Assistenten](der-design-erweiterungs-assistent.md) ein Theme
erzeugt haben. Folgen Sie ihr einmal der Reihe nach; am Ende haben Sie ein Theme, das dargestellt wird, und einen
sauberen ersten Commit.

## Schritt für Schritt

1. **Sehen Sie sich den Baum an**:

   ```bash
   find extension/mytheme -type f | sort
   ```

   Vergleichen Sie mit [Die Teile des Design-Assistenten](die-teile-des-design-assistenten.md).

2. **Prüfen Sie die PHP- und XML-Dateien**:

   ```bash
   php -l extension/mytheme/ezinfo.php
   xmllint --noout extension/mytheme/extension.xml
   ```

   Erwartet: `No syntax errors detected` und keine Ausgabe von `xmllint`.

3. **Korrigieren Sie `composer.json`**: Prüfen Sie, dass `extra` den Eintrag `"ezpublish-legacy-extension-name": "mytheme"`
   hat (ab Exponential 6.0.15 vom Assistenten geschrieben; ergänzen Sie ihn bei der Ausgabe eines älteren Assistenten),
   setzen Sie die PHP-Versionen, die Sie testen, und fügen Sie `homepage`, `support` und `authors` hinzu
   ([composer.json für ein Theme](composer-json-fuer-ein-theme.md)). Prüfen: `composer validate extension/mytheme/composer.json`.

   Nehmen Sie die drei Änderungen an erzeugtem Kopf und Layout vor (der Titel, `{ezcss_load()}` und `{ezscript_load()}`,
   ein einfaches `<meta charset="utf-8" />`), wenn das Theme von einem Assistenten älter als 6.0.15 erzeugt wurde
   ([Drei Änderungen, die der erzeugte Kopf und das Layout brauchen](die-teile-des-design-assistenten.md#drei-änderungen-die-der-erzeugte-kopf-und-das-layout-brauchen)).

4. **Schalten Sie es** für die Installation **ein** und wählen Sie es für einen Test-Siteaccess
   ([Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md),
   [Die Design-Einstellungen, die ein Theme mitliefert](design-einstellungen-eines-themes.md)).

5. **Machen Sie es sichtbar**:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

6. **Öffnen Sie den Test-Siteaccess** in einem Browser. Die Seite verwendet Ihre `pagelayout.tpl`; bei eingeschaltetem
   Template-Debugging nennen die Pfadkommentare Dateien in `extension/mytheme/design/mytheme/`.

7. **Lesen Sie das README, das der Assistent geschrieben hat**, und korrigieren Sie alles, was für Ihr Theme nicht stimmt.

8. **Committen Sie**:

   ```bash
   cd extension/mytheme
   git init -b main
   git add .
   git status --short
   git commit -m "Added: The mytheme theme extension"
   ```

   Lesen Sie `git status --short` vor dem Commit: Nur die Dateien des Themes sollten aufgeführt sein.

## Was meist als Nächstes geändert werden muss

- Der Kopf des Pagelayouts: das Laden von Stylesheets und Skripten ([Stylesheets laden](../theme-design/stylesheets-laden.md)).
- Der Beispiel-Override für `article` ([Override-Grundlagen](../vorlagen/override-grundlagen.md)).
- `ezinfo.php` und `extension.xml`: Website und Copyright.

## Verwandte Seiten

- [Ein Theme unter Git stellen](ein-theme-unter-git-stellen.md)
- [Checkliste: Die Theme-Erweiterung ist bereit](checkliste-theme-erweiterung-bereit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
