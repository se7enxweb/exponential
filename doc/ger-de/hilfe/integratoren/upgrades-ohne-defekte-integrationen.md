---
title: Upgrades ohne defekte Integrationen
audience: integrator, developer, admin
category: integration
order: 400
summary: Integrationen bei Upgrades von Exponential, Erweiterungen und PHP funktionsfähig halten - was sich unter einer Integration typischerweise ändert, die Hinweise zu Verhaltensänderungen lesen, Versionen mit Composer festlegen, eine Generalprobe auf einer Kopie, Regressionstests aus den Abnahmefällen und die Reihenfolge eines Upgrades mit pausierten Integrationen.
keywords: Upgrade, Update, Verhaltensänderungen, Versionsbedingung, Versionen festlegen, veraltet, PHP-Upgrade, Regressionstest, Generalprobe, Cronjobs pausieren, Wartungsfenster, upgrade, update, behavior changes, bc notes, composer constraint, tilde, version pin, deprecation, ezprestapiprovider, PHP upgrade, regression test, rehearsal, pause cronjobs, maintenance window, upgrade check
type: help
---
# Upgrades ohne defekte Integrationen

Diese Seite richtet sich an Integratoren und Betreiber, die eine Installation aktualisieren müssen, von der andere
Systeme abhängen. Exponential wird aktiv gepflegt; jedes Release bringt Korrekturen, Sicherheitshärtung und neue
Funktionen, und einige davon ändern Verhalten, auf das sich eine Integration verlässt. Upgrades sind sicher, wenn Sie
wissen, wo Sie nachsehen müssen, und einmal proben. Das Upgrade selbst beschreibt [Upgrade](../../anleitungen/aktualisieren.md).

## Inhalt

