---
title: "Barrierefreie statische Websites für öffentliche Informationen"
audience: government, project-manager, admin, editor
category: public-sector
order: 2
summary: Für öffentliche Stellen - öffentliche Informationen als statische Website veröffentlichen, die schnell ist, unter Last stabil bleibt, wenig kostet und sich leicht auf Barrierefreiheit prüfen lässt, während Bearbeitung, Freigabe und Nachweise in Exponential bleiben.
keywords: Verwaltung, öffentlicher Sektor, Kommune, öffentliche Informationen, Barrierefreiheit, BITV, WCAG, Verfügbarkeit, Notfallinformationen, Nachvollziehbarkeit, Statisches Veröffentlichen, government, public sector, accessibility, WCAG, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing
type: help
---
# Barrierefreie statische Websites für öffentliche Informationen

Öffentliche Informationen müssen für alle erreichbar sein, auch bei schlechter Verbindung, auf alten Geräten und an
dem Tag, an dem alle gleichzeitig nachsehen. Eine statische Website passt gut dazu: einfache HTML-Seiten, bei denen
nichts berechnet wird, wenn jemand sie aufruft.

## Was Sie am Ende haben

- Ihre Website mit öffentlichen Informationen als statische Seiten, unter Ihrer eigenen Domain mit HTTPS.
- Bearbeitung, Prüfung und Freigabe in Exponential, mit Rollen und Versionen wie bisher.
- Einen Nachweis jeder Veröffentlichung mit Tag: was wann online war.

## Warum statisch hilft

- **Verfügbarkeit.** Ein statischer Host bewältigt einen plötzlichen Ansturm, ohne langsamer zu werden. Nützlich für
  Notfallhinweise und Wahlergebnisse.
- **Sicherheit.** Die öffentliche Website hat keine Anmeldung, keine Datenbank und kein PHP, das man angreifen kann.
  Die Redaktionsinstallation kann im internen Netz bleiben.
- **Nachvollziehbarkeit.** Jede Veröffentlichung ist ein Commit mit einem Tag `static/<ziel>/<n>`, einer Zeit und der
  Person, die veröffentlicht hat. Rücknahmen sind neue Commits, es wird also nichts gelöscht.
- **Kosten.** Die öffentliche Seite braucht nur einen statischen Host.

## Barrierefreiheit

Statisches Veröffentlichen ändert Ihr HTML nicht: Die Kopie ist das, was Ihre Templates rendern, mit umgeschriebenen Links. Also:

- die Barrierefreiheit der Kopie ist die Barrierefreiheit Ihres Designs; prüfen Sie die Kopie mit Ihren üblichen
  Werkzeugen, in der Vorschau des Builds, bevor Sie veröffentlichen;
- Seitentitel, Sprachattribute, `hreflang`-Alternativen und Überschriften bleiben erhalten;
- die Suchseite, die der Build schreibt, funktioniert mit der Tastatur und ohne Server.

Formulare (Beschwerden, Anträge) brauchen einen Server. Senden Sie sie an die Live-Installation oder verlinken Sie den
Formulardienst, den Sie schon nutzen. Siehe [Suche, Formulare und Kommentare auf einer statischen Site](../statisches-veroeffentlichen/suche-formulare-und-kommentare-auf-einer-statischen-site.md).

## Wahl des Hostings

GitHub Pages ist kostenlos, aber Ihre Vorgaben verlangen vielleicht Hosting im eigenen Land oder auf eigenen Servern.
Statisches Veröffentlichen pusht zu jedem Git-Host, und den Ordner kann jeder Webserver ausliefern.
Siehe [Andere Git-Hosts: GitLab, Gitea und eigener Server](../statisches-veroeffentlichen/andere-git-hosts-gitlab-gitea-und-eigener-server.md).

Bevor Sie irgendetwas veröffentlichen: [Private Inhalte privat halten](../statisches-veroeffentlichen/private-inhalte-privat-halten.md).

## Jetzt Ihre eigene erstellen

Starten Sie einen Pilot mit einer Informations-Website: [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
