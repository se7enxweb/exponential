---
title: "Was statisches Veröffentlichen ist"
audience: admin, website-creator, editor, non-technical, non-profit, local-business, individual, student
category: administration
order: 3
summary: Statisches Veröffentlichen in einfachen Worten - eine Kopie Ihrer Website als gewöhnliche Dateien, kostenlos ausgeliefert, während das Bearbeiten in Exponential bleibt; wie es sich vom statischen Cache unterscheidet und was Besucher sehen.
keywords: Statisches Veröffentlichen, was ist das, statische Website, statische Kopie, einfaches HTML, kostenloses Hosting, statischer Cache, Unterschied, static publishing, what is static publishing, static site, static copy, free hosting, free website, GitHub Pages, github.io, static cache, Jekyll alternative, Netlify, Jamstack, deploy
featured: true
type: help
---
# Was statisches Veröffentlichen ist

## Was Sie am Ende haben

Ein klares Bild davon, was statisches Veröffentlichen tut, was es nicht tut, und ob Sie weiterlesen sollten.

## In einem Satz

Statisches Veröffentlichen macht eine Kopie Ihrer Website als gewöhnliche Dateien (HTML-Seiten, Bilder, Stylesheets)
und legt diese Kopie dort ab, wo Dateien kostenlos ausgeliefert werden, etwa bei GitHub Pages.

## So funktioniert es

1. Sie und Ihre Redaktion arbeiten wie immer in Exponential: schreiben, prüfen, veröffentlichen.
2. Soll sich die öffentliche Website ändern, **baut** Exponential eine Kopie: Es besucht jede Seite so, wie ein nicht
   angemeldeter Besucher sie sieht, speichert, was es sieht, und passt die Links so an, dass sie ohne Exponential
   funktionieren.
3. Sie sehen sich die Kopie an und **veröffentlichen** sie. Die Kopie geht in ein Git-Repository; GitHub Pages (oder ein
   anderer Host) liefert sie aus.

Hinter der öffentlichen Kopie stehen keine Datenbank und kein PHP. Es gibt dort nichts zu aktualisieren, zu flicken
oder einzubrechen, sie lädt schnell aus dem Netz von GitHub und das Hosting kostet nichts.

## Was Besucher sehen

Dieselben Seiten, Menüs, Layouts, Bilder und Downloads wie auf Ihrer Website. Was einen Server zum Antworten braucht,
ändert sich:

| Auf Ihrer Website | Auf der statischen Kopie |
|---|---|
| Seiten, Bilder, Galerien, Downloads | dasselbe |
| Suche | eine Suchseite, die im Browser läuft, wenn das Ziel sie eingeschaltet hat |
| Kontaktformulare | an Ihre Live-Website gesendet, oder ein kurzer Hinweis mit einem Link dorthin |
| Anmeldung, Kommentare, Warenkorb | ausgelassen, mit einem kurzen Hinweis |

Die Einzelheiten: [Suche, Formulare und Kommentare auf einer statischen Site](suche-formulare-und-kommentare-auf-einer-statischen-site.md).

## Statisches Veröffentlichen und der statische Cache

Exponential hat seit langem einen **statischen Cache**. Beide schreiben Seiten in Dateien, dienen aber
unterschiedlichen Zwecken:

| | Statischer Cache | Statisches Veröffentlichen |
|---|---|---|
| Wo die Dateien liegen | auf Ihrem eigenen Server, neben Exponential | in einem Repository, ausgeliefert von einem anderen Host |
| Wer sie ausliefert | Ihr Webserver, für den Rest mit PHP | allein der statische Host |
| Zweck | Ihre eigene Website schneller machen | eine öffentliche Kopie, die keinen Server von Ihnen braucht |
| Links | wie auf Ihrer Website | so umgeschrieben, dass sie für sich allein funktionieren, unter jeder Adresse |
| Verlauf | keiner | jede Veröffentlichung bleibt, Zurücknehmen in einem Schritt |

Sie können beides nutzen. Der Wechsel: [Vom statischen Cache zum statischen Veröffentlichen](../../anleitungen/vom-statischen-cache-zum-statischen-veroeffentlichen.md).

## Nächster Schritt

[Passt statisch zu meiner Website?](passt-statisch-zu-meiner-website.md), dann
[In 15 Minuten eine kostenlose Website](in-15-minuten-eine-kostenlose-website.md).
