---
title: "Die Hilfe aktuell halten: Prüfungen, E-Books und DITA"
audience: editor, admin
category: publishing
order: 40
summary: Die Liste der Hilfethemen mit überfälliger Prüfung, die E-Books, die beim Veröffentlichen eines Themas neu erstellt werden, und der DITA-Export der Hilfe.
keywords: Hilfethema, Prüfung, Prüfdatum, überfällig, veröffentlichen, E-Book, EPUB, PDF, DITA, Export, Handbuch, help topic, review, release, manual
context: exphelp/dashboard, exphelp/manual
type: help
---
# Die Hilfe aktuell halten: Prüfungen, E-Books und DITA

Diese Seite ist für die Menschen, die die Hilfe einer Website schreiben und veröffentlichen: Hilfe-Redakteure und
Release-Verantwortliche. Die Hilfethemen und Handbücher sind Inhalte der Website, geschrieben in der
Administrationsoberfläche und auf **Entwurf**, **Prüfung** und **Veröffentlicht** gesetzt (siehe
[Hilfe als Inhalt gepflegt](../../eng-us/features/6.0/help-content.md)). Drei Dinge helfen, sie richtig zu halten.

## Themen mit überfälliger Prüfung

Jedes Thema hat ein **Prüfdatum**: den Tag, an dem jemand es erneut lesen und prüfen sollte, ob es noch zur Website
passt. Ein Thema kann auch zu lange in **Prüfung** warten. Die Startseite der Hilfe (**Hilfe** in der oberen Leiste)
führt beides unter **Hilfethemen mit überfälliger Prüfung** auf:

- **Prüfdatum überschritten**: Das Prüfdatum des Themas ist vorbei, gleich in welchem Status es ist.
- **Seit ... Tagen ohne Änderung in Prüfung**: Das Thema ist länger in Prüfung, als die Website erlaubt (14 Tage, sofern
  Ihr Administrator das nicht geändert hat), und niemand hat es seitdem geändert.

Sie sehen ein Thema in der Liste nur, wenn Sie etwas dagegen tun können. Als Hilfe-Redakteur sehen Sie die Themen in
Prüfung. Als Release-Verantwortlicher sehen Sie außerdem die veröffentlichten Themen, deren Prüfdatum überschritten ist.
Wenn nichts überfällig ist, wird die Liste gar nicht angezeigt.

So kümmern Sie sich um ein Thema:

1. Klicken Sie neben dem Thema auf **Bearbeiten** oder **Status setzen**.
   Das Thema öffnet sich in der Administrationsoberfläche.
2. Ist das Thema veröffentlicht, setzen Sie seinen Status zurück auf **Prüfung** (nur Release-Verantwortliche).
3. Bearbeiten Sie das Thema: Prüfen Sie den Text an der Website und setzen Sie ein neues **Prüfdatum**.
4. Geben Sie es weiter: Ein Release-Verantwortlicher setzt es wieder auf **Veröffentlicht**.

Wer eine Änderung geschrieben hat, ist nicht derjenige, der sie veröffentlicht.

## E-Books der Handbücher

Jedes Handbuch der Hilfe ist auch ein E-Book, als EPUB und PDF. Sie finden die E-Books auf der Seite des Handbuchs in
der Hilfe unter **E-Books**: die Datei, ihre Version, ihre Größe und wann sie zuletzt erstellt wurde.

Sie müssen sie nicht erstellen. Jedes Mal, wenn ein Thema, Kapitel oder Handbuch auf **Veröffentlicht** gesetzt wird,
werden die E-Books jedes Handbuchs, das es enthält, neu erstellt. Das neue Buch wird als neue Version derselben Datei
gespeichert, sodass die früheren Ausgaben in ihrer Versionshistorie bleiben. Ein Buch eines Handbuchs für die
Öffentlichkeit enthält nur öffentliche Themen, gleich, wer veröffentlicht.

**EPUB herunterladen** und **PDF herunterladen** neben dem Titel des Handbuchs erstellen ein Buch sofort, nur mit dem,
was Sie lesen dürfen. Die gespeicherten E-Books unter **E-Books** sind die Ausgaben für alle, die das Handbuch lesen
dürfen.

## Die Hilfe als DITA

DITA ist das Standardformat für technische Dokumentation. Andere Dokumentationswerkzeuge, Übersetzungsdienste und
Druckereien können es lesen.

- **DITA herunterladen** auf der Seite eines Handbuchs liefert eine ZIP-Datei mit dem Handbuch und seinen
  veröffentlichten Themen.
- **Alles als DITA herunterladen** in der Liste der Handbücher liefert jedes veröffentlichte Handbuch, jedes Thema und
  jeden Glossareintrag.

In einem Download sind nur veröffentlichte Themen, die Sie lesen dürfen. Ihr Administrator kann den Export auch in ein
Verzeichnis des Servers schreiben oder in ein Git-Repository, das die Historie der Hilfe aufbewahrt, mit
`./console exp:help:content dita`.

## Mehr erfahren

- [Hilfe als Inhalt gepflegt](../../eng-us/features/6.0/help-content.md)
- [Der Stilleitfaden der Hilfe](../spezifikationen/6.0/hilfe-stilrichtlinien.md)
- [Veröffentlichen, Versionen und der Papierkorb](veroeffentlichen-und-versionen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
