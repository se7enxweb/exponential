<meta http-equiv="Refresh" content="1; URL={"shop/checkout"|ezurl(no)}" />

<h2>{"Waiting for a response from the payment server. This can take some time." | i18n("design/standard/shop")}</h2>
<br/>
{"Retrying to get a valid response." | i18n("design/standard/shop")}
{'(Retry: %attempt out of %max).'|i18n( 'design/standard/shop',, hash( '%attempt', $attempt, '%max', 3 ) )}
<br/><br/>
{"If your page does not automatically refresh then press the refresh button manually." | i18n("design/standard/shop")}
