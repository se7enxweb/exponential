---
title: Prüfen, wer Administratorrechte hat
audience: government, admin, project-manager
category: public-sector
order: 120
summary: Schritt für Schritt - jedes Konto mit vollem Zugriff oder dem Recht, Rollen zu ändern, auflisten, mit den Kennzeichen der Rollenliste, den Rollenseiten, Benutzerkonten prüfen und dem Audit-Protokoll, und das Ergebnis für eine Überprüfung des Zugangs festhalten.
keywords: Administratorrechte, Administratoren, Voller Zugriff, Kann Rollen ändern, Rollenliste, Überprüfung des Zugangs, Audit, privilegierte Konten, admin_role_granted, Rezertifizierung, admin rights, administrators, full access, Can change roles, role list, access review, privileged accounts, recertification
type: help
---
# Prüfen, wer Administratorrechte hat

Diese Aufgabe ist für Administratoren und Prüfer, die eine Überprüfung des Zugangs vorbereiten. Am Ende haben Sie eine
datierte Liste der Konten, die vollen Zugriff haben oder Rollen ändern können, mit Belegen dafür, wie sie diese Rechte
erhalten haben. Hintergrund:
[Überprüfungen des Administratorzugangs](../../betrieb/personen-und-berechtigungen/ueberpruefung-administrativer-zugaenge.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die privilegierten Rollen finden](#die-privilegierten-rollen-finden)
3. [Die Personen finden](#die-personen-finden)
4. [Den Verlauf prüfen](#den-verlauf-prüfen)
5. [Das Ergebnis festhalten](#das-ergebnis-festhalten)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Um Rollenseiten zu lesen, brauchen Sie das Modul `role` (ein Administrator erledigt diesen Schritt oder zeigt dem
  Prüfer die Seiten). Für die Schritte im Audit `audit/read` für den Kanal `access`.
- Shell-Zugang für die Befehle (optional).

## Die privilegierten Rollen finden

1. Öffnen Sie **Benutzer > Rollen und Richtlinien** (`/role/list`).
2. Achten Sie auf die Kennzeichen: *Voller Zugriff* (rot, eine Richtlinie über jedes Modul) und *Kann Rollen ändern*
   (gelb, eine Richtlinie über das Modul `role`).
3. Notieren Sie jede solche Rolle. Die Rolle Administrator hat auf jeder Installation *Voller Zugriff*.

## Die Personen finden

1. Öffnen Sie jede privilegierte Rolle. Die Seite zeigt die Zuweisungen (Benutzer und Gruppen, mit Einschränkungen)
   und **Betroffene Benutzer insgesamt**.
2. Öffnen Sie für jede Gruppe die Gruppe im Benutzerbaum, um ihre Mitglieder aufzulisten.
3. Gleichen Sie unter **Benutzer > Benutzerkonten prüfen** (`/user/hygiene`) ab: Suchen Sie jedes Konto; die Zeile zeigt
   seine Rollen (eigene und geerbte), seinen Zustand und den letzten Besuch.
4. Markieren Sie Konten, die deaktiviert sind, sich nie angemeldet haben oder lange nicht gesehen wurden.

## Den Verlauf prüfen

In der Shell:

```bash
./console exp:audit alerts list --allow-root-user
./console exp:audit search --name='access.*' --from='<start of the period>' --limit=100 --allow-root-user
./console exp:audit verify --allow-root-user
```

Die Warnregel `admin_role_granted` löst aus, wenn eine Administratorrolle vergeben wird; jede ausgelöste Warnung sollte
zu einem genehmigten Antrag passen. `verify` zeigt, dass die Aufzeichnung unversehrt ist. Im Browser: Reiter **Audit**,
**Alarme** (`/audit/alerts`) und **Konsole**.

## Das Ergebnis festhalten

| Feld | Inhalt |
|---|---|
| Datum und Prüfer | |
| Privilegierte Rollen | Namen und Kennzeichen |
| Konten | Login, wie zugewiesen (Gruppe oder direkt), letzter Besuch, zweistufige Anmeldung an oder aus |
| Befunde | zu entfernende Konten, Vergaben ohne Antrag |
| Belege | die Ausgabe der Suche im Audit, das Ergebnis von `verify` |

Handeln Sie dann: Entfernen Sie nicht benötigte Zuweisungen (**Ausgewählte entfernen** auf der Seite der Rolle) und
deaktivieren Sie ungenutzte Konten ([Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md)).

## Verwandte Seiten
- [Die Kontenliste für eine Überprüfung exportieren](die-kontenliste-fuer-eine-ueberpruefung-exportieren.md)
- [Prüfen, was eine Rolle erlaubt](pruefen-was-eine-rolle-erlaubt.md)
- [Grundsätze der minimalen Rechte](../../betrieb/personen-und-berechtigungen/prinzip-der-geringsten-rechte.md)
- [Audit-Warnungen](../../betrieb/ueberwachung/audit-warnungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
