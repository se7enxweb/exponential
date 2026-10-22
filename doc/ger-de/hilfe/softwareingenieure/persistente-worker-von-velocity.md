---
title: Persistente Worker von Velocity
audience: software-engineer, admin
category: software-engineering
order: 330
summary: Wie Exponential Velocity PHP ausführt - der Elternprozess, das Vorwärmen, die Zygote und der Pool persistenter Worker, wie der Pool bemessen wird und wann ein Worker ersetzt wird, was zwischen Anfragen zurückgesetzt wird und was nicht, unter welchem Benutzer Worker laufen und wie Sie den Speicher nach PSS beurteilen - für Ingenieure, die ihn bemessen, betreiben oder Fehler darin suchen.
keywords: Velocity, persistente Worker, Worker-Pool, Elternprozess, Fork, Zygote, Vorwärmen, Zurücksetzen, maxRequests, workerMemoryCeiling, requestTimeout, forkPerRequest, PSS, RSS, Speicher, Kompatibilitätsschicht, Softwareingenieur, persistent workers, worker pool, parent, warm-up, reset, memory, compatibility layer
type: help
---
# Persistente Worker von Velocity

Diese Seite richtet sich an Ingenieure, die Exponential unter Velocity bemessen, betreiben oder Fehler darin suchen. Sie
fasst [die Spezifikation des Worker-Pools](../../spezifikationen/6.0/velocity-worker-pool.md) zusammen, die für
Exponential Velocity 0.0.4.42 und später gilt und jede Einstellung mit ihrem Standard enthält.

## Inhalt

