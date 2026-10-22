---
title: Wie zeige ich ein Element in Sprachen, in die es nicht übersetzt ist?
audience: editor, admin
category: manual
order: 475
summary: Haken Sie im Tab Übersetzungen des Elements Die Hauptsprache verwenden, wenn keine vorrangige Übersetzung vorhanden ist an und klicken Sie auf Aktualisieren; eine Website in einer anderen Sprache zeigt das Element dann in seiner Hauptsprache, statt es wegzulassen.
keywords: immer verfügbar, nicht übersetzt, Hauptsprache, Ersatzsprache, Sprachen der Website, Tab Übersetzungen, fehlende Übersetzung, SiteLanguageList, ShowUntranslatedObjects, always available, untranslated, main language, fallback, site languages, translations tab, missing translation
type: help
---
# Wie zeige ich ein Element in Sprachen, in die es nicht übersetzt ist?

Jede Website einer Installation hat eine geordnete Liste von Sprachen. Sie zeigt ein Element in der ersten Sprache
dieser Liste, die das Element hat. Hat das Element keine davon, wird es weggelassen: Es fehlt in Menüs und Listen, und
seine Adresse liefert keine Seite.

Für Elemente, die überall erscheinen müssen, etwa Bilder, Dateien, Kontaktseiten oder eine gemeinsame Fußzeile,
schalten Sie die Einstellung **immer verfügbar** des Elements ein. Die Website zeigt das Element dann in seiner
Hauptsprache, wenn keine aufgeführte Übersetzung existiert.

## Wer das tun kann

Redakteure, die das Element bearbeiten dürfen.

## Schritte

1. Öffnen Sie das Element und klicken Sie auf den Tab **Übersetzungen**.
2. Haken Sie **Die Hauptsprache verwenden, wenn keine vorrangige Übersetzung vorhanden ist** an.
3. Klicken Sie auf **Aktualisieren**.

Neue Elemente übernehmen diese Einstellung von ihrer Klasse. Administratoren legen die Voreinstellung in der Klasse
fest (**Setup** > **Klassen**, die **Standard Objekt Verfügbarkeit** der Klasse).

## Was schiefgehen kann

- **Das Element erscheint in der falschen Sprache.** Es hat eine Übersetzung in einer Sprache, die in der Liste der
  Website weiter oben steht. Prüfen Sie den Tab **Übersetzungen**, oder fragen Sie einen Administrator nach der
  Reihenfolge der Sprachen der Website.
- **Das Kontrollkästchen ist ausgegraut.** Sie dürfen das Element nicht bearbeiten.
- **Elemente erscheinen in der Administration in jeder Sprache, aber nicht auf der Website.** Die Administration ist
  meist so eingestellt, dass sie nicht übersetzte Elemente trotzdem zeigt, damit Redakteure sie finden. Besucher haben
  diese Einstellung nicht.
- **Ein ganzer Zweig fehlt in einer Sprache.** Ein übergeordneter Ordner ist nicht übersetzt und nicht immer
  verfügbar, also erreichen Besucher nicht, was darunter liegt. Übersetzen Sie den Ordner, oder schalten Sie die
  Einstellung für ihn ein.

## Verwandte Seiten

- [Sprachen](../sprachen.md#die-hauptsprache)
- [Ersatzregeln für nicht übersetzte Inhalte](../verwaltung/sprachen/ersatzregeln-fuer-unuebersetzte-inhalte.md)
- [Wie finde ich, was noch nicht übersetzt ist?](wie-finde-ich-was-noch-nicht-uebersetzt-ist.md)
- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
