# Maltese (mlt-MT): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Maltese (il-Malti standard) as written in Malta, following the orthography of the Kunsill Nazzjonali
tal-Ilsien Malti (Deċiżjonijiet 1, 2008) and the terminology of the Maltese versions of common operating systems,
office software and EU texts. No other locale falls back to this catalogue. The tone is neutral, short and
professional. Avoid English words where an established Maltese word exists ("Ħassar", not "Iddilitja"; "Fittex",
not "Searchja"), but keep the English loans that are the normal Maltese IT vocabulary and are spelled as in
English in Maltese software and media: "cache", "server", "email", "link", "browser", "URL", "ID", "token".
Words of Italian or English origin that Maltese has adapted are written in Maltese spelling: "fajl", "kompjuter",
"dejta", "folder", "kju".

## 2. Form of address

The user is addressed in the **singular (int)**, as Maltese software and public online services do. Politeness
comes from "jekk jogħġbok" (please) and from neutral wording, not from the plural.

- **Buttons, menu items, links and action labels** use the imperative singular: "Salva", "Ħassar", "Editja",
  "Żid", "Ikkanċella", "Oħloq ġdid", "Applika l-bidliet", "Ibgħat għall-pubblikazzjoni", "Agħżel kollox",
  "Neħħi l-magħżula", "Armi l-abbozz", "Battal il-barmil".
- **Titles, column headings and tabs** are nouns: "Struttura tal-kontenut", "Kontijiet tal-utenti",
  "Tip ta' tqassim", "Issettjar tan-notifiki", "Lingwa ewlenija", "Stati tal-oġġetti".
- **Requests** use the imperative singular with "jekk jogħġbok", normally at the end:
  - "Agħżel post, jekk jogħġbok."
  - "Daħħal indirizz tal-email validu, jekk jogħġbok."
- **Instructions in hints and messages** use the imperative singular without "jekk jogħġbok" when the sentence is
  a plain instruction: "Iċċekkja l-konnessjoni u erġa' pprova."
- **Questions and confirmations**: "Żgur li trid ...?" or "Trid tassew ...?":
  - "Żgur li trid tneħħi din il-klassi?"
  - "Trid tassew tneħħi s-sessjonijiet ta' dan l-utent?"
- **Statements about the user** use the second person singular: "M'għandekx permess tneħħi l-klassijiet.",
  "Tista' tibdel l-issettjar tal-istorja tal-verżjonijiet fil-fajl content.ini ...",
  "L-indirizz tal-email tal-kont tiegħek qed jinbidel."
- **Tooltips that describe a click**: "Ikklikkja ... biex ...": "Ikklikkja fuq l-ikona biex tara l-menu
  tal-kuntest."
- **Error messages** state what failed, impersonally or with the passive: "Il-fajl ma setax jiġi ssalvat.",
  "Ma nstab l-ebda element.", "Żball: iddaħħlu valuri invalidi; dawn huma mmarkati bl-aħmar."
- **Status messages** are passive or impersonal: "Dan l-oġġett tħassar u m'għadux disponibbli", "Il-bidliet ġew
  issalvati.", "Notifiki mneħħija: %count."
- Not allowed: the plural form of address (intom, tagħkom, agħżlu, ikklikkjaw) mixed into the catalogue; the
  noun on a button where the source has a verb ("Tħassir" for "Delete"); English verbs with Maltese endings
  where a Maltese verb exists ("ikklikkja" is correct and established, "iddawnlowdja" is not, write "niżżel").

## 3. Script and typography

- **Script**: Latin script with the Maltese letters ċ ġ ħ ż and the digraphs għ and ie; capitals Ċ Ġ Ħ Ż, "Għ",
  "Ie". Never replace them with c g h z. The apostrophe marks an elided consonant or vowel and is part of the word:
  "ta'", "ma'", "Aqta'", "erġa'", "Tista'". The catalogue uses the straight
  apostrophe ', as Maltese software does.
- **The definite article** is written with a hyphen and assimilates before the sun letters (ċ d n r s t x ż z):
  "il-fajl", "is-server", "iċ-ċavetta", "id-dejta", "in-nodu", "ir-rwol", "it-tip", "ix-xogħol", "iż-żona",
  "iz-zona". Before a vowel: "l-oġġett", "l-utent"; after a vowel: "tal-", "fil-", "għall-". English loans and
  codes follow their pronunciation: "il-cache", "is-server", "il-URL", "l-ID". "Siteaccess" itself is not used in running text (see glossary:
  "aċċess għas-sit").
