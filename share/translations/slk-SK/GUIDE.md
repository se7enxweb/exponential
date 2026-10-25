# Slovak (slk-SK): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Slovak (spisovná slovenčina) as codified for Slovakia, with the terminology of the Slovak versions of
common operating systems and office software (Microsoft terminology). No other locale falls back to this
catalogue. The tone is neutral, concise and professional. Avoid colloquial forms, Czech words and Bohemisms
(not "užívateľ", "náhľad" for preview, "zásada" for policy), and needless English words. The user is always
"používateľ", never "užívateľ".

## 2. Form of address

- **Buttons, menu items, links and action labels** use the infinitive, as in Microsoft software: "Uložiť",
  "Odstrániť", "Upraviť", "Pridať", "Zrušiť", "Vytvoriť nový", "Použiť zmeny", "Odoslať na publikovanie",
  "Vybrať všetko", "Uložiť koncept", "Zahodiť koncept", "Vyprázdniť kôš". Not the imperative ("Uložte").
- **Titles, column headings and tabs** are nouns: "Štruktúra obsahu", "Používateľské kontá", "Typ rozloženia",
  "Nastavenia oznámení", "Stavy objektov".
- **Sentences, hints, questions and error messages** address the user with the formal **vy**, written in lower
  case (vy, vás, váš, vaše, vám), and the plural imperative for instructions:
  - Question: "Naozaj chcete odstrániť túto triedu?"
  - Request: "Vyberte umiestnenie." / "Zadajte platnú e-mailovú adresu."
  - Statement: "Nemáte povolenie odstraňovať triedy."
  - Instruction: "Kliknite na tlačidlo „Zrušiť“ a skúste odstrániť iba umiestnenia, ktoré môžete odstrániť."
  - Hint: "Nastavenia histórie verzií môžete zmeniť v súbore content.ini …"
  - Error message: "Koncept sa nepodarilo uložiť.", "Požiadavka sa nedostala na server. Skontrolujte pripojenie a
    skúste to znova."
- **Tooltips that describe a click** use "Kliknutím …": "Kliknutím na ikonu zobrazíte kontextovú ponuku."
- **Status messages** are passive or impersonal: "Blok bol odstránený.", "Vaše voľby boli uložené.",
  "Nenašiel sa žiadny beh úloh cron", "Prebieha kontrola…".
