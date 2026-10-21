# Unterstützung für das Engine-Archiv (phar)

Lesen Sie diese Seite, wenn Sie Exponential Velocity betreiben, wenn Sie durch Kopieren von Dateien bereitstellen
oder wenn ein Stack-Trace einen Pfad `phar://` nennt. Exponential 6.0 kann seine eigene Engine (`kernel/`, `lib/` und
`autoload/`) aus einem Phar-Archiv laden statt aus tausend einzelnen Dateien auf der Festplatte. Das ist standardmäßig
ausgeschaltet; ausgeschaltet ist die Installation genau das, was sie war.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Befehl `exp:phar` (`build`, `check`, `info`, `clean`), neue Einstellung `velocity.ini [ServerSettings] EnginePhar` (Standard `disabled`), Umgebungsvariable `EXP_ENGINE_PHAR`, ein Block *Engine* unter **Setup → Systeminformationen**. `eZINI` löst das Wurzelverzeichnis der Installation über `eZINI::installationRoot()` auf. |
| Wer betroffen ist | Benutzer von Velocity mit `EnginePhar=enabled`: Eine Änderung an `kernel/`, `lib/` oder `autoload/` ist erst live, wenn das Archiv neu gebaut ist (ein Neustart von Velocity baut es bei Bedarf neu). Code, der das Wurzelverzeichnis der Installation aus `__DIR__` berechnet, bricht, wenn er aus dem Archiv läuft. Velocity (qbix-webserver) älter als 0.0.4.8 sendet aus dem Archiv keine Header oder Cookies. |
| Wie Sie es prüfen | `php bin/php/console exp:phar info` und `php bin/php/console exp:phar check` (Exit 1, wenn das Archiv veraltet ist) |
| Wie Sie es beheben | Bauen Sie mit `php bin/php/console exp:phar build` neu oder starten Sie Velocity neu; leeren Sie die Caches, wenn Sie die Engine wechseln; verwenden Sie `EXP_ROOT_DIR` oder `eZINI::installationRoot()` statt Pfaden relativ zu einer Kernel-Datei. |

## Schnellstart

```bash
php bin/php/console exp:phar build      # write dist/engine.phar
php bin/php/console exp:phar info       # what exists, and whether it is in use
php bin/php/console exp:phar clean      # remove it
```

