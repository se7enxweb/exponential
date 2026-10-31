---
title: "Das README eines Themes schreiben"
audience: designer
category: theme-extensions
order: 100
summary: Die Abschnitte, die die README.md eines Themes braucht - was es ist, ein Screenshot, Voraussetzungen, Installation, Einschalten, Konfigurieren, Lizenz und Support - in der Reihenfolge, in der ein Leser sie sucht, mit einer Vorlage zum Kopieren.
keywords: README, README.md, Dokumentation, Installationsanleitung, Voraussetzungen, Screenshot, Support, Vorlage, documentation, install instructions, requirements, template
type: help
---
# Das README eines Themes schreiben

Diese Seite ist für Designer, die ein Theme-Repository vorbereiten. Das README ist die erste Seite, die Menschen auf
GitHub und auf Packagist sehen. Es entscheidet, ob sie das Theme ausprobieren.

## Die Abschnitte, der Reihe nach

1. **Name und ein Satz**: was das Theme ist und für welche Websites.
2. **Ein Screenshot** der Startseite ([Screenshots für ein Theme](../veroeffentlichung/screenshots-fuer-ein-theme.md)).
3. **Voraussetzungen**: die getesteten Versionen von Exponential und PHP und jede Erweiterung, die es braucht.
4. **Installation**: mit Composer und durch Kopieren des Ordners.
5. **Einschalten**: die Einstellungszeilen, die Befehle für Autoloads und Cache.
6. **Konfigurieren**: seine eigene Einstellungsdatei und die Werte, deren Änderung sich lohnt.
7. **Was enthalten ist**: die Design-Namen, die Siteaccess-Einstellungen, Operatoren, Pakete.
8. **Lizenz** und Hinweise Dritter.
9. **Support und Issues**: wo man ein Problem meldet und wie man ein Sicherheitsproblem vertraulich meldet.
10. **Changelog**: ein Link zu `CHANGELOG.md`.

## Eine Vorlage zum Kopieren

````markdown
# My Theme

A responsive theme for Exponential 6 sites with articles, events and a blog.

![The front page in light mode](doc/screenshots/front-light.png)

## Requirements

- Exponential 6.0 (tested with 6.0.15), PHP 8.0 to 8.5

## Install

```bash
composer require yourvendor/mytheme
```

## Switch it on

Add `ActiveExtensions[]=mytheme` to `[ExtensionSettings]` and `SiteDesign=mytheme` to the siteaccess, then:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## License

GNU General Public License v2.0 or later; see LICENSE. Fonts: see design/mytheme/fonts/LICENSE.

## Support

Issues: https://github.com/yourvendor/mytheme/issues. Security problems: see SECURITY.md.
````

## Schritt für Schritt

1. Kopieren Sie die Vorlage in `README.md` im Wurzelverzeichnis des Themes.
2. Ersetzen Sie jeden Wert durch das, was für Ihr Theme stimmt. Schreiben Sie nur Versionen, die Sie getestet haben.
3. Führen Sie jeden Befehl des README auf einer Testinstallation aus und korrigieren Sie, was nicht funktioniert.
4. Lesen Sie es nach dem ersten Push auf GitHub; korrigieren Sie fehlerhafte Bilder und Links.

## Bewährte Praxis

- Schreiben Sie das README in amerikanischem Englisch und nennen Sie das Produkt Exponential.
- Halten Sie Installationsbefehle kopierbar: keine Rückfragen, keine Platzhalter in einem Befehl, ohne es zu sagen.
- Fügen Sie nie Zugangsdaten, Hostnamen echter Kunden oder personenbezogene Daten in ein README ein.

## Verwandte Seiten

- [Ein README, das erklärt und verkauft](../veroeffentlichung/ein-readme-das-erklaert-und-verkauft.md)
- [INSTALL.md und CHANGELOG.md](install-und-changelog-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
