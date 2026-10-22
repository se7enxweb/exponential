---
title: "Ihr erster Tag als Theme-Designer"
audience: theme-designer
category: theme-design
order: 2
summary: Ein Plan für den ersten Arbeitstag eines Theme-Designers, der neu bei Exponential ist - eine echte Website mit dem richtigen Blick ansehen, finden, welches Template was gezeichnet hat, die Inhaltsklassen sammeln und mit einer Liste dessen gehen, was zu gestalten ist.
keywords: Theme-Designer, erster Tag, Einarbeitung, Einstieg, Kommentare mit Template-Pfaden, Inhaltsklassen, Designinventar, Demo-Website, Plan, theme designer, first day, onboarding, getting started, template path comments, content classes, design inventory, demo site, plan
type: help
---
# Ihr erster Tag als Theme-Designer

Diese Seite ist für Theme-Designer an ihrem ersten Tag mit Exponential, ob Sie in ein Team kommen, das eine Website
betreibt, oder ein Kundenprojekt übernommen haben. Sie gibt einen Plan in fünf Blöcken von etwa neunzig Minuten. Am Ende
des Tages wissen Sie, wie die Website aufgebaut ist, die Sie gestalten werden, und haben eine Liste dessen, was zu
zeichnen ist. Ein Theme schreiben Sie heute nicht; das behandelt der
[Plan für die erste Woche für Designer](../designer/designer-hier-beginnen.md#ihre-erste-woche).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Block 1: die Website als Besucher ansehen](#block-1-die-website-als-besucher-ansehen)
3. [Block 2: sie als Redakteur ansehen](#block-2-sie-als-redakteur-ansehen)
4. [Block 3: die Templates hinter den Pixeln finden](#block-3-die-templates-hinter-den-pixeln-finden)
5. [Block 4: das Design, das Sie übernehmen](#block-4-das-design-das-sie-übernehmen)
6. [Block 5: die Liste schreiben](#block-5-die-liste-schreiben)
7. [Was Sie am Ende des Tages haben](#was-sie-am-ende-des-tages-haben)

## Bevor Sie beginnen

Bitten Sie den Entwickler oder Administrator, mit dem Sie arbeiten, um:

- eine **Entwicklungsinstallation**, die Sie kaputt machen dürfen, nie die Live-Website
  ([Ein Design auf einem Siteaccess in der Vorschau ansehen](../designer/theme-design/design-vorschau-auf-einem-siteaccess.md)
  erklärt, wie ein Design neben einem Live-Design ausprobiert wird);
- ein **Redakteurskonto** darauf (ein Testkonto mit einer Adresse unter `example.invalid`, kein gemeinsamer
  Administrator);
- Lesezugriff auf die **Design-Erweiterung** der Website, meist ein Git-Repository
  ([Ein Theme unter Git stellen](../designer/erweiterungen/ein-theme-unter-git-stellen.md)).

## Block 1: die Website als Besucher ansehen

Öffnen Sie die Website im Browser und notieren Sie für jede unterschiedliche Seite, die Sie sehen, um welche Art von
Seite es sich handelt: eine Startseite, ein Artikel, eine Nachrichtenliste, ein Suchergebnis, ein Kontaktformular, eine
Anmeldeseite, eine Seite "nicht gefunden". Probieren Sie es:

1. bei 1440 px Breite, bei 960 px mit 200 % Zoom und bei 375 px;
2. im hellen und im dunklen Farbschema, wenn die Website beide anbietet;
3. nur mit der Tastatur (Tab, Umschalt+Tab, Eingabe).

Notieren Sie, was kaputt oder uneinheitlich aussieht. Diese Notiz ist die erste Eingabe für Ihr Design-Briefing.

## Block 2: sie als Redakteur ansehen

Melden Sie sich mit dem Redakteurskonto in der Administration an und öffnen Sie einige Objekte, die Sie in Block 1
gesehen haben. Notieren Sie für jedes die **Inhaltsklasse** (das Bearbeitungsformular zeigt sie) und welche Felder
ausgefüllt und welche leer sind. Das ist das Material, das Ihr Design aufnehmen muss: siehe
[Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md).

Verwendet die Website Exponential Layouts, öffnen Sie ein Layout im Layout-Editor und sehen Sie sich seine Zonen und
Blöcke an ([Der Layout-Editor](../layout-editor.md)).

## Block 3: die Templates hinter den Pixeln finden

Bitten Sie den Entwickler, auf der Entwicklungsinstallation die **Kommentare mit Template-Pfaden** einzuschalten
(`[TemplateSettings] ShowTemplatePathComments=enabled`). Jedes gerenderte Template wird dann in einen HTML-Kommentar
mit seinem Dateinamen eingeschlossen, sodass "Quelltext anzeigen" oder der Element-Inspektor des Browsers Ihnen sagt,
welches Template ein Stück der Seite gezeichnet hat. [Kommentare mit Template-Pfaden](../designer/vorlagen/template-pfad-kommentare.md)
zeigt, wie Sie sie lesen, und
[Lektion 1.3: Welches Template hat dieses Markup geschrieben?](../trainer/theme-designer/modul-01-designs-und-die-fallback-kette/welches-template-dieses-markup-geschrieben-hat.md)
ist eine kurze Übung. Lassen Sie diese Einstellung in der Produktion nie eingeschaltet.

Wählen Sie drei Seiten und notieren Sie das Pagelayout, das Template der Vollansicht und ein Template der
Zeilenansicht, die jede verwendet.

## Block 4: das Design, das Sie übernehmen

Finden Sie heraus, welche Designs der Siteaccess in welcher Reihenfolge verwendet: [Designs und Siteaccesses](../designer/theme-design/designs-und-siteaccesses.md).
Baut die Website auf einem mitgelieferten Theme auf, machen Sie dessen Rundgang:

| Theme | Rundgang |
|---|---|
| media | [Ein Rundgang durch das Theme media](../designer/theme-design/rundgang-durch-das-media-theme.md) |
| simple | [Ein Rundgang durch das Theme simple](../designer/theme-design/rundgang-durch-das-simple-theme.md) |
| super | [Ein Rundgang durch das Theme super](../designer/theme-design/rundgang-durch-das-super-theme.md) |
| starter | [Ein Rundgang durch das Theme starter](../designer/theme-design/rundgang-durch-das-starter-theme.md) |
| ezwebin | [Das Design ezwebin als Grundlage](../designer/theme-design/ezwebin-als-grundlage.md) |

Öffnen Sie den Ordner der Stylesheets des Designs und suchen Sie die Token-Datei. Gibt es keine, notieren Sie das:
Tokens einzuführen wird Teil Ihrer Arbeit sein ([Design-Tokens](../designer/theme-design/design-tokens.md)).

## Block 5: die Liste schreiben

Schreiben Sie eine Seite in einfachem Text mit vier Listen:

1. **Rahmen:** Kopfbereich, Navigation, Fußbereich, was sich bei kleinen Breiten ändert.
2. **Klassen und Ansichten:** jede Klasse mit den Ansichten, die sie braucht (voll, Zeile, Karte, Einbettung).
3. **Besondere Seiten:** Suche, Anmeldung, Fehler, leere Listen, Formulare.
4. **Blöcke:** jeder Blocktyp und Ansichtstyp, den Redakteure verwenden, wenn die Website Layouts nutzt.

Teilen Sie sie mit Ihrem Entwickler, bevor Sie irgendetwas zeichnen. Sie ist der Umfang Ihres Designs und die erste
Fassung Ihres [Komponenteninventars](komponenteninventar.md).

## Was Sie am Ende des Tages haben

- [ ] Eine Entwicklungsinstallation und ein Redakteurskonto.
- [ ] Funktionierende Kommentare mit Template-Pfaden und drei Seiten, deren Templates Sie zurückverfolgt haben.
- [ ] Die Liste der Designs in der Kaskade des Siteaccess.
- [ ] Eine Liste auf einer Seite mit Rahmen, Klassen und Ansichten, besonderen Seiten und Blöcken.
- [ ] Ein gewählter Lesepfad für die nächsten Tage ([Lesepfade für Theme-Designer](lesepfade-fuer-theme-designer.md)).

## Verwandte Seiten

- [Theme-Designer: hier beginnen](theme-designer-hier-beginnen.md)
- [Wie sich Exponential-Themes unterscheiden](wie-sich-exponential-themes-unterscheiden.md)
- [Ein Design debuggen](../designer/theme-design/ein-design-debuggen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
