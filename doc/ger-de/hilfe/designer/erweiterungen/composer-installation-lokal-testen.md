---
title: "Eine Composer-Installation vor der Veröffentlichung testen"
audience: designer, developer
category: theme-extensions
order: 330
summary: Ihr Theme über ein Composer-Repository vom Typ path oder vcs in eine Wegwerf-Installation von Exponential installieren, bevor Sie es bei Packagist einreichen, und Ordnername, Dateien und dargestellte Seite prüfen.
keywords: Composer-Test, path-Repository, vcs-Repository, lokale Installation, vor Packagist, Testinstallation, composer require dev-main, composer test, path repository, vcs repository, local install, before Packagist, test installation
type: help
---
# Eine Composer-Installation vor der Veröffentlichung testen

Diese Seite ist für Designer, die sicher sein wollen, dass `composer require yourvendor/mytheme` funktioniert, bevor es
jemand anderes versucht. Verwenden Sie eine Wegwerf-Installation, nie eine Live-Website.

## Bevor Sie beginnen

- Eine mit Composer installierte Testinstallation von Exponential, die Sie ändern und wegwerfen dürfen.
- Ihr Theme in einem eigenen Git-Repository mit einer gültigen `composer.json`.

## Schritt für Schritt mit einem path-Repository

1. Fügen Sie in der `composer.json` der Testinstallation ein Repository hinzu, das auf den Ordner Ihres Themes zeigt:

   ```bash
   composer config repositories.mytheme '{"type": "path", "url": "/home/you/src/mytheme", "options": {"symlink": false}}'
   ```

2. Binden Sie es ein:

   ```bash
   composer require yourvendor/mytheme:@dev
   ```

   Erwartet: Composer meldet, dass es `yourvendor/mytheme` aus dem path-Repository installiert.

3. Prüfen Sie den Ordner: `ls extension/mytheme/ezinfo.php extension/mytheme/extension.xml`. Hat der Ordner einen anderen
   Namen, korrigieren Sie `extra.ezpublish-legacy-extension-name`
   ([Der Composer-Installer und der Ordnername](composer-installer-und-erweiterungsname.md)).
4. Aktivieren Sie das Theme, erzeugen Sie die Autoloads neu, leeren Sie die Caches und öffnen Sie eine Seite eines
   Siteaccess, der es verwendet ([Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md)).
5. Prüfen Sie, dass nichts angekommen ist, was Sie nicht ausliefern wollten:
   `find extension/mytheme -name '*.bak' -o -name '.env'` gibt nichts aus.

## Schritt für Schritt mit Ihrem gepushten Repository

Um genau das zu testen, was Nutzer bekommen, sobald es öffentlich ist:

```bash
composer config repositories.mytheme vcs https://github.com/yourvendor/mytheme
composer require yourvendor/mytheme:dev-main
```

Nachdem Sie ein Release getaggt haben, testen Sie den Tag auf dieselbe Weise: `composer require yourvendor/mytheme:1.0.0`.

## Aufräumen

Entfernen Sie den Repository-Eintrag für den Test (`composer config --unset repositories.mytheme`) oder werfen Sie die
Installation weg. Lassen Sie keine path-Repositories in einer Installation zurück, die andere verwenden.

## Verwandte Seiten

- [composer.json für ein Theme](composer-json-fuer-ein-theme.md)
- [Ein Theme bei Packagist einreichen](../veroeffentlichung/bei-packagist-einreichen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
