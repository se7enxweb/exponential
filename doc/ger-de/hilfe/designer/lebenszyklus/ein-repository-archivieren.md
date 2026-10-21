---
title: "Ein Repository archivieren"
audience: designer
category: theme-lifecycle
order: 270
summary: Das GitHub-Repository eines Themes archivieren, damit es lesbar und installierbar bleibt, aber keine Änderungen mehr annimmt, und das Packagist-Paket mit einem Nachfolger als aufgegeben kennzeichnen - ohne etwas zu löschen.
keywords: Repository archivieren, nur lesbar, gh repo archive, aufgegebenes Paket, Packagist abandon, Nachfolger, installierbar halten, archive repository, read-only, abandoned package, successor, keep installable
type: help
---
# Ein Repository archivieren

Diese Seite ist für Designer am Ende des Lebens eines Themes ([Lebensende](lebensende.md)).

## Archivieren, nie löschen

Ein archiviertes GitHub-Repository ist nur lesbar: Code, Tags und Releases bleiben, sodass Composer weiterhin jede Version
installieren kann. Es zu löschen, zerstört jede Website, die daraus installiert, und jeden Link darauf.

## Schritt für Schritt

1. Veröffentlichen Sie zuerst das letzte Release und den Hinweis auf das Lebensende ([Das Lebensende ankündigen](das-lebensende-ankuendigen.md)).
2. Archivieren Sie das Repository:

   ```bash
   gh repo archive yourvendor/mytheme --yes
   ```

   oder im Browser über **Settings > Archive this repository**.
3. Prüfen Sie: `gh repo view yourvendor/mytheme --json isArchived` gibt `"isArchived": true` aus.
4. Öffnen Sie auf Packagist die Seite des Pakets und kennzeichnen Sie es als **Abandoned**, mit dem Nachfolgepaket, falls
   es eines gibt. Composer warnt dann Nutzer, die es installieren, und nennt den Nachfolger.
5. Testen Sie, dass es sich noch installieren lässt: `composer require yourvendor/mytheme` in einer Wegwerf-Installation.

## Rückgängig machen

Ein Archiv lässt sich rückgängig machen (**Unarchive**), wenn jemand das Theme übernimmt ([Ein Theme übergeben](uebergabe-an-einen-neuen-betreuer.md)).

## Verwandte Seiten

- [Das Repository und das Packagist-Paket übertragen](repository-und-packagist-paket-uebertragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
