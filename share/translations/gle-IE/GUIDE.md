# Irish (gle-IE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Irish as defined by **An Caighdeán Oifigiúil** (2017 revision), in the spelling and grammar used by
the State and by the Irish versions of common operating systems and office software. Terms follow
**téarma.ie** (Foclóir na Téarmaíochta) and the established Microsoft Irish terminology; where the two differ,
the téarma.ie term wins unless the Microsoft term is clearly the one users meet every day. No other locale falls
back to this catalogue. The tone is neutral, concise and professional. Avoid dialect forms that the Caighdeán
does not accept (for example Munster "deintear", Ulster "thig liom"), colloquial English loans ("sábháil an
file") and invented words: if neither téarma.ie nor the Microsoft terminology has a term, use a plain
descriptive Irish phrase.

## 2. Form of address

The user is addressed in the singular with **tú**, everywhere. This is the convention of Irish software and of
State online services; the plural "sibh" is never used for a single reader.

- **Buttons, menu items, links and action labels** use the singular imperative, with the object after the verb:
  "Sábháil", "Scrios", "Cuir in eagar", "Cuir leis", "Cealaigh", "Cruthaigh nua", "Cuir na hathruithe i
  bhfeidhm", "Seol lena fhoilsiú", "Roghnaigh gach rud", "Bain na míreanna roghnaithe", "Folmhaigh an bruscar".
- **Titles, column headings and tabs** are nouns or noun phrases: "Struchtúr an ábhair", "Cuntais úsáideora",
  "Cineál leagain amach", "Socruithe fógraí", "Staideanna oibiachtaí".
- **Sentences, hints and instructions** address the user with tú (tú, thú, do, agat, leat) and the singular
  imperative:
  - "Roghnaigh láthair" (Please choose a location)
  - "Cuir isteach seoladh ríomhphoist bailí."
  - "Is féidir leat socruithe stair na leaganacha a athrú sa chomhad content.ini ..."
  - "Seiceáil an nasc agus bain triail eile as."
- **Questions and confirmations**: "An bhfuil tú cinnte gur mian leat ... ?" or "Ar mhaith leat ... ?":
  - "An bhfuil tú cinnte gur mian leat an aicme seo a bhaint?"
  - "Ar mhaith leat teachtaireacht a sheoladh chuig an duine atá á ceadú?"
- **Error messages** state the problem plainly, addressing the user with tú where the user is meant:
  - "Níl cead agat aicmí a bhaint."
  - "Níor aimsíodh an comhad."
- **Status messages** use the autonomous (saorbhriathar) form or an impersonal statement:
  "Sábháladh na hathruithe.", "Baineadh dréacht amháin.", "Níor aimsíodh aon rith cron".
- **Tooltips that describe a click** use "Cliceáil ... chun ...": "Cliceáil an deilbhín chun an roghchlár
  comhthéacs a thaispeáint."
- "Le do thoil" is used sparingly, only where a bare imperative would sound abrupt in a longer request; never on
  buttons.
- Not allowed: the plural sibh/bhur/agaibh and plural imperatives ("Sábhálaigí", "Roghnaígí"); dialect verb forms
  outside the Caighdeán; the verbal noun as a button where the imperative is meant ("Sábháil" is both, which is
  fine, but "Scriosadh" or "Ag scriosadh" on a button is wrong); English words where an Irish term exists.

## 3. Script and typography

- Latin script with the síneadh fada on all five vowels (á é í ó ú, Á É Í Ó Ú). A missing fada is a spelling
  error ("sabhail" is wrong). No other diacritics; no dotted (Gaelic) script.
- **Sentence case** for titles, buttons and headings: "Struchtúr an ábhair", "Leabharlann na meán". Proper names
  keep their capitals.
- **Initial mutations** follow the Caighdeán. Before a capital letter the eclipsis or prefix stays lower case
  without a hyphen: "i mBéarla", "Dollar na hAstráile". Before a lower-case vowel the prefixes n- and t- take a
  hyphen: "an t-aistriúchán", "na n-oibiachtaí"; h is joined without a hyphen: "na hathruithe", "na haicmí".
  Product names and codes are not mutated: "i Exponential", "le Solr".
- **Quotation marks** in running text: “…” (U+201C, U+201D), nested ‘…’. Inside HTML attributes and JavaScript
  the straight quotes of the source stay.
- **No space** before ":", ";", "?" and "!".
- **Numbers and counts**: Irish has several numeral forms (1; 2; 3–6; 7–10; 11–19; 20 and more), with lenition or
  eclipsis of the noun after the numeral, and the source has only "item(s)". Never write "mír(eanna)". A
  placeholder number cannot trigger a mutation, so use a label form in which the count stands free:
  "Oibiachtaí: %count", "Próisis oibrí: %count", "Dréachtaí a baineadh: %count." Where a sentence is
  unavoidable, use "líon" ("líon na n-oibiachtaí").
- **Placeholders**: Irish inflects at the start of a word (mutation) and in the genitive, not with suffixes, so
  gluing letters to a placeholder is rarely tempting. A placeholder can, however, never be mutated or put in the
  genitive itself. Let a noun carry the grammar and put the placeholder in apposition: "Roghnaigh láthair don
  oibiacht %object_name", "Cuir <%class_name> in eagar". Never glue Irish letters to a placeholder ("%namea" is a
  different placeholder); a punctuation mark directly after it is fine.
