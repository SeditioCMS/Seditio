<!-- BEGIN: ADMIN_REVISIONS -->

<div class="title">
	<span><i class="ic-rotate-clockwise"></i></span>
	<h2>{ADMIN_REVISIONS_TITLE}</h2>
</div>

<!-- Navigation Shortcut Buttons -->
<ul class="shortcut-buttons-set">
	<li><a class="shortcut-button {REVISIONS_SUBNAV_ALL_SELECTED}" href="{REVISIONS_SUBNAV_ALL_URL}"><span>
		<i class="ic-notebook ic-3x"></i><br />
		{PHP.L.rev_all_revisions}
	</span></a></li>
	<li><a class="shortcut-button {REVISIONS_SUBNAV_MAINTENANCE_SELECTED}" href="{REVISIONS_SUBNAV_MAINTENANCE_URL}"><span>
		<i class="ic-trash ic-3x"></i><br />
		{PHP.L.rev_maintenance}
	</span></a></li>
	<li><a class="shortcut-button" href="{REVISIONS_CONFIG_URL}"><span>
		<i class="ic-settings ic-3x"></i><br />
		{PHP.L.Configuration}
	</span></a></li>
</ul>
<div class="clear"></div>

<!-- BEGIN: REVISIONS_LIST -->
<div class="content-box">

	<div class="content-box-header">
		<h3>{PHP.L.rev_title} ({ADMIN_REVISIONS_TOTALITEMS})</h3>
		<div class="content-box-header-right">
			{PHP.L.Type} {ADMIN_REVISIONS_TYPE_SELECT}
		</div>
	</div>

	<div class="content-box-content content-table">

		<!-- Filter and Search Bar -->
		<div class="table-filters">
			<form action="{ADMIN_REVISIONS_SEARCH_ACTION_URL}" method="get" class="form-inline">
				<input type="hidden" name="m" value="revisions" />
				<input type="hidden" name="type" value="{ADMIN_REVISIONS_CURRENT_TYPE}" />

				<div>
					{ADMIN_REVISIONS_SEARCH_INPUT}
				</div>

				<div>
					<button type="submit" class="btn btn-primary"><i class="ic-search"></i> {PHP.L.Search}</button>
					<!-- IF {ADMIN_REVISIONS_SEARCH_QUERY} != '' -->
					<a href="{ADMIN_REVISIONS_SEARCH_RESET_URL}" class="btn" title="{PHP.L.Reset}"><i class="ic-close"></i></a>
					<!-- ENDIF -->
				</div>

				<div class="table-filters-perpage">
					<span>{PHP.L.Show}:</span>
					{ADMIN_REVISIONS_PERPAGE_TOP}
				</div>
			</form>
		</div>

		<!-- BEGIN: REVISIONS_NO_ITEMS -->
		<div class="notification information png_bg">
			<div>{PHP.L.rev_no_revisions}</div>
		</div>
		<!-- END: REVISIONS_NO_ITEMS -->

		<!-- BEGIN: REVISIONS_ITEMS -->
		<div class="table cells striped resp-table">

			<div class="table-head resp-table-head">
				<div class="table-row resp-table-row">
					<div class="table-th coltop text-left" style="width:65px;">#ID</div>
					<div class="table-th coltop text-left" style="width:110px;">{PHP.L.Type}</div>
					<div class="table-th coltop text-left">{PHP.L.Title}</div>
					<div class="table-th coltop text-center" style="width:70px;">{PHP.L.rev_version}</div>
					<div class="table-th coltop text-left" style="width:140px;">{PHP.L.Date}</div>
					<div class="table-th coltop text-left" style="width:140px;">{PHP.L.Author}</div>
					<div class="table-th coltop text-left">{PHP.L.rev_comment}</div>
					<div class="table-th coltop text-center" style="width:80px;">{PHP.L.rev_size}</div>
					<div class="table-th coltop text-center" style="width:110px;">{PHP.L.Action}</div>
				</div>
			</div>

			<div class="table-body resp-table-body">

				<!-- BEGIN: REVISIONS_ROW -->
				<div class="table-row resp-table-row">
					<div class="table-td text-left resp-table-td" data-label="#ID">
						#{REVISIONS_LIST_ID}
					</div>
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Type}">
						<span class="badge">{REVISIONS_LIST_TYPE_TITLE}</span>
					</div>
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Title}">
						<!-- IF {REVISIONS_LIST_EDIT_URL} -->
						<a href="{REVISIONS_LIST_EDIT_URL}"><strong>{REVISIONS_LIST_TITLE}</strong></a>
						<!-- ELSE -->
						<strong>{REVISIONS_LIST_TITLE}</strong>
						<!-- ENDIF -->
						<small class="descr">(#{REVISIONS_LIST_ITEMID})</small>
					</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.rev_version}">
						<strong>v{REVISIONS_LIST_VERSION}</strong>
					</div>
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Date}">{REVISIONS_LIST_DATE}</div>
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Author}"><i class="ic-user"></i> {REVISIONS_LIST_AUTHOR}</div>
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.rev_comment}">{REVISIONS_LIST_COMMENT}</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.rev_size}">{REVISIONS_LIST_SIZE}</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.Action}">
						<a href="{REVISIONS_LIST_DIFF_URL}" class="btn btn-small" title="{PHP.L.rev_diff}"><i class="ic-search"></i></a>
						<a href="{REVISIONS_LIST_RESTORE_URL}" class="btn btn-small" title="{PHP.L.rev_restore}" onclick="return sedjs.confirmact('{PHP.L.rev_restore_confirm}');"><i class="ic-refresh"></i></a>
						<a href="{REVISIONS_LIST_DELETE_URL}" class="btn btn-small" title="{PHP.L.Delete}" onclick="return sedjs.confirmact('{PHP.L.rev_delete_confirm}');"><i class="ic-trash"></i></a>
					</div>
				</div>
				<!-- END: REVISIONS_ROW -->

			</div>

		</div>

		<!-- BEGIN: REVISIONS_PAGINATION_BM -->
		<div class="paging">
			<ul class="pagination">
				<li class="prev">{REVISIONS_PAGEPREV}</li>
				{REVISIONS_PAGINATION}
				<li class="next">{REVISIONS_PAGENEXT}</li>
			</ul>
		</div>
		<!-- END: REVISIONS_PAGINATION_BM -->

		<!-- END: REVISIONS_ITEMS -->

	</div>

