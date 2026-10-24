# Korean (kor-KR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Korean (표준어) of the Republic of Korea, spelled according to the rules of the National Institute of
Korean Language (한글 맞춤법, 외래어 표기법). No other locale falls back to this catalogue. The tone is neutral,
polite and concise, as in the Korean versions of Windows, Microsoft 365 and other common business software. The
terminology follows the Microsoft Korean terminology wherever it has a term (개체, 게시, 재정의, 로캘, 미리 보기,
찾아보기, 자리 표시자); established loanwords are written in Hangul (캐시, 템플릿, 워크플로, 콘텐츠). No
colloquial or slang forms, no needless English, no coinages that a Korean administrator would not recognize.

## 2. Form of address

Korean software does not name the user. The pronoun 당신 is never used; the politeness lies in the verb ending.

- **Buttons, menu items, links and action labels** are nouns or noun phrases (Sino-Korean verbal nouns without
  하다), the object first: "저장", "삭제", "편집", "취소", "추가", "적용", "새로 만들기", "모두 선택",
  "초안 저장", "초안 폐기", "게시 요청", "휴지통 비우기", "선택 항목 제거", "변경 내용 적용". Native verbs take
  the nominal form ‑기: "닫기", "보기", "찾아보기", "숨기기", "내보내기", "가져오기". Never an imperative or a
  sentence ending on a button ("저장하십시오", "저장하세요", "저장한다" are wrong).
- **Titles, column headings and tabs** are nouns: "콘텐츠 구조", "사용자 계정", "레이아웃 유형", "알림 설정",
  "개체 상태".
- **Sentences, hints and messages** use the formal polite style **합니다체** (‑ㅂ니다/‑습니다):
  - "클래스를 제거할 권한이 없습니다." (You do not have permission to remove classes.)
  - "버전 기록 설정은 content.ini 파일에서 변경할 수 있으며, ..."
- **Instructions and requests** use the formal imperative ‑십시오, with "‑해 주십시오" where a request is
  softened ("Please ..."):
  - "위치를 선택하십시오" (Please choose a location)
  - "올바른 이메일 주소를 입력하십시오."
  - "연결을 확인한 후 다시 시도하십시오."
  - "“취소” 단추를 클릭한 후 제거 권한이 있는 위치만 제거해 보십시오."
- **Questions and confirmations** use the honorific ‑시겠습니까?:
  - "이 클래스를 제거하시겠습니까?"
  - "이 언어를 제거하시겠습니까?"
  - "이 초안에 저장되지 않은 변경 내용이 있습니다. 그래도 페이지를 나가시겠습니까?"
- **Error messages** state the problem in 합니다체, impersonally or with the system as subject:
  - "파일을 업로드할 수 없습니다."
  - "일부 필수 필드가 비어 있습니다."
  - "요청한 모듈 %module을(를) 찾을 수 없습니다."
- **Status messages** are passive or impersonal, past tense ‑되었습니다 or a short noun phrase:
  - "초안 %count개가 제거되었습니다."
  - "변경 내용이 저장되었습니다."
  - "일치하는 크론 작업 부분이 없습니다."
- **Tooltips that describe a click** use "클릭하면 ...": "아이콘을 클릭하면 상황에 맞는 메뉴가 표시됩니다."
- Not allowed: 당신, 너, 귀하 inside sentences; the plain style 해라체 (‑한다, ‑하라) and the informal 반말
  (‑해, ‑했어); the polite informal 해요체 (‑해요, ‑하세요) mixed into the interface; honorifics for the system
  ("시스템이 저장하셨습니다"); "님" after the user's role; the exclamation mark where the source has none.

## 3. Script and typography

- **Script**: Hangul. No Hanja. Product names, formats, codes, file names, INI settings and commands stay in Latin
  letters (TRANSLATING.md, rule 5): "Solr 인덱스", "JSON 파일", "content.ini". Latin abbreviations are not transcribed
  into Hangul ("URL 별칭", not "유알엘 별칭").
- **Capital letters** do not exist in Hangul. A Latin product name keeps its own spelling; English title case is
  never copied into the Korean text.
- **Word spacing (띄어쓰기)** follows the standard rules: particles and the copula are attached to the preceding
  word, dependent nouns are separated ("삭제할 수 없습니다", "저장한 후"). Fixed terms keep the spacing of this
  glossary ("미리 보기", "사용 안 함", "새로 고침", "웹 사이트").