1. [Was sich unter einer Integration typischerweise ändert](#was-sich-unter-einer-integration-typischerweise-ändert)
2. [Vor dem Upgrade lesen](#vor-dem-upgrade-lesen)
3. [Versionen und Composer](#versionen-und-composer)
4. [Das Upgrade, mit Integrationen](#das-upgrade-mit-integrationen)
5. [Integrationen schreiben, die gut altern](#integrationen-schreiben-die-gut-altern)

## Was sich unter einer Integration typischerweise ändert

| Bereich | Beispiel aus der Linie 6.0 | Wirkung auf eine Integration |
|---|---|---|
| Eingabeprüfung | Datentypen lehnen Werte ab, die sie früher gespeichert haben (`ezprice` lehnt einen nicht numerischen Preis ab, `ezurl` lehnt unsichere Schemata ab, `ezboolean` bildet Text auf 0 oder 1 ab) | Ein Import, der schludrige Werte gesendet hat, scheitert jetzt bei diesen Datensätzen; gut so, aber planen Sie es ein |
| Rechte | Der REST-Inhalts-Controller prüft seit 6.0.15 auf jeder Route Inhaltsrichtlinien | Ein Schlüssel oder Token, dessen Benutzer eine Richtlinie fehlt, erhält `403`, wo es früher gelang |
| Erweiterungen | `ezprestapiprovider` ist veraltet; `ezprestapi` bedient v1 und v2 | Overrides, die die alte Provider-Klasse nennen, funktionieren über eine Brücke; wechseln Sie zu `ezp7xRestApiProvider` |
| Operationen und Trigger | Operationen jenseits der ausgelieferten Liste in `AvailableOperationList[]` können sich ohne Kompatibilitätsregeln ändern | Workflow-Ereignisse an zusätzlichen Operationen brauchen nach jedem Upgrade einen Test |
| Laufzeitumgebung | Worker von Velocity, Abkündigungen in PHP 8.x | Statische Variablen und dynamische Eigenschaften in altem Code; Neustart nach Klassenänderungen |
| Schema | Neue Tabellen und Spalten (zum Beispiel `expapikey`) | Führen Sie das Datenbank-Update Ihrer Engine aus, bevor Sie die Funktion verwenden |

## Vor dem Upgrade lesen

- Die Hinweise zu **Verhaltensänderungen** in `doc/eng-us/bc/6.0/` (zum Beispiel
  [Verhaltensänderungen 2026-10](../../bc/6.0/verhaltensaenderungen-2026-10.md) und
  [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)): Jeder sagt, was sich
  geändert hat, wer betroffen ist, wie man es prüft und wie man es behebt.
- Die Release-Hinweise jeder Erweiterung, die Ihre Integration verwendet (`doc/changelogs/extensions/`).
- Die Checkliste der Upgrade-Anleitung für jeden Zeitraum, den Sie durchlaufen
  ([Upgrade, Teil C](../../anleitungen/aktualisieren.md#teil-c-von-einer-beliebigen-60x-auf-den-aktuellen-stand)).

Durchsuchen Sie sie nach den Namen, die Ihre Integration verwendet: die Klassen, die sie aufruft, die Einstellungen,
die sie setzt, die Routen, die sie aufruft.

## Versionen und Composer

- Die `composer.json` im Wurzelverzeichnis verwendet Tilde-Bedingungen (`~1.2.6`): Die letzte Stelle bleibt frei,
  `composer update` bringt also Korrekturen derselben Linie, aber keine neue Nebenlinie.
- Ihre Integrationserweiterung deklariert in ihrer eigenen `composer.json`, was sie braucht
  (`"se7enxweb/ezprestapi": "~1.2.6"`), damit eine Installation nicht in eine Linie aktualisieren kann, die Sie nicht
  getestet haben
  ([composer.json für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md)).
- Halten Sie die Versionen in den Übergabedokumenten fest; `composer show` und **Setup > Extensions** listen sie auf.

## Das Upgrade, mit Integrationen

1. **Proben Sie auf einer Kopie** der Produktion mit denselben Versionen der Integration; führen Sie die Abnahmefälle
   und die Probeläufe der Importe aus ([Integrationen testen](integrationen-testen.md)).
2. **Informieren Sie die Verantwortlichen der anderen Systeme** über das Fenster; Aufrufe währenddessen können
   scheitern.
3. **Pausieren Sie** die Cronjob-Teile der Integration unter Setup > Cronjobs (und die Queue-Worker), damit kein
   Import halb auf dem alten und halb auf dem neuen Code läuft
   ([Cronjobs für Integrationen](cronjobs-fuer-integrationen.md#anhalten-während-der-wartung)).
4. Schalten Sie wahlweise den Wartungsmodus ein (`php bin/php/maintenance.php on --message="..." --until=1h`);
   API-Clients erhalten `503` mit `Retry-After` und sollten zurückweichen.
5. **Aktualisieren Sie**: Code, Datenbank-Update, Autoloads, Caches, `php bin/php/console exp:velocity deploy`.
6. **Prüfen Sie**: die Abnahmefälle, einen Probelauf je Import, einen Aufruf je API-Client, `exp:audit sinks list`.
7. **Setzen Sie** die Cronjob-Teile **fort** und beobachten Sie die ersten Läufe.
8. Halten Sie im Änderungsprotokoll der Integration fest, was sich für sie geändert hat.

## Integrationen schreiben, die gut altern

- Verwenden Sie die unterstützten APIs (`eZContentFunctions`, Operationen, `expservices`, die REST-Routen) statt
  Tabellen und interner Methoden.
- Halten Sie den Code in einer Erweiterung mit Tests, nicht in verstreuten Skripten.
- Sichern Sie optionale Kernel-Klassen mit `class_exists()` ab (die Audit-API dokumentiert das), damit ein fehlendes
  Teil die Funktion einschränkt, statt sie scheitern zu lassen.
- Ziehen Sie für alles, was eine Website ändern können muss, Einstellungen dem Code vor.
- Folgen Sie dem Projekt: Geben Sie Korrekturen, die Sie brauchen, an das Projekt zurück, damit das nächste Release sie
  enthält ([Integrationen zurückgeben](integrationen-zurueckgeben.md)).

## Verwandte Seiten

- [Upgrade](../../anleitungen/aktualisieren.md)
- [Teil 4 des Erweiterungsbuchs: Upgrade](../erweiterungsentwicklung/teil-4-aktualisieren/README.md)
- [Kunden-Websites aktuell halten](../partner/kunden-websites-aktuell-halten.md)
- [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