Stellen Sie dann die Laufzeit darauf um, entweder in `settings/velocity.ini` (`[ServerSettings] EnginePhar=enabled`,
siehe [Die Laufzeit darauf umstellen](#die-laufzeit-darauf-umstellen)) oder mit einer Umgebungsvariablen in dem, was
die Website ausliefert:

```bash
EXP_ENGINE_PHAR=/path/to/install/dist/engine.phar
```

Entfernen Sie sie wieder, und die Installation ist genau das, was sie war.

## Was es nicht ist

**Es ist kein Ersatz für die Installation.** Nichts verlässt die Festplatte. Dieselben Dateien bleiben, wo sie sind,
und `design/`, `extension/`, `settings/`, `share/`, `bin/` und `var/` sind unberührt und werden weiterhin von der
Festplatte gelesen. Nur der Autoloader verhält sich anders.

**Es ist keine Funktion für Leistung.** Gemessen macht es keinen Unterschied. Siehe *Messungen* unten. Wird es als
Optimierung vorgeschlagen, sind die Zahlen dort die Antwort.

**Es ist kein Schutz des Quelltexts.** Ein Phar ist ein Archiv. Wer es lesen kann, kann es entpacken.

**Es ist noch keine einzelne ausführbare Datei.** Das Archiv mit einer statischen PHP-Binärdatei zu kombinieren —
`spc micro:combine`, womit auf dem Ziel kein PHP mehr nötig wäre — ist nicht implementiert. Siehe *Nicht
implementiert*.


## Verwendung

### Bauen

```bash
php bin/php/console exp:phar build
```

Das Bauen verlangt `phar.readonly=0`. Das ist eine INI-Einstellung, und die Konsole leitet jeden Befehl an ein frisches
PHP weiter, sodass eine auf der Konsolenzeile eingegebene Option den Befehl nie erreicht. Der Befehl führt sich daher
einmal selbst mit der Einstellung erneut aus, statt zu verlangen, dass jemand das weiß. Es muss nichts übergeben
werden.

Das Archiv wird nach `dist/engine.phar` geschrieben. `dist/` wird von Git ignoriert; nichts darin ist Quelltext.

Optionen:

| Option | Bedeutung |
|---|---|
| `--output=PATH` | das Archiv an einen anderen Ort als `dist/engine.phar` schreiben |
| `--force` | neu bauen, auch wenn das Archiv aktuell ist |
| `--json` | das Ergebnis als JSON ausgeben, für Skripte |

### Nur neu gebaut, wenn sich etwas geändert hat

Neben das Archiv schreibt der Bau `dist/engine.phar.index.json`: jede Datei, die er verpackt hat, mit Größe,
Änderungszeit und SHA-1, das PHP-Release, unter dem sie geprüft wurde, sowie Größe und Zeit des Archivs selbst. Ein Bau
und jedes `exp:velocity start`, `restart` und `graceful` vergleicht das zuerst mit der Festplatte und baut nur neu,
wenn

- eine Datei in `kernel/`, `lib/` oder `autoload/` **hinzugefügt, entfernt oder geändert** wurde (eine Datei, deren
  Zeit sich verschoben hat, wird gehasht, sodass ein Checkout oder ein neu erzeugtes Autoload-Array, das dieselben Bytes
  erneut geschrieben hat, keine Änderung ist; ihre neue Zeit wird verzeichnet, und sie wird nicht erneut gehasht);
- der Index fehlt, zu einem anderen Archiv gehört (das Archiv wurde nach seinem Schreiben ersetzt) oder von einem
  älteren Builder geschrieben wurde;
- die Haupt- oder Nebenversion von PHP nicht die ist, unter der die Dateien geprüft wurden.

```bash
php bin/php/console exp:phar build          # engine.phar is current, not rebuilt   (0.3s)
php bin/php/console exp:phar build --force  # built engine.phar                     (about 28s)
php bin/php/console exp:phar check          # current or not, and which files differ; exit 1 when not
php bin/php/console exp:velocity restart --rebuild-phar --allow-root-user   # rebuild at this restart anyway
```

Ein Neustart sagt, was es war: `started (engine.phar is current, not rebuilt)` oder
`started (engine.phar rebuilt: 1 changed (kernel/classes/expvelocity.php))`. Gemessen auf einer Testinstanz:
3,2 Sekunden für einen Neustart ohne Änderung, 33 Sekunden mit einer geänderten Kernel-Datei.

Das ersetzte eine Regel, die neu baute, wenn irgendeine Datei auf der Festplatte neuer als das Archiv war. Diese Regel
übersah eine entfernte Datei und eine Datei, die mit einer älteren Zeit an ihren Platz kam (`rsync -a`, `cp -p`, ein
entpacktes Release), und baute für einen Checkout neu, der nur dieselben Bytes neu schrieb.

**Eine Datei, die sich nicht parsen lässt, behält das alte Archiv.** Jede PHP-Datei wird geparst, bevor etwas
geschrieben wird; eine, die fehlschlägt, wird genannt, nichts wird geschrieben, und das vorhandene Archiv bleibt. Ein
Neustart verweigert dann, bevor er den Server stoppt, der auf dem alten Archiv weiterläuft:

```
velocity: the engine archive needs rebuilding (1 added (kernel/classes/example.php)) and could
not be rebuilt: 1 file(s) do not parse: kernel/classes/example.php (php -l names the line).
Nothing was written and the archive already there was kept; fix that and restart again, or set
EnginePhar=disabled -- the running server was left as it is
```

Der Versionsstempel (Commit und `-dirty`, siehe *Versionierung*) ist kein Grund für einen Neubau: Er bewegt sich mit
jedem Commit, ob sich eine Datei im Archiv geändert hat oder nicht. **Setup → Systeminformationen** meldet das Archiv
daher als passend, wenn seine Dateien die Dateien auf der Festplatte sind, und nennt die beiden Commits daneben.

### Untersuchen

```bash
php bin/php/console exp:phar info
```

Meldet Pfad, Größe, Bauzeit und Version des Archivs, ob die Laufzeit es gerade verwendet, und zum Vergleich die eigene
Version des Arbeitsbaums.

Dieselben Angaben erscheinen in der Administration unter **Setup → Systeminformationen**, in einem Block namens
*Engine*:

- **Geladen aus** — einzelne Dateien auf der Festplatte oder das Archiv
- **Wurzelverzeichnis der Installation** — der absolute Pfad, gegen den der Kernel auflöst
- **Archiv** — Pfad, Zahl der Dateien, Größe, Bauzeit
- **Archive version** — aus dem Archiv gelesen
- **Matches working tree** — ob das Archiv aus dem gebaut wurde, was jetzt auf der Festplatte liegt
- **Phar-Stream-Wrapper** — registriert oder nicht, und `phar.readonly`
- **Opcode cache** — geladen, eingeschaltet, gecachte Skripte

Diesen Block gibt es, weil er das Erste ist, das sich zu lesen lohnt, wenn eine Bearbeitung einer Kernel-Datei scheinbar
nichts bewirkt oder ein Stack-Trace einen Pfad `phar://` nennt.

### Die Laufzeit darauf umstellen

    [ServerSettings]
    EnginePhar=enabled

in `settings/velocity.ini` oder einem Override davon. Der Wert kann sein:

| | |
|---|---|
| `disabled` | die Dateien auf der Festplatte. Der Standard. |
| `enabled` | `dist/engine.phar`, wo immer `expPhar` es ablegt. |
| *ein Pfad* | dieses Archiv, relativ zum Wurzelverzeichnis der Installation oder absolut. |

Der Dienst exportiert beim Start `EXP_ENGINE_PHAR` in die Umgebung des Servers, und die Worker erben es, wenn sie
forken. Es muss die Umgebung sein und kein Kommandozeilenargument, weil `autoload.php` es liest, bevor es irgendetwas
geparst hat -- und das ist zwangsläufig, bevor ein Argument befragt werden könnte.

Ein Archiv, das nicht vorhanden ist, verhindert den Start des Servers, statt auf die Dateien auf der Festplatte
zurückzufallen. Ein stiller Rückfall ist von Erfolg nicht zu unterscheiden, und der Unterschied zeigt sich erst viel
später.

`exp:velocity status` meldet aus demselben Grund, welche Engine läuft: Von außen sehen ein Archiv, das nicht geladen
werden konnte, und ein gewöhnlicher Lauf gleich aus.

**Leeren Sie die Caches beim Umstellen, in beide Richtungen.** Unter einer Engine gerenderte Seiten werden gecacht und
unter der anderen ausgeliefert.

### Zurück

    EnginePhar=disabled

dann die Caches leeren und neu starten. Die Variable zu entfernen oder das Archiv zu löschen funktioniert ebenfalls.
Einen anderen Zustand gibt es nicht.

### Vor 0.0.4.8 konnte es gar nicht funktionieren

Die Kompatibilitätsschicht im Anwendungsserver schreibt `header()`, `setcookie()` und Verwandte um, während jede Datei
eingebunden wird, weil diese Funktionen unter der CLI-SAPI von sich aus nichts tun. Sie tat das, indem sie das Schema
`file://` umhüllte, und über `phar://` eingebundener Code lief nie durch sie hindurch.

Die Engine lief also aus dem Archiv, und jeder dieser Aufrufe ging ins Leere. Die Website renderte die richtigen
Seiten und sendete fast nichts mit ihnen:

    Content-Type: text/html; charset=UTF-8
    Content-Length: 76800
    Connection: close

Kein `Cache-Control`, daher ließ sich nichts cachen, und jede Anfrage wurde gerendert -- aus 75 ms wurden 1,2 bis
1,7 s. Kein `Set-Cookie`, daher konnte sich niemand anmelden: Die Anmeldung antwortete mit 302 an die richtige Stelle
und setzte keine Sitzung. Nichts wurde protokolliert, weil nichts fehlschlug.

`qbix-webserver` 0.0.4.8 umhüllt auch `phar://`. Ist das Archiv in Gebrauch und verhalten sich Header oder Anmeldung
seltsam, prüfen Sie zuerst diese Version.


## Versionierung

Das Archiv wird mit einer Version gestempelt, die aus dem Release, dem kurzen Commit und `-dirty` besteht, wenn der
Arbeitsbaum nicht zu diesem Commit passt:

```
6.0.15stable-aae2c57d72-dirty
```

Ein Bau aus einem veränderten Baum ist erlaubt — ihn abzulehnen würde das Iterieren mühsam machen —, daher hält die
Markierung das Artefakt davon ab, sich als einen Commit auszugeben, der es nicht ist. Der Stempel ist innerhalb des
Archivs unter `ENGINE_VERSION`, über `exp:phar info` und in der Administration lesbar.


## Wie es Klassen auflöst

Der Bau schreibt eine `MANIFEST.php` in das Archiv: eine Zuordnung, deren Schlüssel die Pfade genau so sind, wie die
Klassenzuordnung des Kernels sie hält. Der Autoloader liest sie einmal, danach ist jede Suche ein einzelnes `isset()`.

Das Archiv pro Klasse zu fragen hieße ein stat pro Klasse, und das sind die Kosten, die das vermeiden soll, statt sie
hinzuzufügen.

**Eine Klasse, die das Archiv nicht enthält, wird weiterhin von der Festplatte geladen.** Ein veraltetes Archiv fällt
für diese Klasse daher auf das alte Verhalten zurück, statt zu scheitern. Das ist Absicht: Eine Kernel-Datei, die nach
dem Bau des Archivs hinzugefügt wurde, darf die Installation nicht kaputt machen.


## Was sich im Kernel ändern musste

Kernel-Code leitet Pfade vom Ort seiner eigenen Datei ab. `__DIR__` innerhalb eines Archivs nennt einen Pfad *im
Archiv*, sodass dieser Code Dinge nicht mehr findet, die nie darin waren.

### `autoload.php`

Veröffentlicht `EXP_ROOT_DIR`, das absolute Wurzelverzeichnis der Installation, weil es die eine Datei ist, die
garantiert auf der Festplatte liegt. Löst `EXP_ENGINE_PHAR` aus der Umgebung auf. Löst Klassen von Kernel und
Bibliotheken aus dem Archiv auf, wenn es eines gibt.

Außerdem hält es den Phar-Stream-Wrapper registriert, wenn die Laufzeit selbst über ihn gelesen wird — ihn in diesem Fall
abzumelden, entlädt das laufende Programm. Siehe [Der Phar-Stream-Wrapper](#der-phar-stream-wrapper) unten und den
Kommentar in der Datei.

### `lib/ezutils/classes/ezini.php`

Fünf Stellen berechneten das Wurzelverzeichnis der Installation als drei Ebenen über dieser Datei. Alle fünf rufen jetzt
`eZINI::installationRoot()` auf, das `EXP_ROOT_DIR` zurückgibt, wenn es definiert ist, und sonst auf die alte
Berechnung zurückfällt.

Zwei davon waren wichtig, und **die wichtigste war nicht die offensichtliche**:

- `eZINI::exists()` — Einstellungen scheinen zu fehlen. Laut.
- `eZINI::findInputFiles()` — **leise und viel schlimmer.** Die Basis-`.ini` fällt aus der Liste der Eingaben heraus,
  während das Override, das gegen das Arbeitsverzeichnis aufgelöst wird, bleibt. Die Konfiguration kommt halb geladen
  zurück: `VarDir` liest sich korrekt, `CacheDir` gibt `false` zurück, und der Fehler zeigt sich an ganz anderer Stelle
  als `Cannot use bool as array` in `global_functions.php`.

Erzeugt eine künftige Änderung am Inhalt des Archivs solche Symptome, ist das das Muster, nach dem Sie suchen sollten.

### Nicht geändert

Vier andere Dateien verwenden dasselbe selbstbezogene Muster — `kernel/setup/expcronjobrunner.php`,
`kernel/setup/expextensionwizard.php`, `kernel/setup/expkerneloverridewizard.php` und `lib/ezdb/classes/expmongodb.php`.
Keine liegt auf dem Pfad einer Anfrage. Wird eine davon je verpackt und aus einem Archiv benutzt, braucht sie dieselbe
Behandlung.


## Der Phar-Stream-Wrapper

Der Bootstrap meldet den Phar-Stream-Wrapper ab, und dieser Schutz ist echt: Der Wrapper lässt jede Funktion, die einen
Pfad nimmt, in ein Archiv hineingreifen, und eine Datei, die zugleich ein gültiges Bild und ein gültiges Phar ist, ist
leicht herzustellen. Diese Anwendung nimmt absichtlich Bild-Uploads an.

Der Angriffsweg über die Deserialisierung von Metadaten aus dem Jahr 2018, den der ursprüngliche Kommentar nannte, ist
unter PHP 8 geschlossen. Der Wrapper zählt aus dem oben genannten Grund weiterhin.

Folgen für diese Funktion:

- Ist `EXP_ENGINE_PHAR` gesetzt, bleibt der Wrapper registriert, weil die Laufzeit über ihn gelesen wird.
- Ist es nicht gesetzt, wird der Wrapper abgemeldet — auch auf der Kommandozeile. `expPhar` stellt ihn daher um seine
  eigene Arbeit am Archiv herum wieder her und hinterlässt ihn genau so, wie es ihn vorgefunden hat. `Phar` erweitert
  `RecursiveDirectoryIterator` und lässt sich ohne den Wrapper nicht einmal erzeugen.


## Messungen: Das Archiv ist nicht schneller

Das Archiv gibt es, weil ein einzelnes gestempeltes Artefakt leichter bereitzustellen ist als tausend Dateien. Man hatte
auch gehofft, dass es schneller ist. Das ist es nicht.

Gemessen auf einer Testinstallation, auf `/site/recipes`, der Startseite und einer Inhaltsseite, vier Worker pro Server,
gegen den Server mit persistenten Workern, mit Anfragen, die reihum an die verglichenen Server gingen:

| Änderung | Ergebnis |
|---|---|
| **Opcode-Cache für den Serverprozess eingeschaltet** | **+18 bis +26 %** |
| Engine aus dem Archiv geladen | kein messbarer Unterschied (fünf Läufe: +2,4 %, −2,3 %, −3,8 %, −0,1 %, +1,8 %; Mittel etwa −0,4 %) |
| Günstigere Prüfungen im Datei-Stream-Wrapper des Servers | +9,7 %, dann +2,9 %, dann +0,5 % — Rauschen |
| Opcode-Cache ohne Prüfung der Zeitstempel | im Rauschen des Caches allein |
| Opcode-Cache mit einem Puffer für interned Strings von 32M | im Rauschen |
| Tracing-JIT | im Rauschen; der schlechteste von fünf in einem Lauf, der beste im nächsten |

**Nur der Opcode-Cache lohnt sich.** Auf der Testmaschine war er ausgeschaltet, weil eine systemweite INI-Datei ihn für
jedes PHP auf der Kommandozeile deaktivierte, und der Server mit persistenten Workern läuft unter diesem Interpreter.
Er wird jetzt pro Prozess in `settings/velocity.ini` unter `[PHPSettings]` angefordert; die systemweite Datei blieb
unberührt.

**Messen Sie reihum, nie einen Server nach dem anderen.** Einen Server und danach den anderen zu messen, meldete
+3,9 %, +7,8 %, −19,8 % und +7,8 % für eine Änderung, die etwa −0,4 % wert ist. Die Seite kostet über eine halbe Sekunde,
und die Maschine tut anderes, daher fällt alles, was während eines Laufs abdriftet, ganz auf die gemessene Seite. Eine
Anfrage nach der anderen reihum an jeden Server zu senden, lässt die Drift beide gleich treffen.

**Die verbleibende Zeit ist die Anwendung, nicht der Server.** Ein triviales Skript über denselben Server antwortet in
etwa 30 ms, gegenüber rund 450 ms für eine Inhaltsseite. Die Kosten sind zur Laufzeit erzeugtes PHP (kompilierte
Templates und INI-Caches, bei jeder Anfrage eingebunden), nicht das Finden von Klassendateien, und das Verpacken berührt
das nicht. Die nächste Stelle, an der man suchen sollte, sind die Datenbank und das Rendern der Templates.

## Nicht implementiert

Bewusst nicht getan:

- **Kombination mit einer statischen PHP-Binärdatei.** Der Teil, der daraus eine Übergabe als einzelne Datei ohne PHP
  auf dem Ziel machen würde.
- **Erzwingen der Übereinstimmung von Engine- und Website-Version.** Das Archiv ist gestempelt, und die Administration
  zeigt, ob es passt, aber nichts verweigert bei einer Abweichung den Start. Legen Sie ein neueres Archiv neben eine
  ältere Installation, und der Fehler sieht wie ein Fehler der Website aus.
- **Konsistenzprüfung des Archivs.** `share/filelist.md5` prüft die Kopien auf der Festplatte, die weiterhin vorhanden
  sind, daher funktioniert es weiter. Den Inhalt des Archivs prüft es nicht.


## Dateien

| Pfad | Aufgabe |
|---|---|
| `kernel/classes/expphar.php` | der Dienst: Bau, Info, Aufräumen, Version, Umgang mit dem Wrapper |
| `bin/php/phar.php` | der Befehl, von der Konsole als `exp:phar` gefunden |
| `autoload.php` | veröffentlicht das Wurzelverzeichnis, löst das Archiv auf, lädt Klassen daraus |
| `lib/ezutils/classes/ezini.php` | fragt nach dem Wurzelverzeichnis, statt es zu berechnen |
| `kernel/setup/info.php` | sammelt den Block Engine |
| `design/admin/templates/setup/info.tpl` | rendert ihn |
| `dist/engine.phar` | das Artefakt (von Git ignoriert) |
| `dist/engine.phar.index.json` | was das Artefakt enthält, um über einen Neubau zu entscheiden (von Git ignoriert) |

## Verwandte Seiten

- [Verhaltensänderungen vom 16. bis 30. September 2026: Engine-Archiv](verhaltensaenderungen-2026-09b.md#engine-archiv-expphar) (atomarer Bau, gebündelte Syntaxprüfung)
- [Velocity, der Server mit persistenten Workern](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Velocity-Engines — Qbix, FrankenPHP, der eingebaute Server von PHP](velocity-engines.md)
- [Hinweise zum Upgrade der Velocity-Engine](velocity-engine-upgrade-hinweise.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
