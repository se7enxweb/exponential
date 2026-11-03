# Migrationen: Generatoren und das Aufzeichnen von Änderungen

Eine Migration von Hand zu schreiben ist der genaue Weg; sie aufzuzeichnen der schnelle. Während eine **Aufzeichnung**
läuft, wird jede Änderung, die Sie in der Administration, in der Website-Seitenleiste oder mit einem Befehl machen,
als Schritt einer YAML-Migration festgehalten: Inhalt veröffentlicht, eine Klasse gespeichert, eine Rolle zugewiesen,
eine Einstellung geschrieben. Danach sehen Sie sich an, was sie erfasst hat, erzeugen daraus eine Migration und führen
diese auf Staging und Produktion aus.

Diese Seite erklärt die Aufzeichnung und die Generatoren von Exponential 6.0.15: den, der aus einer Aufzeichnung eine
Migrationsdatei macht, den, der eine Migration dessen schreibt, was eine Installation jetzt hat (Inhalte, Klassen,
Rollen, Sektionen, Einstellungen), den, der eine Migration dessen schreibt, was sich seit einem gespeicherten Stand
geändert hat (ein Sicherungspunkt, eine andere Installation, ein Datenbank-Dump), die Gerüste jeder Art von
Migrationsdatei, den Editor der Administration und den Konverter aus dem YAML des Referenz-Bundles. Die Ausgaben unten
entstanden am 1. und 2. November 2026 auf SQLite.

