---
title: Privatsphäre vor der Kamera
audience: press, advocate
category: press-media
order: 193
summary: Wie Creator und Journalisten die personenbezogenen Daten anderer Menschen aus Videos, Streams und Bildschirmfotos von Exponential heraushalten - Demodaten statt echter Daten, was Sie in der Administration verbergen, Gäste und Zuschauer, Einwilligung und was zu tun ist, wenn versehentlich personenbezogene Daten gezeigt wurden.
keywords: Privatsphäre, personenbezogene Daten, Video, Livestream, Bildschirmfotos, Einwilligung, Demodaten, Benutzerliste, E-Mail-Adressen, DSGVO, unkenntlich machen, Gäste, Zuschauer, privacy, personal data, live stream, screenshots, consent, demo data, users list, GDPR, blur, guests, viewers
type: help
---
# Privatsphäre vor der Kamera

Diese Seite ist für Video-Creator, Streamer und Journalisten, die Exponential auf dem Bildschirm zeigen. Ein
Content-Management-System enthält Daten von Menschen: Benutzerkonten, E-Mail-Adressen, Formulareinsendungen,
Bestellungen, Newsletter-Abonnenten, Protokolleinträge. Nichts davon gehört in eine Aufnahme, wenn die betroffene Person
nicht zugestimmt hat. Die einfache Regel: **Nehmen Sie nur mit Demodaten auf.**

## Inhalt

1. [Demodaten verwenden](#demodaten-verwenden)
2. [Bildschirme mit personenbezogenen Daten](#bildschirme-mit-personenbezogenen-daten)
3. [Menschen in der Aufnahme](#menschen-in-der-aufnahme)
4. [Wenn personenbezogene Daten gezeigt wurden](#wenn-personenbezogene-daten-gezeigt-wurden)
5. [Eine Prüfung vor der Aufnahme](#eine-prüfung-vor-der-aufnahme)

## Demodaten verwenden

Nehmen Sie auf einer Installation auf, die Sie für diesen Zweck eingerichtet haben, nie auf einer Produktionswebsite
oder in der Administration eines Kunden ([Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md)). Legen
Sie erfundene Benutzer mit Namen wie „Demo Editor“ und E-Mail-Adressen unter `example.com` an, das für Beispiele
reserviert ist. Die Seite der Fürsprecher [Die Privatsphäre von Menschen schützen](../fuersprecher/privatsphaere-schuetzen.md)
hat dieselben Regeln für Vorträge und Beiträge.

## Bildschirme mit personenbezogenen Daten

| Bildschirm in der Administration | Was er zeigen kann | Tun Sie stattdessen |
|---|---|---|
| Benutzer und Rollen | Namen, Benutzernamen, E-Mail-Adressen | Zeigen Sie ihn nur mit erfundenen Benutzern |
| Shop: Bestellungen und Kunden | Namen, Adressen, Bestellinhalte | Verwenden Sie Demobestellungen oder lassen Sie ihn weg |
| Gesammelte Informationen, Formulare | Was Besucher eingegeben haben | Verwenden Sie Ihre eigenen Testeinsendungen |
| Newsletter | Abonnentenlisten | Weglassen oder Testabonnenten verwenden |
| Protokolle, der Audit-Trail, die Debug-Leiste | IP-Adressen, Benutzernamen, Pfade, manchmal Hostnamen | Nur auf einer frischen Demo zeigen; zuschneiden |
| Setup und Systeminformationen | Hostnamen, Pfade, Versionen der Serversoftware | Zuschneiden oder unkenntlich machen; siehe [Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md) |
| E-Mail-Programme neben dem Browser | Echte Posteingänge | Schließen Sie sie |

## Menschen in der Aufnahme

- **Gäste**: Fragen Sie vor der Aufnahme, sagen Sie ihnen, wo sie veröffentlicht wird, und lassen Sie sie prüfen, was
  sie auf ihrem eigenen Bildschirm gezeigt haben.
- **Live-Chat**: Die Namen der Zuschauer erscheinen im Bild; folgen Sie den Regeln der Plattform und lesen Sie keine
  persönlichen Angaben vor.
- **Fragen mit Bildschirmfotos von Zuschauern**: Zeigen Sie nicht die Administration eines Zuschauers im Stream; bitten
  Sie ihn, das Problem zu beschreiben oder ein Bildschirmfoto ohne personenbezogene Daten zu schicken.
- **Kunden und Arbeitgeber**: Nennen Sie den Betreiber einer Website nie ohne seine schriftliche Einwilligung als
  Anwender von Exponential ([Privatsphäre in Ihrer Berichterstattung](privatsphaere-in-ihrer-berichterstattung.md)).

## Wenn personenbezogene Daten gezeigt wurden

1. Halten Sie die Aufnahme an oder schneiden Sie die Stelle heraus; bei einem Livestream beenden Sie die Szene mit den
   Daten.
2. Entfernen oder bearbeiten Sie das veröffentlichte Video schnell; machen Sie die Bilder unkenntlich oder laden Sie es
   neu hoch.
3. Wenn ein Passwort, Token oder Schlüssel zu sehen war, ändern Sie es sofort.
4. Sagen Sie der betroffenen Person, was passiert ist, wenn Sie wissen, wer es ist.
5. Vermerken Sie in der Beschreibung, dass das Video bearbeitet wurde, ohne die Daten zu wiederholen
   ([Ein Video oder einen Beitrag korrigieren](ein-video-oder-einen-beitrag-korrigieren.md)).

Die Gesetze zu personenbezogenen Daten unterscheiden sich von Land zu Land; wenn Sie sich über Ihre Pflichten nicht
sicher sind, fragen Sie jemanden, der die Regeln dort kennt, wo Sie veröffentlichen.

## Eine Prüfung vor der Aufnahme

```text
[ ] Demo installation, not production
[ ] Only fictional users, at example.com addresses
[ ] No orders, forms, newsletters or logs with real data
[ ] E-mail, chat and notification pop-ups closed
[ ] Browser history and autocomplete cleared or a clean profile used
[ ] Guests asked and informed
```

## Verwandte Seiten

- [Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)
- [Leitfaden für Livestreams](leitfaden-fuer-livestreams.md)
- [Die Privatsphäre von Menschen schützen](../fuersprecher/privatsphaere-schuetzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
