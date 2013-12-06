<!-- BEGIN: MAIN -->

<div id="title">
  <h2>{PAGEEDIT_PAGETITLE}</h2>
</div>

<div id="bolded-line"></div>

<div id="subtitle">
	{PAGEEDIT_SUBTITLE}
</div>

<div id="page">

<!-- BEGIN: PAGEEDIT_ERROR -->

<div class="error">

	{PAGEEDIT_ERROR_BODY}

</div>

<!-- END: PAGEEDIT_ERROR -->

<form action="{PAGEEDIT_FORM_SEND}" method="post" name="update">

<table class="cells striped" class="simple tableforms">

	<tr>
		<td style="width:176px;">{PHP.skinlang.pageedit.Category}</td>
		<td>{PAGEEDIT_FORM_CAT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Title}</td>
		<td>{PAGEEDIT_FORM_TITLE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Description}</td>
		<td>{PAGEEDIT_FORM_DESC}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Author}</td>
		<td>{PAGEEDIT_FORM_AUTHOR}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Owner}</td>
		<td>{PAGEEDIT_FORM_OWNERID}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Date}</td>
		<td>{PAGEEDIT_FORM_DATE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Begin}</td>
		<td>{PAGEEDIT_FORM_BEGIN}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Expire}</td>
		<td>{PAGEEDIT_FORM_EXPIRE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Pagehitcount}</td>
		<td>{PAGEEDIT_FORM_PAGECOUNT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Extrakey}</td>
		<td>{PAGEEDIT_FORM_KEY}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Alias}</td>
		<td>{PAGEEDIT_FORM_ALIAS}</td>
	</tr>

  <!-- BEGIN: PAGEEDIT_PARSING -->

  <tr>
    <td>{PHP.skinlang.pageedit.Parsing}</td>
    <td>{PAGEEDIT_FORM_TYPE}</td>
  </tr>   
  
  <!-- END: PAGEEDIT_PARSING -->

	<tr>
		<td colspan="2">{PHP.skinlang.pageedit.Bodyofthepage}<br /><br />{PAGEEDIT_FORM_TEXT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Allowcomments}</td>
		<td>{PAGEEDIT_FORM_ALLOWCOMMENTS}</td>
	</tr>	

	<tr>
		<td>{PHP.skinlang.pageedit.Allowratings}</td>
		<td>{PAGEEDIT_FORM_ALLOWRATINGS}</td>
	</tr>	

	<tr>
		<td>{PHP.skinlang.pageedit.Filedownload}</td>
		<td>{PAGEEDIT_FORM_FILE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.URL}<br />
		{PHP.skinlang.pageedit.URLhint}</td>
		<td>{PAGEEDIT_FORM_URL}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Filesize}<br />
		{PHP.skinlang.pageedit.Filesizehint}</td>
		<td>{PAGEEDIT_FORM_SIZE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Filehitcount}<br />
		{PHP.skinlang.pageedit.Filehitcounthint}</td>
		<td>{PAGEEDIT_FORM_FILECOUNT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Pageid}</td>
		<td>#{PAGEEDIT_FORM_ID}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Deletethispage}</td>
		<td>{PAGEEDIT_FORM_DELETE}</td>
	</tr>

	<tr>
		<td colspan="2" class="valid">
		<input type="submit" class="submit btn btn-big" value="{PHP.skinlang.pageedit.Update}">
		<!-- BEGIN: PAGEEDIT_PUBLISH -->
		<input type="submit" class="submit btn btn-big" name="rpagepublish" value="{PAGEEDIT_FORM_PUBLISH_TITLE}" onclick="this.value='{PAGEEDIT_FORM_PUBLISH_STATE}'; return true" />
		<!-- END: PAGEEDIT_PUBLISH -->
		</td>
	</tr>

</table>

</form>

</div>

<!-- END: MAIN -->