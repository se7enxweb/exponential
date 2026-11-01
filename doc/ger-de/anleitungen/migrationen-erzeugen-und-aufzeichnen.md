# Migrationen: Generatoren und das Aufzeichnen von Änderungen

Eine Migration von Hand zu schreiben ist der genaue Weg; sie aufzuzeichnen der schnelle. Während eine **Aufzeichnung**
läuft, wird jede Änderung, die Sie in der Administration, in der Website-Seitenleiste oder mit einem Befehl machen,
als Schritt einer YAML-Migration festgehalten: Inhalt veröffentlicht, eine Klasse gespeichert, eine Rolle zugewiesen,
eine Einstellung geschrieben. Danach sehen Sie sich an, was sie erfasst hat, erzeugen daraus eine Migration und führen
diese auf Staging und Produktion aus.

Diese Seite erklärt die Aufzeichnung, die in Exponential 6.0.15 enthalten ist, und den Generator, der aus einer
Aufzeichnung eine Migrationsdatei macht; er kommt mit den YAML-Schritttypen (siehe
[Eine Migration erzeugen](#eine-migration-aus-einer-aufzeichnung-erzeugen)). Die anderen Generatoren (aus Inhalten,
aus einem Vergleich, der Konverter) folgen später. Die Ausgaben unten entstanden am 1. November 2026 auf SQLite.

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
./console exp:migration:generate --from-recording=<id> news_section   # die Migration (braucht die YAML-Schritttypen)
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
beiden.

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
Aufzeichnung und zeigt, was sie erfasst hat, die Schritte, die eine Migration daraus hätte, und einen Knopf zum
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

Die Tabelle ist der Vertrag zwischen der Aufzeichnung und den YAML-Schritttypen des Arbeitspakets 10 (die Schritte
v1). Schritttypen, die es in dieser Version noch nicht gibt, sind so genannt, wie dieses Arbeitspaket sie nennen wird.

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

`./console exp:migration:generate --from-recording=<id> [name]` macht aus einer Aufzeichnung eine YAML-Migration. Dafür
braucht es die YAML-Schritttypen, die die Aufzeichnung verwendet (content, location, class, role, user, section ...),
die mit den YAML-Schritten v1 kommen. Bis die Installation sie hat, listet der Befehl die Schritte und was fehlt,
schreibt nichts und endet mit Exit 1:

```text
$ ./console exp:migration:generate --from-recording=20261101-124707-de8bac news_section
Exponential migrations: generate from a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
    1  section/ensure section news
    2  content/create object remote:news-folder
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
    4  role/assign role Editor
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618
Step types missing: content, location, role, section
FAIL: needs the YAML step types content, location, role, section (the YAML steps v1 of work package 10): nothing is written until this installation has them
FAIL (exit 1)
```

Das Erzeugen aus Inhalten, aus einem Vergleich und der Konverter aus dem YAML des Referenz-Bundles folgen in einer
späteren Version.

## Verwandte Seiten

- [Änderungen in der Administration aufzeichnen](../hilfe/migrationen/aufzeichnung.md)
- [Rezept: eine Sitzung in der Administration aufzeichnen](../beispiele/kochbuch/migrationen-sitzung-aufzeichnen.md)
- [Das Migrationsdateiformat: aufgezeichnete Ereignisse](../spezifikationen/6.0/migrationsdateiformat.md#aufgezeichnete-ereignisse-exp-migration-recording-event1)
- [Migrationen](migrationen.md) · [Das Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md) · [„Identität bestätigen“](identitaet-bestaetigen.md)
