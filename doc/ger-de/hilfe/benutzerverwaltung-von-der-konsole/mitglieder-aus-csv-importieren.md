---
title: "Mitglieder aus einer CSV-Datei importieren"
audience: admin, hosting
category: administration
order: 15
summary: Viele Mitgliederkonten aus einer Tabelle mit exp:user:import anlegen - die CSV-Datei ohne Passwörter vorbereiten, jede Zeile in einem Probelauf prüfen, mit der Aktivierungsmail oder als deaktivierte Konten importieren und mit übersprungenen und verweigerten Zeilen umgehen.
keywords: Mitglieder importieren, Sammelimport, CSV-Import, Tabelle, Mitgliederliste, Probelauf, übersprungen, verweigert, bestehende Anmeldenamen, Gruppen, Mitglieder übernehmen, import members, exp:user:import, --send-activation, --disabled, would_create, DefaultUserPlacement
type: help
---
# Mitglieder aus einer CSV-Datei importieren

Jede Zeile wird ein Konto, genau wie mit `exp:user:create` angelegt. Kein Passwort reist in der Datei.

## Was Sie am Ende haben

Die Mitglieder Ihrer Liste als Konten in den richtigen Gruppen, jedes mit einer Aktivierungsmail oder deaktiviert, bis
Sie ein Passwort setzen.

## Bevor Sie beginnen

- Die Liste als Tabelle, mit einer Zeile je Person.
- Der Name oder die Knoten-ID der Gruppe, zu der die Mitglieder gehören, hier `Members`.

## Schritte

1. Speichern Sie die Liste als CSV-Datei in UTF-8, mit dieser Kopfzeile:

   ```text
   login,email,first_name,last_name,groups
   jdoe,jane.doe@example.com,Jane,Doe,Members
   rroe,richard.roe@example.com,Richard,Roe,
   ```

   `groups` darf leer bleiben; dann entscheidet `--group`. Mehrere Gruppen werden durch `|` getrennt.
2. Entfernen Sie jede Passwortspalte.
   Eine Datei mit einer Passwortspalte wird verweigert.
3. Kopieren Sie die Datei auf den Server, zum Beispiel nach `var/tmp/members.csv`.
4. Führen Sie den Import als Probelauf aus.

   ```bash
   ./console exp:user:import var/tmp/members.csv --group=Members --send-activation
   ```

   Jede Zeile wird geprüft. Jede Zeile zeigt `would_create`, `skipped` (den Anmeldenamen gibt es schon) oder `refused`
   mit den Gründen.
5. Korrigieren Sie die verweigerten Zeilen in der Datei und führen Sie den Probelauf erneut aus.
6. Führen Sie den Import mit `--apply` aus.

   ```bash
   ./console exp:user:import var/tmp/members.csv --group=Members --send-activation --apply
   ```

   Jede Person erhält die Aktivierungsmail.
7. **Datenschutz:** Die Datei enthält Namen und E-Mail-Adressen.
   Löschen Sie sie vom Server, wenn der Import erledigt ist.

## Ergebnis

`exp:user:list --group=Members --state=unactivated` listet die neuen Konten auf, bis die Personen sie aktivieren.

## Varianten

- **Jetzt keine Mail**: nehmen Sie `--disabled` statt `--send-activation`. Wenn die Mitglieder beginnen dürfen,
  aktivieren Sie die Konten mit `exp:user:bulk enable` und senden dann jeder Person den Link zum Zurücksetzen mit
  `exp:user:reset-password --send-link`.
- **JSON statt CSV**: ein Array von Objekten mit den Schlüsseln `login`, `email`, `first_name`, `last_name`, `groups`,
  und `--type=json`.
- **Eine große Liste**: teilen Sie sie in Dateien zu einigen hundert Zeilen. Ein Lauf, der abbricht, lässt sich
  wiederholen: bestehende Anmeldenamen werden übersprungen.

## Verwandte Seiten

- [Import, Export und Sammeländerungen](import-export-und-sammelaenderungen.md#konten-importieren)
- [Registrierungen freigeben](registrierungen-freigeben.md): Erinnerungen mit `exp:user:resend-activation`.
