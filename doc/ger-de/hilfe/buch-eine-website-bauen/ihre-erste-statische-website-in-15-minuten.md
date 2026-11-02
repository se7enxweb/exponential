---
title: "Ihre erste statische Website in 15 Minuten: die Vereinswebsite auf GitHub Pages"
audience: builder, admin
category: books
order: 180
summary: Das letzte Kapitel - die fertige Vereinswebsite als kostenlose statische Kopie auf GitHub Pages, mit dem Einrichtungsassistenten, einem Build, einer Prüfung und einer Veröffentlichung, und was sich für das Gästebuch und die Mitgliederseite ändert.
keywords: Statisches Veröffentlichen, kostenloses Hosting, kostenlose Website, Vereinswebsite, veröffentlichen, Tutorial, static publishing, GitHub Pages, github.io, free hosting, free website, static site, club site, gh-pages, Building book
type: help
---
# Ihre erste statische Website in 15 Minuten: die Vereinswebsite auf GitHub Pages

Die Vereinswebsite ist fertig. Der Verein hat kein Geld für Hosting, und der Server, auf dem sie läuft, steht im Büro
eines Mitglieds. Dieses Kapitel legt eine Kopie der öffentlichen Website kostenlos auf GitHub Pages, während der
Verein auf diesem Server weiter bearbeitet.

## Was Sie am Ende haben

Die Vereinswebsite unter `https://<konto>.github.io/chess-club/`, veröffentlicht unter **Setup > Statisches
Veröffentlichen**, und einen Weg, sie nach jeder Nachricht erneut zu veröffentlichen.

## Was bleibt und was sich ändert

| Seite der Vereinswebsite | Auf der statischen Kopie |
|---|---|
| Startseite, Nachrichten, Archiv, Mitgliederliste, Linkbaum | dasselbe |
| Gästebucheinträge | so, wie sie bei der letzten Veröffentlichung waren |
| Gästebuchformular | sendet an die Live-Website, oder ein kurzer Hinweis mit Link (Schritt 7 des Assistenten, **Formulare**) |
| Anmeldung der Mitglieder | ausgelassen |

Das Gästebuch funktioniert weiter auf dem Server des Vereins; freigegebene Einträge erreichen die Kopie mit der nächsten
Veröffentlichung. Das ist ein gutes Beispiel für die Aufteilung, die statisches Veröffentlichen vornimmt: Lesen ist
öffentlich und kostenlos, Schreiben bleibt bei Exponential.

## Die Schritte

Folgen Sie [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md)
mit diesen Werten:

1. Repository `chess-club` auf GitHub, öffentlich, leer; ein Fine-grained Token dafür unter **Setup > Geheimnisse** als
   `github_chess_club`.
2. Assistent: Id `club`, der Siteaccess des Vereins, GitHub Pages, das Token, Branch `gh-pages`, relative Links,
   öffentliche Adresse `https://<konto>.github.io/chess-club/`.
3. Den Mitgliederbereich **auslassen**, wenn er Seiten hat, die nur Mitglieder sehen sollen: Pfad
   `/members/private/*`. Seiten, die Anonymous nicht lesen darf, sind ohnehin nie in der Kopie.
4. Bauen, die Vorschau öffnen, sich durch die Nachrichten und den Linkbaum klicken, veröffentlichen.

![Die Vorschau: was sich ändert, die Größe gegenüber den Grenzen und die Schaltflächen Veröffentlichen und Verwerfen](../bilder/statisches-veroeffentlichen/preview-light.webp)

## Nach jeder Nachricht

Wieder **Bauen** und **Veröffentlichen** wählen, oder die Website sich beim Veröffentlichen von Inhalten selbst
veröffentlichen lassen ([Automatisch veröffentlichen](../statisches-veroeffentlichen/automatisch-veroeffentlichen.md)).
Jede Veröffentlichung bleibt erhalten, ein Fehler ist also nur eine Rücknahme entfernt.

## Nächster Schritt

Zurück zum [Anfang des Buchs](README.md) oder das ganze [Handbuch zum statischen Veröffentlichen](../statisches-veroeffentlichen/README.md).
