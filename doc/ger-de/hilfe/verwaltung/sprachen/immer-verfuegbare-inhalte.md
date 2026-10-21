---
title: Immer verfügbare Inhalte
audience: government, editor, admin
category: public-sector
order: 100
summary: Was die Markierung „Immer verfügbar“ eines Objekts auf einer mehrsprachigen Exponential-Website bewirkt, welche Einträge sie tragen sollten (Logos, Medienordner, Benutzer, Dokumente in nur einer Sprache), wie Sie sie pro Objekt und pro Klasse setzen, und ihre Fallstricke.
keywords: immer verfügbar, Die Hauptsprache verwenden, wenn keine vorrangige Übersetzung vorhanden ist, Reiter Übersetzungen, Sprachmaske, gemeinsame Einträge, Medien, Benutzer, Voreinstellung der Klasse, always available, translations tab, language mask, shared items, media, users, class default
type: help
---
# Immer verfügbare Inhalte

Diese Seite richtet sich an Redakteure und Administratoren. **Immer verfügbar** ist eine Markierung pro Objekt: Ein
solches Objekt wird in seiner Hauptsprache auf einem Siteaccess angezeigt, der keine seiner Sprachen aufführt, wo es
sonst ausgeblendet wäre
([Inhaltssprachen, Abschnitt 6](../../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)).

## Inhalt

1. [Welche Einträge immer verfügbar sein sollten](#welche-einträge-immer-verfügbar-sein-sollten)
2. [Die Markierung an einem Eintrag setzen](#die-markierung-an-einem-eintrag-setzen)
3. [Die Voreinstellung einer Klasse setzen](#die-voreinstellung-einer-klasse-setzen)
4. [Fallstricke](#fallstricke)

## Welche Einträge immer verfügbar sein sollten

| Eintrag | Warum |
|---|---|
| Medienordner und Bilder | ein Bild ist in jeder Sprache dasselbe |
| Benutzerkonten und -gruppen | damit der Name einer Autorin auf jeder Sprachwebsite erscheint |
| Logos, Symbole | sprachneutral |
| Ein Dokument, das es nur in einer Sprache gibt (ein Rechtstext) | damit andere Sprachwebsites darauf verlinken können |
| Ordner, die nur Struktur enthalten | damit der Baum navigierbar bleibt |

Seiten mit Text für Leser sollten in der Regel nicht immer verfügbar sein: Sie sollten übersetzt werden oder über die
`SiteLanguageList[]` des Siteaccess auf eine Ersatzsprache zurückfallen (siehe „Ersatzregeln für unübersetzte
Inhalte“).

## Die Markierung an einem Eintrag setzen

1. Öffnen Sie den Eintrag; öffnen Sie den Reiter **Übersetzungen**.
2. Setzen Sie das Häkchen bei **Die Hauptsprache verwenden, wenn keine vorrangige Übersetzung vorhanden ist.**
3. Klicken Sie auf **Aktualisieren**.

Auf der Seite einer Sprache unter **Setup > Sprachen** sind immer verfügbare Objekte in der Objektliste markiert.

## Die Voreinstellung einer Klasse setzen

Bearbeiten Sie unter **Setup > Klassen** die Klasse und setzen Sie **Standard Objekt Verfügbarkeit** („immer
verfügbar“); neue Objekte der Klasse erhalten sie. Bestehende Objekte behalten ihre Einstellung.

## Fallstricke

- Eine immer verfügbare Seite auf Englisch erscheint ohne Warnung auf Englisch auf der französischen Website, wenn der
  französische Siteaccess nur Französisch aufführt. Setzen Sie die Markierung bewusst ein, nicht als Abkürzung für
  fehlende Übersetzungen.
- Die Hauptübersetzung lässt sich nicht entfernen, solange sie die Hauptübersetzung ist; ändern Sie zuerst die
  Hauptsprache (siehe „Hauptsprache eines Eintrags“).
- Die Markierung ist Bit 0 der Sprachmaske des Objekts; sie ändert sonst nichts am Objekt.

## Verwandte Seiten

- [Hauptsprache eines Eintrags](hauptsprache-eines-eintrags.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen (für Redakteure)](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