[Anleitungen](README.md) · [Migrationen](migrationen.md) · Verwandt: [PHP-Migrationen](migrationen-php.md),
[Rücknahme und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md), [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md#aufgezeichnete-ereignisse-exp-migration-recording-event1),
[Aufzeichnen in der Administration](../hilfe/migrationen/aufzeichnung.md)

## Kurz

```sh
./console exp:migration:record:start --label="news section" --apply   # meine Änderungen ab jetzt aufzeichnen
# ... die Änderungen in der Administration machen ...
./console exp:migration:record:stop --apply                           # beenden
./console exp:migration:record:show <id>                              # was sie erfasst hat
./console exp:migration:record:show <id> --plan                       # die Schritte, die eine Migration daraus hätte
./console exp:migration:generate --from-recording=<id> news_section   # die Migration daraus, ein Probelauf
./console exp:migration:generate --from-recording=<id> news_section --apply   # nach migrations/ geschrieben
./console exp:migration:generate --from-class=news_article --from-role="News editor" news_setup   # aus dem, was da ist
./console exp:migration:generate --skeleton news_index --type=sql --apply      # eine leere SQL-Migration
```

Dasselbe in der Administration: **Setup > Migrationen > Aufzeichnung**, mit einem Balken „Aufzeichnung…“ auf jeder
Seite, solange Ihre Änderungen aufgezeichnet werden.

## Was aufgezeichnet wird

Eine Aufzeichnung sieht die **Operationen** des Kernels, keine Web-Anfragen. Sie hängt an denselben Stellen, von
denen das [Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md) aufzeichnet, ob das Prüfprotokoll diese Ereignisse
selbst festhält oder nicht: Wird ein Knoten verschoben, meldet der Kernel einmal „content.node.move“, wo immer
verschoben wurde (Administration, Website-Seitenleiste, ein REST-Aufruf, ein Befehl), und die Aufzeichnung hält
dieses eine Ereignis fest. Eine Seite, die nur liest, eine Suche, eine Anmeldung oder ein Cache-Leeren ändert nichts
und wird nie aufgezeichnet.

| Bereich | Aufgezeichnet |
|---|---|
| Inhalt | veröffentlicht (ein neues Objekt, eine neue Version, eine Übersetzung hinzugefügt oder entfernt), immer verfügbar, Ausgangssprache, Objektzustände, entfernt, endgültig gelöscht, aus dem Papierkorb wiederhergestellt |
| Orte | hinzugefügt, verschoben, kopiert, verborgen, sichtbar gemacht, entfernt, in den Papierkorb gelegt, Sortierung, Prioritäten der Kinder, Hauptort, getauscht, Sektion eines Teilbaums |
| Klassen | angelegt, geändert, kopiert, entfernt (wie Setup > Klassen sie speichert) |
| Sektionen, Objektzustände | angelegt oder geändert, entfernt |
| Rollen | angelegt, geändert, kopiert, entfernt, eine Richtlinie hinzugefügt oder entfernt, zugewiesen und entzogen |
| Benutzer und Gruppen | angelegt, geändert, verschoben, aktiviert, gesperrt und entsperrt, entfernt (ohne personenbezogene Daten, siehe unten); eine Benutzergruppe ist Inhalt |
| URL-Aliase | ein Alias oder ein Platzhalter hinzugefügt oder entfernt |
| Einstellungen | jede Variable, die über den Einstellungseditor (expIniEditor) geschrieben wird, mit Datei und Geltungsbereich |
| Workflows | die geänderten Auslöser |

Jedes aufgezeichnete Ereignis wird in der Form des Migrationsschritts gespeichert, für den es steht, jede ID in einer
**stabilen Form**: Knoten und Objekte über ihre Remote-IDs (`remote:<remote id>`, wie
[YAML-Migrationen](../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung) sie nennen),
Klassen, Sektionen und Objektzustände über ihren Bezeichner, Rollen über ihren Namen, Sprachen über ihr Locale.
Numerische IDs unterscheiden sich zwischen Installationen; diese Namen nicht. Die Werte eines Schritts sind der Stand
**nach** der Änderung (die Felder eines Inhaltsobjekts in jeder Sprache, die ganze Definition einer Klasse, die
Richtlinien einer Rolle mit ihren Einschränkungen), so dass das letzte Ereignis einer Sache das Ganze ist.

Ein Migrationslauf wird nicht aufgezeichnet: Seine Änderungen sind schon eine Migration.

## Zwei Arten von Aufzeichnung

| Art | Zeichnet auf | Gestartet von | Gleichzeitig |
|---|---|---|---|
| **Sitzung** (Standard) | die Änderungen eines Benutzers, wo immer er sie macht: Administration, Website-Seitenleiste, ein Befehl, der als dieser Benutzer läuft | dem Benutzer in Setup > Migrationen oder der Website-Seitenleiste; `exp:migration:record:start --user=<login>` | eine je Benutzer |
| **websiteweit** | jede Änderung jedes Benutzers und der Konsole | einem Inhaber von `migration/edit` in Setup > Migrationen (fragt nach [„Identität bestätigen“](identitaet-bestaetigen.md)); `exp:migration:record:start --site-wide` | eine |

Eine Sitzungsaufzeichnung hält fest, was Sie selbst einrichten. Das websiteweite Fenster ist für mehrere Personen,
die gemeinsam auf einer Staging-Installation ein Release vorbereiten. Eine Änderung, während beide laufen, steht in
beiden. Beide lassen sich für ein Zeitfenster öffnen, das sich selbst schließt (`--for=<Minuten>`, `--until=<Zeit>`;
[Ein Aufzeichnungsfenster auf Zeit](migrationen-pakete-deploy-und-lange-laeufe.md#ein-aufzeichnungsfenster-auf-zeit)).

Solange das websiteweite Fenster offen ist, sieht jeder Benutzer der Administration den Balken „Aufzeichnung…“, weil
auch seine Änderungen aufgezeichnet werden; schließen können es nur Inhaber von `migration/edit`.

## Personenbezogene Daten

Das Konto eines Benutzers (Login, E-Mail-Adresse, Passwort-Hash) und die Felder von Benutzerobjekten werden **nie
aufgezeichnet**: Das Ereignis eines Benutzers behält seine Remote-ID, seine Klasse, seine Gruppen und ob er aktiv ist,
und nennt in `omitted`, was weggelassen wurde. Eine erzeugte Migration wird das Konto über
[Geheimnis-Referenzen](../funktionen/6.0/geheimnisspeicher.md) nennen, so dass keine Adresse und kein Passwort je in
ein Migrations-Repository gelangt, das gepusht wird.

Eine Website kann einzelne Attribute von Benutzerobjekten erlauben, die keine personenbezogenen Daten sind (etwa die
„signature“ eines Teamkontos), mit `[RecordingSettings] UserKeepAttributeList[]`. Wer eine Änderung gemacht hat, wird
nur als Benutzer-ID in der Aufzeichnungstabelle der Installation gehalten; es ist nie Teil eines Schritts, und eine
erzeugte Migration nennt niemanden. Eine Einstellung, deren Name sagt, dass sie ein Geheimnis ist (Passwort, Token,
Schlüssel ...), wird ohne ihren Wert aufgezeichnet (`"secret": true`).

## Die Befehle

Alle geben Text aus, mit `--format=json` die Maschinenausgabe `exp-migration/1`. Start, Stopp und Verwerfen sind
Probeläufe ohne `--apply`. Eine Sitzungsaufzeichnung gehört `--user` (nach Login; Standard `[RunSettings] RunAsUser`
der `migration.ini`, `admin`). Die Befehle geben englischen Text aus.

### Starten

```text
$ ./console exp:migration:record:start --label="news section"
Exponential migrations: start a recording, dry run (--apply starts it)
Would record the changes of admin, "news section"
PASS

$ ./console exp:migration:record:start --label="news section" --apply
Exponential migrations: start a recording
Recording 20261101-124707-de8bac started: the changes of admin, "news section"
Next: make the changes, then ./console exp:migration:record:stop --user=admin --apply
PASS
```

Eine zweite Sitzungsaufzeichnung desselben Benutzers oder ein zweites websiteweites Fenster wird abgelehnt (Exit 1).
Ohne die Tabellen (Upgrade-Schritt `6.0.15-migration-recording-tables`, angelegt von `./console exp:upgrade --apply`)
kann nichts gestartet werden.

### Stand

```text
$ ./console exp:migration:record:status
Exponential migrations: recordings of changes that run
  20261101-124707-de8bac recording session     5 event(s)  2026-11-01 12:47  user #14  "news section"
The changes of admin are recorded now
PASS
```

Welche Aufzeichnungen laufen, steht auch in einer kleinen Datei, `var/<site>/migration/recording/active.json`, damit
eine Anfrage ohne Datenbankabfrage weiß, ob etwas aufgezeichnet wird. Der Befehl schreibt sie aus der Datenbank neu,
wenn beide sich unterscheiden (nachdem `var/` geleert wurde oder auf einem neuen Server), und sagt das mit einer Warnung.

### Beenden, auflisten

```text
$ ./console exp:migration:record:stop --apply
Exponential migrations: stop a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
Next: ./console exp:migration:record:show 20261101-124707-de8bac, then ./console exp:migration:generate --from-recording=20261101-124707-de8bac
PASS

$ ./console exp:migration:record:list
Exponential migrations: recordings of changes, the newest first
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
PASS
```

`stop` nimmt die ID der Aufzeichnung, oder beendet die Sitzungsaufzeichnung von `--user`, oder mit `--site-wide` das
Fenster. `list --count=<n>` listet weniger.

### Zeigen

```text
$ ./console exp:migration:record:show 20261101-124707-de8bac
Exponential migrations: a recording of changes
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"

  12:47:07  content.section.change           section/ensure section news
  12:47:07  content.object.create            content/create object remote:news-folder
  12:47:07  content.node.sort                location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
  12:47:07  access.role.assign               role/assign role Editor
  12:47:07  content.node.hide                location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618

Summary: 5 event(s)
PASS
```

Jede Zeile nennt die Zeit, das Audit-Ereignis und den Schritt, für den es steht, mit dem, worum es geht.
`--format=json` gibt die Ereignisse vollständig aus; das Inhaltsereignis dieser Sitzung:

```json
{
    "v": 1,
    "audit": "content.object.create",
    "subject": "object remote:news-folder",
    "step": {
        "type": "content",
        "mode": "create",
        "class": "folder",
        "parent": "remote:f3e90596361e31d496d4026eb624c983",
        "remote_id": "news-folder",
        "section": "standard",
        "always_available": true,
        "initial_language": "eng-US",
        "states": {
            "ez_lock": "not_locked"
        },
        "fields": {
            "eng-US": {
                "description": "",
                "name": "News",
                "short_description": "",
                "short_name": "",
                "show_children": "1"
            }
        }
    },
    "omitted": [],
    "notes": []
}
```

`--plan` listet stattdessen die Schritte, die eine Migration der Aufzeichnung hätte (siehe
[Zusammenfassen](#die-ereignisse-zu-schritten-zusammenfassen)):

```text
$ ./console exp:migration:record:show 20261101-124707-de8bac --plan
Exponential migrations: the steps of a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"

    1  section/ensure section news  (from content.section.change)
    2  content/create object remote:news-folder  (from content.object.create)
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618  (from content.node.sort)
    4  role/assign role Editor  (from access.role.assign)
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618  (from content.node.hide)

Summary: 5 event(s), 5 step(s)
PASS
```

Ein Schritt, der von den anderen derselben Sache getrennt bleibt (eine Zuweisung, ein Verbergen), bleibt ein eigener
Schritt, in der Reihenfolge, in der er gemacht wurde.

### Verwerfen

```text
$ ./console exp:migration:record:discard 20261101-124707-de8bac --apply
Exponential migrations: discard a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
Discarded: the recording and its events are removed
PASS
```

Verwerfen entfernt die Aufzeichnung der Änderungen, nie die Änderungen. Eine laufende Aufzeichnung, die verworfen
wird, endet.

Die Optionen jedes Befehls: [exp:migration:record:start](../beispiele/befehle/expmigrationrecordstart.md),
[stop](../beispiele/befehle/expmigrationrecordstop.md), [status](../beispiele/befehle/expmigrationrecordstatus.md),
[list](../beispiele/befehle/expmigrationrecordlist.md), [show](../beispiele/befehle/expmigrationrecordshow.md),
[discard](../beispiele/befehle/expmigrationrecorddiscard.md).

## In der Administration

**Setup > Migrationen > Aufzeichnung** (die Ansicht `migration/recording`, Richtlinie `migration/edit`) startet und
beendet die Aufzeichnung Ihrer eigenen Änderungen, öffnet und schließt das websiteweite Fenster, listet jede
Aufzeichnung und zeigt, was sie erfasst hat, die Schritte, die eine Migration daraus hätte, einen Knopf
**Migration erzeugen**, der diese Migration in das Migrationsverzeichnis der Site schreibt, und einen Knopf zum
Verwerfen. Solange Ihre Änderungen aufgezeichnet werden, zeigt jede Seite der Administration einen roten Balken
**Aufzeichnung…** mit **Zeigen** und **Aufzeichnung beenden**. Die Website-Seitenleiste hat Start und Stopp für Ihre
eigene Aufzeichnung in ihrem Bereich Migrationen. Die Seite im Einzelnen:
[Änderungen in der Administration aufzeichnen](../hilfe/migrationen/aufzeichnung.md).

| Richtlinie | Erlaubt |
|---|---|
| `migration/edit` | die eigene Aufzeichnung starten und beenden, das websiteweite Fenster öffnen und schließen, Aufzeichnungen sehen und verwerfen |

Das websiteweite Fenster zu öffnen fragt nach „Identität bestätigen“ (`site.ini [ReauthGroup_system]
Rules[]=migration/recording StartSiteButton`); die eigene Aufzeichnung nicht. Die Konsole wird nie gefragt. Starten,
Beenden und Verwerfen stehen im Prüfprotokoll als `system.migration.record` (das Verb sagt, was).

## Einstellungen

`settings/migration.ini [RecordingSettings]` ([jede Einstellung](../beispiele/ini-einstellungen/migration/RecordingSettings.md)):

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Recording` | `enabled` | `disabled`: nichts wird aufgezeichnet und keine Aufzeichnung kann gestartet werden |
| `EventList[]` | leer | die Audit-Ereignisse, die eine Aufzeichnung festhält; leer: jedes Ereignis der Tabelle unten |
| `UserKeepAttributeList[]` | leer | Attribute von Benutzerobjekten, die keine personenbezogenen Daten sind und aufgezeichnet werden dürfen |
| `MaxEvents` | `10000` | die meisten Ereignisse einer Aufzeichnung; sie läuft dann weiter, fügt aber nichts mehr hinzu und sagt das |

## Die Ereignisse und die Schritte, die daraus werden

Die Tabelle zeigt, was eine Aufzeichnung zu jedem Ereignis festhält: den aufgezeichneten Schritt, im Format der
Aufzeichnung (`exp-migration-recording-event/1`). Der Generator macht aus jedem aufgezeichneten Schritt den Schritt
eines YAML-Schritttyps ([unten](#vom-aufgezeichneten-schritt-zum-migrationsschritt)).

| Audit-Ereignis | Schritttyp / Modus | Die Schlüssel des Schritts |
|---|---|---|
| `content.object.create` | `content` / `create` | `class`, `parent`, `remote_id`, `section`, `always_available`, `initial_language`, `states` (Gruppe => Zustand), `fields` (Locale => Attribut => Wert), `locations` (die anderen Orte: `parent`, `remote_id`) |
| `content.object.publish` | `content` / `update` | `match` und die Schlüssel oben außer `class`, `parent`, `remote_id`; `changed` (die Attribute, die die Version geändert hat) |
| `content.object.translate` | `content` / `update` | wie publish; ein Hinweis nennt die hinzugefügten Sprachen |
| `content.object.translation.remove` | `content` / `update` | wie publish, und `remove_translations` (Locales) |
| `content.object.always_available`, `content.object.initial_language`, `content.object.state` | `content` / `update` | wie publish |
| `content.object.remove`, `content.object.purge` | `content` / `delete` | `match` |
| `content.object.restore` | `trash` / `restore` | `object`, `parent` |
| `content.node.add` | `location` / `create` | `object`, `parent`, `remote_id` |
| `content.node.move` | `location` / `move` | `match`, `parent` |
| `content.node.copy` | `content` / `copy` | `node`, `parent`, `remote_id` (des neuen Objekts), `location_remote_id`, `subtree` (ein Teilbaum kopiert) |
| `content.node.hide` | `location` / `hide` | `match` |
| `content.node.reveal` | `location` / `reveal` | `match` |
| `content.node.sort` | `location` / `update` | `match`, `sort_field` (Name), `sort_order` (`asc`, `desc`) |
| `content.node.priority` | `location` / `update` | `match`, `child_priorities` (Kind-Ort => Priorität) |
| `content.node.main` | `location` / `update` | `match`, `main: true` |
| `content.node.swap` | `location` / `swap` | `match`, `with` |
| `content.node.remove` | `location` / `delete` | `match` |
| `content.node.remove.trash` | `trash` / `move` | `match` |
| `content.node.section` | `section` / `assign` | `match` (die Sektion), `subtree` (der Ort) |
| `content.class.create`, `content.class.copy` | `class` / `create` | `identifier`, `names`, `descriptions`, `contentobject_name`, `url_alias_name`, `is_container`, `always_available`, `sort_field`, `sort_order`, `groups` (Namen), `remote_id`, `attributes` (Bezeichner => `datatype`, `names`, `descriptions`, `placement`, `required`, `searchable`, `translatable`, `information_collector`, `category`, `settings`: die gesetzten `data_int1` ... `data_text5` des Datentyps) |
| `content.class.change` | `class` / `update` | `match` und die Schlüssel von create außer `identifier` |
| `content.class.remove` | `class` / `delete` | `match` |
| `content.section.change` | `section` / `ensure` | `match`, `identifier`, `name`, `navigation_part` |
| `content.section.remove` | `section` / `delete` | `match` |
| `content.state.change` | `object_state_group` / `ensure` | `match`, `identifier`, `names`, `states` (Bezeichner => Namen), `default_language` |
| `content.state.remove` | `object_state_group` / `delete` | `match` |
| `content.urlalias.change` (hinzugefügt) | `url_alias` / `ensure` | `path`, `node` |
| `content.urlalias.change` (entfernt) | `url_alias` / `delete` | `match` (ein Ort oder `global`), `which` (`all`, `selected`) |
| `access.role.create`, `access.role.copy` | `role` / `create` | `name`, `policies` (`module`, `function`, `limitations`: Bezeichner => Werte) |
| `access.role.change`, `access.policy.add`, `access.policy.remove` | `role` / `update` | `match`, `policies` (alle, wie nach der Änderung) |
| `access.role.remove` | `role` / `delete` | `match` |
| `access.role.assign` | `role` / `assign` | `match` (die Rolle), `user` oder `group`, `limitation` (`subtree` oder `section`, oder ein anderer Bezeichner mit seinem Wert) |
| `access.role.unassign` | `role` / `unassign` | wie assign |
| `access.user.create` (und ein als Inhalt veröffentlichter Benutzer) | `user` / `create` | `remote_id`, `class`, `groups`, `enabled`, `fields` (nur `UserKeepAttributeList[]`); `omitted`: login, email, password und die anderen Attribute |
| `access.user.change`, `access.user.move`, `access.user.activate`, `access.user.lock`, `access.user.unlock` | `user` / `update` | `match` und die Schlüssel von create außer `remote_id` |
| `access.user.remove` | `user` / `delete` | `match` |
| `system.setting.write` | `ini` / `set` (`remove`, wenn die Variable entfernt wurde) | `file`, `scope` (`override`, `siteaccess:<name>`, `extension:<name>`), `block`, `variable`, `value` oder `secret: true` ohne Wert |
| `system.workflow.trigger.change` | `workflow_trigger` / `ensure` | `triggers` (`module`, `function`, `connect`, `workflow`: die ID des Workflows, mit Hinweis) |

Werte in stabiler Form: Einschränkungswerte von Rollen werden zu Klassenbezeichnern (Class, ParentClass),
Sektionsbezeichnern (Section, UserSection), Remote-IDs von Orten (Node, Subtree, UserSubtree) und `<gruppe>/<zustand>`
(Zustandseinschränkungen); andere bleiben, wie der Kernel sie speichert, mit Hinweis. Attributwerte sind ihre
Textform (was `fromString()` zurückliest), Relationen als Listen von `remote:`-IDs; Dateien (Bilder, Binärdateien,
Medien) behalten ihren Pfad mit dem Hinweis, dass ein Datei-Schritt sie kopiert.

## Die Ereignisse zu Schritten zusammenfassen

Eine Aufzeichnung hält jedes Ereignis fest; eine Migration braucht einen Schritt je Sache. Der Generator (und
`show --plan`) fasst sie zusammen:

1. Ereignisse über dieselbe Sache (dasselbe Subjekt) werden ein Schritt, an der Stelle des ersten.
2. Ein Anlegen mit folgenden Änderungen ist ein Anlegen mit den letzten Werten.
3. Eine Sache, die in derselben Aufzeichnung angelegt und entfernt wurde, hinterlässt keinen Schritt.
4. Alles, worauf ein Entfernen folgt, ist das Entfernen.
5. Sonst gilt das letzte Ereignis (es trägt den ganzen Stand).
6. Ereignisse, die nicht der Stand einer Sache sind, bleiben einzeln und in Reihenfolge: Zuweisungen, Verschieben,
   Kopieren, Tauschen, Verbergen und Sichtbarmachen, URL-Aliase.

## Eine Migration aus einer Aufzeichnung erzeugen

`./console exp:migration:generate --from-recording=<id> [name]` macht aus einer Aufzeichnung eine YAML-Migration: die
Ereignisse zu Schritten zusammengefasst (oben), jeder in der Form des Schritttyps, der ihn wiederholt, geprüft, wie
`exp:migration:check` eine Datei prüft, und mit `--apply` als `migrations/YYYYMMDDHHMMSS_<name>.yml` geschrieben (ohne
zeigt der Befehl die Datei und schreibt nichts; eine vorhandene Datei wird nie ersetzt). Ohne Namen benennt die
Bezeichnung der Aufzeichnung sie. In der Administration hat die Seite der Aufzeichnung dasselbe als Knopf
**Migration erzeugen**.

```text
$ ./console exp:migration:generate --from-recording=20261101-124707-de8bac news_section
Exponential migrations: generate from a recording, dry run (--apply writes the file)
  20261101-124707-de8bac stopped   session     8 event(s)  2026-11-01 12:47  user #14  "news section"
    1  section/ensure section news
    2  content/create object remote:news-folder
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
    4  role/assign role Editor
    5  user/create user remote:a8a1fb4f3c2c41d6b0a9c5b37c3e6a51
    6  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618
    7  location/swap location remote:89e19c337bfb26c11d3d7a34a71a6618
Left out on purpose: first_name, last_name, signature, image, login, email, password
Not generated, to be done by hand:
  - location/swap location remote:89e19c337bfb26c11d3d7a34a71a6618: a swap of two locations has no step: swap remote:89e19c337bfb26c11d3d7a34a71a6618 and remote:2c5e7b3f0d1e4a6b8c9d0e1f2a3b4c5d by hand
File migrations/20261101125500_news_section.yml
```

Die Datei, die er schreibt:

```yaml
# description: Recording 20261101-124707-de8bac: news section
#
# Generated from the recording 20261101-124707-de8bac (8 events).
# Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run
#
# Not generated, to be done by hand:
# - location/swap location remote:89e19c337bfb26c11d3d7a34a71a6618: a swap of two locations has no step: swap remote:89e19c337bfb26c11d3d7a34a71a6618 and remote:2c5e7b3f0d1e4a6b8c9d0e1f2a3b4c5d by hand
steps:
    - type: section
      mode: ensure
      identifier: news
      name: News
      navigation_part: ezcontentnavigationpart
    - type: content
      mode: create
      class: folder
      parent: "remote:f3e90596361e31d496d4026eb624c983"
      remote_id: news-folder
      section: news
      always_available: true
      language: eng-GB
      fields: { name: News, short_description: "<p>What is new</p>" }
      translations: { ger-DE: { name: Nachrichten } }
      comment: "translations added: ger-DE"
    - type: location
      mode: update
      node: "remote:89e19c337bfb26c11d3d7a34a71a6618"
      sort_field: published
      sort_order: desc
    - type: role
      mode: assign
      role: Editor
      groups: [ "remote:3c160cca19fb135f83bd02d911f04db2" ]
      subtree: "remote:89e19c337bfb26c11d3d7a34a71a6618"
    - type: user
      mode: create
      secrets:
          login: user_a8a1fb4f3c2c_login
          email: user_a8a1fb4f3c2c_email
          password: user_a8a1fb4f3c2c_password
      remote_id: a8a1fb4f3c2c41d6b0a9c5b37c3e6a51
      groups: [ "remote:3c160cca19fb135f83bd02d911f04db2" ]
      enabled: true
      comment: "personal data left out: a generated migration names the account by secret references; set the secrets user_a8a1fb4f3c2c_login, user_a8a1fb4f3c2c_email, user_a8a1fb4f3c2c_password (Setup > Secrets, or env:<variable>) before the run; left out: first_name, last_name, signature, image, login, email, password"
    - type: location
      mode: hide
      node: "remote:89e19c337bfb26c11d3d7a34a71a6618"
```

Lesen Sie sie, machen Sie die Änderungen, die als „to be done by hand“ genannt sind (oder ergänzen Sie die Schritte
selbst), führen Sie `./console exp:migration:check` und `./console exp:migration:dry-run` aus und committen Sie sie wie
jede Migration. Ist eine Erweiterung, die einen Schritttyp mitbringt, nicht aktiv, nennt der Generator die fehlenden
Schritttypen und schreibt nichts.

### Vom aufgezeichneten Schritt zum Migrationsschritt

| Aufgezeichnet | Migrationsschritt |
|---|---|
| `content` / `create`, `update` | `content` / `create` oder `update` (`object:` die Remote-ID); die Felder der Ausgangssprache als `fields`, die anderen Sprachen als `translations`; weitere Orte als Schritte `location` / `create` (mit eigenen Remote-IDs) |
| `content` / `delete` | `content` / `delete` |
| `content` / `copy` | `location` / `copy` (das Objekt des Knotens; die Kopie bekommt eigene Remote-IDs) |
| `location` / `create`, `move`, `hide`, `delete` | `location` mit demselben Modus (`node:` der Ort) |
| `location` / `reveal` | `location` / `unhide` |
| `location` / `update` | `location` / `update` mit `sort_field`, `sort_order`, `main`; für `child_priorities` je Kind ein `location` / `update` mit `priority` |
| `trash` / `move`, `restore` | `trash` mit demselben Modus |
| `class` / `create` | `content_class` / `create`, die Attribute als Liste (`type` ist der Datentyp) |
| `class` / `update` | `content_class` / `ensure` mit der ganzen Definition (fügt Attribute hinzu und ändert sie, entfernt keine) |
| `role` / `create`, `update` | `role` / `create` oder `ensure` mit den Richtlinien (ensure fügt fehlende hinzu, entfernt keine) |
| `role` / `assign`, `unassign` | `role` mit `users` oder `groups` und `subtree` oder `section` |
| `user` / `create` | `user` / `create` mit `secrets` für Login, E-Mail-Adresse und Passwort ([unten](#personenbezogene-daten-und-geheimnisse)) |
| `user` / `update`, `delete` | `user` / `update` oder `delete` (`user:` die Remote-ID) |
| `section`, `object_state_group` | derselbe Schritttyp, Modus `ensure` (oder `assign`, `delete`) |
| `url_alias` / `ensure` | `url_alias` / `ensure` mit `path` und `node`; das Entfernen aller eigenen Adressen eines Orts als `delete` mit `which: all` |
| `ini` / `set`, `remove` | `settings` mit `set` oder `remove` (`siteaccess` für die Datei eines Siteaccess) |

Nicht erzeugt und stattdessen oben in der Datei genannt: das Vertauschen zweier Orte, Workflow-Auslöser (Workflows
haben noch keinen Bezeichner), Einstellungen einer Erweiterung (Migrationen schreiben nur `settings/override` und
`settings/siteaccess/<name>`), geheime Einstellungen (sie kommen nie in eine Migration), das Entfernen einzelner
URL-Aliase, die der Kernel nicht nennt, und Objektzustände von Inhalten (kein Schritttyp setzt sie; der Kommentar des
Schritts nennt sie). Was ein Schritt nicht tragen kann, steht in seinem `comment`. Ein Text, der `${` enthält, wird als
`$${` geschrieben, damit die Engine ihn nicht für einen Platzhalter hält.

### Personenbezogene Daten und Geheimnisse

Eine Aufzeichnung hält nie das Konto eines Benutzers fest (Login, E-Mail-Adresse, Passwort) und auch nicht die Felder
von Benutzerobjekten, die `[RecordingSettings] UserKeepAttributeList[]` nicht nennt. Ein erzeugter Benutzerschritt
nennt das Konto stattdessen über Geheimnisse, die der Schritt liest, wenn er läuft:

```yaml
    - type: user
      mode: create
      secrets: { login: user_a8a1fb4f3c2c_login, email: user_a8a1fb4f3c2c_email, password: user_a8a1fb4f3c2c_password }
      groups: [ "remote:3c160cca19fb135f83bd02d911f04db2" ]
```

Jeder Name ist ein Geheimnis unter **Setup > Geheimnisse** oder `env:<VARIABLE>` für eine Umgebungsvariable des Laufs.
Legen Sie sie auf jeder Installation vor dem Lauf an; fehlt eines, schlägt der Schritt mit seinem Namen fehl, nie mit
einem Wert. Der Schlüssel `secrets` funktioniert auch in von Hand geschriebenen Benutzerschritten.

## Eine Migration dessen erzeugen, was da ist

Derselbe Generator schreibt eine Migration dessen, was eine Installation jetzt hat, um es auf einer anderen zu
wiederholen:

| Option | Erzeugt |
|---|---|
| `--from-content=<Knoten>[,...]` | die Objekte der Knoten (`2`, `remote:<Remote-ID>`, `path:<URL-Pfad>`); `--depth=<n>` nimmt die Ebenen darunter hinzu, `--max-objects=<n>` hört nach so vielen Objekten auf (Standard 500), Eltern zuerst |
| `--from-class=<Bezeichner>[,...]` | Inhaltsklassen mit ihren Attributen |
| `--from-role=<Name>[,...]` | Rollen mit ihren Richtlinien und Einschränkungen in stabilen Formen |
| `--from-section=<Bezeichner>[,...]` | Sektionen |
| `--from-settings=<Datei>[,...]` | die Einstellungen von `settings/override/<Datei>.append.php`; mit `--siteaccess=<Name>` die dieses Siteaccess |

Die Optionen lassen sich kombinieren; die Schritte verwenden den Modus `ensure`, wo der Schritttyp ihn hat (Inhalte,
Klassen, Rollen, Sektionen), so dass die Migration auch dort läuft, wo ein Teil schon da ist. Es gelten dieselben
Regeln wie für eine Aufzeichnung: stabile Bezeichner, Benutzer über Geheimnisse, geheime Einstellungen ausgelassen.

```text
$ ./console exp:migration:generate --from-class=news_article --from-role="News editor" --from-section=news news_setup --apply
```

## Eine Migration aus einem Vergleich erzeugen

`--from-diff` vergleicht die Installation jetzt mit einem gespeicherten Stand und schreibt eine Migration dessen, was
sich geändert hat. Auf dem gespeicherten Stand ausgeführt, macht die Migration ihn wie diese Installation: Änderungen
in der Administration einer Installation werden zu einer Migration für die anderen, ohne dass dabei eine Aufzeichnung
lief.

| `--from-diff=` | Der gespeicherte Stand |
|---|---|
| eine Sicherungspunkt-ID (`20261102-101500`) | der Sicherungspunkt: sein Datenbank-Dump und seine Kopie von `settings/override`. `./console exp:migration:caption --list` listet sie auf; `--backup-dir` nennt den Ordner der ohne git_manager angelegten Sicherungspunkte |
| eine Datei (`var/backup/before.sqlite`, `before.sql`) | ein Datenbank-Dump desselben Schemas: eine SQLite-Datei (an Ort und Stelle nur gelesen) oder ein SQL-Dump der Datenbank-Engine dieser Installation (SQLite, MySQL oder MariaDB, PostgreSQL), der mit dem Konto der Installation in eine Wegwerf-Datenbank `<Datenbank>_migdiff_<pid>` geladen und danach gelöscht wird. Ein MongoDB-Dump wird nicht gelesen: spielen Sie ihn in eine eigene Datenbank ein und vergleichen Sie mit jener Installation über ihre Adresse |
| eine Adresse (`https://staging.example.com`) | eine andere Installation, einmal über ihre REST-Route `GET /api/migration/v1/snapshot` gelesen (Richtlinie `migration/read`; ein API-Schlüssel mit der Berechtigung `migration:read`, genannt mit `--token=<Name des Geheimnisses>` oder `--token=env:VARIABLE`). Dort wird nichts geschrieben |

`--compare` nennt, was verglichen wird; Standard ist alles außer Inhalten:

| Art | Verglichen über | Schritte |
|---|---|---|
| `classes` | Bezeichner: die Definition und jedes Attribut | `content_class` ensure oder delete |
| `roles` | Name: die Richtlinien mit ihren Einschränkungen (nicht die Zuweisungen: sie nennen Benutzer) | `role` ensure oder delete |
| `sections` | Bezeichner: Name und Navigationsbereich | `section` ensure oder delete |
| `states` | Bezeichner der Objektzustandsgruppe: ihre Zustände und Namen | `object_state_group` ensure oder delete |
| `languages` | Locale: der Name | `language` ensure oder delete |
| `content` | mit `--subtree=<Knoten>`: die Objekte dieses Teilbaums über ihre Remote-ID (geändert, Version, Sektion, Elternteil); `--max-objects` (Standard 500) weist einen größeren Teilbaum ab, statt einen Teil davon zu vergleichen | `content` ensure (Eltern zuerst); delete des obersten entfernten Objekts |
| `settings` | `settings/override`: jede Variable jeder Datei (nur ein Sicherungspunkt oder eine andere Installation hat sie) | `settings` set oder remove |

Was hinzukam oder sich änderte, wird so erzeugt, wie `--from-content`, `--from-class` und die anderen es erzeugen
(Modus `ensure`, stabile Bezeichner, Benutzer über Geheimnisse); was entfernt wurde, wird zu einem delete-Schritt nach
allem, was angelegt wird. Jede Seite wird als Momentaufnahme von Fingerabdrücken gelesen (ein SHA-1 dessen, was ein
Schritt setzen würde), so dass eine andere Installation nie einen Wert herausgibt: Die Werte in der Migration sind
immer die dieser Installation. Geheime Einstellungen fehlen in beiden Momentaufnahmen: Setzen Sie sie von Hand. Ein
verschobenes Objekt steht in den Kopfzeilen und ist von Hand mit einem location-Schritt zu verschieben.

```text
$ ./console exp:migration:generate --from-diff=var/backup/before.sqlite --compare=classes,roles,sections,languages,content --subtree=2 news_changes
Exponential migrations: generate from a difference, dry run (--apply writes the file)
Compared with the dump before.sqlite
  classes    0 added, 1 changed, 0 removed
  roles      0 added, 0 changed, 1 removed
  sections   1 added, 1 changed, 0 removed
  languages  1 added, 0 changed, 0 removed
  content    0 added, 1 changed, 0 removed
    1  language/ensure language nor-NO
    2  section/ensure section diff_news
    3  section/ensure section standard
    4  class/create class folder
    5  content/create object remote:9459d3c29e15006e45197295722c7ade
    6  role/delete role Editor
File migrations/20261102120000_news_changes.yml
...
```

(Die Planzeilen nennen die aufgezeichnete Form `create`; die Schritte verwenden `ensure`.) Ohne Unterschiede wird
nichts geschrieben, und der Befehl endet mit 0 und "no differences: nothing to generate". Mit `--apply` wird die Datei
geschrieben; eine vorhandene Datei wird nie ersetzt.

## Gerüste

`--skeleton <name>` schreibt eine leere Migration zum Ausfüllen, von der Art, die `--type` nennt:

| `--type` | Datei | Inhalt |
|---|---|---|
| `php` (Standard) | `<id>.php` | eine Klasse mit `plan()`, `up()` und `down()` |
| `yml` | `<id>.yml` | der Kopf und `steps: []`, ein Beispiel in Kommentaren |
| `graphql` | `<id>.yml` | dasselbe mit einem Beispiel des Schritts `graphql` |
| `ini` | `<id>.ini` | eine INI-Migration mit Beispielen von Blöcken in Kommentaren |
| `sql` | `<id>.sql` | eine SQL-Migration für jede SQL-Engine, mit `-- down` |
| `sql --engine=<Engine>` | `<id>.mysql.sql`, `<id>.pgsql.sql`, `<id>.sqlite.sql`, `<id>.oracle.sql`, `<id>.mongodb.json` | die Datei einer Engine |

Die Formate: [SQL und Einstellungen](migrationen-sql-und-einstellungen.md), [GraphQL](graphql-rezepte.md).

## Der Editor in der Administration

**Einstellungen > Migrationen > Editor** (`migration/editor`, Richtlinie `migration/edit`) schreibt und ändert
YAML-Migrationen ohne Shell. `migration/editor` beginnt eine neue; `migration/editor/<Migrations-ID>` öffnet eine aus
den durchsuchten Migrationsordnern, solange sie nicht gelaufen ist.

- **Der Text** ist die Migration, wie sie gespeichert wird, mit den Kopfzeilen. Schreiben Sie Schritte von Hand, oder:
- **Einen Schritt hinzufügen**: einen Schritttyp wählen und *Formular anzeigen*. Das Formular entsteht aus dem Schema
  des Schritttyps: jeder Schlüssel mit seinem Typ (eine Knoten- oder Objektangabe, eine ganze Zahl, true/false, eine
  Liste oder Map als YAML), den Werten, die er annimmt (eine Auswahlliste), seiner Vorgabe und den Modi, zu denen er
  gehört. Darunter `if`, `references` (Paare Name=Wert, mit den Werten, die der Schritt liefert), `environments`,
  `engines` und `comment`. *Schritt hinzufügen* setzt ihn ans Ende von `steps` oder `down`.
- **Schritte** listet jeden Schritt mit *Bearbeiten* (lädt ihn ins Formular; *Schritt ändern* setzt ihn an seine
  Stelle zurück), nach oben, nach unten und *Entfernen*. Schleifen und Schritte eines Typs, den die Installation nicht
  hat, haben kein Formular: Ändern Sie sie im Text.
- **Prüfen** führt aus, was `exp:migration:check` ausführt: den Kopf, das Dokument und jeden Schritt durch seinen
  Schritttyp. **Vorschau** zeigt das YAML, wie es gespeichert wird.
- **Speichern** schreibt die Datei über den Kernel: eine neue Migration als `<jetzt>_<name>.yml` in den Migrationsordner
  der Website (der erste Eintrag von `[MigrationSettings] DirectoryList`) oder in das `ExtensionDirectory` einer aktiven
  Erweiterung, nie über eine Datei; eine vorhandene an ihrem Ort. Ein Text, der die Prüfung nicht besteht, wird nicht
  gespeichert, und eine Migration, die gelaufen ist, wird nie wieder gespeichert (die Prüfsummenprüfung würde sie
  ablehnen): Ihre Änderung kommt in eine neue Migration.

Wird ein Schritt im Formular hinzugefügt oder geändert, wird der Text aus der eingelesenen Migration neu geschrieben:
Die Kopfzeilen bleiben, Kommentare zwischen den Schritten nicht. Der Editor schreibt nichts außerhalb der
Migrationsordner.

## Migrationen des Referenz-Bundles umwandeln

`exp:migration:convert <Datei oder Ordner>` liest YAML-Migrationen des Migrations-Bundles der Symfony-basierten
Plattform (das „Referenz-Bundle“: eine Liste von Schritten mit `type`, `mode`, `match`, `attributes` und `references`)
und schreibt für jede Datei eine YAML-Migration von Exponential. Gelesen wird nur das dokumentierte Dateiformat; Code
des Bundles wird nicht verwendet.

```text
$ ./console exp:migration:convert var/import/20240115093000_news.yml
Exponential migrations: convert, dry run (--apply writes the files)
var/import/20240115093000_news.yml -> migrations/20240115093000_news.yml: 1 step(s) converted, 1 left to do by hand
    by hand: step 2 (php): the step type php has no counterpart that converts; write it by hand: {"type":"php","class":"X"}
    | # description: Converted from 20240115093000_news.yml
    | #
    | # Converted by exp:migration:convert. Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run
    | #
    | # Not converted, to be done by hand:
    | # - step 2 (php): the step type php has no counterpart that converts; write it by hand: {"type":"php","class":"X"}
    | steps:
    |     - type: section
    |       mode: create
    |       identifier: news
    |       name: News
Converted 1 step(s), 1 to do by hand
PASS
```

| Schritt des Bundles | Wird zu |
|---|---|
| `content` create, update, delete | `content`: `content_type` → `class`, `parent_location` → `parent`, `attributes` → `fields`, `lang` → `language`, `match` (Inhalts-ID, Remote-ID, Knoten-ID oder Knoten-Remote-ID) → `object` oder `node` |
| `content_type` create, update, delete | `content_class`: Namen und Beschreibungen je Sprache, `content_type_group` → `groups`, Attribute (`info-collector`, `disable-translation`, `field-settings`, `position`); die Attribute eines Updates kommen in `update_attributes` (neue nach `add_attributes` verschieben) |
| `content_type_group` | `content_class_group` |
| `location` create, update, delete | `location` create (eins je Elternknoten), update (Priorität, Sortierung), `move` (ein neuer Elternknoten), `hide`/`unhide`, delete |
| `role` create, update, delete, `assign` | `role` mit `policies` (Einschränkungen als Kennung => Werte), `assign`-Schritte mit `users`/`groups`, eine Einschränkung Subtree oder Section |
| `section`, `user`, `user_group`, `language`, `tag` | die gleichnamigen Schritttypen (`username` → `login`, Vor- und Nachname → `fields`) |
| `sql` | `sql` mit einem Schlüssel je Datenbank |
| `reference` set, `trash` purge | `reference` `set`, `trash` purge |

`reference:<name>` und `[reference:<name>]` werden zu `${ref.<name>}`; die Referenzen, die ein Schritt setzt, behalten
ihre Namen, mit den Wertnamen des Kernel-Schritts (`location_id` → `node_id`, `content_id` → `object_id` ...). Was sich
nicht umwandeln lässt, wird nie geraten: PHP- und Service-Aufrufe, HTTP, Mail, Prozesse, Schleifen, Dateien,
Migrationsdefinitionen, andere Modi und Schlüssel ohne Gegenstück kommen in die Liste oben in der neuen Datei, von Hand
zu erledigen. Jeder umgewandelte Schritt wird von seinem Schritttyp geprüft; einer, der nicht besteht, steht mit dem
Grund ebenfalls in der Liste.

Die neue Datei behält Datum und Uhrzeit vom Anfang des alten Namens, die Reihenfolge bleibt also; ein Name ohne sie
bekommt die Zeit von jetzt. `--source=extension:<name>` schreibt in den Migrationsordner einer Erweiterung, `--author`
in den Kopf. Ohne `--apply` wird nichts geschrieben, und eine vorhandene Datei wird nie ersetzt.

## Verwandte Seiten

- [Änderungen in der Administration aufzeichnen](../hilfe/migrationen/aufzeichnung.md)
- [Migrationen erzeugen](../hilfe/migrationen/migrationen-erzeugen.md) und [der Migrationseditor](../hilfe/migrationen/der-migrationseditor.md), Schritt für Schritt mit Bildschirmfotos
- [Rezept: eine Sitzung in der Administration aufzeichnen](../beispiele/kochbuch/migrationen-sitzung-aufzeichnen.md)
- [Das Migrationsdateiformat: aufgezeichnete Ereignisse](../spezifikationen/6.0/migrationsdateiformat.md#aufgezeichnete-ereignisse-exp-migration-recording-event1)
- [Migrationen](migrationen.md) · [Das Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md) · [„Identität bestätigen“](identitaet-bestaetigen.md)
- [`exp:migration:convert`](../beispiele/befehle/expmigrationconvert.md) · [Die Ansicht migration/editor](../beispiele/modulansichten/migration/editor.md)
