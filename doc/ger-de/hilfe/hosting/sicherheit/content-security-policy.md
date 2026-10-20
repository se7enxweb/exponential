---
title: Content Security Policy (CSP) für Hoster
audience: hosting, admin
category: hosting
order: 40
summary: Die Content Security Policy, wie ein Hoster ihr begegnet - die mitgelieferte Richtlinie im Berichtsmodus auf Siteaccesses der Administration, der Sammler csp/report und csp.log, wie Sie den Siteaccess eines Kunden auf Durchsetzung umstellen, was meist kaputtgeht (Erweiterungen, TinyMCE 3, externe Skripte), Rückfall und wie Sie das als Dienst anbieten.
keywords: Content Security Policy, CSP, Berichtsmodus, report-only, durchsetzen, enforce, Mode=auto, csp/report, csp.log, ReportEndpoint, ReportURI, Nonce, AdditionalSources, ReportOnlyDesigns, TinyMCE, XSS, Cross-Site-Scripting, Administration, cross-site scripting, administration
type: help
---
# Content Security Policy (CSP) für Hoster

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hoster und für Produktmanager, die erwägen, die
„Durchsetzung der CSP“ als Teil eines verwalteten Tarifs anzubieten. Eine Content Security Policy sagt dem Browser, welche Skripte,
Styles, Bilder und Verbindungen eine Seite nutzen darf; mit einer strengen Richtlinie läuft ein Skript nicht, das über einen Wert
eingeschleust wurde, den ein Template zu maskieren vergaß. Die Referenz ist
[Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md).

## Inhalt

