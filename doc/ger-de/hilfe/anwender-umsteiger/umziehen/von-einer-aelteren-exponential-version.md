---
title: Von einer älteren Exponential-Version
audience: adopter, admin, developer, hosting, customer
category: adoption
order: 41
summary: Wie ein Anwender mit einer älteren Exponential-Website auf die aktuelle Linie 6.0 wechselt - von einem älteren Release 6.0.x, von 5.4 oder den Linien 4.x und 3.x aus den Jahren von eZ Publish und von 5.x auf dem Symfony-Stack -, warum jetzt, was den Aufwand bestimmt und die Kapitel des Installationsbuchs.
keywords: Upgrade, ältere Version, 6.0.x, 5.x, 4.x, 3.x, Jahre von eZ Publish, Migration, Installationsbuch, Sicherheitskorrekturen, PHP 8, upgrade, older release, eZ Publish years, migration, installation book, security fixes
type: help
---
# Von einer älteren Exponential-Version

Websites, die gebaut wurden, als die Software noch eZ Publish hieß, laufen auf derselben Codebasis, die Exponential
fortführt. Sie auf 6.0 zu bringen erhält den größten Teil von Inhaltsmodell und Datenbank
([Zu Exponential wechseln](../../unternehmen/auswahl/zu-exponential-wechseln.md)).

## Inhalt

1. [Warum jetzt wechseln](#warum-jetzt-wechseln)
2. [Ihr Ausgangspunkt](#ihr-ausgangspunkt)
3. [Was den Aufwand bestimmt](#was-den-aufwand-bestimmt)
4. [Bevor Sie beginnen](#bevor-sie-beginnen)

## Warum jetzt wechseln

- "5.x, 4.x, 3.x and older" erhalten keine Sicherheitskorrekturen; 6.0 ist die gepflegte Linie (`SECURITY.md`).
- Exponential unterstützt PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) (`README.md`), die Website kann also auch auf ein unterstütztes PHP wechseln.
- Nach dem Wechsel keine Lizenzgebühr (`README.md`).

## Ihr Ausgangspunkt

| Sie betreiben | Lesen Sie |
|---|---|
| Ein älteres Release 6.0.x | [Aktualisieren, 11.6 Von einer beliebigen 6.0.x auf den heutigen Stand](../../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |
| 5.4 (oder 5.90) | [Aktualisieren, 11.5 Von 5.4 auf 6.0.0](../../../installation/11-aktualisieren.md#115-von-54-oder-590-auf-600) |
| 3.10 bis 5.3 | [Aktualisieren, 11.4 Von 3.10 bis 5.3](../../../installation/11-aktualisieren.md#114-von-310-bis-53-die-alte-kette) |
| 4.x | [Migration von 4.x](../../../installation/14-migration-von-4x.md) |
| 5.x mit dem Legacy-Kernel | [Migration von 5.x Legacy](../../../installation/15-migration-von-5x-legacy.md) |

Die Sicht der Hoster: [Kunden von älteren Versionen migrieren](../../hosting/migrationen/kunden-von-aelteren-versionen-migrieren.md).

## Was den Aufwand bestimmt

Aus [Zu Exponential wechseln](../../unternehmen/auswahl/zu-exponential-wechseln.md#was-den-aufwand-bestimmt): die Größe
der Inhaltstabellen, die Zahl der gespeicherten Dateien, Ihre eigenen Erweiterungen und Ihr eigener PHP-Code ("porting
them to PHP 8 grows with lines of your own code, not with database size", die Portierung auf PHP 8 wächst also mit den
Zeilen Ihres eigenen Codes, nicht mit der Größe der Datenbank), Templates und Design, Neuindizierung der Suche,
URL-Aliase pro Sprache und Integrationen.

## Bevor Sie beginnen

- Finden Sie heraus, welche Version Sie betreiben ([Aktualisieren, 11.2](../../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie)).
- Listen Sie Ihre eigenen Erweiterungen auf und prüfen Sie sie auf PHP 8 ([Teil 4: Upgrades](../../erweiterungsentwicklung/teil-4-aktualisieren/README.md)).
- Legen Sie eine Sicherung an und testen Sie sie ([Test der Wiederherstellung](../../kunden/gesund-halten/wiederherstellungstest.md)).
- Planen Sie eine Probe auf einer Kopie ([Checkliste zur Inhaltsmigration](checkliste-inhaltsmigration.md)).

## Verwandte Seiten

- [Umziehen](README.md)
- [Aktualisieren](../../../installation/11-aktualisieren.md)
- [Checkliste für den Tag des Upgrades](../../kunden/gesund-halten/checkliste-upgrade-tag.md)
- [Eine Website übernehmen](../../kunden/partner-und-hoster/eine-website-uebernehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