- **Numbers**: the Irish standard uses the decimal point and the thousands comma, as in English in Ireland:
  "1,234.5". Dates day before month: "23/10/2026" or "23 Deireadh Fómhair 2026". Units after a number are written
  out or abbreviated plainly: "%n s", "%n nóiméad", "%n uair"; "%n nóiméad ó shin" for "%n min ago".
- **Exponential** and other product names (Solr, MySQL, Exponential Layouts) stay unchanged and uninflected;
  where the grammar needs a noun, use "an córas Exponential".

## 4. Glossary

| English | Irish | Note |
|---|---|---|
| content | ábhar | gen. ábhair; "Struchtúr an ábhair" |
| content object / object | oibiacht ábhair / oibiacht | pl. oibiachtaí; fem.: "an oibiacht", "na hoibiachta" |
| item (list entry) | mír | pl. míreanna |
| node | nód | pl. nóid; main node: príomhnód |
| location / main location | láthair / príomhláthair | pl. láithreacha; gen. na láithreach, na príomhláithreach; not "suíomh" (that is site) |
| content class / class | aicme ábhair / aicme | pl. aicmí; a PHP class: aicme PHP |
| class group | grúpa aicmí | |
| attribute | aitreabúid | pl. aitreabúidí |
| datatype | cineál sonraí | |
| version | leagan | pl. leaganacha; version history: stair na leaganacha |
| draft | dréacht | Store draft: Sábháil an dréacht; Discard draft: Cuileáil an dréacht |
| publish / published / publishing | foilsigh / foilsithe / foilsiú | Send for publishing: Seol lena fhoilsiú |
| translation | aistriúchán | pl. aistriúcháin |
| language / main language | teanga / príomhtheanga | interface language: teanga an chomhéadain |
| content language | teanga ábhair | |
| siteaccess | rochtain suímh | pl. rochtainí suímh |
| site / website | suíomh / suíomh Gréasáin | |
| design (Exponential design) | dearadh | pl. dearaí |
| structure | struchtúr | |
| template | teimpléad | pl. teimpléid |
| override (template, setting) | sárú | verb: sáraigh; override template: teimpléad sáraithe; overridden: sáraithe |
| fallback | cúltaca | fallback chain: slabhra cúltaca; fallback rule: riail chúltaca; translated through a fallback: aistrithe trí chúltaca |
| alias / URL alias | ailias / ailias URL | |
| wildcard (URL) | saoróg | fem.: an tsaoróg, na saoróige, pl. na saoróga, gen. pl. na saoróg; wildcard alias: ailias saoróige |
| redirect / forward (URL) | atreoraigh / seol ar aghaidh | noun: atreorú; class names such as eZRedirectGateway stay |
| cache | taisce | Clear cache: Glan an taisce |
| workflow | sreabhadh oibre | pl. sreafaí oibre; workflow event: imeacht sreafa oibre |
| trigger / event | truicear / imeacht | pl. truicir / imeachtaí |
| role | ról | pl. róil |
| policy | polasaí | pl. polasaithe |
| limitation (policy) | srian | pl. srianta |
| permission | cead | pl. ceadanna; kept apart from ceadúnas (licence) |
| access / access denied | rochtain / diúltaíodh rochtain | |
| user / user group | úsáideoir / grúpa úsáideoirí | |
| user account / user accounts | cuntas úsáideora / cuntais úsáideora | |
| anonymous user | úsáideoir anaithnid | |
| administrator / admin interface | riarthóir / comhéadan riaracháin | |
| log in / log out | sínigh isteach / sínigh amach | noun: síniú isteach |
| login (field) / username | ainm úsáideora | |
| password | pasfhocal | |
| e-mail | ríomhphost | e-mail address: seoladh ríomhphoist |
| section (Exponential) | rannán | pl. rannáin; never "cuid" or "roinn" when the content section is meant |
| object state / state | staid oibiachta / staid | pl. staideanna |
| state group | grúpa staideanna | |
| relation | gaol | pl. gaolta |
| related objects | oibiachtaí gaolmhara | reverse related: oibiachtaí a thagraíonn don oibiacht seo |
| trash | bruscar | Empty trash: Folmhaigh an bruscar |
| bookmark | leabharmharc | pl. leabharmharcanna |
| sub-items / children (content tree) | fo-mhíreanna | child node: fo-nód |
| parent | máthair | parent node: máthairnód; parent location: máthairláthair, gen. na máthairláithreach |
| subtree | fochrann | |
| owner / creator / modifier | úinéir / cruthaitheoir / mionathraitheoir | |
| created / modified | cruthaithe / athraithe | column headings: "Cruthaithe", "Athraithe" |
| layout (Exponential Layouts) | leagan amach | layout type: cineál leagain amach; the name Exponential Layouts stays |
| layout (directory or path structure) | struchtúr | release layout: struchtúr eisiúna |
| block / zone | bloc / crios | pl. bloic / criosanna; "bloc" is only the Layouts block and the template cache block |
| setting / settings | socrú / socruithe | configuration in the sense of Layout/Settings: socruithe |
| Setup (admin tab) | Socrú | |
| configuration / configure | cumraíocht / cumraigh | |
| Content structure | Struchtúr an ábhair | |
| Media library | Leabharlann na meán | |
| Dashboard | Deais | |
| extension | eisínteacht | pl. eisínteachtaí |
| package | pacáiste | pl. pacáistí |
| module | modúl | pl. modúil |
| view (noun, module view) | amharc | "Module view": amharc modúil |
| View (button) | Féach | to look at something |
| function | feidhm | pl. feidhmeanna |
| search | cuardaigh | noun: cuardach; search engine: inneall cuardaigh |
| search index / reindex | innéacs cuardaigh / athinnéacsaigh | noun: athinnéacsú |
| index (verb) / index (DB) | innéacsaigh / innéacs | |
| index file | comhad innéacs | |
| cronjob | jab cron | pl. jabanna cron; cron run: rith cron |
| schedule | sceideal | verb: sceidealaigh |
| job / queue | jab / scuaine | job queue: scuaine na jabanna |
| worker / lane (queue) | oibrí / lána | worker process: próiseas oibrí |
| log / log file | loga / logchomhad | never "log in" |
| audit / audit trail | iniúchadh / rian iniúchta | |
| rotation (log) / rotate | rothlú / rothlaigh | |
| notification | fógra | pl. fógraí |
| collaboration | comhoibriú | |
| preview | réamhamharc | |
| ID / remote ID | aitheantas / aitheantas cianda | ID in tables stays "ID" |
| identifier | aitheantóir | |
| file / folder / directory | comhad / fillteán / comhadlann | |
| image / media | íomhá / meáin | |
| thumbnail | mionsamhail | pl. mionsamhlacha |
| upload / download | uaslódáil / íoslódáil | |
| shop | siopa | |
| Store (shop tab) | Siopa | every other "Store" is the verb "Sábháil" |
| basket | ciseán | |
| order (shop) | ordú | pl. orduithe; order status: stádas an ordaithe; "ordú" is only the shop order |
| order / ordering (sorting, content order) | ord | sort order: ord sórtála |
| customer / product | custaiméir / táirge | |
| checkout | seiceáil amach | |
| VAT / VAT type | CBL / cineál CBL | cáin bhreisluacha |
| discount / discount rule | lascaine / riail lascaine | |
| rule | riail | pl. rialacha |
| wish list | liosta mianta | |
| currency / price | airgeadra / praghas | |
| payment gateway / gateway | tairseach íocaíochta / tairseach | |
| newsletter | nuachtlitir | pl. nuachtlitreacha |
| subscriber / subscription / subscribe | síntiúsóir / síntiús / liostáil | unsubscribe: díliostáil |
| tag (keyword, eztags) | clib | pl. clibeanna |
| keyword | eochairfhocal | pl. eochairfhocail |
| tag (HTML/XML) / custom tag | clib HTML / clib shaincheaptha | always qualified: "clib XML" |
| export / import | easpórtáil / iompórtáil | |
| move | bog | in a tree, group or status alike |
| copy / swap | cóipeáil / babhtáil | |
| embedding (AI) / vector / chunk | leabú / veicteoir / smután | pl. leabuithe / veicteoirí / smutáin |
| embed / embedded (object in text) | leabaigh / leabaithe | embedded object: oibiacht leabaithe; the tag names embed, embed-inline stay |
| prompt / model / token | leid / samhail / comhartha | token (JWT, bearer, AI): comhartha, pl. comharthaí; bearer token: comhartha iompróra |
| AI | IS | intleacht shaorga; spelled out at first use in long texts |
| deploy / deployment | imscar / imscaradh | |
| release / rollback / symlink | eisiúint / rolladh siar / nasc siombalach | |
| enable / disable | cumasaigh / díchumasaigh | |
| enabled / disabled | cumasaithe / díchumasaithe | |
| active / inactive | gníomhach / neamhghníomhach | |
| warning / error / notice | rabhadh / earráid / nóta | |
| required / optional | riachtanach / roghnach | |
| default | réamhshocraithe | noun: réamhshocrú |
| priority | tosaíocht | |
| sort / ascending / descending | sórtáil / ag dul suas / ag dul síos | abbreviated column labels: suas / síos; the code values asc and desc stay |
| visible / hidden | infheicthe / folaithe | |
| hide / show (reveal) | folaigh / taispeáin | |
| expand / collapse | leathnaigh / laghdaigh | |
| OK / Cancel | OK / Cealaigh | |
| Yes / No | Tá / Níl | in answers to a question, echo its verb where natural |
| Back / Next / Previous / Close | Siar / Ar aghaidh / Roimhe seo / Dún | |
| select / browse | roghnaigh / brabhsáil | |
| select all / deselect all | Roghnaigh gach rud / Díroghnaigh gach rud | Select none: Díroghnaigh gach rud |
| apply | cuir i bhfeidhm | Apply changes: Cuir na hathruithe i bhfeidhm |
| save / store | sábháil | |
| discard | cuileáil | |
| edit | cuir in eagar | |
| remove | bain | take out of a list or place: "Bain den liosta"; never "Scrios" |
| delete | scrios | destroy; never "Bain" |
| new / create / add | nua / cruthaigh / cuir leis | Create new: Cruthaigh nua |
| refresh / update / reset | athnuaigh / nuashonraigh / athshocraigh | |
| run (verb / noun) | rith / rith | pl. rití |
| dry run | triail-rith | |
| self-test | féintástáil | |
| restore / assign / assignment | athchóirigh / sannaigh / sannadh | |
| approve / approval / approved | ceadaigh / ceadú / ceadaithe | approver: ceadaitheoir; never "faomh"; Approver users/groups: Ceadaitheoirí (úsáideoirí) / Ceadaitheoirí (grúpaí) |
| deny / reject / rejected | diúltaigh / diúltaigh / diúltaithe | |
| accept / accepted | glac le / glactha | |
| pending | ar feitheamh | pending list: liosta feithimh; "pending an external action": ag fanacht le … |
| archive (place, menu item) | cartlann | pl. cartlanna |
| archived (version, object, order) | cartlannaithe | |
| Archive / Unarchive (button) | Cartlannaigh / Díchartlannaigh | |
| archive (file .tar/.zip/.phar/.ezpkg) | comhad cartlainne | |
| statistics / system information | staitisticí / faisnéis chórais | |
| database | bunachar sonraí | |
| path | conair | |
| handler | láimhseálaí | pl. láimhseálaithe; Handler as a setting name stays |
| wizard / setup wizard (installer) | draoi / draoi suiteála | the installer itself: an tsuiteáil |
| backend / provider / endpoint | inneall cúil / soláthraí / críochphointe | provider pl. soláthraithe; interface names stay |
| method / listener / controller | modh / éisteoir / rialaitheoir | |
| allowlist / read-only / argument | liosta ceadaithe / inléite amháin / argóint | |
| slot / collection / facets | sliotán / bailiúchán / gnéithe | elevate: ardaigh |
| filter / details | scagaire / sonraí | verb: scag |
| tick (checkbox) | cuir tic i | ticked: le tic |
| container | coimeádán | gen. coimeádáin; for content classes and CSS alike; "Is container class": "Is aicme choimeádáin í" |
| locale / localization | logchaighdeán / logánú | pl. logchaighdeáin; the setting name Locale and %locale stay |
| charset | tacar carachtar | pl. tacair carachtar; gen. an tacair carachtar |
| license / licence | ceadúnas | never "cead", which is permission; LICENSE as a file name stays |
| privilege (DB) / grant | pribhléid / deonaigh | |
| format / formatting | formáid / formáidiú | file or data format / text formatting |
| linked / common | nasctha / coiteann | linked: not "ceangailte" |
| flag | bratach | pl. bratacha; class property flags and command-line flags alike: "an bhratach --allow-write" |
| teaser | réamhbhlaiseadh | |
| slug | sluga URL | no established term; always with "URL" |
| placeholder | ionadchoinneálaí | pl. ionadchoinneálaithe |
| preset | réamhshocrú | pl. réamhshocruithe |
| information collection / collector | bailiú faisnéise / bailitheoir faisnéise | collected information: faisnéis bhailithe; one submission: bailiúchán, pl. bailiúcháin |
| overview / summary | forbhreathnú / achoimre | |
| digest (e-mail) | achoimre ríomhphoist | |
| tab | cluaisín | the admin top-level menus addressed by name ("%tab menu") are "roghchlár “%tab”" |
| rich text | téacs saibhir | gen. téacs shaibhir: eagarthóir téacs shaibhir |
| feed (RSS) | fotha | pl. fothaí; feed reader: léitheoir fothaí; enclosure: iniamh |
| session | seisiún | |
| scope | raon feidhme | |
| thread / chain | snáithe / slabhra | pl. snáitheanna; conversation thread and process thread alike |
| batch | baisc | |
| schema / registry / manifest | scéimre / clárlann / léiriú | |
| stylesheet / theme / icon | stílbhileog / téama / deilbhín | |
| port | port | |
| profile | próifíl | |
| core (Solr) | croí Solr | |
| child documents (Solr) | doiciméid linbh | |
| editor (person) / editor (software) | eagarthóir / eagarthóir | template editor: eagarthóir teimpléad |
| Editor (siteaccess type) | rochtain eagarthóireachta | |
| user interface | comhéadan úsáideora | |
| page / link | leathanach / nasc | |
| title / name / description | teideal / ainm / cur síos | |
| date / time | dáta / am | |
| type / status | cineál / stádas | |
| group / list | grúpa / liosta | |
| value / field | luach / réimse | |
| number / count | uimhir / líon | |
| width / height / size | leithead / airde / méid | |
| server / client | freastalaí / cliant | |
| request / response | iarratas / freagra | |
| task | tasc | |
| pattern / repository / maintainer | patrún / stór / cothaitheoir | repository pl. stórtha; secret store: stór na rún |
| install / uninstall / installation | suiteáil / díshuiteáil / suiteáil | |
| checksum / quarantine | suim sheiceála / coraintín | |
| pool | linn | |
| mapping / resolver / checkpoint | mapáil / réiteoir / seicphointe | |
| resolve / resolvable | réitigh / inréitithe | |
| script | script | pl. scripteanna |
| revoke | cúlghair | |
| label | lipéad | |
| plugin | breiseán | |
| kernel | eithne | |
| debug | dífhabhtú | |

