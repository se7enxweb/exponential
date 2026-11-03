# Benutzerverwaltung von der Konsole

Diese Anleitung ist für Administratoren und Entwickler einer Exponential-6.0.15+-Installation, die Benutzerkonten aus
einer Shell, einem Deploy-Skript oder einem CI-Job verwalten: Konten auflisten und ansehen, anlegen und ändern,
deaktivieren, aktivieren und entfernen, Passwörter zurücksetzen und Registrierungen freigeben. Die Befehle `exp:user`
tun das jeweils so wie die Administration, deshalb verhalten sich Workflows, die Freigabe neuer Konten, das Audit und
das Benutzer-Auditprotokoll genau wie bei einem Klick.

[Anleitungen](README.md) · Verwandt: [Das Hilfehandbuch Benutzerverwaltung von der Konsole](../hilfe/benutzerverwaltung-von-der-konsole/README.md),
[Sicherheit und Audit](sicherheit-und-audit.md),
[Das Benutzer-Auditprotokoll](../hilfe/das-benutzer-auditprotokoll.md),
[Konten für Redakteure und Administratoren einrichten](../hilfe/konten-fuer-redakteure-und-administratoren-einrichten.md),
[Mitglieder verwalten](../hilfe/mitglieder-verwalten.md), [Konsolenbefehle](../beispiele/befehle/README.md)

## Was Sie am Ende haben

- Jedes Konto gefunden und angesehen: seine Gruppen, Rollen, sein Zustand, seine Registrierung und Anmeldungen.
- Ein neues Konto in den richtigen Gruppen, mit einem Passwort, das niemand auf einer Befehlszeile tippen musste.
- Ein Konto sofort deaktiviert, wenn jemand geht, oder endgültig entfernt, ohne dass Sie sich selbst aussperren.
- Ein vergessenes Passwort zurückgesetzt und eine wartende Registrierung freigegeben oder abgelehnt.
- Dasselbe in einem Skript, mit `--format=json` und verlässlichen Exit-Codes.

## Die Befehle

| Befehl | Was er tut | Ändert etwas |
|---|---|---|
| `exp:user:list` | Listet Konten auf: nach Zustand, Gruppe und Suche, seitenweise | nein |
| `exp:user:show <user>` | Ein Konto: Gruppen, Rollen, Zustand, Registrierung, Anmeldungen, Anmeldung in zwei Schritten, Sitzungen | nein |
| `exp:user:create` | Legt ein Konto über das Veröffentlichen von Inhalten an | ja |
| `exp:user:edit <user>` | E-Mail-Adresse, Anmeldename, Namen, Gruppen, Hauptgruppe | ja |
| `exp:user:disable <user>` | Deaktiviert das Konto und beendet seine Sitzungen | ja |
| `exp:user:enable <user>` | Aktiviert ein deaktiviertes Konto wieder | ja |
| `exp:user:delete <user>` | Entfernt das Konto endgültig | ja |
| `exp:user:reset-password <user>` | Sendet den Link zum Zurücksetzen, oder setzt ein erzeugtes Passwort oder eines von der Standardeingabe | ja |
| `exp:user:pending` | Listet die wartenden Registrierungen auf | nein |
| `exp:user:approve <user>` | Gibt eine wartende Registrierung frei, oder lehnt sie mit `--reject` ab | ja |

`<user>` ist ein Anmeldename, eine E-Mail-Adresse (wenn nur ein Konto sie hat) oder die ID des Kontos (die ID seines
Inhaltsobjekts). Jeder Befehl zeigt mit `--help` alle seine Optionen; die erzeugten Seiten der
[Referenz der Konsolenbefehle](../beispiele/befehle/README.md) führen sie ebenfalls auf.

## Drei Regeln, die jeder Befehl befolgt

**Ein Probelauf ohne `--apply`.** Ein Befehl, der etwas ändert, listet zuerst auf, was er tun würde und welche
Prüfungen fehlschlagen, und ändert nichts. Mit `--apply` nimmt er die Änderung vor. `--dry-run` wird angenommen und
bedeutet dasselbe wie keine Option; beide zusammen sind ein Bedienfehler.