- **Punctuation**: the Latin full stop, comma, colon, question mark and ellipsis, without a space before them, as in
  the source. No ideographic full stop (。).
- **Quotation marks**: double quotes “…” in running text, single quotes ‘…’ for a quote inside a quote. Inside HTML
  attributes and JavaScript the straight quotes of the source stay. 「…」 is not used.
- **Plural**: Korean has no grammatical plural; "item(s)", "object(s)" is translated by the plain noun ("항목",
  "개체"). The plural marker ‑들 is added only where the sentence needs it. Never write "항목(들)".
- **Numbers with a placeholder**: a counter or unit follows the number directly: "%count개", "%count개 항목",
  "%n분 전", "%n일". A label form is just as correct: "이 페이지의 클래스: %count", "사용자: %name", "마지막으로 수정한 사람: %name".
  Hangul directly after a placeholder is technically harmless (the checker ends the placeholder at a non-Latin
  letter); a Latin letter directly after it is not.
- **Particles after a placeholder**: the form of a particle depends on the final sound of the word before it
  (을/를, 이/가, 은/는, 와/과, 으로/로), which a placeholder does not reveal. In this order of preference:
  1. Put a Korean noun after the placeholder and attach the particle to that noun: "%objectname 개체를 복사할 위치를
     선택하십시오", "<%class_name> 클래스를 복사하시겠습니까?", "%language 언어로 번역".
  2. Use a label form: "사용자: %name".
  3. Only where neither fits, the double form of the Microsoft style guide, attached without a space:
     "%name을(를)", "%name이(가)", "%name은(는)", "%name와(과)", "%name(으)로".
  A placeholder is never split or rearranged; its position follows the Korean word order (object before verb).
- **Particles after Latin words** follow their Korean pronunciation: "URL을", "PHP를", "Solr를", "JSON을",
  "Exponential을". When unsure, use rule 1 above ("Exponential 시스템을").
- **Numbers**: digits 0–9, the comma as thousands separator and the full stop as decimal mark ("1,024", "1.5 MB").
  A Latin unit is separated by a space ("5 MB", "30 s" in a technical value); Korean units and counters are
  attached ("5분", "3시간", "10초", "2개").
- **Dates and times** in running text: "2026년 10월 23일", "오후 3:15"; the format of a value comes from the locale
  settings, not from the catalogue.

## 4. Glossary

Binding for the kernel and every extension. Where one Korean word stands for two English ones (state/status,
job/task), the context in the Note column decides, and the longer form is used where both could be meant.

