---
title: Gute Issues schreiben
audience: developer, designer, app-creator, user
category: extension-development
order: 100
summary: Melden Sie einen Fehler oder wünschen Sie sich eine Funktion so, dass ein Maintainer damit arbeiten kann - Versionen, Schritte zum Nachstellen, erwartetes und tatsächliches Ergebnis, Protokolle ohne Geheimnisse - mit einer Vorlage und dem, was Sie weglassen sollten.
keywords: Issue, Fehlerbericht, Funktionswunsch, GitHub-Issues, nachstellen, Versionen, Protokolle, Vorlage, Sicherheit, Geheimnisse, issue, bug report, feature request, GitHub issues, reproduce, versions, logs, template, security, secrets
type: help
---
# Gute Issues schreiben

Teil 5, Kapitel 10. Nicht jeder Beitrag ist Code. Ein klarer Fehlerbericht ist oft mehr wert als ein schneller Patch,
weil er dem Maintainer erlaubt, die Ursache statt eines Symptoms zu beheben. Dieses Kapitel zeigt, was ein Maintainer
von einem Issue braucht und was niemals darin stehen darf.

## Bevor Sie schreiben

1. **Suchen** Sie in den Issues des Repositorys, offenen wie geschlossenen, nach der Fehlermeldung oder der Funktion.
2. **Aktualisieren Sie.** Prüfen Sie, ob das Problem mit dem neuesten Release der Erweiterung und von Exponential noch
   besteht.
3. **Machen Sie es klein.** Finden Sie den kürzesten Weg, es auszulösen. Ein Problem, das Sie in vier Schritten zeigen
   können, wird behoben.
4. **Ist es ein Sicherheitsproblem?** Dann eröffnen Sie kein öffentliches Issue. Folgen Sie der Sicherheitsrichtlinie
   des Projekts (die des Kernels steht in `SECURITY.md` im Wurzelverzeichnis seines Repositorys) und melden Sie es
   vertraulich.

## Ein Fehlerbericht

```text
Title: Change content class entry does nothing under an enforced Content Security Policy

Versions
- Exponential 6.0.15, PHP 8.3, Apache with PHP-FPM, SQLite
- expchangeclass 1.0.8
- Design: admin4

Steps
1. Set site.ini [ContentSecurityPolicy] Mode=enforce for the admin siteaccess.
2. Right-click a node in the content tree.
3. Click "Change content class".

Expected
The class selection page opens.

Actual
Nothing happens. The browser console shows:
"Refused to execute inline event handler because it violates the following Content Security Policy directive ..."

Notes
Works with Mode=report-only. The entry has an onclick attribute in popupmenu/classcontextmenu.tpl.
```

Jeder Teil hat einen Zweck:

| Teil | Warum |
|---|---|
| Ein Titel, der das Symptom und die Bedingung nennt | der Maintainer kann es von zwanzig anderen unterscheiden |
| Versionen | die Hälfte aller Fehler hängt von einer ab |
| Schritte von einem bekannten Zustand aus | der Maintainer kann es mit eigenen Augen sehen |
| Erwartet und tatsächlich | manchmal ist der „Fehler“ beabsichtigt, und das zeigt die Meinungsverschiedenheit |
| Fehlermeldungen als Text, kopiert | sie lassen sich durchsuchen; ein Screenshot nicht |
| Was Sie schon herausgefunden haben | spart Zeit; es ist in Ordnung, unsicher zu sein |

## Ein Funktionswunsch

Beschreiben Sie das Problem vor der Lösung:

```text
Title: A setting for the number of items on the overview page

Problem
Our editors manage 400 items. The overview shows 10 per page, so finding one takes many clicks.

Proposal
A setting myoldext.ini [OverviewSettings] Limit, default 10, read in modules/myoldext/overview.php.

Alternatives considered
Overriding the template does not help: the limit is in the PHP view.

I can send a pull request if this is welcome.
```

„I can send a pull request“ ist der willkommenste Satz in einem Funktionswunsch.

## Was niemals in ein Issue gehört

Issues sind öffentlich, oft für immer, und werden von Suchmaschinen und Archiven kopiert:

- **Keine Passwörter, Schlüssel oder Tokens**, auch keine „alten“. Wenn Sie versehentlich einen eingefügt haben,
  ändern Sie ihn sofort; das Löschen des Kommentars entfernt keine Kopien.
- **Keine personenbezogenen Daten Ihrer Benutzer oder Kunden**: Namen, E-Mail-Adressen, IP-Adressen, Inhalte ihrer
  Konten. Ersetzen Sie sie durch erfundene Werte (`user@example.invalid`).
- **Keine internen Hostnamen oder Adressen** Ihrer Server.
- **Keine vollständigen Konfigurationsdateien.** Zitieren Sie die wenigen Einstellungen, auf die es ankommt.

Protokolle brauchen dieselbe Sorgfalt: Kürzen Sie sie auf die Zeilen zum Problem und ersetzen Sie alles
Personenbezogene.

## Nachdem Sie es eröffnet haben

- Beantworten Sie Fragen. Ein Issue, dessen meldende Person nicht antwortet, wird meist nach einer Weile geschlossen.
- Wenn Sie einen Workaround oder die Ursache finden, ergänzen Sie sie.
- Wenn sich herausstellt, dass es Ihr Fehler war, sagen Sie es und schließen Sie es. Auch das ist nützlich: Die
  nächste Person, die sucht, findet die Antwort.

## Verwandte Seiten

- Zurück: [Pull-Request-Etikette](pull-request-etikette.md)
- Weiter: [Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](commit-nachrichten.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [About issues](https://docs.github.com/en/issues/tracking-your-work-with-issues/about-issues) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
