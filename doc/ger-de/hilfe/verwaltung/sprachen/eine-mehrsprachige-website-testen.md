---
title: Eine mehrsprachige Website testen
audience: government, admin, website-creator
category: public-sector
order: 300
summary: Wie Sie eine mehrsprachige Exponential-Website vor dem Start und nach Änderungen testen - die Testsprache Lorem Ipsum (qlo-ZZ) mit exp:loremipsum, Prüfungen pro Sprachadresse, Ersatzregeln, Sprachumschalter, Suche, Formulare und E-Mails, Layouts von rechts nach links und das Leeren der Caches.
keywords: Testen, mehrsprachige Tests, Lorem Ipsum, qlo-ZZ, exp:loremipsum, Testsprache, Qualitätssicherung Sprachen, Ersatztest, Test des Sprachumschalters, Test von rechts nach links, testing, multilingual testing, test language, language QA, fallback test, switcher test, right to left test
type: help
---
# Eine mehrsprachige Website testen

Diese Seite richtet sich an Administratoren und Websiteersteller, die eine mehrsprachige Website prüfen. Exponential
liefert eine eigens zum Testen gemachte Sprache mit, **Lorem Ipsum** (`qlo-ZZ`), die keine Installation von selbst
hinzufügt ([Die Testsprache Lorem Ipsum](../../../anleitungen/testsprache-lorem-ipsum.md)).

![Der Reiter Übersetzungen eines Eintrags, mit den vorhandenen und den fehlenden Übersetzungen](../../bilder/government/gov-translations-tab.webp)

## Inhalt

1. [Mit Lorem Ipsum testen](#mit-lorem-ipsum-testen)
2. [Prüfungen pro Sprache](#prüfungen-pro-sprache)
3. [Prüfungen über Sprachen hinweg](#prüfungen-über-sprachen-hinweg)
4. [Caches](#caches)
5. [Die Testsprache entfernen](#die-testsprache-entfernen)

## Mit Lorem Ipsum testen

Verwenden Sie eine Testinstallation; üben Sie das Entfernen nie an einer Sprache, die Ihre Inhalte verwenden.

1. **Setup > Sprachen > Sprache hinzufügen**, wählen Sie *Lorem Ipsum*.
2. Geben Sie einem Test-Siteaccess `SiteLanguageList[]=qlo-ZZ` und Ihre Hauptsprache an zweiter Stelle.
3. Füllen Sie Übersetzungen mit Lorem-Ipsum-Text:

   ```bash
   ./console exp:loremipsum <object id> --dry-run
   ```

   Der Befehl füllt nur Testsprachen. Entfernen Sie `--dry-run`, wenn die Ausgabe richtig aussieht.
4. Gehen Sie den Test-Siteaccess durch: Jeder übersetzte Text ist sichtbar Lorem Ipsum, sodass unübersetzte feste
   Texte und Ersatzinhalte auffallen.

## Prüfungen pro Sprache

- [ ] Die Adresse antwortet, und `<html lang>` stimmt.
- [ ] Menüs, Fußzeile und feste Texte sind in der Sprache.
- [ ] Datum und Zahlen folgen der Locale.
- [ ] Formulare, ihre Fehlermeldungen und Bestätigungsseiten sind in der Sprache.
- [ ] E-Mails, die von Formularen und Newslettern versendet werden, sind in der Sprache.
- [ ] Fehlerseiten (nicht gefunden, Zugriff verweigert) sind in der Sprache.
- [ ] Die Suche findet Seiten in der Sprache.
- [ ] Bei Sprachen von rechts nach links: `dir="rtl"` und ein gespiegeltes Layout.

## Prüfungen über Sprachen hinweg

- [ ] Der Sprachumschalter führt in jeder Sprache zur selben Seite.
- [ ] Eine Seite, die es in einer Sprache gibt, verhält sich in den anderen so, wie es der Grundsatz für den Ersatz
  vorsieht.
- [ ] Immer verfügbare Einträge (Bilder, Benutzer) erscheinen überall.
- [ ] Eine in einer Sprache veröffentlichte neue Übersetzung ändert die anderen nicht.
- [ ] Übersetzer können nur ihre Sprache bearbeiten.

## Caches

Leeren Sie nach Änderungen an Einstellungen den INI- und den Inhalts-Cache (`php bin/php/ezcache.php
--clear-tag=ini,content`) sowie den Antwort-Cache von Velocity (`./console exp:velocity cache clear --apply`), wenn Velocity die
Website ausliefert; sonst zeigt ein Test womöglich einen alten Stand
([Inhaltssprachen, Abschnitt 10](../../../anleitungen/inhaltssprachen.md#10-befehle-für-die-shell)).

## Die Testsprache entfernen

Entfernen Sie die Lorem-Ipsum-Übersetzungen (oder die Testobjekte) und dann die Sprache unter **Setup > Sprachen**,
damit sie keinen Platz für eine Inhaltssprache belegt.

## Verwandte Seiten

- [Die Testsprache Lorem Ipsum](../../../anleitungen/testsprache-lorem-ipsum.md)
- [Checkliste Sprachen](checkliste-sprachen.md)
- [Checkliste für den Start](../../ersteller/website-ersteller/checkliste-fuer-den-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
