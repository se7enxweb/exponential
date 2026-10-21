---
title: "Checkliste: Start eines Themes"
audience: designer, project-manager
category: theme-design
order: 860
summary: Alles, was zu prüfen ist, bevor ein neues oder geändertes Design live geht: Struktur, Einstellungen, Templates, Assets, Barrierefreiheit, dunkler Modus, rechts nach links, Sicherheitsrichtlinie, Performance, Caches und die Umstellung selbst.
keywords: Checkliste, Start, Livegang, Theme-Start, Design veröffentlichen, vor dem Start, Design-Review, Qualitätsschranke, vor dem Livegang, checklist, launch, go live, theme launch, release design, pre launch, design review, quality gate, before going live
type: help
---
# Checkliste: Start eines Themes

Diese Seite ist für Designer und Projektleiter, bevor ein Design live geht. Jede Zeile verweist auf die Seite, die sie erklärt.
Kopieren Sie sie in Ihr Ticket und haken Sie sie ab.

## Inhalt

1. [Struktur und Einstellungen](#struktur-und-einstellungen)
2. [Templates](#templates)
3. [Assets](#assets)
4. [Qualität](#qualität)
5. [Die Umstellung](#die-umstellung)

## Struktur und Einstellungen

- [ ] Das Design liegt in einer eigenen Erweiterung, registriert mit `DesignExtensions[]` ([Design-Erweiterungen](design-erweiterungen.md)).
- [ ] `ezinfo.php` und `extension.xml` tragen dieselbe Version, Lizenz und denselben Namen ([ezinfo.php](../erweiterungen/ezinfo-php.md)).
- [ ] Die Design-Kette des Siteaccess ist beabsichtigt und kurz ([Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md)).
- [ ] Override-Regeln haben eindeutige Abschnittsnamen mit Präfix und, wo die Reihenfolge zählt, eine `Priority`.
- [ ] Keine Knoten-ids in Einstellungen oder Overrides, die sich zwischen Installationen unterscheiden; stattdessen Remote-ids
  oder Klassen.
- [ ] Keine Geheimnisse in der Erweiterung (Schlüssel, Passwörter, Hostnamen von Datenbanken).

## Templates

- [ ] `./console exp:eztemplatecheck <design>/templates/` meldet jede Datei als gültig ([Ein Design debuggen](ein-design-debuggen.md)).
- [ ] Jeder ausgegebene Wert ist maskiert ([Wash und Maskierung](../templates/wash-und-maskierung.md)).
- [ ] Jeder Text der Oberfläche läuft über `i18n` mit dem Kontext des Themes.
- [ ] Jedes Listen-fetch hat ein Limit; kein fetch in einer Schleife.
- [ ] Cache-Blöcke haben die richtigen Schlüssel; nichts Benutzerspezifisches in Templates im View-Cache ([Caching für Designer](caching-fuer-designer.md)).
- [ ] Kein `<style>`, kein `style=""`, keine Inline-Skripte oder Handler-Attribute ([Checkliste: CSP](checkliste-csp.md)).

## Assets

- [ ] Ein einziges `ezcss_load`, Skripte am Ende des Body oder verzögert ([Stylesheets laden](../../../../eng-us/help/designers/theme-design/loading-stylesheets.md)).
- [ ] Schriften selbst gehostet, Lizenzen mitgeliefert ([Schriften in einem Design](schriften-in-einem-design.md)).
- [ ] Favicons und Touch-Icons ([Favicons und Touch-Icons](favicons-und-touch-icons.md)).
- [ ] Bildaliase für die angezeigten Größen definiert ([Bildaliase](bildaliase.md)).
- [ ] Keine Anfrage an Drittanbieter vor der Einwilligung ([Externe Ressourcen und CDNs](externe-ressourcen-und-cdns.md)).

## Qualität

- [ ] [Checkliste: Barrierefreiheit](checkliste-barrierefreiheit.md) bestanden.
- [ ] [Checkliste: dunkler Modus](checkliste-dunkelmodus.md) bestanden (falls das Design einen hat).
- [ ] [Checkliste: rechts nach links](checkliste-rechts-nach-links.md) bestanden (falls die Website solche Sprachen hat).
- [ ] [Checkliste: Performance](checkliste-leistung.md) bestanden.
- [ ] Jeder Seitentyp bei 1440 px, 960 px mit Skalierung 2 und 375 px geprüft.
- [ ] Die Druckvorschau eines Artikels ist lesbar.
- [ ] Fehlerseiten (nicht gefunden, kein Zugriff) und die Suchseite sind gestaltet.

## Die Umstellung

- [ ] Auf einem Vorschau-Siteaccess mit echten Inhalten getestet ([Ein Design auf einem Siteaccess in der Vorschau ansehen](../../../../eng-us/help/designers/theme-design/previewing-a-design-on-a-siteaccess.md)).
- [ ] Entwicklungseinstellungen auf dem Live-Siteaccess aus (`DevelopmentMode`, `ShowTemplatePathComments`, Debug-Ausgabe).
- [ ] Caches nach der Umstellung geleert, einschließlich Packer ([Der Packer-Cache und Deploys](../../../../eng-us/help/designers/theme-design/packer-cache-and-deploy.md)).
- [ ] Die meistbesuchten Seiten direkt nach der Umstellung abgemeldet geöffnet.
- [ ] Ein Weg zurück: die alten Werte von `SiteDesign` notiert.

## Verwandte Seiten

- [Checkliste: Code-Review für Templates](checkliste-code-review-fuer-templates.md)
- [Checkliste: Die Theme-Erweiterung ist bereit](../erweiterungen/checkliste-theme-erweiterung-bereit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
