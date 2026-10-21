---
title: "Prüfen, was schon veröffentlicht ist"
audience: designer, developer
category: theme-distribution
order: 100
summary: Bevor Sie eine Versionsnummer wählen, listen Sie die Tags auf dem Remote, die GitHub-Releases und die Versionen auf, die Packagist kennt, denn jede Quelle kann etwas wissen, was die anderen nicht wissen.
keywords: veröffentlichte Versionen, Tags, Releases, Packagist-Versionen, git fetch --tags, gh release list, ls-remote, p2 json, published versions, Packagist versions
type: help
---
# Prüfen, was schon veröffentlicht ist

Diese Seite ist für alle, die gleich ein Theme veröffentlichen. Tun Sie das zu Beginn jedes Releases, auch wenn Sie
gestern erst veröffentlicht haben: Jemand anderes oder ein Skript kann seitdem veröffentlicht haben.

## Schritt für Schritt

1. **Tags auf dem Remote**:

   ```bash
   git fetch --tags
   git tag -l 'v*' --sort=version:refname | tail -5
   git ls-remote --tags origin | tail -5
   ```

   `ls-remote` zeigt, was der Server hat, auch wenn Ihr Fetch aus irgendeinem Grund abgelehnt wurde.
2. **GitHub-Releases**:

   ```bash
   gh release list --limit 10
   ```

3. **Was Packagist kennt** (bei einem Composer-Paket):

   ```bash
   curl -s https://repo.packagist.org/p2/yourvendor/mytheme.json | grep -o '"version":"[^"]*"' | head -5
   ```

   Die ersten Einträge sind die neuesten Versionen.
4. **Nehmen Sie die höchste Nummer aus allen drei.** Das ist die Nummer, die Sie erhöhen
   ([Eine Versionsnummer wählen](eine-versionsnummer-waehlen.md)).

## Wenn die Listen nicht übereinstimmen

| Situation | Bedeutung | Maßnahme |
|---|---|---|
| Ein Tag ohne Release | Ein Release wurde nicht fertiggestellt | Legen Sie das Release für diesen Tag an; verwenden Sie seine Nummer nicht erneut |
| Ein Release, dessen Tag Ihrem Klon fehlt | Ihr Klon ist im Rückstand | `git fetch --tags` |
| Packagist hinter den Tags | Der Update-Hook fehlt oder ist fehlgeschlagen | [Packagist zeigt eine alte Version](veraltete-packagist-version.md) |
| Packagist vor Ihren Tags | Ein Tag wurde gelöscht, nachdem Packagist ihn gelesen hatte | Verwenden Sie diese Nummer nie erneut; veröffentlichen Sie die nächste |

## Verwandte Seiten

- [Tags richtig sortieren](tags-richtig-sortieren.md)
- [Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