- **Capitals**: sentence case. Only the first word of a button, title or heading starts with a capital; at the
  start of a sentence the article is capitalized and the noun stays small: "Il-kontenut", "L-oġġetti". Months
  and days are capitalized in Maltese (Jannar, It-Tnejn), as are language and nationality names (il-Malti,
  l-Ingliż). No English title case.
- **Quotation marks**: “…” (U+201C, U+201D) in running text: “%name”. Straight quotes only where the source quotes
  code or a value that is typed exactly, and inside HTML attributes and JavaScript.
- **No space** before ":", ";", "?" and "!".
- **Plural and number forms**: Maltese changes the noun after a number (1: oġġett; 2: żewġ oġġetti; 3–10:
  plural, "tliet oġġetti"; 11 and above: singular again, "ħdax-il oġġett"; numbers 11–19 and some others take
  "-il" before the noun). The source only has "item(s)". Never write "oġġett(i)". Use a label form with the
  placeholder after a colon: "Oġġetti: %count", "Abbozzi mneħħija: %count", "Proċessi tax-xogħol: %count".
- **Placeholders and the article**: a placeholder is never declined and never carries a glued article or ending.
  Do not write "il-%name" or "%name-hom"; put a Maltese noun in front: "l-oġġett %name", "il-klassi <%class_name>",
  "Editja t-taqsima %name". The genitive is built with "ta'" before a free placeholder: "is-sessjonijiet ta'
  %name", or with a label form: "Kont tal-utent: %name". Nothing Latin may follow a placeholder directly (`%namet` is a
  different placeholder); the next character is a space or punctuation.
- **Numbers**: digits 0–9, the decimal point and the thousands comma as in Malta ("1.5 MB", "1,250"). Time units
  after a number: "min", "s", "siegħa/sigħat" written out ("%n min ilu", "%n s").
- **Dashes**: en dash with spaces – for a break in a sentence; the em dash as in the source.

## 4. Glossary

Binding for the kernel and every extension. Where a Maltese term has two English meanings, the note says which
one the row is for.

