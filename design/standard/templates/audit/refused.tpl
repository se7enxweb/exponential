{* audit/refused: a POST refused because the audit cannot record it ([AuditSettings] OnWriteFailure=refuse;
   expAuditGuard::refusedResult(), answered with 503). The reason with its paths is in error.log (AUDIT-REFUSED),
   not on the page. Variables: view, channel, event_name. *}
<div class="message-error" id="exp-audit-refused" role="alert">
<h2>{'Not done: the audit cannot record it'|i18n( 'design/standard/audit' )}</h2>
<p>{'The audit log cannot be written at the moment (channel %channel), and this site is set to refuse security-relevant actions that cannot be recorded. Nothing was changed.'|i18n( 'design/standard/audit',, hash( '%channel', $channel ) )|wash}</p>
<p>{'Please try again later, or tell the administrator: the details are in the error log under AUDIT-REFUSED.'|i18n( 'design/standard/audit' )|wash}</p>
</div>
