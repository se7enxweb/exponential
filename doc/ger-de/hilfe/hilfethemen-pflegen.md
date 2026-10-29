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
[Hilfe als Inhalt gepflegt](../funktionen/6.0/hilfeinhalte.md)). Drei Dinge helfen, sie richtig zu halten.

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

## Ein Thema zur Prüfung geben und freigeben

Unter jedem Thema in der Hilfe zeigt die Karte **Prüfung und Freigabe**, wo das Thema steht und was Sie als Nächstes tun
können. Sie sehen sie, wenn Sie das Thema bearbeiten oder seinen Status ändern dürfen.

1. Öffnen Sie das Thema in der Hilfe.
2. Lesen Sie **Prüfung nach dem Styleguide**. Dort steht, was die Prüfung des Styleguides in diesem Thema findet: Ein
   Fehler hält die Prüfung der Website auf, eine Warnung oder ein Hinweis ist ein Hinweis für Sie und die Prüfenden.
3. Klicken Sie auf **Zur Prüfung geben**, wenn das Thema fertig ist.
   Die Karte meldet, dass das Thema jetzt in Prüfung ist.

Ein Release-Verantwortlicher öffnet das Thema dann und klickt auf **Freigeben**, oder auf **Zurück zum Entwurf**, um es
zurückzugeben. Ein freigegebenes Thema geht mit **Zurück in Prüfung** zurück, bevor jemand es wieder ändert.

Wenn Sie die aktuelle Version eines Themas geschrieben haben, zeigt die Karte keine Schaltfläche **Freigeben**, sondern
den Grund: Jemand anderes mit der Freigaberolle gibt es frei. Dieselbe Regel gilt im Reiter **Objektstatus** der
Administrationsoberfläche.

## Was Leser sagen: War das hilfreich?

Unter jedem freigegebenen Thema beantworten Leser **War das hilfreich?** mit **Ja**, oder mit **Nein** und einem Grund
(veraltet, schwer zu verstehen, es fehlt etwas, falsch, ein anderer Grund). Gespeichert wird nur die Antwort, nie, wer
sie gegeben hat. Die Karte **Prüfung und Freigabe** zeigt, wie oft Leser Ja und Nein geantwortet haben, und die
genannten Gründe. Ein Thema mit vielen Antworten **Nein** ist ein guter Kandidat für die nächste Prüfung.

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

## Handbücher aus den Seiten der Hilfe

Die meisten Handbücher in der Liste der Handbücher entstehen aus den Seiten dieser Hilfe: ein Handbuch für jede
Gruppe von Seiten (für Redakteure, für Administratoren, für die Presse ...), eines für das Benutzerhandbuch, eines für
jedes Buch und je eines für die Installationsanleitung, die Anleitungen, die Funktionen, die Hinweise für
Mitwirkende, die Standards und die Spezifikationen. Jeder Ordner mit Seiten ist ein Kapitel, jede Seite ein Thema.
Wo eine Seite ins Deutsche übersetzt ist, hat das Thema die deutsche Übersetzung, und das deutsche E-Book des
Handbuchs verwendet sie.

Diese Handbücher werden jedes Mal neu aus den Seiten erstellt, wenn eine neue Version von Exponential installiert
wird. Deshalb gilt:

- **Korrigieren Sie diese Themen nicht in der Administrationsoberfläche.** Eine Änderung dort wird bei der nächsten
  Aktualisierung durch die Seite ersetzt. Korrigieren Sie stattdessen die Seite, oder melden Sie den Fehler (**Fehler melden** in der
  Hilfe).
- Sie erkennen sie an ihrer Remote-ID, die mit `help-gen-` beginnt, und an ihrer Hilfe-ID, die mit `doc.` beginnt
  (`doc.help.editors.publishing` ist die Seite `help/editors/publishing.md`).
- Themen, Kapitel und Handbücher, die Sie selbst schreiben, ändert die Aktualisierung nie.
- Auf einer neuen Site werden die Handbücher in der ersten halben Stunde im Hintergrund erstellt. Währenddessen zeigt
  die Liste der Handbücher **Handbücher werden erstellt (n von 52)**.

Ihr Administrator führt die Aktualisierung mit `./console exp:help:content build-manuals --apply -s admin --release` aus und
erstellt die E-Books mit `./console exp:help:content ebooks --apply -s admin` (mit `--language=ger-DE` die deutsche Ausgabe).
Siehe [Hilfe als Inhalt gepflegt](../funktionen/6.0/hilfeinhalte.md).

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

- [Hilfe als Inhalt gepflegt](../funktionen/6.0/hilfeinhalte.md)
- [Der Stilleitfaden der Hilfe](../spezifikationen/6.0/hilfe-stilrichtlinien.md)
- [Veröffentlichen, Versionen und der Papierkorb](veroeffentlichen-und-versionen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
