---
title: Umgebungsspezifische Einstellungen einrichten
audience: government, admin, developer
category: public-sector
order: 330
summary: Schritt für Schritt - die Umgebung jeder Maschine mit EXP_ENV in config.env.php benennen und die abweichenden Einstellungen (E-Mail-Transport, Debug-Ausgabe, das Kennzeichen in der Administration) in Variantendateien halten, sodass eine Testkopie nie Bürger anschreibt und die Produktion nie Debug-Ausgabe zeigt.
keywords: EXP_ENV, config.env.php, Umgebungen, dev, test, prod, Staging, Varianten von Einstellungen, site.ini.test.append.php, E-Mail-Transport, Debug, Kennzeichen der Umgebung in der Administration, environments, staging, settings variants, mail transport, debug, admin environment badge
type: help
---
# Umgebungsspezifische Einstellungen einrichten

Diese Aufgabe ist für Administratoren und Entwickler, die Entwicklungs-, Test- und Produktionskopien einer öffentlichen
Website betreiben. Am Ende kennt jede Maschine ihre Umgebung und liest automatisch die richtigen Einstellungen
([Einstellungen je Umgebung](../../../../../eng-us/features/6.0/environment-settings.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang auf jeder Maschine.
- Eine Liste dessen, was sich je Umgebung unterscheidet (E-Mail, Debug, Datenbanken, das Kennzeichen in der
  Administration).

## Schritte

1. Kopieren Sie auf jeder Maschine das Beispiel und setzen Sie den Namen (`dev`, `test`, `prod` oder Ihr eigener:
   Kleinbuchstaben, Ziffern, `-` oder `_`, mit einem Buchstaben beginnend, höchstens 32 Zeichen):

   ```bash
   cp config.env.php.example config.env.php
   ```

   ```php
   <?php
   define( 'EXP_ENV', 'test' );
   ```

   `config.env.php` wird durch `.gitignore` aus der Versionsverwaltung herausgehalten.
2. Legen Sie die Unterschiede in Variantendateien neben die Standarddateien, den Namen der Umgebung direkt nach `.ini`:

   ```ini
   # settings/override/site.ini.test.append.php
   <?php /*
   [MailSettings]
   Transport=file

   [DebugSettings]
   DebugOutput=enabled
   DebugByIP=enabled
   DebugIPList[]
   DebugIPList[]=192.0.2.10/32 ; tester workstation
   */ ?>
   ```

   Die Variantendateien kommen in die Versionsverwaltung; nur `config.env.php` unterscheidet sich je Maschine.
3. Leeren Sie den INI-Cache einmal (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`); jede Umgebung hat
   danach ihre eigenen Cache-Dateien.
4. Optional: `site.ini [AdminEnvironment] Level=auto` zeigt die Umgebung als Kennzeichen im Kopfbereich von admin4 und
   auf der Anmeldeseite.

## Prüfen, ob es funktioniert hat

```bash
./console exp:ini get 'site.ini/MailSettings/Transport' --allow-root-user
```

gibt auf der Testmaschine `file` und in der Produktion Ihren E-Mail-Transport aus. Templates können
`{exp_environment()}` prüfen.

## Fallstricke

- `dev` und `test` schalten in `index.php` `display_errors` von PHP ein; setzen Sie sie niemals in der Produktion.
- `settings/override` hat weiterhin Vorrang vor den Einstellungen eines Siteaccess; eine Variante überschreibt nur ihre
  eigene Ebene.
- Ohne `EXP_ENV` werden genau die alten Dateien gelesen; ein ungültiger Name zählt als keiner (und wird im Fehler-Log
  von PHP vermerkt).

## Verwandte Seiten
- [Umgebungen: Entwicklung, Test und Produktion](../../../../../eng-us/help/government/operations/hosting/environments-dev-test-prod.md)
- [Staging und Genehmigung](../../../../../eng-us/help/government/operations/upgrades-and-change/staging-and-approval.md)
- [Probleme mit E-Mails](../../../../../eng-us/help/government/operations/incidents/mail-problems.md)
- [Die Konfiguration mit ManagedByDeployment sperren](die-konfiguration-mit-managedbydeployment-sperren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
