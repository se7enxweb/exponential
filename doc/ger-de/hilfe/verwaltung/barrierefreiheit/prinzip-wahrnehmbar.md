---
title: Das Prinzip Wahrnehmbar
audience: government, editor, designer, developer
category: public-sector
order: 300
summary: WCAG-Prinzip 1, wahrnehmbar, erklärt für eine öffentliche Stelle, die Exponential betreibt - was Redaktion, Design und Entwicklung jeweils für Textalternativen, Medien, anpassbare Struktur und unterscheidbare Darstellung tun und was die Administration admin4 bereits bietet.
keywords: wahrnehmbar, WCAG-Prinzip 1, Textalternativen, Alternativtext, Untertitel, anpassbar, Info und Beziehungen, unterscheidbar, Kontrast, Umbruch, Textgröße ändern, Redaktion, Design, Entwicklung, perceivable, WCAG principle 1, text alternatives, alt text, captions, adaptable, info and relationships, distinguishable, contrast, reflow, resize text, 1.1, 1.2, 1.3, 1.4, editors, designers, developers
type: help
---
# Das Prinzip Wahrnehmbar

Diese Seite richtet sich an alle im Team einer öffentlichen Website. WCAG 2.2 gruppiert seine Erfolgskriterien unter
vier Prinzipien; das erste besagt, dass Informationen und Bestandteile der Benutzerschnittstelle so dargestellt werden
müssen, dass die Benutzer sie wahrnehmen können (<https://www.w3.org/TR/WCAG22/>). Unten finden Sie die vier Richtlinien
dieses Prinzips mit dem, was jede Rolle in einem Exponential-Projekt tut, und dem, was die Administration bereits tut.

## Inhalt

1. [1.1 Textalternativen](#11-textalternativen)
2. [1.2 Zeitbasierte Medien](#12-zeitbasierte-medien)
3. [1.3 Anpassbar](#13-anpassbar)
4. [1.4 Unterscheidbar](#14-unterscheidbar)
5. [Verwandte Seiten](#verwandte-seiten)

## 1.1 Textalternativen

| Rolle | Tut |
|---|---|
| Redaktion | füllt **Alternativer Bildtext** für jedes informative Bild aus; beschreibt Diagramme im Text |
| Administration | schaltet „Alternativtext ist erforderlich“ an den Bildattributen der Klassen ein, die die Redaktion verwendet |
| Design | entscheidet, wie dekorative Bilder gekennzeichnet werden; Links und Schaltflächen, die nur aus einem Symbol bestehen, erhalten einen Namen |
| Entwicklung | überschreibt das Bild-Template, wenn dekorative Bilder `alt=""` ausgeben müssen (das Standard-Template weicht auf den Objektnamen aus) |

admin4: Jedes Formularelement hat einen zugänglichen Namen (`AdminFormLabelsTest.php`). Siehe „Alternativtext für
Bilder“.

## 1.2 Zeitbasierte Medien

| Rolle | Tut |
|---|---|
| Redaktion | Untertitel für Videos mit Sprache, Transkripte für Audio und Video, keine Auto-Wiedergabe |
| Entwicklung | fügt im Medien-Template Untertitelspuren hinzu, wenn Sie sie brauchen (das Standard-Template schreibt keine) |

Siehe „Untertitel und Medien“.

## 1.3 Anpassbar

| Rolle | Tut |
|---|---|
| Redaktion | echte Überschriften in der richtigen Reihenfolge, Listen, Tabellen mit Kopfzellen und Beschriftungen |
| Design | Lesereihenfolge gleich visueller Reihenfolge; keine Bedeutung allein durch die Position |
| Entwicklung | Landmarks, ein `h1`, `scope` an Kopfzellen, beschriftete Formularfelder, `autocomplete` an persönlichen Feldern |

admin4: Landmarks, Sprunglink, `scope="col"`, Pflichtfelder für Screenreader gekennzeichnet, Schreibrichtung für
Sprachen von rechts nach links (`AdminLandmarksTest.php`, `AdminRightToLeftTest.php`).

## 1.4 Unterscheidbar

| Rolle | Tut |
|---|---|
| Redaktion | nie Farbe allein („die Felder in Rot“); keine Bilder von Text |
| Design | Text 4,5:1, Bedienelemente und Fokus 3:1, Links unterscheidbar, Umbruch bei 320 px, 200 % Zoom, Textabstände |
| Entwicklung | Tokens, keine festen Höhen, die Text abschneiden, kein horizontales Scrollen der Seite, Benutzereinstellungen respektieren |

admin4: Kontrast-Tokens im hellen und dunklen Modus geprüft, Meldungen mit Symbolen und Farben, responsive Breiten,
erzwungene Farben und reduzierte Bewegung (`AdminTokenContrastTest.php`, `AdminNoticesTest.php`,
`AdminResponsiveTest.php`).

## Verwandte Seiten

- [WCAG 2.2 AA auf Exponential abgebildet](wcag-2-2-zuordnung.md)
- [Farbe und Kontrast in Ihrem Theme](farbe-und-kontrast.md)
- [Alternativtext für Bilder](alternativtext-fuer-bilder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
