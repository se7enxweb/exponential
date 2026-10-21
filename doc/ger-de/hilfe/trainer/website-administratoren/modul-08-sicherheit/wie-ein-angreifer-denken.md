---
title: "Lektion 8.1: Denken wie ein Angreifer"
audience: trainer, admin
category: training
order: 81
summary: Die Teilnehmenden benennen die Angriffe, denen jede öffentliche Website begegnet, ordnen sie den in Exponential eingebauten Abwehrmaßnahmen zu, prüfen von außen, dass der Webserver keine private Datei ausliefert, und arbeiten auf einer Schulungsinstallation die Checkliste zur Härtung durch. 40 Minuten.
keywords: website-administration, sicherheitshärtung, angriffe, sicher von haus aus, geringste rechte, request classifier, security.log, request rules, htaccess, prüfung von außen, checkliste go-live, site administration, security hardening, attacks, secure by default, least privilege, outside check, go-live checklist
type: help
---
# Lektion 8.1: Denken wie ein Angreifer

Fast jeder Angriff auf ein Content-Management-System ist automatisiert: Scanner suchen nach bekannten Dateien, Bots
probieren geleakte Passwörter aus, Scraper zählen jede Adresse durch. Exponential 6 ist gegen das meiste davon von
Haus aus sicher, und die Aufgabe des Administrators ist es, diese Voreinstellungen in Kraft zu halten und zu
schließen, was noch offen ist. Diese Lektion eröffnet das Sicherheitsmodul mit der Sicht des Angreifers und einer
Prüfung, die jeder Administrator in zwei Minuten ausführen kann.

> **Hinweis**
> Führen Sie die Prüfung von außen nur gegen Ihre eigene Schulungsinstallation aus. Eine Website zu sondieren, die
> Sie nicht betreiben, ist ein Angriff.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 8: Sicherheit](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Sicherheitsbeauftragte |
| Voraussetzungen | [Modul 7](../modul-07-logs-und-das-audit-protokoll/README.md); eine über HTTP ausgelieferte Schulungsinstallation und ein Terminal |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- vier Arten automatisierter Angriffe auf ein CMS und je eine eingebaute Abwehr dagegen nennen;
- die Prinzipien „sicher von Haus aus“, „geringste Rechte“ und „Trennung der Rechte“ mit je einem Beispiel erklären;
- von außen prüfen, dass Punktdateien, Einstellungen und Logs nie ausgeliefert werden;
- die Urteile des Request Classifiers in `security.log` finden;
- mit der Checkliste zur Härtung herausfinden, was auf einer Installation noch offen ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Sicherheitshärtung](../../../sicherheitshaertung.md) und
[Kapitel 13: Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md), Abschnitt 13.2.

1. **Wer angreift** (5 Minuten). Botnetze und Scanner (`/wp-login.php`, `/.env`, `/.git/config`), Angreifer mit
   Zugangsdaten (geleakte Paare, Standardpasswörter), Zugangshändler, die eine funktionierende Anmeldung verkaufen,
   Scraper und KI-Crawler sowie Angreifer über die Lieferkette. Nichts davon ist persönlich gemeint.
2. **Die Prinzipien** (5 Minuten). Zeigen Sie die Tabelle der Prinzipien auf der Seite zur Härtung: sicher von Haus
   aus, geringste Rechte, Trennung der Rechte, minimale Angriffsfläche, Prüfung, Korrektur nach vorn. Nennen Sie zu
   jedem eine Sache, die eingebaut ist, und eine, die Aufgabe des Administrators ist.
3. **Was der Webserver nie ausliefern darf** (5 Minuten). Nur eine feste Liste von Asset-Pfaden wird als Datei
   ausgeliefert; alles andere geht an `index.php`. Punktdateien und Skripte unterhalb des Wurzelverzeichnisses
   antworten mit 404. Liefern Sie die Installation nie mit einer allgemeinen Konfiguration „Datei ausliefern, wenn sie
   existiert“ aus.
4. **Beobachten** (3 Minuten). `security.log` enthält die Urteile des Classifiers; er stuft ein, er blockiert nicht.
   Blockieren ist die Aufgabe eines vorgeschalteten Schutzschilds, des Webservers oder der Request Rules, die eine
   Anfrage erlauben, ablehnen, umleiten, umschreiben oder protokollieren können, nachdem ihr URL-Alias übersetzt ist.
5. **Melden** (2 Minuten). Schwachstellen werden vertraulich gemeldet, wie die Seite zur Härtung sagt, nie in einem
   öffentlichen Issue.

### Häufige Fragen

**Ist eine Antwort 200 für `/settings/site.ini` ein Leck?** Nicht, wenn der Inhalt eine HTML-Seite von Exponential
ist. Ein Leck ist es nur, wenn der INI-Text zurückkommt. Sehen Sie sich den Inhalt einmal an.

**Können wir Adressen wie `/content/view/full/123` verbergen?** Ja, mit einer Request Rule, die System-URLs auf ihren
Alias umleitet; siehe [Request Rules](../../../../../eng-us/features/6.0/request-rules.md). Probieren Sie es zuerst auf einer
Schulungs-Website aus; es gibt eine Regel, die nur protokolliert, damit Sie sehen, wer betroffen wäre.

**Blockiert Exponential Angreifer?** Der Classifier nicht; die Seite zur Härtung beschreibt den Schutzschild und den
Webserver als die Stellen, die blockieren.

