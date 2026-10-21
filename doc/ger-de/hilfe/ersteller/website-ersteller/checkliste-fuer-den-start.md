---
title: Checkliste für den Start
audience: website-creator, project-manager
category: website-creation
order: 660
summary: Alles, was vor dem Livegang einer Website zu prüfen ist - Inhalte, Design, Barrierefreiheit, SEO, Formulare und Mail, Sicherheit, Leistung, Betrieb und der Tag des Livegangs selbst - jeder Punkt mit der Seite, die ihn erklärt.
keywords: Start, Livegang, Checkliste, Startcheckliste, vor dem Start, Tag des Livegangs, DNS, HTTPS, Backups, Cronjobs, Sicherheit, SEO, Barrierefreiheit, Website-Ersteller, launch, go live, checklist, launch checklist, pre-launch, go-live day, backups, cronjobs, security, accessibility, website creator
type: help
---
# Checkliste für den Start

Diese Seite richtet sich an Website-Ersteller in den letzten Wochen vor dem Start. Arbeiten Sie sie mit den
Verantwortlichen durch; jeder Punkt nennt die Seite, die ihn erklärt. Projektleiter finden die Seite der Abnahme in
[Qualitätstore und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md).

## Inhalte

- [ ] Jede Seite der Sitemap existiert und ist veröffentlicht; Platzhaltertext ist verschwunden.
- [ ] Menüs, Fußbereiche und gemeinsame Layouts sind endgültig ([Menüs und Navigation](menues-und-navigation.md)).
- [ ] Bilder haben Alternativtexte; Downloads zeigen Format und Größe.
- [ ] Rechtliche Seiten (Impressum, Datenschutz, Cookies) sind veröffentlicht und verlinkt.
- [ ] Testinhalte und Testbenutzer sind entfernt oder nicht mehr veröffentlicht.

## Design und Barrierefreiheit

- [ ] Jeder Seitentyp in den Browsern und auf den Geräten getestet, die Ihre Besucher verwenden.
- [ ] Barrierefreiheit getestet ([Barrierefreiheit testen](barrierefreiheit-testen.md)).
- [ ] Fehlerseiten (nicht gefunden, Zugriff verweigert) sind gestaltet und liefern den richtigen Status.
- [ ] Druckansicht und Siteaccesses mit Schreibrichtung von rechts nach links geprüft, falls vorhanden.

## SEO

- [ ] Titel und Beschreibungen auf jedem Seitentyp ([Metadaten und Teilen in sozialen Medien](metadaten-und-teilen-in-sozialen-medien.md)).
- [ ] `robots.txt` angepasst, Sitemap erzeugt und eingetragen ([robots.txt für Website-Ersteller](robots-txt.md),
  [Sitemaps](sitemaps.md)).
- [ ] Weiterleitungen von der alten Website getestet
  ([Weiterleitungen und verschobene Inhalte](weiterleitungen-und-verschobene-inhalte.md)).
- [ ] Ein Staging-Host ist für Suchmaschinen nicht erreichbar.

## Formulare, Mail und Funktionen

- [ ] Echter Mailtransport gesetzt, Absenderdomain eingerichtet; eine Mail zum Zurücksetzen des Passworts kommt an
      ([10.4 Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)).
- [ ] Jedes Formular abgemeldet getestet; Einsendungen kommen an; Spamschutz ist eingeschaltet.
- [ ] Newsletter mit dem Datei-Transport geprobt, bevor auf echte Mail umgestellt wird
  ([Newsletter versenden](newsletter-versenden.md)).
- [ ] Shop: eine vollständige Testbestellung mit echter Zahlung im Testmodus
  ([Warenkorb, Kasse und Bestellungen](warenkorb-kasse-und-bestellungen.md)).
- [ ] Die Suche findet die wichtigsten Seiten ([Suche auf der Website](suche-auf-der-website.md)).

## Sicherheit

- [ ] Das Administratorpasswort ist geändert und persönlich
  ([Das Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- [ ] Härtung durchgeführt ([Sicherheitshärtung](../../sicherheitshaertung.md)).
- [ ] Die Rolle Anonymous gewährt nur, was Besucher brauchen ([Rechte für Besucher](rechte-fuer-besucher.md)).
- [ ] `[HTMLForms] Secret` gesetzt; Debug-Ausgabe auf öffentlichen Siteaccesses aus.
- [ ] HTTPS auf jedem Host, HTTP leitet auf HTTPS weiter ([Livegang: Hosts und HTTPS](livegang-hosts-und-https.md)).
- [ ] Die Content Security Policy auf der Website mindestens im Modus report-only
  ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).

## Leistung

- [ ] Caches an; statischer Cache aus; Antwort-Cache von Velocity an, wenn Velocity die Website ausliefert
      ([Leistung und Caching](leistung-und-caching.md)).
- [ ] Eine Benchmark-Basislinie gespeichert.
- [ ] Bildaliasse auf das Design abgestimmt.

## Betrieb

- [ ] Cron führt `frequent`, den Standardteil, `infrequent` und `cache_cleanup` aus
  ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Ein Backup wurde erstellt und auf einem anderen Rechner wiederhergestellt
  ([Backups und Wiederherstellungen](backups-und-wiederherstellungen.md)).
- [ ] Logs werden rotiert; jemand liest das Fehlerlog ([Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md)).
- [ ] Wen man anruft, wenn die Website ausfällt, ist schriftlich festgehalten.
- [ ] Die Redaktion ist geschult ([Die Website an die Redaktion übergeben](die-website-an-die-redaktion-uebergeben.md)).

## Tag des Livegangs

1. Inhaltsänderungen auf der alten Website einfrieren.
2. Ein Backup der neuen Website erstellen.
3. DNS oder den Proxy auf die neue Website umstellen; die alte weiterlaufen lassen, bis sich das DNS verbreitet hat.
4. Startseite, eine tief liegende Seite, ein Formular und die Administration unter den öffentlichen Adressen prüfen.
5. Den Inhalts- und den Antwort-Cache leeren, sobald die neuen Adressen antworten.
6. Die Sitemap bei den Suchmaschinen einreichen.
7. In den ersten Tagen das Fehlerlog und die 404-Fehler beobachten.

## Verwandte Seiten

- [Livegang: Hosts und HTTPS](livegang-hosts-und-https.md)
- [Projektvorlagen](../../fuehrung/projektvorlagen.md)
- [Eine Checkliste für die erste Woche](../../../installation/10-nach-der-installation.md#1015-eine-checkliste-für-die-erste-woche)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
