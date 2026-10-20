---
title: "Wählen, wo veröffentlicht wird"
audience: designer, sales-marketing
category: theme-distribution
order: 20
summary: Die Orte, an denen ein Theme für Exponential veröffentlicht werden kann - ein Git-Host (GitHub), Packagist für Composer, projects.exponential.earth, Ankündigungen auf share.exponential.earth und Ihre eigene Website - was jeder Ort Ihnen bietet und in welcher Reihenfolge Sie sie einrichten.
keywords: veröffentlichen, GitHub, Packagist, projects.exponential.earth, share.exponential.earth, ezpedia, eigene Website, wo veröffentlichen, Vertriebskanäle, publish, own website, where to publish, distribution channels
type: help
---
# Wählen, wo veröffentlicht wird

Diese Seite ist für Designer, die entscheiden, wo ihr Theme öffentlich zu finden sein soll. Die meisten Themes nutzen
mehrere Orte gleichzeitig; jeder hat eine andere Aufgabe.

## Die Orte und ihre Aufgaben

| Ort | Seine Aufgabe | Was Nutzer dort tun |
|---|---|---|
| Ein Git-Host (die Themes von 7x nutzen GitHub) | Der Quellcode, die Historie, Issues, Releases | Code lesen, Issues melden, Pull-Requests schicken, Releases herunterladen |
| [Packagist](https://packagist.org/) | Der Composer-Index | `composer require yourvendor/mytheme` |
| [projects.exponential.earth](https://projects.exponential.earth/) | Die Community-Plattform von Exponential: Projektseiten mit Neuigkeiten, Downloads, Foren, Git- und Composer-Anbindung | Projekte nach Typ oder Organisation durchsuchen, Neuigkeiten und RSS-Feeds verfolgen |
| [share.exponential.earth](https://share.exponential.earth/) | Das Community-Portal: Foren (darunter Extensions und Setup & design), Blogs, Tutorials | Fragen stellen, Ankündigungen und Tutorials lesen |
| [eZpedia](https://ezpedia.exponential.earth/) | Das Community-Wiki unter der GNU FDL | Anleitungen und Referenzartikel lesen |
| Ihre eigene Website | Ihre Produktseite, Demos, Support und Verkauf | Entscheiden, Support kaufen, Sie kontaktieren |

Was jede Community-Website bietet, wurde am 18. Oktober 2026 geprüft; siehe
[Eintrag auf projects.exponential.earth](eintrag-auf-projects-exponential-earth.md) und
[Ankündigen auf share.exponential.earth](ankuendigen-auf-share-exponential-earth.md).

## Eine sinnvolle Reihenfolge

1. **Zuerst der Git-Host.** Alles andere verlinkt darauf ([Das GitHub-Repository anlegen](das-github-repository-anlegen.md)).
2. **Erstes Release** mit einem Tag ([GitHub-Releases](github-releases.md)).
3. **Packagist**, sobald es ein getaggtes Release gibt ([Ein Theme bei Packagist einreichen](bei-packagist-einreichen.md)).
4. **Community-Eintrag und Ankündigung**, sobald jemand anderes das Theme installieren kann.
5. **Ihre eigene Seite und Demo**, wann immer es hilft ([Eine Demo-Website betreiben](demo-websites.md)).

## Öffentlich oder privat

| Öffentliches Repository | Privates Repository |
|---|---|
| Jeder kann installieren, lernen und beitragen | Nur die Personen, denen Sie Zugang geben |
| Kostenloses Hosting bei den meisten Git-Hosts und bei Packagist | Packagist listet öffentliche Pakete; private brauchen ein privates Composer-Repository |
| Passt zur GPL und zur Community | Passt zu Arbeit für einen Kunden, die Sie nicht veröffentlichen dürfen |

Arbeit für einen Kunden gehört dem, den der Vertrag nennt. Veröffentlichen Sie sie nur mit diesem Recht
([Verträge und Bedingungen](../../../../eng-us/help/designers/sales/contracts-and-terms.md)).

## Seitenpakete: tragen Sie sie bei

Wenn Ihr Theme mit einem vollständigen Seitenpaket kommt (der Art, aus der der Setup-Assistent eine neue Website
installiert), ermutigen 7x und die Exponential Foundation Sie ausdrücklich, es per Pull-Request bei
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth)
einzureichen, dem Repository des Paketservers, den der Setup-Assistent nutzt. Dann kann jede neue Installation damit
beginnen. Wie: [Ihr Seitenpaket beitragen](../../seitenpakete/ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [Composer-Paket oder .ezpkg](../../../../eng-us/help/designers/extensions/composer-or-ezpkg.md)
- [Wo Exponential-Themes heute zu finden sind](../../../../eng-us/help/designers/sales/where-themes-are-found.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
