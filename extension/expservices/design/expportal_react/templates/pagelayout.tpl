{* Exponential portal, React design. The page content of the eZ view is not used: the
   single page app in javascript/portal/ draws everything from the expservices calls. *}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exponential Portal (React)</title>
<link rel="stylesheet" href={'stylesheets/vendor/bootstrap.min.css'|ezdesign}>
<link rel="stylesheet" href={'stylesheets/portal.css'|ezdesign}>
</head>
<body>
<div id="root" data-services={'ezjscore/call'|ezurl} data-site={'/'|ezurl}>
<noscript>This portal needs JavaScript.</noscript>
</div>
<script src={'javascript/vendor/react.production.min.js'|ezdesign}></script>
<script src={'javascript/vendor/react-dom.production.min.js'|ezdesign}></script>
<script src={'javascript/vendor/react-bootstrap.min.js'|ezdesign}></script>
<script type="module" src={'javascript/portal/app.js'|ezdesign}></script>
</body>
</html>