```bash
./console exp:user:disable jane          # Würde ändern: das Konto jane deaktivieren und seine Sitzungen beenden
./console exp:user:disable jane --apply  # Geändert: ...
```

**Kein Passwort auf der Befehlszeile.** Ein als Argument getipptes Passwort bleibt im Verlauf der Shell und erscheint
in der Prozessliste des Servers. Die Befehle nehmen deshalb nie `--password=...`. Wählen Sie eines davon:

- `--generate-password` (create) oder `--generate` (reset-password): ein Passwort, das die Passwortregeln erfüllt,
  einmal ausgegeben und nie wieder;
- `--password-stdin`: eine Zeile von der Standardeingabe, aus einem Passwortmanager, einer Datei mit Modus 0600 oder
  einem CI-Geheimnis;
- `--send-activation` (create) oder `--send-link` (reset-password): die Person wählt das Passwort selbst.

**Wer handelt, wird festgehalten.** Die Befehle handeln als der Benutzer aus `site.ini [UserSettings] UserCreatorID`
(der Administrator einer neuen Installation) oder als der Benutzer, den Sie mit `--as=<login>` nennen. Das Audit und
das Benutzer-Auditprotokoll nennen diesen Benutzer und den Siteaccess, genau wie bei einer Änderung in der
Administration.

## Konten finden und ansehen

```bash
./console exp:user:list
./console exp:user:list --state=disabled
./console exp:user:list --group=Editors --sort=last_visit --order=desc
./console exp:user:list --state=inactive --days=365
./console exp:user:list --search=example.org --limit=100 --offset=100
```

`--state` nimmt die Filter von **Benutzer > Hygiene** und zwei eigene der Konsole:

