---
title: mail (Migrationsschritt)
audience: developer, admin
category: examples
summary: Sendet eine E-Mail über den Mailtransport der Installation.
keywords: mail, migration, YAML, step, expMigrationStepMail
type: generated
generated: exp:help:examples
---
# mail

Sendet eine E-Mail über den Mailtransport der Installation.

Ein Schritt einer YAML-Migration mit `type: mail`. Die Klasse `expMigrationStepMail` (`kernel/classes/migration/steps/expmigrationstepmail.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[mail]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `to` | `list` | erforderlich | die Adressen, an die sie geht |
| `cc` | `list` |  | Adressen in Kopie |
| `bcc` | `list` |  | Adressen in Blindkopie |
| `subject` | `string` | erforderlich | der Betreff |
| `body` | `string` | erforderlich | der Text |
| `html` | `bool` | `false` | true: der Text ist HTML |
| `from` | `string` |  | der Absender (Standard: site.ini [MailSettings] EmailSender, sonst AdminEmail) |
| `reply_to` | `string` |  | die Adresse, an die Antworten gehen |
| `transport` | `string` |  | ein Transport aus site.ini [MailSettings] TransportAlias (file, sendmail, smtp) nur für diese E-Mail |
| `category` | `string` | `"admin"` | die E-Mail-Kategorie aus mailpreferences.ini; eine optionale erreicht nur die Empfänger, deren Einstellungen es erlauben |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `recipients` | an wie viele Adressen sie ging |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: mail
  to: [ editors@example.com ]
  subject: The news section has a new class
  body: "From today on, news items have a teaser image."
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepmail.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
