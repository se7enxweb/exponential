---
title: "Ein Theme bei Packagist einreichen"
audience: designer, developer
category: theme-distribution
order: 190
summary: Ein Theme als Composer-Paket auf Packagist veröffentlichen - Voraussetzungen, die Seite Submit, die Paketseite prüfen und composer require aus einer sauberen Installation testen.
keywords: Packagist, einreichen, Composer-Paket, packagist.org, composer require, Vendor-Name, Paketseite, submit, composer package, vendor name, package page
type: help
---
# Ein Theme bei Packagist einreichen

Diese Seite ist für Designer, deren Theme in einem öffentlichen Git-Repository mit einer gültigen `composer.json` liegt.
Packagist ist der Hauptindex, den Composer durchsucht; sobald Ihr Theme dort ist, kann jeder
`composer require yourvendor/mytheme` ausführen.

## Bevor Sie beginnen

- Ein öffentliches Repository mit `composer.json` im Wurzelverzeichnis ([composer.json für ein Theme](../erweiterungen/composer-json-fuer-ein-theme.md)).
- `composer validate` läuft fehlerfrei durch.
- Mindestens ein Release-Tag ist gepusht ([GitHub-Releases](github-releases.md)). Ohne Tag listet Packagist nur den
  Branch (`dev-main`).
- Ein Packagist-Konto. Die Anmeldung mit GitHub macht die automatischen Updates am einfachsten ([Packagist automatisch
  aktuell halten](packagist-auto-update.md)).
- Der Paketname ist frei: `https://packagist.org/packages/yourvendor/mytheme` zeigt „not found“.

## Schritt für Schritt

1. Melden Sie sich auf [packagist.org](https://packagist.org/) an.
2. Öffnen Sie **Submit** (`https://packagist.org/packages/submit`).
3. Geben Sie die URL des Repositorys ein (`https://github.com/yourvendor/mytheme`) und drücken Sie **Check**. Packagist
   liest `composer.json` und zeigt den gefundenen Paketnamen.
4. Bestätigen Sie mit **Submit**. Die Paketseite öffnet sich, und die Versionen erscheinen, nachdem Packagist das
   Repository gelesen hat.
5. Prüfen Sie die Paketseite: Name, Beschreibung, Lizenz, das README und die Versionen (`v1.0.0` wird als `1.0.0`
   angezeigt, dazu `dev-main`).
6. Richten Sie sofort die automatische Aktualisierung ein ([Packagist automatisch aktuell halten](packagist-automatisch-aktualisieren.md)).
7. Testen Sie aus einer sauberen Installation:

   ```bash
   composer require yourvendor/mytheme
   ```

   Erwartet: Composer installiert die neueste getaggte Version nach `extension/<name>`.

## Was Packagist liest

Packagist liest `composer.json` aus jedem Tag und jedem Branch. Die Version kommt aus dem Namen des Tags; ein
`version`-Feld in `composer.json` muss, falls vorhanden, damit übereinstimmen. Ein Theme, das sich die Theme-Repositories
von 7x zum Vorbild nimmt, kann vergleichen: `https://repo.packagist.org/p2/se7enxweb/cjw_themes_jumper.json` listet jede
Version mit ihrer Lizenz und Quelle.

## Verwandte Seiten

- [Eine Composer-Installation vor der Veröffentlichung testen](../erweiterungen/composer-installation-lokal-testen.md)
- [Packagist zeigt eine alte Version](veraltete-packagist-version.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
