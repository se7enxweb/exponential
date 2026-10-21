---
title: "Lektion 8.5: Content Security Policy"
audience: trainer, admin
category: training
order: 85
summary: Die Teilnehmenden erklären, wogegen eine Content Security Policy schützt, lesen den Richtlinien-Header der Administration und einer Website, sammeln Verstoßberichte in csp.log und stellen auf einer Schulungsinstallation einen Siteaccess von report-only auf enforce und wieder zurück. 40 Minuten.
keywords: website-administration, Content Security Policy, CSP, report-only, enforce, durchsetzen, Mode=auto, Directives, csp.log, csp/report, ReportEndpoint, entwicklerkonsole, XSS, site administration, browser developer console, violation reports
type: help
---
# Lektion 8.5: Content Security Policy

Eine Content Security Policy teilt dem Browser mit, welche Skripte, Stylesheets, Bilder und Verbindungen eine Seite
verwenden darf. Mit einer strengen Richtlinie läuft ein Skript nicht, das über einen gespeicherten oder
zurückgespiegelten Wert in eine Seite eingeschleust wurde, selbst dort, wo ein Template das Maskieren vergisst.
Exponential sendet standardmäßig in der Administration eine Richtlinie im Modus report-only und auf den Websites
keine. Diese Lektion zeigt Administratoren, wie sie die Richtlinie lesen, sammeln, was sie blockieren würde, und sie
sicher durchsetzen.

> **Hinweis**
> Eine durchgesetzte Richtlinie kann Seiten mit Inline-Code kaputt machen. Setzen Sie sie nur auf Ihrer
> Schulungsinstallation durch, jeweils einen Siteaccess, und kennen Sie den Weg zurück, bevor Sie beginnen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 8: Sicherheit](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Website-Administratoren; Entwickler sind willkommen |
| Voraussetzungen | [Lektion 8.1](wie-ein-angreifer-denken.md); [Lektion 7.2](../modul-07-logs-und-das-audit-protokoll/der-log-viewer.md); ein Browser mit Entwicklerwerkzeugen; eine Shell |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Unterschied zwischen `report-only` und `enforce` erklären und was `Mode=auto` bewirkt;
- den Richtlinien-Header einer Seite im Browser oder mit `curl` lesen;
- die Berichte an den eigenen Sammler des Kernels leiten und sie unter **Setup > Logdateien** lesen;
- die Richtlinie auf einem Siteaccess durchsetzen und wieder zurücknehmen;
- eine Direktive pro Siteaccess lockern, wenn sich ein echter Bedarf zeigt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md), die Abschnitte
„Einstellungen“, „Einschalten“ und „Berichte“.

1. **Wofür sie da ist** (3 Minuten). Eine Richtlinie wie `script-src 'self'` ohne `'unsafe-inline'` bedeutet, dass ein
   eingeschleustes `<script>` nicht läuft. Sie ist eine zweite Verteidigungslinie hinter dem Maskieren in Templates.
2. **Die Modi** (3 Minuten). `disabled`; `report-only` (der Browser meldet, was er blockieren würde, und blockiert
   nichts); `enforce` (der Browser blockiert); `auto`, die Voreinstellung: report-only auf Siteaccesses der
   Administration, keine Richtlinie auf allen anderen.
3. **Die Direktiven** (2 Minuten). `site.ini [ContentSecurityPolicy] Directives[]`, eine Direktive pro Eintrag,
   standardmäßig alle `'self'` mit `object-src 'none'`. `AdditionalSources[]` fügt einer Direktive einen Host hinzu,
   ohne den Rest zu wiederholen.
4. **Berichte** (3 Minuten). Mit `ReportEndpoint=enabled` (oder `ReportURI=/csp/report`) schickt der Browser Berichte
   an die View `csp/report` des Kernels, die pro Verstoß eine maskierte Zeile in `var/log/csp.log` schreibt. Der
   Log-Viewer führt sie als **Content-Security-Policy-Berichte**; die **Fehlerübersicht** gruppiert sie nach Direktive.
5. **Der sichere Weg zum Durchsetzen** (4 Minuten). Arbeiten Sie im Modus report-only, sammeln Sie Berichte aus der
   echten Nutzung, setzen Sie zuerst einen Siteaccess durch, lockern Sie nur, was nötig ist, leeren Sie den INI-Cache
   und laden Sie PHP neu. Für den Weg zurück setzen Sie `Mode=report-only` und leeren den INI-Cache erneut.

### Häufige Fragen

**Welche Seiten gehen beim Durchsetzen kaputt?** Templates mit Inline-Code, meist aus Erweiterungen oder eigenen
Designs. Die Funktionsseite beschreibt einen Test, der sie nach Erweiterung auflistet.

**Kann ich einen Kartendienst erlauben?** Ja: `AdditionalSources[]=script-src https://maps.example.com` (mit dem
echten Host), pro Siteaccess, statt `'unsafe-inline'` für alles.

**Funktioniert das klassische Admin-Design mit einer durchgesetzten Richtlinie?** Designs in `ReportOnlyDesigns[]`
(standardmäßig `admin`, `admin2` und `admin_classic`) behalten eine Richtlinie im Modus report-only, auch wenn der
Siteaccess durchsetzt.

### Stolperfallen

- **Alles auf einmal durchsetzen.** Eine kaputte Administration sperrt alle aus, die sie reparieren müssten. Nehmen
  Sie einen Siteaccess, am besten einen zweiten Siteaccess der Administration, den nur Sie verwenden.
