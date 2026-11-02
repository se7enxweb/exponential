# Vom statischen Cache zum statischen Veröffentlichen

Diese Anleitung ist für Administratoren, die den statischen Cache schon nutzen und sich fragen, ob das statische
Veröffentlichen ihn ersetzt, ergänzt oder etwas anderes ist. Sie vergleicht beide und zeigt, wie eine Site vom einen
zum anderen wechselt.

[Anleitungen](README.md) · Verwandt: [Generator für den statischen Cache](../funktionen/6.0/generator-fuer-den-statischen-cache.md),
[Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md),
[Was statisches Veröffentlichen ist](../hilfe/statisches-veroeffentlichen/was-statisches-veroeffentlichen-ist.md)

## Was Sie am Ende haben

Eine klare Wahl für jede Site zwischen statischem Cache, statischem Veröffentlichen oder beidem, und eine erste
statische Kopie, gemacht mit dem Crawler, den Sie schon kennen.

## Zwei Funktionen, ein Crawler

Beide rufen die Site wie ein Besucher ab und schreiben Seiten in Dateien. Sie unterscheiden sich darin, wohin die
Dateien gehen und wer sie ausliefert.

| | Statischer Cache | Statisches Veröffentlichen |
|---|---|---|
| Zweck | Geschwindigkeit: Der Webserver beantwortet anonyme Besucher aus Dateien | Eine öffentliche Kopie, die überhaupt kein Exponential braucht |
| Wo die Dateien liegen | `static` unterhalb der Installation, neben der Live-Site | Der Ordner des Ziels, ein Git-Repository, zu einem Host gepusht |
| Wer sie ausliefert | Ihr Webserver, mit Rewrite-Regeln, vor der Live-Site | GitHub Pages oder ein beliebiger statischer Host |
| Links | unverändert, die eigenen Adressen der Site | umgeschrieben: relativ oder unterhalb eines Basispfads |
| Dateien und Bilder | von der Live-Site ausgeliefert | in die Kopie kopiert |
| Suche, Formulare, Anmeldung | funktionieren: Anfragen, die den Cache verfehlen, erreichen PHP | Ersatz (Suchindex, Formular-Endpunkt, ausgeblendet) |
| Bereinigung | nicht nötig, derselbe Server | Debug-Ausgaben, Formular-Token und Session-IDs herausgenommen; Seiten, die nicht bereinigt werden können, abgelehnt |
| Aktualisierung | beim Veröffentlichen von Inhalten werden die Dateien neu geschrieben | bauen und veröffentlichen, von Hand, beim Veröffentlichen (verzögert) oder nach Zeitplan |
| Verlauf | keiner | jede Veröffentlichung ein Commit und Tag; Zurücknehmen |
| Gestartet in | Setup > Cache > Statischer Inhalts-Cache, `makestaticcache.php` | Setup > Statisches Veröffentlichen, `exp:static:*` |

Der Build des statischen Veröffentlichens nutzt den Crawler des Generators für den statischen Cache in einem
Exportmodus: dieselbe Linkverfolgung, dieselbe Tiefe (`MaxDepth` fällt auf `[CacheSettings] MaxCacheDepth` zurück),
aber ohne Cookie und ohne die Modulansichten.

## Welches

- **Behalten Sie den statischen Cache**, wenn die Live-Site öffentlich bleibt und Suche, Formulare, Anmeldung oder einen
  Shop behalten muss. Er macht diese Site schnell; das statische Veröffentlichen würde diese Funktionen wegnehmen.
- **Nutzen Sie das statische Veröffentlichen**, wenn der öffentliche Teil ohne Server auskommt: eine Broschüre, ein Blog,
  eine Dokumentation, ein Portfolio. Die Live-Installation kann dann intern bleiben, und die öffentliche Site kostet
  kein Hosting.
- **Nutzen Sie beides**, wenn die Live-Site für Mitglieder und Kunden öffentlich bleibt und eine kostenlose statische
  Kopie ein zweites Publikum bedient (ein Spiegel der Dokumentation, ein Archiv, eine Kampagnen-Site).

Die Einstellungen liegen in derselben Datei, `staticcache.ini`: `[CacheSettings]` für den Cache,
`[StaticPublishSettings]` und `[Target_<id>]` für das Veröffentlichen. Sie beeinflussen sich nicht gegenseitig.

## Eine Site umstellen

1. Notieren Sie Tiefe und Seitengrenze Ihres statischen Caches; `MaxDepth` und `MaxPages` des Ziels haben dieselbe Rolle.
2. Legen Sie unter Setup > Statisches Veröffentlichen > **Neues Ziel** ein Ziel für denselben Siteaccess an.
3. Bauen Sie mit `--dry-run` und lesen Sie den Bericht: Seiten, die der statische Cache klaglos ausgeliefert hat, können
   hier **abgelehnt** werden, weil sie eine Session starten oder ein Formular-Token tragen. Lassen Sie sie aus
   (`ExcludePaths[]`) oder ändern Sie sie.
4. Sehen Sie die Kopie an (`exp:static:serve`) und prüfen Sie die Links (`exp:static:diff --check-links`).
5. Veröffentlichen Sie und richten Sie die Domain auf den Host ([eigene Domains und DNS](statisches-veroeffentlichen-eigene-domains-und-dns.md)).
6. Liefert die Kopie die Öffentlichkeit aus, braucht dieser Siteaccess den statischen Cache nicht mehr; schalten Sie ihn
   ab, wenn Sie sicher sind, dass nichts mehr die öffentliche Adresse der Live-Site erreicht.

## Nächster Schritt

[In 15 Minuten eine kostenlose Website](../hilfe/statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
