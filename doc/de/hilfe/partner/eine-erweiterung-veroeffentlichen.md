---
title: Eine Erweiterung als Partner veröffentlichen
audience: partner
category: partnership
order: 420
summary: Schritt für Schritt von einer funktionierenden Erweiterung in einem Kundenprojekt zu einem veröffentlichten, gepflegten Paket - Kundencode abtrennen, Metadaten, Lizenz, README, Repository, Composer und Packagist, Versionierung und Release-Regeln, Ankündigung und Erfassung der Punkte.
keywords: Erweiterung veröffentlichen, Release, Composer, Packagist, GitHub, ezinfo.php, extension.xml, Version, Lizenz, README, Tag, ankündigen, publish extension, release, version, license, tag, announce
type: help
---
# Eine Erweiterung als Partner veröffentlichen

Diese Seite ist für Entwickler in Partnerunternehmen, die eine Erweiterung veröffentlichen möchten. Die technische
Grundlage ist die Entwickleranleitung [Erweiterungen](../../anleitungen/extensions.md); diese Seite ergänzt die Checkliste
des Partners rundherum.

## 1. Sie vom Kundenprojekt trennen

- [ ] Entfernen Sie Kundennamen, URLs, Zugangsdaten, Inhalte und Geschäftsregeln.
- [ ] Machen Sie kundenspezifisches Verhalten zu einer Einstellung mit neutralem Standardwert.
- [ ] Holen Sie die schriftliche Zustimmung des Kunden ein, den allgemeinen Teil zu veröffentlichen (wenn es bezahlte
      Arbeit war).

## 2. Ihr die Metadaten geben

Aus [Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen):

- [ ] `ezinfo.php` mit `'Version'` (ohne Präfix `v`), Name, Copyright, Lizenz und Info-URL.
- [ ] `extension.xml` mit derselben `<version>`, `<license>` und `<info_url>`.
- [ ] `composer.json`, wenn Sie auf Packagist veröffentlichen; halten Sie dort die Version mit, falls Sie eine führen.
- [ ] `php -l ezinfo.php` und `xmllint --noout extension.xml` laufen fehlerfrei.

## 3. Lizenz und Hinweise

- [ ] Eine Datei `LICENSE`; für Code, der auf Exponential aufbaut, eine GPL-kompatible Lizenz (das Projekt verwendet
      die GPL v2 oder später).
- [ ] Copyright-Zeilen, die Ihr Unternehmen und alle früheren Autoren nennen.
- [ ] Code von Dritten mit seiner Lizenz aufgeführt.

## 4. README

```text
# <Extension name>
What it does in two sentences.
Requirements: Exponential <version range>, PHP <range>, other extensions.
Install: composer require <vendor>/<name>  (or git clone ...)
Activate: Setup > Extensions, or ActiveExtensions[] in settings
Configure: <settings>
Support: <issues URL>, paid support by <company> (optional)
License: GPL v2 or later.  Credits: <original authors, sponsors>
```

## 5. Repository und Paket

- [ ] Ein öffentliches Git-Repository mit der vollständigen Historie, die Sie veröffentlichen dürfen.
- [ ] Issues aktiviert.
- [ ] Bei Packagist registriert, mit dem Webhook, damit neue Tags erscheinen.

## 6. Release

- [ ] Wählen Sie die Version anhand der veröffentlichten Tags, nach Version sortiert (`git tag -l 'v*' --sort=version:refname`).
- [ ] Committen Sie zuerst das Anheben der Version, dann setzen Sie das Tag auf diesen Commit.
- [ ] Verschieben oder löschen Sie nie ein veröffentlichtes Tag; veröffentlichen Sie stattdessen die nächste Version.

## 7. Ankündigen

- Ein Forenbeitrag auf <https://share.exponential.earth/forums> und eine Projektseite auf <https://projects.exponential.earth>.
- Ein kurzer Artikel oder ein Tutorial (500 Punkte als Artikel, 500 als Tutorial ab 1.500 Wörtern,
  [Das Punktesystem](das-punktesystem.md)).
- Erfassen Sie es ([Ihre Partneraktivität erfassen](partneraktivitaet-erfassen.md)).

## 8. Pflegen

- Testen Sie sie gegen jedes neue Exponential-Release ([Releases testen](releases-testen.md)).
- Beantworten Sie Issues; übernehmen Sie nützliche Pull Requests.
- Wenn Sie die Pflege einstellen, sagen Sie es in der README.

## Verwandte Seiten

- [Partner für Erweiterungen und Themes](partner-fuer-erweiterungen-und-themes.md)
- [Erweiterungen an die Upstream-Projekte zurückgeben](erweiterungen-zurueckgeben.md)
- [Erweiterungen](../../anleitungen/extensions.md)
- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md): eine Erweiterung veröffentlichen und was die GPL verlangt, wenn Sie sie verbreiten
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