</div>
<!-- END: REVISIONS_LIST -->

<!-- BEGIN: REVISIONS_MAINTENANCE -->
<div class="content-box">

	<div class="content-box-header">
		<h3>{PHP.L.rev_maintenance}</h3>
		<div class="clear"></div>
	</div>

	<div class="content-box-content content-table">

		<div class="notification information png_bg">
			<div>
				<strong>{PHP.L.rev_total_count}:</strong> {ADMIN_REVISIONS_TOTALITEMS} &nbsp;|&nbsp; <strong>{PHP.L.rev_total_size}:</strong> {ADMIN_REVISIONS_TOTALSIZE}
			</div>
		</div>

		<div class="table cells striped resp-table">

			<div class="table-head resp-table-head">
				<div class="table-row resp-table-row">
					<div class="table-th coltop text-left">{PHP.L.Description}</div>
					<div class="table-th coltop text-center" style="width:280px;">{PHP.L.Action}</div>
				</div>
			</div>

			<div class="table-body resp-table-body">

				<div class="table-row resp-table-row">
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Description}">
						<strong>{PHP.L.rev_prune_custom_title}</strong><br />
						<small class="descr">{PHP.L.rev_prune_custom_desc}</small>
					</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.Action}">
						<form action="{REVISIONS_PRUNE_ACTION_URL}" method="post" class="form-inline" style="display:inline-flex; align-items:center; justify-content:center; gap:6px;">
							<span>{PHP.L.rev_prune_older_than_days}:</span>
							{REVISIONS_PRUNE_DAYS_INPUT}
							<button type="submit" class="btn btn-small" onclick="return sedjs.confirmact('{PHP.L.Confirm}');"><i class="ic-trash"></i> {PHP.L.rev_prune_btn}</button>
						</form>
					</div>
				</div>

				<div class="table-row resp-table-row">
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Description}">
						<strong>{PHP.L.rev_prune_quick_title}</strong><br />
						<small class="descr">{PHP.L.rev_prune_quick_desc}</small>
					</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.Action}">
						<a href="{REVISIONS_PRUNE_30_URL}" class="btn btn-small" onclick="return sedjs.confirmact('{PHP.L.Confirm}');"><i class="ic-trash"></i> 30 {PHP.L.Days}</a>
						<a href="{REVISIONS_PRUNE_60_URL}" class="btn btn-small" onclick="return sedjs.confirmact('{PHP.L.Confirm}');"><i class="ic-trash"></i> 60 {PHP.L.Days}</a>
						<a href="{REVISIONS_PRUNE_90_URL}" class="btn btn-small" onclick="return sedjs.confirmact('{PHP.L.Confirm}');"><i class="ic-trash"></i> 90 {PHP.L.Days}</a>
					</div>
				</div>

				<div class="table-row resp-table-row">
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Description}">
						<strong>{PHP.L.rev_optimize_title}</strong><br />
						<small class="descr">{PHP.L.rev_optimize_desc}</small>
					</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.Action}">
						<a href="{REVISIONS_OPTIMIZE_URL}" class="btn btn-small" onclick="return sedjs.confirmact('{PHP.L.Confirm}');"><i class="ic-refresh"></i> {PHP.L.rev_optimize_btn}</a>
					</div>
				</div>

				<div class="table-row resp-table-row">
					<div class="table-td text-left resp-table-td" data-label="{PHP.L.Description}">
						<strong>{PHP.L.rev_wipeall_title}</strong><br />
						<small class="descr">{PHP.L.rev_wipeall_desc}</small>
					</div>
					<div class="table-td text-center resp-table-td" data-label="{PHP.L.Action}">
						<a href="{REVISIONS_WIPEALL_URL}" class="btn btn-small" onclick="return sedjs.confirmact('{PHP.L.rev_wipeall_confirm}');"><i class="ic-trash"></i> {PHP.L.Wipeall}</a>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>
<!-- END: REVISIONS_MAINTENANCE -->

<!-- END: ADMIN_REVISIONS -->
