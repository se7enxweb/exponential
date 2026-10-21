---
title: Aufgaben zu Personen und Berechtigungen
audience: government, admin, project-manager
category: public-sector
order: 10
summary: Index der Schritt-für-Schritt-Aufgaben zum Zugang von Mitarbeitern auf einer Exponential-Site im öffentlichen Sektor - Rollen für Abteilungen, Freigebende, Prüfer und Auftragnehmer, Eintritte und Austritte, Passwörter und zweistufige Anmeldung, Sektionen, Zustände und Überprüfungen des Zugangs.
keywords: Aufgaben, Anleitung, Rollen, Berechtigungen, Freigebender, Redakteur einer Abteilung, Auftragnehmer, Prüfer, ausscheidender Mitarbeiter, neue Mitarbeiter, Passwort zurücksetzen, zweistufige Anmeldung, Kontosperre, Überprüfung des Zugangs, Sektionen, Zustände, Index, tasks, how to, roles, permissions, approver, department editor, contractor, auditor, leaver, new staff, password reset, two-step sign-in, lockout, access review, sections, states
type: help
---
# Aufgaben zu Personen und Berechtigungen

Dieser Ordner enthält kurze, nummerierte Abläufe für Administratoren und Mitarbeiter des Service Desks, die den Zugang
von Mitarbeitern zu einer öffentlichen Website verwalten. Jede Aufgabe nennt die nötigen Rechte, die genauen Seiten und
Befehle, wie Sie das Ergebnis prüfen und was in einen Änderungsnachweis gehört. Die Erklärungen dahinter stehen in
[Personen und Berechtigungen](../../betrieb/personen-und-berechtigungen/README.md) und
[Redaktionelle Workflows](../../betrieb/redaktionelle-workflows/README.md).

