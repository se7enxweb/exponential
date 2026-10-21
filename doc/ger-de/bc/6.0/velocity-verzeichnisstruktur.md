# Die Verzeichnisstruktur von Velocity (im Stil von Debians Apache)

Lesen Sie diese Seite, wenn Sie Exponential Velocity mit der Qbix-Engine betreiben und wissen möchten, wo seine
Konfiguration liegt, wie Sie eine Site, ein Konfigurations-Snippet oder ein Modul aktivieren oder deaktivieren und was
mit einer vorhandenen Installation geschieht, wenn sie in die neue Struktur überführt wird. Die Struktur entspricht
Debians `/etc/apache2`, sodass ein Administrator, der Apache kennt, alles an der erwarteten Stelle findet.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Konfiguration von Velocity liegt in `/etc/vc` (eine Ebene über dem `/etc/qbix` der Engine), aufgeteilt in `vc.conf`, `ports.conf`, `envvars` und die Verzeichnisse `*-available` / `*-enabled`. |
| Wer betroffen ist | Jede Installation, die Velocity mit der Qbix-Engine betreibt. FrankenPHP und die reinen PHP-Engines sind es nicht (siehe „Andere Engines“). |
| Wie Sie es prüfen | `./console exp:velocity layout` zeigt die aufgelösten Pfade und den Stand der Migration. |
| Wie Sie es beheben | Von Hand ist nichts zu tun: Die Migration läuft bei `start` und `restart`, ist idempotent und lässt die alten Pfade weiter funktionieren. |

## Der Verzeichnisbaum

```
/etc/vc/  Velocity's overlay on the engine's base tree /etc/qbix (loaded after it, so it wins)
          (registered by the engine's vc distribution: Q_WebServer_Distribution_Vc)
├── vc.conf                   base settings shared by every site       (apache2.conf)
├── ports.conf                listen ports                             (ports.conf)
├── envvars                   environment for the server process      (envvars)
├── conf-available/*.conf     shared snippets                          (conf-available)
├── conf-enabled/*.conf       -> ../conf-available/*.conf  symlinks    (conf-enabled)
├── mods-available/*.conf     engine modules: http2, cache, compat,    (mods-available)
│                             precompress, log, dashboard, brand, ...
├── mods-enabled/*.conf       -> ../mods-available/*.conf  symlinks    (mods-enabled)
├── sites-available/*.conf    one per installation (host)              (sites-available)
├── sites-enabled/*.conf      -> ../sites-available/*.conf symlinks    (sites-enabled)
└── designs/                  the server's own page designs

/var/lib/vc/sites/<site>.json   metadata / extended info per installation
/var/log/vc/  /var/cache/vc/  /run/vc/   FHS counterparts (optional; see "Other assets")
```

Die Dateien enthalten JSON-Objekte (das Konfigurationsformat der Engine) unter den Dateinamen von Apache. `envvars`
enthält Zeilen der Form `export NAME=value`, wie bei Apache.

Der Server liest `envvars` bei jedem Start (zuerst den Basisbaum, wobei `/etc/vc` Vorrang vor `/etc/qbix` hat) und
exportiert, was darin gesetzt wird, in seine eigene Umgebung, bevor er forkt. So sieht jeder Worker die Werte mit
`getenv()`, gleich welche Shell `exp:velocity restart` oder `deploy` ausführt. Hier gehören Einstellungen hin, die eine
Exponential-Installation aus der Umgebung liest, etwa `EXP_DEMO_MODE=1` auf einer öffentlichen Demo
([Demomodus](demomodus.md)). Die Datei wird gelesen, nie ausgeführt: Werte, die eine Shell
brauchen (`$X`, Backticks), werden nicht exportiert, und `PATH`, `LD_*`, `DYLD_*`, `MALLOC_*`, `HTTP_*`, `PHPRC`,
`PHP_INI_SCAN_DIR`, `GLIBC_TUNABLES`, `HOME`, `USER` und die anderen Namen in der `docs/layout.md` der Engine
("envvars: the server's environment") werden abgelehnt, mit einer Zeile auf der Standardfehlerausgabe beim Start.
`exp:velocity start` übergibt dieselben gefilterten Werte auch selbst, für ältere Engines
(`expVelocityConfigLayout::exportable()`, das dieselbe Liste in `ENV_DENIED` und `ENV_DENIED_PREFIXES` führt).

## Aktivieren und deaktivieren (die a2ensite-Familie)

```bash
./console exp:velocity layout                      # resolved paths and migration state
./console exp:velocity site enable <name>          # also: site disable, conf enable|disable, mod enable|disable
./console exp:velocity layout migrate              # run the migration explicitly
```

Das Aktivieren legt den relativen symbolischen Link im Verzeichnis `*-enabled` an. Das Deaktivieren entfernt nur den
Link; die Datei in `*-available` bleibt erhalten.

### Schreibweisen der Optionen

`exp:velocity` akzeptiert dieselben GNU- und BSD-Schreibweisen wie die eigenen Werkzeuge der Engine:

- `--keep-global=A,B`, `--keep-global A,B`, `-keep-global=A,B` und `-keep-global A,B` bedeuten dasselbe.
- `-json` ist `--json`; `--no-json` schaltet es wieder aus.
- Alles nach `--` wird unverändert weitergegeben: `exp:velocity ctl server:status -- --json` übergibt `--json` an die
  Konsole der Engine, statt es hier auszuwerten.
