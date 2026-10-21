---
title: "Screenshots für ein Theme"
audience: designer, sales-marketing
category: theme-distribution
order: 310
summary: Welche Screenshots ein Theme braucht - Startseite, ein Artikel, eine Liste, heller und dunkler Modus, ein Telefon und eine Ansicht mit 200 % Zoom -, wie Sie sie reproduzierbar aufnehmen, wo Sie sie aufbewahren und was nie darauf erscheinen darf.
keywords: Screenshots, Bilder, Startseite, Dunkelmodus, mobil, Zoom, README-Bild, Produktseite, Demo-Inhalte, Datenschutz, screenshots, images, front page, dark mode, mobile, zoom, README image, product page, demo content, privacy
type: help
---
# Screenshots für ein Theme

Diese Seite ist für Designer, die die Bilder vorbereiten, die ein Theme auf GitHub, Packagist, einer Projektseite oder
einer Produktseite zeigen. Screenshots sind der erste Qualitätsnachweis, den Menschen sehen.

## Der Satz, den Sie erstellen

| Screenshot | Größe | Zeigt |
|---|---|---|
| Startseite, hell | 1440 px breit | Das Gesamtbild |
| Startseite, dunkel | 1440 px breit | Dass der Dunkelmodus gestaltet ist und kein nachträglicher Einfall (falls das Theme einen hat) |
| Ein Artikel (Vollansicht) | 1440 px breit | Typografie und Lesekomfort |
| Eine Liste oder ein Suchergebnis | 1440 px breit | Zeilenansichten und Blättern |
| Telefon | 375 px breit | Das responsive Layout |
| 200 % Zoom | 960 px breit mit Skalierung 2 | Dass das Layout hält, wenn Menschen den Text vergrößern |

Das Projekt Exponential prüft seine eigenen Seiten bei 1440 px, 960 px mit Skalierung 2 und 375 px, im hellen und
dunklen Modus; dieselben Größen zu verwenden, lässt Nutzer vergleichen.

## Schritt für Schritt

1. Installieren Sie das Theme auf einer Demo-Installation mit Demo-Inhalten ([Demo-Inhalte für ein Theme](demo-inhalte.md)).
2. Verwenden Sie ein Browserfenster mit genau der Breite oder einen Headless-Browser, den Sie bei jedem Release erneut
   ausführen können.
3. Nehmen Sie jeden Screenshot der Tabelle auf. Speichern Sie als PNG oder WebP mit aussagekräftigen Namen:
   `front-light-1440.png`, `front-dark-1440.png`, `article-375.png`.
4. Bewahren Sie sie im Repository auf, zum Beispiel in `doc/screenshots/`, damit das README sie mit einem relativen
   Pfad zeigen kann.
5. Schreiben Sie im README zu jedem einen kurzen Alternativtext: was das Bild zeigt.
6. Gehen Sie die [Screenshot-Checkliste](screenshot-checkliste.md) durch.

## Nie auf einem Screenshot

- Echte Inhalte von Kunden, Namen, E-Mail-Adressen oder Gesichter ohne Einwilligung.
- Eine Seite der Administration mit Benutzernamen, Serverpfaden oder Einstellungen.
- Browsererweiterungen, Lesezeichen oder andere Tabs.
- Inhalte, die Sie nicht zeigen dürfen (Fotos oder Logos anderer).

## Verwandte Seiten

- [Eine Demo-Website betreiben](demo-websites.md)
- [Testen bei Zoomstufen](../theme-design/testen-bei-zoomstufen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
