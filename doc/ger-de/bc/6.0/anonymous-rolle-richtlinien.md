# Hinweis zum Aktualisieren: die öffentlichen Richtlinien der Rolle Anonymous

Ein Besucher, der nicht angemeldet ist, ist der anonyme Benutzer und hat die Richtlinien der Rolle **Anonymous**. Dieser
Hinweis sagt, welche Richtlinien eine öffentliche Website dieser Rolle gibt, warum eine neue Installation jede
Registrierung ablehnen konnte und wie Sie die Rolle einer bestehenden Installation vervollständigen.

## Was falsch war

Seit dem 12. Oktober 2026 schreibt eine neue Installation `PolicyOmitListExclude[]=user/register` (site.ini
`[RoleSettings]`, in `settings/override/site.ini.append.php`): Die Registrierung steht nicht mehr jedem offen, sondern
braucht die Richtlinie **User / Register**. Die Grunddaten gaben der Rolle Anonymous diese Richtlinie erst ab dem
19. Oktober 2026, und keines der Website-Pakete (`sevenx_multisite`, `sevenx_site`, `cjw_multisite`, die Pakete
ezwebin, ezflow und ezdemo, `plain_site` und ihre `_clean`-Varianten) gab sie selbst. Eine Installation, die dazwischen
angelegt wurde, lehnt jede Registrierung ab (`user/register` antwortet mit der Anmeldeseite), und ihre Anmeldeseiten
zeigen keinen Link "Registrieren".

Eine Installation, die von einer älteren Version aktualisiert wurde, ist nicht betroffen, solange ihre Einstellungen
`user/register` nicht aus `PolicyOmitList[]` ausnehmen: Die Registrierung ist dann über diese Liste offen, mit oder
ohne Richtlinie.

## Was sich ändert

- **Der Setup-Assistent und der Kickstarter** prüfen nach den Rollen des Website-Pakets, dass die Rolle Anonymous
  `user/register` hat, und fügen es hinzu, wenn es fehlt (`expAnonymousPublicPolicies::ensure()` in
  `kernel/setup/steps/ezstep_create_sites.php`). Das gilt für jedes Website-Paket und alle Grunddaten, auf jedem
  Datenbanksystem.
- **Die Website-Pakete** `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_site`, `sevenx_site_clean` und
  `cjw_multisite` geben `user/register` selbst, wenn es der Rolle fehlt, und, weil sie cjw_newsletter installieren,
  `newsletter/subscribe`, `newsletter/configure`, `newsletter/unsubscribe` und `newsletter/archive`: Das
  Anmeldeformular, die beiden Links jeder Newsletter-Mail und die Webversion eines versandten Newsletters werden von
  Besuchern geöffnet, die nicht angemeldet sind. Eine Richtlinie, die die Rolle schon hat, wird nie ein zweites Mal
  hinzugefügt.
- **`bin/php/addanonymouspublicpolicies.php`** vervollständigt die Rolle bei einer bestehenden Installation (unten).

## Die Richtlinien, die eine öffentliche Website der Rolle Anonymous gibt

| Richtlinie | Warum | Woher sie kommt |
| --- | --- | --- |
| content / read, Sektion Standard | die öffentlichen Inhalte lesen; content/view, content/search, content/advancedsearch und content/download geben die Funktion *read* an, Suche, erweiterte Suche und das Herunterladen einer öffentlichen Datei brauchen also nichts weiter | Grunddaten |
| content / pdf, Sektion Standard | die PDF-Ansicht öffentlicher Inhalte | Grunddaten |
| user / login, SiteAccess = die öffentlichen Siteaccesses | Anmelden auf der Website (nicht in der Administration); auch user/activate, das *login* angibt | Website-Paket |
| user / register | Registrieren (`user/register`) und die Bestätigungsseite (`user/success`, das *register* angibt) bei einer neuen Installation, die `user/register` aus `PolicyOmitList[]` herausnimmt | Grunddaten ab 6.0.15, Setup-Schritt, Website-Pakete, Aktualisierungsskript |
| rss / feed | die RSS-Feeds `rss/feed/<name>` | Website-Pakete, Aktualisierungsskript |
| tags / view (eztags) | die Tag-Seiten, auf die Artikel- und Rezeptseiten verlinken | Website-Pakete, Aktualisierungsskript |
| info-collection / read | das Lead-Formular der Website Bold | Pakete `sevenx_*` und `cjw_multisite` |
| content / read, Sektion Media | Bilder und Dateien, die in öffentliche Seiten eingebettet sind | Website-Pakete (Klassenliste oder Sektion, je nach Paket) |
| ezjscore / call, FunctionList ezstarrating_rate, ezstarrating_user_has_rated, loadMore | Bewerten und das "Mehr laden" der Layout-Listen | Website-Pakete |
| newsletter / subscribe, configure, unsubscribe, archive (cjw_newsletter) | siehe oben | Pakete `sevenx_*` und `cjw_multisite`, Aktualisierungsskript |
| osm_proxy / tiles (cjw_themes_jumper) | die Kartenkacheln des CJW-Designs | `cjw_multisite` |

Als Richtlinie nicht nötig: `user/forgotpassword`, `user/activate`, `user/success` und `user/login` stehen in
`PolicyOmitList[]`; `oauth/authorize` prüft seine Richtlinie selbst, nachdem sich der Besucher angemeldet hat. Nicht für
Besucher: `ezinfo` (von den Website-Paketen auf öffentlichen Siteaccesses abgeschaltet).

## Was beim Aktualisieren zu tun ist

Führen Sie das Skript zuerst mit `--dry-run` aus. Es listet jede Richtlinie, die es hinzufügen würde, und den Grund,
warum eine wegfällt (die Rolle hat sie schon, in irgendeiner Form, auch begrenzt oder als `module/*`; oder das Modul ist
nicht installiert):

```bash
php bin/php/addanonymouspublicpolicies.php --dry-run
php bin/php/addanonymouspublicpolicies.php
```

Es fügt nur hinzu, was fehlt, ohne Begrenzungen, macht den Rollen-Cache ungültig, prüft, was gespeichert wurde, und gibt
`PASS` oder `FAIL` aus. Ein zweiter Lauf fügt nichts hinzu.

- `--skip=user/register` hält die Registrierung auf einer Website geschlossen, die die Richtlinie absichtlich entfernt
  hat (das Skript kann eine entfernte Richtlinie nicht von einer unterscheiden, die nie da war).
- `--only=user/register` fügt nur diese Richtlinie hinzu.
- `--role=<Name oder ID>` vervollständigt eine andere Rolle als Anonymous.

Das Skript ändert nie `PolicyOmitList[]` oder `PolicyOmitListExclude[]` und berührt die anderen Richtlinien der Rolle
nie.

Um die Registrierung bei einer neuen Installation zu schließen, entfernen Sie *User / Register* aus der Rolle Anonymous.
Um sie bei einer aktualisierten Installation zu schließen, fügen Sie `PolicyOmitListExclude[]=user/register` zu
`[RoleSettings]` in `settings/override/site.ini.append.php` hinzu und geben Sie der Rolle die Richtlinie nicht.

Code: `kernel/classes/expanonymouspublicpolicies.php`, Befehl
`kernel/private/classes/commands/addanonymouspublicpolicies.php`. Test ohne Datenbank:
`tests/tests/kernel/classes/expAnonymousPublicPoliciesTest.php`.
