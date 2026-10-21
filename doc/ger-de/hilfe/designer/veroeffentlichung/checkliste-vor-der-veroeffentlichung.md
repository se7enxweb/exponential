---
title: "Checkliste: vor der Veröffentlichung"
audience: designer
category: theme-distribution
order: 400
featured: true
summary: Die letzte Checkliste, bevor ein Theme-Release öffentlich wird - Repository, Lizenzen, Version und Tag, Release Notes, Packagist, Screenshots, Einträge, Support und Sicherheit -, jeder Punkt verlinkt mit der Seite, die ihn erklärt.
keywords: Checkliste, veröffentlichen, Release-Checkliste, vor der Veröffentlichung, Checkliste Veröffentlichung, Tag, Packagist, Lizenz, checklist, publish, release checklist, before publishing, distribution checklist, license
type: help
---
# Checkliste: vor der Veröffentlichung

Diese Seite ist für Designer, die gleich ein Release veröffentlichen. Das Theme selbst sollte bereits die
[Checkliste: Die Theme-Erweiterung ist bereit](../erweiterungen/checkliste-theme-erweiterung-bereit.md) bestehen.

## Repository

- [ ] Öffentliches Repository, Standard-Branch `main`, Issues aktiviert ([Das GitHub-Repository anlegen](das-github-repository-anlegen.md)).
- [ ] Beschreibung und Topics gesetzt ([Repository-Name, Beschreibung und Topics](repository-name-beschreibung-und-topics.md)).
- [ ] README mit erstem Bildschirm, Installation, Lizenz, Support ([Ein README, das erklärt und verkauft](ein-readme-das-erklaert-und-verkauft.md)).
- [ ] `SECURITY.md` vorhanden ([Eine Sicherheitsrichtlinie für ein Theme](sicherheitsrichtlinie-fuer-ein-theme.md)).

## Lizenzen

- [ ] `LICENSE` für den Code; Lizenzen für Dokumentation und Bilder angegeben ([Lizenzwahl](lizenzwahl-ueberblick.md)).
- [ ] Jeder Bestandteil Dritter mit seiner Lizenz aufgeführt ([Schriften, Icons und Bibliotheken Dritter](schriften-icons-und-bibliotheken-dritter.md)).

## Version und Tag

- [ ] Veröffentlichte Tags und Releases geprüft ([Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md)).
- [ ] Version in jedes Feld geschrieben und vor dem Tag committet ([Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md)).
- [ ] `CHANGELOG.md` enthält das Release ([Eine CHANGELOG.md führen](eine-changelog-datei-fuehren.md)).
- [ ] Arbeitsbaum sauber; Theme aus diesem Commit installiert und angesehen.
- [ ] Annotierter Tag einzeln gepusht ([Annotierte Tags](annotierte-tags.md)); denken Sie daran, dass er endgültig ist.

## Release und Composer

- [ ] GitHub-Release mit Notes ([GitHub-Releases](github-releases.md), [Release Notes schreiben](release-notes-schreiben.md)).
- [ ] Packagist zeigt die neue Version innerhalb von Minuten ([Packagist automatisch aktuell halten](packagist-automatisch-aktualisieren.md)).
- [ ] `composer require yourvendor/mytheme` funktioniert in einer sauberen Installation.

## Gefunden und unterstützt werden

- [ ] Screenshots aktuell ([Screenshot-Checkliste](screenshot-checkliste.md)); Demo aktualisiert ([Eine Demo-Website betreiben](demo-websites.md)).
- [ ] Projektseite und Neuigkeiten auf projects.exponential.earth ([Eintrag](eintrag-auf-projects-exponential-earth.md)).
- [ ] Ankündigung bei nennenswerten Releases ([Ankündigen auf share.exponential.earth](ankuendigen-auf-share-exponential-earth.md)).
- [ ] Ein Seitenpaket, falls das Theme eines hat, per Pull-Request bei [se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) eingereicht (von 7x und der Exponential Foundation ausdrücklich empfohlen; [wie](../../seitenpakete/ihr-seitenpaket-beitragen.md)).
- [ ] Support-Kanäle angegeben ([Issue-Tracker und Support-Kanal](issue-tracker-und-support-kanal.md)).

## Verwandte Seiten

- [Ein Theme veröffentlichen: hier beginnen](ein-theme-veroeffentlichen-hier-beginnen.md)
- [Checkliste: ein Wartungszyklus](../lebenszyklus/checkliste-ein-wartungszyklus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
