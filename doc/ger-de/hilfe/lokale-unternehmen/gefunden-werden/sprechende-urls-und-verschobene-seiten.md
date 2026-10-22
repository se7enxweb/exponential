---
title: Sprechende URLs und verschobene Seiten
audience: local-business, businesses, customer, editor
category: local-business
order: 24
summary: Wie Exponential aus Seitennamen lesbare Seitenadressen macht, was mit der alten Adresse passiert, wenn eine Seite umbenannt oder verschoben wird, kurze Adressen für Flyer und Schilder und worauf Sie achten, wenn Sie eine alte Website ersetzen.
keywords: sprechende URLs, URL-Alias, Adresse, Weiterleitung, umbenannte Seite, verschobene Seite, kurze URL, Flyer, QR-Code, alte Website, Wildcards, lokales Unternehmen, nice URLs, URL alias, redirect, short URL, old website, local business
type: help
---
# Sprechende URLs und verschobene Seiten

Diese Seite ist für Redakteure oder Inhaber der Website eines kleinen Unternehmens, die lesbare, stabile
Seitenadressen wollen. Exponential erledigt das meiste davon von selbst.

## Inhalt

1. [Adressen entstehen aus Namen](#adressen-entstehen-aus-namen)
2. [Seiten umbenennen und verschieben](#seiten-umbenennen-und-verschieben)
3. [Kurze Adressen für Gedrucktes](#kurze-adressen-für-gedrucktes)
4. [Eine alte Website ersetzen](#eine-alte-website-ersetzen)
5. [Checkliste](#checkliste)

## Adressen entstehen aus Namen

Wenn Sie eine Seite namens „Opening hours“ in einem Ordner namens „Visit us“ veröffentlichen, gibt Exponential ihr
die Adresse `/Visit-us/Opening-hours` (eine Seite „Öffnungszeiten“ im Ordner „Besuchen Sie uns“ entsprechend eine
deutsche Adresse). Das nennt man einen **URL-Alias**, und er ist eingebaut
([URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)).

Wählen Sie Namen deshalb mit Bedacht:

- kurz und klar: „Speisekarte“, nicht „Unsere wunderbare Speisekarte für 2026“;
- keine Jahreszahlen in Ordnernamen, die sich ändern werden;
- in derselben Sprache wie die Besucher, die sie lesen (eine zweite Sprache hat eigene Adressen:
  [Mehrsprachig für Urlaubsregionen](../marketing/mehrsprachig-fuer-urlaubsregionen.md)).

## Seiten umbenennen und verschieben

Wenn eine Seite umbenannt oder verschoben wird, funktioniert die alte Adresse weiter und schickt Besucher zur neuen.
Exponential behält dafür standardmäßig **Verlaufs-Aliase**
([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)). Links in
alten Newslettern, gedruckte QR-Codes und Ergebnisse von Suchmaschinen gehen nicht kaputt.

Was die alte Adresse kaputt macht:

- die Seite zu löschen (in den Papierkorb zu verschieben);
- ein Administrator, der Verlaufs-Aliase entfernt.

Wenn eine Seite nicht mehr gebraucht wird, überlegen Sie, sie auszublenden oder auf eine Ersatzseite zu verweisen,
statt sie zu löschen. Routine: [URLs, Weiterleitungen und verschobene Seiten](../../kunden/alltag/urls-weiterleitungen-und-verschobene-seiten.md).

## Kurze Adressen für Gedrucktes

Ein Flyer oder ein Schild im Schaufenster braucht eine kurze Adresse, etwa `example.com/menu` oder
`example.com/offer`. Ein Administrator kann **eigene Aliase** und **Wildcards** anlegen, die eine kurze Adresse auf
eine beliebige Seite schicken ([URL-Wildcards und eigene Aliase](../../ersteller/website-ersteller/url-platzhalter.md)).
Gute Einsatzzwecke:

| Kurze Adresse | Führt zu |
|---|---|
| `/menu` | `/Food-and-drink/Menu` |
| `/hours` | `/Visit-us/Opening-hours` |
| `/offer` | dem Angebot des Monats, jeden Monat geändert |

Führen Sie eine Liste der kurzen Adressen, die Sie gedruckt haben, damit niemand eine entfernt, die noch auf einem
Schild steht.

## Eine alte Website ersetzen

Wenn eine neue Exponential-Website eine alte ersetzt, stehen die alten Adressen in Suchergebnissen, Lesezeichen und
auf den Seiten anderer Leute. Vor der Umstellung:

1. Erstellen Sie eine Liste der alten Adressen, auf die es ankommt (die Speisekarte, die Kontaktseite, die
   meistbesuchten Seiten).
2. Bitten Sie Ihren Ersteller, jede mit einer dauerhaften Weiterleitung auf ihre neue Seite zu schicken (Wildcards
   oder Regeln des Webservers).
3. Öffnen Sie nach der Umstellung jede alte Adresse, und prüfen Sie, dass sie auf der richtigen neuen Seite landet.

## Checkliste

- [ ] Ordner- und Seitennamen sind kurz und ändern sich nicht oft.
- [ ] Alte Adressen einer ersetzten Website leiten auf die neuen Seiten weiter.
- [ ] Gedruckte kurze Adressen stehen auf einer Liste und sind getestet.
- [ ] Niemand löscht Seiten mit gedruckten oder bekannten Adressen ohne Ersatz.

## Verwandte Seiten

- [Grundlagen der lokalen Suchmaschinenoptimierung](grundlagen-der-lokalen-suchmaschinenoptimierung.md)
- [Den Inhaltsbaum gliedern](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)
- [URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
