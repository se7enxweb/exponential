---
title: Redakteure und Rollen je Abteilung
audience: government, admin
category: public-sector
order: 80
summary: Wie Sie die Redakteure jeder Abteilung in einer gemeinsamen Exponential-Installation nur an ihrer eigenen Site arbeiten lassen - Rollen eingeschränkt nach Teilbaum oder Sektion, ein oder mehrere Siteaccesses der Administration, gemeinsame Medien und die Überprüfung des Zugangs.
keywords: Rollen je Abteilung, Einschränkung auf Teilbaum, Einschränkung auf Sektion, Redakteure, Abteilungen, Siteaccess der Administration, Berechtigungen, Multisite, Trennung, Überprüfung des Zugangs, roles per department, subtree limitation, section limitation, editors, departments, administration siteaccess, permissions, multisite, separation, access review
type: help
---
# Redakteure und Rollen je Abteilung

Diese Seite richtet sich an Administratoren. In einer gemeinsamen Installation kann eine Administration alle Sites
bedienen; nach Teilbaum eingeschränkte Rollen halten jedes Team im Ordner seiner Site
([Mehrere Sites einrichten, Redakteure und die Administration](../../../ersteller/website-ersteller/multisite-einrichten.md#redaktion-und-administration),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)).

## Inhalt

1. [Eine Rolle je Abteilung](#eine-rolle-je-abteilung)
2. [Teilbaum oder Sektion](#teilbaum-oder-sektion)
3. [Gemeinsame Medien](#gemeinsame-medien)
4. [Mehrere Administrationen](#mehrere-administrationen)
5. [Den Zugang überprüfen](#den-zugang-überprüfen)

## Eine Rolle je Abteilung

| Richtlinie | Einschränkung |
|---|---|
| `content/read` | Teilbaum: der Ordner der Abteilung, der Medienordner, gemeinsame Ordner zum Lesen |
| `content/create`, `content/edit` | Teilbaum: der Ordner der Abteilung |
| `content/versionread` | Teilbaum: der Ordner der Abteilung |
| `user/login` | der Siteaccess der Administration |

Weisen Sie die Rolle der Redakteursgruppe der Abteilung zu; oder verwenden Sie eine Rolle und schränken Sie die
Zuweisung nach Teilbaum ein, wenn Sie sie jeder Gruppe zuweisen.

## Teilbaum oder Sektion

| Einschränken nach | Gut für |
|---|---|
| Teilbaum | den Ordner einer Abteilung; folgt dem Baum |
| Sektion | über den Baum verteilte Inhalte, die zusammengehören; steuert auch, was Besucher sehen |

## Gemeinsame Medien

Geben Sie jeder Abteilung einen Unterordner in der Medienbibliothek mit Rechten zum Anlegen und Leserechte auf die ganze
Bibliothek, damit Bilder wiederverwendet, aber nicht von anderen geändert werden können.

## Mehrere Administrationen

Sollen Teams die Sites der anderen gar nicht sehen, sind mehrere Siteaccesses der Administration möglich, jeder mit
eigenen Einstellungen; der Zugang wird dennoch von Rollen entschieden, also schränken Sie auch diese ein.

## Den Zugang überprüfen

- Listen Sie zweimal im Jahr die Rollenzuweisungen je Gruppe auf (**Benutzer > Rollen und Richtlinien**).
- Verwenden Sie **Benutzer > Benutzerkonten prüfen**, um lange nicht genutzte Konten zu finden
  ([Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)).
- Rollenzuweisungen werden im Kanal `access` des Audit-Protokolls festgehalten
  ([Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)).

## Verwandte Seiten

- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen](../../../../anleitungen/sektionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
