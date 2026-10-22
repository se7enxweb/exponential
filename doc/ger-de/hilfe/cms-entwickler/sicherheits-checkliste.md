---
title: Sicherheits-Checkliste
audience: cms-developer
category: cms-development
order: 43
summary: Für CMS-Entwickler die Sicherheitsarbeit eines Website-Projekts als Checklisten. Die Seite behandelt Inhaltsmodell und Rollen, Templates und Ausgaben, Formulare und Uploads, Ihren eigenen Code, APIs, Konfiguration und Bereitstellung sowie die Härtung vor dem Live-Gang. Jeder Punkt verweist auf die Seite, die ihn erklärt.
keywords: Sicherheit, Checkliste, Härtung, Rollen, geringste Rechte, wash, Maskierung, XSS, CSRF, ezformtoken, Formular-Token, Content Security Policy, CSP, SQL-Injection, eZDB escapeString, Uploads, API-Schlüssel, RequireHTTPS, Debug-Ausgabe, Zwei-Faktor, Audit-Trail, Anfrageregeln, security, checklist, hardening, least privilege, escaping, form token, uploads, API keys, debug output, two-factor, audit trail, request rules
type: help
---
# Sicherheits-Checkliste

Diese Seite richtet sich an Entwickler, die Websites bauen und sie sicher übergeben müssen. Exponential ist an vielen
Stellen standardmäßig sicher, und ein Projekt kann das trotzdem mit einer unbedachten Rolle, einem Template oder einer
Einstellung zunichtemachen. Gehen Sie diese Listen während der Entwicklung und noch einmal vor dem Start durch; die
Anleitung zur Härtung erklärt die Angriffe hinter jedem Punkt ([Sicherheitshärtung](../sicherheitshaertung.md)).

## Inhalt

