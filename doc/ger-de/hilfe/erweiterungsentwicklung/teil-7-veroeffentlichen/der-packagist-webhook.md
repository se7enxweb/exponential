---
title: Der Packagist-Webhook
audience: developer, app-creator
category: extension-development
order: 60
summary: Wenn Packagist eine alte neueste Version zeigt, obwohl der Tag auf GitHub ist, fehlt dem Repository sein Packagist-Hook. Wie Sie das erkennen, wie Sie ihn einrichten und wie Sie das Paket einmalig auf den aktuellen Stand bringen.
keywords: Packagist, Webhook, Hook, GitHub-Integration, veraltete Version, automatische Aktualisierung, Paket aktualisieren, webhook, hook, GitHub integration, stale version, auto-update, gh api hooks, update package
type: help
---
# Der Packagist-Webhook

Teil 7, Kapitel 6. Sie haben den Tag `v1.2.4` gepusht, das GitHub-Release ist da, aber Packagist bietet immer noch
`1.2.3` an, und `composer require` installiert die alte Version. Die übliche Ursache: GitHub teilt Packagist nie mit,
dass sich das Repository geändert hat, weil das Repository keinen Packagist-Hook hat. Dieses Kapitel zeigt, wie Sie
das prüfen und beheben.

## Wie Packagist von neuen Tags erfährt

Packagist beobachtet Repositorys nicht nach eigenem Zeitplan bei jedem Push. GitHub benachrichtigt es über einen
Webhook (oder die GitHub-Integration von Packagist) bei jedem Push. Ohne diese Benachrichtigung bleibt die Paketseite
bei dem Stand, den Packagist zuletzt gelesen hat.

Genau das hat dieses Projekt bei einer seiner Erweiterungen festgestellt: Ihre Packagist-Seite blieb monatelang bei
einer alten Version stehen, weil das Repository keinen Hook hatte. Die Tags waren alle da; nur die Benachrichtigung
fehlte.

## Prüfen

Auf GitHub: **Settings > Webhooks** des Repositorys listet die Hooks auf. Einer sollte auf `packagist.org` zeigen.

Mit dem Kommandozeilenwerkzeug von GitHub (Sie brauchen Administratorrechte für das Repository):

```bash
gh api repos/your-account/myext/hooks --jq '.[].config.url'
```

Keine Zeile mit `packagist.org` bedeutet: kein Hook.

Auf Packagist zeigt die Paketseite, wann sie zuletzt aktualisiert wurde, und warnt Maintainer, wenn das Paket nicht
automatisch aktualisiert wird.

## Beheben

Die eigene Anleitung von Packagist steht auf seiner Seite
[About: how to update packages](https://packagist.org/about#how-to-update-packages) (auf Englisch). Kurz gesagt, eines
von beiden:

1. **Die GitHub-Integration**: Melden Sie sich bei Packagist mit GitHub an und gewähren Sie ihm Zugriff auf die
   Organisation; Packagist richtet dann Hooks für die Pakete ein, die es sehen kann.
2. **Ein Webhook von Hand**: In **Settings > Webhooks > Add webhook** des Repositorys die Payload-URL und das Secret,
   die Packagist auf Ihrer Profilseite anzeigt, Inhaltstyp `application/json`, das Ereignis „push“.

Das Secret ist Ihr Packagist-API-Token: Halten Sie es aus Issues, Commit-Nachrichten und Skripten im Repository heraus.

## Einmalig auf den aktuellen Stand bringen

Nachdem der Hook eingerichtet ist, aktualisiert der nächste Push Packagist. Um den Rückstand jetzt aufzuholen,
verwenden Sie die Schaltfläche **Update** auf der Paketseite (als Maintainer), oder pushen Sie irgendetwas.

Prüfen Sie dann: Die Paketseite listet die neue Version, und

```bash
composer show your-account/myext --all | grep versions
```

zeigt sie ebenfalls.

## Verwandte Seiten

- Zurück: [Composer und Packagist](composer-und-packagist.md)
- Weiter: [Changelogs](changelogs.md)
- [Creating webhooks](https://docs.github.com/en/webhooks/using-webhooks/creating-webhooks) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
