---
title: "Checkliste: Code-Review für Templates"
audience: designer, developer
category: theme-design
order: 920
summary: Was ein Prüfer bei einer Änderung an einem Template oder Stylesheet kontrolliert: Maskierung, Übersetzungen, fetch und Caching, Overrides, Inline-Code, Token und dunkle Werte, logische Eigenschaften, Barrierefreiheit und die hellen und dunklen Screenshots, die zur Änderung gehören.
keywords: Code-Review, Template-Review, Review-Checkliste, Checkliste für Pull Requests, wash, i18n, fetch-Limit, Cache-Schlüssel, Inline-CSS, Token, Review von Admin-Templates, code review, template review, review checklist, pull request checklist, fetch limit, cache keys, inline css, tokens, admin template review
type: help
---
# Checkliste: Code-Review für Templates

Diese Seite ist für Prüfer von Änderungen an Templates und Stylesheets und für Designer, deren Änderung schon beim ersten Review
angenommen werden soll. Sie folgt der Review-Liste des Projekts für Admin-Templates und ergänzt, was für jedes Design gilt.

## Inhalt

1. [Korrektheit und Sicherheit](#korrektheit-und-sicherheit)
2. [Performance](#performance)
3. [Gestaltung](#gestaltung)
4. [Barrierefreiheit und Sprachen](#barrierefreiheit-und-sprachen)
5. [Nachweise](#nachweise)

## Korrektheit und Sicherheit

- [ ] Jeder Wert aus Inhalten, Einstellungen oder der Anfrage wird bei der Ausgabe maskiert ([Wash und Maskierung](../templates/wash-und-maskierung.md)).
- [ ] URLs mit `ezurl`/`ezroot` gebaut, Design-Dateien mit `ezimage`/`ezdesign`; keine fest eingetragenen Pfade von Siteaccesses.
- [ ] Optionale Felder vor der Verwendung mit `has_content` geprüft.
- [ ] `{def}`-Variablen in eingebundenen Templates mit `{undef}` entfernt; keine Kollision mit den Namen des Einbindenden.
- [ ] Override-Regeln: eindeutige Namen mit Präfix, ein korrektes `Match`, `Priority`, wo es darauf ankommt ([Reihenfolge der Overrides](../templates/override-reihenfolge.md)).
- [ ] Das Template lässt sich parsen: `./console exp:eztemplatecheck <file or folder>`.

## Performance

- [ ] Jedes Listen-fetch hat ein Limit; kein fetch in einer Schleife ([Performance von fetch](../templates/fetch-leistung.md)).
- [ ] Die Schlüssel der Cache-Blöcke decken alles ab, was die Ausgabe ändert, und nicht mehr.
- [ ] Nichts Benutzerspezifisches in einem im View-Cache gespeicherten Template; kein `csp_nonce()` in zwischengespeicherter Ausgabe.

## Gestaltung

- [ ] Kein `<style>` und kein `style=""`; keine Inline-Skripte oder -Handler ([CSP-sicheres Design](csp-sicheres-design.md)).
- [ ] Gemeinsame Klassen wiederverwendet (Administration: die Komponenten `.a4-*`), bevor neues CSS entsteht.
- [ ] Nur Token für Farben, Radius, Abstände, Schriften; ein dunkler Wert für jedes neue Farbtoken.
- [ ] Modul-CSS in der richtigen Datei (Administration: `stylesheets/modules/<group>.css` oder das Admin-Stylesheet der Erweiterung).
- [ ] Logische Eigenschaften statt links und rechts.
- [ ] Änderungen an Stylesheets mit dem Packer-Schritt deployt; keine `?v=`-Abfragen.

## Barrierefreiheit und Sprachen

- [ ] Jeder Text der Oberfläche läuft über `i18n` mit dem richtigen Kontext, mit Platzhaltern für veränderliche Teile.
- [ ] Überschriften in der richtigen Reihenfolge; Landmarks intakt; Knöpfe sind Knöpfe, Links sind Links.
- [ ] Bilder haben Alternativtexte; Bedienelemente, die nur ein Symbol zeigen, haben Namen.
- [ ] Fokus sichtbar; die Tastatur funktioniert für alles Interaktive, das die Änderung hinzufügt.

## Nachweise

- [ ] Screenshots der geänderten Seite im hellen **und** im dunklen Modus, bei 1440 px und bei 960 px mit Skalierung 2.
- [ ] Bei Admin-Designs: `php vendor/bin/phpunit --testsuite design` läuft durch ([Der Test der Admin-Gestaltungsregeln](der-test-der-admin-gestaltungsregeln.md)).
- [ ] Keine Fehler in der Konsole auf den geänderten Seiten.

## Verwandte Seiten

- [Checkliste: Start eines Themes](checkliste-theme-start.md)
- [Stilleitfaden für Templates](../templates/template-styleguide.md)
- [Häufige Template-Fehler](../templates/haeufige-template-fehler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
