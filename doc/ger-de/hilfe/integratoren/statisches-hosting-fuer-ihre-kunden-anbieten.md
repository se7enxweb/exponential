---
title: "Statisches Hosting für Ihre Kunden anbieten"
audience: integrator, developer, admin
category: integration
order: 551
summary: Für Integratoren und Wiederverkäufer - nehmen Sie statisches Veröffentlichen in Ihr Angebot auf, mit GitHub Pages oder Ihrem eigenen Git-Host, einem Ziel pro Kunde, Zugangsdaten pro Repository und einer klaren Linie zwischen dem, was eine statische Kopie kann und was nicht.
keywords: Integrator, Wiederverkäufer, Agentur, Dienstleistung, Managed Hosting, statisches Hosting, kostenloses Hosting, statische Website, Statisches Veröffentlichen, integrator, reseller, static hosting, free hosting, static site, GitHub Pages, gh-pages, GitLab Pages, Gitea, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, multisite
type: help
---
# Statisches Hosting für Ihre Kunden anbieten

Statisches Veröffentlichen ist in Exponential 6.0.15 eingebaut, Sie können es also ohne neue Software anbieten: Der
Kunde bearbeitet in Exponential, und die öffentliche Website ist eine statische Kopie auf GitHub Pages, auf den Pages
eines anderen Git-Hosts oder auf einem einfachen Webserver, den Sie betreiben.

## Was Sie am Ende haben

- Eine Dienstleistung, die Sie in einem Satz beschreiben können: „Bearbeiten in Exponential, veröffentlicht als schnelle statische Website.“
- Eine wiederholbare Einrichtung: ein Ziel pro Kunden-Site, ein Repository und eine Zugangsangabe pro Ziel.

## Drei Arten, es anzubieten

| Angebot | Wo die Kopie liegt | Wer zahlt |
|---|---|---|
| Kostenlos | GitHub Pages, öffentliches Repository im Konto des Kunden | niemand; Sie berechnen Einrichtung und Betreuung |
| Betreut | Ihr GitLab, Gitea oder Forgejo mit Pages, oder ein statischer Webserver | Sie, in Ihrer Hosting-Gebühr |
| Gemischt | GitHub Pages für die öffentliche Website, Ihr Server für Formulare und Suche | der Kunde zahlt nur die Redaktionsinstallation |

Jeder Git-Host funktioniert für den Push: [Andere Git-Hosts: GitLab, Gitea und eigener Server](../statisches-veroeffentlichen/andere-git-hosts-gitlab-gitea-und-eigener-server.md).

## Viele Kunden betreiben

- Eine Installation mit vielen Sites: Jeder öffentliche Siteaccess bekommt sein eigenes Ziel, sein eigenes Repository
  und seinen eigenen Namen für die Zugangsdaten. Ein Ziel pusht nie in das Repository eines anderen Kunden.
- Geben Sie den Redakteuren jedes Kunden die Richtlinien `staticpublish/*`, beschränkt auf ihr Ziel.
- Zugangsdaten sind Namen in Setup > Geheimnisse oder Umgebungsvariablen; ihre Werte erscheinen nie in Einstellungen,
  Logs oder Site-Paketen. Siehe [Repositorys und Zugangsdaten](../../anleitungen/statisches-veroeffentlichen-repositorys-und-zugangsdaten.md).
- Der Cronjob-Teil `staticpublish` wiederholt ausstehende Pushes und führt die Zeitpläne aller Ziele aus.

## Klar über die Grenzen sprechen

Schreiben Sie sie in Ihr Angebot: kein Shop, kein Mitgliederbereich, Formulare nur über die Live-Site, die
Größengrenzen und Bedingungen von GitHub. Siehe [Grenzen und Bedingungen von GitHub Pages](../statisches-veroeffentlichen/grenzen-und-bedingungen-von-github-pages.md) und
[Passt statisch zu meiner Website?](../statisches-veroeffentlichen/passt-statisch-zu-meiner-website.md)

## Mit Kunden sprechen

Die Redaktionsinstallation kann hinter Ihrer Firewall oder einem VPN liegen; die öffentliche Website hat kein PHP und
keine Datenbank, auf dieser Seite gibt es also nichts zu patchen. Jede Veröffentlichung ist ein Commit mit Tag, und
zurückgehen ist ein Klick.

## Jetzt Ihre eigene erstellen

Richten Sie eine Kunden-Site als Pilot ein: [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
