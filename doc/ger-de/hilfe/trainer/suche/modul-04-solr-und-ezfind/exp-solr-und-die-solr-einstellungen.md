---
title: "Lektion 4.2: exp:solr und die Solr-Einstellungen"
audience: trainer, admin, hosting
category: training
order: 42
summary: Die Teilnehmenden unterscheiden den Kernel-Block [SolrSettings] vom ezfind-Block [SolrBase] in solr.ini, konfigurieren ein laufendes Solr mit Url und prüfen es mit exp:solr status und ping, wie es eine Überwachungsprüfung täte. 30 Minuten.
keywords: suchkurs, exp:solr, solr.ini, SolrSettings, SolrBase, Enabled, Url, BinaryPath, Host, Port, SearchServerURI, ConnectionTimeout, status, ping, überwachung, monitoring, search course
type: help
---
# Lektion 4.2: exp:solr und die Solr-Einstellungen

Zwei Blöcke von `solr.ini` handeln von Solr, und die Teilnehmenden verwechseln sie. `[SolrSettings]` des Kernels teilt
dem Konsolenbefehl `exp:solr` mit, wo Solr ist und ob er es starten darf. `[SolrBase]` der Erweiterung teilt ezfind
mit, wohin Index- und Suchanfragen gehen. Diese Lektion trennt beide und macht `exp:solr` zu einem Teil der Routine der
Teilnehmenden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 4: Solr und ezfind](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Experten |
| Zielgruppe | Website-Administratoren und Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 4.1](ezfind-und-solr-installieren.md): ezfind aktiv und das mitgelieferte Solr läuft |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Unterschied zwischen `solr.ini [SolrSettings]` (Kernel, `exp:solr`) und `solr.ini [SolrBase]` (ezfind)
  erklären;
- `[SolrSettings] Enabled=true` mit `Url` für ein laufendes Solr in `settings/override/solr.ini.append.php`
  konfigurieren;
