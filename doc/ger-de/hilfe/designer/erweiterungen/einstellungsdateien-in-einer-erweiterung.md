---
title: "Einstellungsdateien in einer Erweiterung"
audience: designer, developer
category: theme-extensions
order: 140
summary: Wie ein Theme Einstellungen als .ini.append.php-Dateien in seinem Ordner settings/ mitliefert, das Dateiformat mit seinem PHP-Kommentarschutz, welche Einstellungen in ein Theme gehören und wie Sie sehen, welche Datei gewinnt.
keywords: Einstellungen, ini, ini.append.php, Einstellungen von Erweiterungen, Override, exp:ini where, charset, design.ini, override.ini, site.ini, settings, extension settings, override
type: help
---
# Einstellungsdateien in einer Erweiterung

Diese Seite ist für Designer, die einem Theme Einstellungen hinzufügen. Der Ordner `settings/` jeder aktiven Erweiterung
wird vom Einstellungssystem gelesen, nach den Standardwerten des Kernels und vor den eigenen Overrides der Installation.

## Das Dateiformat

Eine Einstellungsdatei einer Erweiterung ist nach der Einstellungsdatei benannt, die sie ändert, mit `.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mytheme

*/ ?>
```

- Das `<?php /* ... */ ?>` um die Einstellungen verhindert, dass ein Webserver sie als Text anzeigt, wenn jemand die
  Datei direkt abruft.
- `#?ini charset="utf-8"?` gibt die Kodierung an. Verwenden Sie für neue Dateien UTF-8.
- Eine Zeile `Variable[]` allein (ohne etwas nach den Klammern) leert ein Array, bevor neue Einträge hinzukommen.

## Welche Einstellungen ein Theme meist mitliefert

| Datei | Was ein Theme dort einträgt |
|---|---|
| `design.ini.append.php` | `DesignExtensions[]`, seine Listen für Stylesheets und Skripte |
| `override.ini.append.php` | Override-Regeln für Templates ([Override-Grundlagen](../vorlagen/override-grundlagen.md)) |
| `image.ini.append.php` | [Bildaliase](../theme-design/bildaliase.md), die die Templates des Themes verwenden (Bildaliase) |
| `site.ini.append.php` | Nur, was für jeden Siteaccess unbedenklich ist, zum Beispiel die Registrierung eines Template-Operators |
| `<theme>.ini` | Die eigenen Optionen des Themes ([Einem Theme eine eigene Einstellungsdatei geben](eine-eigene-ini-datei.md)) |

## Wo Einstellungen gewinnen

Vom schwächsten zum stärksten: die `settings/*.ini` des Kernels, die `settings/` der aktiven Erweiterungen (frühere
Erweiterungen gewinnen über spätere), Siteaccess-Einstellungen (auch die innerhalb von Erweiterungen) und schließlich das
`settings/override/` der Installation. Die vollständige Liste steht in
[Ablageorte für Einstellungs-Overrides](../../../spezifikationen/6.0/ini-override-platzierungen.md).

## Schritt für Schritt: eine Einstellung hinzufügen und prüfen

1. Legen Sie die Datei in `extension/mytheme/settings/` an oder bearbeiten Sie sie.
2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Sehen Sie jede Datei, die die Variable setzt, und den geltenden Wert:

   ```bash
   ./console exp:ini where image.ini/AliasSettings/AliasList --allow-root-user
   ```

   Erwartet: die Datei Ihrer Erweiterung unter den Dateien, in Ladereihenfolge, und der geltende Wert.

## Regeln

- Setzen Sie nie Passwörter, API-Schlüssel oder Tokens in die Einstellungen eines Themes. Eine Erweiterung mit
  Geheimnissen lässt sich nicht einmal als Paket exportieren ([Die Geheimnisprüfung](geheimnispruefung-vor-dem-paketieren.md)).
- Liefern Sie nie `settings/override/` in einem Theme mit: Dieser Ordner gehört der Installation.
- Kommentieren Sie jeden Block: Der nächste Betreuer (oder Sie in zwei Jahren) braucht den Grund.

## Verwandte Seiten

- [Siteaccess-Einstellungen in einem Theme](siteaccess-einstellungen-in-einem-theme.md)
- [Anleitung zu Erweiterungen, Abschnitt 5: konfigurieren](../../../anleitungen/extensions.md#5-die-extension-konfigurieren)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
