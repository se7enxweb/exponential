---
title: "Benutzerverwaltung von der Konsole"
audience: admin, developer, hosting
category: administration
order: 1
summary: Der Anfang von allem rund um die Befehle exp:user in Exponential 6.0.15 - Benutzerkonten auflisten, anlegen, ändern, deaktivieren und entfernen, Passwörter zurücksetzen, Registrierungen freigeben, Gruppen, Rollen, Sitzungen und die zweistufige Anmeldung verwalten, Konten importieren, exportieren und prüfen, aus einer Shell, einem Deploy-Skript oder einem CI-Job, genau wie die Administration.
keywords: Benutzerverwaltung, Konsole, Kommandozeile, Benutzerkonten, Konto anlegen, Konto deaktivieren, Konto entfernen, Passwort zurücksetzen, Registrierung freigeben, Gruppen, Rollen, Import, Export, Probelauf, Skript, CI, user management, console, command line, CLI, exp:user, exp:user:list, exp:user:create, exp:user:edit, exp:user:disable, exp:user:delete, exp:user:reset-password, exp:user:approve, exp:user:groups, exp:user:roles, exp:user:import, exp:user:export, exp:user:bulk, dry run, --apply
featured: true
type: help
---
# Benutzerverwaltung von der Konsole

Die Befehle `exp:user` verwalten die Benutzerkonten einer Installation aus einer Shell, einem Deploy-Skript oder
einem CI-Job. Jeder Befehl tut, was die Administration bei einem Klick tut: dieselben Operationen, Workflows,
Prüfungen, Mails und Audit-Ereignisse. Nichts ändert sich, bevor Sie `--apply` angeben.

Nutzen Sie sie, wenn Sie viele Konten betreuen, wenn Sie ohne Browser auf dem Server arbeiten, wenn ein Skript eine
Installation einrichtet oder wenn ein Administrator ausgesperrt ist.

## Inhalt

