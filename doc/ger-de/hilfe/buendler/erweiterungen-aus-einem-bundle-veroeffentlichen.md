---
title: Erweiterungen aus einem Bundle veröffentlichen
audience: bundler, partner, developer
category: bundling
order: 480
summary: Wie ein Bündler die allgemeinen Erweiterungen eines Bundles zu veröffentlichten Erweiterungen macht - Trennung von Kundenarbeit, Metadaten und Lizenz, ein öffentliches Repository und Packagist, Releases mit Versionen, die sich nie bewegen, sowie Ankündigung und Pflege.
keywords: Erweiterung veröffentlichen, Lizenz, Release, Version, ankündigen, pflegen, publish extension, Packagist, Composer, ezinfo.php, extension.xml, license, README, release, tag, version, webhook, announce, maintain, bundle
type: help
---
# Erweiterungen aus einem Bundle veröffentlichen

Diese Seite richtet sich an Bündler, deren Bundle Erweiterungen enthält, die auch andere nutzen könnten. Die
Checkliste für Partner ist [Eine Erweiterung als Partner veröffentlichen](../partner/eine-erweiterung-veroeffentlichen.md);
diese Seite folgt ihr und ergänzt, was ein Bündler bedenken sollte.

## Inhalt

1. [Welche Erweiterungen Sie veröffentlichen](#welche-erweiterungen-sie-veröffentlichen)
2. [Die Schritte](#die-schritte)
3. [Releases, die sich nie bewegen](#releases-die-sich-nie-bewegen)
4. [Nach der Veröffentlichung](#nach-der-veröffentlichung)

## Welche Erweiterungen Sie veröffentlichen

| Erweiterung in Ihrem Bundle | Veröffentlichen? |
|---|---|
| Eine allgemeine Funktion (ein Operator, ein Datentyp, eine Integration) | ja, sie hilft anderen und bringt Ihnen deren Korrekturen |
| Bindeglieder des Bundles (Ihre Installer-Hooks, Ihr Branding) | meist nein; behalten Sie sie im Repository des Bundles |
| Kundenspezifischer Code | nein, es sei denn, der Kunde stimmt zu und Sie entfernen alles Kundenspezifische |
| Ein Fork der Erweiterung eines anderen | schicken Sie Änderungen zuerst an das Upstream-Projekt ([Erweiterungen zurückgeben](../partner/erweiterungen-zurueckgeben.md)) |

## Die Schritte

Aus [Eine Erweiterung als Partner veröffentlichen](../partner/eine-erweiterung-veroeffentlichen.md):

1. **Trennen Sie sie vom Kundenprojekt**: keine Kundennamen, URLs, Zugangsdaten, Inhalte oder Geschäftsregeln;
   kundenspezifisches Verhalten wird zu einer Einstellung mit neutralem Standardwert.
2. **Metadaten**: `ezinfo.php` mit `'Version'` (ohne Präfix `v`), Name, Urheberrecht, Lizenz und Info-URL;
   `extension.xml` mit derselben `<version>`, `<license>` und `<info_url>`; `composer.json`, wenn Sie auf Packagist
   veröffentlichen; `php -l ezinfo.php` und `xmllint --noout extension.xml` laufen fehlerfrei.
3. **Lizenz und Hinweise**: eine Datei `LICENSE`; "für Code, der auf Exponential aufbaut, eine GPL-kompatible Lizenz
   (das Projekt verwendet GPL v2 oder später)"; Code Dritter mit seiner Lizenz aufgeführt.
4. **README**: was sie tut, Voraussetzungen, Installation, Aktivierung, Konfiguration, Support, Lizenz.
5. **Repository und Paket**: ein öffentliches Git-Repository mit aktivierten Issues, auf Packagist registriert "mit dem
   Webhook, damit neue Tags erscheinen".
6. **Release** (siehe unten).
7. **Kündigen** Sie sie an: Forumsbeitrag, Projektseite.

Benennen Sie sie mit Ihrem eigenen Präfix, nie mit `exp`
([Ein Bundle benennen und gestalten](ein-bundle-benennen-und-gestalten.md#ihren-code-benennen)).

## Releases, die sich nie bewegen

- "Wählen Sie die Version aus den veröffentlichten Tags, nach Version sortiert
  (`git tag -l 'v*' --sort=version:refname`)."
- "Committen Sie zuerst die Versionserhöhung und taggen Sie dann diesen Commit."
- "Verschieben oder löschen Sie nie einen veröffentlichten Tag; veröffentlichen Sie stattdessen die nächste Version."

Packagist liest einen Tag einmal; ein verschobener Tag ergibt zwei verschiedene Pakete mit derselben Versionsnummer.
Ihr Bundle legt die veröffentlichte Version dann wie jede andere fest
([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)).

## Nach der Veröffentlichung

- Verlangen Sie in Ihrem Bundle das veröffentlichte Paket statt einer privaten Kopie.
- "Testen Sie sie gegen jedes neue Exponential-Release"; "Beantworten Sie Issues; übernehmen Sie nützliche Pull
  Requests"; "Wenn Sie die Pflege einstellen, sagen Sie das in der README."
- Erfassen Sie sie beim Partnerteam, wenn Sie Partner sind
  ([Ihre Partneraktivität erfassen](../partner/partneraktivitaet-erfassen.md)).

## Verwandte Seiten

- [Erweiterungs-Bundles](erweiterungs-bundles.md)
- [Erweiterungen (Entwicklerhandbuch)](../../anleitungen/extensions.md)
- [Partner für Erweiterungen und Themes](../partner/partner-fuer-erweiterungen-und-themes.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