### Stolperfallen

- **Regeln löschen, damit etwas funktioniert.** Ein fehlendes Bild ist nie ein Grund, eine Regel aus `.htaccess` zu
  entfernen. Suchen Sie die Ursache.
- **Den falschen Host testen.** Die Prüfung von außen muss die Adresse verwenden, die auch die Besucher verwenden,
  über denselben Webserver und Proxy.
- **Den Teilnehmenden Angst machen.** Bleiben Sie sachlich: Der Punkt ist, dass die meisten Abwehrmaßnahmen bereits
  eingeschaltet sind.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, zum Beispiel mit `exp:velocity start` ausgeliefert (siehe
[Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md)). Ersetzen Sie `http://127.0.0.1:8087` durch
ihre Adresse.

1. Führen Sie in einem Terminal die Prüfung von außen aus:

   ```bash
   for p in /.git/config /.env /settings/site.ini /composer.json /kernel/classes/ezini.php /var/log/error.log; do
     printf '%-32s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8087$p)"
   done
   ```

2. Sehen Sie sich für `/settings/site.ini` einmal den Inhalt an:
   `curl -s http://127.0.0.1:8087/settings/site.ini | head -5`. Er muss HTML sein, kein INI-Text.
3. Rufen Sie eine typische Sondierungsadresse auf:
   `curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8087/wp-login.php`.
4. Öffnen Sie in der Administration **Setup > Logdateien** und öffnen Sie `security.log`. Suchen Sie Ihren
   Sondierungsversuch und lesen Sie seine Kategorie und Regel.
5. Lesen Sie die Checkliste am Ende von [Sicherheitshärtung](../../../sicherheitshaertung.md#checkliste). Markieren Sie
   jede Zeile für Ihre Schulungsinstallation als *erledigt*, *offen* oder *nicht zutreffend*.
6. Prüfen Sie zwei der Zeilen selbst: Ist die Debug-Ausgabe aus (**Setup > Ini Einstellungen** oder
   `php bin/php/console exp:ini get site.ini/DebugSettings/DebugOutput`), und existiert
   `var/log/initial-admin-password` noch?
7. Sehen Sie sich an, was die Request Rules für eine System-URL entscheiden würden, ohne etwas zu ändern:

   ```bash
   php bin/php/ezrequestrules.php -s <site> --uri=content/view/full/2
   ```

8. Teilen Sie Ihre drei wichtigsten offenen Zeilen mit der Gruppe.

## Erwartetes Ergebnis

- Schritt 1 zeigt `404` für jeden Pfad, oder `200` nur dort, wo der Inhalt eine Seite von Exponential ist (Schritt 2).
- Schritt 4 zeigt für die Anfrage an `/wp-login.php` eine Zeile mit einer Kategorie wie `probe/...`.
- Schritt 5 gibt allen Teilnehmenden eine markierte Checkliste; auf einer frischen Schulungsinstallation sind die
  Zeilen zu Passwörtern und Dateien meist offen.
- Der Trainer sammelt die offenen Zeilen auf einem Flipchart; sie sind die Tagesordnung für den Rest des Moduls.

## Quiz

1. Was davon wird von den ausgelieferten Regeln nicht als Datei ausgeliefert?
   A. Ein Bild unterhalb von `storage/original/image` B. `/.env` C. Ein Stylesheet eines Designs D. Dateien unter
   `/.well-known/`
2. Was macht der Request Classifier mit einer Anfrage, die er als Angriff einstuft?
3. Nennen Sie das Prinzip hinter „CLI-Skripte verweigern ohne `--allow-root-user` die Ausführung als root“.
4. Eine Kollegin möchte die nginx-Konfiguration „Datei ausliefern, wenn sie existiert, sonst `index.php`“ verwenden.
   Was antworten Sie?
5. Wo melden Sie eine Schwachstelle, die Sie in Exponential gefunden haben?

<details>
<summary>Antworten</summary>

1. **B.** Punktdateien antworten mit 404; `/.well-known/` ist die einzige Ausnahme
   ([Kapitel 13, 13.2](../../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)).
2. Er schreibt sie mit ihrer Kategorie und Regel in `security.log`; er blockiert nichts.
3. Geringste Rechte.
4. Nein: Eine solche Konfiguration liefert jede vorhandene Datei aus, auch `settings/*.ini` und die SQLite-Datenbank.
   Verwenden Sie den Server-Block aus Kapitel 8.
5. Vertraulich, wie [Eine Schwachstelle melden](../../../sicherheitshaertung.md#eine-schwachstelle-melden) beschreibt;
   nie in einem öffentlichen Issue.

</details>

## Weiterführende Literatur

- [Sicherheitshärtung](../../../sicherheitshaertung.md): jede Bedrohung mit ihrer Abwehr.
- [Kapitel 13: Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md): die
  Referenz Einstellung für Einstellung und die Checkliste für den Go-live.
- [Request Rules](../../../../../eng-us/features/6.0/request-rules.md): Anfragen erlauben, ablehnen, umleiten oder umschreiben.
- [Härtung für Hoster](../../../hosting/sicherheit/haertung-fuer-hoster.md) und
  [Request Shield und Request Rules](../../../hosting/sicherheit/request-shield-und-request-regeln.md).

## Verwandte Seiten

- [Modul 8: Sicherheit](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
