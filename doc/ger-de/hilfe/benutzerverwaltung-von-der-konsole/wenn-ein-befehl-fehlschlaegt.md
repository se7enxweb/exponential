---
title: "Wenn ein Befehl fehlschlägt"
audience: admin, developer, hosting
category: administration
order: 21
summary: Fehlerbehebung für die Befehle exp:user nach Symptom - nichts geändert, ein Konto oder eine Gruppe nicht gefunden oder mehrdeutig, ein geschütztes Konto, eine wartende Veröffentlichung, ein Passwort von der Standardeingabe, der falsche Host in einem Link, eine Sammelanzahl, die nicht stimmt, ein Import, der Zeilen verweigert, und die Exit-Codes 2, 3 und 4.
keywords: Fehlerbehebung, Fehler, FAIL, Exit-Code, nichts geändert, Konto nicht gefunden, geschützt, letzter Administrator, Veröffentlichung wartet, nie aktiviert, keine Standardeingabe, Anzahl stimmt nicht, Import verweigert, Datenbank nicht erreichbar, exp:user, troubleshooting, error, no account has the login, accounts share the e-mail address, user groups are named, the publish waits, standard input holds no password, --confirm
type: help
---
# Wenn ein Befehl fehlschlägt

Jeder Befehl endet mit `PASS` oder mit `FAIL (exit <code>)` und den Gründen darüber. Beginnen Sie mit dem Exit-Code.
Die Meldungen der Befehle sind englisch; sie stehen hier so, wie Sie sie sehen.

## Was Sie am Ende haben

Die Ursache des Fehlers und die Behebung.

## Nach Exit-Code

| Exit-Code | Bedeutung | Was zu tun ist |
|---|---|---|
| 1 | fehlgeschlagen oder verweigert | lesen Sie die Zeilen `FAIL:`; die Tabellen unten erklären sie |
| 2 | Bedienfehler | eine Option oder ein Wert ist falsch; führen Sie den Befehl mit `--help` aus |
| 3 | die Datenbank ist nicht erreichbar | prüfen Sie den Datenbankserver und `site.ini [DatabaseSettings]`, oder führen Sie ihn mit dem richtigen `--siteaccess` aus |
| 4 | interner Fehler | sehen Sie in `var/log/error.log` nach und melden Sie ihn mit dem Befehl, den Sie ausgeführt haben |

## Nichts hat sich geändert

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| `Would change (dry run, nothing changed; --apply makes the change)` | Der Befehl lief als Probelauf. Geben Sie `--apply` an. |
| `--apply and --dry-run exclude each other` | Geben Sie nur eines davon an. |
| `the account is disabled already: nothing to do` | Eine Warnung, kein Fehler. Das Konto ist in dem Zustand, den Sie verlangt haben. |

## Das Konto oder die Gruppe wird nicht gefunden

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| `no account has the login ...` | Prüfen Sie die Schreibweise, oder nehmen Sie die ID aus `exp:user:list --search=...`. Ein Anmeldename wird ohne Rücksicht auf Groß- und Kleinschreibung verglichen. |
| `2 accounts share the e-mail address ...` | Nennen Sie das Konto mit Anmeldename oder ID. `exp:user:list --state=duplicate_email` listet solche Konten auf. |
| `2 user groups are named ...` | Nennen Sie die Gruppe mit ihrer Knoten-ID. `exp:user:groups` zeigt die Knoten-IDs. |
| `node ... is not a user group` | Die Knoten-ID gehört zu etwas anderem. Finden Sie die Gruppe mit `exp:user:groups`. |
| `--as: ...` oder `UserCreatorID ... names no account` | Der handelnde Benutzer existiert nicht. Nennen Sie mit `--as` ein bestehendes Konto. |

## Der Befehl verweigert

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| `this is the last enabled account with every right` | Aktivieren oder legen Sie zuerst einen anderen Administrator an. |
| `the administrator account ... is protected`, `the anonymous user ...`, `the user who acts ...` | Ein geschütztes Konto wird nie deaktiviert oder entfernt ([Geschützte Konten](konten-deaktivieren-aktivieren-und-entfernen.md#geschützte-konten)). |
| `the account was never activated` bei enable | Nehmen Sie `exp:user:approve <user> --apply`. |
| `the registration does not wait` bei approve | Das Konto ist schon aktiv oder hat keine wartende Registrierung. Prüfen Sie es mit `exp:user:show`. |
| Eine Gruppe lässt sich nicht löschen | Sie hat noch Mitglieder oder Untergruppen. Verschieben Sie diese zuerst. |
| Eine Sammeländerung verweigert `--confirm` | Die Anzahl stimmt nicht mehr mit dem überein, was die Filter auswählen. Führen Sie den Probelauf erneut aus und nehmen Sie die neue Anzahl. |
| Ein Import verweigert die Datei | Sie hat eine Passwortspalte, oder keines oder beide von `--send-activation` und `--disabled`. |
| Ein Import verweigert Zeilen | Jede verweigerte Zeile hat ihre Zeilennummer und Gründe: ein ungültiger Anmeldename oder eine ungültige Adresse, oder eine Gruppe, die es nicht gibt. Korrigieren Sie die Zeilen und importieren Sie erneut; bestehende Anmeldenamen werden übersprungen. |
| `exp:user:2fa --reset` kann nicht zurücksetzen | Die Extension `sevenx_authentication_2fa` ist nicht aktiv. |

## Passwörter und Mails

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| `--password-stdin: standard input holds no password` | Leiten Sie das Passwort in den Befehl um. Ein interaktives Terminal wartet auf eine Zeile. |
| `choose exactly one of --send-link, --generate and --password-stdin` | `exp:user:reset-password` braucht einen Weg zum Passwort. |
| Eine Passwortprüfung schlägt fehl | Das Passwort erfüllt die Passwortregeln nicht. Nehmen Sie `--generate` oder ein längeres Passwort. |
| Der Link zum Zurücksetzen oder Aktivieren zeigt auf den falschen Host | Der Link wird aus dem Siteaccess gebildet. Führen Sie den Befehl mit `--siteaccess=<der öffentliche Siteaccess>` aus. |
| `the publish waits` nach create | Ein Workflow hält das Konto zurück, meist die Freigabe neuer Konten: `exp:user:pending`, dann `exp:user:approve`. |

## Verwandte Seiten

- [Benutzerverwaltung von der Konsole: FAQ](faq.md)
- [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md): `errors` und `warnings` für
  Skripte.