| English | Maltese | Note |
|---|---|---|
| content | kontenut | |
| content object / object | oġġett tal-kontenut / oġġett | pl. oġġetti |
| item (list entry, general) | element | pl. elementi |
| node | nodu | pl. nodi; main node: nodu ewlieni |
| location (node placement) / main location | post / post ewlieni | pl. postijiet |
| content class / class | klassi tal-kontenut / klassi | pl. klassijiet |
| class group | grupp ta' klassijiet | |
| attribute | attribut | pl. attributi |
| datatype | tip ta' dejta | pl. tipi ta' dejta |
| version | verżjoni | pl. verżjonijiet |
| draft | abbozz | pl. abbozzi; Store draft: Salva l-abbozz; Discard draft: Armi l-abbozz |
| publish / published / publishing | ippubblika / ippubblikat / pubblikazzjoni | Send for publishing: Ibgħat għall-pubblikazzjoni |
| translation / language | traduzzjoni / lingwa | |
| main language / content language | lingwa ewlenija / lingwa tal-kontenut | interface language: lingwa tal-interfaċċja |
| siteaccess | aċċess għas-sit | pl. aċċessi għas-sit; never "siteaccess" in running text |
| site / website | sit / sit web | |
| design (eZ design) | disinn | |
| structure | struttura | |
| template | mudell | pl. mudelli; template file: fajl tal-mudell |
| override (template or setting) | sostituzzjoni | override template: mudell ta' sostituzzjoni; overridden: sostitwit |
| fallback (language, design) | riżerva | fallback language: lingwa ta' riżerva; fallback chain: katina ta' riżerva |
| alias / URL alias | alias / alias tal-URL | |
| cache / clear cache | cache / Battal il-cache | il-cache; cache management: ġestjoni tal-cache |
| workflow | fluss tax-xogħol | pl. flussi tax-xogħol |
| workflow event / event | avveniment tal-fluss tax-xogħol / avveniment | |
| trigger | attivatur | pl. attivaturi |
| role | rwol | pl. rwoli |
| policy | politika | pl. politiki |
| limitation (policy) | limitazzjoni | |
| permission / access | permess / aċċess | access denied: aċċess miċħud |
| user / user group | utent / grupp ta' utenti | |
| user account / user accounts | kont tal-utent / kontijiet tal-utenti | |
| anonymous user | utent anonimu | |
| administrator / admin interface | amministratur / interfaċċja tal-amministrazzjoni | |
| log in, sign in / log out | idħol / oħroġ | noun: dħul / ħruġ; failed sign-ins: tentattivi ta' dħul falluti |
| login (field) / username | isem tal-utent | |
| password | password | il-password, pl. passwords |
| e-mail | email | e-mail address: indirizz tal-email |
| section | taqsima | pl. taqsimiet; only the Exponential section (an INI section: see section 5) |
| object state / state | stat tal-oġġett / stat | pl. stati; state group: grupp ta' stati |
| relation / related objects | relazzjoni / oġġetti relatati | reverse related: oġġetti li jirreferu għal dan |
| trash | barmil | Empty trash: Battal il-barmil |
| bookmark | favorit | pl. favoriti |
| sub-items / children | sottoelementi | |
| parent | ġenitur | parent node: nodu ġenitur |
| owner | sid | |
| creator / modifier | min ħoloq / min immodifika | column headings: Ħallieq / Modifikatur |
| created / modified | maħluq / immodifikat | column headings |
| layout (Exponential Layouts) | tqassim | layout type: tip ta' tqassim; the name Exponential Layouts stays |
| layout (directory or release structure) | struttura | |
| block / zone | blokk / żona | |
| setting / settings | issettjar | settings (configuration) is never "tqassim" |
| Setup (admin tab) | Konfigurazzjoni | |
| configuration / configure | konfigurazzjoni / ikkonfigura | |
| Content structure | Struttura tal-kontenut | |
| Media library | Librerija tal-midja | |
| Dashboard | Pannell | |
| extension | estensjoni | pl. estensjonijiet |
| package | pakkett | pl. pakketti |
| module | modulu | pl. moduli |
| view (noun, module view) | veduta | Module view: veduta tal-modulu |
| view (button) | Ara | |
| function | funzjoni | |
| search (verb / noun) | fittex / tfittxija | search engine: magna tat-tfittxija |
| search index / reindex | indiċi tat-tfittxija / reindiċizzazzjoni | index (verb): indiċizza |
| index (database) | indiċi | |
| index file | fajl tal-indiċi | |
| cronjob | kompitu cron | pl. kompiti cron |
| schedule | skeda | |
| job | kompitu | pl. kompiti |
| queue | kju | job queue: kju tal-kompiti |
| worker | proċess tax-xogħol | pl. proċessi tax-xogħol |
| task | ħidma | pl. ħidmiet |
| log / log file | reġistru / fajl tar-reġistru | a log file, never "log in" |
| audit / audit trail | awditu / traċċa tal-awditu | |
| notification | notifika | pl. notifiki |
| collaboration | kollaborazzjoni | |
| preview | dehra minn qabel | button: Ara minn qabel |
| ID / remote ID / identifier | ID / ID remot / identifikatur | l-ID |
| file / folder / directory | fajl / folder / direttorju | pl. fajls / folders / direttorji |
| image / media | immaġni / midja | pl. immaġnijiet |
| thumbnail | minjatura | |
| upload / download | tella' / niżżel | nouns: tlugħ / tniżżil |
| shop | ħanut | |
| Store (shop tab) | Ħanut | every other "Store" is the verb "Salva" |
| basket | qoffa tax-xiri | |
| order (shop) | ordni | pl. ordnijiet; order status: status tal-ordni |
| order (sorting) | sekwenza | sort order: sekwenza tas-sortjar |
| customer / product | klijent / prodott | |
| checkout | ħlas | button: Kompli għall-ħlas |
| VAT / VAT type | VAT / tip ta' VAT | |
| discount / discount rule | skont / regola tal-iskont | |
| rule | regola | pl. regoli |
| wish list | lista tax-xewqat | |
| currency / price | munita / prezz | |
| payment gateway / gateway | portal tal-ħlas / portal | |
| newsletter | bullettin | |
| subscriber / subscription / subscribe | abbonat / abbonament / abbona | |
| tag (keyword, eztags) | tikketta | pl. tikketti |
| keywords / keyword | kliem ewlieni / kelma ewlenija | |
| tag (HTML/XML) / custom tag | tag / tag personalizzat | it-tag HTML |
| export / import | esporta / importa | nouns: esportazzjoni / importazzjoni |
| move / copy / swap | mexxi / ikkopja / ibdel | move also in the tree, to a group or to a state |
| embedding (vector) / chunk / vector | rappreżentazzjoni vettorjali / framment / vettur | pl. frammenti |
| embed / embedded (object in text) | inkorpora / inkorporat | noun: inkorporazzjoni (embedding content) |
| prompt / model / token | prompt / mudell tal-AI / token | always "mudell tal-AI", never bare "mudell" (= template) |
| AI | AI | l-intelliġenza artifiċjali in running text where it reads better |
| deploy / deployment / release | skjera / skjerament / rilaxx | |
| enable / disable | attiva / iddiżattiva | |
| enabled / disabled | attivat / diżattivat | |
| active / inactive | attiv / inattiv | |
| warning / error / notice | twissija / żball / avviż | pl. twissijiet / żbalji / avviżi |
| required / optional | meħtieġ / fakultattiv | |
| default | predefinit | default value: valur predefinit |
| priority | prijorità | |
| sort / ascending / descending | issortja / axxendenti / dixxendenti | |
| visible / hidden | viżibbli / moħbi | |
| hide / show / reveal | aħbi / uri / uri | |
| expand / collapse | espandi / ikkollassa | |
| OK / Cancel / Yes / No | OK / Ikkanċella / Iva / Le | |
| Back / Next / Previous / Close | Lura / Li jmiss / Ta' qabel / Agħlaq | |
| select / select all | agħżel / Agħżel kollox | |
| select none / deselect all | Neħħi l-għażla / Neħħi l-għażla kollha | |
| browse | esplora | Browse for location: Esplora biex issib post |
| apply | applika | Apply changes: Applika l-bidliet |
| save / store (button, user action) | salva | Store draft: Salva l-abbozz; stored (persisted data): maħżun; storage: ħażna |
| discard | armi | |
| edit | editja | |
| remove | neħħi | take out of a list, group or place |
| delete | ħassar | destroy; noun: tħassir |
| new / create / add | ġdid / oħloq / żid | Create new: Oħloq ġdid |
| refresh / reset | ġedded / irrisettja | |
| update | aġġorna | noun: aġġornament |
| run (verb) | ħaddem | |
| run (noun: program, cron, index run) | tħaddim | |
| dry run / self-test | prova mingħajr bidliet / awtotest | |
| debug | debug | il-modalità debug; debug output: output tad-debug |
| statistics / system information | statistika / informazzjoni dwar is-sistema | |
| database | bażi tad-dejta | |
| path | mogħdija | |
| handler / wizard | ġestur / assistent | setup wizard: assistent tal-installazzjoni |
| backend / provider / endpoint | backend / fornitur / endpoint | pl. fornituri; `<provider>` in INI keys stays |
| method / listener / controller | metodu / semmiegħ / kontrollur | |
| allowlist / read-only / argument | lista ta' dak permess / għall-qari biss / argument | |
| slot / collection / facets / elevate | slott / ġabra / aspetti / għolli | collection (one submission, MongoDB collection): ġabra, pl. ġabriet |
| filter / details | filtru / dettalji | |
| tick (checkbox) | immarka | |
| container (class flag "Is container") | kontenitur | |
| container (Bootstrap, CSS) | container | |
| locale / localization | lokalità / lokalizzazzjoni | pl. lokalitajiet; codes and paths (`share/locale`, `%locale`, `Locale=`) stay |
| approve / approval / approved / approver | approva / approvazzjoni / approvat / approvatur | |
| deny / reject / rejected | iċħad / irrifjuta / irrifjutat | |
| accept / accepted | aċċetta / aċċettat | |
| pending | pendenti | "qed jistenna" is fine in running text |
| wildcard (URL) | wildcard | URL wildcard: wildcard tal-URL |
| redirect / forward (URL) | idderieġi / għaddi | noun: ridirezzjoni; redirected: dderieġut |
| charset | sett ta' karattri | pl. settijiet ta' karattri |
| license | liċenzja | the file name LICENSE stays; licence statement: dikjarazzjoni |
| privilege (DB) / grant | privileġġ / agħti | |
| format / formatting | format / ifformattjar | |
| linked / common | marbut / komuni | |
| flag / flags (class flags) | indikatur / indikaturi | country and language images: bandiera, pl. bnadar |
| teaser | introduzzjoni | |
| slug | slug | |
| placeholder (input hint of a form field) | test ta' gwida | a marker in a text or a dummy value: see section 5 |
| preset | issettjar predefinit | Revert preset: Reġġa' lura l-issettjar predefinit |
| information collection / information collector | ġbir ta' informazzjoni | attribute: attribut ta' ġbir ta' informazzjoni; collected information: informazzjoni miġbura |
| overview / summary | ħarsa ġenerali / sommarju | |
| tab | tab | it-tab, pl. tabs |
| rich text | test formattjat | |
| feed | feed | RSS feed: feed RSS |
| subtree | sottosiġra | |
| session | sessjoni | |
| assign / assignment | assenja / assenjazzjoni | |
| scope | ambitu | |
| thread (conversation) | konverżazzjoni | process thread: thread |
| chain | katina | |
| batch | lott | |
| schema / registry / manifest | skema / reġistru tas-sistema / manifest | registry is never bare "reġistru" (= log) |
| stylesheet / theme / icon | folja tal-istil / tema / ikona | |
| port | port | |
| digest | sommarju perjodiku | |
| profile | profil | |
| core (Solr) | core | Solr core: core ta' Solr |
| lane (queue) | korsija | pl. korsiji |
| editor (person) | editur | |
| editor (software) | editur | template editor: editur tal-mudelli |
| Editor (siteaccess kind) | Editur | |
| user interface | interfaċċja tal-utent | |
| page / pages | paġna / paġni | |
| link | link | pl. links |
| title / name / description | titlu / isem / deskrizzjoni | |
| date / time | data / ħin | |
| status | status | |
| type | tip | pl. tipi |
| group | grupp | pl. gruppi |
| list | lista | pl. listi |
| value / field | valur / qasam | pl. valuri / oqsma |
| number / count | numru / għadd | |
| width / height / size | wisa' / għoli / daqs | |
| server / client | server / klijent | |
| request / response | talba / rispons | |
| archived (version, object, order) | arkivjat | |
| Archive / Unarchive (button) | Arkivja / Neħħi mill-arkivju | |
| archive (place, menu item, log archive) | arkivju | |
| archive (file .tar/.zip/.phar/.ezpkg) | arkivju | file archive: fajl tal-arkivju |
| rotation / rotate (log file) | rotazzjoni / dawwar | |
| pattern | xejra | regex pattern: espressjoni regolari |
| repository | repożitorju | |
| maintainer | manutentur | |
| install / uninstall / installation | installa / iddiżinstalla / installazzjoni | |
| checksum | checksum | |
| quarantine | kwarantina | |
| pool (of processes or threads) | grupp | pool of workers: grupp ta' proċessi tax-xogħol |
| mapping | korrispondenza | |
| resolve / resolvable / resolver | iddetermina / determinabbli / riżolutur | |
| checkpoint | punt ta' kontroll | |
| confirmation questions ("Delete ...?") | Żgur li trid ...? | "Trid tassew tħassar ...?" also correct |
| child documents (Solr) | dokumenti subordinati | |
| script | skript | pl. skripts |
| revoke | irrevoka | |
| label (of a field, button or block) | isem / test | field label: isem tal-qasam; button label: test tal-buttuna; never "tikketta" (= tag) |
| plugin | plugin | |
| kernel | kernel | |
| button | buttuna | |
| menu / context menu | menu / menu tal-kuntest | |
| window | tieqa | |
| message | messaġġ | |
| comment | kumment | |
| attachment | annessjoni | |
| author | awtur | |
| key / API key | ċavetta / ċavetta tal-API | |
| connection | konnessjoni | |
| print | ipprintja | |
| help | għajnuna | |
| options | għażliet | |
| changes | bidliet | |
| history | storja | version history: storja tal-verżjonijiet |
| contact | kuntatt | |
| address | indirizz | |
| country | pajjiż | |
| unknown | mhux magħruf | |
| empty | vojt | |
| all / none | kollha / xejn | |
| open | iftaħ | |
| send | ibgħat | |
| continue / finish | kompli / lesti | |

