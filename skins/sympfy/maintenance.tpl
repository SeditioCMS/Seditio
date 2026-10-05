<!-- BEGIN: MAINTENANCE -->

{MAINTENANCE_HEADER1}

<title>{MAINTENANCE_MAINTITLE} - {MAINTENANCE_SUBTITLE}</title>
<link href="skins/{PHP.skin}/css/maintenance.css" type="text/css" rel="stylesheet">

{MAINTENANCE_HEADER2}

<div class="maintenance-card">
  <div class="maintenance-header">
    <h2>{PHP.L.Maintenance}</h2>
    <div class="maintenance-reason">{MAINTENANCE_REASON}</div>
  </div>

  <!-- BEGIN: MAINTENANCE_ERROR -->
  <div class="maintenance-error">{MAINTENANCE_ERROR_BODY}</div>
  <!-- END: MAINTENANCE_ERROR -->

  <form name="login" class="maintenance-form" action="{MAINTENANCE_FORM_SEND}" method="post">
    <div class="form-group">
      <label>{PHP.L.Username}</label>
      {MAINTENANCE_USER}
    </div>
    <div class="form-group">
      <label>{PHP.L.Password}</label>
      {MAINTENANCE_PASSWORD}
    </div>
    <div class="form-group">
      <input type="submit" class="btn-submit" value="{PHP.L.Login}">
    </div>
  </form>
</div>

{MAINTENANCE_FOOTER}

<!-- END: MAINTENANCE -->