---
title: Eine Seite aktualisieren, die alte Inhalte zeigt
audience: government, editor
category: public-sector
order: 590
summary: Für Redakteure - wenn eine veröffentlichte Änderung nicht erscheint, prüfen, was Sie sehen, den Cache der Seite über den Kasten Cache leeren der Administration leeren (Inhalt - Knoten oder Teil der Baumstruktur) und wissen, welche Caches nur das Web-Team leeren kann.
keywords: Cache, alte Inhalte, Seite nicht aktualisiert, Cache leeren, Inhalt - Knoten, Inhalt - Teil der Baumstruktur, View-Cache, statischer Cache, Velocity, CDN, Browser-Cache, old content, page not updated, Clear cache, Content - node, Content - subtree, view cache, static cache, browser cache
type: help
---
# Eine Seite aktualisieren, die alte Inhalte zeigt

Diese Seite ist für Redakteure, die eine Änderung veröffentlicht haben und noch die alte Seite sehen. Exponential leert
beim Veröffentlichen den View-Cache des Objekts und verwandter Seiten, aber Listen in Cache-Blöcken, der statische
Cache, der Antwort-Cache von Velocity, ein CDN oder Ihr eigener Browser können die alte Version noch eine Weile zeigen
([Caches](../../../../anleitungen/caches.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Stellen Sie sicher, dass die Änderung veröffentlicht ist: **Versionen verwalten** zeigt Ihre Version als
  *Veröffentlicht* (nicht als *Entwurf* oder *Ausstehend*).
- Sehen Sie in einem privaten Browserfenster nach, abgemeldet: Ihr eigener Browser zeigt vielleicht eine gespeicherte
  Kopie.

## Schritte

1. Öffnen Sie die Seite in der Administration.
2. Wählen Sie im Kasten **Cache leeren** in der Seitenspalte der Administration:
   - **Inhalt - Knoten** für diese Seite;
   - **Inhalt - Teil der Baumstruktur** für diese Seite und alles darunter (zum Beispiel einen Ordner, dessen Liste
     veraltet ist);
3. klicken Sie auf **Leeren**.
4. Laden Sie die Seite abgemeldet in einem privaten Fenster neu.
5. Immer noch alt: Die Kopie liegt in einem Cache vor dem CMS (statischer Cache, der Antwort-Cache von Velocity, ein
   CDN). Fragen Sie das Web-Team; es kann diese leeren (zum Beispiel `./console exp:velocity cache clear` für Velocity).

Die anderen Auswahlmöglichkeiten des Kastens (**Template**, **Ini Einstellungen**, **Statisch**, **Gesamter Cache**)
sind für Administratoren; sie betreffen die ganze Site.

## Das Ergebnis prüfen

- [ ] Die Seite zeigt abgemeldet den neuen Inhalt.
- [ ] Listen auf anderen Seiten (Startseite, Menüs) erneuern sich mit ihrer eigenen Ablaufzeit; prüfen Sie sie später
  oder fragen Sie das Web-Team.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Die Version ist noch ausstehend | eine Freigabe hält sie zurück ([Eine Seite zur Freigabe schicken](eine-seite-zur-freigabe-schicken.md)) |
| **Gesamter Cache** auf einer stark besuchten Site leeren | verlangsamt die ganze Site; überlassen Sie das den Administratoren |
| Eine Datei im Cache (PDF) ist alt | ersetzen Sie die Datei ordnungsgemäß ([Ein Dokument durch eine neue Version ersetzen](ein-dokument-durch-eine-neue-version-ersetzen.md)) |
| Die Änderung wurde in einer anderen Sprache vorgenommen | prüfen Sie den Sprach-Siteaccess |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [View-Cache und Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md)
- [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
