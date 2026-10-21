---
title: Personen und Berechtigungen
audience: government, admin, project-manager
category: public-sector
order: 10
summary: Übersicht der Seiten zum Zugang der Beschäftigten auf einer Exponential-Website im öffentlichen Sektor - Rollen, Richtlinien und Einschränkungen, Sektionen, Zustände, geringste Rechte, Rollenentwürfe für typische Teams, Eintritte und Austritte, Kontenpflege, zweistufige Anmeldung, Funktionstrennung und Überprüfung der Zugänge.
keywords: Personen, Berechtigungen, Rollen, Richtlinien, Zugriffskontrolle, geringste Rechte, Rollenentwurf, Eintritt Wechsel Austritt, Zwei-Faktor, Funktionstrennung, Überprüfung der Zugänge, Übersicht, people, permissions, roles, policies, access control, least privilege, role design, joiners movers leavers, two-factor, separation of duties, access review, index
type: help
---
# Personen und Berechtigungen

Dieser Ordner richtet sich an Administratoren, die den Zugang der Beschäftigten zu einer öffentlichen Website einrichten,
und an Führungskräfte, die ihn gegenüber einem Prüfer begründen müssen. Er erklärt das Berechtigungsmodell von
Exponential und bietet fertige Rollenentwürfe für die Teams, die öffentliche Stellen üblicherweise haben, aufgebaut
ausschließlich aus Richtlinienfunktionen, die es im Kernel gibt. Die Vorgehensweisen stehen unter
[Aufgaben zu Personen und Berechtigungen](../../aufgaben/personen-und-berechtigungen/README.md).

## Inhalt
1. [Seiten](#seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Verwandte Seiten](#verwandte-seiten)

## Seiten

| Seite | Beantwortet |
|---|---|
| [Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md) | Wie Exponential entscheidet, wer was tun darf, und wie Sie das dokumentieren. |
| [Sektionen und Teilbaum-Einschränkungen](sektionen-und-teilbaum-einschraenkungen.md) | Wann Sie eine Grenze mit einer Sektion ziehen und wann mit einem Teilbaum. |
| [Objektzustände in Berechtigungen](objektzustaende-in-berechtigungen.md) | Zugriff an eine Phase wie In Prüfung oder Freigegeben knüpfen. |
| [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md) | Sieben Arbeitsregeln und die Rechte, die besondere Sorgfalt verlangen. |
| [Rollenentwurf: das zentrale Web-Team](rollenentwurf-webteam.md) | Website-weite Inhaltsarbeit und Betrieb, ohne Rollen oder Einstellungen. |
| [Rollenentwurf: Redaktionen der Fachbereiche](rollenentwurf-fachredaktionen.md) | Eine Rolle, je Fachbereich mit einer Teilbaum-Einschränkung zugewiesen. |
| [Rollenentwurf: Freigebende](rollenentwurf-freigebende.md) | Was eine Freigebende braucht: das Freigabe-Ereignis, den Posteingang, versionread. |
| [Rollenentwurf: Prüfer](rollenentwurf-pruefende.md) | Nur lesender Zugriff auf das Prüfprotokoll, maskierte Logs und Versionen. |
| [Rollenentwurf: externe Dienstleister und befristet Beschäftigte](rollenentwurf-externe-dienstleister.md) | Zugang, der zu einem Vertrag passt und mit ihm endet. |
| [Eintritt, Wechsel und Austritt](eintritt-wechsel-austritt.md) | Der Lebenszyklus eines Kontos, abgebildet auf Gruppen, Deaktivieren sowie Deaktivieren und verschieben. |
| [Pflege der Benutzerkonten](pflege-von-benutzerkonten.md) | Ruhende, gesperrte, verwaiste und gemeinsam genutzte Konten; Sperre; eindeutige Anmeldenamen. |
| [Zweistufige Anmeldung für Beschäftigte](zwei-faktor-anmeldung-fuer-beschaeftigte.md) | Einführung von Authenticator-Codes und Umgang mit verlorenen Telefonen. |
| [Funktionstrennung](funktionstrennung.md) | Erstellen, Freigabe, Konten, Administration und Prüfung aufteilen. |
| [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md) | Eine regelmäßige Überprüfung mit Rollenliste, Kontenliste und Prüfprotokoll. |

## Lesereihenfolge

1. [Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md), dann
   [Sektionen und Teilbaum-Einschränkungen](sektionen-und-teilbaum-einschraenkungen.md).
2. [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md) und [Funktionstrennung](funktionstrennung.md):
   die Regeln, die Sie anwenden werden.
3. Die Rollenentwürfe, die zu Ihren Teams passen.
4. [Eintritt, Wechsel und Austritt](eintritt-wechsel-austritt.md), [Pflege der Benutzerkonten](pflege-von-benutzerkonten.md) und
   [Zweistufige Anmeldung für Beschäftigte](zwei-faktor-anmeldung-fuer-beschaeftigte.md): Konten im Alltag führen.
5. [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md): all das nach Plan überprüfen.

Nach oben: [Exponential im öffentlichen Sektor betreiben](../README.md). Die Einstiegsseite des Bereichs ist [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Verwandte Seiten
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
- [Leitfaden zu Sektionen](../../../../anleitungen/sektionen.md) und [Leitfaden zu Objektzuständen](../../../../anleitungen/objektzustaende.md)
- [Redaktionelle Workflows](../redaktionelle-workflows/README.md)
- [Kurs für Website-Administratoren: Rollen und Richtlinien](../../../trainer/website-administratoren/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