## Inhalt
1. [Rollen und Zuweisungen](#rollen-und-zuweisungen)
2. [Konten: Eintritte, Wechsel und Austritte](#konten-eintritte-wechsel-und-austritte)
3. [Sicherheit der Anmeldung](#sicherheit-der-anmeldung)
4. [Sektionen, Zustände und Freigaben](#sektionen-zustände-und-freigaben)
5. [Überprüfungen und Audits](#überprüfungen-und-audits)
6. [Empfohlene Reihenfolge](#empfohlene-reihenfolge)
7. [Verwandte Seiten](#verwandte-seiten)

## Rollen und Zuweisungen

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Eine Redakteursrolle einer Abteilung für einen Teilbaum anlegen](eine-redakteursrolle-einer-abteilung-fuer-einen-teilbaum-anlegen.md) | Eine Rolle, je Abteilungsgruppe mit einer Einschränkung auf einen Teilbaum zugewiesen. |
| [Eine Benutzergruppe für eine Abteilung anlegen](eine-benutzergruppe-fuer-eine-abteilung-anlegen.md) | Eine Gruppe je Abteilung trägt die Rollen; Personen treten Gruppen bei und verlassen sie. |
| [Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen](eine-rolle-mit-einer-einschraenkung-auf-einen-teilbaum-zuweisen.md) | **Mit Einschränkungen zuordnen**, Teilbaum, der Ordner, die Gruppe. |
| [Eine Rolle mit einer Einschränkung auf eine Sektion zuweisen](eine-rolle-mit-einer-einschraenkung-auf-eine-sektion-zuweisen.md) | **Mit Einschränkungen zuordnen**, Sektion, die Sektion, die Gruppe. |
| [Eine Rolle für eine neue Abteilung kopieren](eine-rolle-fuer-eine-neue-abteilung-kopieren.md) | Meist stattdessen zuweisen; nur kopieren, wenn sich die Richtlinien unterscheiden. |
| [Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md) | Die Zuweisung auf der Seite der Rolle markieren, **Ausgewählte entfernen**. |
| [Einem Freigebenden Rechte geben](einem-freigebenden-rechte-geben.md) | In die Gruppe der Freigebenden aufnehmen; die Rolle braucht das Modul collaboration und versionread. |
| [Einem Prüfer Lesezugriff auf das Audit-Protokoll geben](einem-pruefer-lesezugriff-auf-das-audit-protokoll-geben.md) | `audit/read` je Kanal, maskierte Logs, keine Schreibrechte. |
| [Zugriff auf Logs ohne Administratorrechte geben](zugriff-auf-logs-ohne-administratorrechte-geben.md) | `setup/logs`; `managelogs` und `unmasklogs` nur bei Bedarf. |
| [Einer Kollegin oder einem Kollegen das Recht geben, Caches zu leeren](einer-kollegin-das-recht-geben-caches-zu-leeren.md) | `setup/managecache` allein. |
| [Einen Auftragnehmer nur einen Ordner bearbeiten lassen](einen-auftragnehmer-nur-einen-ordner-bearbeiten-lassen.md) | Eine Vertragsgruppe, eine enge Rolle auf einen Ordner, Freigabe an, ein sauberes Ende. |
| [Redakteure aus dem Siteaccess der Administration heraushalten](redakteure-aus-dem-siteaccess-der-administration-heraushalten.md) | `user/login` nach SiteAccess einschränken. |
| [Prüfen, was eine Rolle erlaubt](pruefen-was-eine-rolle-erlaubt.md) | Die Sätze und Kennzeichen der Richtlinien lesen, dann mit einem Testkonto testen. |

## Konten: Eintritte, Wechsel und Austritte

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Einen neuen Mitarbeiter hinzufügen](einen-neuen-mitarbeiter-hinzufuegen.md) | Das Konto in der Gruppe der Abteilung anlegen. |
| [Einen Mitarbeiter in eine andere Abteilung verschieben](einen-mitarbeiter-in-eine-andere-abteilung-verschieben.md) | Den neuen Ort hinzufügen, als Hauptort setzen, den alten entfernen. |
| [Einen ausscheidenden Mitarbeiter entfernen](einen-ausscheidenden-mitarbeiter-entfernen.md) | Am letzten Tag deaktivieren, übergeben, dann stilllegen. |
| [Einen ausscheidenden Mitarbeiter deaktivieren und verschieben](einen-ausscheidenden-mitarbeiter-deaktivieren-und-verschieben.md) | Deaktiviert und in eine versteckte Gruppe ohne Rollen verschoben; umkehrbar. |
| [Konten finden, die seit Monaten nicht genutzt wurden](konten-finden-die-seit-monaten-nicht-genutzt-wurden.md) | **Seit N Tagen nicht gesehen** und **Nie angemeldet** unter Benutzerkonten prüfen. |
| [Ein gesperrtes Konto entsperren](ein-gesperrtes-konto-entsperren.md) | Zuerst die fehlgeschlagenen Anmeldungen ansehen, dann **Entsperren**. |
| [Die offene Registrierung schließen](die-offene-registrierung-schliessen.md) | Kein `user/register` für Besucher; `PolicyOmitListExclude[]` auf aktualisierten Sites. |

## Sicherheit der Anmeldung

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Das Passwort eines Mitarbeiters zurücksetzen](das-passwort-eines-mitarbeiters-zuruecksetzen.md) | Bevorzugt **Passwort vergessen?**; sonst die Karte Passwort. |
| [Ein Passwort auf der Kommandozeile zurücksetzen](ein-passwort-auf-der-kommandozeile-zuruecksetzen.md) | `resetuserpassword.php -u <login> -g`, als root oder mit Genehmigung eines Administrators. |
| [Die Kontosperre einschalten](die-kontosperre-einschalten.md) | `MaxNumberOfFailedLogin` mit einer Liste vertrauenswürdiger Netze. |
| [Die zweistufige Anmeldung für Mitarbeiter einschalten](die-zweistufige-anmeldung-fuer-mitarbeiter-einschalten.md) | Feld, Richtlinien, Verschlüsselungsschlüssel, Administratoren einrichten, dann `Enforce2FA`. |
| [Einem Benutzer helfen, der sein Telefon verloren hat](einem-benutzer-helfen-der-sein-telefon-verloren-hat.md) | Die Person prüfen, das Feld auf Codes per E-Mail umstellen, neu einrichten. |

## Sektionen, Zustände und Freigaben

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Eine Sektion für eingeschränkte Inhalte anlegen](eine-sektion-fuer-eingeschraenkte-inhalte-anlegen.md) | Eine neue Sektion ist für Besucher unsichtbar; geben Sie Mitarbeitern Lesezugriff. |
| [Besucher eine neue Sektion lesen lassen](besucher-eine-neue-sektion-lesen-lassen.md) | Die Sektion zu `content/read` der Rolle Anonymous hinzufügen. |
| [Einen Freigabe-Workflow für eine Sektion einrichten](einen-freigabe-workflow-fuer-eine-sektion-einrichten.md) | Ereignis Approve, Auslöser **Vor dem Veröffentlichen von Inhalten**, Workflow-Cronjob. |
| [Eine Zustandsgruppe für die Prüfung einrichten](eine-zustandsgruppe-fuer-die-pruefung-einrichten.md) | Entwurf, In Prüfung, Freigegeben; zuerst bestehende Inhalte freigeben. |

## Überprüfungen und Audits

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Prüfen, wer Administratorrechte hat](pruefen-wer-administratorrechte-hat.md) | Kennzeichen in der Rollenliste, Rollenseiten, Kontozeilen, das Audit-Protokoll. |
| [Die Kontenliste für eine Überprüfung exportieren](die-kontenliste-fuer-eine-ueberpruefung-exportieren.md) | **Liste als CSV exportieren**; als personenbezogene Daten behandeln. |

## Empfohlene Reihenfolge

Für eine neue Site: die Gruppen, die Redakteursrolle der Abteilung, die Rolle der Freigebenden und den
Freigabe-Workflow anlegen; dann die Anmeldung absichern (Redakteure aus der Administration heraushalten, zweistufige
Anmeldung, bei Bedarf Kontosperre); dann Mitarbeiter hinzufügen. Nach dem Go-live werden die Aufgaben zu Konten und
Überprüfungen zur Routine.

Nach oben: [Aufgaben](../README.md) und [Exponential im öffentlichen Sektor betreiben](../../betrieb/README.md). Die
Einstiegsseite des Bereichs ist [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Verwandte Seiten
- [Aufgaben im Betrieb](../betrieb/README.md)
- [Anleitung zu Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Kurs für Site-Administratoren](../../../trainer/website-administratoren/README.md)
- [Anleitung zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
