<!-- BEGIN: MAIN -->

<div id="title">

	{FORUMS_SECTIONS_PAGETITLE}

</div>

<div id="subtitle">

	<ul style=" padding:2px; margin:0;">
		<li style="display:inline; list-style:none; padding-right:8px;"><a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a></li>
		<li style="display:inline; list-style:none; padding-right:8px;"><a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a></li>
		<li style="display:inline; list-style:none; padding-right:8px;">{FORUMS_SECTIONS_GMTTIME}</li>
	</ul>

</div>

<div id="main">

{FORUMS_SECTIONS_MYPOSTS}

<table class="cells">

	<thead>
	<tr>
		<td class="coltop" colspan="2">{PHP.skinlang.forumssections.Sections}  &nbsp;  &nbsp; <a href="forums.php?c=fold#top">{PHP.skinlang.forumssections.FoldAll}</a> / <a href="forums.php?c=unfold#top">{PHP.skinlang.forumssections.UnfoldAll}</a></td>
		<td class="coltop" style="width:176px;">{PHP.skinlang.forumssections.Lastpost}</td>
		<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Topics}</td>
		<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Posts}</td>
		<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Views}</td>
		<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Activity}</td>
	</tr>
	</thead>

	<!-- BEGIN: FORUMS_SECTIONS_ROW -->

	<!-- BEGIN: FORUMS_SECTIONS_ROW_CAT -->

	{FORUMS_SECTIONS_ROW_TBODY_END}
	<tbody id="{FORUMS_SECTIONS_ROW_CAT_CODE}">

	<tr>
		<td colspan="7" style="padding:4px;">
		<strong>{FORUMS_SECTIONS_ROW_CAT_TITLE}</strong>
		</td>
	</tr>

	{FORUMS_SECTIONS_ROW_CAT_TBODY}

	<!-- END: FORUMS_SECTIONS_ROW_CAT -->
	

	<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->

	<tr>
		<td style="width:32px;" class="centerall">
			<img src="{FORUMS_SECTIONS_ROW_ICON}" alt="" />
		</td>

		<td>
		<h3 style="margin:4px;"><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
		&nbsp;{FORUMS_SECTIONS_ROW_DESC}
		
    <!-- BEGIN: FORUMS_SECTIONS_ROW_SUBFORUMS -->
    <div class="subforums">
      <ul>	
	    <!-- BEGIN: FORUMS_SECTIONS_ROW_SUBFORUMS_LIST -->
			<li><a href="{FORUMS_SECTIONS_ROW_SUBFORUMS_URL}">{FORUMS_SECTIONS_ROW_SUBFORUMS_TITLE}</a></li>	
			<!-- END: FORUMS_SECTIONS_ROW_SUBFORUMS_LIST -->
	    </ul>    
    </div>
    <!-- END: FORUMS_SECTIONS_ROW_SUBFORUMS -->
    
    </td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_LASTPOST}<br />
		{FORUMS_SECTIONS_ROW_LASTPOSTDATE} {FORUMS_SECTIONS_ROW_LASTPOSTER}<br />
		{FORUMS_SECTIONS_ROW_TIMEAGO}
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_TOPICCOUNT_ALL}<br />
		<span class="desc">({FORUMS_SECTIONS_ROW_TOPICCOUNT})</span>
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_POSTCOUNT_ALL}<br />
		<span class="desc">({FORUMS_SECTIONS_ROW_POSTCOUNT})</span>
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_VIEWCOUNT_SHORT}
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_ACTIVITY}
		</td>

	</tr>		

	<!-- END: FORUMS_SECTIONS_ROW_SECTION -->

	<!-- END: FORUMS_SECTIONS_ROW -->
	</tbody>

</table>

</div>

<!-- END: MAIN -->