---
title: Faktenblatt für Journalisten
audience: press, advocate, sales-marketing
category: press-media
order: 4
summary: Ein einseitiges Faktenblatt zu Exponential für Redaktionen - was es ist, wer es leitet und betreibt, die Geschichte in Daten, Releases, Lizenz, Plattformen, die wichtigsten Funktionen, Sicherheitsprozess, Sprachen und Kontakte - mit einer Quelle für jede Zeile und dem Datum, an dem die Fakten geprüft wurden.
keywords: Faktenblatt, Fakten, Zahlen, Hintergrundpapier, Redaktion, One-Pager, wichtigste Fakten, Exponential, 7x, Exponential Foundation, Release, Lizenz, GPL, PHP, Datenbanken, Kontakte, fact sheet, facts, figures, backgrounder, newsroom, one pager, key facts, release, license, databases, contacts
featured: true
type: help
---
# Faktenblatt für Journalisten

Diese Seite ist für Reporter und Redakteure, die die Fakten an einem Ort brauchen. Jede Zeile hat unten eine Quelle. Die
Fakten wurden am **19. Oktober 2026** geprüft. Prüfen Sie vor der Veröffentlichung die Zeilen, die sich mit Releases
ändern (mit * markiert), auf den unter [Quellen](#quellen) verlinkten Seiten.

## Inhalt

1. [Das Faktenblatt](#das-faktenblatt)
2. [Quellen](#quellen)
3. [Zeilen, die sich ändern](#zeilen-die-sich-ändern)
4. [Das Faktenblatt verwenden](#das-faktenblatt-verwenden)

## Das Faktenblatt

```text
EXPONENTIAL - FAKTENBLATT FÜR DIE PRESSE                                Fakten geprüft am 19. Oktober 2026

PRODUKT        Exponential: ein freies Open-Source-Content-Management-System und Anwendungs-Framework in PHP
               "with advanced CMS functionality"; seine bemerkenswerteste Eigenschaft ist ein vollständig
               anpassbares Inhaltsmodell.
WAS ES TUT     Strukturierte Inhalte, Versionen und Freigabe, Multisite, viele Sprachen (inkl. rechts nach links),
               eingebauter Shop, Workflows, REST API, Seitenbaukasten (Exponential Layouts).
LEITUNG        7x, ein nordamerikanisches Unternehmen für Webdesign, Entwicklung, Support und Hosting (ehemals
               Brookins Consulting); hat 2023 die Leitung des Projekts übernommen.
BETRIEB        Die Exponential Foundation, eine gemeinnützige Organisation nach 501(c)(3), gegründet am
               19. März 2026 in Seattle, Washington, USA.
URHEBERRECHT   "7x & Exponential Foundation" (1998 - 2026); "eZ Systems AS" (1999 - 2014) bleibt im Code.
GESCHICHTE     Codebasis seit 1999 entwickelt, zuerst als eZ Publish, ein Produkt, das sein Hersteller nach fast
               18 Jahren einstellte. 7x: Idee 2014 (se7enx.com registriert), Projektbeginn 2022, Start Dezember
               2023. Am 11./12. August 2025 in Exponential umbenannt.
RELEASES *     6.0.0 am 1. Januar 2024 ... 6.0.14 am 7. Juni 2026 (15 Releases). Aktuelle Linie: 6.0.15,
               in Entwicklung (lib/version.php), kein Release-Tag am 19. Oktober 2026.
LIZENZ         Software: GNU GPL v2 oder später, kostenlos. Dokumentation: GNU FDL 1.3 oder später.
               Exponential Velocity: MIT.
LÄUFT AUF *    PHP 8.0 - 8.5. SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL.
               Linux, BSD, macOS, Windows (dokumentierte Stufen). Apache, Nginx und andere, oder Velocity.
NEU IN 6.0.15* Exponential Velocity (PHP-Webserver), Exponential Layouts (Seitenbaukasten), Exponential
               Intelligence (schreibt nur Entwürfe), Design admin4, Prüfprotokoll, Hilfesystem.
SICHERHEIT     Vertrauliche Meldungen an security@se7enx.com; "a first answer within one business day";
               Korrekturen erscheinen im nächsten 6.0.x-Release; veröffentlichte Versionen werden nie geändert.
SPRACHEN *     368 Locale-Dateien (43 von rechts nach links); Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md))
               (26. Okt.).
NICHT          Nicht mit eZ Systems oder Ibexa verbunden.
CODE           github.com/se7enxweb/exponential        Pakete: Composer / Packagist, Vendor se7enxweb
WEB            exponential.earth   foundation.exponential.earth   se7enx.com   doc.exponential.earth
PRESSE         info@se7enx.com   Pressekit: exponential.earth/about/press-kit
```

## Quellen

| Zeile | Quelle |
|---|---|
| Produkt, was es tut | [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md), „What is Exponential?“ und „Main Exponential features“; [Exponential 6, der Kernel](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel) |
| Leitung | <https://se7enx.com/about>; README, „Who is 7x“; [Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt) |
| Betrieb | <https://foundation.exponential.earth/>; [Was die Foundation ist](../wer-ist-die-exponential-foundation.md#was-die-foundation-ist) |
| Urheberrecht | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| Geschichte | [Wer ist 7x?, Zeitleiste](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste); [7x in drei Etappen](7x-in-drei-etappen.md) |
| Releases | [Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x); `git tag -l 'v6.0.*' --sort=version:refname` |
| Lizenz | README, „License“; [Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen) |
| Läuft auf | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen); [Datenbankunterstützung](../datenbankunterstuetzung.md); [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Neu in 6.0.15 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Sicherheit | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md); [Sicherheitskorrekturen](../wer-ist-die-exponential-foundation.md#sicherheitskorrekturen) |
| Sprachen | [Sprachen in Exponential](../../sprachen.md) |
| Nicht verbunden | README, „Exponential Project Notice : 2025.08.12“ |
| Pressekontakt | Fußzeile von <https://exponential.earth/>; [Pressekontakte](pressekontakte.md) |

## Zeilen, die sich ändern

| Zeile | Prüfen Sie sie mit |
|---|---|
| Releases | [Die 6.0.x-Releases auf einen Blick](die-6-0-x-releases-auf-einen-blick.md) oder den Tags auf GitHub |
| Läuft auf | Dem README-Abschnitt „Requirements“ |
| Neu in 6.0.15 | Dem Changelog; sobald 6.0.15 getaggt ist, ist es nicht mehr „in Entwicklung“ |
| Sprachen | [Sprachen in Exponential](../../sprachen.md) |

## Das Faktenblatt verwenden

- Kopieren Sie jede beliebige Zeile; nennen Sie „Exponential-Dokumentation“ oder verlinken Sie diese Seite als Quelle.
- Behalten Sie das Datum: „Stand 19. Oktober 2026“. Wenn Sie eine Zeile selbst aktualisieren, ändern Sie das Datum und
  Ihre Quelle.
- Ergänzen Sie keine Zahlen, die hier nicht stehen. Zahlen zu Installationen, Nutzern oder Kunden sind nicht
  veröffentlicht ([Was die Quellen nicht sagen](was-die-quellen-nicht-sagen.md)).
- Die Fürsprecher führen eine kürzere Fassung für Handzettel: [Faktenblatt auf einer Seite](../fuersprecher/faktenblatt.md).

## Verwandte Seiten

- [Unsere Ziele](../../ziele.md): das Warum hinter den Fakten, warum es das Projekt gibt und wie 7x seinen Lebensunterhalt verdient
- [Exponential in Kürze](exponential-in-kuerze.md)
- [Exponential in Zahlen](exponential-in-zahlen.md)
- [Leitfaden: Faktenprüfung](leitfaden-faktenpruefung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
