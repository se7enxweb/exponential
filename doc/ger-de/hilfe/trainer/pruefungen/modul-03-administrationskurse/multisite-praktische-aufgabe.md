---
title: "Lektion 3.6: Multisite, praktische Aufgabe"
audience: trainer, admin, developer, hosting
category: training
order: 36
summary: Eine praktische Aufgabe von 50 Minuten für das Ende des Kurses Multisite und Seitenpakete, die Siteaccess-Einrichtung einer Schulungsinstallation lesen, sie mit Test-Hostnamen auf Host-Matching umstellen, mit curl nachweisen, welcher Siteaccess antwortet, eine Empfehlung für eine oder mehrere Installationen schreiben und die ursprünglichen Einstellungen wiederherstellen, bewertet mit einem Bewertungsraster aus fünf Kriterien. 75 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, multisite, praktische aufgabe, bewertungsraster, siteaccess, MatchOrder, HostMatchMapItems, SiteURL, curl Host-Header, exp:ini, empfehlung, assessments and practical exercises, practical task, rubric, curl Host header, recommendation
type: help
---
# Lektion 3.6: Multisite, praktische Aufgabe

Eine zweite Website beginnt mit einer Matching-Regel. In dieser Aufgabe lesen die Teilnehmenden, wie ihre
Schulungsinstallation ihre Siteaccesses findet, stellen sie mit Test-Hostnamen auf Host-Matching um, weisen das
Ergebnis aus der Shell nach, ohne DNS anzufassen, und setzen alles zurück. Eine kurze schriftliche Empfehlung prüft
den Teil des Kurses, in dem es um Urteilsvermögen geht.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Teilnehmenden ändern nur die
> Einstellungen ihrer eigenen Schulungsinstallation. Die Test-Hostnamen enden auf `.test`, was nie das öffentliche
> Internet erreicht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 3: Die Administrationskurse prüfen](README.md) |
| Dauer | 75 Minuten (5 Einweisung, 50 Aufgabe, 20 Bewertung) |
| Niveau | Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Multisite und Seitenpakete |
| Voraussetzungen | [Lektion 3.5](multisite-schriftlicher-test.md); eine Shell auf der eigenen Schulungsinstallation der Teilnehmenden, bereitgestellt auf einem lokalen Port |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- die Siteaccesses und Matching-Regeln einer Installation auflisten;
- Host-Matching mit einer Host-Zuordnung und den passenden Werten für `SiteURL` konfigurieren;
- mit `curl` nachweisen, welcher Siteaccess auf einen Hostnamen antwortet;
- Einstellungsdateien sicher sichern und wiederherstellen;
- für einen gegebenen Fall eine oder mehrere Installationen begründet empfehlen.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: eine Schulungsinstallation pro Teilnehmer, installiert mit URL-Zugang (die Voreinstellung),
   bereitgestellt auf einem lokalen Port wie in
   [Die Websites der Übungsumgebung bereitstellen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-websites-der-uebungsumgebung-bereitstellen.md).
   Notieren Sie die Namen des öffentlichen Siteaccess und des Administrations-Siteaccess des verwendeten Seitenpakets.
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus; 50 Minuten.
3. **Bewertung**: Lesen Sie die Notizen der Teilnehmenden mit den Ausgaben von `curl` und prüfen Sie, dass die
   Einstellungsdateien am Ende mit ihren Sicherungen übereinstimmen.

### Häufige Fragen

**Warum `curl -H 'Host: ...'` und nicht ein Browser?** Mit dem Header wählen die Teilnehmenden den Hostnamen, ohne DNS
oder die Hosts-Datei des Rechners zu bearbeiten. Die Installation sieht dasselbe, was ein Browser senden würde.

