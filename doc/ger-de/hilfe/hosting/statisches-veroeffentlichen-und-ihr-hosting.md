---
title: "Statisches Veröffentlichen und Ihr Hosting"
audience: hosting
category: hosting
order: 901
summary: Für Hosting-Anbieter und Administratoren - mit statischem Veröffentlichen kann die dynamische Installation intern bleiben, hinter einer Firewall oder einem VPN, während die öffentliche Website statisch ist; was der Build vom Netz, vom Server und vom Cluster braucht, und was es kostet.
keywords: Hosting, Hosting-Anbieter, Firewall, VPN, interne Installation, Sicherheit, Kosten, Cluster, ausgehendes Git, Statisches Veröffentlichen, hosting, firewall, internal installation, security, cost, cluster, DFS, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing
type: help
---
# Statisches Veröffentlichen und Ihr Hosting

Statisches Veröffentlichen teilt eine Website in zwei Teile: die Exponential-Installation, in der bearbeitet wird, und
eine statische Kopie, die Besucher bekommen. Für das Hosting ändert das, was öffentlich sein muss, was schnell gepatcht
werden muss und was es kostet.

## Was Sie am Ende haben

Einen Plan, wo die Redaktionsinstallation läuft, was sie vom Netz braucht und welche Hosting-Kosten wegfallen.

## Die Redaktionsinstallation kann intern bleiben

Die öffentliche Website liefert GitHub Pages oder ein anderer statischer Host aus. Die Exponential-Installation muss
dann nur für Redakteure erreichbar sein: in einem internen Netz, hinter einem VPN oder hinter einer IP-Freigabeliste.

Was sie braucht:

- **Ausgehend** HTTPS (oder SSH) zum Git-Host für den Push, und zu `api.github.com` für die GitHub-Einrichtung.
- **Ihren eigenen Webserver, von ihr selbst erreichbar**: Der Build crawlt die Website über HTTP. Setzen Sie
  `CrawlURL` auf eine interne Adresse wie `http://127.0.0.1/`, wenn der öffentliche Name intern nicht auflöst.
- **Eingehend nur für Formulare**, wenn Sie den Formular-Endpunkt nutzen: Die Besucher der statischen Website senden
  an `staticpublish/form` unter einer öffentlichen Adresse. Ohne ihn zeigen Formulare einen Link.

## Ressourcen

- Ein Build rendert jede Seite einmal. Rechnen Sie mit einigen Minuten CPU für Hunderte Seiten; Teil-Builds einzelner
  Seiten laufen beim Veröffentlichen von Inhalten.
- Speicherplatz: Der Zielordner hält eine Kopie der Website und ihre Git-Geschichte. Builds liegen unter
  `var/<site>/static-publish/`.
- Builds und Veröffentlichungen laufen als Konsolenprozesse, nie in einer Web-Anfrage, und pro Ziel nur einer zur Zeit.

## Cluster und Velocity

Dateien unter `var/` werden über den Cluster-Datei-Handler gelesen, DFS-Cluster funktionieren also. Unter Velocity
startet der Job als abgelöster Konsolenprozess. Siehe die [Funktionsseite](../../funktionen/6.0/statisches-veroeffentlichen.md#plattformen).

## Kosten

- Hosting der öffentlichen Website: kostenlos auf GitHub Pages für öffentliche Repositorys, oder die Kosten eines
  einfachen statischen Webservers.
- Die Redaktionsinstallation: kleiner als eine öffentliche, weil sie nur Redakteure trägt, keine Besucher.
- Weniger Bereitschaft: Ein Sicherheitsrelease von PHP oder Exponential ist für die öffentliche Website kein Notfall mehr.

Grenzen von GitHub Pages: [Grenzen und Bedingungen von GitHub Pages](../statisches-veroeffentlichen/grenzen-und-bedingungen-von-github-pages.md).
Private Inhalte aus der Kopie heraushalten: [Private Inhalte privat halten](../statisches-veroeffentlichen/private-inhalte-privat-halten.md).

## Jetzt Ihre eigene erstellen

Probieren Sie es auf einer Staging-Site: [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
