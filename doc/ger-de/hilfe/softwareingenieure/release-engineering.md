---
title: Release Engineering
audience: software-engineer, developer
category: software-engineering
order: 600
summary: Wie Releases von Exponential und seinen Erweiterungen entstehen - der Hauptzweig und sein Alias 6.0.x-dev, wo die Kernel-Version liegt, das Release-Verfahren für Erweiterungen mit ezinfo.php und extension.xml, das Dateimanifest, Prüfungen vor dem Taggen, GitHub-Releases und Packagist und was zu tun ist, wenn sich ein Release als fehlerhaft herausstellt.
keywords: Release Engineering, Release, Tag, Version, lib/version.php, ezinfo.php, extension.xml, filelist.md5, GitHub-Release, Packagist, Webhook, Release-Checkliste, Softwareingenieur, release engineering, release, version, GitHub release, webhook, release checklist
type: help
---
# Release Engineering

Diese Seite richtet sich an Ingenieure, die Releases des Kernels oder einer Erweiterung erstellen, und an jene, die wissen
müssen, was ein Release garantiert. Die Regeln stammen aus
[Versionsverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md) und
[Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md); der Kurs
für Maintainer von Erweiterungen ist [Veröffentlichen](../erweiterungsentwicklung/teil-7-veroeffentlichen/README.md).

## Inhalt

1. [Linien und Versionen](#linien-und-versionen)
2. [Bevor Sie taggen](#bevor-sie-taggen)
3. [Eine Erweiterung veröffentlichen](#eine-erweiterung-veröffentlichen)
4. [Veröffentlichung](#veröffentlichung)
5. [Wenn ein Release fehlerhaft ist](#wenn-ein-release-fehlerhaft-ist)

## Linien und Versionen

- `main` ist die Entwicklungslinie. `composer.json` gibt ihr den Alias `6.0.x-dev` (`extra.branch-alias`), und die CI läuft
  darauf.
- Die Kernel-Version liegt in `lib/version.php` (`VERSION_MAJOR`, `VERSION_MINOR`, `VERSION_RELEASE`) und in der
  Datenbankzeile `ezsite_data` `ezpublish-version`, die das Update-SQL schreibt. Die `composer.json` im Hauptverzeichnis hat
  kein Feld `version`; Composer nimmt die Version aus dem Tag.
- Kernel-Releases werden als `v6.0.<n>` getaggt. Die Tags `v6.0.0` bis `v6.0.14` existieren; die Linie 6.0.15 ist in
  Entwicklung.
- Erweiterungen sind eigene Composer-Pakete mit eigenen Versionen.

Eine Nummer wählen: [Versionsnummern und unveränderliche Tags](versionsnummern-und-unveraenderliche-tags.md).

## Bevor Sie taggen

Ein Tag ist billig richtig zu machen und unmöglich zu korrigieren. Vor dem Taggen:

- [ ] Der Arbeitsbaum ist sauber.
- [ ] Quality und PHPUnit sind auf dem Commit grün, den Sie taggen werden ([CI-Gates](ci-gates.md)).
- [ ] Die Versionsfelder sind in dem Commit erhöht, auf den der Tag zeigt.
- [ ] `share/filelist.md5` passt zu den Dateien (`php bin/php/checkmanifest.php --all`).
- [ ] Die Changelog-Seite für die Version existiert ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).
- [ ] Jede für Upgrades relevante Änderung hat ihre Seite in `doc/eng-us/bc/`.
- [ ] Im Repository committete Build-Artefakte passen zu ihren Quellen.

## Eine Erweiterung veröffentlichen

Jedes Release schreibt seine Nummer in die Erweiterung, in dem Commit, auf den der Tag zeigt:

1. Wählen Sie die Nummer anhand dessen, was bereits veröffentlicht ist.
2. Setzen Sie `'Version'` in `ezinfo.php` und `<metadata><version>` in `extension.xml` darauf, ohne das Präfix `v` (Tag
   `v1.8.4` ergibt `1.8.4`). Lassen Sie nie `//autogentag//` oder `dev-master` stehen. Beide Dateien nennen außerdem die
   Lizenz und eine `info_url` auf dem öffentlichen Repository der Erweiterung.
3. Ändern Sie jedes weitere Versionsfeld, das das Repository führt (`version` in seiner `composer.json`, den Kopf seiner
   eigenen `share/filelist.md5`), und erneuern Sie die Prüfsummen dieses Manifests.
4. Prüfen Sie: `php -l ezinfo.php` und `xmllint --noout extension.xml`.
5. Committen Sie, **dann** taggen Sie diesen Commit, dann pushen Sie beides. Taggen Sie nie zuerst und erhöhen danach.

Erweiterungen, die im Kernel-Repository ausgeliefert werden, tragen die Version des Kernels. Die Info-Seite liest beide
Dateien ([Info- und Paketseiten](../../funktionen/6.0/info-und-paketseiten.md),
[Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)).

## Veröffentlichung

- **GitHub-Release**: aus dem Tag erstellt, mit Notes aus dem Changelog
  ([GitHub-Releases und Release Notes](../erweiterungsentwicklung/teil-7-veroeffentlichen/github-releases-und-release-notes.md)).
- **Packagist**: Jedes `se7enxweb/*`-Repository ist ein Composer-Paket. Ein Webhook am GitHub-Repository informiert
  Packagist über neue Tags; ein Paket ohne ihn bleibt bei einer alten Version, bis es von Hand aktualisiert wird
  ([Der Packagist-Webhook](../erweiterungsentwicklung/teil-7-veroeffentlichen/der-packagist-webhook.md)).
- **Neu festlegen im Hauptprojekt**: Veröffentlichen Sie die Erweiterung, sehen Sie die Version auf Packagist und
  committen Sie dann die neue Bedingung im Hauptprojekt. Das Push-Gate lehnt eine Bedingung ab, die Packagist noch nicht
  erfüllen kann, weil die Installation der CI sonst auf jedem Zweig scheitern würde.

## Wenn ein Release fehlerhaft ist

Verschieben, löschen oder erstellen Sie den Tag nicht neu, und ersetzen Sie nicht die Assets des Releases. Veröffentlichen
Sie die nächste Version mit der Korrektur und sagen Sie in ihren Notes klar, was an der vorherigen falsch war. Nutzer, die
Bereiche festlegen, erhalten die neue Version; niemandem ist mit einem Loch in der Geschichte geholfen.

## Verwandte Seiten

- [Die Release-Checkliste](../erweiterungsentwicklung/teil-7-veroeffentlichen/die-release-checkliste.md)
- [Versionen und Releases, für Anwendungsentwickler](../ersteller/anwendungsentwickler/versionen-und-releases.md)
- [Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
