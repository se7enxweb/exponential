---
title: Rechts nach links in der Administration
audience: government, admin, editor
category: public-sector
order: 160
summary: Was Mitarbeitende, die mit einer hebräischen, arabischen oder anderen Oberflächensprache mit Schreibrichtung von rechts nach links arbeiten, seit 6.0.15 in der Administration von Exponential erhalten - ein gespiegeltes Layout, isolierte Namen, Bearbeiten von rechts nach links und die bekannten offenen Punkte.
keywords: von rechts nach links, RTL, Administration, Administrationsoberfläche, Hebräisch, Arabisch, gespiegeltes Layout, Bidi, isolierte Namen, Schreibrichtung im Editor, Oberflächensprache, right to left, admin interface, Hebrew, Arabic, mirrored layout, bidi, isolated names, editor direction, interface language
type: help
---
# Rechts nach links in der Administration

Diese Seite richtet sich an Administratoren und Redakteure, deren Mitarbeitende die Administration in einer Sprache
mit Schreibrichtung von rechts nach links nutzen. Seit 6.0.15 dreht sich die Administration um, wenn die
Oberflächensprache von rechts nach links geschrieben wird: Die Seite trägt `dir="rtl"`, die Seitenspalten tauschen
die Plätze, Text beginnt rechts, und Symbole, die entlang der Zeile zeigen, zeigen in die andere Richtung
([Sprachen mit Schreibrichtung von rechts nach links in der Administration](../../../../eng-us/features/6.0/right-to-left.md)).

## Inhalt

1. [Zu einer Oberfläche von rechts nach links wechseln](#zu-einer-oberfläche-von-rechts-nach-links-wechseln)
2. [Was sich ändert](#was-sich-ändert)
3. [Inhalte von rechts nach links bearbeiten](#inhalte-von-rechts-nach-links-bearbeiten)
4. [Bekannte offene Punkte](#bekannte-offene-punkte)

## Zu einer Oberfläche von rechts nach links wechseln

In der rechten Werkzeugleiste enthält der Kasten **Oberfläche** die Liste **Sprache der Oberfläche**; wählen Sie zum
Beispiel *עברית (ישראל)* (Hebräisch, `heb-IL`) und klicken Sie auf **Anwenden** ([Sprachen (für Redakteure)](../../sprachen.md)).
Die Wahl gilt bis zur Abmeldung. Die hebräische Übersetzung der Administration ist vollständig und maschinell
unterstützt; Arabisch (`ara-SA`) ist neu und ebenfalls maschinell unterstützt; für beide ist eine Durchsicht durch
Muttersprachler willkommen ([Sprachen in Exponential, Abschnitt 4](../../../../eng-us/languages.md#4-every-administration-translation)).

## Was sich ändert

| Teil | Verhalten |
|---|---|
| Seitenrahmen | `<html dir="rtl" lang="he-IL">`; der Inhaltsbaum rechts, die Werkzeugleiste links |
| Layout | admin4, admin4l, das klassische Design und AdminUI spiegeln sich über logische CSS-Eigenschaften |
| Symbole | Winkel, Pfeile und Caret-Zeichen, die entlang der Zeile zeigen, werden gespiegelt |
| Namen | ein von einem Redakteur eingegebener Name (einer Seite, eines Benutzers) wird isoliert, sodass ein englischer Name auf einer hebräischen Seite seine Satzzeichen behält |
| Von Redakteuren gewählte Ausrichtung von Inhalten | nicht gespiegelt: „rechtsbündig“ heißt in jeder Sprache rechts |

## Inhalte von rechts nach links bearbeiten

Unabhängig von der Oberflächensprache trägt Text in einer Inhaltssprache mit Schreibrichtung von rechts nach links in
den Bearbeitungsformularen, der Vollansicht und der Vorschau `lang` und `dir="rtl"`; die Online-Editoren (TinyMCE und
der tiptap-Editor) stellen ihre Schreibrichtung um. So kann eine französischsprachige Redakteurin eine arabische
Übersetzung bearbeiten und sie in der richtigen Richtung sehen.

## Bekannte offene Punkte

Aus [Sprachen mit Schreibrichtung von rechts nach links in der Administration](../../../../eng-us/features/6.0/right-to-left.md#follow-ups):

- Einige Stylesheets von Erweiterungen verwenden stellenweise noch physisches Links und Rechts.
- Die Anmeldeseite folgt nur der eigenen Locale des Siteaccess; eine Anmeldeseite von rechts nach links braucht einen
  Siteaccess, dessen Locale von rechts nach links geschrieben wird.
- Einige Modulseiten und unübersetzter englischer Text innerhalb einer Seite von rechts nach links folgen dem
  einfachen bidirektionalen Unicode-Algorithmus.

## Verwandte Seiten

- [Rechts-nach-links-Sprachen auf öffentlichen Websites](rechts-nach-links-sprachen-auf-oeffentlichen-websites.md)
- [Sprachen der Administrationsoberfläche](sprachen-der-administrationsoberflaeche.md)
- [Liste der Oberflächensprachen](../../../../eng-us/features/6.0/ui-switch-languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