1. [Das Modell](#das-modell)
2. [Den Pool bemessen](#den-pool-bemessen)
3. [Wann ein Worker ersetzt wird](#wann-ein-worker-ersetzt-wird)
4. [Was zwischen Anfragen zurückgesetzt wird](#was-zwischen-anfragen-zurückgesetzt-wird)
5. [Die Zygote und das Vorwärmen](#die-zygote-und-das-vorwärmen)
6. [Der Benutzer, unter dem Worker laufen](#der-benutzer-unter-dem-worker-laufen)
7. [Betrieb](#betrieb)

## Das Modell

- Ein Elternprozess lädt die Anwendung einmal, wärmt sie optional vor und forkt dann Worker. Jeder Worker beantwortet eine
  Anfrage nach der anderen und wird nach jeder in den Zustand zurückversetzt, den er beim Fork hatte.
- Der Elternprozess nimmt jede Verbindung an und liest jede Anfrage. Statische Dateien, Treffer im Antwort-Cache und die
  eigenen Seiten des Servers beantwortet er selbst; nur eine Anfrage, die PHP ausführt, geht an einen Worker.
- Sind alle Worker beschäftigt, wartet die Anfrage im Elternprozess; jenseits von `maxConnections` antwortet der Server
  mit `503`.
- Der Tod eines Workers kostet nie eine Anfrage, die bedient werden könnte: Ein `GET`, `HEAD` oder `OPTIONS`, dessen
  Beantwortung er noch nicht begonnen hatte, läuft noch einmal auf einem anderen Worker; ein `POST` wird nie zweimal
  ausgeführt und erhält ein `502`.

| Modus | Verhalten |
|---|---|
| Persistent (Standard) | jeder Worker bedient viele Anfragen |
| `forkPerRequest` | eine Anfrage je Worker, dann ein frischer Fork: die Isolation eines neuen Prozesses um den Preis eines Forks je Anfrage |

## Den Pool bemessen

`--workers=N` oder `[ServerSettings] Workers` in `settings/velocity.ini`. Ohne das bemisst der Server den Pool nach
Speicher und Kernen (höchstens 8 je Kern und 64 insgesamt, nie weniger als 4). Statische Pools forken jeden Worker beim
Start; ein dynamischer Pool (`SpareWorkers` über 0) forkt bei Bedarf und beendet untätige Worker über der Reservezahl.

**Beurteilen Sie Speicher nach PSS, nie nach RSS.** RSS zählt jede geteilte Copy-on-Write-Seite einmal je Worker und
überschätzt einen Pool um ein Vielfaches. Am 26.09.2026 gemessen kostete ein vollständiger Exponential-Worker etwa
11 MB PSS, mit etwa 47 MB geteiltem Code, der einmal vor dem Fork geladen wurde.

## Wann ein Worker ersetzt wird

| Auslöser | Einstellung (Standard) |
|---|---|
| er hat genug Anfragen bedient | `maxRequests` (1000) |
| sein Heap ist zu groß | `workerMemoryCeiling` (256 MB oder 3/4 von `memory_limit`, falls niedriger) |
| eine Anfrage lief zu lange | `requestTimeout` (30 s); der Client erhält `504`, die Anfrage läuft nicht noch einmal |
| Ausgabepuffer wurden unausgeglichen hinterlassen | immer |
| die Anwendung hat es verlangt | `Q_WebServer_Pool::retireAfterResponse( $reason )` |

Der Grund wird protokolliert; ein Worker, der immer wieder ersetzt wird, deutet also auf ein Leck hin.

## Was zwischen Anfragen zurückgesetzt wird

| Zustand | Wie |
|---|---|
| Statische Klasseneigenschaften | Momentaufnahme beim Start, nach jeder Anfrage wiederhergestellt |
| `$_GET`, `$_POST`, `$_COOKIE`, `$_FILES`, `$_SERVER`, `$_REQUEST` | aus der Anfrage neu befüllt |
| `$_SESSION` | geschrieben und geleert |
| Eigene Globals | Momentaufnahme und Wiederherstellung, außer den Namen in `keepGlobals` |
| Shutdown-Funktionen, Fehler-Handler, Autoloader, `ini_set`, `putenv` | je Anfrage verfolgt und wiederhergestellt |
| Antwort-Header | geleert |

**Absichtlich nicht zurückgesetzt:** OPcache, geladene Klassen, APCu, internierte Zeichenketten, Autoloader-Zuordnungen.
**Aufgabe der Anwendung:** Zustand von Datenbanktransaktionen, Datei-Handles, Stream-Kontexte, Signal-Handler,
cURL-Handles und Register in Globals, die einmal mit `include_once` befüllt werden. Siehe
[Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md).

## Die Zygote und das Vorwärmen

- **Die Zygote** ist ein zusätzlicher Prozess, der am Ende des Starts geforkt wird, vor der ersten Verbindung. Spätere
  Worker werden von ihr geforkt und erben so keine Verbindung eines Besuchers.
- **Das Vorwärm-Skript** läuft einmal im Elternprozess vor dem Forken (Exponential liefert `bin/php/velocity-warmup.php`
  mit); jeder Worker erbt den vorgewärmten Speicher per Copy-on-Write. Was das Vorwärmen hinterlässt, wird zur Basis
  jedes Workers; es muss also den Anfragezustand zurücksetzen. Prüfen Sie ein Vorwärmen am Verhalten: Mehrere
  verschiedene URLs müssen verschiedene Inhalte liefern.

## Der Benutzer, unter dem Worker laufen

Ein als root gestarteter Server behält root nur im Serverprozess. Die Zygote und jeder Worker geben es direkt nach dem
Fork ab, bevor Anwendungscode läuft; der Benutzer kommt von der Kommandozeile, aus der Konfiguration, der Umgebung
(`VC_RUN_USER`, `QBIX_RUN_USER`) oder vom Eigentümer des Document Root. Worker als root werden abgelehnt, sofern das nicht
ausdrücklich erlaubt ist.

## Betrieb

```bash
php bin/php/console exp:velocity status
php bin/php/console exp:velocity graceful        # reload workers one at a time, the port keeps answering
php bin/php/console exp:velocity restart
php bin/php/console exp:velocity deploy --allow-root-user
```

Prüfen Sie nach einem Neustart, dass ein neuer Elternprozess läuft und der Port antwortet. Die Ablage der Konfiguration
auf der Festplatte folgt der Organisation von `/etc/apache2` unter Debian
([Ablage von Velocity auf der Festplatte](../../bc/6.0/velocity-verzeichnisstruktur.md)).

## Verwandte Seiten

- [Anwendungsserver](anwendungsserver.md)
- [Upgrade-Hinweise zur Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md)
- [Opcode-Cache und Profil von Velocity](../../funktionen/6.0/velocity-opcode-cache-und-profil.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
