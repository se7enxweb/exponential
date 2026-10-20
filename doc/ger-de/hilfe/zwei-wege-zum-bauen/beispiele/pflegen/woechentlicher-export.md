---
title: "Layouts jede Woche exportieren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1322
summary: Ein geplanter exp:layouts export --all bewahrt eine Historie der Layouts.
keywords: geplanter Export, Cron, Sicherung, Historie, Layouts, scheduled export, cron, backup, history, layouts
type: help
---
# Layouts jede Woche exportieren

Jedes Layout lässt sich auf den Stand der letzten Woche zurücksetzen.

*Lebenszyklus: Pflegen, Beispiel 23 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Oder montags von Hand **Layouts > Exportieren**.

## Auf der Kommandozeile

1. Eine Crontab-Zeile auf dem Server (als der Benutzer, unter dem die Site läuft):

```bash
0 3 * * 1  cd /path/to/installation && ./console exp:layouts export --all --output=var/backup/layouts-$(date +\%Y\%m\%d).zip
```

## Welchen Weg Sie wählen

Bewahren Sie die Dateien außerhalb des Web-Roots auf und löschen Sie alte.

## Prüfen

- Jede Woche erscheint eine neue Datei.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../../../../eng-us/help/build-two-ways/export-and-import-layouts.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
