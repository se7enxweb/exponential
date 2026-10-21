---
title: "Lektion 4.4: Theme-Designer, praktische Aufgabe"
audience: trainer, designer
category: training
order: 44
summary: Eine praktische Aufgabe von 75 Minuten für das Ende des Kurses Theme-Design, eine Theme-Erweiterung mit eigenem Pagelayout bauen, das Design registrieren, es nur für einen Siteaccess verwenden, ein Stylesheet ohne Inline-CSS laden, ein Template überschreiben und das Ergebnis mit Template-Pfadkommentaren nachweisen, bewertet mit einem Bewertungsraster aus sechs Kriterien. 100 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, theme-design, praktische aufgabe, bewertungsraster, theme-erweiterung, pagelayout, stylesheet, kein inline-css, override, template-pfadkommentare, assessments and practical exercises, theme design, practical task, rubric, theme extension, pagelayout.tpl, DesignExtensions, SiteDesign, no inline CSS, template path comments, ezinfo.php, extension.xml
type: help
---
# Lektion 4.4: Theme-Designer, praktische Aufgabe

Die Teilnehmenden bauen aus einem leeren Ordner eine kleine Theme-Erweiterung und schalten sie nur für den
öffentlichen Siteaccess ein. Das Ergebnis muss die Administration unberührt lassen, darf keine Inline-Styles oder
-Skripte enthalten und muss sich mit Template-Pfadkommentaren nachverfolgen lassen.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Alle Teilnehmenden arbeiten auf ihrer
> eigenen Schulungsinstallation; Einstellungen werden für einen Siteaccess geändert, niemals global.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 4: Die technischen Kurse prüfen](README.md) |
| Dauer | 100 Minuten (5 Einweisung, 75 Aufgabe, 20 Bewertung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Theme-Design |
| Voraussetzungen | [Lektion 4.3](theme-designer-schriftlicher-test.md); eine Shell und ein Editor auf der eigenen Schulungsinstallation |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- eine Theme-Erweiterung mit den nötigen Dateien anlegen;
- ihr Design registrieren und es für einen Siteaccess auswählen;
- ein Stylesheet aus dem Design ohne Inline-CSS laden;
- ein Template für einen Inhaltstyp überschreiben;
- mit Template-Pfadkommentaren nachweisen, welche Datei geantwortet hat, und sie wieder ausschalten.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: eine Schulungsinstallation pro Teilnehmer, über einen lokalen Port ausgeliefert, mit einem Seitenpaket,
   das Artikel enthält. Die Teilnehmenden kennen den Namen des öffentlichen Siteaccess.
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus; 75 Minuten. Das Theme heißt
   `<account>theme`, sein Design `<account>`.
3. **Bewertung**: Öffnen Sie die öffentliche Website und die Administration, sehen Sie sich den Seitenquelltext an,
   lesen Sie die Dateien und führen Sie das grep aus Schritt 9 selbst aus.

### Häufige Fragen

**Dürfen die Teilnehmenden mit dem Design-Assistenten beginnen?** Wenn Ihr Kurs ihn behandelt hat, ja; die Dateien
müssen trotzdem vollständig sein, und das Bewertungsraster gilt für das Ergebnis
([Der Assistent für Design-Erweiterungen](../../../../../eng-us/help/designers/extensions/design-extension-wizard.md)).

**Welche Stylesheet-Einstellung liest das Pagelayout?** Das hängt vom Pagelayout ab, das die Teilnehmenden schreiben.
Akzeptieren Sie jeden dokumentierten Weg, eine Datei aus dem Ordner `stylesheets/` des Designs ohne `style`-Block zu
laden.

### Stolperfallen

- **`SiteDesign` global gesetzt.** Die Administration verwandelt sich in das Theme. Bewertet unter **Registrierung und
  Geltungsbereich**; beheben Sie es mit
  [Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md).
- **Pfadkommentare eingeschaltet gelassen.** Wird bewertet; am Ende müssen sie ausgeschaltet sein.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Arbeiten Sie im Stammverzeichnis Ihrer eigenen
Schulungsinstallation.

1. Legen Sie `extension/<account>theme/` mit `ezinfo.php`, `extension.xml` (in beiden die Version `0.1.0`), `LICENSE`
   und einer kurzen `README.md` an, die die Zeile `SiteDesign` dokumentiert, die jemand bei der Installation setzen
   muss.
2. Legen Sie in der Erweiterung `design/<account>/templates/pagelayout.tpl` an: eine einfache Seite mit einer
   Kopfzeile, die den Namen der Website zeigt, dem Modulergebnis und einer Fußzeile mit dem Namen Ihres Themes.
3. Legen Sie `design/<account>/stylesheets/theme.css` mit mindestens einem Stil für Kopf- und Fußzeile an und laden
   Sie die Datei aus dem Pagelayout über das Design (kein `<style>`-Block, kein `style=""`).
4. Fügen Sie `settings/design.ini.append.php` mit `DesignExtensions[]=<account>theme` hinzu.
5. Aktivieren Sie die Erweiterung und setzen Sie dann `SiteDesign` nur für den öffentlichen Siteaccess:

   ```bash
   ./console exp:ini set site.ini/DesignSettings/SiteDesign <account> siteaccess:<public siteaccess> --dry-run --allow-root-user
   ```

   Lesen Sie die Ausgabe und führen Sie den Befehl dann ohne `--dry-run` aus. Leeren Sie die INI- und
   Template-Caches.
6. Überschreiben Sie in Ihrem Design `node/view/full.tpl` für die Klasse `article` (Regel in der Datei
   `settings/override.ini.append.php` der Erweiterung), sodass der Titel und die Einleitung mit einer Klasse aus Ihrem
   Stylesheet angezeigt werden. Leeren Sie die Override- und Template-Caches.
7. Prüfen Sie Ihre Templates mit `php bin/php/eztemplatecheck.php -s<public siteaccess> extension/<account>theme/design/
   --allow-root-user`.
8. Schalten Sie die Template-Pfadkommentare für den öffentlichen Siteaccess ein, öffnen Sie einen Artikel und kopieren
   Sie die Zeile `START`, die zeigt, dass Ihr Override auf `node/view/full.tpl` antwortet. Schalten Sie die Kommentare
   wieder aus und leeren Sie den Template-Cache.
9. Führen Sie Folgendes im Stammverzeichnis der Installation aus und bewahren Sie die Ausgabe auf (es sollte nichts
   ausgegeben werden):

   ```bash
   grep -rn -E "<style|style=\"|<script[^>]*>[^<]|on(click|change|load|submit)=" extension/<account>theme/design
   ```

10. Öffnen Sie die Administration und prüfen Sie, dass sie weiterhin ihr eigenes Design verwendet.

## Erwartetes Ergebnis

- Die öffentliche Website zeigt die Kopf- und Fußzeile der Teilnehmenden, gestaltet über `theme.css`; Artikel
  verwenden das Override.
- Die Administration sieht aus wie vorher.
- Die Template-Prüfung meldet die Templates als gültig; das grep gibt nichts aus.
- Die Notizen der Teilnehmenden enthalten die Zeile `START ... (node/view/full.tpl)`; am Ende sind die Kommentare
  ausgeschaltet.
- **Setup > Extensions** listet das Theme mit der Version 0.1.0 auf.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Dateien der Theme-Erweiterung | Fehlen | Nur Design, keine Metadaten | Metadaten vorhanden, LICENSE oder README fehlt | Alle Dateien, Versionen stimmen überein, README dokumentiert `SiteDesign` | Die Dateien, **Setup > Extensions** |
| Registrierung und Geltungsbereich (Pflicht) | Theme nicht gefunden | Gefunden, aber `SiteDesign` global gesetzt | Nur öffentlich, Caches nicht geleert | Registriert, nur öffentlicher Siteaccess, Administration unverändert | Beide Websites, die Einstellungsdateien |
| Pagelayout und Stylesheet | Standardaussehen | Pagelayout, kein Stylesheet | Stylesheet über Inline-Block geladen | Stylesheet aus dem Design geladen, Seite gestaltet | Seitenquelltext |
| Override | Keins | Regel oder Datei fehlt | Gilt für alle Klassen | Nur Artikel, Titel und Einleitung angezeigt | Ein Artikel und ein Ordner |
| Kein Inline-Code | Inline-Styles und -Skripte | Mehrere gefunden | Einer gefunden | Das grep gibt nichts aus | Die Ausgabe des grep |
| Nachverfolgen und Aufräumen | Nicht erledigt | Kommentare eingeschaltet, nicht genutzt | Zeile kopiert, Kommentare eingeschaltet gelassen | Zeile kopiert, Kommentare ausgeschaltet, Template-Prüfung ohne Fehler | Notizen, Seitenquelltext |

**Bestanden**: 12 von 18 Punkten oder mehr und **Registrierung und Geltungsbereich** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Warum dokumentiert die README `SiteDesign`, statt dass das Theme es setzt?
2. Was hätte `DesignExtensions[]=<account>` (der Name des Designs) bewirkt?
3. Was sagt Ihnen die Zeile `START ... (node/view/full.tpl)`?
4. Warum ist es wichtig, dass das grep nichts ausgegeben hat?

<details>
<summary>Antworten</summary>

1. Ein globales `SiteDesign` würde die Administration und jede andere Website verändern
   ([Die Design-Einstellungen, die ein Theme mitliefert](../../../../../eng-us/help/designers/extensions/design-settings-a-theme-ships.md)).
2. Nichts wäre gefunden worden: Der Wert ist der Name der Erweiterung
   ([Das Design registrieren](../../../../../eng-us/help/designers/extensions/registering-the-design.md)).
3. Ihre Datei hat auf die Anfrage nach `node/view/full.tpl` geantwortet
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat)).
4. Eine Website kann eine Content Security Policy einschalten, ohne dass das Theme kaputtgeht
   ([Inline-Skripte und -Styles entfernen](../../../../../eng-us/help/designers/lifecycle/removing-inline-scripts-and-styles.md)).

</details>

## Weiterführende Literatur

- [Die Ordnerstruktur einer Theme-Erweiterung](../../../../../eng-us/help/designers/extensions/extension-folder-structure.md)
- [Die Design-Einstellungen, die ein Theme mitliefert](../../../../../eng-us/help/designers/extensions/design-settings-a-theme-ships.md)
- [Checkliste: Theme-Erweiterung bereit](../../../../../eng-us/help/designers/extensions/checklist-theme-extension-ready.md)

## Verwandte Seiten

- [Lektion 4.3: Theme-Designer, schriftlicher Test](theme-designer-schriftlicher-test.md)
- [Modul 4: Die technischen Kurse prüfen](README.md)
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
