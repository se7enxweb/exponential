---
title: "Lernpfad: Administrator"
audience: student
category: learning
order: 6
summary: Ein schrittweiser Lernpfad für angehende Website-Administratoren, aufgebaut aus vorhandenen Hilfeseiten und Anleitungen - Installieren, Siteaccesses und Einstellungen, Benutzer und Rollen, Sektionen, Sprachen, Caches, Cronjobs, Logs, Sicherheit, Sicherungen und Updates -, mit einer Übungsaufgabe und einer Prüfung für jeden Schritt.
keywords: lernpfad, administrator, website-administration, installieren, rollen, richtlinien, sektionen, sprachen, caches, cronjobs, logs, sicherheit, upgrade, schwerpunkt, learning path, site administration, install, roles, policies, sections, languages, security, track
type: help
---
# Lernpfad: Administrator

Dieser Pfad ist für Lernende, die eine Exponential-Website betreiben werden: sie installieren, entscheiden, wer was
darf, sie schnell und sicher halten und sie aktualisieren. Schließen Sie zuerst den [Redakteurpfad](lernpfad-redakteur.md)
ab. Sie brauchen ein Terminal auf dem Rechner Ihrer Lerninstallation.

Jeder Befehl unten wird im Wurzelverzeichnis der Installation ausgeführt. Die Konsole ist `php bin/php/console`; viele
Installationen legen eine Abkürzung an, sodass auch `./console` funktioniert, und viele Hilfeseiten schreiben es so.
Wenn Sie als `root` arbeiten, fügen Sie `--allow-root-user` hinzu, wo ein Skript es verlangt
([Exponential 6.0 installieren](../../INSTALL.md)).

## Inhalt

