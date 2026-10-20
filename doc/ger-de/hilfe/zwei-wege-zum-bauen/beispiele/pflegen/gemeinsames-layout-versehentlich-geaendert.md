---
title: "Wenn ein Redakteur das gemeinsame Layout versehentlich geändert hat"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1332
summary: Eine Änderung an Header / Footer oder Prefooter zeigt sich auf vielen Seiten; setzen Sie sie aus dem letzten Export zurück.
keywords: gemeinsames Layout, Fehler, Zurücksetzen, Header / Footer, Prefooter, shared layout, mistake, rollback
type: help
---
# Wenn ein Redakteur das gemeinsame Layout versehentlich geändert hat

Die Fußzeile jeder Seite ist in wenigen Minuten wieder in Ordnung.

*Lebenszyklus: Pflegen, Beispiel 33 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Wenn es nur ein Entwurf ist: Öffnen Sie ihn und klicken Sie auf **Verwerfen**.

## Auf der Kommandozeile

1. `./console exp:layouts import <last export> --only=fd0aa7af77a85a03bf1080018f5db1dc --strategy=overwrite --publish --dry-run`, prüfen, ohne `--dry-run` ausführen, dann `./console exp:cache clear --tag=content`.

## Welchen Weg Sie wählen

Bewahren Sie besonders Exporte der gemeinsamen Layouts auf.

## Prüfen

- Alle Seiten zeigen die wiederhergestellte Fußzeile.

## Verwandte Seiten

- [Ein Layout zurücksetzen](../../layout-zuruecksetzen.md)
- [Gemeinsame Layouts und Zonenverknüpfung](../../../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
