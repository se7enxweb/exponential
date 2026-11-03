---
title: "Für Site-Administratoren"
audience: admin
category: administration
order: 16
summary: Was die Befehle exp:user einem Site-Administrator bringen, der Konten schon in der Administration verwaltet - wann die Konsole schneller ist, die Befehle für die tägliche Arbeit, die Sicherheitsregeln und eine wöchentliche Routine.
keywords: Site-Administrator, tägliche Arbeit, Konsole oder Administration, wann die Konsole, wöchentliche Routine, Kontenprüfung, Probelauf, geschützte Konten, Audit, Kontenpflege, site administrator, exp:user, hygiene
type: help
---
# Für Site-Administratoren

Alles hier geht auch in der Administration. Die Konsole ist schneller, wenn Sie mit vielen Konten arbeiten, wenn Sie
ohnehin auf dem Server sind oder wenn die Administration nicht erreichbar ist.

## Was Sie am Ende haben

Eine klare Vorstellung, wann Sie die Konsole nutzen, und die Handvoll Befehle, die Sie jede Woche brauchen.

## Wann die Konsole hilft

| Situation | Befehl |
|---|---|
| Niemand kann sich in der Administration anmelden | [Einen ausgesperrten Administrator wiederherstellen](einen-ausgesperrten-administrator-wiederherstellen.md) |
| Jemand geht heute | [Den Zugang beim Ausscheiden schließen](den-zugang-beim-ausscheiden-schliessen.md) |
| Hunderte Registrierungen warten | [Einen Rückstau von Registrierungen freigeben](einen-rueckstau-von-registrierungen-freigeben.md) |
| Eine Liste neuer Mitglieder kommt als Tabelle | [Mitglieder aus einer CSV-Datei importieren](mitglieder-aus-csv-importieren.md) |
| Sie wollen wissen, wer welche Rolle hat | `exp:user:roles list <user>` ([Gruppen und Rollen](gruppen-und-rollen.md)) |
| Jemand fragt, was mit seinem Konto geschehen ist | `exp:user:audit <user>` ([Audit und Kontenpflege](audit-und-kontenpflege.md)) |

## Die Regeln, die Sie schützen

- Nichts ändert sich ohne `--apply`. Führen Sie jeden Befehl einmal ohne aus, lesen Sie die Ausgabe, dann geben Sie
  `--apply` an.
- Geschützte Konten werden verweigert: der anonyme Benutzer, der Benutzer aus `UserCreatorID`, `admin`, Sie selbst
  und der letzte aktivierte Administrator.
- Eine Sammeländerung braucht `--confirm` mit der Anzahl ihres Probelaufs.
- Jede Änderung steht im Benutzer-Auditprotokoll, mit Ihrem Namen, wenn Sie `--as=<Ihr Anmeldename>` angeben.

**Hinweis:** Geben Sie `--as=<Ihr Anmeldename>` für Änderungen an, die Sie selbst vornehmen. Ohne die Option hält das
Audit den Benutzer aus `[UserSettings] UserCreatorID` fest.

## Eine wöchentliche Routine

1. Führen Sie `./console exp:user:hygiene` aus.
2. Führen Sie `./console exp:user:pending` aus und entscheiden Sie jede wartende Registrierung.
3. Führen Sie `./console exp:user:list --state=locked` aus und entsperren Sie die Personen, die Sie kennen.
4. Deaktivieren Sie nach einem Probelauf die Konten, die sich seit einem Jahr nicht angemeldet haben:
   `./console exp:user:bulk disable --state=inactive --days=365`.

## Verwandte Seiten

- [Benutzerverwaltung von der Konsole](README.md)
- [Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md): dieselbe Prüfung in der Administration.