**Braucht Velocity nach der Änderung einen Neustart?** Wenn die Website von Velocity bereitgestellt wird, ja: Es hält
Einstellungen im Speicher ([Caches](../../../../anleitungen/caches.md#kurz-gesagt)). Rechnen Sie es zu Schritt 6.

### Stolperfallen

- **Die Override-Datei überschreiben.** Die Teilnehmenden müssen sie zuerst sichern (Schritt 2). Das Wiederherstellen
  aus der Sicherung wird bewertet.
- **Großbuchstaben oder ein Port in der Zuordnung.** Der Hostname muss genau übereinstimmen, in Kleinbuchstaben und
  ohne Port; ein Tippfehler ergibt für beide Namen denselben Siteaccess, was ein guter Lernmoment ist.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Arbeiten Sie im Wurzelverzeichnis Ihrer eigenen
Schulungsinstallation; notieren Sie jeden Befehl und seine Ausgabe.

1. Listen Sie die aktuellen Siteaccess-Einstellungen auf:

   ```bash
   ./console exp:ini list site.ini/SiteAccessSettings --allow-root-user
   ```

   Notieren Sie `MatchOrder`, `AvailableSiteAccessList[]` und welcher Siteaccess öffentlich ist und welcher die
   Administration.
2. Sichern Sie `settings/override/site.ini.append.php` und die beiden Dateien
   `settings/siteaccess/<name>/site.ini.append.php` des öffentlichen und des Administrations-Siteaccess in ein eigenes
   Verzeichnis unter `var/`.
3. Fügen Sie in `settings/override/site.ini.append.php` Host-Matching hinzu und behalten Sie URL-Matching als
   Rückfallebene:

   ```ini
   [SiteAccessSettings]
   MatchOrder=host;uri
   HostMatchType=map
   HostMatchMapItems[]
   HostMatchMapItems[]=www.training.test;<public siteaccess>
   HostMatchMapItems[]=admin.training.test;<admin siteaccess>
   ```

4. Setzen Sie `[SiteSettings] SiteURL` in der Datei des öffentlichen Siteaccess auf `www.training.test` und in der
   Datei des Administrations-Siteaccess auf `admin.training.test`.
5. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
6. Läuft die Website auf Velocity, starten Sie ihn neu. Testen Sie dann beide Namen gegen Ihren lokalen Port:

   ```bash
   curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' -H 'Host: admin.training.test' http://127.0.0.1:<port>/
   curl -s -o /dev/null -w '%{http_code}\n' -H 'Host: www.training.test' http://127.0.0.1:<port>/
   ```

7. Testen Sie einen nicht aufgeführten Namen, `-H 'Host: other.training.test'`, und notieren Sie, welcher Siteaccess
   geantwortet hat und warum.
8. Schreiben Sie für diesen Fall eine Empfehlung von fünf bis acht Sätzen: *Eine Schule betreibt eine Hauptwebsite und
   eine eigene Website für ihren Ehemaligenverein. Dieselben Mitarbeitenden bearbeiten beide, und die Website der
   Ehemaligen verwendet Nachrichten der Hauptwebsite wieder.* Eine Installation mit zwei Siteaccesses oder zwei
   Installationen?
9. Stellen Sie die drei Dateien aus Ihren Sicherungen wieder her, leeren Sie den INI-Cache (starten Sie Velocity neu,
   wenn er die Website bereitstellt) und prüfen Sie, dass `MatchOrder` wieder dem Wert aus Schritt 1 entspricht.

## Erwartetes Ergebnis

- Notizen mit den ursprünglichen Einstellungen, dem Ort der Sicherungen und den Ausgaben von `curl`.
- Der Administrations-Host antwortete mit einer Weiterleitung zur Anmeldeseite (oder mit der Anmeldeseite), der
  öffentliche Host mit `200`; der nicht aufgeführte Name fiel auf URL-Matching zurück und erreichte den
  Standard-Siteaccess.
- Eine Empfehlung für eine Installation mit zwei Siteaccesses, weil Inhalte und Redakteure geteilt werden, mit der
  Nennung dessen, was die Antwort ändern würde (getrennte Kunden, Isolation, getrennte Sicherungen).
- Nach Schritt 9 sind `MatchOrder` und `SiteURL` wie vorher.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Die Einrichtung lesen | Nichts notiert | Nur MatchOrder | Siteaccesses notiert, Rollen unklar | MatchOrder, Liste und Rollen notiert | Die Notizen |
| Host-Zuordnung und SiteURL | Nicht geschrieben | Zuordnung mit Fehlern (Groß-/Kleinschreibung, Port) | Zuordnung richtig, SiteURL fehlt | Zuordnung und beide SiteURL-Werte richtig | Die Einstellungsdateien (vor der Wiederherstellung), die Notizen |
| Nachweis mit curl | Kein Test | Ein Host getestet | Beide getestet, nicht aufgeführter Name nicht erklärt | Beide getestet, Rückfall erklärt | Die Ausgaben von `curl` |
| Empfehlung | Fehlt | Antwort ohne Begründung | Begründung, keine Bedingung, die sie ändern würde | Begründung und die Bedingungen, die sie ändern würden | Der Text |
| Sichern und Wiederherstellen (Pflicht) | Keine Sicherung | Gesichert, nicht wiederhergestellt | Wiederhergestellt, Cache nicht geleert | Wiederhergestellt, Cache geleert, geprüft | Die Notizen, `exp:ini list` |

**Bestanden**: 10 von 15 Punkten oder mehr und **Sichern und Wiederherstellen** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Warum hielt `MatchOrder=host;uri` die Website für Adressen, die nicht in der Zuordnung stehen, funktionsfähig?
2. Was muss auf einem echten Server die neuen Hostnamen noch kennen?
3. Was würden Sie hinzufügen, wenn die Website hinter einem Proxy auf einem anderen Rechner stünde?
4. Welche Einstellung würden Sie ändern, wenn jede Website ihren eigenen INI-Cache bräuchte?

<details>
<summary>Antworten</summary>

1. Wenn kein Host passt, wird die nächste Methode, `uri`, versucht
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
2. Der Webserver oder Velocity, DNS und das Zertifikat
   ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#zuordnung-nach-url-host-oder-port)).
3. Seine Adresse in `[HTTPHeaderSettings] TrustedProxies[]`
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
4. `[FileSettings] INICacheDir=site`
   ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#logs-und-caches-pro-website)).

</details>

## Weiterführende Literatur

- [10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site):
  die in der Aufgabe verwendeten Befehle.
- [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md): die
  Entscheidungstabelle.
- [Seitenpakete](../../../hosting/bereitstellung/seitenpakete-fuer-hoster.md): ein Multisite-Paket von Anfang an installieren.

## Verwandte Seiten

- [Lektion 3.5: Multisite, schriftlicher Test](multisite-schriftlicher-test.md)
- [Modul 3: Die Administrationskurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
