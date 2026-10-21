# LoveStack-Funktionen in Exponential 6

LoveStack ist die Distribution von Mugo Web des ursprünglich von eZ Systems veröffentlichten Legacy-Kernels
(`mugoweb/ezpublish-legacy` auf GitHub und Packagist). Im Lauf der Jahre hat Mugo Web ihr eine Reihe wertvoller
Funktionen und Korrekturen hinzugefügt. Exponential 6.0.15 übernimmt 36 davon in die Exponential-Distribution, jede in
den eigenen APIs von Exponential neu gebaut, dokumentiert und durch Tests abgedeckt, sodass jede Exponential-Website
sie zusammen mit den eigenen Funktionen von Exponential verwenden kann. Mit Dank an Mugo Web, auf dessen Arbeit dies
aufbaut.

Diese Seite ist der Überblick: was jede Funktion tut, für wen sie gedacht ist, wie Sie sie einschalten, ihre
Einstellungen und Standardwerte, was sich bei einem Upgrade und bei einer neuen Installation ändert, der Test, der sie
abdeckt, und der LoveStack-Commit, aus dem sie stammt. Die ausführlichen Seiten sind unter
[Weitere Dokumente](#weitere-dokumente) aufgeführt.

**In einem Satz:** Auf einer vorhandenen Website ändert sich nichts, außer Sie schalten es ein; Ausnahmen sind reine
Fehlerkorrekturen, einige neue Warnungen und Log-Einstellungen sowie die Sicherheitsprüfungen beim Abmelden (L03) und
für hochgeladene Bilder (L05, L06).

**Wenn Sie Exponential betreiben und LoveStack nie betrieben haben**, ist nichts zu tun: Die Funktionen sind in
6.0.15 enthalten, und diejenigen, die das Verhalten einer Website ändern könnten, sind ausgeschaltet, bis Sie sie
einschalten. **Wenn Sie LoveStack betreiben**, siehe [Eine Website von LoveStack umziehen](#eine-website-von-lovestack-umziehen).

---

## Quellenangabe (woher die Funktionen stammen, und wann)

Die in Exponential übernommenen LoveStack-Funktionen wurden aus dem Repository **mugoweb/ezpublish-legacy** von Mugo
Web genommen, wie es am Tag der Portierung stand, dem **12. Oktober 2026**. Jede Funktion, jeder Test und jeder
Vergleich in diesem Dokument bezieht sich auf genau diesen Commit, sodass die Arbeit jederzeit nachvollzogen und gegen
dieselbe Quelle geprüft werden kann.

| | |
|---|---|
| Repository | <https://github.com/mugoweb/ezpublish-legacy> (Standard-Branch `master`, nicht archiviert) |
| Für die Portierung verwendeter Commit | [`e61e9fdf0ca464197b79935ef784ed459721aa87`](https://github.com/mugoweb/ezpublish-legacy/commit/e61e9fdf0ca464197b79935ef784ed459721aa87), "Merge pull request #238 from mugoweb/php8-fix" |
| Quellbaum bei diesem Commit | <https://github.com/mugoweb/ezpublish-legacy/tree/e61e9fdf0ca464197b79935ef784ed459721aa87> |
| Datum dieses Commits | 23. Februar 2026, 08:49:45 UTC (bei der Prüfung am 12. Oktober 2026 weiterhin der neueste Commit auf `master`) |
| Damals neuestes Release | [`v2025.03.1-1`](https://github.com/mugoweb/ezpublish-legacy/releases/tag/v2025.03.1-1), 1. März 2025 |
| Gemeinsamer Vorfahre mit Exponential | [`3429aa91bba018f518fcb41757c9d627a0a73c0f`](https://github.com/mugoweb/ezpublish-legacy/commit/3429aa91bba018f518fcb41757c9d627a0a73c0f), 9. März 2016 (Upstream ezsystems/ezpublish-legacy) |
| Verglichene Arbeit von Mugo Web | `3429aa91bb..e61e9fdf0c`: [Vergleichsansicht](https://github.com/mugoweb/ezpublish-legacy/compare/3429aa91bba018f518fcb41757c9d627a0a73c0f...e61e9fdf0ca464197b79935ef784ed459721aa87) |
| Zeit seit der letzten Änderung, zum Zeitpunkt der Portierung | 231 Tage (7 Monate und 19 Tage) seit dem letzten Commit; 590 Tage (über 19 Monate) seit dem letzten Release |

Zum Nachvollziehen: `git clone https://github.com/mugoweb/ezpublish-legacy && git -C ezpublish-legacy checkout e61e9fdf0ca464197b79935ef784ed459721aa87`.
Die für jede Funktion (L01–L36) unten genannten Commit-IDs sind Commits in diesem Repository und öffnen sich als
`https://github.com/mugoweb/ezpublish-legacy/commit/<id>`.

---

## Die Funktionen in einfachen Worten

Was die 36 Funktionen sind, gruppiert danach, was sie nach dem Upgrade mit einer vorhandenen Website tun.

### Für jede Website eingeschaltet: Korrekturen und Sicherheitsprüfungen

| | Funktion | Was Sie sehen würden |
|---|---|---|
| [L03](#l03--das-abmelden-beendet-die-session-und-ihr-cookie) | Das Abmelden beendet die Session wirklich | nach dem Abmelden sind die Session und ihr Cookie weg, statt nur eine neue ID zu erhalten |
| [L05](#l05--hochgeladene-bilder-werden-anhand-ihres-inhalts-geprüft) | Hochgeladene Bilder werden anhand ihres Inhalts geprüft | eine Datei, die kein aufgeführter Bildtyp ist (eine umbenannte Datei, HEIC, SVG), wird abgelehnt |
| [L06](#l06--vorschaubilder-von-paketen-werden-ebenso-geprüft) | Vorschaubilder von Paketen werden ebenso geprüft | ein gefälschtes Bild als Vorschaubild eines Pakets oder Bild eines Styles wird abgelehnt |
| [L11](#l11--datenbankfehler-tragen-die-fehlernummer-des-treibers) | Datenbankfehler tragen die Fehlernummer des Treibers | Entwickler können im Code „doppelter Schlüssel“ von „Tabelle fehlt“ unterscheiden |
| [L12](#l12--eine-warnung-wenn-eine-änderung-jeden-view-cache-leert) | Eine Warnung, wenn eine Änderung jeden View-Cache leert | eine Log-Zeile, die sagt, dass eine große Änderung alle View-Caches geleert hat |
| [L20](#l20--das-fenster-orte-lässt-einen-versteckten-ort-versteckt) | Das Fenster Orte im Bearbeitungsformular erzwingt nicht mehr „Sichtbar“ | ein versteckter Ort bleibt versteckt, wenn Sie einen weiteren hinzufügen |
| [L21](#l21--auswahl--und-matrixfelder-ohne-überzählige-zeilenumbrüche) | Keine überzähligen Zeilenumbrüche in Auswahl- und Matrixfeldern | sauberere Ausgabe |
| [L24](#l24--der-dialog-subsup-schließt-bei-ok) | Der Dialog für Tief- und Hochstellung schließt, wenn Sie OK drücken | war in Exponential bereits so |
| [L31](#l31--templates-können-layout-ansichten-unterscheiden) | Templates können Layout-Ansichten wieder unterscheiden | Fehlerkorrektur für Designer |
| [L34](#l34--sehr-große-zahlen-halten-den-suchindex-nicht-mehr-an) | Sehr große Zahlen halten den Suchindex nicht mehr an | Fehlerkorrektur |
| [L35](#l35--relationen-verknüpfter-objekte-bleiben-aus-dem-suchindex-heraus) | Relationen verknüpfter Objekte bleiben aus dem Suchindex heraus | war in Exponential bereits so |
| [L36](#l36--der-cronjob-hide-und-der-suchindex) | Das Verstecken von Inhalten per Cronjob aktualisiert auch den Suchindex | versteckte Inhalte erscheinen nicht mehr in der Suche |
| [PHP 8](#php-8-prüfung-und-weitere-korrekturen) | PHP-8-Korrekturen aus LoveStack, nachgestellt und korrigiert, wo noch nötig | weniger Deprecations und Fehler unter PHP 8.1 bis 8.5 |

### Für jede Website eingeschaltet: kleine sichtbare Änderungen

| | Funktion | Was Sie sehen würden |
|---|---|---|
| [L07](#l07--php-deprecations-erhalten-ein-eigenes-log) | PHP-Deprecations erhalten eine eigene Debug-Stufe und ein eigenes Log | `var/log/deprecated.log`; die Debug-Leiste zählt sie, statt sie aufzulisten |
| [L08](#l08--größe-der-log-dateien-in-siteini) | Die Größe der Log-Dateien ist eine Einstellung, standardmäßig 2 MB | weniger, längere Log-Dateien (bisher 200 KB) |
| [L09](#l09--eine-warnung-für-einen-operator-parameter-ohne-standardwert) | Eine Warnung, wenn ein Parameter eines Template-Operators keinen Standardwert hat | für Template-Entwickler |
| [L15](#l15--eine-schaltfläche-kopieren-in-der-vollständigen-admin-ansicht) | Eine Schaltfläche Kopieren in der vollständigen Admin-Ansicht | bisher gab es Kopieren nur im Kontextmenü |
| [L27](#l27--die-template-operatoren-rawurlencode-und-rawurldecode) | Die Template-Operatoren `rawurlencode` und `rawurldecode` | für Template-Entwickler |
| [SameSite](#die-session-einstellung-samesite-von-lovestack-wird-ebenfalls-gelesen) | Die Einstellung `[Session] SameSite` von LoveStack wird ebenfalls gelesen | für LoveStack geschriebene Einstellungen funktionieren weiter |

### Neue Optionen, ausgeschaltet, bis Sie sie einschalten

| | Funktion | Was Sie sehen würden |
|---|---|---|
| [L10](#l10--bearbeitungslinks-in-der-liste-der-verwendeten-templates) | Ein Format für Bearbeitungslinks in der Liste der verwendeten Templates der Debug-Ausgabe | für Template-Entwickler |
| [L13](#l13--audit-einträge-für-änderungen-an-orten) | Audit-Einträge für das Hinzufügen eines Orts und das Ändern des Hauptorts | neue Einträge in der Audit-Konsole (das Entfernen eines Orts wird bereits verzeichnet) |
| [L14](#l14--site-daten-in-templates-und-in-der-administration) | Site-Daten in Templates lesbar, dazu eine Liste in der Administration | für Entwickler und Administratoren |
| [L16](#l16--pflicht-alternativtext-für-bildfelder) | „Alternativtext ist erforderlich“ für ein Bildfeld, pro Klasse | Redakteure können ein Bild ohne Alternativtext nicht speichern, wo es eingeschaltet ist |
| [L17](#l17--den-gespeicherten-wert-von-textzeilenfeldern-trimmen) | „Gespeicherten Wert trimmen“ für Textzeilenfelder, pro Klasse | führende und nachgestellte Leerzeichen werden beim Speichern entfernt |
| [L18](#l18--von-einer-php-klasse-erzeugte-namen) | Ihre eigene PHP-Klasse kann den Namen oder URL-Alias eines Objekts erzeugen | für Entwickler |
| [L19](#l19--standard-des-klassenfilters-für-fetches) | Klassenfilter können standardmäßig „include“ sein | ändert die Ergebnisse von Fetches, daher standardmäßig aus |
| [L22](#l22--versteckte-custom-tags) | Versteckte Custom Tags | Custom Tags, die existieren, aber nicht in der Liste des Editors angeboten werden |
| [L29](#l29--antwort-header-für-fehlerseiten) | Zusätzliche Header pro Fehlerseite (zum Beispiel `Cache-Control` bei 404) | in `error.ini` gesetzt |
| [L30](#l30--die-reihenfolge-der-suchergebnisse-in-den-dialogen-der-redakteure) | Reihenfolge der Suchergebnisse von ezjscore aus den Sucheinstellungen | wird mit der Reihenfolge ausgeliefert, die sie schon immer hatte |
| [L32](#l32--anfrageadressen-werden-einmal-dekodiert) | Anfrageadressen werden einmal dekodiert (`%2B` bleibt `+`) | einige View-Parameter ändern sich, daher aus |
| [L33](#l33--das-doppelte-anführungszeichen-in-iri-url-aliasen) | Das doppelte Anführungszeichen wird aus IRI-URL-Aliasen entfernt | neue Aliase können sich von älteren unterscheiden, daher aus |

### Für neue Installationen eingeschaltet, für Upgrades ausgeschaltet

| | Funktion | Warum sie Sorgfalt braucht | Wie damit umgegangen wird |
|---|---|---|---|
| [L01](#l01--knotendaten-aufrufe-können-eine-richtlinie-verlangen) | `ezjscnode` (Knotendaten als JSON/XML) kann eine Richtlinie verlangen | Frontend-JavaScript, das sie für anonyme Besucher liest, würde nicht mehr funktionieren; heute kann jeder, der einen Knoten lesen darf, auf diese Weise dessen Metadaten lesen | bei neuen Installationen ein, bei Upgrades aus; die [Liste der Aufrufer](love/l01-aufrufer.md) sagt, wer sie verwendet |
| [L02](#l02--die-registrierung-kann-eine-richtlinie-verlangen) | Die Registrierung braucht eine Richtlinie | Websites, die sich auf offene Registrierung verlassen, würden den Link „Registrieren“ verlieren | nur bei neuen Installationen |
| [L04](#l04--ein-konto-pro-anmeldename) | Anmeldenamen werden in der Datenbank eindeutig | das Hinzufügen des Index schlägt fehl, wenn doppelte Anmeldenamen existieren; LDAP oder SSO können Duplikate erzeugen | neue Installationen haben den Index; Upgrades erhalten zuerst einen Bericht über Duplikate und den Index nur auf Wunsch |
| [L23](#l23--bedienelemente-für-custom-tags-im-rich-text-editor) | Bedienelemente für Custom Tags im Rich-Text-Editor | der Editor sieht anders aus | bei neuen Installationen ein, bei Upgrades aus |

### Neue APIs für Entwickler (nur Ergänzungen, nichts Bestehendes ändert sich)

| | Funktion |
|---|---|
| [L18](#l18--von-einer-php-klasse-erzeugte-namen) | `{MyClass}` im Namensmuster einer Klasse: Ihre eigene PHP-Klasse berechnet den Namen oder URL-Alias |
| [L25](#l25--ezuribuild-baut-eine-site-url-in-einem-aufruf) | `eZURI::build( $url, $full )`: eine Site-URL in einem Aufruf bauen |
| [L26](#l26--eine-nachricht-mit-einem-anderen-mail-transport) | `eZMailTransport::send( $mail, $transport )`: den Mail-Transport pro Nachricht wählen |
| [L28](#l28--include--in-einstellungsdateien) | `[Include path/*.ini]` in Einstellungsdateien, um große Einstellungen aufzuteilen |
| [L31](#l31--templates-können-layout-ansichten-unterscheiden) | `eZTemplateDesignResource::setKeyValue()` |

### Kurz gesagt

Die sichtbaren neuen Funktionen sind Pflicht-Alternativtext, getrimmte Textfelder, eine Schaltfläche Kopieren,
Audit-Einträge für Orte, Bedienelemente für Custom Tags im Editor, zusätzliche Header für Fehlerseiten und Includes in
Einstellungsdateien. Die Sicherheitsänderungen sind `ezjscnode` hinter einer Richtlinie, eine Registrierung, die sich
schließen lässt, ein echtes Abmelden, eindeutige Anmeldenamen und Bild-Uploads, die anhand ihres Inhalts geprüft
werden. Alles andere sind kleine Fehlerkorrekturen und Hilfen für Entwickler.

---

## Funktion für Funktion

Jeder Eintrag sagt, für wen die Funktion gedacht ist (**Redakteur**, **Administrator**, **Entwickler**), was sie tut,
ihre Einstellungen mit ihren ausgelieferten Standardwerten, was eine neue Installation und eine aktualisierte Website
erhalten, wie Sie sie einschalten, der Test, der sie abdeckt, und der LoveStack-Commit, aus dem sie stammt.
Einstellungen, die Sie ändern, gehören in ein Override (`settings/override/<file>.append.php`) oder einen
Siteaccess, nie in die ausgelieferte Datei; leeren Sie danach den INI-Cache
(`php bin/php/ezcache.php --clear-tag=ini`). Die Testdateien liegen unter `tests/tests/` und laufen für sich:
`php vendor/bin/phpunit tests/tests/<path>`.

### Sicherheit und Sessions

#### L01 — Knotendaten-Aufrufe können eine Richtlinie verlangen

Für **Administratoren**. Die ezjscore-Aufrufe `ezjscnode::load`, `::subtree` und `::updatepriority` geben Name, IDs,
Remote-ID, Besitzer, Sektion, Daten, Klasse und Übersetzungen eines Knotens als JSON oder XML an jeden Benutzer
zurück, der den Knoten lesen darf, anonyme Besucher eingeschlossen. Mit `RequirePolicy=enabled` braucht ein Benutzer
zusätzlich die Richtlinie *ezjscore / call* mit der Funktionsliste `ezjscnode`.

| | |
|---|---|
| Einstellungen | `extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjscnode] Functions[]=ezjscnode`, `RequirePolicy=disabled`. `RequirePolicy` funktioniert für jeden Block `[ezjscServer_<name>]`: nicht gesetzt = `Functions[]` wird wie bisher geprüft (ein abgelehnter Aufruf antwortet mit seinem Fehlertext); `disabled` = nicht geprüft; `enabled` = geprüft, und eine Anfrage, deren Aufrufe alle aus diesem Grund abgelehnt werden, wird mit **403 Forbidden** beantwortet |
| Neue Installation | `RequirePolicy=enabled` (geschrieben nach `settings/override/ezjscore.ini.append.php`); die Rolle Editor erhält *ezjscore / call* mit `ezjscnode`, das die Liste der Unterelemente und die Vorschau der Bildgrößen des Rich-Text-Editors verwenden. Administratoren haben es über den vollen Zugriff |
| Upgrade | unverändert (`RequirePolicy=disabled`) |
| Einschalten | geben Sie *ezjscore / call* mit der Funktionsliste `ezjscnode` jeder Rolle, deren Benutzer die Administration ohne vollen Zugriff verwenden, und setzen Sie dann `RequirePolicy=enabled` in `settings/override/ezjscore.ini.append.php`. Die ausgelieferten Designs rufen `ezjscnode` nie für anonyme Besucher auf; siehe [wer ezjscnode aufruft](love/l01-aufrufer.md) |
| Test | `extension/ezjscore/ezjscServerRouterPolicyTest.php` |
| LoveStack | [`aa307a7ff3`](https://github.com/mugoweb/ezpublish-legacy/commit/aa307a7ff3110121006b1b97f19fdcbdeab41190) (2024) |
| Anleitung | [Rollen und Richtlinien, Abschnitt 8](../../../eng-us/guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data) |

#### L02 — Die Registrierung kann eine Richtlinie verlangen

Für **Administratoren**. Das Registrieren eines neuen Kontos (`user/register`) kann aus der Liste der Views
herausgenommen werden, die jeder öffnen darf; dann braucht es die Richtlinie *User / Register*. Die Anmeldeseiten von
admin und admin4, das Design standard und seine Anmelde-Werkzeugleiste sowie die Designs media und starter zeigen den
Link zum Registrieren oder die Schaltfläche Sign Up nur einem Benutzer, der `user/register` öffnen darf, sodass eine
Website ohne offene Registrierung keinen toten Link zeigt.

| | |
|---|---|
| Einstellungen | `site.ini [RoleSettings] PolicyOmitListExclude[]` (neu, leer) nimmt einzelne Einträge aus `PolicyOmitList[]` heraus, nachdem jede Einstellungsdatei gelesen ist, sodass die Einträge erhalten bleiben, die Erweiterungen hinzufügen. `user/register` bleibt in der ausgelieferten `PolicyOmitList[]` |
| Neue Installation | `PolicyOmitListExclude[]=user/register`: Die Registrierung braucht die Richtlinie. Für eine offene Registrierung geben Sie der Rolle Anonymous *User / Register* |
| Upgrade | unverändert: Die Registrierung und ihr Link bleiben offen |
| Einschalten | fügen Sie `PolicyOmitListExclude[]=user/register` unter `[RoleSettings]` in `settings/override/site.ini.append.php` hinzu; geben Sie der Rolle Anonymous *User / Register*, wo die Registrierung offen bleiben soll |
| Für Entwickler | `expPolicyOmitList::entries()`, `::excluded()`, `::omits( $module, $view )`; in Templates `fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) )` |
| Tests | `kernel/classes/expPolicyOmitListTest.php`, `kernel/content/eZLoginTemplatesRegisterLinkTest.php` |
| LoveStack | [`98854ab935`](https://github.com/mugoweb/ezpublish-legacy/commit/98854ab9352eecbfd00e42b067d0cb3b7d26e6f7) (2021) |
| Anleitung | [Rollen und Richtlinien, Abschnitt 8](../../../eng-us/guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data) |

#### L03 — Das Abmelden beendet die Session und ihr Cookie

Für **alle**. Das Abmelden entfernt die Session und ihren Inhalt, statt ihr eine neue ID zu geben und den Inhalt zu
behalten. Jeder Session-Handler sendet das Session-Cookie abgelaufen, mit demselben Pfad, derselben Domain, `Secure`,
`HttpOnly` und `SameSite`, mit denen es gesetzt wurde, sodass der Browser genau dieses Cookie verwirft und die alte
Session-ID niemanden mehr anmeldet. Eine später in derselben Anfrage gestartete Session erhält eine neue ID, unter
PHP-FPM wie unter Velocity.

| | |
|---|---|
| Einstellungen | keine |
| Neue Installation und Upgrade | ein |
| Hinweis für Entwickler | eigener Code, der nach `eZUser::logoutCurrent()` Session-Variablen liest, findet die Session leer. Neu: `eZSession::expiredCookie()`, `eZSession::expireCookie()` |
| Test | `kernel/classes/eZUserLogoutTest.php` |
| LoveStack | [`13ace6c9c8`](https://github.com/mugoweb/ezpublish-legacy/commit/13ace6c9c87ace0869ab234672aeffb377412e29) (2019) |
| Anleitung | [Sicherheit und Audit, Abschnitt 1](../../../eng-us/guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes) |

#### L04 — Ein Konto pro Anmeldename

Für **Administratoren**. Ein Anmeldename gehört zu einem Konto. Ein Konsolenbefehl führt die Anmeldenamen auf, die
mehr als ein Konto hat (auch solche, die sich nur in der Groß- und Kleinschreibung unterscheiden), und fügt einen
eindeutigen Index auf `ezuser.login` hinzu, sobald es keine mehr gibt. Eine erste Version eines Benutzerkontos, deren
Formular die Validierung nicht bestanden hat, wird nicht gespeichert, und ein Entwurf, ein Paketimport, die
Kontozeichenkette der Content-API und eine erste Anmeldung über LDAP geben einem Konto keinen Anmeldenamen mehr, den
ein anderes Konto hat; zwei Anfragen, die gleichzeitig dasselbe Konto anlegen, enden mit einem Konto.

| | |
|---|---|
| Befehle | `./console exp:user-login-check` (nur Bericht; `--show-logins`, `--json`) und `./console exp:user-login-check --fix-index` (fügt den Index nach einem sauberen Bericht hinzu, lehnt sonst ab). Exit-Code 0 sauber oder Index hinzugefügt, 1 Duplikate gefunden oder Index abgelehnt, 2 Fehler |
| Neue Installation | das Kernel-Schema jeder Engine hat den eindeutigen Index |
| Upgrade | das Datenbank-Update fügt den Index **nicht** hinzu, weil es bei Duplikaten fehlschlagen würde. Führen Sie den Bericht aus, lösen Sie, was er aufführt, und dann `--fix-index` |
| Für Entwickler | `expUserLoginIntegrity::duplicates()`, `indexStatus()`, `addUniqueIndex()`, `loginTakenBy()`, `storeUser()` |
| Test | `kernel/classes/expUserLoginIntegrityTest.php` |
| LoveStack | [`826e1d4647`](https://github.com/mugoweb/ezpublish-legacy/commit/826e1d4647060b30ed6af1be5dff3233ced17840) (2020) |
| Anleitung | [Sicherheit und Audit, Abschnitt 7](../../../eng-us/guides/security-and-audit.md#7-one-account-per-login-2-minutes) |

#### L05 — Hochgeladene Bilder werden anhand ihres Inhalts geprüft

Für **alle**. Fileinfo von PHP liest den Typ eines hochgeladenen Bildes aus seinem Inhalt, und der Typ muss auf einer
Liste erlaubter Typen stehen. Eine abgelehnte Datei wird nicht gespeichert. Das gilt für den Bild-Datentyp im
Bearbeitungsformular, `content/upload`, den Upload-Dialog des Rich-Text-Editors, den AJAX-Uploader, WebDAV und
Importe; `eZContentUpload` lehnt eine solche Datei ab, bevor es ein Objekt anlegt.

| | |
|---|---|
| Einstellungen | `image.ini [ImageUploadSettings] ValidateMIMEType=enabled`, `AllowedMIMETypes[]` = `image/jpeg`, `image/png`, `image/gif`, `image/webp`, `image/avif`, `image/tiff`, `image/bmp`. SVG steht nicht auf der Liste |
| Neue Installation und Upgrade | ein: Eine Datei, die ihrem Inhalt nach kein aufgeführtes Bild ist (eine umbenannte Datei, HEIC, SVG), wird mit "A valid image file is required." oder "Images of the type %1 are not accepted." abgelehnt |
| Um einen weiteren Typ zu akzeptieren | fügen Sie ihn in einem Override zu `AllowedMIMETypes[]` hinzu. `ValidateMIMEType=disabled` behält nur die Prüfung mit `getimagesize()`. Ohne die Erweiterung fileinfo entscheidet diese Prüfung, und eine Warnung pro Prozess sagt das |
| Für Entwickler | `expImageUploadValidator::validate( $path, $originalName = null )` gibt `array( 'valid' => bool, 'mime' => string )` zurück |
| Tests | `kernel/classes/expImageUploadValidatorTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php` |
| LoveStack | [`c8e45fced7`](https://github.com/mugoweb/ezpublish-legacy/commit/c8e45fced70f3b9f3a03c1b4329f3eb863c72188) (2017) |
| Anleitung | [Verhaltensänderungen vom Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md#access-and-security) |

#### L06 — Vorschaubilder von Paketen werden ebenso geprüft

Für **Administratoren**. Der Paket-Assistent prüft das Vorschaubild eines neuen Pakets und die Bilder eines
Style-Pakets mit derselben Prüfung des Inhalts und derselben Liste erlaubter Typen wie L05.

| | |
|---|---|
| Einstellungen | die von L05 |
| Neue Installation und Upgrade | ein |
| Test | `kernel/classes/packages/eZStylePackageCreatorTest.php` |
| LoveStack | [`04b3b6985a`](https://github.com/mugoweb/ezpublish-legacy/commit/04b3b6985a0d8b07f1d0f32d0d9840c1b308fb62) (2018) |
| Anleitung | [Verhaltensänderungen vom Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md#access-and-security) |

#### Die Session-Einstellung `SameSite` von LoveStack wird ebenfalls gelesen

Für **Administratoren** von Websites, die von LoveStack kommen. `site.ini [Session] SameSite=`, der Name, den
LoveStack für das Attribut SameSite des Session-Cookies verwendet, wird gelesen, wenn `CookieSameSite` von Exponential
nicht gesetzt ist (`CookieSameSite` gewinnt, wenn beide gesetzt sind), und einmal pro Anfrage auf der
Deprecation-Stufe der Debug-Ausgabe vermerkt. Für Besucher ändert sich nichts; benennen Sie die Einstellung in
`CookieSameSite` um, damit der Hinweis verschwindet.

| | |
|---|---|
| Test | `lib/ezsession/eZSessionSameSiteAliasTest.php` |
| LoveStack | [`e56cb305f0`](https://github.com/mugoweb/ezpublish-legacy/commit/e56cb305f0d83548bf3ec8ac44668be7ff4bc796) (2025) |
| Anleitung | [Sicherheit und Audit, Abschnitt 1](../../../eng-us/guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes) |

### Diagnose und Protokollierung

#### L07 — PHP-Deprecations erhalten ein eigenes Log

Für **Administratoren** und **Entwickler**. PHP-Deprecations haben eine eigene Debug-Stufe. Sie werden nach
`var/log/deprecated.log` geschrieben, jede eindeutige Meldung einmal pro Anfrage, und nur gezählt: Die Debug-Leiste
zeigt ihre Zahl (`depr`), sie bleiben in der Zahl der Warnungen der Seite, und keine wird im Speicher gehalten oder mit
den anderen Meldungen aufgelistet. Unter PHP 8 hält das die Debug-Ausgabe lesbar und ihren Speicherverbrauch niedrig.

| | |
|---|---|
| Einstellungen | `site.ini [DebugSettings] DeprecatedLog=enabled`. Fügen Sie `AlwaysLog[]=deprecated` hinzu, um sie auch bei ausgeschalteter Debug-Ausgabe zu schreiben |
| Neue Installation und Upgrade | ein; `DeprecatedLog=disabled` lässt sie wie bisher Warnungen sein |
| Für Entwickler | `eZDebug::LEVEL_DEPRECATED`, `eZDebug::SHOW_DEPRECATED` (Teil von `SHOW_ALL`), `eZDebug::writeDeprecated()`, `eZDebug::deprecatedCount()` |
| Test | `lib/ezutils/eZDebugDeprecatedTest.php` |
| LoveStack | [`fbbd333a85`](https://github.com/mugoweb/ezpublish-legacy/commit/fbbd333a85350123a2234db1357fc5fd9478ca3b), [`e20570a7ad`](https://github.com/mugoweb/ezpublish-legacy/commit/e20570a7ad846b31f3e60609dec0b9c0f7e44086), [`b7832d110e`](https://github.com/mugoweb/ezpublish-legacy/commit/b7832d110e4790edc4510328353f24acfb7df19b) (2025) |
| Anleitung | [PHP-Deprecations und Größe der Log-Dateien](../../../eng-us/guides/operating-a-site.md#php-deprecations-and-log-file-sizes) |

#### L08 — Größe der Log-Dateien in site.ini

Für **Administratoren**. Die Größe, bei der Log-Dateien rotiert werden, und wie viele alte Dateien behalten werden,
sind Einstellungen, die eZLog und eZDebug lesen.

| | |
|---|---|
| Einstellungen | `site.ini [FileSettings] LogFileMaxSize=2097152` (2 MB), `LogFileRotateCount=3`. `eZLog::setMaxLogSize()`, `setLogrotateFiles()` und die Konstanten in `config.php` haben weiterhin Vorrang davor |
| Neue Installation und Upgrade | Log-Dateien werden bei 2 MB statt bei 200 KB rotiert; `LogFileMaxSize=204800` behält die alte Größe |
| Test | `lib/ezfile/eZLogMaxSizeTest.php` |
| LoveStack | [`e39dde30ff`](https://github.com/mugoweb/ezpublish-legacy/commit/e39dde30ff3213bc32e7423b0827ef29f07d55af) (2021) |
| Anleitung | [PHP-Deprecations und Größe der Log-Dateien](../../../eng-us/guides/operating-a-site.md#php-deprecations-and-log-file-sizes) |

#### L09 — Eine Warnung für einen Operator-Parameter ohne Standardwert

Für **Template-Entwickler**. Ein Parameter eines Template-Operators, den das Template weglässt und für den der
Operator keinen Standardwert gibt, ist `null`, mit der Warnung `Operator '<op>' has no default value for parameter
'<name>'`, einmal pro Operator und Parameter in einer Anfrage (für ein kompiliertes Template wird einmal gewarnt, beim
Kompilieren).

| | |
|---|---|
| Neue Installation und Upgrade | ein; nur die Warnung ist neu |
| Test | `lib/eztemplate/eZTemplateOperatorDefaultWarningTest.php` |
| LoveStack | [`0bb0bac618`](https://github.com/mugoweb/ezpublish-legacy/commit/0bb0bac618273b2eea8ba139864677b666c0b382) (2016) |
| Anleitung | [Ein Operator-Parameter ohne Standardwert](../../../eng-us/guides/templates-and-design.md#an-operator-parameter-without-a-default-value) |

#### L10 — Bearbeitungslinks in der Liste der verwendeten Templates

Für **Template-Entwickler**. Die Links „Bearbeiten“ in der Liste der verwendeten Templates der Debug-Ausgabe können
auf eine Adresse Ihrer Wahl zeigen, zum Beispiel auf den Template-Editor der Administration.

| | |
|---|---|
| Einstellungen | `site.ini [DebugSettings] UsedTemplateEditUrl=`, ein Format für `sprintf()`: `%1$s` die Adresse des Editors mit der Datei, `%2$s` der Siteaccess, `%3$s` die Template-Datei. Leer (ausgeliefert) = der Link, den die Liste schon immer hatte |
| Neue Installation und Upgrade | unverändert, bis gesetzt |
| Test | `kernel/common/eZTemplatesStatisticsReporterEditUrlTest.php` |
| LoveStack | [`fb20bee0d2`](https://github.com/mugoweb/ezpublish-legacy/commit/fb20bee0d2be87187f992284971abe0347798106) (2019) |
| Anleitung | [Bearbeitungslinks in der Liste der verwendeten Templates](../../../eng-us/guides/templates-and-design.md#edit-links-in-the-list-of-used-templates) |

#### L11 — Datenbankfehler tragen die Fehlernummer des Treibers

Für **Entwickler**. `eZDBException::getCode()` ist bei jedem Treiber die Fehlernummer des Treibers für die
fehlgeschlagene Anweisung, wie `errorNumber()`: die Nummer von MySQL, der SQLSTATE von PostgreSQL (eine
Zeichenkette), der Ergebniscode von SQLite, die ORA-Nummer von Oracle; MongoDB behält den Code des Servers in
`errorNumber()`. Die Seite „Datenbank nicht verfügbar“ (HTTP 503) erkennt außerdem die Fehlernummern, die besagen,
dass die Verbindung verloren ging oder nicht hergestellt werden konnte.

| | |
|---|---|
| Neue Installation und Upgrade | ein. Das `errorNumber()` von PostgreSQL ist der SQLSTATE statt `1`; bei eingeschalteter Behandlung von Ausnahmen wirft MySQL unter PHP 8.1 und neuer `eZDBException` statt `mysqli_sql_exception` |
| Test | `lib/ezdb/eZDBExceptionCodeTest.php` |
| LoveStack | [`3d18f47551`](https://github.com/mugoweb/ezpublish-legacy/commit/3d18f475510fa3a81a4b0984854d87ace79fd269) (2017) |
| Anleitung | [Kennen Sie Ihren Datenbanktreiber](../../../eng-us/guides/operating-a-site.md#7-know-your-database-driver) |

#### L12 — Eine Warnung, wenn eine Änderung jeden View-Cache leert

Für **Administratoren**. Würde eine Änderung die View-Caches von `site.ini [ContentSettings] CacheThreshold` oder mehr
Knoten leeren und werden stattdessen alle View-Caches als abgelaufen markiert, nennt eine Warnung im Log die Zahl der
Knoten und den Schwellenwert (bisher war es eine Debug-Meldung).

| | |
|---|---|
| Neue Installation und Upgrade | ein |
| Test | `kernel/classes/eZContentCacheManagerThresholdWarningTest.php` |
| LoveStack | [`f9691221a3`](https://github.com/mugoweb/ezpublish-legacy/commit/f9691221a3d3999722544285a9be7da52a1f44d6) (2018) |
| Anleitung | [Caches, häufige Fragen](../../../eng-us/guides/caches.md#6-usual-questions) |

### Inhalte, Datentypen und die Administration

#### L13 — Audit-Einträge für Änderungen an Orten

Für **Administratoren**. Änderungen an Orten sind Ereignisse des Audit-Protokolls von Exponential (Kanal `content`):
`content.node.add` einmal pro hinzugefügtem Ort, `content.node.remove` mit der Angabe, ob der entfernte Ort der
Hauptort war, und `content.node.main`, wenn ein Redakteur einen anderen Hauptort wählt oder der nächste Ort an die
Stelle eines entfernten Hauptorts tritt. Die Audit-Konsole zeigt sie als Ort hinzugefügt, Ort entfernt und Hauptort
geändert. Die Namen `location-assign`, `location-remove` und `main-node-update` von LoveStack sind diesen Ereignissen
zugeordnet, sodass Aufrufe von `eZAudit::writeAudit()` und Audit-Dateien mit diesen Namen weiter funktionieren.

| | |
|---|---|
| Einstellungen | `audit.ini [AuditEventSettings] Enabled[]`; `[AuditCompatSettings] Map[]` und `[AuditSettings] AuditFileNames[]` enthalten die Namen von LoveStack |
| Neue Installation und Upgrade | `content.node.remove` ein, wie bisher; `content.node.add` und `content.node.main` aus |
| Einschalten | `Enabled[]=content.node.add` und `Enabled[]=content.node.main` unter `[AuditEventSettings]` in `settings/override/audit.ini.append.php` |
| Test | `kernel/content/eZAuditLocationTest.php` (braucht eine Datenbank; rollt zurück, was er schreibt) |
| LoveStack | [`9c931192e0`](https://github.com/mugoweb/ezpublish-legacy/commit/9c931192e06022a7be9929f62d4c851a91fe25b7) (2017) |
| Anleitung | [Audit-Referenz: Änderungen an Orten](../../../eng-us/bc/6.0/audit.md#location-changes-who-added-removed-or-chose-a-location) |

#### L14 — Site-Daten in Templates und in der Administration

Für **Entwickler** und **Administratoren**. Ein Template kann eine Zeile von `ezsite_data` über ihren Namen lesen, für
Namen, die Sie aufführen; **Setup > Site-Daten** (`setup/sitedata`, Richtlinie `setup/system_info`) führt die Zeilen
mit Filter und Seitenaufteilung auf, in admin, admin3 und admin4.

| | |
|---|---|
| Einstellungen | `site.ini [SiteDataSettings] TemplateReadable[]` (leer: nichts ist aus Templates lesbar) |
| Template | `{def $row = fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )}` liefert das eZSiteData-Objekt oder false, wenn der Name nicht aufgeführt oder nicht gespeichert ist |
| Neue Installation und Upgrade | nichts lesbar, bis ein Name aufgeführt ist; die Liste in der Administration ist vorhanden |
| Für Entwickler | `eZSiteData::createNew()`, `templateReadableNames()`, `isTemplateReadable()`, `fetchList()`, `fetchListCount()` |
| Tests | `kernel/content/eZContentFunctionCollectionSiteDataTest.php`, `kernel/classes/eZSiteDataCreateTest.php` |
| LoveStack | [`55029bd473`](https://github.com/mugoweb/ezpublish-legacy/commit/55029bd473e47e73b86f748f0d2d264a5e14843a), [`a679a13176`](https://github.com/mugoweb/ezpublish-legacy/commit/a679a131768b3bbdb2337c82c16d15ec9e58c854), [`94b7b6c239`](https://github.com/mugoweb/ezpublish-legacy/commit/94b7b6c239f7abbf58cbefed07ac28784933c997) (2017–2019) |
| Anleitungen | [Site-Daten in Templates](../../../eng-us/guides/templates-and-design.md#9-site-data-in-templates), [Site-Daten in den Systeminformationen](../../../eng-us/guides/system-information.md#9-site-data) |

#### L15 — Eine Schaltfläche Kopieren in der vollständigen Admin-Ansicht

Für **Redakteure**. Die vollständige Ansicht der Administration (admin, admin3, admin4) hat eine Schaltfläche
**Kopieren** neben Verschieben, aktiv mit Erstellrecht am Elternknoten des Elements, wie im Kontextmenü. Sie öffnet
dieselben Schritte zum Kopieren wie das Kontextmenü.

| | |
|---|---|
| Neue Installation und Upgrade | ein |
| Test | `kernel/content/eZAdminFullViewCopyButtonTest.php` |
| LoveStack | [`4d56593614`](https://github.com/mugoweb/ezpublish-legacy/commit/4d565936146cd970051a51f27350abb30ecfa853) (2019) |
| Anleitung | [Aus der vollständigen Ansicht kopieren](../../../eng-us/guides/content-model-and-editing.md#copy-from-the-full-view) |

#### L16 — Pflicht-Alternativtext für Bildfelder

Für **Redakteure** und **Administratoren** (Barrierefreiheit). Ein Bildattribut kann einen Alternativtext verlangen.
Das Bearbeitungsformular, der Upload-Dialog des Online-Editors (auch von Tiptap verwendet) und der AJAX-Uploader von
Relationsfeldern lehnen dann ein Bild ohne Alternativtext ab; der AJAX-Uploader hat ein Feld für den Alternativtext für
Klassen mit einem Bildattribut. Pakete tragen die Option mit.

| | |
|---|---|
| Einstellungen | die Option des Klassenattributs **Alternativtext ist erforderlich** auf der Seite zur Bearbeitung der Klasse (`eZImageType::ALT_TEXT_REQUIRED_FIELD`, `data_int2`) |
| Neue Installation und Upgrade | aus für jede Klasse |
| Einschalten | pro Bildattribut, auf der Seite zur Bearbeitung der Klasse |
| Tests | `kernel/classes/datatypes/eZImageTypeAltTextTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderAltTextTest.php` |
| LoveStack | [`a28863d9c0`](https://github.com/mugoweb/ezpublish-legacy/commit/a28863d9c08e8ad417d1e1abcbbd664b777c743c) (2017) |
| Anleitung | [Alternativtext ist erforderlich](../../../eng-us/guides/content-model-and-editing.md#alternative-text-is-required-image-fields) |

#### L17 — Den gespeicherten Wert von Textzeilenfeldern trimmen

Für **Redakteure** und **Administratoren**. Ein Textzeilenattribut kann Leerzeichen am Anfang und am Ende seines Wertes
entfernen, im Bearbeitungsformular, bei der Informationssammlung, bei Importen und Uploads.

| | |
|---|---|
| Einstellungen | die Option des Klassenattributs **Gespeicherten Wert trimmen** (`eZStringType::TRIM_FIELD`, `data_int2`) |
| Neue Installation und Upgrade | aus für jede Klasse |
| Einschalten | pro Textzeilenattribut, auf der Seite zur Bearbeitung der Klasse |
| Test | `kernel/classes/datatypes/eZStringTypeTrimTest.php` |
| LoveStack | [`6a5c7dd23e`](https://github.com/mugoweb/ezpublish-legacy/commit/6a5c7dd23efffa349d255f65bc03b7de55483915) (2020) |
| Anleitung | [Gespeicherten Wert trimmen](../../../eng-us/guides/content-model-and-editing.md#trim-the-stored-value-text-line-fields) |

#### L18 — Von einer PHP-Klasse erzeugte Namen

Für **Entwickler**. Ein Muster für Objektnamen oder URL-Aliase einer Klasse, das mit `{MyResolver}` beginnt, wird von
Ihrer PHP-Klasse `MyResolver` aufgelöst, wenn sie aufgeführt ist und `expNamePatternResolverInterface`
implementiert. Jeder andere Name wird protokolliert, und das Muster wird wie üblich aus seinen Token aufgelöst.

| | |
|---|---|
| Einstellungen | `content.ini [NamePatternSettings] ResolverClasses[]` (leer) |
| Neue Installation und Upgrade | nichts ändert sich, bis eine Klasse aufgeführt ist |
| Für Entwickler | `expNamePatternResolverInterface::resolveNamePattern( $object, $version, $translation, $length, $sequence )`; `eZNamePatternResolver::resolve()`, `customResolver()`, `limitName()`. Der Konstruktor und `resolveNamePattern()` sind unverändert |
| Tests | `kernel/classes/eZNamePatternResolverClassTest.php`, `kernel/classes/eZNamePatternResolverLimitTest.php` |
| LoveStack | [`95d86f5990`](https://github.com/mugoweb/ezpublish-legacy/commit/95d86f5990ba83be39e4871bd88c2ecf120e8099) (2020) |
| Anleitung | [Von einer PHP-Klasse erzeugte Namen](../../../eng-us/guides/content-model-and-editing.md#names-made-by-a-php-class-developers) |

#### L19 — Standard des Klassenfilters für Fetches

Für **Template-Entwickler**. Der Typ des Klassenfilters, den die Fetch-Funktionen `list`, `tree`, `list_count`,
`tree_count` und `calendar` verwenden, wenn ein Fetch `class_filter_array` ohne `class_filter_type` angibt.

| | |
|---|---|
| Einstellungen | `content.ini [FetchSettings] DefaultClassFilterType=` (leer: die heutigen Ergebnisse, das Array wird ignoriert; `include`: nach dem Array filtern) |
| Neue Installation und Upgrade | leer |
| Test | `kernel/content/eZContentFunctionCollectionClassFilterTypeTest.php` |
| LoveStack | [`9c0ff40a78`](https://github.com/mugoweb/ezpublish-legacy/commit/9c0ff40a784057bc2cd5c0eb2600510ce8c7ba1c) (2018) |
| Anleitung | [Standard des Klassenfilters für Fetches](../../../eng-us/guides/content-model-and-editing.md#class-filter-default-for-fetches-template-developers) |

#### L20 — Das Fenster Orte lässt einen versteckten Ort versteckt

Für **Redakteure**. Im Fenster Orte des Bearbeitungsformulars beginnt ein vorhandener Ort bei „Unverändert“, nicht bei
„Sichtbar“, sodass das Veröffentlichen einen versteckten Ort nicht mehr sichtbar macht; ein neuer Ort beginnt so, wie
er angegeben wurde (EZP-26501).

| | |
|---|---|
| Neue Installation und Upgrade | ein (eine Korrektur) |
| Test | `kernel/content/eZEditLocationsVisibilityTest.php` |
| LoveStack | [`f1b7f056b4`](https://github.com/mugoweb/ezpublish-legacy/commit/f1b7f056b48ae296d9b0cee7b340dc5bd8879011) (2016) |
| Anleitung | [Das Fenster Orte](../../../eng-us/guides/content-model-and-editing.md#the-locations-window-keeps-a-hidden-location-hidden) |

#### L21 — Auswahl- und Matrixfelder ohne überzählige Zeilenumbrüche

Für **Designer** und Besucher. Die View-Templates der Datentypen Auswahl und Matrix schreiben kein überzähliges
`<br />`; mehrere ausgewählte Optionen werden durch je ein `<br />` getrennt.

| | |
|---|---|
| Neue Installation und Upgrade | ein, für jede Website, die diese Templates nicht überschreibt |
| Test | `kernel/datatypes/eZSelectionMatrixViewTemplateTest.php` |
| LoveStack | [`eee7f69fc3`](https://github.com/mugoweb/ezpublish-legacy/commit/eee7f69fc3aa06243b5e9d1dc5f5a3c5c948bdcc), [`67eec8f972`](https://github.com/mugoweb/ezpublish-legacy/commit/67eec8f972ffb023d588dafef0a91beea1be3e80), [`492a946963`](https://github.com/mugoweb/ezpublish-legacy/commit/492a946963082c988b69d24023854d60dd8bac62) |
| Anleitung | [Auswahl- und Matrixfelder](../../../eng-us/guides/content-model-and-editing.md#selection-and-matrix-fields-without-stray-line-breaks) |

### Rich-Text-Editor

#### L22 — Versteckte Custom Tags

Für **Administratoren** und **Redakteure**. Custom Tags, die Sie aufführen, werden in den Editoren (TinyMCE 3,
TinyMCE 8, Tiptap) für neue Tags nicht angeboten. Wo ein versteckter Tag bereits in einem Text steht, wird er weiterhin
angezeigt, gerendert, ist bearbeitbar und wird unverändert zurückgespeichert.

| | |
|---|---|
| Einstellungen | `content.ini [CustomTagSettings] IsHidden[]` (keiner versteckt) |
| Neue Installation und Upgrade | keiner versteckt |
| Für Entwickler | `eZOEXMLInput::hiddenCustomTags()`, `offeredCustomTags()`, die Schlüssel `hidden` und `preview` von `getCustomTagDefinitions()` |
| Test | `extension/ezoe/expOECustomTagListTest.php` |
| LoveStack | [`e21ef46691`](https://github.com/mugoweb/ezpublish-legacy/commit/e21ef466914d44d384666fd024080f06be4c426c) (2019) |
| Anleitung | [Versteckte Custom Tags](../../../eng-us/guides/content-model-and-editing.md#hidden-custom-tags) |

#### L23 — Bedienelemente für Custom Tags im Rich-Text-Editor

Für **Redakteure**. Ein fokussierter Custom Tag erhält eine Leiste, um ihn nach oben oder unten zu verschieben, ihn zu
bearbeiten und zu entfernen (sein Inhalt bleibt), auch mit Alt+Umschalt+Pfeil hoch, Alt+Umschalt+Pfeil runter,
Alt+Umschalt+E und Alt+Umschalt+Entf; die Rücktaste in einem leeren Block-Custom-Tag entfernt ihn; jeder Block-Custom-Tag
zeigt seinen Namen als Beschriftung; der Dialog eines Tags zeigt `images/editor/custom_tags_thumbs/<name>.png` aus
einem Design als Beispiel. TinyMCE 3 erhält das Plugin `exp_customtag_controls` (auf der Plugin-API von TinyMCE 3 und
dem jQuery von ezjscore gebaut, kein YUI), TinyMCE 8 die Schaltflächen in seiner Werkzeugleiste für Custom Tags, Tiptap
dasselbe in exp_oe_tiptap 0.1.2.

| | |
|---|---|
| Einstellungen | `ezoe.ini [EditorSettings] CustomTagControls=disabled`; Stylesheet `ezoe_customtags.css` (wirkt nur, wenn eingeschaltet) |
| Neue Installation | `CustomTagControls=enabled` |
| Upgrade | unverändert (`disabled`) |
| Einschalten | `CustomTagControls=enabled` in `settings/override/ezoe.ini.append.php`, dann den INI-Cache und die gepackten Skripte leeren: `php bin/php/ezcache.php --clear-tag=ini --clear-id=ezjscore-packer` |
| Test | `extension/ezoe/expOECustomTagAssetsTest.php` |
| LoveStack | [`63334f1681`](https://github.com/mugoweb/ezpublish-legacy/commit/63334f16818083e9d545cc7a99190d8d2ca9f109) (2019; sein Plugin `custom_tags_controls` trägt "Copyright 2014, Mugo Web") |
| Anleitung | [Bedienelemente für Custom Tags](../../../eng-us/guides/content-model-and-editing.md#custom-tag-controls) |

#### L24 — Der Dialog sub/sup schließt bei OK

Für **Redakteure**. Der Dialog der Custom Tags sub und sup von TinyMCE 3 schließt, wenn Sie OK drücken. Das
`tag_custom.tpl` von Exponential hatte diese Korrektur bereits, daher hat sich nichts geändert; es wurde im Browser
unter TinyMCE 3 und Tiptap geprüft (TinyMCE 8 hat eigene Schaltflächen für sub und sup).

| | |
|---|---|
| Test | nur Prüfung im Browser (ein JavaScript-Dialog) |
| LoveStack | [`3a8c14bc0c`](https://github.com/mugoweb/ezpublish-legacy/commit/3a8c14bc0c61699d69ff5aa175ac717cbaf6f51a) (2019) |

### APIs und Einstellungen für Entwickler

#### L25 — `eZURI::build()` baut eine Site-URL in einem Aufruf

Für **Entwickler**. `eZURI::build( string $url, bool $full = false, bool $htmlEscape = true )` liefert denselben Link
wie der Template-Operator `ezurl` (Indexdatei und Siteaccess davor; mit `$full` Schema und Host). Verwenden Sie
`$htmlEscape = false` für eine URL in einem Header oder einer JSON-Antwort.

| | |
|---|---|
| Test | `lib/ezutils/eZURIBuildTest.php` |
| LoveStack | [`f2284944b2`](https://github.com/mugoweb/ezpublish-legacy/commit/f2284944b27f0ba38e2f41876804ae9400fd25ce) (2020) |
| Anleitung | [URLs, für Entwickler](../../../eng-us/guides/urls-and-aliases.md#for-developers) |

#### L26 — Eine Nachricht mit einem anderen Mail-Transport

Für **Entwickler**. `eZMailTransport::send( eZMail $mail, ?string $transportType = null )` sendet eine Nachricht mit
einem anderen Transport aus `site.ini [MailSettings] TransportAlias[]` (zum Beispiel `file`), ohne die Einstellung zu
ändern; die Mail-Sperre gilt wie bisher.

| | |
|---|---|
| Test | `lib/ezutils/eZMailTransportSendTest.php` |
| LoveStack | [`df07cb1ef5`](https://github.com/mugoweb/ezpublish-legacy/commit/df07cb1ef5de68eaedc0ba86e2dbc91cfccea5d2) (2019) |
| Anleitung | [Testen, ohne Mails zu senden](../../../eng-us/guides/mail-preferences-developer.md#8-testing-without-sending-mail) |

#### L27 — Die Template-Operatoren `rawurlencode` und `rawurldecode`

Für **Template-Entwickler**. Kodierung nach RFC 3986 (`%20` für ein Leerzeichen) in Templates:
`{$value|rawurlencode}`. `urlencode` und `urldecode` sind unverändert. Alle vier Operatoren liefern einen leeren Text
für einen Wert, der nicht gesetzt ist.

| | |
|---|---|
| Einstellungen | `template.ini [PHP] PHPOperatorList[rawurlencode]`, `PHPOperatorList[rawurldecode]` (ausgeliefert) |
| Neue Installation und Upgrade | verfügbar |
| Test | `kernel/common/eZTemplateRawURLEncodeOperatorTest.php` |
| LoveStack | [`627004e5c4`](https://github.com/mugoweb/ezpublish-legacy/commit/627004e5c4ab08917804382cca0b6c67afc68c47) (2021) |
| Anleitung | [Einen Wert für eine URL kodieren](../../../eng-us/guides/templates-and-design.md#10-encoding-a-value-for-a-url-rawurlencode-and-rawurldecode) |

#### L28 — `[Include …]` in Einstellungsdateien

Für **Administratoren** und **Entwickler**. Eine Zeile `[Include site.d/*.ini]` in einer Einstellungsdatei liest an
dieser Stelle andere Dateien: Das Muster ist relativ zur einbindenden Datei, Platzhalter sind erlaubt, die Dateien
werden in der Reihenfolge ihrer Namen gelesen, Includes lassen sich verschachteln, eine Schleife wird einmal mit einer
Warnung gelesen, und ein Muster ohne Treffer ergibt einen Hinweis. Die eingebundenen Dateien sind Teil des INI-Caches,
sodass `php bin/php/ezcache.php --clear-tag=ini` und `exp:velocity deploy` ihre Änderungen übernehmen. Die
Einstellungsseite, der klassische Einstellungseditor und `exp:ini` behalten die Zeile, wie sie geschrieben ist, und
lösen sie nicht auf.

| | |
|---|---|
| Neue Installation und Upgrade | nur aktiv, wo eine Datei eine solche Zeile hat |
| Für Entwickler | `eZINI::includedFiles()` |
| Test | `lib/ezutils/eZINIIncludeTest.php` |
| LoveStack | [`f7c382bc2b`](https://github.com/mugoweb/ezpublish-legacy/commit/f7c382bc2bb4abcbeeb521653f08ca2617eb277f) (2018) |
| Anleitung | [Eine große Einstellungsdatei aufteilen](../../../eng-us/guides/settings-page.md#splitting-a-large-settings-file-with-include-) |

#### L29 — Antwort-Header für Fehlerseiten

Für **Administratoren**. Jede HTTP-Fehlerseite kann nach ihren Statuszeilen zusätzliche Antwort-Header senden; sie
werden mit der Seite im View-Cache gespeichert und von dort erneut gesendet. Ein ungültiger Header-Name oder ein Wert
mit einem Zeilenumbruch wird mit einer Warnung weggelassen.

| | |
|---|---|
| Einstellungen | `error.ini [HTTPError-<code>] HeaderList[<name>]=<value>`, zum Beispiel `HeaderList[Cache-Control]=no-store` unter `[HTTPError-404]` (standardmäßig leer) |
| Neue Installation und Upgrade | nichts ändert sich, bis ein Eintrag hinzugefügt wird |
| Test | `kernel/error/ErrorHeaderListTest.php` |
| LoveStack | [`97690e9c93`](https://github.com/mugoweb/ezpublish-legacy/commit/97690e9c93b156f8c49724b13b69a0de699fed49) (2017) |
| Anleitung | [Antwort-Header von Fehlerseiten](../../../eng-us/guides/templates-and-design.md#12-response-headers-of-error-pages) |

#### L30 — Die Reihenfolge der Suchergebnisse in den Dialogen der Redakteure

Für **Administratoren**. Die Suche von ezjscore (verwendet von den Dialogen zum Einbetten und Verlinken des Editors und
von der Relationssuche) sortiert Ergebnisse von eZ Find nach den Sucheinstellungen, statt immer das Neueste zuerst.

| | |
|---|---|
| Einstellungen | `ezfind.ini [SearchSettings] SortBy[]`; ezjscore liefert `SortBy[published]=desc` aus, die Reihenfolge, die es schon immer verwendet hat |
| Neue Installation und Upgrade | unveränderte Reihenfolge; ohne eZ Find oder die Einstellung bleibt die Reihenfolge Neuestes zuerst |
| Test | `extension/ezjscore/ezjscServerFunctionsJsSearchSortByTest.php` |
| LoveStack | [`3d2d337b0d`](https://github.com/mugoweb/ezpublish-legacy/commit/3d2d337b0dd44de0be91fc703f21dbd96622f35e), [`3af1ec3aee`](https://github.com/mugoweb/ezpublish-legacy/commit/3af1ec3aeef93ae9da652c13828ccdc92513302d) (2025) |
| Anleitung | [Die Reihenfolge der Suchergebnisse](../../../eng-us/guides/content-model-and-editing.md#the-order-of-search-results-in-the-editors-dialogs) |

#### L31 — Templates können Layout-Ansichten unterscheiden

Für **Designer** und **Entwickler**. Jede Knotenansicht setzt den Override-Schlüssel `layout` (leer außerhalb von
`layout/set`, das Layout von `layout/set` bleibt erhalten), sodass ein Template-Override wieder darauf passen kann.
Neu: `eZTemplateDesignResource::setKeyValue( string $key, $value, bool $override = true )`.

| | |
|---|---|
| Neue Installation und Upgrade | ein |
| Test | `kernel/common/eZTemplateDesignResourceSetKeyValueTest.php` |
| LoveStack | [`856a95e240`](https://github.com/mugoweb/ezpublish-legacy/commit/856a95e240b4f2422c60d5253f9730ed99e033af) (2016) |
| Anleitung | [Der Schlüssel layout](../../../eng-us/guides/templates-and-design.md#11-telling-a-layout-view-apart-the-layout-key) |

### URLs und Suche

#### L32 — Anfrageadressen werden einmal dekodiert

Für **Administratoren**. Die Adresse einer Anfrage wird einmal statt zweimal dekodiert, sodass `/(tag)/bar%2Bbar` den
View-Parameter `bar+bar` statt `bar bar` ergibt. PHP-FPM und Velocity übergeben dem Kernel dieselbe rohe Adresse, und
die Schlüssel des Antwort-Caches werden daraus gebildet, daher ändern sie sich nicht.

| | |
|---|---|
| Einstellungen | `site.ini [URLTranslator] DecodeOnce=disabled` |
| Neue Installation und Upgrade | aus |
| Einschalten | `DecodeOnce=enabled` in `settings/override/site.ini.append.php`, nachdem Sie eigene Links geprüft haben, die ein kodiertes `+` oder `%` in einen View-Parameter setzen |
| Für Entwickler | `new eZURI( $uri, $decode )`, `setURIString( $uri, $fullInitialize, $decode )` (`null`: der Einstellung folgen), `eZURI::decodeOnce()` |
| Test | `lib/ezutils/eZSysRequestURIDecodeOnceTest.php` |
| LoveStack | [`f435c2f91e`](https://github.com/mugoweb/ezpublish-legacy/commit/f435c2f91eaeba686d8f3bda4a6f8c41bb3e7329) (2018) |
| Anleitung | [Adressen dekodieren und bereinigen](../../../eng-us/guides/urls-and-aliases.md#8-decoding-and-cleaning-addresses-two-optional-settings) |

#### L33 — Das doppelte Anführungszeichen in IRI-URL-Aliasen

Für **Administratoren**. Die Bereinigung von URL-Aliasen in LoveStack entfernt `!`, Punkte und Trennzeichen am Ende
eines Alias und behandelt `"` als reserviertes Zeichen. Exponential entfernt diese nachgestellten Zeichen seit 2016;
was hinzukommt, ist das doppelte Anführungszeichen: In der IRI-Transformation (`urlalias_iri`) wird es zu einem
Trennzeichen und wie die anderen an den Enden entfernt.

| | |
|---|---|
| Einstellungen | `site.ini [URLTranslator] TrailingCleanup=disabled` |
| Neue Installation und Upgrade | aus |
| Einschalten | `TrailingCleanup=enabled` in `settings/override/site.ini.append.php`. Vorhandene Aliase bleiben, bis ihr Objekt erneut veröffentlicht wird; der alte Alias leitet dann weiter |
| Für Entwickler | `eZCharTransform::trailingCleanup()` |
| Test | `lib/ezi18n/eZCharTransformTrailingCleanupTest.php` |
| LoveStack | [`7517c0e925`](https://github.com/mugoweb/ezpublish-legacy/commit/7517c0e925178e5f932ac653439a39203ec03cb0) (2017) |
| Anleitung | [Adressen dekodieren und bereinigen](../../../eng-us/guides/urls-and-aliases.md#8-decoding-and-cleaning-addresses-two-optional-settings) |

#### L34 — Sehr große Zahlen halten den Suchindex nicht mehr an

Für **Administratoren**. Eine Zahl in einem Text jenseits des Wertebereichs der Ganzzahlspalte des eingebauten
Suchindex wird als Grenzwert der Spalte gespeichert, statt die Indizierung des Objekts anzuhalten. Jeder Treiber gibt
seinen Wertebereich an (`MAX_INT`, `MIN_INT`: vorzeichenbehaftet 32 Bit für MySQL, PostgreSQL und andere Treiber,
vorzeichenbehaftet 64 Bit für SQLite und MongoDB), und `$db->clampInteger( $value )` hält einen Wert darin, auch einen,
der als Gleitkommazahl geschrieben ist.

| | |
|---|---|
| Neue Installation und Upgrade | ein |
| Test | `kernel/search/eZSearchEngineIntegerClampTest.php` |
| LoveStack | [`3774a54ead`](https://github.com/mugoweb/ezpublish-legacy/commit/3774a54ead854dd3e6d525e08b9d77b9853bfef6) (2018) |
| Anleitung | [Zahlen im eingebauten Suchindex](../../../eng-us/guides/operating-a-site.md#numbers-in-the-built-in-search-index) |

#### L35 — Relationen verknüpfter Objekte bleiben aus dem Suchindex heraus

Für **Administratoren**. Der Suchindex lässt die Relationsfelder verknüpfter Objekte weg, und die Relations-Datentypen
brauchen keinen Schutz vor Rekursion. Exponential tut dasselbe seit 2018 (EZP-27101), daher gibt es nichts
einzuschalten und keine Einstellung; eine Website, die von LoveStack kommt, erhält dieselben Suchergebnisse.

| | |
|---|---|
| Test | `kernel/datatypes/eZObjectRelationMetaDataTest.php` (dokumentiert das Verhalten, einschließlich eines Zyklus von Relationen) |
| LoveStack | [`5c089f6250`](https://github.com/mugoweb/ezpublish-legacy/commit/5c089f6250645e2354342deda3a510fed63dcc7f) (2017) |
| Anleitung | [Zahlen im eingebauten Suchindex](../../../eng-us/guides/operating-a-site.md#numbers-in-the-built-in-search-index) |

#### L36 — Der Cronjob hide und der Suchindex

Für **Administratoren** und **Entwickler**. Der Cronjob hide (`hide.php`) teilt der Suchmaschine jeden Knoten mit, den
er versteckt (in Exponential seit 2018), und das Ereignis `content/cache` gibt seinen Listenern von jedem Absender im
Kernel `( $nodeIDs, $objectIDs )`, jetzt auch von der Seite der URL-Aliase eines Knotens; ein Listener, der nur die
Knoten-IDs nimmt, funktioniert weiter.

| | |
|---|---|
| Neue Installation und Upgrade | ein |
| Tests | `kernel/classes/runnable/HideCronjobSearchIndexTest.php`, `kernel/classes/eZContentCacheEventSendersTest.php` |
| LoveStack | [`3298b53584`](https://github.com/mugoweb/ezpublish-legacy/commit/3298b535848044a39e7211f9413711c1b62f97f3), [`89a5a73877`](https://github.com/mugoweb/ezpublish-legacy/commit/89a5a738777f83ec1b219638d06ee24b96c91195) (2018) |
| Anleitung | [Cronjobs](../../../eng-us/guides/cronjobs.md#1-parts-scripts-and-the-global-part) |

### PHP-8-Prüfung und weitere Korrekturen

LoveStack enthält etwa vierzig Korrekturen für PHP 7.2 bis 8.3. Jede genannte Stelle wurde unter PHP 8.5 und 8.1 als
Regressionstest nachgestellt; die meisten waren in Exponential (das unter PHP 8.1 bis 8.5 läuft) bereits behandelt und
bleiben als Tests, die das dokumentieren. Die noch fehlschlagenden sind korrigiert, für jede Installation, ohne dass
bei einem Upgrade etwas zu tun ist:

- die Template-Operatoren `get_class` und `is_class` akzeptieren auch ohne Template-Kompilierung einen Wert, der kein
  Objekt ist (LoveStack [`e828c54709`](https://github.com/mugoweb/ezpublish-legacy/commit/e828c54709a8ed4916f43042d6d1b01c1b8c89b8));
- `wash(email)` eines leeren Wertes, der klassische HTML-Debug-Bericht, die Debug-Leiste und `ezurl` / `eZURI` einer
  Zahl oder von null ergeben keine Deprecation „Passing null“ und keine Warnung (LoveStack
  [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43));
- `eZHTTPTool` kommt mit `$_POST` gleich null zurecht (LoveStack
  [`75cbb1daa2`](https://github.com/mugoweb/ezpublish-legacy/commit/75cbb1daa2804b129f1a3b395dd44d79ebcf667b));
- das Update-Skript `cleanupfieldvaluerelations.php` von 5.4 überspringt leere Relationsdaten, und
  `eZPostgreSQLDB::escapeString( null )` liefert `''` (LoveStack
  [`1bd250d4c6`](https://github.com/mugoweb/ezpublish-legacy/commit/1bd250d4c6cdfeda4d99a44d56bc1cd801ebf935));
- die Bestellungen eines Kunden werden aus den Bestelllisten, dem Archiv, der Kundenliste und dem Dashboard des Shops
  gefunden, wenn die E-Mail-Adresse ein „+“ enthält (LoveStack
  [`5a17c08bfc`](https://github.com/mugoweb/ezpublish-legacy/commit/5a17c08bfc761df3fc0d9ce6d117f3cbc9cfc129)). Ein eigenes
  Design, das auf `shop/customerorderview` verlinkt, kann auf dieselbe Weise `|rawurlencode` an die Adresse anfügen.

Der Teil zur Rechtschreibprüfung von LoveStack
[`cb8a3a1c58`](https://github.com/mugoweb/ezpublish-legacy/commit/cb8a3a1c587c99676e8ffd94c2bd47b4d1562eb2) war bereits in
Exponential und wird jetzt durch einen Test abgesichert. Stelle für Stelle, mit dem Test jeder Stelle:
[PHP-8-Prüfung](love/php8-pruefung.md).

---

## Eine Website von LoveStack umziehen

Eine LoveStack-Datenbank ist der von Exponential sehr ähnlich. Zwei Konsolenbefehle erledigen die Arbeit, die für sie
spezifisch ist:

```sh
./console exp:lovestack check            # read only: what the installation brings along, and what to do with it
./console exp:lovestack migrate          # dry run: prints what it would write
./console exp:lovestack migrate --apply  # records the database as Exponential's
```

- `check` führt auf: die Versionszeile `lovestack` der Datenbank, die Zeile `db-schema-version` von LoveStack, einen
  eindeutigen Index auf `ezuser.login`, eine Spalte `data_text NOT NULL DEFAULT ''`, eine aktive Erweiterung
  `legacy_2018`, Einstellungen, die es nur in LoveStack gibt, in `settings/override`, die Siteaccesses und die
  Erweiterungen (mit dem Namen in Exponential, wo es einen gibt) und das Paket `mugoweb/ezpublish-legacy` in
  `composer.json`. Exit-Code 0: nichts zu tun; 1: etwas braucht Aufmerksamkeit. `--json` für einen maschinenlesbaren
  Bericht.
- `migrate` setzt in `ezsite_data` `ezpublish-version` von `lovestack` auf die Version dieses Release
  (`6.0.15stable`) und trägt die Schemaversion von LoveStack als `lovestack-schema-version` ein (die eigene Zeile von
  LoveStack bleibt). Danach gibt es die Einstellungen und Erweiterungen aus, die von Hand zu ändern sind. Es ändert nie
  Inhalte oder Einstellungen, ein zweiter Lauf ändert nichts, und eine Datenbank, die nicht von LoveStack stammt, wird
  mit einer entsprechenden Meldung abgelehnt.
- Tests: `kernel/classes/expLoveStackCheckTest.php` und `kernel/classes/expLoveStackMigrateTest.php`, auf einer
  Datenbank in der Form von LoveStack, gebaut aus dem Schema von Exponential und den Schemaschritten 1 bis 4 von
  LoveStack. Dieselben Inhaltsoperationen (Veröffentlichen, neue Versionen, Hinzufügen, Verschieben und Entfernen von
  Orten, Kopieren, ein neues Klassenattribut an einer Klasse mit Objekten, der Papierkorb) ergeben denselben Inhalt wie
  auf einer Exponential-Datenbank, sodass die Spalte `data_text NOT NULL` und der eindeutige Index der Anmeldenamen von
  LoveStack bleiben können, wie sie sind.

Der vollständige Weg, von den Backups bis zu Ihrem eigenen Code: [Eine Website von LoveStack umziehen](love/migration.md)
und der Abschnitt [Umstieg von LoveStack](../../anleitungen/aktualisieren.md#umstieg-von-lovestack) der Anleitung zum
Upgrade.

---

## Bereits in Exponential

Diese LoveStack-Funktionen waren bereits in Exponential, gleich oder auf die eigene Weise von Exponential:

| LoveStack-Funktion | In Exponential |
|---|---|
| SameSite-Session-Cookies (2025) | `site.ini [Session] CookieSameSite`, Standard `Lax`, `None` nur mit `Secure`; der Name `SameSite` von LoveStack wird ebenfalls gelesen ([oben](#die-session-einstellung-samesite-von-lovestack-wird-ebenfalls-gelesen)) |
| Passwörter werden nie stillschweigend als MD5 gespeichert | der Standardalgorithmus von PHP, `HashType=php_default` |
| Längere Mindestpasswörter und generierte Passwörter | 10 und 16 Zeichen |
| Erhöhte Passwortsicherheit (EZP-24744) | ja |
| Das Datum, an dem ein Objekt in den Papierkorb kam, und das Sortieren des Papierkorbs danach (EZP-28881) | ja, dieselbe Datenbankspalte |
| WebP-Bilder | ja |
| SQLite-Datenbanken | der eigene SQLite3-Treiber und Schema-Handler von Exponential |
| Log-Zeilen mit Siteaccess und URL | ja, auch unter der Velocity-Engine |
| Maskierte Werte auf der Fehlerseite der Datenbank | die Seite wird von `eZExecution::renderErrorPage()` gerendert, die Werte maskiert |
| `eZSiteData::create()` | ja |
| „Veröffentlichte Version“ in den Objektinformationen | ja |
| Objektzustände (EZP-25089), der Rich-Text-Editor unter PHP 7 | ja |
| Leere Werte in Benutzerentwürfen werden nicht gespeichert (EZP-29096) | ja; ein Entwurf wird nur mit einem freien Anmeldenamen zu einem Konto |
| Versionsdetails in der Administration | Setup > Systeminformationen und Info > Über |
| Unterstützung für PHP 8 | PHP 8.1 bis 8.5 |
| Deprecations für dynamische Eigenschaften (PHP 8.2) | behandelt mit `#[AllowDynamicProperties]` |
| Audit standardmäßig eingeschaltet | das eigene Audit-Protokoll von Exponential ist in jeder Installation eingeschaltet (`settings/audit.ini Audit=enabled`) |

---

## Nicht übernommen, und warum

Einige Änderungen von LoveStack passen zur eigenen Arbeitsweise von LoveStack und wurden in Exponential nicht
gebraucht, oder Exponential löst dasselbe Problem bereits auf andere Weise. Nichts davon ist ein Urteil über die
Arbeit; jeder Punkt hat hier einfach eine andere Antwort.

| Punkt in LoveStack | Warum Exponential es anders macht |
|---|---|
| Die Erweiterung `legacy_2018` (verschiebt `ezenum` aus dem Kernel, behält YUI 2.7.0 und die alten Beispieldesigns) | Exponential behält `ezenum` im Kernel und hat YUI durch seine eigene UI-API ersetzt. Eine Website, die von LoveStack kommt, deaktiviert `legacy_2018` (zwei Klassen `eZEnumType` würden kollidieren) |
| `site.ini [HTTPHeaderSettings] SetContentLengthHeader` | Velocity, FrankenPHP und der HTTP-Cache senden `Content-Length` bereits für das, was sie ausliefern, nach der Kompression |
| `update/run.php` und der Schemazähler `lovestack` | Exponential hat sein Upgrade-SQL pro Release in `update/database/<engine>/`; `exp:lovestack migrate` trägt stattdessen eine LoveStack-Datenbank ein |
| Das Entfernen des dritten Parameters `$searchTypes` von `eZSearch::search()` | würde eine API ändern, die Such-Plugins Dritter verwenden |
| `eZPersistentObject` schreibt `''` für Werte, die keine Zeichenketten sind | die null-sichere Behandlung von Exponential behält Zahlen als Zahlen |
| Neue Formulierung von Debug-Beschriftungen, Konstanten als Array-Schlüssel | Formulierung und Stil; Exponential hat dieselben Dateien auf seine eigene Weise geändert |
| Das Entfernen der Beispiel-Siteaccesses | das Installationsprogramm von Exponential verwaltet seine eigenen Siteaccesses |
| CSS für alte Browser, Travis, Änderungen an composer und README | spezifisch für das eigene Packaging von LoveStack |
| Das Entfernen eines führenden Zeilenumbruchs durch den CSS-Optimierer | der Packer von Exponential arbeitet anders |
| JavaScript-Platzhaltertext ersetzt durch HTML-`placeholder` | abgedeckt durch den Ersatz von YUI in Exponential |
| Rewrite-Regel für Schriften, Beispielkonfiguration für Apache | Exponential dokumentiert seine eigene Einrichtung des Webservers |
| Der Neuaufbau der Spalte `data_text NOT NULL DEFAULT ''` | eine schwere Änderung an der größten Tabelle, die Exponential nicht braucht; eine Datenbank, die sie bereits hat, funktioniert weiter |
| Leeren des Caches von ezjscore nach Alter | die Cache-Werkzeuge von Exponential (`exp:cache`) decken gepackte Dateien ab |
| Einen Autor ohne E-Mail-Adresse akzeptieren (`ezauthor`) | eher eine Verhaltensänderung als eine PHP-8-Korrektur; vorerst lehnt Exponential ihn weiter ab, wie jedes frühere Release |

---

## Wie getestet wurde

Jede der oben genannten Funktionen ist in der PHPUnit-Suite von Exponential abgedeckt. Jede Testdatei läuft für sich,
und abgesehen von einigen Tests, die als datenbankabhängig markiert sind (und zurückrollen, was sie schreiben), laufen
sie ohne Datenbank. Die Tests, die eine Änderung braucht, lassen sich allein ausführen: siehe
[nur die Tests einer Änderung ausführen](../../../eng-us/features/6.0/quality-checks.md#run-only-the-tests-of-a-change).
`kernel/classes/expShippedFeatureDefaultsTest.php` prüft, dass eine aktualisierte Website die Standardwerte behält, die
diese Seite nennt.

Der eigene `tests/`-Baum von LoveStack wurde ebenfalls mit dem von Exponential verglichen: Jeder Testfall, den Mugo Web
seit dem gemeinsamen Vorfahren hinzugefügt oder geändert hat und der Verhalten abdeckt, das Exponential hat, ist in der
Suite, für PHPUnit neu gebaut und gewürdigt. Die vollständige Liste steht in [Tests, Funktion für Funktion](love/testen.md)
und [Die Tests von LoveStack in der Testsuite von Exponential](love/zuordnung-der-tests.md).

---

## Lizenz und Danksagungen

- LoveStack steht unter der GNU GPL Version 2 (seine `LICENSE`, mit dem Urheberrecht von eZ Systems; `composer.json`:
  `GPL-2.0`). Exponential steht unter der GNU GPL Version 2 oder später
  ([GPL-2.0-or-later](https://spdx.org/licenses/GPL-2.0-or-later.html)). Die Änderungen von Mugo Web stehen unter
  derselben GPL Version 2 und dürfen daher mit Exponential kombiniert werden; die davon abgeleiteten Teile werden unter
  den Bedingungen der GPL Version 2 verbreitet.
- Jede Funktion auf dieser Seite wird als von der Arbeit von Mugo Web abgeleitet behandelt, auch dort, wo sie neu
  gebaut statt kopiert wurde. Jede Datei mit abgeleitetem Code behält jeden vorhandenen Urheberrechtshinweis und fügt
  die Zeile
  `Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit <sha>), Copyright (C) Mugo Web, GPL v2` hinzu;
  der LoveStack-Commit wird in der Commit-Nachricht und im
  [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#lovestack-features-from-2026-10-12) genannt.
- Die Bedienelemente für Custom Tags (L23) folgen dem Entwurf des Editor-Plugins `custom_tags_controls` von LoveStack,
  "Copyright 2014, Mugo Web"; dieser Hinweis wandert mit den abgeleiteten Teilen.
- Die Formulierung der Doppellizenz in der ursprünglichen README von eZ Systems kann immer nur von eZ Systems für den
  Code von eZ Systems gewährt werden; sie gibt keine Rechte am Code von Mugo Web oder von Exponential.
- „LoveStack“ ist der Name, den Mugo Web für seine Distribution verwendet. Exponential verwendet ihn nur, um zu
  beschreiben, woher eine Funktion stammt; keine Klasse, keine Einstellung und kein Produkt in Exponential trägt ihn,
  abgesehen vom Befehl `exp:lovestack` für Websites, die von dort umziehen. Nichts hier impliziert, dass Mugo Web
  Exponential unterstützt.

**Danke an Mugo Web** für Jahre sorgfältiger Arbeit am Legacy-Kernel, dafür, ihre Schemaschritte so genau zu
dokumentieren, dass eine LoveStack-Datenbank erkannt und umgezogen werden kann, und dafür, alles als Open Source zu
veröffentlichen. Diese Seite gibt es wegen dieser Arbeit.

---

## Weitere Dokumente

| Dokument | Für |
|---|---|
| [love/README.md](love/README.md) | der Index der ausführlichen Seiten |
| [love/erste-schritte.md](love/erste-schritte.md) | alle: betrifft mich das, und wo fange ich an |
| [love/funktionen.md](love/funktionen.md) | jede Funktion mit ihrem Standard bei einem Upgrade und bei einer neuen Installation, in einer Tabelle pro Bereich |
| [love/api.md](love/api.md) | Klassen, Methoden, Einstellungen, Template-Funktionen und Konsolenbefehle, für Entwickler |
| [love/migration.md](love/migration.md) | eine LoveStack-Website Schritt für Schritt auf Exponential umziehen |
| [love/testen.md](love/testen.md) | die Tests jeder Funktion und wie Sie sie ausführen |
| [love/zuordnung-der-tests.md](love/zuordnung-der-tests.md) | die eigenen Testfälle von LoveStack und wo sie in der Suite von Exponential stehen |
| [love/php8-pruefung.md](love/php8-pruefung.md) | die Korrekturen von LoveStack für PHP 7.2 bis 8.3, Stelle für Stelle |
| [love/l01-aufrufer.md](love/l01-aufrufer.md) | wer `ezjscnode` aufruft und was das Einschalten von L01 ändert |
| [love/faq.md](love/faq.md) | häufige Fragen |
| [love/roadmap.md](love/roadmap.md) | wie die Arbeit gemacht wurde und was als Nächstes kommt |
| [love/changelog.md](love/changelog.md) | die Releases, die diese Funktionen enthalten |