1. [Hier beginnen](#hier-beginnen)
2. [Die Befehle](#die-befehle)
3. [Drei Regeln, die jeder Befehl befolgt](#drei-regeln-die-jeder-befehl-befolgt)
4. [Aufgaben](#aufgaben)
5. [Mehr](#mehr)

## Hier beginnen

- **Zum ersten Mal**: Lesen Sie die Anleitung
  [Benutzerverwaltung von der Konsole](../../anleitungen/benutzerverwaltung-von-der-konsole.md). Sie führt durch das
  Finden, Anlegen, Ändern und Entfernen von Konten.
- **Nach Rolle**: [Für Site-Administratoren](fuer-site-administratoren.md),
  [Für Entwickler: Skripte und CI](fuer-entwickler-skripte-und-ci.md) und
  [Für Agenturen und Hosting](fuer-agenturen-und-hosting.md).
- **Eine schnelle Antwort**: die [FAQ](faq.md). **Ein Fehler**: [Wenn ein Befehl fehlschlägt](wenn-ein-befehl-fehlschlaegt.md).
- Was die Funktion bringt: [Benutzerverwaltung von der Konsole](../../funktionen/6.0/benutzerverwaltung-von-der-konsole.md).

## Die Befehle

Jeder Befehl zeigt mit `--help` alle seine Optionen. `<user>` ist ein Anmeldename, eine E-Mail-Adresse (wenn nur ein
Konto sie hat) oder die ID des Kontos.

| Befehl | Was er tut | Seite |
|---|---|---|
| `exp:user:list`, `exp:user:show` | Konten finden und ansehen | [Konten finden, anlegen und ändern](konten-finden-anlegen-und-aendern.md) |
| `exp:user:create`, `exp:user:edit` | ein Konto anlegen, Adresse, Anmeldename, Namen und Gruppen ändern | [Konten finden, anlegen und ändern](konten-finden-anlegen-und-aendern.md) |
| `exp:user:disable`, `exp:user:enable`, `exp:user:delete` | ein Konto stoppen, wieder starten, endgültig entfernen | [Konten deaktivieren, aktivieren und entfernen](konten-deaktivieren-aktivieren-und-entfernen.md) |
| `exp:user:reset-password`, `exp:user:require-password-change`, `exp:user:check-login` | Passwörter und Anmeldeprüfungen | [Passwörter und Anmeldeprüfungen](passwoerter-und-anmeldepruefungen.md) |
| `exp:user:pending`, `exp:user:approve`, `exp:user:resend-activation` | wartende Registrierungen | [Registrierungen freigeben](registrierungen-freigeben.md) |
| `exp:user:groups`, `exp:user:roles`, `exp:user:whoami` | Benutzergruppen, Rollenzuweisungen, als wer die Konsole handelt | [Gruppen und Rollen](gruppen-und-rollen.md) |
| `exp:user:sessions`, `exp:user:unlock`, `exp:user:2fa` | Sitzungen, gesperrte Konten, zweistufige Anmeldung und Passkeys | [Sitzungen und Anmeldesicherheit](sitzungen-und-anmeldesicherheit.md) |
| `exp:user:export`, `exp:user:import`, `exp:user:bulk` | viele Konten auf einmal | [Import, Export und Sammeländerungen](import-export-und-sammelaenderungen.md) |
| `exp:user:audit`, `exp:user:hygiene` | der Verlauf eines Kontos, der Zustand aller Konten | [Audit und Kontenpflege](audit-und-kontenpflege.md) |

## Drei Regeln, die jeder Befehl befolgt

**Ein Probelauf ohne `--apply`.** Ein Befehl, der etwas ändert, listet zuerst auf, was er tun würde und welche
Prüfungen fehlschlagen. Geben Sie `--apply` an, um die Änderung vorzunehmen. `--dry-run` wird angenommen und bedeutet
dasselbe wie keine Option; beide zusammen sind ein Bedienfehler (Exit-Code 2).

**Kein Passwort auf der Befehlszeile.** Kein Befehl nimmt `--password=...`, weil der Verlauf der Shell und die
Prozessliste es behalten würden. Ein Passwort kommt von der Standardeingabe (`--password-stdin`), wird erzeugt und
einmal ausgegeben, oder die Person wählt es über eine Mail.

**Wer handelt, wird festgehalten.** Die Befehle handeln als der Benutzer aus `site.ini [UserSettings] UserCreatorID`
oder als der Benutzer, den Sie mit `--as=<login>` nennen. Das Audit und das Benutzer-Auditprotokoll halten diesen
Benutzer und den Siteaccess fest.

**Geschützte Konten** werden nie deaktiviert oder entfernt: der anonyme Benutzer, der Benutzer aus `UserCreatorID`,
das Administratorkonto (ID 14 oder der Anmeldename `admin`), der handelnde Benutzer und der letzte aktivierte
Administrator.

## Aufgaben

| Aufgabe | Seite |
|---|---|
| Ein Konto für einen neuen Redakteur einrichten | [Einen neuen Redakteur einrichten](einen-neuen-redakteur-einrichten.md) |
| Den Zugang von jemandem schließen, der geht | [Den Zugang beim Ausscheiden schließen](den-zugang-beim-ausscheiden-schliessen.md) |
| Viele wartende Registrierungen abarbeiten | [Einen Rückstau von Registrierungen freigeben](einen-rueckstau-von-registrierungen-freigeben.md) |
| Einen Administrator wieder hineinlassen | [Einen ausgesperrten Administrator wiederherstellen](einen-ausgesperrten-administrator-wiederherstellen.md) |
| Jemandem helfen, der das Telefon mit der Authenticator-App verloren hat | [Die zweistufige Anmeldung nach einem verlorenen Telefon zurücksetzen](zweistufige-anmeldung-nach-verlorenem-telefon-zuruecksetzen.md) |
| Viele Mitgliederkonten aus einer Tabelle anlegen | [Mitglieder aus einer CSV-Datei importieren](mitglieder-aus-csv-importieren.md) |

## Mehr

- Die JSON-Ausgabe und die Exit-Codes, Schlüssel für Schlüssel:
  [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md).
- Dieselben Aufgaben in der Administration:
  [Konten für Redakteure und Administratoren einrichten](../konten-fuer-redakteure-und-administratoren-einrichten.md),
  [Mitglieder verwalten](../mitglieder-verwalten.md), [Das Benutzer-Auditprotokoll](../das-benutzer-auditprotokoll.md)
  und [Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md).
- Jeder Befehl mit allen Optionen: [Konsolenbefehle](../../beispiele/befehle/README.md).

## Nächster Schritt

[Konten finden, anlegen und ändern](konten-finden-anlegen-und-aendern.md).
