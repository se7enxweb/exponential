---
title: syscard.ini [Providers]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Providers] in syscard.ini: ProviderClasses, Enabled, Priority, Budget"
keywords: Einstellung, syscard.ini, Providers, INI, setting, ProviderClasses, Enabled, Priority, Budget
type: generated
generated: exp:help:examples
---
# syscard.ini [Providers]

Der Block `[Providers]` von `settings/syscard.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ProviderClasses

> The providers, in the order their facts are shown. A provider says itself which cards it belongs to: the ones  
> of exp:expfetch are on all three cards, the inventory ones only on exp:info, the project ones only on exp:about.  

Standard: eine Liste mit 16: `expSysCardExponentialProvider`, `expSysCardHostProvider`, `expSysCardPhpProvider`, `expSysCardVelocityProvider`, `expSysCardWebServerProvider`, `expSysCardDatabaseProvider`, `expSysCardSiteProvider`, `expSysCardCacheProvider`, `expSysCardHelpProvider`, `expSysCardCronProvider`, `expSysCardComposerProvider`, `expSysCardExtensionsProvider` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Providers]
# ein leeres "ProviderClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ProviderClasses[]
ProviderClasses[]=expSysCardExponentialProvider
ProviderClasses[]=expSysCardHostProvider
ProviderClasses[]=expSysCardPhpProvider
ProviderClasses[]=expSysCardVelocityProvider
ProviderClasses[]=expSysCardWebServerProvider
ProviderClasses[]=expSysCardDatabaseProvider
ProviderClasses[]=expSysCardSiteProvider
ProviderClasses[]=expSysCardCacheProvider
ProviderClasses[]=expSysCardHelpProvider
ProviderClasses[]=expSysCardCronProvider
ProviderClasses[]=expSysCardComposerProvider
ProviderClasses[]=expSysCardExtensionsProvider
ProviderClasses[]=expSysCardSiteAccessProvider
ProviderClasses[]=expSysCardSettingsProvider
ProviderClasses[]=expSysCardQueueProvider
ProviderClasses[]=expSysCardAboutProvider
*/ ?>
```

## Enabled

> Switch a provider off by its id (exponential, host, php, velocity, webserver, database, site, caches, help, cron,  
> composer, extensions, siteaccess, settings, queue, about): Enabled\[velocity\]=disabled  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Providers]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
*/ ?>
```

## Priority

> Change when a provider runs (the higher, the earlier; when the total budget is spent, the lowest are left out)  
> and how many seconds it may take: Priority\[help\]=90, Budget\[database\]=5  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Providers]
# ein leeres "Priority[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Priority[]
*/ ?>
```

## Budget

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Providers]
# ein leeres "Budget[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Budget[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