- **Confirmations**: "Naozaj chcete …?": "Naozaj chcete zrušiť prepojenie tejto zóny?"
- Not allowed: the familiar form (ty, tvoj, chceš, môžeš, ulož); "Vy/Vás/Váš" with a capital letter inside a
  sentence; the infinitive as an instruction inside a sentence ("Použiť toto pole na …" is wrong; write "Toto pole
  slúži na …"); "prosím" in front of every request (use it sparingly; "Vyberte umiestnenie." is enough); the
  imperative on buttons.

## 3. Script and typography

- **Script**: Latin with the full set of Slovak diacritics (á ä č ď é í ĺ ľ ň ó ô ŕ š ť ú ý ž). Mind the rhythmic
  rule (no two long syllables in a row: "krásny", not "krásný") and ä/ô.
- **Capitalization**: sentence case everywhere: "Štruktúra obsahu", "Knižnica médií", "Stavy objektov". No English
  title case. Product names (Exponential, Solr, Exponential Layouts) keep their spelling.
- **Quotation marks**: Slovak quotes „…“ (U+201E, U+201C) in running text: „%name“. Straight quotes only inside
  HTML attributes, JavaScript or where the source quotes code or a value typed exactly.
- **Punctuation**: no space before ":", ";", "?" and "!". Ellipsis "…". Dash in a sentence: the en dash with spaces
  " – ".
- **Plural forms**: Slovak has three (1; 2–4; 5 and more, also 0), the source only "item(s)". Never write
  "položka(y)" or "položky(iek)". Use a label form with the placeholder after a colon: "Objekty: %count",
  "Pracovné procesy: %count", "Odstránené koncepty: %count". Where the number is known to be 1 or a clear singular,
  use the singular.
- **Placeholders are never declined.** Slovak would decline a name ("objektu Domov"); a placeholder cannot take an
  ending, and a Latin letter glued to it breaks it (`%namea` is a different placeholder). Put a noun in front that
  carries the case and leave the placeholder in the nominative: "Upraviť sekciu %name", "Trieda <%class_name>",
  "Verzie objektu „%name“", "Jazyky: %count". Never attach an ending with a hyphen ("%name-u") – that is not
  Slovak usage.
- **Numbers**: digits 0–9; decimal comma in prose ("1,5 MB"); thousands separated by a non-breaking space
  ("10 000"). Units after a number with a space: "min", "h", "s", "MB"; "pred %n min". Dates in the form
  "23. 10. 2026" when the text writes them out.
- **Compounds with codes**: Slovak uses phrases, not hyphenated compounds: "súbor INI", "značka XML",
  "adresa URL", "alias URL", "vyrovnávacia pamäť INI". "e-mail", "e-mailová adresa" with a hyphen.

## 4. Glossary

Binding for the kernel and every extension. Where a word depends on its context (TRANSLATING.md, "Words that need
their context"), the note says which meaning takes which word.

| English | Slovak | Note |
|---|---|---|
| content | obsah | |
| content object / object | objekt obsahu / objekt | |
| item (list entry, general) | položka | pl. položky |
| node | uzol | main node: hlavný uzol |
| location / main location | umiestnenie / hlavné umiestnenie | |
| content class / class | trieda obsahu / trieda | |
| class group | skupina tried | |
| attribute | atribút | |
| datatype | typ údajov | |
| version | verzia | version history: história verzií |
| draft | koncept | Store draft: Uložiť koncept; Discard draft: Zahodiť koncept |
| publish / published / publishing | publikovať / publikované / publikovanie | Send for publishing: Odoslať na publikovanie |
| translation / language | preklad / jazyk | |
| main language / content language | hlavný jazyk / jazyk obsahu | interface language: jazyk rozhrania |
| siteaccess | prístup k lokalite | pl. prístupy k lokalite; column abbreviation SA: PL |
| site / website | lokalita / webová lokalita | |
| design (eZ design) | vzhľad | |
| structure | štruktúra | |
| template | šablóna | template file: súbor šablóny |
| override (template, setting) | prepísanie | override template: prepisujúca šablóna; overridden: prepísaný |
| fallback | záložný | fallback language: záložný jazyk; fallback chain: záložný reťazec |
| alias / URL alias | alias / alias URL | |
| wildcard (URL) | zástupný znak | URL wildcard: zástupný znak URL; the markers {1}/{2} and rich-text markers: zástupný symbol |
| redirect / forward (URL) | presmerovanie / presmerovať | Redirecting: Presmerovanie; Forward: Presmerovať; Direct: Priamo |
| cache | vyrovnávacia pamäť | Clear cache: Vymazať vyrovnávaciu pamäť |
| workflow | pracovný postup | workflow event: udalosť pracovného postupu |
| trigger / event | spúšťač / udalosť | |
| role | rola | pl. roly |
| policy | politika | pl. politiky (as in "skupinová politika") |
| limitation (policy) | obmedzenie | |
| permission / access | povolenie / prístup | access denied: prístup odmietnutý |
| privilege (DB) / grant | oprávnenie / udeliť | |
| user / user group | používateľ / skupina používateľov | never "užívateľ" |
| user account / user accounts | používateľské konto / používateľské kontá | |
| anonymous user | anonymný používateľ | |
| administrator / admin interface | správca / rozhranie správy | |
| log in / log out | prihlásiť sa / odhlásiť sa | |
| login (field) / username | prihlasovacie meno / používateľské meno | |
| password | heslo | |
| e-mail | e-mail | e-mail address: e-mailová adresa |
| section | sekcia | |
| object state / state group | stav objektu / skupina stavov | |
| relation / related objects | väzba / súvisiace objekty | reverse related: objekty odkazujúce na tento objekt |
| trash | kôš | Empty trash: Vyprázdniť kôš |
| bookmark | záložka | |
| subtree | podstrom | |
| parent / sub-items / children | nadradená položka / podradené položky / podradené položky | |
| owner / creator / modifier | vlastník / autor / autor zmeny | |
| created / modified | vytvorené / zmenené | column headings |
| layout (Exponential Layouts) | rozloženie | layout type: typ rozloženia; the name Exponential Layouts stays |
| layout (directory or release structure) | štruktúra | directory layout: štruktúra adresárov |
| block / zone | blok / zóna | |
| setting / settings | nastavenie / nastavenia | configuration values, plural as a title |
| Setup (admin tab) | Nastavenie | the tab; "Settings" as a title: Nastavenia |
| configuration / configure | konfigurácia / konfigurovať | |
| Content structure | Štruktúra obsahu | |
| Media library | Knižnica médií | |
| Dashboard | Nástenka | |
| extension | rozšírenie | |
| package | balík | |
| module / view / function | modul / zobrazenie / funkcia | the module view is a noun: zobrazenie modulu |
| View (button) | Zobraziť | |
| search | hľadať | noun: vyhľadávanie; search engine: vyhľadávací nástroj; web search engines (Google etc.): vyhľadávače |
| search index / reindex / index (verb) | index vyhľadávania / preindexovať / indexovať | noun: preindexovanie |
| index (DB) | index | index file: indexový súbor |
| cronjob | úloha cron | |
| schedule | plán / naplánovať | scheduled: naplánované |
| job / queue / worker | úloha / front / pracovný proces | job queue: front úloh |
| task | úloha | |
| lane | pruh / vetva | queue lane: pruh frontu; retrieval lane (vector, BM25): vetva |
| pool (processes, threads) | fond | worker pool: fond pracovných procesov |
| log / log file | denník / súbor denníka | always the record; never the verb "log in" |
| audit / audit trail | audit / záznam auditu | |
| rotation / rotate | rotácia / rotovať | rotated log: rotovaný súbor denníka |
| notification | oznámenie | |
| collaboration | spolupráca | |
| preview | ukážka | |
| ID / remote ID / identifier | ID / vzdialené ID / identifikátor | |
| file / folder / directory | súbor / priečinok / adresár | folder for content, directory for the file system |
| image / media | obrázok / médiá | |
| thumbnail | miniatúra | |
| upload / download | nahrať / stiahnuť | |
| export / import | exportovať / importovať | noun: export / import |
| shop | obchod | |
| Store (shop tab) | Obchod | the shop tab of the administration header only |
| store / save | uložiť | every "Store" except the shop tab |
| basket | košík | |
| order (shop) | objednávka | order status: stav objednávky; order list: zoznam objednávok |
| order (sorting) | poradie | sort order: poradie zoradenia |
| customer / product | zákazník / produkt | |
| checkout | dokončenie objednávky | |
| VAT / VAT type | DPH / typ DPH | |
| discount / discount rule | zľava / pravidlo zľavy | |
| rule | pravidlo | |
| price / currency | cena / mena | |
| wish list | zoznam želaní | |
| payment gateway / gateway | platobná brána / brána | |
| newsletter | bulletin | |
| subscriber / subscription / subscribe | odberateľ / odber / prihlásiť sa na odber | unsubscribe: odhlásiť sa z odberu |
| tag (keyword, eztags) / keyword | štítok / kľúčové slovo | pl. štítky |
| tag (HTML/XML) / custom tag | značka / vlastná značka | XML tag: značka XML |
| move / copy / swap | premiestniť / kopírovať / vymeniť | move within the tree, a group or a state |
| embedding (vector) / chunk / vector | vektorová reprezentácia / časť / vektor | embedding content in a framework: vkladanie obsahu |
| embed / embedded (object in text) | vložiť / vložený | |
| prompt / model / token | výzva / model / token | |
| AI | AI | umelá inteligencia where spelled out |
| deploy / deployment / release | nasadiť / nasadenie / vydanie | |
| enable / disable | povoliť / zakázať | enabled / disabled: povolené / zakázané |
| active / inactive | aktívny / neaktívny | |
| warning / error / notice | upozornenie / chyba / oznámenie | notice also as log level and PHP notice; "Notice:" in front of an installer hint: Poznámka: |
| required / optional | povinné / voliteľné | |
| default / priority | predvolený / priorita | default value: predvolená hodnota |
| sort / ascending / descending | zoradiť / vzostupne / zostupne | |
| visible / hidden | viditeľné / skryté | |
| hide / show / reveal | skryť / zobraziť / zobraziť | |
| expand / collapse | rozbaliť / zbaliť | |
| OK / Cancel / Yes / No | OK / Zrušiť / Áno / Nie | |
| Back / Next / Previous / Close | Späť / Ďalej / Predchádzajúci / Zavrieť | "Ďalej" on a wizard button; "ďalší" as an adjective |
| select / browse | vybrať / prehľadávať | |
| Select all / Deselect all / Select none | Vybrať všetko / Zrušiť výber / Zrušiť výber | |
| tick (checkbox) | začiarknuť | untick: zrušiť začiarknutie |
| apply | použiť | Apply changes: Použiť zmeny |
| discard | zahodiť | |
| edit | upraviť | noun: úprava |
| remove | odstrániť | taking out of a membership (group, role, class group, relation, assignment): odobrať; a list entry that is then gone (basket, list, rows, queue): odstrániť |
| delete | odstrániť | destroy for good: natrvalo odstrániť |
| new / create / add | nový / vytvoriť / pridať | Create new: Vytvoriť nový |
| refresh / update / reset | obnoviť / aktualizovať / vynulovať | reset to defaults: obnoviť predvolené; password reset: obnovenie hesla |
| restore / assign / assignment | obnoviť / priradiť / priradenie | |
| run (verb) / run (noun) | spustiť / beh | dry run: skúšobný beh; cron run: beh úloh cron |
| self-test | samotest | |
| statistics / system information | štatistika / informácie o systéme | |
| database | databáza | |
| path | cesta | |
| handler / wizard | obslužný program / sprievodca | |
| backend / provider / endpoint | back-end / poskytovateľ / koncový bod | INI keys and class names stay as written |
| method / listener / controller | metóda / prijímač udalostí / kontrolér | |
| allowlist / read-only / argument | zoznam povolených / iba na čítanie / argument | |
| slot / collection / facets / elevate | slot / kolekcia / fazety / zvýhodnenie | collection in Solr and MongoDB; Solr elevate: zvýhodnenie výsledkov |
| filter / details | filter / podrobnosti | |
| container (class, "Is container") | kontajner | is container: je kontajner |
| container (Bootstrap/CSS) | kontajner | |
| locale / localization | miestne nastavenie / lokalizácia | heading "Locales": Miestne nastavenia |
| Approve / Approval / Approved / Approver | schváliť / schválenie / schválené / schvaľovateľ | |
| Deny / denied | odmietnuť / odmietnuté | |
| Reject / Rejected | zamietnuť / zamietnuté | a human decision (review, approval, plan, registration); a server, validator or service refusing a request, token, file or line: odmietnuť / odmietnuté |
| Accept / Accepted | prijať / prijaté | |
| Pending | čaká na vybavenie | pending items: položky čakajúce na vybavenie; Queued: Vo fronte |
| charset | znaková sada | |
| license | licencia | |
| format / formatting | formát / formátovanie | |
| linked / common | prepojený / spoločný | |
| flags (class flags) | príznaky | |
| teaser | upútavka | |
| slug | slug | the URL part of a name; explain once as "časť URL" where needed |
| placeholder | zástupný text | |
| preset | predvoľba | |
| information collection / collector | zber informácií / zberač informácií | collected information: zozbierané informácie; one sending of a collector: zber (pl. zbery) |
| overview / summary | prehľad / súhrn | |
| digest (e-mail) | súhrnná správa | |
| tab | karta | |
| rich text | formátovaný text | |
| feed | informačný kanál | RSS feed: informačný kanál RSS |
| session | relácia | |
| scope | rozsah | |
| thread / chain | vlákno / reťazec | conversation thread and process thread alike |
| batch | dávka | |
| schema / registry / manifest | schéma / register / manifest | |
| stylesheet / theme / icon | šablóna štýlov / motív / ikona | |
| port | port | |
| profile | profil | |
| core (Solr) | jadro | Solr core: jadro Solr |
| kernel | jadro systému | |
| editor (person) / editor (software) | redaktor / editor | template editor: editor šablón |
| Editor (siteaccess kind) | redakčné rozhranie | |
| user interface | používateľské rozhranie | |
| page / pages | stránka / stránky | |
| link | odkaz | hyperlink in content: odkaz |
| title / name / description | názov / názov / popis | a person's name: meno |
| date / time | dátum / čas | |
| status / type | stav / typ | |
| group / list | skupina / zoznam | |
| value / field | hodnota / pole | |
| number / count | číslo / počet | |
| width / height / size | šírka / výška / veľkosť | |
| server / client / request / response | server / klient / požiadavka / odpoveď | |
| archived (version, object, order) | archivované | |
| Archive / Unarchive (button) | Archivovať / Zrušiť archiváciu | |
| archive (place, menu item, log archive) | archív | |
| archive (file .tar/.zip/.phar/.ezpkg) | archív | archive file: súbor archívu |
| pattern | vzor | |
| repository / storage | úložisko | package and git repository, file storage (var/storage); where both meet in one sentence, storage is ukladací priestor |
| maintainer | správca balíka | |
| install / uninstall / installation | inštalovať / odinštalovať / inštalácia | |
| checksum | kontrolný súčet | |
| quarantine | karanténa | |
| mapping | mapovanie | |
| resolve / resolvable / resolver | rozlíšiť / rozlíšiteľný / rozlišovač | path resolution: rozlíšenie cesty |
| checkpoint | kontrolný bod | |
| child documents (Solr) | podradené dokumenty | |
| script | skript | |
| revoke | odvolať | |
| label | označenie | the caption of a field, button or block |
| plugin | zásuvný modul | |
| debug | ladenie | debug output: ladiaci výstup |

## 5. Decisions after the translation

These decisions were made after the first translation and complete the glossary.

| English | Slovak | Note |
|---|---|---|
| query (database, Solr, search) | dotaz | pl. dotazy, never "dopyt"; search query: hľadaný výraz or vyhľadávací dotaz |
| upgrade (Exponential, schema, table, extension) | inovácia / inovovať | as in Microsoft software; update stays aktualizácia / aktualizovať, and so does upgrading PHP |
| Resume (after Pause) | Pokračovať / pokračovanie | "Obnoviť" is already refresh and restore |
| Pause / paused | Pozastaviť / pozastavené | |
| page layout (classic pagelayout.tpl) | rozvrhnutie stránky | kept apart from rozloženie (Exponential Layouts) |
| database engine / SQL engine | databázový stroj | not "databázový nástroj" (a tool) |
| template engine | šablónový systém | |
| engine (Velocity server, online editor) | modul | "Predvolený modul", "Moduly editora" |
| storage engine (MySQL) | úložný mechanizmus | |
| probe (live, remote, model probe) | test / test naživo | remote probes: vzdialené testy |
| probe script (protocol probe file) | sonda | |
| purge (cache pages) | vyprázdniť / vyprázdnenie | |
| purge (trash, object, order, audit records) | natrvalo odstrániť / trvalé odstránenie | not "vyčistiť" |
| purge (cluster purge cronjob, InnoDB purge) | čistenie | |
| prune (archives, threads) | prečistiť / prečistenie | |
| staging (environment, site) | predprodukčné prostredie / predprodukčná lokalita | the badges DEV/TEST/STAGE/PROD stay codes |
| staged (proposed changes) | pripravené | staged review: kontrola pripravených zmien |
| stage (step, level) | fáza / úroveň | "Úroveň 1 – bežné oprávnenia" |
| toolbar | panel nástrojov | not "panel s nástrojmi" |
| toggle (editor button) | zapnúť alebo vypnúť | on/off as a state: zap. / vyp. |
| drag (to arrange) | presunúť (myšou) | handle, grip: úchyt |
| hit / miss / hit rate (cache) | zásah / výpadok / úspešnosť | |
| issue (GitHub, tracker) | hlásenie | issue tracker: systém na sledovanie hlásení; any other issue: problém |
| commit (git) | commit | commit() as a method stays |
| machine (server, workstation) | počítač | |
| stub (imported placeholder object) | útržok | pl. útržky |
| copyright notice / license notice | upozornenie na autorské práva / upozornenie na licenciu | fixed phrases; otherwise upozornenie is warning only |
| privacy notice | vyhlásenie o ochrane osobných údajov | |
| Hostname | Názov hostiteľa | |
| orphan / orphaned | osirelý / osirelé | |
| warm (cache) / preload | zahriať / predbežné načítanie | |
| drift check | kontrola odchýlok | |
| retention / zero retention | uchovávanie, doba uchovávania / nulové uchovávanie | |
| budget / held by a budget | rozpočet / zadržané (rozpočtom) | |
| severity / actor / fingerprint | závažnosť / aktér / odtlačok | |
| sink (audit) | výstup | pl. výstupy; "Audit sink failed": Zlyhanie výstupu auditu; not "cieľ" |
| hash / hash chain | haš / reťazec hašov | "Haš dotazu"; hash() in template code stays |
| rollback | návrat k predchádzajúcemu vydaniu | |
| two-step sign-in / authenticator app | dvojstupňové prihlásenie / overovacia aplikácia | |
| viewer / log viewer | zobrazovač / zobrazovač denníkov | not "prehliadač", which is a web browser |
| bounce / hard bounce | nedoručenie / trvalé nedoručenie | bounce mailbox: schránka nedoručených správ |
| suppression list | zoznam blokovaných adries | |

Three rules go with these decisions:

1. **A placeholder for a siteaccess stands behind its noun** in a sentence: "pre prístup k lokalite %siteaccess".
   "%siteaccess" alone only in a label with a colon: "Prístup k lokalite: %siteaccess".
2. **"Obnoviť" has two meanings** (refresh, restore). It is not used for a third: reset is "vynulovať" except the
   fixed phrases "obnoviť predvolené" and "obnovenie hesla"; resume is "pokračovať".
3. **"Upozornenie" is warning.** Notice is "oznámenie", an installer "Notice:" is "Poznámka:".
