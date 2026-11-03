---
title: "Für Entwickler: Skripte und CI"
audience: developer, hosting
category: administration
order: 17
summary: Die Befehle exp:user in Skripten, Deployments und CI nutzen - die JSON-Ausgabe exp-user/1, die Exit-Codes, Passwörter aus CI-Geheimnissen über die Standardeingabe, wiederholbares Einrichten und jq-Beispiele für Prüfungen und Berichte.
keywords: Skripte, CI, kontinuierliche Integration, Deployment, Automatisierung, Exit-Codes, wiederholbar, Einrichtung, Dienstkonto, CI-Geheimnis, Zustandsprüfung, Überwachung, scripts, --format=json, exp-user/1, jq, idempotent, password-stdin, check-login, GitHub Actions
type: help
---
# Für Entwickler: Skripte und CI

Jeder Befehl nimmt `--format=json` und endet mit einem verlässlichen Exit-Code. Optionen und Schlüssel sind ein
dauerhafter Vertrag: neue können hinzukommen, keiner wird umbenannt oder entfernt.

## Was Sie am Ende haben

Skripte, die Konten anlegen, prüfen und berichten, ohne Bildschirmausgaben zu zerlegen und ohne ein Passwort auf
einer Befehlszeile.

## Die Ausgabe

`--format=json` gibt ein JSON-Objekt mit dem Schema `exp-user/1` aus:

```json
{
    "schema": "exp-user/1",
    "command": "exp:user:disable",
    "mode": "disable",
    "dry_run": true,
    "user": { "id": 1234, "login": "jane", "email": "jane@example.com", "name": "Jane Doe", "enabled": true },
    "changes": [ "disable the account jane and end its sessions" ],
    "applied": false,
    "errors": [],
    "warnings": [],
    "exit": 0
}
```

Jeder Schlüssel, je Befehl: [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md).

| Exit-Code | Bedeutung |
|---|---|
| 0 | erledigt, oder ein Probelauf, den nichts verweigert hat |
| 1 | fehlgeschlagen oder verweigert |
| 2 | Bedienfehler: eine falsche Option oder ein falscher Wert |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

## Beispiele

Ein Deployment scheitern lassen, wenn ein Dienstkonto fehlt oder deaktiviert ist:

```bash
./console exp:user:show ci-bot --format=json | jq -e '.account.enabled' > /dev/null
```

Ein Dienstkonto einmal anlegen, mit seinem Passwort aus einem CI-Geheimnis:

```bash
if ! ./console exp:user:show ci-bot --format=json > /dev/null; then
    printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:create --login=ci-bot --email=ci@example.com \
        --group=Editors --password-stdin --apply --format=json
fi
```

Prüfen, ob das Passwort noch funktioniert, ohne eine fehlgeschlagene Anmeldung zu zählen:

```bash
printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:check-login ci-bot --password-stdin --format=json \
    | jq -r '.valid, .reason'
```

Die wartenden Konten als ID und Anmeldename berichten:

```bash
./console exp:user:pending --format=json --limit=500 | jq -r '.accounts[] | "\(.id) \(.login)"'
```

Die Konten je Zustand für ein Dashboard zählen:

```bash
./console exp:user:hygiene --format=json | jq '.counts'
```

Eine Pipeline anhalten, wenn ein Probelauf etwas verweigern würde:

```bash
./console exp:user:bulk disable --state=inactive --days=730 --format=json > var/tmp/plan.json || exit 1
jq -r '.matched' var/tmp/plan.json
```

## Regeln für Skripte

- Führen Sie zuerst den Probelauf aus und prüfen Sie `errors`, bevor Sie mit `--apply` ausführen.
- Übergeben Sie ein Passwort nur über die Standardeingabe (`--password-stdin`). Legen Sie es nie in eine Variable, die
  ausgegeben wird.
- `generated_password` steht in der Ausgabe des Laufs, der es gesetzt hat, und nie wieder. Speichern Sie es sofort,
  oder erzeugen Sie keines.
- `--as=<Dienstkonto>` lässt das Audit die Automatisierung nennen, nicht den Benutzer aus `UserCreatorID`.
- Geben Sie `--siteaccess=<siteaccess>` an, wenn die Links in Mails auf eine bestimmte Website zeigen sollen.

## Verwandte Seiten

- [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md)
- [Für Agenturen und Hosting](fuer-agenturen-und-hosting.md)
- [Konsolenbefehle](../../beispiele/befehle/README.md)
