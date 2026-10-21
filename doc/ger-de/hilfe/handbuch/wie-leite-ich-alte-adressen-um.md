---
title: Wie leite ich eine Gruppe alter Adressen um (URL-Platzhalter)?
audience: admin
category: manual
order: 525
summary: Geben Sie unter Setup > URL Platzhalter ein Muster wie old-blog/* und ein Ziel wie blog/{1} ein, wählen Sie Weiterleitend oder Direkt, klicken Sie auf Erstellen und prüfen Sie es dann mit Eine Adresse ausprobieren.
keywords: URL-Platzhalter, Weiterleitung, alte Adressen, Muster, 301, Migration, content/urlwildcards, URL wildcards, redirect, old addresses, pattern, forwarding, migration
context: content/urlwildcards
type: help
---
# Wie leite ich eine Gruppe alter Adressen um (URL-Platzhalter)?

Ein Platzhalter leitet jede Adresse, die auf ein Muster passt, an eine andere weiter, zum Beispiel alle Adressen
`old-blog/...` nach `blog/...` nach einem Umzug oder Relaunch.

## Wer das tun kann

Benutzer, deren Rolle `content/urltranslator` hat.

## Schritte

1. Öffnen Sie **Setup** > **URL Platzhalter**.
2. Geben Sie unter **Neue Weiterleitung mit Platzhalter anlegen** das Muster ein, zum Beispiel `old-blog/*`.
3. Geben Sie das Ziel ein. `{1}` steht für das, worauf der `*` gepasst hat, zum Beispiel `blog/{1}`. Es kann auch eine
   Adresse auf einer anderen Website sein.
4. Wählen Sie **Weiterleitend** (Besucher und Suchmaschinen sehen die neue Adresse) oder **Direkt** (die Seite wird unter
   der alten Adresse angezeigt).
5. Klicken Sie auf **Erstellen**.
6. Geben Sie unter **Eine Adresse ausprobieren** eine alte Adresse ein und klicken Sie auf **Ausprobieren**: Die Seite
   zeigt unter **Wird zu**, wohin sie führt.

![URL Platzhalter mit den Formularen Neue Weiterleitung mit Platzhalter anlegen und Eine Adresse ausprobieren](../../../eng-us/help/manual/images/url-wildcards.webp)

## Was schiefgehen kann

- **Adressen laufen im Kreis oder führen auf die falsche Seite.** Ein Ziel, das auf einen anderen Platzhalter passt,
  erzeugt eine Schleife. Probieren Sie die Adresse zuerst aus, und halten Sie Muster spezifisch.
- **Der Platzhalter wirkt nicht.** Eine echte Seite oder ein Alias mit dieser Adresse hat Vorrang. Platzhalter fangen
  nur Adressen ab, die es nicht gibt.

## Verwandte Seiten

- [Wie gebe ich einer Seite eine zusätzliche Adresse?](wie-gebe-ich-einer-seite-eine-zusaetzliche-adresse.md)
- [URL-Platzhalter](../../../eng-us/help/creators/website-creators/url-wildcards.md)
- [Weiterleitungen und verschobene Inhalte](../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
