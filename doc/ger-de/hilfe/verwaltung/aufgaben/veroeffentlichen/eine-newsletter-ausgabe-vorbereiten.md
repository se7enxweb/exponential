---
title: Eine Newsletter-Ausgabe vorbereiten
audience: government, editor
category: public-sector
order: 540
summary: Für Redakteure - eine Newsletter-Ausgabe mit cjw_newsletter vorbereiten - die Ausgabe unter der Liste anlegen, Artikel hinzufügen (oder Inhalte aus dem Artikelpool wiederverwenden), einen Test senden, ihn prüfen und den Versand planen.
keywords: Newsletter, Ausgabe, cjw_newsletter, Newsletter dashboard, Artikelpool, Testversand, Vorschau, planen, senden, Liste, Skin, Abonnenten, newsletter, edition, article pool, test send, preview, schedule, send, list, skin, subscribers
type: help
---
# Eine Newsletter-Ausgabe vorbereiten

Diese Seite ist für Redakteure, die einen Newsletter zusammenstellen: eine monatliche Übersicht über Nachrichten,
Beteiligungsverfahren und Veranstaltungen. Die Erweiterung `cjw_newsletter` hält Ausgaben als Inhalte unter einer
Liste; der Bereich **Newsletter** der Administration (`/newsletter/index`, das **Newsletter dashboard**) zeigt
Abonnenten, Versand, Rückläufer und die redaktionellen Werkzeuge
([Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md),
[Newsletter versenden](../../../../../eng-us/help/creators/website-creators/sending-newsletters.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Das Newsletter-System und die Liste existieren, und Sie dürfen unter der Liste Ausgaben anlegen.
- Sie wissen, wer Ausgaben freigibt, falls Ihre Site Freigaben verwendet.
- Die Site versendet echte E-Mails erst nach einer Probe; fragen Sie das Web-Team, welcher Transport aktiv ist.

## Schritte

1. Öffnen Sie die Liste im Inhaltsbaum (unter dem Wurzelordner der Newsletter), **Neu erstellen**, wählen Sie
   **CJW Newsletter Edition**; geben Sie ihr einen Titel (die Betreffzeile).
2. Fügen Sie unter der Ausgabe Artikel hinzu (**CJW Newsletter Article**) oder verwenden Sie bestehende Inhalte aus dem
   Artikelpool wieder (**Article pools** im Newsletter dashboard).
3. Halten Sie jeden Artikel kurz, mit einem Link zur vollständigen Seite auf der Site; geben Sie Bildern
   Alternativtexte.
4. Sehen Sie sich die **Vorschau** der Ausgabe im Newsletter-Skin an.
5. Senden Sie einen Test an sich selbst und an eine Kollegin oder einen Kollegen (der Testversand an der Ausgabe);
   lesen Sie ihn auf einem Telefon und mit ausgeschalteten Bildern.
6. Gehen Sie die Prüfliste unten durch; planen Sie den Versand dann mit **Send**. Der Cronjob reiht ihn ein und
   versendet ihn.

## Das Ergebnis prüfen

- [ ] Die Test-E-Mail ist angekommen, Betreff und Absender stimmen, jeder Link funktioniert und zeigt auf die
  öffentliche Site.
- [ ] Die E-Mail liest sich auch ohne Bilder gut.
- [ ] Nach dem Versand zeigt das Newsletter dashboard den Versand unter **Last sends**; Rückläufer erscheinen unter
  **Bounces**.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| An die falsche Liste senden | prüfen Sie die Liste vor **Send** |
| Links zur Administration oder zu einer Staging-Site | testen Sie die Links abgemeldet |
| Eine Ausgabe für dringende Warnmeldungen verwenden | Newsletter werden geplant; dringende Informationen kommen zuerst auf die Site ([Eine dringende Warnmeldung auf der Startseite veröffentlichen](eine-dringende-warnmeldung-auf-der-startseite-veroeffentlichen.md)) |
| Personenbezogene Daten in der Ausgabe | der Newsletter geht an jeden Abonnenten; veröffentlichen Sie nichts, was Sie nicht auch auf der Site veröffentlichen würden |

Ein Versand lässt sich stoppen, während er läuft (`newsletter/send_abort`); fragen Sie das Web-Team, wenn etwas schief
gegangen ist.

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Newsletter (Nachrichten und Warnmeldungen)](../../aufbauen/nachrichten-und-warnmeldungen/newsletter.md)
- [Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
