---
title: Einer Seite eine kurze Adresse geben
audience: government, editor
category: public-sector
order: 360
summary: Für Redakteure - einer Seite eine kurze, einprägsame Adresse für Druck und Radio geben (example.gov/permits), mit einem URL-Alias auf der Seite URL-Aliase des Knotens, wählen, ob er weiterleitet, und Konflikte vermeiden.
keywords: kurze Adresse, sprechende URL, URL-Alias, Neuer Alias, Alias im Hauptverzeichnis erstellen, Der Alias soll zum Ziel weiterleiten, Kampagnenadresse, Druck, short address, vanity URL, URL alias, Create new alias, URL alias name, Alias should redirect, Place alias on the site root, campaign address, print
type: help
---
# Einer Seite eine kurze Adresse geben

Diese Seite ist für Redakteure, die für eine Seite eine kurze Adresse brauchen, um sie auf ein Faltblatt zu drucken
oder im Radio zu nennen: `/permits` statt `/living/roads-and-parking/parking/resident-parking-permits`. Ein URL-Alias an
der Seite leistet das ([URLs und Aliase](../../../../anleitungen/urls-und-aliase.md),
[URL-Aliase und sprechende URLs](../../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Wählen Sie die kurze Adresse: ein Wort, das man sich merkt, kleingeschrieben, ohne Leerzeichen (`permits`, `vote`,
  `heat`).
- Prüfen Sie, ob sie frei ist: Öffnen Sie `/<Wort>` auf der Site; die Antwort sollte „nicht gefunden“ lauten.
- Ihre Rolle darf Aliase verwalten (die Seite URL-Aliase eines Knotens braucht Bearbeitungsrechte; globale Aliase
  brauchen `content/urltranslator`).

## Schritte

1. Öffnen Sie die Seite; öffnen Sie ihre Seite **URL-Aliase** (aus dem Menü des Elements oder über
   `/content/urlalias/<node id>`). Die Seite listet die **Generated aliases** und die Aliase, die Sie angelegt haben.
2. Unter **Neuer Alias**:
   - **URL alias name**: `permits`;
   - **Sprache**: die Sprache der Seite;
   - markieren Sie **Alias im Hauptverzeichnis erstellen**, damit die Adresse `/permits` lautet und nicht unter dem
     übergeordneten Element der Seite liegt;
   - markieren Sie **Der Alias soll zum Ziel weiterleiten**, damit Besucher (und Suchmaschinen) auf der echten Adresse
     landen.
3. Klicken Sie auf **Erstellen**.

## Das Ergebnis prüfen

- [ ] Abgemeldet öffnet `https://<Ihre Site>/permits` die Seite (und mit eingeschalteter Weiterleitung zeigt die
  Adresszeile die vollständige Adresse).
- [ ] Der Alias steht auf der Seite URL-Aliase des Knotens; **Ausgewählte entfernen** entfernt ihn später.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Ein Wort, das schon die Adresse eines Ordners ist | der Alias kollidiert; wählen Sie ein anderes |
| Eine kurze Adresse für eine befristete Kampagne | entfernen Sie sie, wenn die Kampagne endet, oder lassen Sie sie auf eine erklärende Seite zeigen |
| Die Adresse drucken, bevor sie getestet ist | testen Sie zuerst |
| Eine Adresse je Sprache | legen Sie bei Bedarf einen Alias für jeden Sprach-Siteaccess an |
| Ein Alias auf eine Modulseite (Suche, eine Formularansicht) | das ist ein globaler Alias: **Setup > URL-Übersetzer** |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Alte Adressen nach einer Umstrukturierung weiterleiten](alte-adressen-nach-einer-umstrukturierung-weiterleiten.md)
- [Weiterleitungen und verschobene Inhalte](../../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