| Zustand | Konten |
|---|---|
| `all` | jedes Konto, dessen Objekt nicht im Papierkorb liegt (die Vorgabe) |
| `enabled`, `disabled` | aktiviert oder deaktiviert (ein Konto ohne seine Einstellungszeile gilt als deaktiviert) |
| `unactivated` | deaktiviert mit einem Aktivierungsschlüssel: die Registrierung wurde nie abgeschlossen |
| `pending` | wartende Registrierungen (siehe [Registrierungen freigeben](#registrierungen-freigeben)) |
| `locked` | mehr fehlgeschlagene Anmeldungen als `[UserSettings] MaxNumberOfFailedLogin` |
| `never` | nie angemeldet |
| `inactive` | früher angemeldet, aber seit `--days` Tagen nicht mehr (Vorgabe 180) |
| `orphaned` | kein Objekt, ein nie veröffentlichtes Objekt oder keine Platzierung |
| `duplicate_login`, `duplicate_email` | ein Anmeldename oder eine Adresse, die auch ein anderes Konto hat |

`--group` nimmt eine Knoten-ID oder den Namen einer Benutzergruppe und listet ihre Mitglieder in jeder Tiefe auf.
`--search` sucht im Anmeldenamen, im Namen und in der E-Mail-Adresse.

```bash
./console exp:user:show jane
./console exp:user:show jane@example.com
./console exp:user:show 1234 --format=json
```

`exp:user:show` ergänzt, was die Liste weglässt: jede Gruppe mit der Hauptgruppe zuerst, jede Rolle mit ihrer Herkunft
(das Konto oder eine Gruppe) und ihrer Einschränkung, den Zustand der Registrierung, die letzte Passwortänderung, die
das Audit kennt, ob bei der nächsten Anmeldung ein neues Passwort verlangt wird, die Anmeldung in zwei Schritten, die
Zahl der aktiven Sitzungen und ob das Konto geschützt ist (siehe unten). Einen Passwort-Hash zeigt es nie.

## Ein Konto anlegen

```bash
./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
    --group=Editors --generate-password
./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
    --group=Editors --generate-password --apply
```

Das Konto ist ein Inhaltsobjekt der Benutzerklasse (`[UserSettings] UserClassID` oder `--class`) in der ersten
`--group`, veröffentlicht mit der Operation content/publish. Wiederholen Sie `--group` für weitere Gruppen; die erste
ist die Hauptgruppe. Ohne `--group` kommt das Konto nach `[UserSettings] DefaultUserPlacement`.

Weil das Veröffentlichen seine Workflows ausführt, hält eine Installation, die neue Konten freigeben lässt (zum
Beispiel neue Redakteure, siehe [Freigabe von Benutzerkonten](../bc/6.0/freigabe-von-benutzerkonten.md)), das Konto
zurück, bis es freigegeben ist: der Befehl sagt es, und das Konto erscheint in `exp:user:pending`.

Die Prüfungen sind die der Administration: ein gültiger Anmeldename, den niemand hat, eine gültige E-Mail-Adresse (mit
`[UserSettings] RequireUniqueEmail` eine, die niemand hat), und ein Passwort, das die Passwortregeln erfüllt. Eine
fehlgeschlagene Prüfung wird genannt und nichts angelegt, mit oder ohne `--apply`.

Die drei Wege zum Passwort:

```bash
# einmal ausgegeben: geben Sie es der Person auf einem sicheren Weg
./console exp:user:create --login=jane --email=jane@example.com --group=Editors --generate-password --apply

# aus einem Geheimnis: nie auf der Befehlszeile
printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:create --login=ci-bot --email=ci@example.com \
    --group=Editors --password-stdin --apply

# die Person aktiviert das Konto aus der Mail und wählt dann mit „Passwort vergessen“ ein Passwort
./console exp:user:create --login=member1 --email=member1@example.com --group=Members --send-activation --apply
```

`--disabled` legt das Konto deaktiviert an; aktivieren Sie es später mit `exp:user:enable`.

## Ein Konto ändern

```bash
./console exp:user:edit jane --email=jane.doe@example.com --apply
./console exp:user:edit jane --login=jdoe --apply
./console exp:user:edit jane --first-name=Janet --apply
./console exp:user:edit jane --add-group=Editors --remove-group=Members --main-group=Editors --apply
```

Eine neue E-Mail-Adresse und ein neuer Anmeldename werden geprüft wie beim Anlegen, und das Audit hält jede Änderung
fest (`access.user.email.change`, `access.user.login.change`). Ein neuer Name ist eine neue Version des Objekts,
veröffentlicht mit seinen Workflows. `--add-group` fügt eine Platzierung in einer Gruppe hinzu, `--remove-group`
entfernt eine; ein Konto behält immer mindestens eine Gruppe. Wird die Hauptgruppe entfernt, wird die nächste Gruppe
zur Hauptgruppe, oder die, die Sie mit `--main-group` nennen.

## Deaktivieren, aktivieren und entfernen

```bash
./console exp:user:disable jane --apply
./console exp:user:enable jane --apply
./console exp:user:delete jane --apply
```

`exp:user:disable` tut, was **Benutzereinstellungen bearbeiten** tut: das Konto wird deaktiviert und alle seine
Sitzungen enden, auch die erfassten unter **Ihre Geräte**. Nichts wird entfernt; `exp:user:enable` macht es
rückgängig. Nutzen Sie es, wenn jemand geht oder ein Konto übernommen worden sein könnte.

`exp:user:enable` verweigert ein Konto, das nie aktiviert wurde (es hat noch seinen Aktivierungsschlüssel): geben Sie
seine Registrierung stattdessen mit `exp:user:approve` frei, damit die Registrierung mit den dazugehörigen Mails und
Schritten abgeschlossen wird.

`exp:user:delete` entfernt die Platzierungen des Kontos endgültig, wie es die Administration tut: Konten kommen nie in
den Papierkorb, deshalb gehen das Konto, seine Einstellungen und seine Sitzungen. Die Inhalte, die die Person angelegt
hat, bleiben.

**Geschützte Konten.** `exp:user:disable` und `exp:user:delete` verweigern:

| Konto | Warum |
|---|---|
| der anonyme Benutzer (`[UserSettings] AnonymousUserID`) | jeder Besucher ist dieser Benutzer |
| der Benutzer aus `[UserSettings] UserCreatorID` | der Kernel legt Konten als dieser Benutzer an |
| das Administratorkonto (ID 14 oder der Anmeldename `admin`) | das Konto einer neuen Installation |
| der handelnde Benutzer (`--as`) | Sie sperren sich nicht selbst aus |
| der letzte aktivierte Administrator | ein Konto mit allen Rechten (eine Rolle mit einer Richtlinie auf jedes Modul) muss bleiben |

`exp:user:show` sagt, ob ein Konto geschützt ist und warum.

## Ein Passwort zurücksetzen

```bash
./console exp:user:reset-password jane --send-link --apply
./console exp:user:reset-password jane --generate --require-change --apply
./console exp:user:reset-password ci-bot --password-stdin --apply < /root/secrets/ci-bot-password
```

- `--send-link` sendet die Mail von **Passwort vergessen** mit ihrem Einmal-Link an die Adresse des Kontos. Das
  Passwort bleibt, bis die Person den Link benutzt, und der Link gilt für `[UserSettings] ForgotPasswordKeyLifetime`.
- `--generate` setzt ein Passwort, das die Passwortregeln erfüllt, und gibt es einmal aus.
- `--password-stdin` setzt das von der Standardeingabe gelesene Passwort; es muss die Passwortregeln erfüllen.
- `--require-change` (mit `--generate` oder `--password-stdin`) lässt die Person bei der nächsten Anmeldung ein neues
  Passwort wählen.

`--generate` und `--password-stdin` beenden die Sitzungen des Kontos, wie es eine Passwortänderung in der
Administration tut. Das Audit hält `access.user.password.change` fest (oder `access.user.password.reset.request` für
den Link), nie das Passwort. Im Demomodus (`EXP_DEMO_MODE=1`) wird das Passwort eines Administrators nie geändert.

## Registrierungen freigeben

Eine Registrierung wartet auf eine von drei Arten, die `exp:user:pending` in seiner Spalte State nennt:

| Zustand | Wartet auf |
|---|---|
| `email` | die Person, die ihre E-Mail-Adresse bestätigt (das Konto hat einen Aktivierungsschlüssel) |
| `administrator` | einen Administrator, der das Konto aktiviert (es ist deaktiviert, hat keinen Aktivierungsschlüssel und war nie angemeldet) |
| `workflow` | einen Freigebenden, der den Freigabeeintrag zur Veröffentlichung des Kontos annimmt |

```bash
./console exp:user:pending
./console exp:user:approve member1 --apply
./console exp:user:approve spam-account --reject --apply
```

Die Freigabe aktiviert das Konto wie **Benutzer > Nicht aktiviert** (die Registrierung wird dann abgeschlossen: das
Konto wird veröffentlicht und die Willkommensmail gesendet), oder nimmt den Freigabeeintrag an und setzt seinen
Workflow sofort fort. `--reject` verweigert den Freigabeeintrag und entfernt das Konto endgültig, wie es der
Ablehnungslink der Registrierungsmail tut; ein aktives Konto entfernt `--reject` nie. Das Audit hält
`access.registration.approve` oder `access.registration.reject` fest.

## Gruppen, Rollen, Anmeldesicherheit und viele Konten auf einmal

| Befehl | Was er tut | Ändert etwas |
|---|---|---|
| `exp:user:groups [list\|create\|rename\|move\|delete] [group]` | Der Baum der Benutzergruppen mit Mitgliedern; eine Gruppe angelegt (`--name`, `--parent`), umbenannt, verschoben (`--to`) oder, wenn sie leer ist, in den Papierkorb gelegt | ja, außer `list` |
| `exp:user:roles list <target>` | Die Rollen, die einen Benutzer oder eine Gruppe erreichen, mit Herkunft und Einschränkung | nein |
| `exp:user:roles assign\|unassign <role> <target>` | Eine Rolle zugewiesen oder entzogen, auf Wunsch eingeschränkt mit `--subtree=<Knoten>` oder `--section=<ID>`; `<target>` ist ein Benutzer oder `group:<Knoten-ID oder Name>` | ja |
| `exp:user:whoami` | Das Konto, als das die Konsole handelt, seine Rollen, ob es alle Rechte hat | nein |
| `exp:user:sessions <user>` | Die laufenden Sitzungen (Ihre Geräte); `--end` beendet alle, `--session=<ID>` eine | mit `--end` oder `--session` |
| `exp:user:unlock <user>` | Die fehlgeschlagenen Anmeldungen eines gesperrten Kontos zurück auf 0 | ja |
| `exp:user:2fa <user>` | Anmeldung in zwei Schritten und Passkeys; `--reset` bei einem verlorenen Telefon, `--remove-passkeys` bei einem verlorenen Gerät | mit `--reset` oder `--remove-passkeys` |
| `exp:user:require-password-change <user>` | Bei der nächsten Anmeldung ein neues Passwort verlangen; `--clear` entfernt die Markierung | ja |
| `exp:user:resend-activation <user>` | Die Aktivierungsmail erneut, mit einem neuen Schlüssel | ja |
| `exp:user:check-login <user> --password-stdin` | Ob ein Passwort das Konto anmeldet; nie eine fehlgeschlagene Anmeldung | nein |
| `exp:user:export` | Die Konten, die die Listenfilter auswählen, als CSV oder JSON (`--type`, `--output`); nie ein Passwort oder Hash | nein |
| `exp:user:import <file>` | Konten aus einer CSV- oder JSON-Datei, jedes angelegt wie mit `exp:user:create`; `--send-activation` oder `--disabled`; eine Passwortspalte wird verweigert | ja |
| `exp:user:bulk <action>` | `disable`, `enable`, `delete`, `approve`, `reject` oder `unlock` für jedes Konto, das die Filter auswählen; `--apply` braucht `--confirm=<Anzahl>` | ja |
| `exp:user:audit <user>` | Das Benutzer-Auditprotokoll des Kontos, gefiltert mit `--event`, `--result`, `--from`, `--to` | nein |
| `exp:user:hygiene` | Die Zahl der Konten in jedem Zustand, mit dem Befehl, der sie auflistet | nein |

```bash
# eine Gruppe für eine Partneragentur, mit der Rolle Editor in einem Teilbaum
./console exp:user:groups create --name=Partners --parent=Editors --apply
./console exp:user:roles assign Editor group:Partners --subtree=2 --apply

# jemand geht: deaktivieren, jede Sitzung beenden, dann das Audit prüfen
./console exp:user:disable jane --apply
./console exp:user:audit jane --from=2026-10-01

# ein ausgesperrter Administrator, aus der Shell des Servers
./console exp:user:unlock admin --apply
./console exp:user:reset-password admin --generate --apply

# Mitglieder aus einer Tabelle, jedes mit der Aktivierungsmail
./console exp:user:import members.csv --group=Members --send-activation           # prüft jede Zeile
./console exp:user:import members.csv --group=Members --send-activation --apply

# seit zwei Jahren ungenutzte Konten deaktiviert, nach einem Blick auf die Liste
./console exp:user:bulk disable --state=inactive --days=730 --group=Members
./console exp:user:bulk disable --state=inactive --days=730 --group=Members --apply --confirm=42
```

`exp:user:bulk` behandelt jedes Konto wie der einzelne Befehl: geschützte Konten und der letzte Administrator werden
als verweigert aufgeführt und nie geändert. `--confirm` muss die Zahl sein, die der Probelauf gezeigt hat; hat sich
die Auswahl inzwischen geändert, wird der Lauf verweigert und ändert nichts. Ein `delete` in großer Zahl braucht einen
Filter: es entfernt nie jedes Konto.

Eine Importdatei hat die Spalten `login`, `email`, `first_name`, `last_name` und `groups` (Knoten-IDs oder Namen,
getrennt durch `|`). Sie enthält nie ein Passwort: eine Datei mit einer Passwortspalte wird als Ganzes verweigert. Ein
vorhandener Anmeldename wird übersprungen, deshalb kann eine korrigierte Datei erneut importiert werden.
`exp:user:export` schreibt dieselben Spalten und mehr; ein Export enthält personenbezogene Daten, behandeln Sie ihn
entsprechend (`--output` legt die Datei mit Modus 0600 an).

Bewusst nicht angeboten: zwei Konten zusammenführen (der Kernel hat keinen sicheren Weg, Eigentum und Verlauf eines
Kontos auf ein anderes zu übertragen), Passwörter in Importdateien und eine Option `--password`.

## In Skripten und CI

Jeder Befehl nimmt `--format=json` und gibt ein JSON-Objekt mit `schema` (`exp-user/1`), `command`, `mode`, `errors`,
`warnings` und `exit` aus, dazu die Schlüssel des Befehls: `accounts` und `total` für die Listen, `account` für
`show`, und für die ändernden Befehle `dry_run`, `applied`, `changes` und `user` (ID, Anmeldename, E-Mail-Adresse,
Name, aktiviert). Ein erzeugtes Passwort ist der Schlüssel `generated_password`, nur in dem Lauf vorhanden, der es
gesetzt hat.

| Exit-Code | Bedeutung |
|---|---|
| 0 | erledigt (oder, ohne `--apply`, nichts verweigert) |
| 1 | fehlgeschlagen oder verweigert: das Konto wurde nicht gefunden, eine Prüfung schlug fehl, das Konto ist geschützt |
| 2 | Bedienfehler: eine falsche Option oder ein falscher Wert |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

```bash
# ein Deployment scheitern lassen, wenn ein Dienstkonto fehlt oder deaktiviert ist
./console exp:user:show ci-bot --format=json | jq -e '.account.enabled' > /dev/null

# jedes wartende Konto, als ID und Anmeldename
./console exp:user:pending --format=json --limit=500 | jq -r '.accounts[] | "\(.id) \(.login)"'
```

Optionen und Schlüssel sind ein dauerhafter Vertrag: neue können hinzukommen, keiner wird je umbenannt oder entfernt
(`tests/tests/kernel/classes/user/expUserCommandContractTest.php` prüft es).

## Fehlerbehebung

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| `no account has the login ...` | Prüfen Sie die Schreibweise, oder nehmen Sie die ID aus `exp:user:list --search=...`. Ein Anmeldename wird ohne Rücksicht auf Groß- und Kleinschreibung verglichen. |
| `2 accounts share the e-mail address ...` | Nennen Sie das Konto mit Anmeldename oder ID. `exp:user:list --state=duplicate_email` listet solche Konten auf. |
| `2 user groups are named ...` | Nennen Sie die Gruppe mit ihrer Knoten-ID (`exp:user:show <user>` und `exp:user:list` zeigen Knoten-IDs). |
| `the publish waits` nach create | Ein Workflow hält das Konto zurück, meist die Freigabe neuer Konten: `exp:user:pending`, dann `exp:user:approve`. |
| `the account was never activated` bei enable | Nehmen Sie `exp:user:approve <user> --apply`. |
| `this is the last enabled account with every right` | Aktivieren oder legen Sie zuerst einen anderen Administrator an. |
| `--password-stdin: standard input holds no password` | Leiten Sie das Passwort in den Befehl um; ein interaktives Terminal wartet auf eine Zeile. |
| Der Link zum Zurücksetzen zeigt auf den falschen Host | Der Link wird aus dem Siteaccess gebildet: führen Sie den Befehl mit `--siteaccess=<der öffentliche Siteaccess>` aus. |
