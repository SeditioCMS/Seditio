<!-- BEGIN: ADMIN_HOME -->

<div class="dashboard-header">
	<div class="title">
		<span><i class="ic-home"></i></span>
		<h2>{ADMIN_HOME_TITLE}</h2>
	</div>
	<div class="dashboard-actions">
		<a href="{HOME_CLEARCACHE_URL}" class="btn-clearcache">
			<i class="ic-refresh"></i> <span>{PHP.L.adm_clearcache}</span>
		</a>
	</div>
</div>

{ADMIN_QV}

<div class="dashboard-bottom-grid">

	<!-- Block 1: SQL Database Statistics -->
	{ADMIN_QV_DB}

	<!-- Block 2: Information (Tabs: About System & Upgrade) -->
	<div class="dashboard-card sedtabs info-tabs-card">
		<div class="card-header">
			<h3 class="tab-title">{PHP.L.adm_infos}</h3>
			<ul class="card-tabs">
				<!-- BEGIN: ADMIN_INFOS_TAB -->
				<li><a href="{PHP.sys.request_uri}#tab-system" data-tabtitle="{PHP.L.adm_about_system}">{PHP.L.adm_about_system}</a></li>
				<!-- END: ADMIN_INFOS_TAB -->
				<!-- BEGIN: ADMIN_UPG_TAB -->
				<li><a href="{PHP.sys.request_uri}#tab-upgrade" data-tabtitle="{PHP.L.upg_upgrade}">{PHP.L.upg_upgrade}</a></li>
				<!-- END: ADMIN_UPG_TAB -->
			</ul>
		</div>
		<div class="card-body">
			<!-- BEGIN: ADMIN_INFOS_TABBODY -->
			<div class="tab-content" id="tab-system">
				<div class="table cells striped resp-table no-label">
					<div class="table-body resp-table-body">
						<div class="table-row resp-table-row">
							<div class="table-td resp-table-td">Seditio:</div>
							<div class="table-td resp-table-td text-right"><strong>186</strong></div>
						</div>
						<div class="table-row resp-table-row">
							<div class="table-td resp-table-td">{PHP.L.adm_phpver}:</div>
							<div class="table-td resp-table-td text-right"><strong>{INFOS_PHPVERSION}</strong></div>
						</div>
						<div class="table-row resp-table-row">
							<div class="table-td resp-table-td">{PHP.L.adm_zendver}:</div>
							<div class="table-td resp-table-td text-right"><strong>{INFOS_ZENDVERSION}</strong></div>
						</div>
						<div class="table-row resp-table-row">
							<div class="table-td resp-table-td">{PHP.L.adm_interface}:</div>
							<div class="table-td resp-table-td text-right"><strong>{INFOS_INTERFACE}</strong></div>
						</div>
						<div class="table-row resp-table-row">
							<div class="table-td resp-table-td">SQL:</div>
							<div class="table-td resp-table-td text-right"><strong>{INFOS_MYSQL}</strong></div>
						</div>
						<div class="table-row resp-table-row">
							<div class="table-td resp-table-td">{PHP.L.adm_os}:</div>
							<div class="table-td resp-table-td text-right"><strong>{INFOS_OS}</strong></div>
						</div>
					</div>
				</div>
			</div>
			<!-- END: ADMIN_INFOS_TABBODY -->

			<!-- BEGIN: ADMIN_UPG_TABBODY -->
			<div class="tab-content content-table" id="tab-upgrade">
				<form id="forcesqlversion" action="{UPG_FORCESQLVERSION_SEND}" method="post">
					<div class="table cells striped resp-table no-label">
						<div class="table-body resp-table-body">
							<div class="table-row resp-table-row">
								<div class="table-td resp-table-td">{PHP.L.upg_codeversion}:</div>
								<div class="table-td resp-table-td text-right"><strong>{UPG_VERSION}</strong></div>
							</div>
							<div class="table-row resp-table-row">
								<div class="table-td resp-table-td">{PHP.L.upg_sqlversion}:</div>
								<div class="table-td resp-table-td text-right"><strong>{UPG_SQLVERSION}</strong></div>
							</div>
							<div class="table-row resp-table-row">
								<div class="table-td resp-table-td">{UPG_CHECKSTATUS}</div>
								<div class="table-td resp-table-td text-right">{UPG_STATUS}</div>
							</div>
						</div>
					</div>
					<div class="table-btn text-center margin-top-15">
						{UPG_FORCESQL} <button type="submit" class="submit btn btn-sm">{PHP.L.Update}</button>
					</div>
				</form>
			</div>
			<!-- END: ADMIN_UPG_TABBODY -->
		</div>
	</div>

	<!-- Block 3: Seditio News -->
	<div class="dashboard-card news-card">
		<div class="card-header">
			<h3><i class="ic-news"></i> {ADMIN_RSS_NEWS_TAB_TITLE}</h3>
		</div>
		<div class="card-body">
			<!-- BEGIN: ADMIN_RSS_NEWS_TABBODY -->
			{ADMIN_RSS_NEWS}
			<!-- END: ADMIN_RSS_NEWS_TABBODY -->
		</div>
	</div>

</div>

<!-- END: ADMIN_HOME -->