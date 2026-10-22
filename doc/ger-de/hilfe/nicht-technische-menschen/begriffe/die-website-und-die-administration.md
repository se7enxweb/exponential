---
title: Die Website und die Administration
audience: non-technical, editor, user
category: plain-language
order: 13
summary: Eine Exponential-Installation mit mehreren Türen - die öffentliche Website, die Administration, oft ein einfacherer Editor, manchmal eine zweite Sprache oder eine zweite Website -, wofür jede Tür da ist, warum die Anmeldung an einer Sie nicht an einer anderen anmeldet und wie Sie Ihre Adressen finden.
keywords: Website, Administration, Admin, Editor, Siteaccess, Adresse, URL, anmelden, Frontend, Backend, Multisite, Sprachversion, erklärt, site, administration, admin, editor, address, sign in, front end, back end, multisite, language site, explained
type: help
---
# Die Website und die Administration

## Inhalt

1. [Ein Gebäude, mehrere Türen](#ein-gebäude-mehrere-türen)
2. [Die Türen, denen Sie wahrscheinlich begegnen](#die-türen-denen-sie-wahrscheinlich-begegnen)
3. [Die Anmeldung gilt pro Tür](#die-anmeldung-gilt-pro-tür)
4. [Schreiben Sie Ihre Adressen auf](#schreiben-sie-ihre-adressen-auf)
5. [Häufige Überraschungen](#häufige-überraschungen)

## Ein Gebäude, mehrere Türen

Stellen Sie sich einen Laden mit einer Vordertür für Kunden und einer Hintertür für das Personal vor. Es ist ein
Gebäude mit einem Lager: Was das Personal durch die Hintertür in die Regale stellt, sehen die Kunden durch die
Vordertür. Eine Exponential-Installation ist genauso. Die **Website** ist die Vordertür. Die **Administration** ist der
Personaleingang. Beide zeigen dieselben Inhalte, die einmal gespeichert sind. Technisch heißt jede Tür **Siteaccess**;
das Wort brauchen Sie nur im Gespräch mit Ihrem Entwickler.

## Die Türen, denen Sie wahrscheinlich begegnen

| Tür | Für | Übliche Adresse |
|---|---|---|
| **Die Website** | Besucher | `https://www.example.com` |
| **Die Administration** | Administratoren und Redakteure: jedes Werkzeug, das Ihre Rolle erlaubt | `https://www.example.com/admin` ([Anmelden in der Administration](../../anmelden-in-der-administration.md)) |
| **Der Editor** | Nur Redakteure: eine einfachere Administration mit nur Dashboard, Inhalten und Medienbibliothek | `https://www.example.com/editor` oder `https://edit.example.com` ([Anmelden im Editor](../../anmelden-im-editor.md)) |
| **Eine Sprachversion** | Besucher in einer anderen Sprache | `https://www.example.com/de` oder ein eigener Hostname ([Sprachen](../../sprachen.md)) |
| **Eine zweite Website** | Eine zweite Marke oder ein zweites Projekt in derselben Installation | ein eigener Hostname ([Eine Website hinzufügen](../../kunden/wachstum/eine-website-hinzufuegen.md)) |

Welche davon Ihre Website hat und wie die genauen Adressen lauten, hängt davon ab, wie sie eingerichtet wurde. Ihr
Administrator weiß es.

## Die Anmeldung gilt pro Tür

Eine Anmeldung gehört zu einem Browser und zu einer Tür. Wer im Editor angemeldet ist, ist dadurch nicht in der
Administration oder auf der öffentlichen Website angemeldet, und umgekehrt
([Angemeldet bleiben und abmelden](../../anmelden-im-editor.md#angemeldet-bleiben-und-abmelden)). Das ist Absicht:
So bleibt der Personaleingang getrennt.

Auf der öffentlichen Website lassen manche Websites Besucher sich als **Mitglieder** registrieren und anmelden, zum
Beispiel für einen Bereich nur für Mitglieder. Das ist eine andere Art von Konto mit einer anderen Rolle
([Rollen und Rechte erklärt](rollen-und-rechte-erklaert.md)).

## Schreiben Sie Ihre Adressen auf

Füllen Sie das einmal aus und bewahren Sie es bei Ihren Passwörtern auf (nie die Passwörter selbst auf Papier):

| | Adresse |
|---|---|
| Öffentliche Website | |
| Administration | |
| Editor (falls vorhanden) | |
| Andere Sprachen oder Websites | |
| Wo Sie ein Problem melden | |

Die [Checkliste für den ersten Tag](../checklisten/der-erste-tag.md) enthält diesen Schritt.

## Häufige Überraschungen

| Überraschung | Erklärung |
|---|---|
| Ein kleines Browserfenster fragt nach einem Benutzernamen, bevor die Website erscheint | Manche Testserver haben ein zusätzliches Browser-Passwort. Es ist nicht Ihr Exponential-Konto ([Wenn die Anmeldung nicht funktioniert](../../anmelden-im-editor.md#wenn-die-anmeldung-nicht-funktioniert)). |
| Ich sehe weniger Reiter als meine Kollegin | Ihre Rolle erlaubt weniger Werkzeuge, oder Sie verwenden die Tür Editor ([Rollen und Rechte erklärt](rollen-und-rechte-erklaert.md)). |
| Meine Änderung ist in der Administration, aber nicht auf der Website | Noch nicht veröffentlicht, oder ein Cache ([Entwürfe, Versionen und Veröffentlichen erklärt](entwuerfe-versionen-und-veroeffentlichen-erklaert.md), [Caches erklärt](caches-erklaert.md)). |
| Ein gelbes Banner **Demo-Modus** | Eine öffentliche Testwebsite; geben Sie nie etwas Privates ein ([Demomodus](../../demomodus.md)). |

## Verwandte Seiten

- [Begriffe erklärt](README.md)
- [Die erste Anmeldung](../erste-schritte/die-erste-anmeldung.md)
- [Ein Rundgang durch die Administration](../../rundgang-durch-die-administration.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
