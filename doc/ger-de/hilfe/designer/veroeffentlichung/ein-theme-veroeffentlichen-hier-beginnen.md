---
title: "Ein Theme veröffentlichen: hier beginnen"
audience: designer, developer
category: theme-distribution
order: 10
featured: true
summary: Die Übersicht für das Veröffentlichen eines Themes - GitHub-Repository, Versionsnummern und endgültige Tags, Releases und Changelogs, Packagist, Lizenzwahl, Screenshots und Demo-Websites sowie die Community-Websites share.exponential.earth, projects.exponential.earth und eZpedia.
keywords: Veröffentlichung, Theme veröffentlichen, GitHub, Packagist, Release, Version, Tag, Changelog, Lizenz, Screenshots, Demo-Website, projects.exponential.earth, share.exponential.earth, ezpedia, distribution, publish theme, license, demo site
type: help
---
# Ein Theme veröffentlichen: hier beginnen

Dieser Bereich ist für Designer, deren Theme-Erweiterung funktioniert und die möchten, dass andere sie finden,
installieren und ihr vertrauen. Bauen Sie zuerst die Erweiterung ([Theme-Erweiterungen: hier beginnen](../erweiterungen/theme-erweiterungen-hier-beginnen.md)).

## Die eine Regel, die Sie sich merken sollten

**Ein veröffentlichtes Release ist endgültig.** Ein Tag, der in ein öffentliches Repository gepusht wurde, und die
Composer-Version, die Packagist daraus liest, lassen sich nie korrigieren. Ein Fehler in einem Release wird mit dem
nächsten Release behoben. Lesen Sie vor Ihrem ersten Release [Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md).

## Eine Lesereihenfolge

1. Wo und wie: [Wählen, wo veröffentlicht wird](waehlen-wo-veroeffentlicht-wird.md),
   [Das GitHub-Repository anlegen](das-github-repository-anlegen.md),
   [Ein README, das erklärt und verkauft](ein-readme-das-erklaert-und-verkauft.md).
2. Lizenzen: [Lizenzwahl: ein Überblick](lizenzwahl-ueberblick.md).
3. Versionen und Releases: [Eine Versionsnummer wählen](eine-versionsnummer-waehlen.md),
   [Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md),
   [Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md), [GitHub-Releases](github-releases.md).
4. Composer: [Ein Theme bei Packagist einreichen](bei-packagist-einreichen.md),
   [Packagist automatisch aktuell halten](packagist-automatisch-aktualisieren.md).
5. Gefunden werden: [Screenshots](screenshots-fuer-ein-theme.md), [Demo-Websites](demo-websites.md),
   [projects.exponential.earth](eintrag-auf-projects-exponential-earth.md),
   [share.exponential.earth](ankuendigen-auf-share-exponential-earth.md).
6. Letzte Prüfung: [Checkliste: vor der Veröffentlichung](checkliste-vor-der-veroeffentlichung.md).

