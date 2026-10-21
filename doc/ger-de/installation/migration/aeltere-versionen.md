---
title: Ältere Releases dieses Kernels
audience: admin, developer, project-manager
category: install-upgrade
order: 25
summary: Welche Anleitung eine Site von einer älteren Release dieses Kernels – eZ Publish 3.x, 4.x und 5.x, den Community-Releases 2013.x und 2014.x, der reinen Legacy-Linie 5.90 (2015.01, 2017.x und später), LoveStack – zu Exponential 6 bringt, und wie Sie feststellen, welche davon Sie betreiben.
keywords: eZ Publish 3.x, 4.x, 5.x, 5.4, 2014.11, 5.90, 2017.12, LoveStack, ezpublish-legacy, Upgrade-Kette, ältere Versionen, Legacy, upgrade chain, older versions, legacy
type: install
---
# Ältere Releases dieses Kernels

Exponential 6 ist die Fortführung des Legacy-Kernels von eZ Publish, eines Produkts, das sein Hersteller nach fast 18
Jahren eingestellt hat. Sites, die noch eine seiner Releases betreiben, wechseln **an Ort und Stelle** zu Exponential 6:
Die Datenbank wird mit den Update-Dateien dieses Repositorys aktualisiert, der Code wird ersetzt, und die Extensions
werden auf PHP 8 portiert. Nichts muss exportiert und importiert werden. Der alte Name erscheint im Folgenden nur, um
die Release zu bezeichnen, von der Sie migrieren.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md)

## Welche Release betreiben Sie?

Lesen Sie `lib/version.php` der alten Installation und die Versionszeilen von `ezsite_data`
([Kapitel 11.2](../11-aktualisieren.md#112-welche-version-betreiben-sie)); für die 5.x-Ära sehen Sie sich das
Wurzelverzeichnis der Installation an ([Kapitel 15.2](../15-migration-von-5x-legacy.md#152-welche-variante-betreiben-sie)).

| Sie betreiben | Anleitung | Der Weg der Datenbank |
|---|---|---|
| 3.0 bis 3.9 | [Kapitel 14.5.1](../14-migration-von-4x.md#1451-welche-ausgangspunkte-funktionieren) | nicht direkt: Die Update-Skripte dieser Versionen sind nicht in diesem Repository; bringen Sie die Site zuerst mit der alten Release 3.10 auf 3.10 und folgen Sie dann der nächsten Zeile |
| 3.10, 4.0 bis 4.7 auf PHP 5 | [Kapitel 14](../14-migration-von-4x.md) | die alte Kette bis 5.4, dann auf 6.0.0, dann auf den heutigen Stand |
| 5.0 bis 5.4, Community-Releases 2013.x und 2014.x (mit dem Symfony-Stack) | [Kapitel 15](../15-migration-von-5x-legacy.md) | den Legacy-Teil herauslösen; die 5.4-Datenbank geht auf 6.0.0, dann auf den heutigen Stand |
| Die reine Legacy-Linie 5.90 (2015.01, 2017.08, 2017.12 und spätere Releases, die `VERSION_MAJOR = 5`, `VERSION_MINOR = 90` melden) | [Kapitel 11.5](../11-aktualisieren.md#115-von-54-oder-590-auf-600), mit [15.9](../15-migration-von-5x-legacy.md#159-benutzer-passwort-hashes-und-sitzungen) für Passwörter | auf 6.0.0, dann auf den heutigen Stand |
| Releases, die nach einem Jahr später als 2017 benannt sind | in diesem Repository nicht geprüft | lesen Sie `lib/version.php`: Eine 5.90-Release folgt der Zeile darüber |
| LoveStack (die Distribution des Legacy-Kernels von Mugo Web) | [LoveStack: Migration](../../bc/6.0/love/migration.md) | `./console exp:explovestack` prüft sie und registriert ihre Datenbank als die von Exponential |
| Exponential 6.0.0 bis 6.0.14 | [Kapitel 11.6](../11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) | die Update-Dateien von 6.0.x |

Danach finden Sie in [Kapitel 17](../17-migrationsreferenz.md) die Nachschlagetabellen und in [Kapitel 12](../12-fehlerbehebung.md) die
Lösungen für das, was schiefgeht.

## Die Schritte, die alle älteren Releases gemeinsam haben

1. **Kopie, nie an Ort und Stelle.** Stellen Sie eine Sicherung der alten Site auf einem Staging-Server wieder her und migrieren Sie die Kopie.
2. **Vorabprüfung**: Zeichensatz und Tabellentyp (MySQL), die Prüfabfragen aus [14.5.3](../14-migration-von-4x.md#1453-vorabprüfungsabfragen).
3. **Phase A**: jede SQL-Update-Datei der Kette, in der richtigen Reihenfolge ([14.5.5](../14-migration-von-4x.md#1455-phase-a-die-sql-dateien)).
4. **Phase B**: die PHP-Reparaturskripte, gegen das fertige Schema ([14.5.6](../14-migration-von-4x.md#1456-phase-b-die-reparaturskripte)).
5. **Code**: ein frisches Exponential 6 ([Kapitel 3](../03-den-code-beschaffen.md)); Einstellungen, Designs und Extensions
   übernommen; Extensions auf PHP 8 portiert ([14.9](../14-migration-von-4x.md#149-eigene-extensions-von-php-5-auf-php-8-portieren)).
6. **Prüfen** ([14.12](../14-migration-von-4x.md#1412-die-inhalte-nach-der-migration-prüfen)), proben, umstellen;
   behalten Sie die alte Site bis zur Abnahme ([14.14](../14-migration-von-4x.md#1414-rollback-plan)).

Die Adressen bleiben bei einer Migration an Ort und Stelle dieselben (die URL-Alias-Tabellen wandern mit der Datenbank mit),
daher sind Weiterleitungen nur für das nötig, was Sie absichtlich ändern.

## Wie es von dort weitergeht

Sobald die Site auf Exponential 6 läuft, steht jeder andere Weg der [Übersicht](README.md) offen: der Symfony-Stack daneben
([Platform Legacy](exponential-platform-legacy.md)), eine Distribution zum Aufbau von Seiten ([Nexus](exponential-platform-nexus.md))
oder eine reine Symfony-Plattform ([DXP](exponential-platform-dxp.md)). Machen Sie immer nur einen Wechsel auf einmal.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
