---
title: "Konten finden, anlegen und ändern"
audience: admin, developer, hosting
category: administration
order: 2
summary: exp:user:list und exp:user:show finden und zeigen Konten nach Zustand, Gruppe und Suche; exp:user:create legt ein Konto über das Veröffentlichen von Inhalten an, mit einem Passwort, das niemand auf einer Befehlszeile tippt; exp:user:edit ändert E-Mail-Adresse, Anmeldenamen, Namen und Gruppen.
keywords: Benutzer auflisten, Benutzer finden, Benutzer anzeigen, Benutzer anlegen, neues Konto, E-Mail-Adresse ändern, Anmeldename ändern, zu Gruppe hinzufügen, aus Gruppe entfernen, Hauptgruppe, Zustand, Filter, Suche, seitenweise, exp:user:list, exp:user:show, exp:user:create, exp:user:edit, list users, create user, --generate-password, --password-stdin, --send-activation
type: help
---
# Konten finden, anlegen und ändern

`exp:user:list` und `exp:user:show` lesen; `exp:user:create` und `exp:user:edit` ändern. Die ändernden Befehle sind
ein Probelauf, bis Sie `--apply` angeben.

## Was Sie am Ende haben

Jedes Konto gefunden und angesehen, ein neues Konto in den richtigen Gruppen und ein bestehendes Konto mit neuer
Adresse, neuem Anmeldenamen, Namen oder neuer Gruppe.

## Konten finden

```bash
./console exp:user:list
./console exp:user:list --state=disabled --group=Editors
./console exp:user:list --state=inactive --days=365 --sort=last_visit --order=desc
./console exp:user:list --search=example.com --limit=100 --offset=100
```

| Option | Bedeutung |
|---|---|
| `--state` | `all` (Vorgabe), `enabled`, `disabled`, `unactivated`, `pending`, `locked`, `never`, `inactive`, `orphaned`, `duplicate_login`, `duplicate_email` |
| `--group` | eine Benutzergruppe nach Knoten-ID oder Name: ihre Mitglieder in jeder Tiefe |
| `--search` | ein Text im Anmeldenamen, im Namen oder in der E-Mail-Adresse |
| `--days` | mit `--state=inactive`: seit so vielen Tagen nicht angemeldet (Vorgabe 180) |
| `--sort`, `--order` | `name` (Vorgabe), `login`, `email`, `last_visit`, `login_count`, `id`; `asc` (Vorgabe) oder `desc` |
| `--limit`, `--offset` | Seitengröße (Vorgabe 25, höchstens 500) und die zu überspringenden Konten |

Die Zustände sind die Filter von **Benutzer > Benutzerkonten prüfen**. Die Anleitung erklärt jeden:
[Benutzerverwaltung von der Konsole](../../anleitungen/benutzerverwaltung-von-der-konsole.md#konten-finden-und-ansehen).

## Ein Konto ansehen

```bash
./console exp:user:show jane
./console exp:user:show jane@example.com
./console exp:user:show 1234 --format=json
```

Die Ausgabe nennt die Gruppen (die Hauptgruppe zuerst), jede Rolle mit ihrer Herkunft und ihrer Einschränkung, den
Zustand, die Registrierung, die Anmeldungen und Fehlversuche, die letzte Passwortänderung, die zweistufige Anmeldung,
die aktiven Sitzungen und ob das Konto geschützt ist. Einen Passwort-Hash zeigt sie nie.

## Ein Konto anlegen

1. Führen Sie den Befehl ohne `--apply` aus.

   ```bash
   ./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
       --group=Editors --generate-password
   ```

   Der Befehl listet auf, was er anlegen würde und welche Prüfungen fehlschlagen.
2. Beheben Sie jede fehlgeschlagene Prüfung, zum Beispiel einen vergebenen Anmeldenamen.
3. Führen Sie denselben Befehl mit `--apply` aus.
   Das Konto wird veröffentlicht. Ein erzeugtes Passwort wird einmal ausgegeben.

`--login` und `--email` sind Pflicht. Wiederholen Sie `--group` für weitere Gruppen; die erste ist die Hauptgruppe.
Ohne `--group` kommt das Konto nach `[UserSettings] DefaultUserPlacement`. `--class` wählt eine andere Benutzerklasse
als `[UserSettings] UserClassID`.

Wählen Sie einen Weg zum Passwort:

| Option | Was geschieht |
|---|---|
| `--generate-password` | ein Passwort, das die Passwortregeln erfüllt, einmal ausgegeben |
| `--password-stdin` | eine Zeile von der Standardeingabe, aus einem Passwortmanager, einer Datei mit Modus 0600 oder einem CI-Geheimnis |
| `--send-activation` | das Konto beginnt deaktiviert und erhält die Aktivierungsmail; die Person wählt dann mit **Passwort vergessen** ein Passwort |

`--disabled` legt das Konto deaktiviert an; aktivieren Sie es später mit `exp:user:enable`.

**Hinweis:** Das Konto wird mit seinen Workflows veröffentlicht. Wo neue Konten freigegeben werden müssen, wartet das
Konto und erscheint in `exp:user:pending` ([Registrierungen freigeben](registrierungen-freigeben.md)).

## Ein Konto ändern

```bash
./console exp:user:edit jane --email=jane.doe@example.com --apply
./console exp:user:edit jane --login=jdoe --apply
./console exp:user:edit jane --first-name=Janet --last-name=Doe --apply
./console exp:user:edit jane --add-group=Editors --remove-group=Members --main-group=Editors --apply
```

- Eine neue E-Mail-Adresse und ein neuer Anmeldename werden geprüft wie beim Anlegen. Das Audit hält
  `access.user.email.change` und `access.user.login.change` fest.
- Ein neuer Name ist eine neue Version des Benutzerobjekts, veröffentlicht mit seinen Workflows.
- `--add-group` fügt eine Platzierung in einer Gruppe hinzu, `--remove-group` entfernt eine. Beide lassen sich
  wiederholen.
- Ein Konto behält immer mindestens eine Gruppe. Geht die Hauptgruppe, wird die nächste Gruppe zur Hauptgruppe, oder
  die, die Sie mit `--main-group` nennen.

Um ein Konto in eine andere Gruppe zu verschieben, fügen Sie die neue Gruppe hinzu und entfernen die alte in einem
Befehl.

## Ein Konto entfernen

`exp:user:delete` entfernt ein Konto endgültig. Lesen Sie zuerst
[Konten deaktivieren, aktivieren und entfernen](konten-deaktivieren-aktivieren-und-entfernen.md): meist ist das
Deaktivieren die bessere Wahl.

## Verwandte Seiten

- [Gruppen und Rollen](gruppen-und-rollen.md): Gruppen anlegen und Rollen zuweisen.
- [Konten für Redakteure und Administratoren einrichten](../konten-fuer-redakteure-und-administratoren-einrichten.md):
  dasselbe in der Administration.
- [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md): die Schlüssel von `list`, `show`
  und den ändernden Befehlen.
