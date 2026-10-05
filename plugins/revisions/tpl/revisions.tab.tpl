<!-- BEGIN: MAIN -->

<!-- BEGIN: NO_REVISIONS -->
<div class="notification information png_bg">
	<div>{PHP.L.rev_no_revisions}</div>
</div>
<!-- END: NO_REVISIONS -->

<!-- BEGIN: REVISIONS_LIST -->
<div class="table cells striped resp-table">

	<div class="table-head resp-table-head">
		<div class="table-row resp-table-row">
			<div class="table-th coltop text-center" style="width:70px;">{PHP.L.rev_version}</div>
			<div class="table-th coltop text-left" style="width:160px;">{PHP.L.rev_date}</div>
			<div class="table-th coltop text-left" style="width:140px;">{PHP.L.rev_author}</div>
			<div class="table-th coltop text-left">{PHP.L.rev_comment}</div>
			<div class="table-th coltop text-center" style="width:90px;">{PHP.L.rev_size}</div>
			<div class="table-th coltop text-center" style="width:140px;">{PHP.L.Action}</div>
		</div>
	</div>

	<div class="table-body resp-table-body">

		<!-- BEGIN: REVISIONS_ROW -->
		<div class="table-row resp-table-row">
			<div class="table-td resp-table-td text-center" data-label="{PHP.L.rev_version}">
				<strong>v{REVISIONS_ROW_VERSION}</strong>
				<!-- BEGIN: LATEST_BADGE -->
				<span class="badge" style="font-size:10px;">{PHP.L.rev_current_label}</span>
				<!-- END: LATEST_BADGE -->
			</div>
			<div class="table-td resp-table-td text-left" data-label="{PHP.L.rev_date}">{REVISIONS_ROW_DATE}</div>
			<div class="table-td resp-table-td text-left" data-label="{PHP.L.rev_author}"><i class="ic-user"></i> {REVISIONS_ROW_AUTHOR}</div>
			<div class="table-td resp-table-td text-left" data-label="{PHP.L.rev_comment}">{REVISIONS_ROW_COMMENT}</div>
			<div class="table-td resp-table-td text-center" data-label="{PHP.L.rev_size}">{REVISIONS_ROW_SIZE}</div>
			<div class="table-td resp-table-td text-center" data-label="{PHP.L.Action}">
				<a href="{REVISIONS_ROW_DIFF_URL}" class="btn btn-small" title="{PHP.L.rev_diff}"><i class="ic-search"></i></a>
				<!-- IF {REVISIONS_ROW_CAN_RESTORE} -->
				<a href="{REVISIONS_ROW_RESTORE_URL}" class="btn btn-small" title="{PHP.L.rev_restore}" onclick="return sedjs.confirmact('{PHP.L.rev_restore_confirm}');"><i class="ic-refresh"></i></a>
				<!-- ENDIF -->
				<a href="{REVISIONS_ROW_DELETE_URL}" class="btn btn-small" title="{PHP.L.Delete}" onclick="return sedjs.confirmact('{PHP.L.rev_delete_confirm}');"><i class="ic-trash"></i></a>
			</div>
		</div>
		<!-- END: REVISIONS_ROW -->

	</div>

</div>
<!-- END: REVISIONS_LIST -->

<!-- END: MAIN -->
