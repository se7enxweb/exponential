---
title: Das Prinzip Bedienbar
audience: government, editor, designer, developer
category: public-sector
order: 310
summary: WCAG-Prinzip 2, bedienbar, für eine öffentliche Stelle, die Exponential betreibt - Zugänglichkeit per Tastatur, ausreichend Zeit, Anfälle, Navigation und Eingabemodalitäten, mit den Aufgaben jeder Rolle und dem, was die Administration admin4 bietet.
keywords: bedienbar, WCAG-Prinzip 2, Tastatur, ausreichend Zeit, Zeitlimits, Anfälle, Blitzen, navigierbar, Sprunglink, Seitentitel, Fokusreihenfolge, Linkzweck, sichtbarer Fokus, Zielgröße, Ziehen, operable, WCAG principle 2, keyboard, enough time, timeouts, seizures, flashing, navigable, skip link, page titled, focus order, link purpose, focus visible, target size, dragging, 2.1, 2.2, 2.3, 2.4, 2.5
type: help
---
# Das Prinzip Bedienbar

Diese Seite richtet sich an alle im Team einer öffentlichen Website. Das zweite Prinzip von WCAG besagt, dass
Bestandteile der Benutzerschnittstelle und die Navigation bedienbar sein müssen (<https://www.w3.org/TR/WCAG22/>). Unten
finden Sie seine fünf Richtlinien mit den Aufgaben jeder Rolle in einem Exponential-Projekt.

## Inhalt

1. [2.1 Per Tastatur zugänglich](#21-per-tastatur-zugänglich)
2. [2.2 Ausreichend Zeit](#22-ausreichend-zeit)
3. [2.3 Anfälle und körperliche Reaktionen](#23-anfälle-und-körperliche-reaktionen)
4. [2.4 Navigierbar](#24-navigierbar)
5. [2.5 Eingabemodalitäten](#25-eingabemodalitäten)
6. [Verwandte Seiten](#verwandte-seiten)

## 2.1 Per Tastatur zugänglich

| Rolle | Tut |
|---|---|
| Entwicklung | native Elemente; Menüs, Dialoge, Schieberegler und eingebettete Widgets funktionieren mit der Tastatur; keine Fallen |
| Redaktion | vermeidet das Einbetten von Widgets Dritter, die die Tastatur festhalten |

admin4: Reiter, Baum, Kontextmenüs, Alternative zum Ziehen und Ablegen, Datumsauswahlen (`AdminKeyboardTest.php`).
Siehe „Tastatur und Fokus“.

## 2.2 Ausreichend Zeit

| Rolle | Tut |
|---|---|
| Administration | wägt die Sitzungsdauer aus Sicherheitsgründen gegen Menschen ab, die langsam arbeiten: `site.ini [Session] SessionTimeout` (ab Werk 259200 s) ([13.7](../../../installation/13-sicherheitshaertung.md#137-sessions-und-cookies)) |
| Design | Slider und Karussells lassen sich anhalten; kein automatisches Neuladen der Seite |
| Redaktion | keine Inhalte, die sich von selbst bewegen, ohne Steuerung zum Anhalten |

Abgelaufene Formulare zeigen die Seite „Formular abgelaufen“
([Seite „Formular abgelaufen“](../../../../eng-us/features/6.0/form-expired-page.md)).

## 2.3 Anfälle und körperliche Reaktionen

| Rolle | Tut |
|---|---|
| Redaktion | keine Videos oder Animationen, die mehr als dreimal pro Sekunde blitzen |
| Design | beachtet `prefers-reduced-motion` (admin4 tut das in `admin4.css`) |

## 2.4 Navigierbar

| Rolle | Tut |
|---|---|
| Entwicklung | Sprunglink, Landmarks, beschreibender `<title>`, logische Fokusreihenfolge, aktuelle Seite mit `aria-current` gekennzeichnet |
| Redaktion | klare Objektnamen (sie werden zu Seitentiteln), beschreibende Überschriften, aussagekräftige Linktexte |
| Design | sichtbarer Fokus; fixierte Elemente, die den Fokus nicht verdecken (2.4.11) |

admin4: Sprunglink, `aria-current="page"` im Brotkrumenpfad und im oberen Menü, ein Fokusrahmen
(`AdminLandmarksTest.php`, `AdminHeaderTabOrderTest.php`).

## 2.5 Eingabemodalitäten

| Rolle | Tut |
|---|---|
| Design | Zielflächen mindestens 24 x 24 px (2.5.8); sichtbare Beschriftungen stimmen mit den zugänglichen Namen überein |
| Entwicklung | Alternativen mit einem einzelnen Zeiger zum Ziehen (2.5.7); keine Aktionen allein beim Drücken des Zeigers |

admin4: Zielflächen mindestens 24 x 24 px ([Styling von admin4](../../../bc/6.0/admin4-gestaltung.md)); das Umsortieren
von Unterelementen per Ziehen funktioniert auch mit der Tastatur und durch Bearbeiten der Prioritätszellen.

## Verwandte Seiten

- [Tastatur und Fokus](tastatur-und-fokus.md)
- [WCAG 2.2 AA auf Exponential abgebildet](wcag-2-2-zuordnung.md)
- [Tastenkürzel](../../tastenkuerzel.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