- `exp:solr status` und `exp:solr ping` ausführen und ihre Antworten lesen;
- erklären, was `start`, `stop` und `restart` mit `BinaryPath` und mit `Url` tun.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Befehle zur Serversteuerung](../../../../../eng-us/features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)
und [Such-Backends](../../../hosting/grundlagen/such-backends.md#der-befehl-expsolr).

1. **Zwei Blöcke** (4 Minuten). Schreiben Sie beide an die Tafel:
   - `[SolrSettings]` (Kernel-Datei `settings/solr.ini`): `Enabled` (bei Auslieferung `false`), `Url` (ein laufendes
     Solr; es gilt dann als anderswo verwaltet), `BinaryPath` (ein lokales Solr-Steuerprogramm, verwendet, wenn `Url`
     leer ist), `Host` und `Port` (`127.0.0.1`, `8983`), `DataDir`, `RunDir` und `LogDir` (`var/vc/solr/run`,
     `var/vc/solr/log`), `StartTimeout` (`60`).
   - `[SolrBase]` (die `solr.ini` der Erweiterung): `SearchServerURI` (`http://localhost:8983/solr/ezp-default`),
     `ConnectionTimeout` (`10`), `ProcessTimeout` (`20`), `ProcessMaxRetries` (`2`), optionale Authentifizierung und
     `Shards[]` für mehrere Cores.
   Beide müssen auf dasselbe Solr zeigen; nur `[SolrBase]` entscheidet, wohin Suchen gehen.
2. **Die Verben** (3 Minuten). `status`: konfiguriert oder nicht, und ob es antwortet. `ping`: lebt es. `start`,
   `stop`, `restart`: führen die eigene Steuerung von Solr für eine lokale Instanz mit `BinaryPath` aus; mit `Url`
   melden sie, dass Solr anderswo verwaltet wird. Solange Solr nicht konfiguriert ist, sagt jedes Verb das, und
   `start` endet mit einem Wert ungleich null, damit eine Überwachungsprüfung „nicht konfiguriert“ nie mit „läuft“
   verwechselt.
3. **Überwachung** (3 Minuten). Der Tipp aus dem Hosting-Leitfaden: Nehmen Sie `exp:solr status` für jede Website mit
   Solr in die Überwachung auf. Der Exit-Status des Befehls unterscheidet die Zustände: `0` erledigt oder läuft, `1`
   konfiguriert, aber fehlgeschlagen oder antwortet nicht, `3` nicht installiert oder nicht eingeschaltet, `4`
   anderswo verwaltet (`start` oder `stop` bei gesetztem `Url`). `--json` gibt das Ergebnis als JSON aus. `ping` fragt
   `/solr/admin/info/system` von Solr unterhalb der Basis-URL ab.

### Häufige Fragen

**Warum sagt exp:solr „nicht konfiguriert“, während ezfind problemlos sucht?** `[SolrSettings] Enabled` steht noch auf
`false`. ezfind braucht es nicht; `exp:solr` schon.

**Kann exp:solr das mitgelieferte Solr starten?** `start` führt ein in `BinaryPath` genanntes Solr-Steuerprogramm aus.
Das mitgelieferte Solr aus Lektion 4.1 wird mit `java -jar start.jar` gestartet; setzen Sie dafür `Url` und lassen Sie
`exp:solr` es prüfen.

**Wohin gehört ein Solr-Passwort?** `[SolrBase]` von ezfind hat `SearchServerAuthentication`,
`SearchServerAuthenticationMethod` (basic) und `SearchServerUserPass`. Halten Sie solche Werte in `settings/override`,
nie in einem Repository und nie in Kursmaterial.

### Stolperfallen

- **Url mit einem Core-Pfad setzen.** Die `Url` des Kernels ist die Basis-URL mit Schema und Port, wie im Beispiel der
  Dokumentation `http://127.0.0.1:8983`; der Core gehört in `SearchServerURI` von ezfind. Proben Sie `ping` vor dem
  Kurs gegen das Solr Ihrer Übungsumgebung.
- **settings/solr.ini bearbeiten.** Overrides gehören in `settings/override/solr.ini.append.php`;
  `exp:ini ... global` schreibt dorthin.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 4.1, mit dem mitgelieferten Solr, das noch in seinem eigenen
Terminal läuft. Fügen Sie `--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Führen Sie `php bin/php/console exp:solr status` aus und lesen Sie die Zeile **reason**.
2. Geben Sie jede Datei aus, die den Kernel-Schalter und die Adresse von ezfind setzt:

   ```bash
   php bin/php/console exp:ini where solr.ini/SolrSettings/Enabled
   php bin/php/console exp:ini where solr.ini/SolrBase/SearchServerURI
   ```

3. Konfigurieren Sie den Kernel-Block für das laufende Solr:

   ```bash
   php bin/php/console exp:ini set solr.ini/SolrSettings/Enabled true global
   php bin/php/console exp:ini set solr.ini/SolrSettings/Url http://127.0.0.1:8983 global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Führen Sie `exp:solr status` und `exp:solr ping` erneut aus.
5. Führen Sie `php bin/php/console exp:solr stop` aus. Lesen Sie, was er sagt, und prüfen Sie im anderen Terminal,
   dass Solr noch läuft.
6. Beenden Sie Solr in seinem Terminal mit Strg+C. Führen Sie `exp:solr status` und `ping` aus und notieren Sie den
   Exit-Status (`echo $?`).
7. Starten Sie Solr wieder wie in Lektion 4.1 und führen Sie noch einmal `exp:solr ping` aus.
8. Schreiben Sie die Zeile auf, die Sie für diese Website in ein Überwachungssystem aufnehmen würden.

## Erwartetes Ergebnis

- Schritt 1 sagt, dass Solr nicht konfiguriert ist, weil `[SolrSettings] Enabled is not true`.
- Schritt 2 listet die `solr.ini` des Kernels und die der Erweiterung auf und nach Schritt 3 die Override-Datei.
- Schritt 4 meldet ein konfiguriertes Solr, das antwortet.
- Schritt 5 meldet, dass Solr anderswo verwaltet wird (eine `Url` ist gesetzt), beendet nichts und endet mit `4`.
- Schritt 6 meldet, dass Solr nicht antwortet, mit Exit-Status `1`; Schritt 7 antwortet wieder.
- Der Trainer prüft die Überwachungszeile aus Schritt 8: `exp:solr status` oder `ping`, ausgeführt als Benutzer der
  Website.

## Quiz

1. Welcher Block entscheidet, wohin ezfind seine Suchen schickt?
   A. `[SolrSettings]` B. `[SolrBase]` C. `[SearchSettings]` D. `[SiteSettings]`
2. Was tun `start` und `stop`, wenn `[SolrSettings] Url` gesetzt ist?
3. Was ist der mitgelieferte Wert von `[SolrSettings] Enabled`?
4. Warum endet `exp:solr start` auf einer Installation ohne Solr mit einem Wert ungleich null?
5. Wohin gehören Overrides von `solr.ini`?

<details>
<summary>Antworten</summary>

1. **B.** `SearchServerURI` in `[SolrBase]` von ezfind.
2. Sie melden, dass Solr anderswo verwaltet wird, und tun nichts
   ([Befehle zur Serversteuerung](../../../../../eng-us/features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).
3. `false`.
4. Damit eine Überwachungsprüfung „nicht konfiguriert“ nie mit „läuft“ verwechselt.
5. In `settings/override/solr.ini.append.php` (oder die Einstellungen eines Siteaccess), nie in `settings/solr.ini`.

</details>

## Weiterführende Informationen

- [Befehle zur Serversteuerung](../../../../../eng-us/features/6.0/web-server-and-solr-commands.md): die Referenz zu `exp:solr`
  und die Tabelle zu `[SolrSettings]`.
- [Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md): Prüfungen in die
  Überwachung aufnehmen.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md#der-befehl-expsolr): der Befehl aus Sicht eines Hosters.

## Verwandte Seiten

- [Modul 4: Solr und ezfind](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
