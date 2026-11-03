---
title: "Import, Export und Sammeländerungen"
audience: admin, developer, hosting
category: administration
order: 8
summary: exp:user:export schreibt die Konten, die die Listenfilter auswählen, als CSV oder JSON ohne jedes Passwort, exp:user:import legt Konten aus einer CSV- oder JSON-Datei genau wie exp:user:create an, und exp:user:bulk deaktiviert, aktiviert, entfernt, gibt frei, lehnt ab oder entsperrt viele Konten mit einer Anzahl, die Sie bestätigen.
keywords: Benutzer exportieren, Benutzer importieren, CSV, JSON, Sammeländerung, viele Konten, Massenänderung, sammelweise deaktivieren, sammelweise freigeben, sammelweise ablehnen, Datenschutz, personenbezogene Daten, exp:user:export, exp:user:import, exp:user:bulk, export users, import users, bulk disable, --confirm
type: help
---
# Import, Export und Sammeländerungen

Diese Befehle arbeiten mit vielen Konten auf einmal. Jeder wählt Konten mit den Filtern von `exp:user:list` aus
(`--state`, `--group`, `--search`, `--days`).

## Was Sie am Ende haben

Eine Datei mit Konten, Konten aus einer Datei angelegt, oder eine Änderung an vielen Konten.

## Konten exportieren

```bash
./console exp:user:export --output=var/tmp/editors.csv --group=Editors
./console exp:user:export --type=json --state=inactive --days=365 > var/tmp/inactive.json
```

`--type` ist `csv` (Vorgabe) oder `json`. Ohne `--output` geht die Datei auf die Standardausgabe.

Jedes Konto hat diese Felder: `id`, `login`, `email`, `first_name`, `last_name`, `name`, `enabled`, `states`, `groups`
(Knoten-IDs, getrennt durch `|`), `main_group`, `last_visit`, `login_count`. Ein Export enthält nie ein Passwort oder
einen Passwort-Hash.

**Datenschutz:** Die Datei enthält personenbezogene Daten: Namen und E-Mail-Adressen. Bewahren Sie sie dort auf, wo
nur die Personen sie lesen können, die sie brauchen, und entfernen Sie sie, wenn die Arbeit erledigt ist.

## Konten importieren

Die Datei ist CSV oder JSON. Eine CSV-Datei beginnt mit dieser Kopfzeile:

```text
login,email,first_name,last_name,groups
jdoe,jane.doe@example.com,Jane,Doe,Members
rroe,richard.roe@example.com,Richard,Roe,Members|1234
```

Eine JSON-Datei ist ein Array von Objekten mit denselben Schlüsseln. `groups` enthält Knoten-IDs oder Namen, getrennt
durch `|`.

```bash
./console exp:user:import var/tmp/members.csv --send-activation
./console exp:user:import var/tmp/members.csv --send-activation --apply
./console exp:user:import var/tmp/staff.json --type=json --disabled --group=Editors --apply
```

- Wählen Sie genau eines von `--send-activation` und `--disabled`.
  - `--send-activation`: jede Person erhält die Aktivierungsmail und wählt dann mit **Passwort vergessen** ein
    Passwort.
  - `--disabled`: die Konten werden deaktiviert angelegt; Sie setzen ihre Passwörter später zurück.
- Zeilen ohne Gruppen kommen in `--group` (für weitere Gruppen wiederholen), standardmäßig in
  `[UserSettings] DefaultUserPlacement`.
- Ein Anmeldename, den es schon gibt, wird übersprungen und aufgelistet.
- Eine Datei mit irgendeiner Passwortspalte wird verweigert.
- Jedes Konto wird genau wie mit `exp:user:create` angelegt: mit seinen Prüfungen, Workflows und Audit-Ereignissen.

Der Probelauf prüft jede Zeile und listet auf, was angelegt würde. Jede Zeile hat ein Ergebnis: `would_create` im
Probelauf, `created` mit `--apply`, oder `skipped` oder `refused` mit den Gründen.

Die Aufgabe Schritt für Schritt: [Mitglieder aus einer CSV-Datei importieren](mitglieder-aus-csv-importieren.md).

## Sammeländerungen

```bash
./console exp:user:bulk disable --state=inactive --days=730
./console exp:user:bulk disable --state=inactive --days=730 --confirm=42 --apply
```

Die Aktionen sind `disable`, `enable`, `delete`, `approve`, `reject` und `unlock`. Sie gelten für die Konten, die die
Filter auswählen, höchstens `--limit` (Vorgabe 500, höchstens 5000).

1. Führen Sie den Befehl ohne `--apply` aus.
   Der Probelauf listet die Konten und ihre Anzahl auf.
2. Prüfen Sie die Liste.
3. Führen Sie den Befehl erneut mit `--apply` und `--confirm=<die Anzahl>` aus.
   Die Zahl muss mit dem übereinstimmen, was die Filter in diesem Moment auswählen. Stimmt sie nicht, ändert sich
   nichts.

Geschützte Konten und der letzte Administrator werden übersprungen und aufgelistet, nie geändert. Jedes Konto wird so
geändert, wie es der einzelne Befehl ändern würde, mit seinem eigenen Audit-Ereignis.

**Achtung:** `delete` und `reject` entfernen Konten endgültig. Exportieren Sie die Konten vorher, und nehmen Sie
lieber `disable`.

## Verwandte Seiten

- [Einen Rückstau von Registrierungen freigeben](einen-rueckstau-von-registrierungen-freigeben.md): `bulk approve`
  und `bulk reject`.
- [Audit und Kontenpflege](audit-und-kontenpflege.md): die Konten finden, die eine Sammeländerung auswählen soll.
- [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md): die Schlüssel von `export`,
  `import` und `bulk`.
