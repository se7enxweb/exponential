---
title: "Packagist automatisch aktuell halten"
audience: designer, developer
category: theme-distribution
order: 200
summary: Packagist jeden neuen Tag lesen lassen, sobald er gepusht ist - über die GitHub-Integration oder einen Webhook - und nach einem Release prüfen, dass der Hook funktioniert.
keywords: Packagist-Webhook, automatische Aktualisierung, GitHub-Hook, packagist api github, Paket aktualisieren, neue Version nicht angezeigt, Hooks, Packagist webhook, auto update, GitHub hook, update package, new version not shown, hooks
type: help
---
# Packagist automatisch aktuell halten

Diese Seite ist für Designer, die ein Theme auf Packagist veröffentlicht haben. Ohne Update-Hook erfährt Packagist nicht
zeitnah von neuen Tags, und Nutzer sehen eine alte Version als die neueste.

## Zwei Wege

| Weg | Wie |
|---|---|
| Die GitHub-Integration | Melden Sie sich bei Packagist mit Ihrem GitHub-Konto an und lassen Sie Packagist die Hooks Ihrer Repositories verwalten; es setzt den Hook für Pakete, auf die es zugreifen kann |
| Ein manueller Webhook | Fügen Sie unter **Settings > Webhooks** des Repositorys die GitHub-Hook-URL von Packagist hinzu (`https://packagist.org/api/github?username=<your Packagist user>`), mit Ihrem Packagist-API-Token als Secret, Inhaltstyp `application/json`, Ereignis „push“ |

Die Packagist-eigene Seite **Profile > Settings > API Token** zeigt Ihr Token und erklärt den Hook für Ihr Konto; folgen
Sie ihrem aktuellen Text, er ist maßgeblich.

## Schritt für Schritt: den Hook prüfen

1. Listen Sie die Hooks des Repositorys auf (braucht Administratorrechte):

   ```bash
   gh api repos/yourvendor/mytheme/hooks --jq '.[].config.url'
   ```

   Erwartet: eine URL auf `packagist.org`. Keine Ausgabe bedeutet, dass es keinen Hook gibt.
2. Prüfen Sie nach dem nächsten Release innerhalb weniger Minuten Packagist:

   ```bash
   curl -s https://repo.packagist.org/p2/yourvendor/mytheme.json | grep -o '"version":"[^"]*"' | head -3
   ```

   Erwartet: die neue Version zuerst.
3. In GitHub zeigt **Settings > Webhooks > der Packagist-Hook > Recent Deliveries** jeden Aufruf und seine Antwort; eine
   rote Markierung sagt Ihnen, dass der Hook fehlgeschlagen ist, und warum.

## Wenn sich der Hook noch nicht einrichten lässt

Auf der Packagist-Seite des Pakets kann ein Betreuer **Update** drücken, damit Packagist das Repository sofort liest. Tun
Sie das nach jedem Release, bis der Hook funktioniert.

## Verwandte Seiten

- [Packagist zeigt eine alte Version](veraltete-packagist-version.md)
- [Ein Theme bei Packagist einreichen](bei-packagist-einreichen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
