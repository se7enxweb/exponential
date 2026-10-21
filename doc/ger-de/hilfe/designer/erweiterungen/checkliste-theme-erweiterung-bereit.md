---
title: "Checkliste: Die Theme-Erweiterung ist bereit"
audience: designer
category: theme-extensions
order: 450
featured: true
summary: Die Checkliste, bevor eine Theme-Erweiterung Ihre Hände verlässt - Struktur, Metadaten, Einstellungen, Lizenz, Composer, Pakete, Designqualität und ein sauberes Repository -, jeder Punkt mit dem Befehl oder der Seite, die ihn prüft.
keywords: Checkliste, bereit, Theme-Erweiterung, Release-Reife, Metadaten, Lizenz, composer validate, xmllint, php -l, Qualität, checklist, ready, theme extension, release readiness, metadata, license, quality
type: help
---
# Checkliste: Die Theme-Erweiterung ist bereit

Diese Seite ist für Designer, die gleich ein Theme veröffentlichen oder übergeben. Kopieren Sie die Liste in Ihr
Release-Issue und haken Sie sie ab.

## Struktur und Metadaten

- [ ] `extension/<name>/` verwendet einen gültigen Namen ([Benennen](ihre-theme-erweiterung-benennen.md)).
- [ ] `ezinfo.php` besteht `php -l` und zeigt Name, Version, Copyright, Lizenz, Website.
- [ ] `extension.xml` besteht `xmllint --noout` und stimmt mit `ezinfo.php` überein.
- [ ] Jedes Versionsfeld zeigt die Release-Nummer ([Alle Versionsfelder synchron halten](versionsfelder-synchron-halten.md)).
- [ ] `LICENSE` enthält den offiziellen Text; Hinweise Dritter liegen neben ihren Assets.
- [ ] `README.md` und, wenn die Installation mehr als einen Schritt braucht, `INSTALL.md`; `CHANGELOG.md` enthält dieses Release.

## Einstellungen

- [ ] `settings/design.ini.append.php` registriert das Design (`DesignExtensions[]`).
- [ ] Das Theme setzt `SiteDesign` nicht global ([Die Design-Einstellungen, die ein Theme mitliefert](design-einstellungen-eines-themes.md)).
- [ ] Kein Passwort, Schlüssel oder Token in einer Einstellungsdatei; kein `settings/override/` im Theme.
- [ ] Jede eigene Einstellung ist kommentiert.

## Paketierung

- [ ] `composer validate` läuft fehlerfrei durch; `extra.ezpublish-legacy-extension-name` nennt den Ordner.
- [ ] Eine Testinstallation mit Composer landet im richtigen Ordner und wird dargestellt.
- [ ] Mitgelieferte `.ezpkg`-Dateien bestehen die Prüfung und installieren sich auf einer Testseite.

## Designqualität

- [ ] Checkliste Barrierefreiheit bestanden ([Checkliste: Barrierefreiheit](../theme-design/checkliste-barrierefreiheit.md)).
- [ ] Kein Inline-CSS oder -JavaScript; die CSP-Checkliste bestanden ([Checkliste: CSP](../theme-design/checkliste-csp.md)).
- [ ] Heller und dunkler Modus, 200 % Zoom und eine Telefonbreite geprüft ([Checkliste: Dunkelmodus](../theme-design/checkliste-dunkelmodus.md)).
- [ ] Templates maskieren die Ausgabe ([Checkliste: Code-Review für Templates](../theme-design/checkliste-code-review-fuer-templates.md)).

## Repository

- [ ] `git status --short` ist leer; nichts Ignoriertes ist hineingerutscht ([Eine .gitignore für Themes](gitignore-fuer-themes.md)).
- [ ] Keine Kundennamen oder personenbezogenen Daten in Demodaten, Screenshots oder Text.

Wenn jedes Kästchen abgehakt ist, fahren Sie mit [Checkliste: vor der Veröffentlichung](../veroeffentlichung/checkliste-vor-der-veroeffentlichung.md) fort.

## Verwandte Seiten

- [Fehlersuche bei Theme-Erweiterungen](fehlersuche-bei-theme-erweiterungen.md)
- [Checkliste: Theme-Start](../theme-design/checkliste-theme-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
