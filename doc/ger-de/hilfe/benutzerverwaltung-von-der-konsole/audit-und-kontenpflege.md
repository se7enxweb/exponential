---
title: "Audit und Kontenpflege"
audience: admin, developer, hosting
category: administration
order: 9
summary: exp:user:audit zeigt die Zeilen des Benutzer-Auditprotokolls über ein Konto oder von ihm, gefiltert nach Ereignis, Ergebnis und Datum; exp:user:hygiene zählt die Konten je Zustand, jeweils mit dem Befehl exp:user:list, der sie auflistet.
keywords: Benutzer-Auditprotokoll, Verlauf eines Kontos, wer hat was geändert, Ereignisse, abgewiesen, fehlgeschlagen, Kontenpflege, Benutzerkonten prüfen, inaktive Konten, nie angemeldet, gesperrt, verwaist, doppelte Anmeldenamen, doppelte E-Mail-Adressen, nicht aktiviert, Überprüfung, exp:user:audit, exp:user:hygiene, user audit log, hygiene
type: help
---
# Audit und Kontenpflege

`exp:user:audit` beantwortet, was mit einem Konto geschehen ist. `exp:user:hygiene` beantwortet, welche Konten
Aufmerksamkeit brauchen. Beide lesen nur.

## Was Sie am Ende haben

Den Verlauf eines Kontos und eine Liste der Konten, die aufzuräumen sind.

## Der Verlauf eines Kontos

```bash
./console exp:user:audit jane
./console exp:user:audit jane --event=access.user.password --from=2026-10-01
./console exp:user:audit jane --result=refused --limit=50
```

Der Befehl listet die Zeilen des Benutzer-Auditprotokolls (**Benutzer > Benutzer-Auditprotokoll**) über das Konto
oder von ihm auf: die Zeit, das Ereignis, das Ergebnis, den Handelnden und den Siteaccess.

| Option | Bedeutung |
|---|---|
| `--event` | ein Ereignisname oder sein Anfang, zum Beispiel `access.user.password` |
| `--result` | `success`, `refused` oder `failed` |
| `--from`, `--to` | Tage als `YYYY-MM-DD` |
| `--limit`, `--offset` | Seitengröße und die zu überspringenden Zeilen |

Nutzen Sie es, wenn jemand fragt, warum sich sein Konto geändert hat, oder wenn eine Anmeldung verdächtig aussieht.

## Der Zustand aller Konten

```bash
./console exp:user:hygiene
./console exp:user:hygiene --days=365
```

Der Befehl zeigt die Zahlen von **Benutzer > Benutzerkonten prüfen**: die Konten je Zustand.

| Zustand | Konten |
|---|---|
| aktiviert, deaktiviert | können sich anmelden oder nicht |
| nicht aktiviert | registriert, nie aktiviert |
| wartend | Registrierungen, die warten |
| gesperrt | zu viele falsche Passwörter |
| nie angemeldet | angelegt, nie benutzt |
| inaktiv | seit `--days` Tagen nicht angemeldet (Vorgabe 180) |
| verwaist | kein Benutzerobjekt, nie veröffentlicht oder keine Platzierung |
| doppelte Anmeldenamen, doppelte E-Mail-Adressen | mit einem anderen Konto geteilt |

Jede Zahl kommt mit dem Befehl `exp:user:list`, der diese Konten auflistet. Kopieren Sie ihn, um sie zu sehen, und
handeln Sie dann mit den einzelnen Befehlen oder mit `exp:user:bulk`.

## Eine regelmäßige Überprüfung

1. Führen Sie `./console exp:user:hygiene` aus.
2. Listen Sie die Konten jeder Zahl auf, die nicht 0 ist, mit dem Befehl daneben.
3. Deaktivieren Sie die Konten, die niemand braucht ([Sammeländerungen](import-export-und-sammelaenderungen.md#sammeländerungen)).
4. Entsperren oder setzen Sie die gesperrten Konten zurück, die aktiven Personen gehören.
5. Fragen Sie die Inhaber doppelter E-Mail-Adressen, welches Konto sie benutzen.

## Verwandte Seiten

- [Das Benutzer-Auditprotokoll](../das-benutzer-auditprotokoll.md): dasselbe Protokoll in der Administration.
- [Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md): die Seite und ihre Filter.
- [Benutzer-Auditprotokoll](../../funktionen/6.0/benutzer-auditprotokoll.md): jedes Ereignis und jede Einstellung.
