# Hilfe als Inhalt gepflegt: Prüfablauf, "War das hilfreich?", Token-Clients, EPUB-Prüfung, Historie, öffentliche Seiten (6.0.15)

Die Teile des Hilfe-Plans ([help-system.md](../../spezifikationen/6.0/hilfesystem.md)) und seines Styleguides, die nach
[dem DITA-Export, den gespeicherten E-Books und den überfälligen Prüfungen](hilfeinhalte-dita-ebooks-reviews.md) noch
offen oder nur eine Absprache waren, sind in 6.0.15 enthalten. Diese Seite beschreibt, was sich für eine Site ändert, die
[Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md) verwendet, und was nach dem Deploy zu tun ist.

## Was sich ändert

| Teil | Was er tut | Wo |
|---|---|---|
| Wer schreibt, gibt nicht frei | der Status Veröffentlicht wird aus einer Zuweisung genommen, wenn der aktuelle Benutzer die aktuelle Version des Hilfeobjekts angelegt hat | jede Statuszuweisung (`eZContentOperationCollection::updateObjectState()`) |
| Prüfablauf am Thema | die Karte **Prüfung und Freigabe** unter einem Thema: die Schritte, die die Rollen des Benutzers erlauben, als Schaltflächen, die Prüfung des Themas nach dem Styleguide, seine Antworten auf "War das hilfreich?" | `exphelp/topic/<help_id>` (admin, admin4), die POST-Ansicht `exphelp/state` |
| Veröffentlichte help_ids | jede help_id wird erfasst, wenn ihr Thema veröffentlicht wird; der Validator meldet eine Umbenennung (`help-id.renamed`), eine Wiederverwendung (`help-id.reused`) und eine veröffentlichte help_id ohne Thema (`help-id.gone`) | Tabelle `exphelp_help_id`, `check --fix` |
| Weitere Regeln des Styleguides | `review-date.too-far`, `version.successor`, `verb.mix` (WARNING), `language.sentence-too-long`, `language.paragraph-too-long` (NOTICE) | `./console exp:help:content check` |
| "War das hilfreich?" | Ja, oder Nein mit Grund, je Thema und Tag, nichts darüber, wer geantwortet hat | Tabelle `exphelp_feedback`, `exphelp/feedback`, `./console exp:help:content feedback` |
| Kontexthilfe für Token-Clients | `/api/help/v1/topic/<help_id>` und `/manual/<remote_id>` mit einem OAuth-Token oder einem persönlichen API-Schlüssel mit dem neuen Scope `help` | REST-Provider `help` |
| Prüfung eines EPUB | epubcheck und Ace by DAISY für jedes EPUB, wo die Site ihre Befehle nennt | `EpubCheckCommand`, `AceCommand`, `BookChecks`; `export --check` |
| Historie in git | der DITA-Export, in ein Git-Repository geschrieben und bei jedem Lauf mit Änderungen committet | Cronjob-Teil `exphelp`, `DitaHistoryDirectory`; `dita --commit` |
| Öffentliche Seiten | die Voll- und Zeilenansichten der Hilfeklassen auf der öffentlichen Site | Extension `exphelpsite` (je öffentlichem Siteaccess) |

Nichts hiervon ändert eine Site, die Hilfe als Inhalt nicht installiert hat: Die Statusoperation kehrt bei Objekten,
die keine Hilfe sind, sofort zurück, die Themenseite und die Rückmeldeansicht sind nicht erreichbar, der Cronjob-Teil
meldet, dass er übersprungen wird.

## Verhaltensänderungen

- **Wer ein Hilfeobjekt geschrieben hat, kann es nicht mehr freigeben**, auch nicht im Reiter Objektstatus. Ein Benutzer
  mit der Rolle Help release, der auch die aktuelle Version eines Themas geschrieben hat, sieht keine Schaltfläche
  Freigeben, und der Status wird nicht zugewiesen (die übrigen Status derselben Zuweisung schon). Eine Site mit nur einem
  Redakteur setzt `exphelp.ini [ContentSettings] ReleaseByAuthor=enabled`. Die Startdaten der Kommandozeile
  (`seed-topics --release`, `build-manuals --release`) sind nicht eingeschränkt.
- **Die Rolle Help editor erhält `exphelp/context`** mit dem nächsten Rollenschritt (`exp:help:content roles` oder
  `install`): Der Link "JSON" auf der Seite eines Themas funktioniert für Hilfe-Redakteure, und ein Hilfe-Redakteur kann
  einen API-Schlüssel mit dem Scope `help` anlegen.
