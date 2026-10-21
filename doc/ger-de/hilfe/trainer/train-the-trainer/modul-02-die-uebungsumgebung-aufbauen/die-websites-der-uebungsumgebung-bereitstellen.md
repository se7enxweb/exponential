---
title: "Lektion 2.4: Die Websites der Übungsumgebung mit Velocity bereitstellen"
audience: trainer, admin
category: training
order: 24
summary: Starten, prüfen und stoppen Sie die Schulungs-Websites mit exp:velocity, mit der Engine php für eine Übungsumgebung auf Laptops und qbix für einen gemeinsamen Schulungsserver, mit einem Port pro Installation. 35 Minuten.
keywords: velocity, exp:velocity, start, stop, status, engine php, qbix, port 8087, eingebauter server, übungsumgebung, bereitstellen, built-in server, training lab, serving
type: help
---
# Lektion 2.4: Die Websites der Übungsumgebung mit Velocity bereitstellen

Eine Installation braucht einen Webserver, bevor die Teilnehmenden sie öffnen können. Die Konsole von Exponential
startet einen für Sie: `exp:velocity` steuert Exponential Velocity, und seine mitgelieferte Standard-Engine ist der
eingebaute Server von PHP, der keine weitere Installation braucht. Diese Lektion zeigt, wie Sie die Website jeder
Teilnehmerin und jedes Teilnehmers bereitstellen, prüfen und wieder stoppen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Übungsumgebungen vorbereiten |
| Voraussetzungen | [Lektion 2.3](eine-installation-pro-teilnehmer.md): eine installierte Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Website mit `php bin/php/console exp:velocity start` und `stop` starten und stoppen;
- `exp:velocity status` lesen, um jede Adresse der Website zu finden;
- eine Installation auf einen anderen Port verlegen, sodass mehrere auf einem Rechner laufen können;
- zwischen der Engine `php` (Laptop) und der Engine `qbix` (gemeinsamer Schulungsserver) wählen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie die Engine-Tabelle aus [Die Website bereitstellen, 8.3.2](../../../../installation/08-die-website-ausliefern.md#832-die-engine-wählen)
   (4 Minuten). `php`: Entwicklung, die mitgelieferte Voreinstellung, Port 8087, kein TLS. `qbix`: empfohlen von der
   Entwicklung bis zur Produktion, Ports 8088 (HTTP) und 8080 (HTTPS).
2. Starten Sie eine Website und führen Sie `status` aus (3 Minuten). Der Status listet jede Adresse auf, die man
   öffnen kann: die Pfade der Website, den Login der Administration, Setup > Systeminformationen und Setup > Caches.
3. Zeigen Sie den Portwechsel (3 Minuten):
   `php bin/php/console exp:velocity config set PHPServerSettings Port 8091`, dann neu starten.

### Häufige Fragen

**Können Teilnehmende die Websites der anderen öffnen?** Mit der voreingestellten Bind-Adresse `127.0.0.1` ist die
Website nur vom Rechner selbst aus erreichbar. Das ist die richtige Voreinstellung für eine Übungsumgebung auf
Laptops.

**Ist die Engine `php` gut genug?** Für eine Übungsumgebung, ja. Sie ist reines HTTP und nur für die Entwicklung;
niemals für Besucher.

**Was ist mit einem gemeinsamen Schulungsserver?** Verwenden Sie dort die Engine `qbix`, nachdem Sie
[Die Website bereitstellen, 8.3](../../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg)
gelesen haben, oder einen klassischen Webserver mit einem virtuellen Host pro Installation.

### Stolperfallen

- **Port bereits belegt.** Zwei Installationen auf einem Rechner verwenden beide standardmäßig 8087. Geben Sie jeder
  ihren eigenen Port.
- **Eine Wartungsseite.** Hat ein Installationslauf mittendrin angehalten, ist die Website im Wartungsmodus:
  `php bin/php/maintenance.php status`, dann `off`.
- **Falsches `--url`.** Wurde die Website mit `--url=http://localhost` installiert, aber unter einem anderen Port
  geöffnet, zeigen Links an die falsche Stelle. Installieren Sie mit der Adresse, die Sie verwenden werden.

## Praktische Übung

Auf dem Rechner mit der Installation aus Lektion 2.3:

1. Im Wurzelverzeichnis der Installation: `php bin/php/console exp:velocity start`.
2. `php bin/php/console exp:velocity status` und suchen Sie die Adresse der Website und des Logins der Administration.
3. `curl -sI http://127.0.0.1:8087/ | head -1` und erwarten Sie `HTTP/1.1 200 OK` oder eine Weiterleitung.
4. Öffnen Sie die Adresse der Administration aus dem Status in einem Browser und melden Sie sich als `admin` mit dem
   erzeugten Passwort an.
5. Stoppen Sie den Server: `php bin/php/console exp:velocity stop`.
6. Verlegen Sie die Installation auf einen anderen Port:
   `php bin/php/console exp:velocity config set PHPServerSettings Port 8091`, starten Sie dann erneut und prüfen Sie
   mit `status`.
7. Stellen Sie den Port mit `php bin/php/console exp:velocity config unset PHPServerSettings Port` zurück und stoppen
   Sie den Server.

## Erwartetes Ergebnis

- Die Website hat unter `http://127.0.0.1:8087/` geantwortet, und die Anmeldung in der Administration hat
  funktioniert.
- Nach dem Portwechsel hat `status` Adressen auf Port 8091 aufgelistet.
- Nach `unset` ist der mitgelieferte Port zurück; der Server ist am Ende gestoppt.

## Quiz

1. Welche Velocity-Engine ist die mitgelieferte Voreinstellung, und auf welchem Port lauscht sie?
2. Welcher Befehl zeigt jede Adresse einer laufenden Website?
3. Zwei Installationen auf einem Laptop: Was muss sich unterscheiden?
4. Warum ist die voreingestellte Bind-Adresse `127.0.0.1`?

<details>
<summary>Antworten</summary>

1. Die Engine `php` (der eingebaute Server von PHP), Port 8087.
2. `php bin/php/console exp:velocity status`.
3. Ihre Ports (zum Beispiel mit `exp:velocity config set PHPServerSettings Port <port>`).
4. Der Server hat keine eigene Authentifizierung und stellt das ganze Dokumentverzeichnis bereit; er ist nur vom
   Rechner selbst aus erreichbar (oder über einen Tunnel oder Proxy).

</details>

## Weiterführende Literatur

- [Die Website bereitstellen](../../../../installation/08-die-website-ausliefern.md): Velocity, FrankenPHP, Apache und nginx.
- [Der Webserver Velocity](../../../../funktionen/6.0/velocity-webserver.md): die Funktionsseite.
- [Wartungsmodus](../../../../anleitungen/wartungsmodus.md): wann eine Website die Wartungsseite zeigt.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
