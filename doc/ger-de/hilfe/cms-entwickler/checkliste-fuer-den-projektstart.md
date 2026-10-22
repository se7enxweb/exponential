---
title: Checkliste für den Projektstart
audience: cms-developer
category: cms-development
order: 46
summary: Für CMS-Entwickler, die eine Website live schalten, eine Checkliste von einer Woche vor dem Start bis zur Woche danach. Sie behandelt Inhalte und Modell, Templates und Design, Einstellungen, Server und Caches, Cronjobs, Sicherheit, Suche, SEO und Weiterleitungen, Barrierefreiheit, Sicherungen, die Übergabe an Redakteure und was Sie nach dem Start beobachten.
keywords: Start, Live-Gang, Checkliste für den Live-Gang, Checkliste für den Start, Produktion, Übergabe, Umstellung, Bereitstellung, Cronjobs, Sicherungen, Debug-Ausgabe, Weiterleitungen, Sitemap, Überwachung, Redakteure, Schulung, nach dem Start, launch, go live, go-live checklist, launch checklist, production, handover, cut-over, deployment, backups, monitoring, after launch
type: help
---
# Checkliste für den Projektstart

Diese Seite richtet sich an Entwickler, die eine Website gleich live schalten. Kopieren Sie sie in Ihr Projekt, haken
Sie jeden Punkt ab und notieren Sie, wer ihn geprüft hat. Jeder Punkt verweist auf die Seite, die ihn erklärt. Hoster
haben eine eigene Liste für die Serverseite
([Live-Gang der Website eines neuen Kunden](../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md)).

## Inhalt