- **Den INI-Cache und die Worker vergessen.** Der Header ändert sich erst, wenn jeder PHP-Prozess die neue Einstellung
  liest.
- **Berichte erwarten, ohne die Seiten zu öffnen.** Handler-Attribute werden erst gemeldet, wenn sie ausgeführt
  werden: Öffnen Sie auch Menüs und Dialoge.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert zum Beispiel unter `http://127.0.0.1:8087`; ersetzen
Sie die Adresse und die Namen der Siteaccesses durch Ihre eigenen.

1. Lesen Sie die wirksamen Einstellungen der Richtlinie:

   ```bash
   php bin/php/console exp:ini get site.ini/ContentSecurityPolicy/Mode
   ```

2. Lesen Sie den Header einer Seite der Administration und einer öffentlichen Seite:

   ```bash
   curl -s -D - -o /dev/null http://127.0.0.1:8087/<admin siteaccess>/user/login | grep -i content-security
   curl -s -D - -o /dev/null http://127.0.0.1:8087/ | grep -i content-security
   ```

3. Öffnen Sie die Administration im Browser mit geöffneter Entwicklerkonsole. Achten Sie auf Meldungen mit dem Hinweis
   *Report Only*, während Sie das Dashboard, eine Inhaltsseite und einen Dialog öffnen.
4. Leiten Sie die Berichte für den Siteaccess der Administration an den Sammler des Kernels. Fügen Sie in
   `settings/siteaccess/<admin siteaccess>/site.ini.append.php` Folgendes hinzu (legen Sie zuerst eine Kopie der Datei
   an):

   ```ini
   [ContentSecurityPolicy]
   ReportEndpoint=enabled
   ```

5. Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`); führen Sie auf Velocity ein Deploy aus, wie
   Ihr Trainer es zeigt.
6. Arbeiten Sie fünf Minuten mit der Administration: Inhaltsseiten, der Online-Editor, Menüs und Dialoge.
7. Öffnen Sie **Setup > Logdateien** und dann im Navigator **Content-Security-Policy-Berichte**. Lesen Sie eine Zeile:
   die Direktive, was blockiert wurde, und `disposition=report`.
8. Öffnen Sie die **Fehlerübersicht** und suchen Sie die Gruppe von `csp.log`.
9. Setzen Sie die Richtlinie auf dem Siteaccess der Administration durch: Fügen Sie demselben Block `Mode=enforce`
   hinzu, leeren Sie den INI-Cache und wiederholen Sie Schritt 2. Öffnen Sie einige Seiten und notieren Sie, was nicht
   mehr funktioniert.
10. Nehmen Sie es zurück: Setzen Sie `Mode=report-only` (oder entfernen Sie die Zeile), leeren Sie den INI-Cache und
    prüfen Sie den Header erneut.

## Erwartetes Ergebnis

- Schritt 2: Die Seite der Administration sendet `Content-Security-Policy-Report-Only`; die öffentliche Seite sendet
  mit `Mode=auto` keine Richtlinie.
- Schritt 7 zeigt Zeilen, die mit `csp/<directive>` beginnen und `disposition=report` enthalten, falls etwas
  blockiert worden wäre; gar keine Zeile ist ebenfalls ein gutes Ergebnis.
- Schritt 9: Der Header ist jetzt `Content-Security-Policy`; die Berichte zeigen `disposition=enforce`.
- Schritt 10: Der Header ist wieder report-only.
- Der Trainer prüft, dass alle Teilnehmenden am Ende die Rücknahme durchgeführt haben.

## Quiz

1. Was sendet `Mode=auto` auf einem öffentlichen Siteaccess?
   A. Eine durchgesetzte Richtlinie B. Eine Richtlinie im Modus report-only C. Keine Richtlinie D. Die Richtlinie der
   Administration
2. Wohin schreibt der Kernel die Verstoßberichte, die er sammelt?
3. Was ist das Erste, was Sie vor dem Durchsetzen einer Richtlinie tun?
4. Wie nehmen Sie eine durchgesetzte Richtlinie zurück?
5. Eine Seite braucht Skripte von einem externen Host. Was ist besser, als `'unsafe-inline'` hinzuzufügen?

<details>
<summary>Antworten</summary>

1. **C.** `auto` bedeutet report-only auf Siteaccesses der Administration und ausgeschaltet auf allen anderen
   ([Content Security Policy, Einstellungen](../../../../../eng-us/features/6.0/content-security-policy.md#settings)).
2. `var/log/csp.log`, im Log-Viewer als **Content-Security-Policy-Berichte** aufgeführt.
3. Im Modus report-only arbeiten und die Berichte aus der echten Nutzung sammeln.
4. `Mode=report-only` (oder `disabled`) setzen und den INI-Cache erneut leeren; PHP neu laden oder Velocity per Deploy
   aktualisieren.
5. Den Host mit `AdditionalSources[]` dieser Direktive hinzufügen, nur für diesen Siteaccess.

</details>

## Weiterführende Literatur

- [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md): jede Einstellung, die Berichte und
  das Durchsetzen mit einer Nonce.
- [Kapitel 13, 13.9 Sicherheits-Header und HTTPS](../../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https).
- [Content Security Policy für Hoster](../../../hosting/sicherheit/content-security-policy.md).

## Verwandte Seiten

- [Modul 8: Sicherheit](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