| English | Korean | Note |
|---|---|---|
| content | 콘텐츠 | not 컨텐츠 |
| content object | 콘텐츠 개체 | |
| object | 개체 | Microsoft term; not 객체, not 오브젝트 |
| item | 항목 | list entry in general |
| item(s) / items | 항목 | no plural marker |
| node | 노드 | main node: 기본 노드 |
| location | 위치 | the place of an object in the tree |
| main location | 기본 위치 | |
| content class | 콘텐츠 클래스 | the content class; a PHP class: PHP 클래스 |
| class | 클래스 | |
| class group | 클래스 그룹 | |
| attribute | 속성 | class attribute: 클래스 속성; an HTML attribute: HTML 속성 |
| datatype | 데이터 형식 | |
| version | 버전 | version history: 버전 기록 |
| draft | 초안 | |
| store draft | 초안 저장 | button |
| discard draft | 초안 폐기 | button |
| discard | 폐기 | discard changes: 변경 내용 취소 |
| publish | 게시 | verb in a sentence: 게시하다 |
| published | 게시됨 | status; in a sentence: 게시되었습니다 |
| publishing | 게시 | |
| send for publishing | 게시 요청 | button |
| translation | 번역 | |
| language | 언어 | |
| main language | 기본 언어 | |
| content language | 콘텐츠 언어 | |
| interface language | 인터페이스 언어 | |
| siteaccess | 사이트 액세스 | |
| site | 사이트 | |
| website | 웹 사이트 | Microsoft spacing |
| design | 디자인 | the Exponential design |
| structure | 구조 | |
| Content structure | 콘텐츠 구조 | tab |
| Media library | 미디어 라이브러리 | tab |
| Dashboard | 대시보드 | |
| template | 템플릿 | template file: 템플릿 파일 |
| override | 재정의 | a template or setting that replaces another |
| override template | 재정의 템플릿 | |
| overridden | 재정의됨 | |
| fallback | 대체 | the one used when the first is missing |
| fallback language | 대체 언어 | fallback chain: 대체 체인 |
| alias | 별칭 | |
| URL alias | URL 별칭 | |
| wildcard | 와일드카드 | URL wildcard: URL 와일드카드 |
| redirect | 리디렉션 | Microsoft spelling |
| forward | 전달 | |
| cache | 캐시 | |
| clear cache | 캐시 지우기 | |
| workflow | 워크플로 | not 워크플로우 |
| workflow event | 워크플로 이벤트 | |
| event | 이벤트 | |
| trigger | 트리거 | |
| role | 역할 | |
| policy | 정책 | |
| limitation | 제한 | policy limitation: 정책 제한 |
| permission | 권한 | |
| access | 액세스 | access denied: 액세스 거부됨 |
| user | 사용자 | |
| user group | 사용자 그룹 | |
| user account | 사용자 계정 | |
| anonymous user | 익명 사용자 | |
| administrator | 관리자 | |
| administration interface | 관리 인터페이스 | |
| log in / sign in | 로그인 | verb in a sentence: 로그인하다; two-step sign-in: 2단계 로그인 |
| log out / sign out | 로그아웃 | |
| login (field) / username | 사용자 이름 | |
| password | 암호 | Microsoft term; not mixed with 비밀번호 |
| e-mail | 이메일 | e-mail address: 이메일 주소 |
| section | 섹션 | always the Exponential section |
| object state | 개체 상태 | the Exponential states feature |
| state | 상태 | object state; write 개체 상태 where status could be meant |
| state group | 상태 그룹 | |
| status | 상태 | of a version, job or order |
| relation | 관계 | |
| related objects | 관련 개체 | |
| reverse related objects | 이 개체를 참조하는 개체 | |
| trash | 휴지통 | empty trash: 휴지통 비우기 |
| bookmark | 책갈피 | |
| sub-items / children | 하위 항목 | |
| parent | 상위 항목 | parent node: 상위 노드 |
| subtree | 하위 트리 | |
| owner | 소유자 | |
| creator | 작성자 | |
| modifier | 수정한 사람 | |
| created | 생성일 | column heading; in a sentence: 생성되었습니다 |
| modified | 수정일 | column heading; in a sentence: 수정되었습니다 |
| layout | 레이아웃 | the page layout of Exponential Layouts; layout type: 레이아웃 유형 |
| layout (directory or release structure) | 구조 | |
| block | 블록 | |
| zone | 영역 | |
| settings | 설정 | configuration |
| setting | 설정 | |
| setup (Setup tab, Setup menu, setup wizard) | 설정 | setup as the installation process: 설치 |
| configuration | 구성 | |
| configure | 구성 | |
| extension | 확장 | an Exponential extension; file name extension: 확장명 |
| package | 패키지 | |
| plugin | 플러그인 | |
| kernel | 커널 | |
| module | 모듈 | |
| view (module view) | 보기 | module view: 모듈 보기 |
| View (button) | 보기 | |
| function (module) | 기능 | a PHP function: 함수 |
| search | 검색 | |
| search engine | 검색 엔진 | |
| search index | 검색 인덱스 | |
| index (verb) | 인덱싱 | in a sentence: 인덱싱하다 |
| reindex | 다시 인덱싱 | |
| index (database) | 인덱스 | |
| index file | 인덱스 파일 | |
| cronjob | 크론 작업 | the program name in code stays cron |
| schedule | 일정 | scheduled: 예약됨 |
| job | 작업 | job queue: 작업 큐 |
| queue | 큐 | |
| worker | 작업자 | worker process: 작업자 프로세스 |
| task | 작업 | |
| lane (queue) | 레인 | |
| log | 로그 | never the verb "log in" |
| log file | 로그 파일 | |
| rotation / rotate (log, key) | 순환 | rotated log: 순환된 로그; not 회전, which is the rotation of an image |
| audit | 감사 | |
| audit trail | 감사 내역 | |
| notification | 알림 | notification settings: 알림 설정 |
| digest (notification) | 요약 알림 | |
| collaboration | 공동 작업 | |
| preview | 미리 보기 | |
| identifier | 식별자 | |
| ID | ID | |
| remote ID | 원격 ID | |
| file | 파일 | |
| folder | 폴더 | |
| directory | 디렉터리 | Microsoft spelling |
| path | 경로 | |
| image | 이미지 | |
| media | 미디어 | |
| thumbnail | 축소판 | |
| upload | 업로드 | |
| download | 다운로드 | |
| export | 내보내기 | |
| import | 가져오기 | |
| shop | 상점 | |
| Store (shop tab) | 상점 | every other Store is 저장 |
| basket | 장바구니 | |
| order (shop) | 주문 | order status: 주문 상태 |
| order (sorting) | 순서 | sort order: 정렬 순서 |
| customer | 고객 | |
| product | 상품 | |
| checkout | 결제 | |
| VAT | 부가가치세 | VAT type: 부가가치세 유형 |
| discount | 할인 | |
| discount rule | 할인 규칙 | |
| rule | 규칙 | |
| price | 가격 | |
| currency | 통화 | |
| payment gateway | 결제 게이트웨이 | |
| wish list | 위시리스트 | |
| newsletter | 뉴스레터 | |
| subscription | 구독 | |
| subscriber | 구독자 | |
| subscribe / unsubscribe | 구독 / 구독 취소 | |
| tag (keyword, eztags) | 태그 | |
| keywords | 키워드 | |
| tag (HTML/XML) | 태그 | always with its kind: HTML 태그, XML 태그 |
| custom tag | 사용자 지정 태그 | |
| move | 이동 | |
| copy | 복사 | |
| swap | 바꾸기 | |
| embed / embedded | 포함 / 포함된 | an object embedded in rich text |
| embedding (vector) | 임베딩 | a question or text embedded as a vector: 임베딩되다; embedded in rich text: 포함된 |
| chunk | 청크 | |
| vector | 벡터 | |
| prompt | 프롬프트 | |
| model | 모델 | |
| token | 토큰 | |
| AI | AI | |
| provider | 공급자 | |
| deploy / deployment | 배포 | |
| release | 릴리스 | released (help topic): 릴리스됨; 게시됨 stays reserved for publish |
| enable | 사용 | the setting; in a sentence: 사용하도록 설정 |
| disable | 사용 안 함 | in a sentence: 사용하지 않도록 설정 |
| enabled / disabled | 사용 / 사용 안 함 | state of a setting |
| active / inactive | 활성 / 비활성 | |
| warning | 경고 | |
| error | 오류 | |
| notice | 참고 | |
| required | 필수 | |
| optional | 선택 사항 | |
| default | 기본값 | as an adjective: 기본 |
| priority | 우선순위 | |
| sort | 정렬 | |
| ascending / descending | 오름차순 / 내림차순 | also for Asc. / Desc.; never shortened to 오름/내림 |
| visible | 표시됨 | |
| hidden | 숨김 | |
| hide | 숨기기 | |
| show / reveal | 표시 | |
| expand / collapse | 펼치기 / 접기 | not 확장, which is extension |
| OK | 확인 | |
| Cancel | 취소 | |
| Yes / No | 예 / 아니요 | not 아니오 |
| Back | 뒤로 | |
| Next / Previous | 다음 / 이전 | |
| Close | 닫기 | |
| select | 선택 | |
| Select all | 모두 선택 | |
| Deselect all / Select none | 모두 선택 취소 | |
| tick (checkbox) | 선택 | "확인란을 선택하십시오"; checkbox: 확인란 |
| browse | 찾아보기 | |
| apply | 적용 | Apply changes: 변경 내용 적용 |
| save / store | 저장 | |
| edit | 편집 | |
| remove | 제거 | take out of a list, group or place |
| delete | 삭제 | destroy |
| new | 새 | button "New": 새로 만들기 |
| create | 만들기 | Create new: 새로 만들기 |
| add | 추가 | |
| refresh | 새로 고침 | |
| update | 업데이트 | |
| reset | 초기화 | |
| restore | 복원 | |
| assign / assignment | 할당 | |
| approve | 승인 | |
| approval / approved | 승인 / 승인됨 | |
| approver | 승인자 | |
| reject / rejected | 거부 / 거부됨 | |
| deny / denied | 거부 / 거부됨 | |
| accept / accepted | 수락 / 수락됨 | |
| pending | 보류 중 | pending list: 보류 목록; waiting / queued (status label): 대기 중 |
| archive (button) | 보관 | Unarchive: 보관 해제 |
| archived (version, object, order) | 보관됨 | |
| archive (place, menu entry) | 보관함 | |
| archive (file .tar, .zip, .phar) | 아카이브 파일 | |
| run (verb) | 실행 | |
| run (noun: a cron or index run) | 실행 | last run: 마지막 실행 |
| dry run | 모의 실행 | |
| self-test | 자체 테스트 | |
| debug | 디버그 | |
| editor (software) | 편집기 | template editor: 템플릿 편집기 |
| editor (person) | 편집자 | |
| statistics | 통계 | |
| system information | 시스템 정보 | |
| database | 데이터베이스 | |
| handler | 처리기 | |
| wizard | 마법사 | |
| backend | 백엔드 | |
| endpoint | 엔드포인트 | |
| method | 메서드 | payment method: 결제 수단 |
| listener | 수신기 | |
| controller | 컨트롤러 | |
| allowlist | 허용 목록 | |
| read-only | 읽기 전용 | |
| argument | 인수 | |
| slot | 슬롯 | |
| collection | 컬렉션 | MongoDB, cache collection, eztags dynamic collection; never for an information collection |
| facets | 패싯 | |
| elevate (Solr) | 상위 고정 | |
| core (Solr) | 코어 | |
| child documents (Solr) | 하위 문서 | |
| filter | 필터 | |
| details | 세부 정보 | |
| container | 컨테이너 | "Is container": 컨테이너 여부 |
| locale | 로캘 | |
| localization | 지역화 | |
| charset | 문자 집합 | |
| license | 라이선스 | |
| privilege (database) | 권한 | grant: 부여 |
| format | 형식 | file or data format |
| formatting | 서식 | rich text: 서식 있는 텍스트 |
| linked | 연결됨 | |
| common | 공통 | |
| flags | 플래그 | |
| teaser | 요약문 | |
| slug | 슬러그 | |
| placeholder | 자리 표시자 | |
| preset | 사전 설정 | |
| information collection | 정보 수집 | collector: 정보 수집기; one submitted collection: 수집 항목 ("수집 항목 %count개"); collected information: 수집한 정보; "Collects information": 정보 수집함 |
| overview | 개요 | |
| summary | 요약 | |
| tab | 탭 | |
| feed | 피드 | |
| session | 세션 | |
| scope | 범위 | |
| thread | 스레드 | a conversation or a process thread |
| chain | 체인 | |
| batch | 일괄 처리 | |
| schema | 스키마 | |
| registry | 레지스트리 | |
| manifest | 매니페스트 | |
| stylesheet | 스타일시트 | |
| theme | 테마 | |
| icon | 아이콘 | |
| port | 포트 | |
| profile | 프로필 | |
| pattern | 패턴 | |
| repository | 리포지토리 | |
| maintainer | 유지 관리자 | not 관리자, which is administrator |
| install / installation | 설치 | |
| uninstall | 설치 제거 | |
| checksum | 체크섬 | |
| quarantine | 격리 | |
| pool | 풀 | |
| mapping | 매핑 | |
| resolve (path, name) | 확인 | resolvable: 확인 가능; resolves to (URL alias): 확인 결과; becomes: 변환 결과 |
| resolver | 확인자 | |
| checkpoint | 검사점 | |
| script | 스크립트 | |
| revoke | 해지 | |
| label | 레이블 | |
| user interface | 사용자 인터페이스 | |
| page | 페이지 | |
| link | 링크 | |
| title | 제목 | |
| name | 이름 | |
| description | 설명 | |
| date / time | 날짜 / 시간 | |
| type | 유형 | |
| group | 그룹 | |
| list | 목록 | |
| value | 값 | |
| field | 필드 | |
| number | 번호 | a quantity: 수 |
| count | 개수 | |
| width / height / size | 너비 / 높이 / 크기 | |
| server / client | 서버 / 클라이언트 | |
| request / response | 요청 / 응답 | |
| context menu | 상황에 맞는 메뉴 | |
| button | 단추 | |

