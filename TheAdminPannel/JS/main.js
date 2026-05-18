document.addEventListener('DOMContentLoaded', () => {
	const mainContent = document.getElementById('main-content');
	const sidebarNav = document.getElementById('sidebar-nav');

	if (!mainContent || !sidebarNav) {
		return;
	}

	const navButtons = Array.from(sidebarNav.querySelectorAll('button[data-tab]'));

	const ICONS = {
		users: '<svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
		droplet: '<svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>',
		clock: '<svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
		building: '<svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path></svg>',
		mail: '<svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
		phone: '<svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>'
	};

	navButtons.forEach((button) => {
		button.addEventListener('click', () => {
			navButtons.forEach((item) => item.classList.remove('active'));
			button.classList.add('active');
			renderScreen(button.dataset.tab || 'dashboard');
		});
	});

	renderScreen(navButtons[0]?.dataset.tab || 'dashboard');

	function renderScreen(tab) {
		switch (tab) {
			case 'dashboard':
				renderDashboard();
				break;
			case 'donors':
				renderSimplePage('Donors', 'No donor data is loaded in this simplified portal.');
				break;
			case 'inventory':
				renderSimplePage('Inventory', 'Inventory records are intentionally omitted.');
				break;
			case 'requests':
				renderSimplePage('Requests', 'Request records are intentionally omitted.');
				break;
			case 'hospitals':
				renderSimplePage('Hospitals', 'Hospital records are intentionally omitted.');
				break;
			case 'staff':
				renderSimplePage('Staff', 'Staff records are intentionally omitted.');
				break;
			default:
				renderDashboard();
		}
	}

	function renderDashboard() {
		mainContent.innerHTML = `
			<div class="space-y-8">
				<div>
					<h1 class="text-2xl font-bold">BBMS Overview</h1>
					<p class="text-gray-500 text-sm">A simple admin portal for the main website.</p>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
					<div class="card p-6">
						<div class="flex items-center justify-between mb-4">
							<div class="bg-red-50 text-red-600 p-2 rounded-lg">${ICONS.users}</div>
						</div>
						<p class="text-gray-500 text-sm font-medium">Donors</p>
						<h3 class="text-2xl font-bold mt-1">Managed</h3>
					</div>
					<div class="card p-6">
						<div class="flex items-center justify-between mb-4">
							<div class="bg-red-50 text-red-600 p-2 rounded-lg">${ICONS.droplet}</div>
						</div>
						<p class="text-gray-500 text-sm font-medium">Inventory</p>
						<h3 class="text-2xl font-bold mt-1">Simple</h3>
					</div>
					<div class="card p-6">
						<div class="flex items-center justify-between mb-4">
							<div class="bg-red-50 text-red-600 p-2 rounded-lg">${ICONS.clock}</div>
						</div>
						<p class="text-gray-500 text-sm font-medium">Requests</p>
						<h3 class="text-2xl font-bold mt-1">Tracked</h3>
					</div>
					<div class="card p-6">
						<div class="flex items-center justify-between mb-4">
							<div class="bg-red-50 text-red-600 p-2 rounded-lg">${ICONS.building}</div>
						</div>
						<p class="text-gray-500 text-sm font-medium">Hospitals</p>
						<h3 class="text-2xl font-bold mt-1">Linked</h3>
					</div>
				</div>

				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<div class="card p-6">
						<h3 class="font-bold text-gray-900 mb-4">Quick Notes</h3>
						<ul class="space-y-3 text-sm text-gray-600">
							<li>• No mock data files are loaded.</li>
							<li>• The portal stays HTML, CSS, and JavaScript only.</li>
							<li>• Sections can be expanded later if needed.</li>
						</ul>
					</div>
					<div class="card p-6">
						<h3 class="font-bold text-gray-900 mb-4">Contact</h3>
						<div class="space-y-3 text-sm text-gray-600">
							<div class="flex items-center gap-2">${ICONS.mail}<span>admin@bbms.local</span></div>
							<div class="flex items-center gap-2">${ICONS.phone}<span>(555) 000-0000</span></div>
						</div>
					</div>
				</div>
			</div>
		`;
	}

	function renderSimplePage(title, description) {
		mainContent.innerHTML = `
			<div class="space-y-6 max-w-3xl">
				<div>
					<h1 class="text-2xl font-bold">${title}</h1>
					<p class="text-gray-500 text-sm">${description}</p>
				</div>
				<div class="card p-6">
					<p class="text-sm text-gray-600">This section is intentionally blank to keep the portal clean and data-free.</p>
				</div>
			</div>
		`;
	}
});