- **`exphelp/feedback` ist eine neue Richtlinienfunktion.** Leser mit `exphelp/read` dürfen bereits antworten; Anonymous
  erhält sie nur mit `AnonymousFeedback=enabled` und dem Rollenschritt.
- **Der Validator meldet mehr.** `check` kann jetzt WARNINGs und NOTICEs ausgeben, die er vorher nicht ausgab
  (Prüfdaten mehr als 12 Monate entfernt, oder 6 für die Bereiche privacy und account; lange Sätze in Themen für
  Besucher und Mitglieder; eine Mischung aus "click" und "tap"). Keine davon ist ein ERROR, `check` besteht also weiter,
  außer `help-id.renamed` und `help-id.reused`, die erst erscheinen, nachdem help_ids erfasst wurden.
- **Die REST-API hat einen Provider `help`** (`rest.ini [ApiProvider] ProviderClass[help]`) und einen Scope `help` für
  API-Schlüssel. Eine Site, die ihre eigenen Provider in einer Überschreibung aufführt, behält sie; der Scope wird auf der
  Seite der API-Schlüssel Benutzern angeboten, die `exphelp/context` haben.
- **Der Cronjob-Teil `infrequent` führt `exphelp.php` aus.** Er entfernt Antworten, die älter als `FeedbackKeepDays`
  (730) sind, und schreibt und committet den DITA-Export nur, wenn `DitaHistoryDirectory` gesetzt ist.

## Neue Einstellungen

`exphelp.ini [ContentSettings]`: `ReleaseByAuthor` (disabled), `SentenceWordsPublic` (20), `SentenceWordsTechnical` (25),
`ParagraphSentencesPublic` (4), `PlainLanguageAudiences[]` (Visitors, Members), `ReviewMonths` (12), `ShortReviewMonths`
(6), `ShortReviewAreas[]` (privacy, account), `Feedback` (enabled), `FeedbackKeepDays` (730), `AnonymousFeedback`
(disabled), `EpubCheckCommand`, `AceCommand` (leer), `BookChecks` (report), `DitaHistoryDirectory` (leer),
`DitaHistoryLogin` (admin), `DitaHistoryAuthor` (leer). `rest.ini`: `[ApiProvider] ProviderClass[help]`,
`[ApiKeySettings] Scopes[help]`, `ScopeNames[help]`, `RouteScopes[expHelpRestController_topic]`,
`RouteScopes[expHelpRestController_manual]`, `[expHelpRestController_CacheSettings] ApplicationCache=disabled`.
`cronjob.ini`: `exphelp.php` in `[CronjobPart-infrequent]`, `[CronjobPart-exphelp]`.

## Datenbank

Zwei neue Tabellen, `exphelp_help_id` und `exphelp_feedback`, in den Kernel-Schemas von MySQL, PostgreSQL und SQLite und
in `share/db_schema.dba`. Eine bestehende Installation erhält sie über die Datenbankaktualisierung
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, mit `IF NOT EXISTS` geschrieben) oder, auf jeder Engine
(auch Oracle und MongoDB), über den neuen Schritt `tables` des Befehls. Ohne sie ist "War das hilfreich?" aus, und keine
help_id wird erfasst; alles andere funktioniert.

## Nach dem Deploy

```
php bin/php/ezpgenerateautoloads.php -k        # oder: ./console exp:velocity deploy --kernel
./console exp:help:content install -s <Admin-Siteaccess> --dry-run
./console exp:help:content install --apply -s <Admin-Siteaccess>     # Tabellen, exphelp/context für Redakteure
./console exp:help:content install --apply -s <Admin-Siteaccess>     # No change.
./console exp:help:content check --apply -s <Admin-Siteaccess> --fix # erfasst die help_ids der veröffentlichten Themen
```

Ein Server, der PHP am Laufen hält (PHP-FPM, Exponential Velocity), muss die geänderte Kernel-Datei
`kernel/content/ezcontentoperationcollection.php` laden; leeren Sie den INI-Cache (`--clear-tag=ini`) für die neuen
Einstellungen und den REST-Provider. Für öffentliche Hilfeseiten fügen Sie `ActiveAccessExtensions[]=exphelpsite` nur dem
öffentlichen Siteaccess hinzu und leeren den Cache der Template-Überschreibungen. Die Texte der Oberfläche stehen in den
24 Übersetzungskatalogen mit dem Kontext der Hilfe (`kernel/exphelp`).
