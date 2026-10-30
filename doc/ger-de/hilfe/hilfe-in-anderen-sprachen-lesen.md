---
title: Die Hilfe in Ihrer Sprache lesen
audience: editor, user, admin
category: languages
order: 20
summary: Die Hilfe wird in der Sprache der Administration angezeigt. Wechseln Sie die Sprache der Oberfläche, um sie in einer anderen zu lesen; eine Seite, die noch nicht übersetzt ist, wird auf Englisch angezeigt, mit einem Hinweis.
keywords: Hilfe, Sprache, Übersetzung, Deutsch, Englisch, Sprache der Oberfläche, nicht übersetzt, Dokumentationssprache, Lesesprache, help, language, translation, German, English, interface language, not translated, documentation language
type: help
---
# Die Hilfe in Ihrer Sprache lesen

Diese Seite ist für alle, die die Hilfe der Administration lesen und sie lieber in ihrer eigenen Sprache lesen
möchten.

## Die Hilfe folgt der Sprache der Administration

Die Hilfe wird in der Sprache angezeigt, in der die Administration angezeigt wird. Sie wählen keine eigene Sprache für
die Hilfe: Wechseln Sie die Sprache der Oberfläche, und die Hilfe wechselt mit.

1. Öffnen Sie die Auswahl der Sprache der Oberfläche in der oberen Leiste der Administration (siehe
   [Sprachen](sprachen.md), falls Ihre Administration sie nicht anzeigt).
2. Wählen Sie eine Sprache, zum Beispiel Deutsch.
3. Öffnen Sie die Hilfe, oder laden Sie die Hilfeseite neu, auf der Sie sich befinden.

Die Seite, auf der Sie sich befinden, bleibt geöffnet: Jede Hilfeseite hat in jeder Sprache dieselbe Adresse, Sie
sehen also dieselbe Seite, jetzt in der Sprache, die Sie gewählt haben.

## Die Sprache in der Hilfe selbst wechseln

Die Startseite der Hilfe und jede Hilfeseite zeigen neben der Suche die Sprache, in der Sie lesen:
„Lesesprache: English / Deutsch“. Die Sprache, in der Sie lesen, ist hervorgehoben; wählen Sie eine andere, um zu
wechseln. Es ist dieselbe Auswahl wie die Sprache der Oberfläche in der oberen Leiste: Die ganze Administration
wechselt in diese Sprache, und Sie bleiben auf der Seite, die Sie gerade lesen.

Angeboten werden nur die Sprachen, die Hilfeseiten haben. Neben der Auswahl sehen Sie, wie viel der Hilfe übersetzt
ist, zum Beispiel „Deutsch: 120 von 7.265 Seiten“. Darf Ihr Konto die Sprache der Oberfläche nicht ändern, zeigt die
Hilfe die Sprache, in der Sie lesen, ohne die Auswahl.

## Das englische Original

Eine übersetzte Seite hat über dem Text einen Link auf ihr englisches Original („Original auf Englisch lesen“). Das
Original öffnet sich unter derselben Adresse, gefolgt von `/(lang)/eng-us`; es wird für diesen einen Aufruf auf
Englisch angezeigt, mit einem Link zurück zur Seite in Ihrer Sprache. Die Sprache Ihrer Oberfläche ändert sich dabei
nicht. (Der ältere Schlüssel `/(lang)/en-us` funktioniert weiterhin; ein `?lang=` in der Adresse wird nicht gelesen.)

## Eine Seite, die noch nicht übersetzt ist

Die Dokumentation wird zuerst auf Englisch geschrieben und Seite für Seite übersetzt. Hat eine Seite noch keine
Übersetzung in Ihrer Sprache, zeigt die Hilfe die englische Seite und weist darüber darauf hin, zum Beispiel:

> Diese Seite ist noch nicht auf Deutsch übersetzt. Sie wird auf Englisch angezeigt. Original auf Englisch öffnen

Alles andere funktioniert wie gewohnt: die Suche, der Navigationsbaum und die Links. Die Änderungsprotokolle sind
immer auf Englisch; sie werden nicht übersetzt.

## In Ihrer Sprache suchen

Die Suche durchsucht die Seiten in Ihrer Sprache und die englischen Seiten, die noch nicht übersetzt sind. Das
englische Original einer übersetzten Seite zeigt sie nicht an, so erscheint jede Seite einmal, in der besten Sprache,
die es für Sie gibt.

## Welche Sprachen es gibt

Englisch ist das Original. Deutsch wird gerade übersetzt. Ihr Administrator kann in den Einstellungen eine Sprache
hinzufügen (`exphelp.ini`, `[DocLanguages]`); Entwickler und Übersetzer finden unter
[Übersetzte Dokumentation](../bc/6.0/uebersetzte-dokumentation.md), wie das geht.

In den Dateien der Dokumentation ist jede Sprache ein Verzeichnis, benannt nach dem Code, den Exponential für die
Sprache verwendet, in Kleinbuchstaben: `doc/eng-us/` ist das englische Original (`eng-US`), `doc/ger-de/` die deutsche
Übersetzung (`ger-DE`). Eine neue Sprache bekommt ihr Verzeichnis auf dieselbe Weise, zum Beispiel `doc/fre-fr/` für
Französisch (`fre-FR`). Diese Namen brauchen Sie nicht, um die Hilfe zu lesen: Jede Hilfeadresse ist in jeder Sprache
dieselbe, und Links und Lesezeichen aus der Zeit, bevor die Verzeichnisse umbenannt wurden, öffnen weiterhin die
richtige Seite.

## Siehe auch

- [Sprachen](sprachen.md): Inhaltssprachen und die Sprache der Oberfläche
- [Suchen](suchen.md)
- [Erste Schritte](erste-schritte.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
