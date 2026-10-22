---
title: "Kopf- und Fußbereiche gestalten"
audience: theme-designer
category: theme-design
order: 18
summary: Wie Theme-Designer den Rahmen einer Exponential-Website gestalten - Kopf- und Fußbereich, die das Pagelayout um jede Seite zeichnet, auch um Such-, Anmelde- und Fehlerseiten; was meist hineingehört, der angemeldete Zustand, Umschalter für Sprache und Farbschema und kleine Bildschirme.
keywords: Theme-Designer, Kopfbereich, Fußbereich, Header, Footer, Pagelayout, Rahmen, Logo, Name der Website, Sprunglink, Sprachumschalter, Theme-Umschalter, angemeldet, fixierter Kopfbereich, kleine Bildschirme, theme designer, header, footer, pagelayout, frame, logo, site name, skip link, language switcher, theme switch, signed-in, sticky header, small screens
type: help
---
# Kopf- und Fußbereiche gestalten

Diese Seite ist für Theme-Designer, die den Rahmen einer Website gestalten. In Exponential wird der Rahmen von einem
Template gezeichnet, dem **Pagelayout**, und zwar um das Ergebnis jeder Anfrage: um Inhaltsseiten, aber auch um die
Suche, die Anmeldung, Benutzerseiten und Fehlerseiten. Ihn gut zu gestalten heißt, ihn einmal für alle zu gestalten.
Die Template-Seite steht in [Das Pagelayout](../designer/theme-design/das-pagelayout.md) und
[Variablen des Pagelayouts](../designer/theme-design/pagelayout-variablen.md).

## Inhalt