## Alle Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Wählen, wo veröffentlicht wird](waehlen-wo-veroeffentlicht-wird.md) | GitHub, Packagist, Projektseiten, Ihre eigene Website |
| [Das GitHub-Repository anlegen](das-github-repository-anlegen.md) | Schritt für Schritt mit `gh` |
| [Repository-Name, Beschreibung und Topics](repository-name-beschreibung-und-topics.md) | Auf GitHub gefunden werden |
| [Ein README, das erklärt und verkauft](ein-readme-das-erklaert-und-verkauft.md) | Die erste Seite, die Menschen lesen |
| [Der erste Push und der Standard-Branch](erster-push-und-standard-branch.md) | `main` und der erste Push |
| [Eine Versionsnummer wählen](eine-versionsnummer-waehlen.md) | Welche Nummer für welche Änderung |
| [Nach .9 kommt .10](nur-die-letzte-stelle-zaehlt.md) | Nur die letzte Stelle zählt |
| [Tags richtig sortieren](tags-richtig-sortieren.md) | `--sort=version:refname` |
| [Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md) | Zuerst Tags und Releases |
| [Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md) | Der Release-Commit |
| [Annotierte Tags](annotierte-tags.md) | Tags mit einer Nachricht |
| [Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md) | Warum, und was daraus folgt |
| [GitHub-Releases](github-releases.md) | `gh release create` |
| [Release Notes schreiben](release-notes-schreiben.md) | Was Nutzer wissen müssen |
| [Wenn ein Release schiefgeht](wenn-ein-release-schiefgeht.md) | Nach vorn korrigieren |
| [Changelog-Einträge](changelog-praefixe.md) | Added, Updated, Removed, Renamed, Fixed |
| [Eine CHANGELOG.md führen](eine-changelog-datei-fuehren.md) | Die Historiendatei |
| [Ein Theme bei Packagist einreichen](bei-packagist-einreichen.md) | Einmalige Einrichtung |
| [Packagist automatisch aktuell halten](packagist-automatisch-aktualisieren.md) | Der GitHub-Hook |
| [Packagist zeigt eine alte Version](veraltete-packagist-version.md) | Diagnose |
| [Versionsbedingungen Ihrer Nutzer](versionsbedingungen-fuer-nutzer.md) | `~`, `^` und feste Versionen |
| [Datei-Prüfsummen](filelist-pruefsummen.md) | `share/filelist.md5` |
| [Lizenzwahl: ein Überblick](lizenzwahl-ueberblick.md) | Code, Dokumentation, Bilder |
| [Die GPL für Theme-Code](gpl-fuer-theme-code.md) | Warum die meisten Themes unter der GPL stehen |
| [Die GFDL für Theme-Dokumentation](gfdl-fuer-theme-dokumentation.md) | Dokumentation unter der GFDL |
| [Creative Commons für Bilder](creative-commons-fuer-bilder.md) | Bilder und Grafiken |
| [Schriften, Icons und Bibliotheken Dritter](schriften-icons-und-bibliotheken-dritter.md) | Die Arbeit anderer in Ihrer |
| [Kommerzielle Bedingungen neben der GPL](kommerzielle-bedingungen-neben-der-gpl.md) | Was verkauft werden kann und was sich nicht einschränken lässt |
| [Copyright-Hinweise in Dateien](copyright-hinweise-in-dateien.md) | Dateiköpfe und Hinweise |
| [Screenshots für ein Theme](screenshots-fuer-ein-theme.md) | Was Sie zeigen |
| [Screenshot-Checkliste](screenshot-checkliste.md) | Vor dem Hochladen |
| [Eine Demo-Website betreiben](demo-websites.md) | Eine Live-Vorschau |
| [Demo-Inhalte für ein Theme](demo-inhalte.md) | Inhalte, die sich zu zeigen lohnen |
| [Eintrag auf projects.exponential.earth](eintrag-auf-projects-exponential-earth.md) | Die Community-Plattform |
| [Ankündigen auf share.exponential.earth](ankuendigen-auf-share-exponential-earth.md) | Foren und Blogs |
| [Dokumentieren auf eZpedia](dokumentieren-auf-ezpedia.md) | Das Community-Wiki |
| [Issue-Tracker und Support-Kanal](issue-tracker-und-support-kanal.md) | Wo Nutzer fragen |
| [Eine Sicherheitsrichtlinie für ein Theme](sicherheitsrichtlinie-fuer-ein-theme.md) | Vertrauliche Meldungen |
| [Checkliste: vor der Veröffentlichung](checkliste-vor-der-veroeffentlichung.md) | Die letzte Prüfung |

## Verwandte Seiten

- [Themes und Dienstleistungen verkaufen: hier beginnen](../verkauf/verkaufen-hier-beginnen.md)
- [Der Lebenszyklus der Wartung: hier beginnen](../lebenszyklus/lebenszyklus-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