## 5. Decisions after the translation

| English | Irish | Distinction |
|---|---|---|
| position (in a list; order of states, policies, bookmarks) | ionad, pl. ionaid | "Position %position of %count": "Ionad %position as %count"; never "suíomh". Marketing "positioning" may stay "suíomhú" |
| section (INI, [Section]) | rannóg, pl. rannóga | "sa rannóg chéanna"; not "bloc" and not "rannán" |
| section (part of a page or text) | cuid, pl. codanna | |
| child (XML element, package.xml, custom tag) | leanbh, pl. leanaí | only for XML/DOM children; children in the content tree are fo-mhíreanna |
| child process | próiseas linbh | not "macphróiseas" |
| destination (URL alias, wildcard) | ceann scríbe | gen. an chinn scríbe ("téacs an chinn scríbe"); never "sprioc", which stays for other targets |
| fetch function / fetch alias | feidhm fetch / ailias fetch | pl. feidhmeanna fetch; not "feidhm fála" |
| extension point | pointe eisínteachta | pl. pointí eisínteachta; not "pointe leathnaithe" (leathnaigh is expand) |
| cronjob part | cuid jab cron | pl. codanna jab cron, gen. na coda; not "cuid cron" |
| grip / drag handle | greim | "ar a ghreim"; not "hanla" |
| menu / top menu / left menu | roghchlár / roghchlár uachtarach / roghchlár ar chlé | pl. roghchláir; drop-down menu: roghchlár anuas; menu arrangement (menuconfig): eagar |
| page layout (pagelayout.tpl) | leagan amach leathanaigh | pl. leaganacha amach leathanaigh; the file name pagelayout.tpl stays |
| property / variable / parameter | airí / athróg / paraiméadar | pl. airíonna / athróga / paraiméadair |
| operator (template) | oibreoir | pl. oibreoirí |
| column / row (table, DB) | colún / ró | pl. colúin / rónna |
| entry / input / output | iontráil / ionchur / aschur | iontráil pl. iontrálacha |
| report (noun / verb) | tuarascáil / tuairiscigh | |
| tool / panel | uirlis / painéal | uirlis pl. uirlisí |
| key (array, INI, API) | eochair | pl. eochracha |
| message / process | teachtaireacht / próiseas | pl. teachtaireachtaí / próisis |
| storage | stóras | the place or system that holds files; the act of storing is stóráil; storage key: eochair stórála |
| command line | líne na n-orduithe | not "líne ordaithe" |
| visitor | cuairteoir | pl. cuairteoirí |
| cache hit / miss | amas / iomrall | hit rate: ráta amas |
| validate / validation | bailíochtaigh / bailíochtú | validation error: earráid bhailíochtaithe |
| checkbox / radio button | ticbhosca / cnaipe raidió | pl. ticbhoscaí / cnaipí raidió |
| search phrase | frása cuardaigh | pl. frásaí cuardaigh |
| navigation part | cuid nascleanúna | pl. codanna nascleanúna |
| Stop (button) | Stad | |
| tip / hint | nod, pl. noda | "Tip": "Nod"; "leid" is reserved for the AI prompt |
| Undo / Dismiss | Cealaigh an gníomh / Ruaig | |
| poll / vote / feedback | pobalbhreith / vóta, vótáil / aiseolas | |
| Locked out (user) / Locked (state) | Glasáilte amach / Faoi ghlas | |
| Staging / Production (environment) | Stáitsiú / Táirgeadh | the badges DEV, TEST, STAGE, PROD stay as codes |
| Desktop / Landscape (preview) | Deasc / Tírdhreach | |
| regular expression | slonn ionadaíochta | |
| issue (GitHub, setup check) | saincheist, pl. saincheisteanna | a problem found in a log or check may be "fadhb" |

- **suíomh is site, nothing else.** A place in a list is "ionad", a place in the content tree is "láthair"; the
  location of a file is named by its path where possible ("Conair an chomhaid INI").
- **Three kinds of section** stay apart: rannán (Exponential section), rannóg (INI section), cuid (part of a page
  or text).
- **Three kinds of child** stay apart: fo-mhír / fo-nód (content tree), leanbh (XML element), próiseas linbh
  (process); Solr keeps "doiciméid linbh".
- **Code words stay unmutated and untranslated**: fetch, embed, pagelayout.tpl, Locale, Handler, LICENSE,
  asc|desc. The Irish noun next to them carries the grammar ("feidhm fetch", "an comhad LICENSE").