1. [Was mitgeliefert wird](#was-mitgeliefert-wird)
2. [Meldungen sammeln](#meldungen-sammeln)
3. [Einen Siteaccess auf Durchsetzung umstellen](#einen-siteaccess-auf-durchsetzung-umstellen)
4. [Was meist mehr als self braucht](#was-meist-mehr-als-self-braucht)
5. [Rückfall](#rückfall)
6. [Es als Dienst anbieten](#es-als-dienst-anbieten)
7. [Checkliste](#checkliste)

## Was mitgeliefert wird

Die Richtlinie wird aus `site.ini [ContentSecurityPolicy]` gesendet, neben den fünf Sicherheitsheadern aus
`[HTTPHeaderSettings] SecurityHeaders[]`.

| `Mode` | Wirkung |
|---|---|
| `disabled` | keine Richtlinie |
| `report-only` | Header `Content-Security-Policy-Report-Only`: Der Browser meldet, was blockiert würde, und blockiert nichts |
| `enforce` | Header `Content-Security-Policy`: Der Browser blockiert, was die Richtlinie nicht erlaubt |
| `auto` (der Standard) | `report-only` auf Siteaccesses der Administration (die, deren Design mit `admin` beginnt, etwa admin, admin3, admin4, admin4l, adminui), `disabled` auf allen anderen |

Die mitgelieferten Direktiven erlauben nur Dateien der Website selbst: `script-src 'self'` (kein Inline-`<script>`, keine Attribute
`on...=`, keine Links `javascript:`, kein `eval()`), `style-src 'self'`, `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`,
`frame-ancestors 'self'`, mit `data:` und `blob:` für Bilder und Medien. Mit `Nonce=enabled` (der Standard) bekommt jede Anfrage eine
Nonce für `script-src`.

Also **ändert sich in der Auslieferung für einen Besucher nichts**: Die Administration meldet nur, die öffentlichen Websites senden keine
Richtlinie. Durchsetzen ist eine Entscheidung pro Siteaccess, nach einer Prüfung getroffen.

## Meldungen sammeln

Der Kernel sammelt Meldungen selbst. Die Ansicht `csp/report` nimmt, was der Browser sendet, und schreibt pro Verstoß eine Zeile nach
`var/log/csp.log`, im Log-Viewer als **Berichte der Content Security Policy** aufgeführt (Schlüssel `csp`). Richten Sie die Richtlinie pro
Siteaccess darauf:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
ReportEndpoint=enabled
```

- Die Ansicht ist für jeden Besucher offen (Browser senden Meldungen ohne Anmeldung), antwortet mit einem Status und ohne Inhalt und wird
  nie zwischengespeichert.
- Sie ist pro Client (`ReportsPerMinute=60`) und insgesamt (`ReportsPerMinuteTotal=600`) begrenzt; eine Flut wird zu einer Zeile
  `abuse/csp-report-flood` pro Minute in `security.log`.
- Adressen werden wie in den Anfrage-Logs maskiert; die Fehlerübersicht gruppiert `csp.log` nach Direktive und blockierter Quelle.
- `csp.log` wird wie die Anfrage-Logs rotiert: 5 Archive, komprimiert, 10 MB, nach 30 Tagen bereinigt.

Siehe [Meldungen](../../../../eng-us/features/6.0/content-security-policy.md#reports).

## Einen Siteaccess auf Durchsetzung umstellen

Die Schritte aus [Auf Durchsetzung umstellen, Schritt für Schritt](../../../../eng-us/features/6.0/content-security-policy.md#switching-to-enforce-step-by-step),
so wie ein Hoster sie für einen Kunden ausführt:

1. **Prüfen Sie die Templates** der Installation, mit jeder Erweiterung, die sie nutzt:

   ```bash
   CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
   ```

   Jede Zeile mit einem Fehler ist ein Template mit Inline-Code, das eine durchgesetzte Richtlinie kaputtmachen würde.
2. **Sammeln Sie einige Tage lang Meldungen aus echter Nutzung** mit `ReportEndpoint=enabled`: die Seiten jeder Erweiterung, die Editoren,
   die Dialoge und Kontextmenüs.
3. **Setzen Sie zuerst einen Siteaccess durch**, am besten einen zweiten Siteaccess der Administration, den nur der leitende Redakteur des
   Kunden nutzt, mit denselben Designs und Erweiterungen.
4. **Lockern Sie nur, was Sie müssen**, pro Siteaccess und pro Direktive, zum Beispiel einen Host für Karten:

   ```ini
   [ContentSecurityPolicy]
   Mode=enforce
   AdditionalSources[]=script-src https://maps.example.com
   ```

5. **Leeren Sie den INI-Cache** (`php bin/php/ezcache.php --clear-tag=ini`), laden Sie PHP-FPM neu und starten Sie Velocity neu, damit
   jeder Prozess den neuen Header sendet.

Für die öffentlichen Websites beginnen Sie mit `Mode=report-only` in ihrem Siteaccess; ihre Designs und der Rich Text, den sie rendern,
sind vom Test nicht abgedeckt.

> **Tipp**
> `ReportOnlyDirectives[]` sendet eine strengere Richtlinie im Berichtsmodus neben einer durchgesetzten, sodass Sie den Weg zu
> `style-src 'self'` messen können, während die durchgesetzte Richtlinie noch Styles mit `'unsafe-inline'` erlaubt
> ([Durchsetzen: Nonce, mehr Quellen, eine strengere Begleiterin, Rückfall](../../../../eng-us/features/6.0/content-security-policy.md#enforcing-nonce-more-sources-a-stricter-companion-rollback)).

## Was meist mehr als self braucht

| Quelle | Wirkung unter einer durchgesetzten Richtlinie | Ausweg |
|---|---|---|
| TinyMCE 3, die Standard-Engine von ezoe | sein Bearbeitungs-Template und seine Dialoge nutzen Inline-Skripte und -Handler | `Directives[script-src]='self' 'unsafe-inline'` und dasselbe für `style-src` auf diesem Siteaccess, oder wechseln Sie zu TinyMCE 8 |
| TinyMCE 8 | setzt Styles auf seine eigenen Elemente | `'unsafe-inline'` in `style-src` |
| Erweiterungen mit Inline-Einträgen im Kontextmenü | Einträge werden gezeichnet, tun aber nichts, ohne Fehler | wandeln Sie sie in der Erweiterung um, oder behalten Sie `'unsafe-inline'` für diesen Siteaccess |
| Tabellen im Rich Text, Ausrichtung von Zellen, Bildränder aus Inhalten | die Werte `style=""` werden verworfen | `'unsafe-inline'` in `style-src`, wenn das zählt |
| Externe Skripte (Karten, reCAPTCHA, Social Buttons, Werbeserver) | blockiert | fügen Sie ihre Hosts mit `AdditionalSources[]` hinzu |
| Player mit `<object>`/`<embed>`, Inline-PDF | durch `object-src 'none'` blockiert | `Directives[object-src]='self'`, wo nötig |

Die klassischen Designs der Administration bleiben unter `Mode=enforce` über `ReportOnlyDesigns[]` (admin, admin2, admin_classic) im
Berichtsmodus, sodass ein Benutzer, der eines mit dem Umschalter der Oberfläche wählt, eine funktionierende Seite behält. Siehe
[Bevor Sie auf der Administration durchsetzen](../../../../eng-us/features/6.0/content-security-policy.md#before-enforcing-on-the-administration).

## Rückfall

Setzen Sie `Mode=report-only` im Siteaccess, leeren Sie den INI-Cache, laden Sie PHP-FPM neu und starten Sie Velocity neu. Sonst ändert
sich nichts: Dieselbe Richtlinie wird im Berichtsmodus gesendet, und die strengere Begleiterin wird nicht mehr gesendet.

```ini
# settings/siteaccess/<siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=report-only
```

## Es als Dienst anbieten

Ein Hoster kann wählen, Arbeit an der CSP in einen verwalteten Tarif aufzunehmen. Was die Quellen als Umfang stützen:

- Sammeln von Meldungen auf der Administration ab dem ersten Tag (`ReportEndpoint=enabled`), mit Durchsicht der Übersicht im monatlichen
  Betriebsrundgang ([Checkliste für den monatlichen Betrieb](../checklisten/checkliste-fuer-den-monatlichen-betrieb.md));
- ein Projekt zur Durchsetzung pro Website: die Prüfung, ein Zeitraum für Meldungen, zuerst ein Siteaccess, dann der Rest;
- eine Regel, dass Erweiterungen und Designs von Kunden vor dem Ausrollen auf Inline-Code geprüft werden.

Ob und wie Sie dafür abrechnen, entscheidet der Hoster; siehe [Hosting-Tarife gestalten](../geschaeft/hosting-tarife-gestalten.md).

## Checkliste

- [ ] `ReportEndpoint=enabled` auf dem Siteaccess der Administration; `csp.log` erscheint im Log-Viewer.
- [ ] Die Prüfung der Templates mit jeder Erweiterung der Installation ausgeführt.
- [ ] Ein Zeitraum für Meldungen mit echter Redaktionsarbeit, einschließlich Dialogen und Kontextmenüs.
- [ ] Zuerst ein Siteaccess durchgesetzt; Erweiterungen und Editoren durch Nutzung geprüft.
- [ ] Ausnahmen nur pro Siteaccess und Direktive hinzugefügt und aufgeschrieben.
- [ ] Rückfall getestet: `Mode=report-only`, INI-Cache geleert, PHP-FPM neu geladen, Velocity neu gestartet.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Content Security Policy (Referenz der Funktion)](../../../../eng-us/features/6.0/content-security-policy.md)
- [Sicherheitshärtung: Cross-Site-Scripting und die Content Security Policy](../../sicherheitshaertung.md#cross-site-scripting-xss-und-die-content-security-policy)
- [Sicherheitslog](../betrieb/sicherheitslog.md)
- [Härtung für Hoster](haertung-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