1. [Eine Woche vorher](#eine-woche-vorher)
2. [Inhalte und Modell](#inhalte-und-modell)
3. [Templates und Design](#templates-und-design)
4. [Einstellungen](#einstellungen)
5. [Server, Caches und Cron](#server-caches-und-cron)
6. [Sicherheit](#sicherheit)
7. [Suche, SEO und Weiterleitungen](#suche-seo-und-weiterleitungen)
8. [Barrierefreiheit](#barrierefreiheit)
9. [Sicherungen und Übergabe](#sicherungen-und-übergabe)
10. [Der Starttag](#der-starttag)
11. [Die Woche danach](#die-woche-danach)

## Eine Woche vorher

- [ ] Der Live-Rechner ist so bereitgestellt, wie er laufen wird: dieselbe Server-Engine, dasselbe PHP, dieselbe
      Datenbank-Engine wie auf Staging ([Eine Website bereitstellen](eine-website-bereitstellen.md)).
- [ ] Die Repositorys des Projekts sind mit einem Tag versehen oder beim Commit festgehalten, der live geht.
- [ ] Es gibt einen Plan für den Rückweg: Datenbank-Dump, vorheriger Code, die Schritte und wer entscheidet.

## Inhalte und Modell

- [ ] Das Inhaltsmodell hat seine Prüfung bestanden
      ([Checkliste zur Prüfung des Inhaltsmodells](checkliste-zur-pruefung-des-inhaltsmodells.md)).
- [ ] Klassen und Starterinhalte liegen in Paketen im Repository; Layouts sind exportiert
      ([Seitenpakete und Installer](seitenpakete-und-installer.md)).
- [ ] Test- und Demo-Inhalte entfernt oder verborgen; der Papierkorb von Testelementen geleert.
- [ ] Importierte Inhalte von einem Redakteur stichprobenartig geprüft; innere Links und Bilder aufgelöst
      ([Inhalte importieren](inhalte-importieren.md)).

## Templates und Design

- [ ] Jede Klasse hat eine Full View und die Listenansichten, die sie braucht; keine allgemeinen Attributlisten auf
      öffentlichen Seiten ([Ansichtsmodi](ansichtsmodi.md)).
- [ ] Fehlerseiten (404, Zugriff verweigert) gestaltet ([Fehler-Templates](../designer/vorlagen/fehler-templates.md)).
- [ ] Kein Inline-CSS oder -JavaScript; Assets über den Packer; kein `?v=` an Asset-Links.
- [ ] Seiten im hellen und dunklen Modus geprüft, bei 1440 px und bei 200 % Zoom.

## Einstellungen

- [ ] Debug-Ausgabe und `ShowUsedTemplates` aus; `TemplateCompile` an.
- [ ] Namen der Website, `MetaDataArray`, Adressen der Website und E-Mail-Absender für Live gesetzt; Test-E-Mails gehen
      bis zum Start nur an `example.invalid`.
- [ ] `settings/override/` auf dem Rechner geschrieben, nicht kopiert; Varianten je Umgebung vorhanden
      ([Einstellungen und INI für CMS-Entwickler](einstellungen-und-ini-fuer-cms-entwickler.md)).
- [ ] `ManagedByDeployment=enabled`, wo die Konfiguration aus der Versionsverwaltung kommt.

## Server, Caches und Cron

- [ ] Velocity (oder der gewählte Server) läuft mit OPcache; `exp:cache status` zeigt die erwarteten Ebenen
      ([Leistung](leistung.md)).
- [ ] Benchmark der wichtigsten Seiten als Ausgangsbasis festgehalten (`./console exp:benchmark`).
- [ ] Cronjobs für jeden öffentlichen Siteaccess eingeplant; Workflows, Benachrichtigungen und verzögertes Indexieren
      bei laufendem Cron getestet ([Cronjobs](cronjobs.md)).
- [ ] Caches nach der letzten Bereitstellung vorgewärmt ([Caches vorladen](../../anleitungen/caches-vorwaermen.md)).

## Sicherheit

- [ ] Die [Sicherheits-Checkliste](sicherheits-checkliste.md) ist vollständig, einschließlich der Härtung vor dem Live-Gang.
- [ ] Das Administratorpasswort des Installers geändert; persönliche Administratorkonten mit Zwei-Faktor-Anmeldung.
- [ ] Rollen mit Testkonten getestet; die Rolle Anonymous geprüft.

## Suche, SEO und Weiterleitungen

- [ ] Der Suchindex nach dem letzten Import neu aufgebaut; die Suchseite getestet ([Suche](suche.md)).
- [ ] Weiterleitungen für alte Adressen eingerichtet; kanonische Links, Titel und Beschreibungen geprüft
      ([SEO-Grundlagen](seo-grundlagen.md)).
- [ ] `robots.txt` hat die Live-Regeln und die Adresse der Sitemap; keine übrig gebliebene Regel sperrt die ganze
      Website.

## Barrierefreiheit

- [ ] Tastatur, Zoom und Kontrast auf den wichtigsten Templates getestet; Formulare beschriftet
      ([Barrierefreiheit](barrierefreiheit.md)).

## Sicherungen und Übergabe

- [ ] Sicherungen von Datenbank und Speicher laufen, verlassen den Rechner, und eine Wiederherstellung wurde getestet
      ([Was zu sichern ist](../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
- [ ] Redakteure geschult und mit eigenen Konten versorgt; eine kurze Anleitung zu den Klassen und Layouts der Website
      übergeben ([Editors 101](../trainer/redakteure-101/README.md)).
- [ ] Die Projektdokumentation (Modell, Erweiterungen, Bereitstellung, Cron, Kontakte) liegt im Repository.

## Der Starttag

1. Frieren Sie Inhaltsänderungen auf der alten Website ein; führen Sie bei einer Migration einen letzten Delta-Import
   aus.
2. Stellen Sie bereit, schalten Sie DNS oder den Proxy um, prüfen Sie HTTPS.
3. Prüfen Sie die Startseite, eine tiefe Seite je Klasse, die Suche, ein Formular, die Anmeldung und die
   Administration.
4. Beobachten Sie in den ersten Stunden `var/log/error.log` und die 404-Fehler.

## Die Woche danach

- [ ] 404-Liste täglich geprüft; fehlende Weiterleitungen ergänzt.
- [ ] Fehlerlog und Audit-Warnungen gelesen.
- [ ] Cron-Läufe auf der Seite Cronjobs bestätigt.
- [ ] Benchmark unter echtem Verkehr wiederholt und mit der Ausgangsbasis verglichen.
- [ ] Rückmeldungen der Redakteure gesammelt und die ersten Korrekturen geplant.

## Verwandte Seiten

- [Checkliste für die Migration](../../installation/migration/checkliste.md)
- [Checkliste für den Live-Gang aus der Sicherheitshärtung](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang)
- [Checkliste: Start eines Themes](../designer/theme-design/checkliste-theme-start.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