## 5. Decisions after the translation

These decisions take precedence over any older usage. Section 4 already reflects them where a glossary term was
concerned.

| English | Maltese | Distinction |
|---|---|---|
| restore (trash, file, checkpoint, value) | irrestawra; noun: restawr; restored: irrestawrat | never "irkupra" |
| revert | reġġa' lura; noun: treġġigħ lura; reverted: treġġa' lura | not "irriverti", not "irritorna" |
| undo / cannot be undone | reġġa' lura / ma jistax jitreġġa' lura | never "Erġa' lura", which reads as "go back again" next to "Lura" (Back) |
| lock (edit lock, account lock) | sakkatura; locked: msakkar (f. msakkra); to lock: issakkar | not "imblukkat" (= blocked) |
| unlock | iżblokka; unlocked: żblokkat; noun: żblokk | not "iftaħ" |
| lock (process, file or database lock) | lock, pl. locks | technical loan; row lock: lock tar-ringiela |
| toolbar | virga tal-għodod, pl. vireg tal-għodod | not "barra/bar tal-għodod", not "toolbar" |
| rating / star rating / rate | valutazzjoni / valutazzjoni bl-istilel / ivvaluta | "klassifikazzjoni" means ranking |
| query (database, search) | mistoqsija, pl. mistoqsijiet | SQL query: mistoqsija SQL; search query: mistoqsija tat-tfittxija; "query" only as code |
| statement (SQL) | istruzzjoni, pl. istruzzjonijiet | "stqarrija" is a public statement |
| placeholder (marker in a text or pattern: {1}, {option}, %name) | simbolu tal-pożizzjoni, pl. simboli tal-pożizzjoni | the input hint of a form field stays "test ta' gwida" |
| placeholder (dummy value in a setting) | valur ta' eżempju | |
| backup | backup, pl. backups; to back up: agħmel backup | "kopja ta' riżerva" collides with riżerva (fallback) |
| entry (cache, registry, list, crontab, changelog) | entrata, pl. entrati | not "daħla"; an OPML outline stays "daħla", a sign-in is "dħul" |
| check (noun) | verifika, pl. verifiki | health check: verifika tas-saħħa; link check: verifika tal-links; drift check: verifika tad-devjazzjoni; consistency check: verifika tal-konsistenza. "kontroll" is control, gate and checkpoint |
| check (verb) | iċċekkja | |
| hit / miss / hit rate (cache) | suċċess / nuqqas / rata ta' suċċess | hit ratio: proporzjon tas-suċċessi; never "misjub" or "sejba" |
| hit (search result) | riżultat, pl. riżultati | |
| timeout / timed out | skadenza (tal-ħin) / skadut | session timed out: is-sessjoni skadiet; execution timeout: ħin massimu tal-eżekuzzjoni; "timeout" only as an INI key |
| staging (environment) | pre-produzzjoni | staging site: sit ta' pre-produzzjoni; the badges DEV/TEST/STAGE/PROD stay codes |
| staged / staged changes | imħejji / bidliet imħejjija | staged review: reviżjoni tal-bidliet imħejjija |
| target (of a reference, link, rule) | mira, pl. miri | target mode: modalità mmirata |
| destination; target of a symlink, redirect or copy | destinazzjoni | |
| purge (trash, objects, files) | ħassar għal kollox; noun: tħassir għal kollox | cache pages: neħħi mill-cache; never "purga" or "tindif" |
| prune (logs, threads) | naqqas | |
| INI section ([Name] in an .ini file) | blokk, pl. blokki; where ambiguous: blokk tal-INI | "taqsima" is only the Exponential section, so "[Section]" of a content section stays "[Taqsima]" |
| Webshop | Ħanut online | not "onlajn" |
| Desc. / Asc. | Dixx. / Axx. | |
| pause / resume (cron parts) | Agħmel pawża / Kompli | status: fil-pawża / tkompla |
| breadcrumb / sidebar | mogħdija tan-navigazzjoni / pannell tal-ġenb | |
| dismiss | Warrab | |
| poll / vote / feedback | stħarriġ / ivvota / rispons | |
| finding | sejba, pl. sejbiet | |
| secret | sigriet, pl. sigrieti | |
| row / column / form | ringiela, pl. ringieli / kolonna / formola, pl. formoli | |
| source / report / environment | sors, pl. sorsi / rapport / ambjent | |
