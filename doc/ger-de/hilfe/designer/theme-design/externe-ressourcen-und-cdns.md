---
title: Externe Ressourcen und CDNs
audience: designer
category: theme-design
order: 630
summary: Wenn ein Design etwas von einem anderen Host lädt (Schriften, Skripte, Karten, Videos, Analyse): die Kosten an Geschwindigkeit, Datenschutz und Sicherheitsrichtlinie, die selbst gehosteten Alternativen der mitgelieferten Themes, Einwilligung vor Einbettungen von Drittanbietern und die beteiligten Einstellungen.
keywords: CDN, externe Ressourcen, Drittanbieter, Google Fonts, jQuery-CDN, LoadFromCDN, Datenschutz, Einwilligung, YouTube-Einbettung, Karten, Analyse, Subresource Integrity, CSP-Direktiven, Self-Hosting, cdn, external resources, third party, privacy, consent, youtube embed, maps, analytics, subresource integrity, csp directives, self-hosting
type: help
---
# Externe Ressourcen und CDNs

Diese Seite ist für Designer, die versucht sind, ein `<link>` oder `<script>` von einem anderen Host hinzuzufügen. Jeder fremde
Ursprung kostet Geschwindigkeit, Datenschutz und eine Änderung der Sicherheitsrichtlinie der Website. Die Seite beantwortet: wann es
sich lohnt und wie die mitgelieferten Themes es vermeiden oder eingrenzen.

## Inhalt

1. [Die Kosten](#die-kosten)
2. [Zuerst selbst hosten](#zuerst-selbst-hosten)
3. [Wenn ein Drittanbieter unvermeidlich ist](#wenn-ein-drittanbieter-unvermeidlich-ist)
4. [Die Sicherheitsrichtlinie](#die-sicherheitsrichtlinie)

## Die Kosten

- **Geschwindigkeit**: Jeder neue Host bedeutet eine DNS-Abfrage, eine TCP-Verbindung und einen TLS-Handshake vor dem ersten Byte.
  Der Kommentar im Stylesheet des Designs media nennt das als Grund, warum es seine Schrift von einem Schrift-Host in sein eigenes
  `fonts/` verlegt hat.
- **Datenschutz**: Die Adresse des Besuchers und die Seite, die er liest, gehen an den Drittanbieter. In vielen Ländern braucht das
  eine Einwilligung, bevor es geschieht.
- **Verfügbarkeit**: Wenn der Drittanbieter ausfällt oder blockiert ist, bricht Ihr Design.
- **Sicherheitsrichtlinie**: Jeder Ursprung muss in `[ContentSecurityPolicy] Directives[]` der Website aufgenommen werden.

## Zuerst selbst hosten

| Statt | Der mitgelieferte Weg |
|---|---|
| Link zu Google Fonts | Schriftdateien im `fonts/` des Designs, `@font-face` mit dem CSS gepackt ([Schriften in einem Design](schriften-in-einem-design.md)) |
| jQuery von einem CDN | `ezjsc::jquery` mit `ezjscore.ini [eZJSCore] LoadFromCDN=disabled` (Standard): das lokale jQuery 4 |
| Kartenkacheln direkt vom Kachelserver | das Modul `osm_proxy` des Themes starter speichert Kartenkacheln zwischen, sodass Besucher den Kachelserver nie kontaktieren (laut seinem README) |
| Symbolschriften von einem CDN | SVG-Symbole im Design oder in `share/icons/` |

## Wenn ein Drittanbieter unvermeidlich ist

Videos, Karten mit Live-Daten, Zahlungs-Widgets, Analyse:

- **Zuerst fragen.** Das Design media zeichnet einen Platzhalter für ein YouTube-Video und lädt den Player erst, wenn der Besucher
  zustimmt (`exp-youtube-consent.js` und `.css`, Texte als JSON-Block in `pagelayout/head/script.tpl` übergeben), und hat ein Skript
  für die Cookie-Einwilligung (`exp-cookie-consent.js`).
- **Spät laden.** Verwenden Sie `loading="lazy"` an iframes, `defer` an Skripten.
- **Festlegen und prüfen.** Für ein Skript von einem CDN eine feste Versions-URL und einen Subresource-Integrity-Hash
  (`integrity=""`); der Kommentar über `ExternalScripts[]` in `ezjscore.ini` empfiehlt https und Integrität für externe Skripte.

## Die Sicherheitsrichtlinie

Jeder Ursprung wird der Direktive hinzugefügt, die er braucht, pro Siteaccess, zum Beispiel:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[ContentSecurityPolicy]
Directives[frame-src]='self' https://www.youtube-nocookie.com
Directives[img-src]='self' data: blob: https://i.ytimg.com
```

Halten Sie die Liste kurz und im README Ihres Themes dokumentiert, damit Administratoren wissen, was sie erlauben müssen. Siehe
[CSP-sicheres Design](csp-sicheres-design.md).

## Verwandte Seiten

- [Schriften in einem Design](schriften-in-einem-design.md)
- [jQuery in Designs](jquery-in-designs.md)
- [Performance von Themes](theme-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