- Die einbuchstabigen Optionen von Exponential (`-s admin`, `-v`, `-d`) und echte Optionen namens `no-…`
  (`--no-colors`) sind unverändert.

## Ladereihenfolge und Vorrang

Wie bei den Includes von `apache2.conf` gewinnt das Spätere (tiefes Zusammenführen):

1. die Standardwerte der Engine, dann die `config/server.json` der Anwendung
2. `vc.conf`
3. `ports.conf`
4. `mods-enabled/*` (in der Reihenfolge der Namen)
5. `conf-enabled/*` (in der Reihenfolge der Namen)
6. die Site-Datei, `--config=/etc/vc/sites-enabled/<site>.conf`
7. Optionen auf der Kommandozeile (`--port`, `--workers`, ...)

Die Engine liest einen Baum nur, wenn sie dazu angewiesen wird: `--conf-dir=DIR|auto`, `QBIX_CONF_DIR` oder
`VC_CONF_DIR`, oder eine `--config`-Datei in einem Verzeichnis `sites-*`. Sie übernimmt nie einen Baum, nur weil er
existiert; so bleiben ein fremder Server oder die Testsuite vom `/etc/vc` einer Maschine unberührt.

## Wer was schreibt

- `velocity.ini` (die Einstellungen von Exponential) bleibt die Quelle für das, was `exp:velocity` erzeugt:
  `sites-available/<site>.conf` und die Moduldateien, die es verwaltet. Erzeugte Dateien tragen
  `"_generated": "exp:velocity"`. Wie bei einer conffile von dpkg wird eine Datei, deren Markierung ein Administrator
  entfernt hat, nie wieder überschrieben.
- Alles andere im Baum (zusätzliche Snippets in `conf-available`, Ergänzungen in `envvars`) gehört dem Administrator
  und wird nur gelesen.

## Migration einer vorhandenen Installation

Die Migration läuft bei `start` und `restart` (und bei `layout migrate`). Sie ist idempotent und zerstört nichts:

1. **Das Verzeichnis auflösen.** `[LayoutSettings] ConfDir=auto` bedeutet `/etc/vc`, wenn es existiert oder angelegt
   werden kann; sonst `/etc/qbix`, wenn es existiert; sonst `<root>/var/vc/qbix/etc` (für einen Benutzer ohne
   Root-Rechte).
2. **Den Baum anlegen**, wenn er fehlt, die erzeugten Dateien schreiben und die aktivierenden Links anlegen.
3. **Prüfen**, dass der zusammengeführte Baum Schlüssel für Schlüssel der einzelnen erzeugten Konfiguration
   entspricht, die er ersetzt, bevor er verwendet wird. Ist das nicht der Fall, verwendet Velocity weiter die alte
   einzelne Datei und protokolliert den Grund.
4. **Den alten Pfad funktionsfähig halten:** `var/tmp/velocity-server.json` wird weiterhin geschrieben.
5. **Festhalten** in `/var/lib/vc/sites/<site>.json` (Wurzelverzeichnis, Engine-Version, Zeitpunkt der Migration,
   frühere Pfade).
6. **Protokollieren** jeder Aktion im Velocity-Log.

### Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/velocity.ini` | `LayoutSettings` | `ConfDir` | `auto` | Installation |

## Weitere Dateien

Jede andere Datei, die Velocity auf der Festplatte hält (PID, Logs, Antwort-Cache, vorkomprimierte Dateien,
Prewarm-Cache des Kompatibilitätsmodus, Zertifikate, Dashboard-Token, Warm-up-Skript, Engine-Archiv), wird an einer
Stelle in `expVelocity` aufgelöst und von `exp:velocity layout` aufgeführt. Ihre Standardwerte sind unverändert. Die
oben genannten FHS-Orte lassen sich über Einstellungen wählen, werden aber nicht erzwungen, denn das Verschieben
laufender Caches und Logs ist eine eigene Entscheidung.

## Andere Engines

Der Baum gehört zur Qbix-Engine.

- Mit `[ServerSettings] Engine=frankenphp` ist die gesamte Konfiguration ein einziges erzeugtes Caddyfile
  (`var/vc/frankenphp/run/Caddyfile`; eigene Direktiven fügen Sie über `[FrankenPHPSettings] SiteInclude` hinzu). Die
  Binärdatei liegt in `var/vc/frankenphp/bin/`, der Zustand von Caddy in `var/vc/frankenphp/caddy/`.
- Mit `Engine=php` gibt es keine Konfigurationsdatei, nur `bin/php/velocity-router.php`.

Bei beiden führt `layout` diese Dateien statt des Baums auf, und `layout migrate` sowie `site|conf|mod` verweigern
den Dienst. Siehe [Velocity-Engines](velocity-engines.md).

## Verwandte Seiten

- [Velocity-Engines](velocity-engines.md)
- [Hinweise zum Upgrade der Velocity-Engine](velocity-engine-upgrade-hinweise.md)
- [Velocity-Pakete, Docker-Images und Binärdateien](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)
- [Velocity-Chronik: September 2026, 24. September](../../geschichte/velocity/2026-09d.md)