## 5. Decisions after the translation

These terms were settled after the first translation and complement the glossary.

| English | Korean | Note |
|---|---|---|
| cronjob part / part (cronjob.ini) | 크론 작업 부분 / 부분 | "%count개 부분", "부분 실행"; not 파트, not 영역 |
| navigation part | 탐색 부분 | 영역 is zone |
| fetch function / fetch alias | fetch 함수 / fetch 별칭 | fetch is the template function name and stays; 가져오기 is import, 페치 is not used |
| operator (template) | 연산자 | |
| extension point | 확장 지점 | survey of extension points: 확장 지점 조사 |
| comment (collaboration, approval) | 의견 | Add comment: 의견 추가 |
| comment (under an article on the site) | 댓글 | |
| comment (in code, INI, JSON, debug output) | 주석 | |
| comment (descriptive field, e.g. of a language) | 설명 | |
| secret / secret store | 비밀 값 / 비밀 값 저장소 | never bare 비밀, never 암호 (password) |
| client secret (OAuth) | 클라이언트 비밀 값 | 암호 is password; both appear on the same screens |
| storage (file storage, var/storage, DFS, object storage) | 스토리지 | object storage: 개체 스토리지; storage directory: 스토리지 디렉터리; storage key: 스토리지 키; split directory: 분할 디렉터리 |
| store (a place that keeps records: secret, session, package store) | 저장소 | the verb and the button Store stay 저장; repository stays 리포지토리; the storage method of a datatype: 저장 방식 |
| source (import, harvest, OER source; source node; origin of a value) | 원본 | source node: 원본 노드 |
| source (code, HTML source, open source) | 소스 | source code: 소스 코드; open source: 오픈 소스 |
| Source: (label naming where data was read) | 출처: | "From %file": 출처: %file |
| purge (cached pages) | 삭제 | "모든 페이지 삭제" |
| purge (trash, audit, orders, orphans, cluster files marked deleted) | 영구 삭제 | cluster purge cronjob: 클러스터 영구 삭제 크론 작업; the database's own purge (InnoDB) stays purge |
| orphan / orphaned | 분리된 / 분리됨 | orphan files: 분리된 파일; never 고아 |
| symbolic link / symlink | 심볼릭 링크 | not 기호화된 링크 |
| scan (storage, directories, log file) | 스캔 | scanned: 스캔됨; 검사 is check |
| check (a diagnostic run, health check, link check) | 검사 | check (verify, in an instruction): 확인; tick a checkbox: 선택 |
| probe / live probe | 검사 / 실시간 검사 | |
| finding (check result) | 결과 | in running text 검사 결과; not 문제점, not 발견 |
| time budget | 시간 한도 | budget (money, tokens): 예산 |
| timeout | 시간 제한 | execution time exceeded: 실행 시간 초과 |
| binary (executable) | 실행 파일 | binary data, binary file: 이진 데이터, 이진 파일; not 바이너리 |
| installer / installer source | 설치 관리자 / 설치 관리자 원본 | not 설치 프로그램 |
| queued (put into a queue) | 큐에 추가됨 | the status label "Queued": 대기 중 |
| staged / staged review | 검토 대기 / 검토 후 적용 | not 스테이징; a staging site (environment) is 스테이징 사이트 |
| placement (of an object in the tree, browse dialogs) | 배치 위치 | the bare label "Placement" and the package comparison: 배치 |
| arrangement (menu) | 배치 | |
| warm (caches) / warm-up / preload | 미리 준비 / 준비 / 미리 로드 | |
| redact / mask / unmasked | 마스킹 / 마스킹 / 마스킹 안 함 | language mask (bit mask): 언어 마스크 |
| grip, drag handle | 핸들 | not 손잡이 |
| vendor (package, author) | 공급업체 | the vendor/ directory stays as code |
| syndication / Syndicate (datatype) | 신디케이션 | not 배포 피드: 배포 is deploy |
| poll / vote | 설문 / 투표 | vote count: "%count표" |
| breadcrumb | 이동 경로 | |
| documentation / manual (book) | 설명서 | manual (by hand): 수동 |
| help topic | 도움말 주제 | |
| alert (audit) | 경보 | warning: 경고; notification: 알림 |
| fingerprint | 지문 | |
| actor / severity | 행위자 / 심각도 | |
| deprecation | 사용 중단 | deprecation warning: 사용 중단 경고 |
| machine (server, workstation) | 컴퓨터 | |
| asset (content check) | 자산 | |
| Unicode | 유니코드 | |
| hit / miss / hit rate (cache) | 적중 / 누락 / 적중률 | |
| property (PHP, CSS, object) | 속성 | same word as attribute; the context distinguishes them |
| signature (method) | 시그니처 | digital signature: 서명 |
| Skipped (status) / Skip (button) | 건너뜀 / 건너뛰기 | |
| Never (date column) | 없음 | |
