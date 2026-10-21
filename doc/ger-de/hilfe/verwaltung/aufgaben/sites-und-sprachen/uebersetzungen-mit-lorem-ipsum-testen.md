---
title: Übersetzungen mit Lorem Ipsum testen
audience: government, admin, website-creator
category: public-sector
order: 390
summary: Aufgabe für Administratoren und Site-Ersteller - die Testsprache Lorem Ipsum (qlo-ZZ) hinzufügen, Übersetzungen mit exp:loremipsum füllen, Ersatzsprachen und unübersetzte Texte auf einem Test-Siteaccess prüfen und sie wieder entfernen.
keywords: Lorem Ipsum, Testsprache, Übersetzungen testen, Ersatz testen, qlo-ZZ, test language, exp:loremipsum, exploremipsumfill.php, translation testing, fallback test
type: help
---
# Übersetzungen mit Lorem Ipsum testen

Diese Aufgabe richtet sich an Administratoren und Site-Ersteller, auf einer Testinstallation. Hintergrund:
[Die Testsprache Lorem Ipsum](../../../../anleitungen/testsprache-lorem-ipsum.md) und
[Eine mehrsprachige Website testen](../../sprachen/eine-mehrsprachige-website-testen.md).

## Schritte

1. **Setup > Sprachen**, **Sprache hinzufügen**, wählen Sie *Lorem Ipsum* (`qlo-ZZ`).
2. Geben Sie einem Test-Siteaccess `SiteLanguageList[]=qlo-ZZ`, gefolgt von Ihrer Hauptsprache; leeren Sie den
   INI-Cache.
3. Füllen Sie Übersetzungen, zuerst als Probelauf:

   ```bash
   php bin/php/exploremipsumfill.php --subtree=2 --limit=20 --dry-run
   php bin/php/exploremipsumfill.php --subtree=2 --limit=20
   ```

   (`./console exp:loremipsum` ist derselbe Befehl.) Er füllt nur Testsprachen.
4. Durchsuchen Sie den Test-Siteaccess: Alles Übersetzte ist Lorem Ipsum; alles in der Hauptsprache ist ein Ersatz oder
   ein fester Text des Designs, der noch nicht übersetzbar ist.

## Prüfen

Sie haben eine Liste der festen Texte im Design, die zu übersetzen sind, und der Seiten, die auf einen Ersatz
zurückfallen.

## Aufräumen

Entfernen Sie die Übersetzungen in Lorem Ipsum (oder die Testobjekte), dann die Sprache unter **Setup > Sprachen**,
damit sie keinen der 62 Plätze für Inhaltssprachen belegt.

## Verwandte Seiten

- [Sprachen in Exponential, Abschnitt 12](../../../../../eng-us/languages.md#12-test-languages-lorem-ipsum)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
