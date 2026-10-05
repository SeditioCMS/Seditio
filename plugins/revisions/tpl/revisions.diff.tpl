<!-- BEGIN: ADMIN_REVISIONS_DIFF -->

<div class="title">
	<span><i class="ic-rotate-clockwise"></i></span>
	<h2>{REVISIONS_DIFF_TITLE}</h2>
</div>

<ul class="shortcut-buttons-set">
	<li><a class="shortcut-button" href="{REVISIONS_DIFF_BACK_URL}"><span>
		<i class="ic-arrow-left ic-3x"></i><br />
		{PHP.L.rev_back_to_list}
	</span></a></li>
	<!-- IF {REVISIONS_DIFF_EDIT_URL} -->
	<li><a class="shortcut-button" href="{REVISIONS_DIFF_EDIT_URL}"><span>
		<i class="ic-pencil ic-3x"></i><br />
		{PHP.L.rev_back_to_edit}
	</span></a></li>
	<!-- ENDIF -->
	<li><a class="shortcut-button" href="{REVISIONS_DIFF_RESTORE_URL}" onclick="return sedjs.confirmact('{PHP.L.rev_restore_confirm}');"><span>
		<i class="ic-repeat ic-3x"></i><br />
		{PHP.L.rev_restore}
	</span></a></li>
</ul>
<div class="clear"></div>

<!-- BEGIN: REVISIONS_DIFF_INFO -->
<div class="notification information png_bg">
	<div>
		<strong>{PHP.L.rev_version} v{REVISIONS_DIFF_OLD_VERSION}:</strong> {REVISIONS_DIFF_OLD_DATE} ({PHP.L.rev_author}: {REVISIONS_DIFF_OLD_AUTHOR})<br />
		<strong>{PHP.L.rev_comment}:</strong> {REVISIONS_DIFF_OLD_COMMENT}
	</div>
</div>
<!-- END: REVISIONS_DIFF_INFO -->

<!-- BEGIN: REVISIONS_DIFF_NO_CHANGES -->
<div class="notification success png_bg">
	<div>{PHP.L.rev_diff_no_changes}</div>
</div>
<!-- END: REVISIONS_DIFF_NO_CHANGES -->

<!-- BEGIN: REVISIONS_DIFF_FIELDS -->
<div class="content-box">

	<div class="content-box-header">
		<h3>{PHP.L.rev_diff}</h3>
		<div class="clear"></div>
	</div>

	<div class="content-box-content content-table">

		<div class="table cells striped resp-table">

			<div class="table-head resp-table-head">
				<div class="table-row resp-table-row">
					<div class="table-th coltop text-left" style="width:200px;">{PHP.L.Field}</div>
					<div class="table-th coltop text-left">{PHP.L.Value} / Diff</div>
				</div>
			</div>

			<div class="table-body resp-table-body">

				<!-- BEGIN: REVISIONS_DIFF_ROW -->
				<div class="table-row resp-table-row">
					<div class="table-td resp-table-td text-left" data-label="{PHP.L.Field}">
						<strong>{REVISIONS_DIFF_FIELD_TITLE}</strong><br />
						<small class="descr">{REVISIONS_DIFF_FIELD_CODE}</small>
					</div>
					<div class="table-td resp-table-td text-left" data-label="{PHP.L.Value}">
						<!-- BEGIN: REVISIONS_DIFF_LINES -->
						<div class="diff-block">
							<!-- BEGIN: LINE_SAME -->
							<div class="diff-line diff-same">{REVISIONS_DIFF_LINE_VAL}</div>
							<!-- END: LINE_SAME -->
							<!-- BEGIN: LINE_DEL -->
							<div class="diff-line diff-del"><del>{REVISIONS_DIFF_LINE_VAL}</del></div>
							<!-- END: LINE_DEL -->
							<!-- BEGIN: LINE_ADD -->
							<div class="diff-line diff-add"><ins>{REVISIONS_DIFF_LINE_VAL}</ins></div>
							<!-- END: LINE_ADD -->
						</div>
						<!-- END: REVISIONS_DIFF_LINES -->
					</div>
				</div>
				<!-- END: REVISIONS_DIFF_ROW -->

			</div>

		</div>

		<div style="text-align:center; padding:15px 0;">
			<a href="{REVISIONS_DIFF_RESTORE_URL}" class="submit btn" onclick="return sedjs.confirmact('{PHP.L.rev_restore_confirm}');">
				<i class="ic-repeat"></i> {PHP.L.rev_restore}
			</a>
			<a href="{REVISIONS_DIFF_BACK_URL}" class="btn">
				{PHP.L.Back}
			</a>
		</div>

	</div>

</div>
<!-- END: REVISIONS_DIFF_FIELDS -->

<!-- END: ADMIN_REVISIONS_DIFF -->