1. [Inhaltsmodell und Zugriff](#inhaltsmodell-und-zugriff)
2. [Templates und Ausgaben](#templates-und-ausgaben)
3. [Formulare und Uploads](#formulare-und-uploads)
4. [Ihr eigener Code](#ihr-eigener-code)
5. [APIs](#apis)
6. [Konfiguration und Bereitstellung](#konfiguration-und-bereitstellung)
7. [Konten](#konten)
8. [Härtung vor dem Live-Gang](#härtung-vor-dem-live-gang)
9. [Beobachten und Melden](#beobachten-und-melden)

## Inhaltsmodell und Zugriff

- [ ] Die Rolle Anonymous liest nur, was Besucher lesen dürfen (`content/read` auf den öffentlichen Sektionen), und
      darüber hinaus nichts außer `user/login` und dem, was die Formulare der Website brauchen.
- [ ] Inhalte nur für Mitglieder liegen in einer eigenen Sektion, die eine der Mitgliedergruppe zugewiesene Rolle
      liest, statt nur verborgen zu sein.
- [ ] Redakteursrollen sind nach Klasse und Teilbaum eingeschränkt; keine Redakteursrolle hat `setup/*`, `role/*` oder
      `user/*` über den Bedarf hinaus.
- [ ] Rollen sind Gruppen zugewiesen; Mitglieder und Personal sind in verschiedenen Gruppen.
- [ ] Der Zugriff wurde mit Testkonten unter `example.invalid` getestet, und `./console exp:accesscheck` erklärt jede
      Überraschung ([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)).

## Templates und Ausgaben

- [ ] Jeder Wert, den Sie selbst ausgeben, läuft durch `|wash()`; Ausgaben von Datentypen kommen aus
      `attribute_view_gui` ([Wash und Maskierung](../designer/vorlagen/wash-und-maskierung.md)).
- [ ] Keine `<style>`-Blöcke, `style=""`-Attribute, Inline-Skripte oder `javascript:`-Links in Templates, damit eine
      Content Security Policy durchgesetzt werden kann ([CSP-sicheres Design](../designer/theme-design/csp-sicheres-design.md),
      [Content Security Policy](../../funktionen/6.0/content-security-policy.md)).
- [ ] Redakteure können kein Markup speichern: Rich Text hat kein Tag für Skripte; eigene Tags statt eingefügtem HTML
      ([Rich Text](rich-text.md)).
- [ ] Keine besucherbezogenen Daten in gemeinsamen Caches
      ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md#besucherbezogene-ausgaben)).

## Formulare und Uploads

- [ ] `ezformtoken` ist aktiv, und jedes Formular behält die verborgenen Felder, die das Original-Template ausgibt.
- [ ] Öffentliche Formulare haben einen Captcha-Datentyp; gesammelte Antworten werden nach Plan entfernt
      ([Formulare und Informationssammlung](formulare-und-informationssammlung.md)).
- [ ] Upload-Typen sind auf das beschränkt, was Redakteure brauchen; Dateiattribute haben eine Höchstgröße.
- [ ] Die Selbstregistrierung ist nur dort eingeschaltet, wo sie nötig ist, mit Prüfung der E-Mail-Adresse
      ([Benutzer und Registrierung](benutzer-und-registrierung.md)).

## Ihr eigener Code

- [ ] Jede Modul-View deklariert eine Richtlinienfunktion und prüft sie; Schreibzugriffe brauchen POST und das
      Formular-Token.
- [ ] SQL läuft über `eZPersistentObject` oder `eZDB` mit maskierten Werten (`escapeString()`) und auf Ganzzahlen
      umgewandelten IDs ([eZDB](../erweiterungsentwicklung/teil-8-referenz/ezdb.md)).
- [ ] Weiterleitungen gehen nur zu Adressen der Website ([Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)).
- [ ] Inhalte werden über die Kernel-API angelegt, als eigener Benutzer mit genau den nötigen Rechten
      ([Inhalte aus PHP](inhalte-aus-php.md)).
- [ ] Die Regeln des Buchs sind erfüllt:
      [Sicherheit in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md).

## APIs

- [ ] `rest.ini [Authentication] RequireAuthentication=enabled` (mitgeliefert) und `RequireHTTPS=enabled`.
- [ ] Ein Konto je Client, mit einer auf den Bedarf eingeschränkten Rolle; API-Schlüssel mit Scopes und Ablaufdatum
      ([Die REST-API](die-rest-api.md), [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)).
- [ ] Schlüssel und Tokens liegen in Umgebungsvariablen oder Geheimnisspeichern, nie in Repositorys.

## Konfiguration und Bereitstellung

- [ ] Debug-Ausgabe auf Staging und Live aus oder auf Ihre Adresse beschränkt; PHP `display_errors=Off`.
- [ ] `settings/override/` liegt in keinem Repository und wird nicht zwischen Rechnern kopiert.
- [ ] Code ist für den Benutzer der Website nur lesbar; Worker und Cronjobs laufen nie als root.
- [ ] `ManagedByDeployment=enabled`, wo die Konfiguration aus der Versionsverwaltung kommt
      ([Eine Website bereitstellen](eine-website-bereitstellen.md)).
- [ ] Die Rewrite-Regeln des Webservers verhindern, dass `settings/`, Interna von `var/` und Quelldateien ausgeliefert
      werden ([Sicherheitshärtung, Kapitel 13](../../installation/13-sicherheitshaertung.md)).

## Konten

- [ ] Das Administratorpasswort des Installers geändert und `var/log/initial-admin-password` entfernt
      ([Das Administratorpasswort ändern](../administratorpasswort-aendern.md)).
- [ ] Ein persönliches Administratorkonto je Person; Zwei-Faktor-Anmeldung für Administratoren
      ([Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Passwortregeln entschieden (`MinPasswordLength`, `WeakPasswordCheck`, `[PasswordSettings]`).
- [ ] Demomodus aus (`EXP_DEMO_MODE` nicht gesetzt) ([Demomodus](../demomodus.md)).

## Härtung vor dem Live-Gang

- [ ] Überall HTTPS, HSTS, sichere Cookies.
- [ ] Die Administration auf einem eigenen Host oder per Netzwerk eingeschränkt.
- [ ] Anfrageregeln oder der Request Shield davor, zuerst beobachtend, dann durchsetzend
      ([Anfrageregeln](../../funktionen/6.0/anfrageregeln.md)).
- [ ] Die Liste Einstellung für Einstellung: [Checkliste für den Live-Gang](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang).

## Beobachten und Melden

- [ ] Audit-Warnungen gehen an eine Person, und der Trail verlässt den Rechner
      ([Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)).
- [ ] Logs werden wöchentlich gelesen; Sicherungen liegen außerhalb des Rechners, und eine Wiederherstellung wurde
      getestet.
- [ ] Aktualisierungen von Exponential und seinen Erweiterungen werden verfolgt und getestet.
- [ ] Ihr Team weiß, wie eine Schwachstelle gemeldet wird
      ([Eine Schwachstelle melden](../sicherheitshaertung.md#eine-schwachstelle-melden)).

## Verwandte Seiten

- [Sicherheitsstandards](../../standards/sicherheit.md)
- [Checkliste zur Sicherheitsprüfung für Hoster](../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
