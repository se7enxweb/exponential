---
title: "Lektion 2.3: URLs, Weiterleitungen und Metadaten für Suchmaschinen"
audience: trainer, admin, developer
category: training
order: 23
summary: Die Teilnehmenden halten jede alte Adresse einer migrierten Website funktionsfähig - mit Knoten-Aliasen, weiterleitenden eigenen URL-Aliasen, URL-Wildcards oder Webserver-Regeln -, bauen und testen eine Weiterleitungstabelle und übernehmen die Metadaten für Suchmaschinen. 60 Minuten.
keywords: migration zu exponential, url-alias, weiterleitung, url-wildcard, weiterleitungstabelle, suchmaschinenoptimierung, globaler url-alias, migrating to exponential, url alias, redirect, 301, url wildcard, redirect table, seo, xrowmetadata, sitemap, canonical, 404, global url alias
type: help
---
# Lektion 2.3: URLs, Weiterleitungen und Metadaten für Suchmaschinen

Eine Migration, die Adressen verliert, verliert Suchmaschinen-Ranking und jeden Link, den andere Websites auf sie
gesetzt haben. Diese Lektion vermittelt die drei Wege, auf denen Exponential eine alte Adresse erhält, wann der
Webserver helfen muss und wie man eine Weiterleitungstabelle automatisch testet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 2: Inhalte auf Exponential abbilden](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und alle, die für die Suchmaschinenoptimierung verantwortlich sind |
| Voraussetzungen | [Lektion 2.1](klassen-datentypen-und-der-baum.md); ein Administratorkonto auf einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- für jede alte Adresse entscheiden: gleiche Adresse, 301-Weiterleitung oder bewusst entfernt (410 oder 404);
- zwischen dem eigenen Alias eines Knotens, einem weiterleitenden eigenen Alias, einer URL-Wildcard und einer
  Webserver-Regel wählen;
- in der Administration eine URL-Wildcard anlegen und ausprobieren;
- einen automatischen Test für eine Weiterleitungstabelle schreiben;
- die zu übernehmenden Metadaten für Suchmaschinen aufzählen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Checkliste, Abschnitte 4 und 5](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen),
[Inhalte abbilden, Abschnitt 10](../../../../installation/migration/inhaltsmodell-zuordnen.md#10-urls) und
[Links, URL-Aliase, Wildcards und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md).

1. **Drei Entscheidungen** (3 Minuten). Jede alte Adresse wird beibehalten, mit 301 weitergeleitet oder bewusst
   entfernt.
2. **Drei Werkzeuge in Exponential** (8 Minuten):
   - der **eigene URL-Alias des Knotens**, wenn der alte Pfad der neue Pfad sein kann (der Baum spiegelt die alte
     Struktur);
   - ein **eigener URL-Alias** am Knoten, der (mit 301) auf seine Hauptadresse weiterleitet, einer pro abweichender
     alter Adresse;
   - eine **URL-Wildcard** (`news/*` zu `articles/{1}`) für ganze Gruppen, die sich regelmäßig geändert haben.
3. **Der Webserver** (3 Minuten). Adressen mit Query-Strings (`?p=123`, `index.php?id=5`) erreichen die URL-Aliase
   nie: Leiten Sie sie im Webserver weiter, aus derselben Tabelle.
4. **Tests** (3 Minuten). Testen Sie jede Zeile automatisch, Status und Ziel, nicht stichprobenartig. Behalten Sie
   Weiterleitungen mindestens ein Jahr bei.
5. **Metadaten** (3 Minuten). Titel und Beschreibungen werden `xrowmetadata` zugeordnet; Canonical, `hreflang` und
   Robots-Regeln liegen im Pagelayout; eine XML-Sitemap nach dem Go-live.

### Häufige Fragen

**Woraus wird die Weiterleitungstabelle erzeugt?** Aus dem Import: Remote-ID zu neuem Knoten, alte Adresse zu neuer
Adresse (Checkliste, Abschnitt 4). Das ist ein weiterer Grund für Remote-IDs.

**Welches Werkzeug für 5 000 Blogbeiträge, deren Pfad nur den Datumsteil verloren hat?** Wenn die Änderung regelmäßig
ist, eine Wildcard oder eine Webserver-Regel; wenn nicht, ein weiterleitender Alias pro Beitrag, vom Import erzeugt.

**Was geschieht, wenn eine Wildcard und ein Alias beide passen?** Die Seite in der Administration sagt es:
Wildcards werden nur herangezogen, wenn kein URL-Alias zur Adresse passt, und die erste passende Wildcard wird
verwendet.

**Funktioniert die alte Adresse bei einem In-place-Upgrade einer alten Exponential-Website weiter?** Ja: Die
URL-Alias-Tabellen ziehen mit der Datenbank um ([Ältere Versionen](../../../../installation/migration/aeltere-versionen.md)).

### Stolperfallen

- **Durch Klicken testen.** Zehn Klicks testen keine 3 000 Zeilen. Verwenden Sie ein Skript.
- **Medienadressen vergessen.** Auch auf alte Bild- und PDF-Adressen verlinken andere Websites.
- **Wildcards in einer Schulungsinstallation stehen lassen.** Die Teilnehmenden entfernen ihre Wildcard am Ende der
  Übung.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Administratorkonto.

1. Öffnen Sie in der Administration **Setup > URL Platzhalter**. Lesen Sie die Erklärung oben auf der Seite.
2. Geben Sie unter **Neuer URL Platzhalter** `old-news/*` als abzufangende Adresse und `news/{1}` als **Ziel** ein.
   Lassen Sie **URL umleiten** angehakt.
3. Klicken Sie auf **Erstellen**.
4. Geben Sie unter **Eine Adresse ausprobieren** im Feld **Adresse** `old-news/2026/october` ein und klicken Sie auf
   **Ausprobieren**. Notieren Sie, welche Wildcard gepasst hat und wohin die Adresse führt.
5. Fragen Sie in einem Terminal die Website nach der alten Adresse und sehen Sie sich den Status und den
   `Location`-Header an (ersetzen Sie den Host durch die Adresse Ihrer Schulungswebsite):

   ```bash
   curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' http://localhost/old-news/2026/october
   ```

6. Öffnen Sie die URL-Alias-Seite eines eigenen Knotens unter `/admin/content/urlalias/<node id>`. Lesen Sie die Liste
   **Generated aliases** und das Formular **Neuer Alias** mit **Der Alias soll zum Ziel weiterleiten**. Legen Sie
   keinen Alias an.
7. Schreiben Sie in Ihren Notizen eine Weiterleitungstabelle mit fünf Zeilen für das WordPress-Szenario: einen alten
   Permalink, `/?p=123`, `/category/news/`, `/wp-content/uploads/2024/05/plan.pdf` und `/wp-login.php`. Schreiben Sie
   für jede Zeile das Werkzeug (Knoten-Alias, eigener Alias, Wildcard, Webserver) und den Status auf. Verwenden Sie
   die Tabelle in der [WordPress-Anleitung, Abschnitt 4](../../../../installation/migration/wordpress.md#4-adressen-und-weiterleitungen).
8. Schreiben Sie eine Testschleife für die Tabelle: Fragen Sie für jede Zeile die alte Adresse ab und vergleichen Sie
   Status und Ziel, wie in Schritt 5.
9. Wählen Sie wieder unter **Setup > URL Platzhalter** Ihre Wildcard aus und klicken Sie auf **Ausgewählte entfernen**.

## Erwartetes Ergebnis

- Schritt 4: Die Seite zeigt, dass `old-news/*` gepasst hat, und das Ziel `news/2026/october`.
- Schritt 5: Status `301` und eine Weiterleitungsadresse, die auf `news/2026/october` endet.
- Die Weiterleitungstabelle schickt `/?p=123` an den Webserver (Query-Strings erreichen die URL-Aliase nie) und
  markiert `/wp-login.php` mit 410 oder einer Weiterleitung zur neuen Anmeldung.
- Nach Schritt 9 ist die Liste der Wildcards wieder leer.
- Der Trainer prüft Schritt 5 auf zwei Bildschirmen und eine Weiterleitungstabelle.

## Quiz

1. Welches Exponential-Werkzeug passt zu einer ganzen Gruppe alter Adressen, die sich regelmäßig geändert haben?
   A. Ein globaler URL-Alias B. Eine URL-Wildcard C. Eine Sektion D. Ein Objektzustand
2. Warum lässt sich `index.php?id=5` nicht mit einem URL-Alias weiterleiten?
3. Wann wird eine URL-Wildcard herangezogen?
4. Wie lange sollten Weiterleitungen laut Checkliste beibehalten werden?
5. Nennen Sie zwei Arten von Metadaten für Suchmaschinen, die übernommen werden sollten.

<details>
<summary>Antworten</summary>

1. **B** ([Checkliste, Abschnitt 4](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen)).
2. Query-Strings erreichen die URL-Aliase nie; behandeln Sie sie im Webserver
   ([WordPress-Anleitung, Abschnitt 4](../../../../installation/migration/wordpress.md#4-adressen-und-weiterleitungen)).
3. Nur wenn kein URL-Alias zur Adresse passt; die erste passende Wildcard gewinnt (die Seite **URL Platzhalter** in der
   Administration und [URLs und Aliase, Abschnitt 4](../../../../anleitungen/urls-und-aliase.md#4-url-platzhalter)).
4. Mindestens ein Jahr (Checkliste, Abschnitt 4).
5. Zwei beliebige aus: Titel und Meta-Beschreibungen, Canonical-Adressen, `hreflang`, Robots-Regeln, die XML-Sitemap,
   strukturierte Daten, Analyse- und Verifizierungs-Tags
   ([Checkliste, Abschnitt 5](../../../../installation/migration/checkliste.md#5-metadaten-für-suchmaschinen)).

</details>

## Weiterführende Informationen

- [Links, URL-Aliase, Wildcards und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md): jedes Adresswerkzeug der
  Administration.
- [Die gemeinsame Migrations-Checkliste, Abschnitt 4](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen):
  die Schritte für Weiterleitungen.
- [Die Import- und Exportwerkzeuge von Exponential, Abschnitt 3](../../../../installation/migration/exponential-tools.md#3-urls-weiterleitungen-und-metadaten):
  die Werkzeuge für Adressen und Metadaten, mit Befehlen.

## Verwandte Seiten

- [Modul 2: Inhalte auf Exponential abbilden](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
