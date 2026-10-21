---
title: Die Release-Checkliste
audience: developer, designer, app-creator
category: extension-development
order: 140
summary: Das ganze Release einer Erweiterung auf einer Seite, in der richtigen Reihenfolge - Nummer wählen, Dateien anheben, Changelog, testen, taggen, pushen, GitHub-Release, Packagist, ankündigen - jeder Schritt mit Link zu seinem Kapitel.
keywords: Release-Checkliste, Vorgehen beim Release, Version, Tag, ezinfo, extension.xml, Changelog, Tests, Push, GitHub-Release, Packagist, ankündigen, release checklist, release procedure, changelog, tests, push, GitHub release, announce
type: help
---
# Die Release-Checkliste

Teil 7, Kapitel 14, das letzte dieses Teils. Jeder Schritt eines Releases, in der richtigen Reihenfolge. Kopieren Sie
sie für jedes Release in ein Issue oder in Ihre Notizen und haken Sie sie ab.

## Vorher

- [ ] Jede Änderung dieses Releases ist committet und auf den Haupt-Branch gepusht; die CI ist für den letzten Commit
      grün.
- [ ] Holen Sie die Tags und listen Sie die neuesten fünf auf, als Versionen sortiert:
      `git fetch --tags && git tag -l 'v*' --sort=version:refname | tail -5`
      ([Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md)).
- [ ] Vergleichen Sie mit `gh release list` und der Packagist-Seite.
- [ ] Wählen Sie die nächste Nummer: Patch, Minor oder Major ([Regeln für Versionsnummern](regeln-fuer-versionsnummern.md)).
      Nach `.9` kommt `.10`.

## Der Release-Commit

- [ ] `'Version'` in `ezinfo.php` und `<version>` in `extension.xml` auf die neue Nummer gesetzt, ohne `v`
      ([ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md)).
- [ ] Auch `version` in `composer.json`, falls die Datei eines hat.
- [ ] `share/filelist.md5` aktualisiert, falls die Erweiterung eine mitliefert
      ([filelist.md5 und die Upgrade-Prüfung](filelist-md5-und-die-upgrade-pruefung.md)).
- [ ] Der Changelog-Eintrag ist geschrieben, Upgrade-Hinweise zuerst ([Changelogs](changelogs.md)).
- [ ] Committet: `Updated: Version 1.2.4`.

## Prüfen

- [ ] `git status` ist sauber; `git log -1` ist der Release-Commit.
- [ ] `php -l ezinfo.php`, `xmllint --noout extension.xml`, `composer validate`.
- [ ] Lint und Tests auf der ältesten und der neuesten unterstützten PHP-Version
      ([Vor einem Release testen](vor-einem-release-testen.md)).
- [ ] Eine saubere Installation aus dem Repository auf einer Test-Website, nach der README.
- [ ] Jede Funktion einmal verwendet; keine Fehler, Warnungen, Deprecations oder CSP-Meldungen aus der Erweiterung.
- [ ] Ein Update vom vorigen Release funktioniert mit den Upgrade-Hinweisen, so wie sie geschrieben sind.

## Taggen und veröffentlichen

- [ ] `git tag -a v1.2.4 -m "Version 1.2.4"` auf dem Release-Commit.
- [ ] `git push origin main` und `git push origin v1.2.4` (den Tag mit seinem Namen).
- [ ] Ab hier ist die Version dauerhaft ([Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md)).
- [ ] `gh release create v1.2.4 --title "1.2.4" --notes-file ...`
      ([GitHub-Releases und Release Notes](github-releases-und-release-notes.md)).
- [ ] Die Packagist-Seite zeigt innerhalb weniger Minuten 1.2.4; wenn nicht, prüfen Sie den Hook
      ([Der Packagist-Webhook](der-packagist-webhook.md)).

## Danach

- [ ] Installieren Sie die veröffentlichte Version (`composer require your-account/myext:1.2.4`) auf einer
      Test-Website und öffnen Sie die Seite „Über“: Die Version zeigt 1.2.4.
- [ ] Kündigen Sie an, wo es darauf ankommt ([Ein Release ankündigen](ein-release-ankuendigen.md)).
- [ ] Schließen Sie die Issues, die das Release behoben hat, mit einem Link auf das Release.
- [ ] Wenn etwas nicht stimmt: Rühren Sie weder Tag noch Release an; beheben Sie es und veröffentlichen Sie 1.2.5, mit
      dem Hinweis, was an 1.2.4 falsch war.

## Verwandte Seiten

- Zurück: [Der Wartungslebenszyklus](der-wartungslebenszyklus.md)
- Weiter: [Teil 8: Referenz der eingebauten Funktionen für Erweiterungsautoren](../teil-8-referenz/README.md)
- [Teil 7: Ihre Erweiterung veröffentlichen](README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
