---
title: Nebenläufigkeit, Statics und langlebige Prozesse
audience: software-engineer, developer
category: software-engineering
order: 340
summary: Wie Sie Exponential-Code schreiben, der korrekt ist, wenn ein PHP-Prozess viele Anfragen bedient und viele Prozesse eine Website bedienen - statische Caches und Singletons, idempotente Registrierungen, exit und cleanExit, Absicherung mit class_exists nach einer Bereitstellung, Datenbankverbindungen und Transaktionen, Dateirennen zwischen Benutzern und Knoten und wie Sie diese Fehler testen.
keywords: Nebenläufigkeit, Statics, statische Eigenschaften, Singleton, langlebiger Prozess, persistenter Worker, Velocity, idempotent, Registrierungen, exit, cleanExit, class_exists, Race Condition, Dateisperren, Vorwärmen, Softwareingenieur, concurrency, static properties, long-lived process, persistent worker, registrations, race condition, file locking, warm-up
type: help
---
# Nebenläufigkeit, Statics und langlebige Prozesse

Diese Seite richtet sich an Ingenieure, die PHP für Exponential schreiben oder prüfen. Zwei Tatsachen machen einige
klassische PHP-Gewohnheiten unsicher: Unter Velocity bedient ein Prozess viele Anfragen, und auf jedem Server bedienen
viele Prozesse (und manchmal mehrere Rechner) gleichzeitig eine Website. Die Regeln hier stammen aus
[Kernel-Architektur](../../standards/architektur/kernel-architektur.md#server-velocity-php-fpm-und-frankenphp) und
[der Spezifikation des Worker-Pools](../../spezifikationen/6.0/velocity-worker-pool.md).

## Inhalt

1. [Zwei Arten des Teilens](#zwei-arten-des-teilens)
2. [Statics und Singletons](#statics-und-singletons)
3. [Registrierungen müssen idempotent sein](#registrierungen-müssen-idempotent-sein)
4. [Eine Anfrage beenden](#eine-anfrage-beenden)
5. [Code, der sich unter einem laufenden Worker ändert](#code-der-sich-unter-einem-laufenden-worker-ändert)
6. [Ressourcen, die die Anwendung verwalten muss](#ressourcen-die-die-anwendung-verwalten-muss)
7. [Rennen zwischen Prozessen](#rennen-zwischen-prozessen)
8. [Auf diese Fehler testen](#auf-diese-fehler-testen)

## Zwei Arten des Teilens

| Teilen | Wo | Typischer Fehler |
|---|---|---|
| In der Zeit: ein Prozess, viele Anfragen | persistente Worker von Velocity, das Vorwärmen | ein Wert aus Anfrage A erscheint in Anfrage B |
| Im Raum: viele Prozesse, eine Website | jeder Server, mehrere Webknoten, Cronjobs neben Webanfragen | zwei Prozesse schreiben dieselbe Datei oder Zeile |

## Statics und Singletons

Der Kernel verwendet statische Caches und Singletons breit (`eZINI::instance()`, `eZDB::instance()`, Caches je Klasse).
Velocity stellt statische Klasseneigenschaften nach jeder Anfrage auf ihre Momentaufnahme beim Start zurück, sodass ein
statischer Cache in einem normalen Worker nicht zwischen Anfragen durchsickert. Zwei Fälle gehen trotzdem schief:

- **Während des Vorwärmens gesetzter Zustand** wird zur Basis jedes Workers. Ein Vorwärmen setzt die Statics von
  Benutzerklassen auf ihre Standards zurück und behält nur Konfiguration und Typregister.
- **Zustand, den die Momentaufnahme nicht sieht**: in `keepGlobals` genannte Globals, einmal mit `include_once` befüllte
  Register, in Statics gehaltene Ressourcen (Verbindungen, Datei-Handles).

Die Regel für Kernel-Code: **keine Daten je Anfrage in Statics ohne Zurücksetzen.** Übergeben Sie Werte lieber als
Parameter und schlüsseln Sie anfragebezogene Caches nach etwas, das sich je Anfrage ändert.

## Registrierungen müssen idempotent sein

Ein Listener, Handler oder Callback, der in einem langlebigen Worker einmal je Anfrage angehängt wird, häuft sich an: Die
zweite Anfrage führt ihn zweimal aus. Registrieren Sie so, dass es nichts bewirkt, wenn die Registrierung schon existiert,
und benennen Sie Listener als Zeichenketten `Class::method`, damit Duplikate erkennbar sind. Das gilt für
`ezpEvent`-Listener, Registrierungen von Template-Operatoren und Autoloader
([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).

## Eine Anfrage beenden

- Verwenden Sie `exit` nicht zur Ablaufsteuerung. Kernel-Code beendet eine Anfrage über `eZExecution::cleanExit()`.
- Unter Velocity werden `exit` und `die` so umgeschrieben, dass sie die Anfrage beenden, nicht den Worker.
- Schließen Sie einen Download, auf den `cleanExit()` folgt, nicht in einen Block `catch ( Exception )` ein, der auch das
  Signal zum Beenden der Anfrage fängt: Die Seite wird an den Download angehängt, und die Ausgabe verdoppelt sich.

## Code, der sich unter einem laufenden Worker ändert

Worker erben die Klassen, die das Vorwärmen geladen hat. Nach einer Bereitstellung:

- ist eine **neue Klasse**, die eine mitgelieferte INI-Datei nennt, einem Worker unbekannt, der vor der Bereitstellung
  gestartet wurde; sichern Sie ihre Verwendung dort, wo der Kernel sie lädt, mit `class_exists()` ab, wie es
  `ezpKernelWeb` tut;
- erreicht eine **geänderte Klasse** die Worker erst nach `exp:velocity restart` oder `exp:velocity deploy`;
- braucht ein **geändertes Template** oder Stylesheet keinen Neustart, nur die Template-Caches und den Antwort-Cache von
  Velocity.

## Ressourcen, die die Anwendung verwalten muss

| Ressource | Was zu tun ist |
|---|---|
| Zustand von Datenbanktransaktionen | vor dem Ende der Anfrage festschreiben oder zurückrollen; nie eine Transaktion offen lassen |
| Datenbankverbindung im Vorwärmen | vor dem Fork schließen, damit Worker keinen Socket teilen |
| Datei-Handles, Stream-Kontexte, cURL-Handles | in der Anfrage schließen, die sie geöffnet hat |
| Signal-Handler | nicht im Anfrage-Code installieren |

## Rennen zwischen Prozessen

- **Dateien unter `var/`** können von verschiedenen Benutzern des Betriebssystems geschrieben werden (dem Benutzer des
  Webservers, dem Benutzer, der Cron oder die Konsole ausführt, dem Worker-Benutzer von Velocity). Legen Sie Dateien mit
  den Dateimodi der Installation an und testen Sie mit gemischten Benutzern ([Dateimodi](../../bc/6.0/dateimodi.md)).
- **Cache-Dateien** laufen über `eZClusterFileHandler`, das gleichzeitiges Erzeugen auf gemeinsamem Speicher behandelt.
- **Cronjob-Teile** müssen gefahrlos zweimal laufen und unterbrochen werden können.
- **SQLite** serialisiert Schreibende mit einer Sperrdatei; lange Schreibtransaktionen blockieren andere Schreibende.

## Auf diese Fehler testen

- Führen Sie einen Test zweimal im selben Prozess aus: Zustand, den der erste Lauf hinterlässt, zeigt sich im zweiten.
- Rufen Sie unter Velocity mehrere verschiedene URLs nacheinander auf und prüfen Sie, dass jede ihre eigenen Inhalte
  liefert.
- Ein Test, der allein besteht und im ganzen Lauf fehlschlägt, teilt Zustand mit einem anderen Test; führen Sie die
  beiden Dateien zusammen aus, um das Paar zu finden ([CI-Gates](ci-gates.md)).
- Beobachten Sie das Log von Velocity auf Worker, die über der Speichergrenze ersetzt werden: ein Leck.

## Verwandte Seiten

- [Persistente Worker von Velocity](persistente-worker-von-velocity.md)
- [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md)
- [Leistung in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/leistung-in-erweiterungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
