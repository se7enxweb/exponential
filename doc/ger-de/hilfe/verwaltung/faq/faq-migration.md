---
title: "FAQ: Migration von älteren Systemen"
audience: government, project-manager, admin, partner
category: public-sector
order: 100
summary: Antworten auf die Migrationsfragen öffentlicher Stellen - der Umzug von älteren Websites aus der Zeit von eZ Publish, von eZ Platform oder Ibexa oder von anderen Systemen wie WordPress, Drupal oder statischem HTML zu Exponential, was erhalten bleibt, was sich ändert und wo die Anleitungen stehen.
keywords: FAQ Migration, migrieren, Geschichte von eZ Publish, 4.x, 5.x, eZ Platform, Ibexa, WordPress, Drupal, Joomla, statisches HTML, Zuordnung der Inhalte, Weiterleitungen, öffentlicher Sektor, migration FAQ, migrate, eZ Publish history, static HTML, content mapping, redirects, public sector
type: help
---
# FAQ: Migration von älteren Systemen

Diese Seite beantwortet die Migrationsfragen, die öffentliche Stellen stellen, wenn sie eine alte Website zu
Exponential bringen. Die Antworten stammen aus den Migrationskapiteln des Installationshandbuchs (Kapitel 14 bis 17) und
dem [Überblick zur Migration](../../../installation/migration/README.md).

## Inhalt

1. [Was ist die Geschichte der Codebasis?](#was-ist-die-geschichte-der-codebasis)
2. [Wir betreiben eine alte Website mit 4.x oder 5.x. Können wir zu Exponential wechseln?](#wir-betreiben-eine-alte-website-mit-4x-oder-5x-können-wir-zu-exponential-wechseln)
3. [Was bleibt gleich, und was ändert sich?](#was-bleibt-gleich-und-was-ändert-sich)
4. [Wir betreiben eZ Platform oder Ibexa. Können wir wechseln?](#wir-betreiben-ez-platform-oder-ibexa-können-wir-wechseln)
5. [Wir betreiben WordPress, Drupal oder ein anderes System. Können wir wechseln?](#wir-betreiben-wordpress-drupal-oder-ein-anderes-system-können-wir-wechseln)
6. [Wie halten wir alte Adressen funktionsfähig?](#wie-halten-wir-alte-adressen-funktionsfähig)
7. [Gibt es eine Schulung für ein Migrationsteam?](#gibt-es-eine-schulung-für-ein-migrationsteam)

## Was ist die Geschichte der Codebasis?

Exponential führt die Codebasis von eZ Publish fort, die ihr Hersteller nach fast achtzehn Jahren eingestellt hat
([Wer ist 7x?](../../wer-ist-7x.md)). 7x hat diesen Code als Exponential 6 auf PHP 8 weitergeführt
([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)). Websites, die in dieser Zeit gebaut
wurden, können deshalb zu Exponential wechseln, ohne das System zu wechseln.

## Wir betreiben eine alte Website mit 4.x oder 5.x. Können wir zu Exponential wechseln?

Ja, und diese Versionslinien werden nicht mehr unterstützt, also sollten Sie es tun (`SECURITY.md`). Die Kapitel
[14. Migration von der Versionslinie 4.x](../../../installation/14-migration-von-4x.md) und
[15. Migration vom Legacy-Stack 5.x](../../../installation/15-migration-von-5x-legacy.md) bringen solche Websites auf 6.0; die
Datenbank wird mit den Update-Dateien auf den neuen Stand gebracht, statt exportiert und neu importiert zu werden
([11. Aktualisieren](../../../installation/11-aktualisieren.md)).

## Was bleibt gleich, und was ändert sich?

Die Familie des Datenbankschemas, die Datentypen für Inhalte, Rich Text (`ezxmltext`), die Template-Sprache und die API
für Erweiterungen bleiben gleich. Was sich ändert, ist vor allem der Wechsel von PHP 5 zu PHP 8 (Ihr eigener PHP-Code ist
der größte Arbeitsposten), Komponenten, die Composer installiert, und entfernte alte JavaScript-Bibliotheken
([14.1 Was sich ändert und was nicht](../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht)).

## Wir betreiben eZ Platform oder Ibexa. Können wir wechseln?

Ja; Kapitel [16. Migration von eZ Platform und Ibexa](../../../installation/16-migration-von-ez-platform-und-ibexa.md)
behandelt das, und [17. Referenz zur Migration](../../../installation/17-migrationsreferenz.md) enthält die
Zuordnungstabellen. Die Distributionen von Exponential Platform sind ein weiterer Weg
([Exponential Platform](../../../installation/migration/exponential-platform.md)).

## Wir betreiben WordPress, Drupal oder ein anderes System. Können wir wechseln?

Ja, mit einer Migration der Inhalte: Es gibt Anleitungen für [WordPress](../../../installation/migration/wordpress.md),
[Drupal](../../../installation/migration/drupal.md), [Joomla](../../../installation/migration/joomla.md),
[statisches HTML](../../../installation/migration/statisches-html.md) und weitere im
[Überblick zur Migration](../../../installation/migration/README.md). Ordnen Sie zuerst die Inhaltstypen den Inhaltsklassen
zu ([Zuordnung des Inhaltsmodells](../../../installation/migration/inhaltsmodell-zuordnen.md)).

## Wie halten wir alte Adressen funktionsfähig?

Mit Weiterleitungen: URL-Platzhalter und eigene URL-Aliasse in Exponential oder Regeln auf dem Webserver
([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md),
[URL-Platzhalter](../../ersteller/website-ersteller/url-platzhalter.md)). Erstellen Sie die Liste aus der alten Website, bevor
Sie umschalten.

## Gibt es eine Schulung für ein Migrationsteam?

Ja: [Migration zu Exponential](../../trainer/migration/README.md), 25 Lektionen, von der Planung bis zum Verlassen, mit
einem Migrationsplan als Abschlussarbeit. Verwenden Sie für das Projekt die
[Checkliste zur Migration](../../../installation/migration/checkliste.md).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Überblick zur Migration](../../../installation/migration/README.md)
- [Migrationen für Hoster](../../hosting/migrationen/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
