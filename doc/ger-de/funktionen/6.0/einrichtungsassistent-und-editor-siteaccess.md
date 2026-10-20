# Das neue Aussehen des Einrichtungsassistenten und der Editor-Siteaccess

Diese Seite ist für alle, die Exponential installieren, und für Administratoren, die für ihre Redakteure eine einfachere
Administrationsoberfläche möchten. Am 01.10.2026 und 02.10.2026 erhielt der Einrichtungsassistent ein neues Aussehen, und
er installiert jetzt **drei** Siteaccesses statt zwei: die Website, den Admin und den **Editor**, eine
Administrationsoberfläche für Menschen, die Inhalte bearbeiten, die Website aber nicht administrieren.

## Die drei Siteaccesses

| Siteaccess | Host-Konvention | Für | Design |
|---|---|---|---|
| site | `yourdomain.com` | Besucher | Ihr Website-Design |
| admin | `admin.yourdomain.com` | Administratoren | admin4 (oder das gewählte Admin-Design) |
| editor | `edit.yourdomain.com` | Redakteure | `editor`, das auf `admin4l`, dann `admin4`, `admin3`, `admin2`, `admin` zurückgreift |

Der Editor-Siteaccess (`settings/siteaccess/editor`) wird aus dem installierten Admin-Siteaccess erzeugt. Verglichen mit
dem Admin:

- sein `SiteName` ist „Editor“, und er hat eine eigene `SiteURL`;
- er trägt die Extension-Einstellungen des Admins; `ExtensionSettingsSiteAccess=admin` lässt die Admin-Einstellungen der
  Extensions auch dort gelten;
- er zeigt nur die Inhaltsreiter: Dashboard, Inhaltsstruktur, Medien, Benutzer, Shop, Tags, Newsletter;
- er hat keine Entwickler-Werkzeugleiste und keinen Knotenreiter Layouts;
- die Module hinter den ausgeblendeten Reitern antworten mit 404: `setup`, `visual`, `explayouts_ui`,
  `explayouts_ui_api`, `git_manager`, `xrowextract`, `bccie`, `syndication`, `audit`, `oauthadmin`, `class`, `role`,
  `section`, `state`, `workflow`, `trigger`, `package` und `rss` (Administration der Website, nicht Bearbeitung; das
  Sperren von `state` sperrt auch `state/assign`, sodass Redakteure Objektzustände im Admin setzen, und `role` hält auch
  Rollenzuweisungen im Admin);
- der Service Worker der Website bleibt draußen, wie beim Admin (`/editor` kommt zu `/admin` hinzu);
- der Assistent gibt ihm eine rechte Seitenleiste, die mit Lesezeichen beginnt.

`design/editor` hat keine eigenen Templates. Es ist das Design des Editor-Siteaccess, und ein README beschreibt, was es
zur Administrationsoberfläche eines Redakteurs macht. Legen Sie dort nur Templates ab, wo der Editor anders aussehen
muss.

## Mit dem Assistenten installieren

1. **Schritt Siteaccess**: Der Assistent nennt die drei Siteaccesses und die Hostnamen-Konvention.
2. **Schritt Website-Details**: Füllen Sie das Pflichtfeld für Pfad, Port oder Hostname des Editors aus. Es ist mit
   `editor`, `8082` oder `edit.<host>` vorausgefüllt und wird wie das Admin-Feld geprüft. Das Feld Datenbank listet nur
   SQLite-Datenbankdateien aus `var/storage/sqlite3` auf (eine Datei mit dem Kopf „SQLite format 3“ oder eine leere
   Datei) statt jeder Datei, SQL-Dumps eingeschlossen.
3. **Abschlussseite**: Sie sehen die Benutzer-, Admin- und Editor-Website, jede mit ihrer Adresse, und das
   Multisite-Bild von Exponential.

Das Aussehen: eine weiße Karte auf einer dunklen schiefergrauen Seite mit einem sanften orangefarbenen Schimmer
(`setup3.css`, nach `setup.css` und `setup2.css` geladen, nur Überschreibungen), der Fortschrittsbalken als schmaler
orangefarbener Balken unter dem Logo, Hilfe und Zusammenfassung in einem Seitenbereich. Die Fußzeile lautet „Exponential
copyright 1998-2026 7x & Exponential Foundation“, und die Links zeigen auf exponential.earth.

Korrekturen derselben Änderung:

- Der Assistent protokolliert nicht mehr auf jeder Seite „Language 'eng-GB' does not exist or is not used!“ (nur der
  Setup-Siteaccess).
- Das Aufwärmen von Velocity nimmt nie den Lease des Assistenten. Mit `CheckValidity=true` zählte jede Ausgabe beim
  Aufwärmen als erste Seite des Assistenten und sperrte nach einem Neustart eine halbe Stunde lang alle mit „The site is
  being set up“ aus.

## Mit dem Kickstarter installieren

`kickstart.ini` benennt den Editor wie den Admin-Siteaccess. `kickstart.ini-dist` trägt die drei Einstellungen,
auskommentiert, nach denen des Admins.

| Datei | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `kickstart.ini` | `EditorAccess` | `editor` (`<Access>_editor`, wenn `editor` belegt ist) | der Name des Siteaccess |
| `kickstart.ini` | `EditorAccessPort` | `AccessPort` + 2 (`8082` für `8080`), der nächste freie Port, wenn dieser belegt ist | Zugangsmethode Port |
| `kickstart.ini` | `EditorAccessHostname` | `edit.<AccessHostname without www.>` (`edit.example.com` für `www.example.com`) | Zugangsmethode Host |

Der Standard ist nie gleich dem Wert der Website oder des Admins: Zwei Siteaccesses auf einem Port oder Host würden einen
von ihnen unerreichbar machen. `exp:install` nimmt `--editor-access`, `--editor-port` und `--editor-host` an und lehnt
einen Wert ab, der kollidiert.

## Einer bestehenden Installation einen Editor-Siteaccess hinzufügen

Für eine früher erstellte Installation ändert sich nichts. Um einen Editor-Siteaccess zu erhalten:

1. Kopieren Sie `settings/siteaccess/editor` und `design/editor` aus einer frischen Installation.
2. Setzen Sie seinen `SiteName`, seine `SiteURL` und sein `SiteDesign`. Die Design-Liste in der `site.ini.append.php`
   des Editors (`AdditionalSiteDesignList[]`) nennt `admin4l`, `admin4`, `admin3`, `admin2`, `admin`.
3. Ordnen Sie ihn wie den Admin-Siteaccess in `site.ini`, Block `[SiteAccessSettings]`, zu.
4. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.

## Verwandte Seiten

- [Ausgeblendete Admin-Reiter](ausgeblendete-admin-reiter.md), [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md), [das Design admin4](../../../eng-us/features/6.0/admin4-design.md)
- [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md), [Kickstarter-CLI](kickstarter-cli.md), [Referenz der Kickstarter-CLI](../../../eng-us/bc/6.0/kickstartercli.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Oktober 2026](../../../eng-us/history/2026/2026-10.md), [Januar 2024, zweite Hälfte](../../../eng-us/history/2024/2024-01b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
