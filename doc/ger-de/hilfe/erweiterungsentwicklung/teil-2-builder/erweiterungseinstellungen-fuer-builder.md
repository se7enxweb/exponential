---
title: Einstellungen in einer Erweiterung
audience: developer, designer, app-creator
category: extension-development
order: 40
summary: Die Konfiguration einer Website in einer Einstellungserweiterung unter Versionskontrolle halten - der Aufbau des Ordners, globale Einstellungen und Einstellungen je Siteaccess, vorhandene Einstellungen mit exp:ini move-all übernehmen und was draußen bleibt (Geheimnisse).
keywords: Einstellungserweiterung, Website-Erweiterung, Konfiguration, Versionskontrolle, move-all, create-extension, Siteaccess-Einstellungen in einer Erweiterung, override__group, Geheimnisse, settings extension, site extension, configuration, version control, secrets
type: help
---
# Einstellungen in einer Erweiterung

Teil 2, Kapitel 4. Eine **Einstellungserweiterung** ist eine Erweiterung, die nichts als Einstellungen enthält: die
Konfiguration Ihrer Website. Sie ist die erste Erweiterung, die die meisten Builder anlegen, und die sicherste: Sie
bringt keinen Code mit und kann daher nichts kaputt machen, was nicht schon Einstellungen kaputt machen könnten. In
diesem Kapitel bauen Sie eine.

## Warum Einstellungen in eine Erweiterung gehören

| Einstellungen in `settings/override/` und `settings/siteaccess/` | Einstellungen in einer Erweiterung |
|---|---|
| Vermischt mit serverspezifischen Werten und Geheimnissen | Nur die Konfiguration der Website; Geheimnisse bleiben draußen |
| Schwer unter Versionskontrolle zu stellen | Ein Ordner, ein Git-Repository |
| Von Hand auf einen zweiten Server kopiert | Installiert wie jede Erweiterung |
| Gehen bei einer Neuinstallation verloren oder werden schlecht zusammengeführt | Überstehen eine Neuinstallation: Schalten Sie die Erweiterung wieder ein |

## Der Aufbau

```text
extension/mysite_settings/
    ezinfo.php
    extension.xml
    settings/
        site.ini.append.php            settings for every siteaccess
        content.ini.append.php
        image.ini.append.php
        siteaccess/
            mysite/                    one site's own settings (makes the extension its owner)
                site.ini.append.php
                override.ini.append.php
            mysite_admin/
                site.ini.append.php
```

Die Metadatendateien sind dieselben wie bei jeder Erweiterung; siehe [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md)
und [extension.xml](../teil-3-erweiterungsentwickler/extension-xml.md).

## Mit einem Befehl bauen

`exp:ini move-all` verschiebt jede Einstellungsdatei einer Ebene in eine andere und kann dabei die Erweiterung anlegen:

```bash
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --dry-run
```

Der Probelauf gibt aus, was verschoben würde. Wenn es stimmt:

```bash
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --activate
```

`--create-extension` schreibt `extension.xml`, `ezinfo.php` und `settings/`, wenn die Erweiterung noch nicht existiert;
`--activate` fügt sie in der globalen Override-Datei zu `ActiveExtensions[]` hinzu. Eine Verschiebung wird
zurückgenommen, wenn sich ein wirksamer Wert ändern würde, außer Sie geben `--force` an; `--files=site,content`
beschränkt sie auf einige Dateien. Danach zeigt

```bash
./console exp:ini where site.ini/DesignSettings/SiteDesign -s mysite
```

dass der Wert nun aus `ext-siteaccess:mysite_settings` kommt.

## Oder von Hand

```bash
mkdir -p extension/mysite_settings/settings/siteaccess/mysite
```

Legen Sie `extension/mysite_settings/settings/siteaccess/mysite/site.ini.append.php` an:

```php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=My site

[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

*/ ?>
```

Fügen Sie die Metadatendateien hinzu, schalten Sie die Erweiterung ein und leeren Sie den INI-Cache:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_settings global
php bin/php/ezcache.php --clear-tag=ini
```

## Was nicht hineingehört

- **Datenbankeinstellungen und Passwörter** (`[DatabaseSettings]`), Zugangsdaten des Mailservers, API-Schlüssel,
  Geheimnisse jeder Art. Sie bleiben in `settings/override/`, das nie committet wird.
- **Werte, die sich zwischen Ihrem Entwicklungs- und Ihrem Live-Server unterscheiden** (Hostnamen, Pfade,
  Debug-Schalter). Halten Sie sie im `settings/override/` des jeweiligen Servers.
- **Dateien einer anderen Erweiterung.** Überschreiben Sie deren Einstellungen; kopieren und bearbeiten Sie nie ihre
  Dateien.

Die `.gitignore` einer Einstellungserweiterung kann leer sein; darin ist absichtlich nichts Geheimes. Wenn Sie unsicher
sind: `exp:ini` maskiert bei der Ausgabe die Werte von Variablen, deren Name `Password`, `Secret`, `Token` oder `Salt`
enthält oder auf `...Key` endet; das ist ein guter Hinweis darauf, was draußen bleiben sollte.

## Die Reihenfolge zählt

Eine Einstellungserweiterung, die gegen andere Erweiterungen gewinnen soll, muss in `ActiveExtensions[]` **vor** ihnen
stehen (eine frühere Erweiterung hat die höhere Priorität). Auf der Seite Extensions in der Administration können Sie
sie nach oben schieben, und dort sehen Sie auch die Ladereihenfolge, die der Kernel tatsächlich verwendet; siehe
[Ladereihenfolge der Erweiterungen](../../../../eng-us/features/6.0/extension-loading-order.md).

## Übung

1. Führen Sie den Probelauf von `move-all` für einen Ihrer Siteaccesses aus.
2. Lesen Sie, was er verschieben würde. Ist etwas Geheimes darunter? Dann planen Sie, diesen Block in
   `settings/override/` zu behalten (`exp:ini move` für einzelne Blöcke gibt Ihnen diese Kontrolle).
3. Führen Sie die Verschiebung auf Ihrer Entwicklungsinstallation aus und stellen Sie die neue Erweiterung unter Git:
   `git -C extension/mysite_settings init`.

## Verwandte Seiten

- Zurück: [Siteaccess-Einstellungen](siteaccess-einstellungen.md)
- Weiter: [Einstellungen mit exp:ini ändern](der-befehl-exp-ini.md)
- [Einstellungen je Website in Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)
- [Mit RAD: eine Erweiterung nur mit Einstellungen](../teil-3-erweiterungsentwickler/tutorials/rad-einstellungs-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