1. [Die Schritte](#die-schritte)
2. [Weiterführend](#weiterführend)
3. [Wohin der Pfad als Nächstes führt](#wohin-der-pfad-als-nächstes-führt)

## Die Schritte

### Schritt 1: Über die Kommandozeile installieren (2 Stunden)

- Lesen: [Erste Schritte: Exponential installieren](../../anleitungen/erste-schritte.md),
  [Exponential 6.0 installieren](../../INSTALL.md), Abschnitte 1 bis 4.
- Üben: Installieren Sie eine Lernwebsite mit `exp:install` und liefern Sie sie mit der Engine `php` aus
  ([Eine Lernwebsite mit einem Befehl installieren](eine-lernwebsite-mit-einem-befehl-installieren.md)).
- Prüfen: Welche drei Siteaccesses hat jede Installation? (`site`, `admin` und `editor`.)

### Schritt 2: Einstellungen und Siteaccesses (2 Stunden)

- Lesen: [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md),
  [Wie INI-Einstellungen funktionieren](../erweiterungsentwicklung/teil-2-builder/wie-ini-einstellungen-funktionieren.md),
  [exp:ini](../../funktionen/6.0/exp-ini-befehl.md).
- Üben: Führen Sie `php bin/php/console exp:ini where site/DesignSettings/SiteDesign site` aus und erklären Sie jede
  Zeile der Ausgabe.
- Prüfen: Warum sollten Sie `settings/site.ini` selbst niemals bearbeiten?

### Schritt 3: Benutzer, Gruppen, Rollen und Richtlinien (3 Stunden)

- Lesen: [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md),
  [Wie gebe ich einer Gruppe Bearbeitungsrechte für eine Sektion?](../handbuch/wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md).
- Üben: [Projekt 9](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md).
- Prüfen: Was bewirkt eine Rolle, bevor sie zugewiesen ist? (Nichts.)

### Schritt 4: Sektionen und Objektzustände (1 Stunde)

- Lesen: [Sektionen](../../anleitungen/sektionen.md), [Objektzustände](../../anleitungen/objektzustaende.md).
- Üben: Legen Sie eine Sektion an und weisen Sie ihr einen Teilbaum Ihrer Lernwebsite zu.

### Schritt 5: Sprachen (2 Stunden)

- Lesen: [Inhaltssprachen](../../anleitungen/inhaltssprachen.md), Abschnitte 1 bis 5.
- Üben: [Projekt 5](projekt-05-eine-mehrsprachige-website.md).
- Prüfen: Was bewirkt das Hinzufügen einer Sprache nicht? (Es übersetzt nichts und ändert keinen Siteaccess.)

### Schritt 6: Caches (1 Stunde)

- Lesen: [Caches verstehen](caches-verstehen.md), [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md),
  [Caches](../../anleitungen/caches.md).
- Üben: Veröffentlichen Sie ein Element, finden Sie es in einer Liste und leeren Sie dann nur den Inhalts-Cache mit
  `--clear-tag=content`.

### Schritt 7: Cronjobs und Workflows (1 Stunde)

- Lesen: [Cronjobs](../../anleitungen/cronjobs.md), [Workflows](../../anleitungen/workflows.md).
- Üben: Listen Sie die Cronjob-Teile mit `php bin/php/console list cron` auf.

### Schritt 8: Logs, Fehler und das Audit-Protokoll (1 Stunde)

- Lesen: [Logs und Fehler lesen](logs-und-fehler-lesen.md), [Wie lese ich die Logdateien?](../handbuch/wie-lese-ich-die-logdateien.md),
  [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md).
- Üben: Öffnen Sie `var/log/error.log` nach einer Seite mit einem absichtlichen Template-Fehler und beheben Sie ihn
  dann.

### Schritt 9: Sicherheit (2 Stunden)

- Lesen: [Sicherheitshärtung](../sicherheitshaertung.md), [Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md),
  [Das Administratorpasswort ändern](../administratorpasswort-aendern.md).
- Üben: Schalten Sie die Anmeldung in zwei Schritten für ein Testkonto ein
  ([Wie schalte ich die Anmeldung in zwei Schritten ein?](../handbuch/wie-schalte-ich-die-zweistufige-anmeldung-ein.md)).

### Schritt 10: Pakete, Erweiterungen und Updates (2 Stunden)

- Lesen: [Site-Pakete für Lernende](site-pakete-fuer-lernende.md), [Erweiterungen](../../anleitungen/extensions.md),
  Abschnitte 1 bis 5, [Mit Git aktualisieren](../mit-git-aktualisieren.md), [Upgrade](../../anleitungen/aktualisieren.md).
- Üben: Öffnen Sie **Setup > Extensions** und finden Sie drei aktive Erweiterungen; lesen Sie, was jede davon tut.
- Prüfen: Was kommt bei einem Update zuerst, Git oder Composer? (Zuerst Git, dann Composer, wie
  [Mit Git entwickeln](../mit-git-entwickeln.md#auf-dem-neuesten-stand-bleiben) sagt.)

## Weiterführend

| Thema | Seiten |
|---|---|
| Andere Datenbanken als SQLite | [Datenbankunterstützung](../datenbankunterstuetzung.md), [Datenbanken](../../installation/09-datenbanken.md) |
| Eine echte Website ausliefern | [Die Website ausliefern](../../installation/08-die-website-ausliefern.md), [Bereitstellen](../../anleitungen/bereitstellung.md) |
| Suche | [Projekt 6](projekt-06-eine-suche-die-das-richtige-findet.md), der Kurs [Suche](../trainer/suche/README.md) der Trainer |
| Mehrere Websites in einer Installation | [Site-Pakete: hier beginnen](../seitenpakete/README.md), [Multisite und Site-Pakete](../trainer/multisite-und-pakete/README.md) |
| Arbeit beim Hosting | [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md) |
| Täglicher Betrieb | [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md) |

## Wohin der Pfad als Nächstes führt

- Lernprojekte: [1](projekt-01-eine-eigene-inhaltsklasse.md), [2](projekt-02-eine-kleine-website-struktur.md),
  [5](projekt-05-eine-mehrsprachige-website.md), [6](projekt-06-eine-suche-die-das-richtige-findet.md),
  [9](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md), [Abschlussprojekt](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md).
- Mit einem Trainer: [Website-Administration](../trainer/website-administratoren/README.md), dann
  [Hosting und Betrieb](../trainer/hosting-betrieb/README.md).
- Nächster Pfad: [Entwickler](lernpfad-entwickler.md).

## Verwandte Seiten

- [Einen Lernpfad wählen](einen-lernpfad-waehlen.md)
- [Caches verstehen](caches-verstehen.md)
- [Checklisten für Lernende](checklisten-fuer-lernende.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