1. [Der Rahmen ist überall](#der-rahmen-ist-überall)
2. [Was ein Kopfbereich meist enthält](#was-ein-kopfbereich-meist-enthält)
3. [Was ein Fußbereich meist enthält](#was-ein-fußbereich-meist-enthält)
4. [Angemeldet oder nicht](#angemeldet-oder-nicht)
5. [Umschalter: Sprache und Farbschema](#umschalter-sprache-und-farbschema)
6. [Kleine Bildschirme](#kleine-bildschirme)
7. [Fixierte Kopfbereiche](#fixierte-kopfbereiche)
8. [Was Sie übergeben](#was-sie-übergeben)

## Der Rahmen ist überall

Zeichnen Sie den Rahmen mindestens um diese Seiten, nicht nur um einen Artikel:

- eine Inhaltsseite (Vollansicht) und die Startseite;
- die Suchseite mit Ergebnissen und ohne ([Suchergebnisse gestalten](suchergebnisse-gestalten.md));
- die Anmeldeseite ([Anmelde- und Benutzerseiten gestalten](anmelde-und-benutzerseiten-gestalten.md));
- eine Fehlerseite "nicht gefunden" ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md)).

Ist die Startseite ein Layout, das Redakteure bauen, umgibt der Rahmen es trotzdem, und sein erster Block sitzt direkt
unter Ihrem Kopfbereich ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md)).

## Was ein Kopfbereich meist enthält

| Element | Hinweise |
|---|---|
| Sprunglink | erstes fokussierbares Element, sichtbar bei Fokus ([Sprunglinks](../designer/theme-design/sprunglinks.md)) |
| Logo oder Name der Website | verlinkt auf die Startseite; helle und dunkle Version |
| Hauptnavigation | siehe [Navigation und Menüs gestalten](navigation-und-menues-gestalten.md) |
| Suche | ein Feld oder eine Schaltfläche, die eines öffnet |
| Sprachumschalter | auf mehrsprachigen Websites |
| Anmelde- oder Kontolink | wenn Besucher Konten haben |
| Umschalter für das Farbschema | optional |

Halten Sie den Kopfbereich flach. Jedes Element darin erscheint auf jeder Seite, wird mit dem Rahmen gecacht und kostet
auf dem Telefon Platz.

## Was ein Fußbereich meist enthält

Kontaktangaben, nachrangige Links (Datenschutz, Nutzungsbedingungen, Erklärung zur Barrierefreiheit), Profile in
sozialen Netzwerken, eine Anmeldung zum Newsletter, wenn die Website einen hat, die Copyright-Zeile. Der Fußbereich ist
ein guter Ort für Links, die Redakteure ändern müssen: Fragen Sie, ob sie aus Inhalten kommen (ein Ordner mit Links, ein
Menüblock) oder im Template feststehen. Fester Text in einem Template muss trotzdem durch die Übersetzung
([Template-Texte übersetzen](../designer/vorlagen/template-texte-uebersetzen.md)).

## Angemeldet oder nicht

Derselbe Rahmen wird anonymen Besuchern und angemeldeten Benutzern gezeigt. Gestalten Sie beide Zustände: "Anmelden"
gegenüber dem Namen des Benutzers und einem Abmeldelink. Die Teile des Rahmens, die sich pro Benutzer unterscheiden,
müssen pro Benutzer gecacht oder aus dem gemeinsamen Cache herausgehalten werden; das entscheidet der Entwickler
([Benutzer- und Anmelde-Templates](../designer/vorlagen/benutzer-und-anmelde-templates.md#der-angemeldete-zustand-im-rahmen)).
Halten Sie den benutzerabhängigen Teil klein.

## Umschalter: Sprache und Farbschema

**Sprache.** Mitgelieferte Designs bauen den Umschalter mit dem Template-Operator `language_switcher`, der die
Siteaccesses (Sprachen) auflistet, die der Administrator verbunden hat; die Version des Medien-Themes ist
`pagelayout/header/language_selector.tpl`. Gestalten Sie ihn so, dass jede Sprache in ihrer eigenen Sprache geschrieben
ist ("Deutsch", "Français"), die aktuelle Sprache markiert ist und nie nur Flaggen verwendet werden
([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md)).

**Farbschema.** Ein Umschalter zwischen hell, dunkel und System braucht ein kleines Skript und einen Schutz vor dem
ersten Zeichnen, als Datei geladen, damit die Seite nicht aufblitzt
([Der Theme-Umschalter und der Schutz vor dem ersten Zeichnen](../designer/theme-design/theme-umschalter-und-pre-paint-guard.md)).
Gestalten Sie drei Zustände und das Symbol oder die Beschriftung für jeden.

## Kleine Bildschirme

Bei geringen Breiten wird der Kopfbereich meist zu Logo, Menüschaltfläche und Suchschaltfläche. Entscheiden Sie:

- was sichtbar bleibt und was ins Menü wandert;
- ob das Menü als Panel, als Blatt oder bildschirmfüllend öffnet;
- wohin die Links für Sprache und Konto kommen;
- die Fokusreihenfolge, wenn das Menü offen ist ([Tastatur und Fokus](../designer/theme-design/tastatur-und-fokus.md#menüs-aufklappbereiche-und-dialoge)).

Testen Sie bei 375 px und bei 960 px mit 200 % Zoom; vergrößerte Desktop-Browser bekommen oft den Kopfbereich für kleine
Bildschirme.

## Fixierte Kopfbereiche

Ein Kopfbereich, der beim Scrollen sichtbar bleibt, nimmt auf jeder Seite Platz vom Inhalt weg, bei 200 % Zoom noch
mehr, und kann das Element verdecken, das den Tastaturfokus hat. Wenn Sie einen möchten, halten Sie ihn kompakt, lassen
Sie ihn bei geringen Höhen einklappen, und bitten Sie den Entwickler, `scroll-padding-top` zu setzen, damit fokussierte
Elemente und Sprungziele nicht darunter verschwinden.

## Was Sie übergeben

- Kopf- und Fußbereich in drei Breiten, hell und dunkel, anonym und angemeldet.
- Das geöffnete Menü für kleine Bildschirme.
- Den Rahmen um eine Such-, eine Anmelde- und eine Fehlerseite.
- Eine Liste, welche Elemente aus Inhalten kommen und welche fester Text sind.

## Verwandte Seiten

- [Das Pagelayout](../designer/theme-design/das-pagelayout.md)
- [Landmarks und Überschriften](../designer/theme-design/landmarks-und-ueberschriften.md)
- [Favicons und Touch-Icons](../designer/theme-design/favicons-und-touch-icons.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
