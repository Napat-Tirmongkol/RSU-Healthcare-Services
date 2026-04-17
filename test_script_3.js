
        /* ══════════════════════════════════════════════════════════════
           POLLING — live dashboard updates every 20s (no persistent connection)
           ══════════════════════════════════════════════════════════════ */

        const _liveStyle = document.createElement('style');
        _liveStyle.textContent = `
  @keyframes livePulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.8)} }
  @keyframes kpiFade   { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
  @keyframes feedSlide { from{opacity:0;transform:translateX(10px)} to{opacity:1;transform:translateX(0)} }
  .kpi-updated { animation: kpiFade .4s ease both; }
  .feed-new    { animation: feedSlide .3s ease both; }
`;
        document.head.appendChild(_liveStyle);

        const badge = document.getElementById('ws-badge');
        const dot = document.getElementById('ws-dot');
        const label = document.getElementById('ws-label');

        function setBadge(state) {
            const styles = {
                live: { bg: '#f0fdf4', color: '#16a34a', border: '#c7e8d5', dot: '#22c55e', anim: 'livePulse 1.6s infinite', text: 'Live' },
                loading: { bg: '#fffbeb', color: '#d97706', border: '#fde68a', dot: '#f59e0b', anim: 'livePulse .8s infinite', text: 'Updating…' },
                offline: { bg: '#fef2f2', color: '#dc2626', border: '#fecaca', dot: '#ef4444', anim: 'none', text: 'Offline' },
            };
            const s = styles[state] || styles.offline;
            badge.style.cssText = `display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:8px;font-size:10px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;transition:all .3s;background:${s.bg};color:${s.color};border:1px solid ${s.border}`;
            dot.style.background = s.dot;
            dot.style.animation = s.anim;
            label.textContent = s.text;
        }

        function animateKpi(el, toVal) {
            if (!el) return;
            const from = parseInt(el.textContent.replace(/,/g, ''), 10) || 0;
            if (from === toVal) return;
            const dur = 600, start = performance.now();
            const ease = t => 1 - Math.pow(1 - t, 3);
            el.classList.remove('kpi-updated'); void el.offsetWidth; el.classList.add('kpi-updated');
            (function tick(now) {
                const p = Math.min((now - start) / dur, 1);
                el.textContent = Math.floor(ease(p) * (toVal - from) + from).toLocaleString();
                if (p < 1) requestAnimationFrame(tick);
                else el.textContent = toVal.toLocaleString();
            })(start);
        }

        function escHtml(s) {
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function renderActivity(logs) {
            const feed = document.getElementById('activity-feed');
            const link = feed?.querySelector('a[href]');
            if (!feed) return;
            feed.querySelectorAll('.feed-item').forEach(el => el.remove());
            if (!logs?.length) return;
            const frag = document.createDocumentFragment();
            logs.forEach((log, i) => {
                const ts = new Date(log.timestamp.replace(' ', 'T'));
                const timeStr = ts.toLocaleString('th-TH', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
                const row = document.createElement('div');
                row.className = 'feed-item feed-new';
                row.style.animationDelay = (i * 0.04) + 's';
                row.innerHTML = `<div class="feed-dot"><i class="fa-solid fa-bolt text-[11px]"></i></div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2 mb-0.5">
                    <span class="text-[10px] font-black uppercase tracking-wider truncate" style="color:#2e9e63">${escHtml(log.action)}</span>
                    <span class="text-[9px] text-gray-400 whitespace-nowrap">${timeStr}</span>
                </div>
                <p class="text-[12px] font-bold text-gray-800 leading-snug truncate">${escHtml(log.admin_name || 'System')}</p>
                <p class="text-[11px] text-gray-400 leading-snug mt-0.5 line-clamp-1">${escHtml(log.description || '')}</p>
            </div>`;
                frag.appendChild(row);
            });
            feed.insertBefore(frag, link);
        }

        // ── Polling ───────────────────────────────────────────────────────────────────
        const POLL_INTERVAL = 20000; // 20 seconds
        let pollTimer = null;

        function poll() {
            setBadge('loading');
            fetch('ajax_stats.php', { credentials: 'same-origin' })
                .then(r => r.ok ? r.json() : Promise.reject(r.status))
                .then(d => {
                    if (!d.ok) { setBadge('offline'); return; }
                    animateKpi(document.getElementById('kpi-users'), d.users);
                    animateKpi(document.getElementById('kpi-camps'), d.camps);
                    animateKpi(document.getElementById('kpi-borrows'), d.borrows);

                    // Borrows urgency badge + sub text
                    const ub = document.getElementById('borrows-urgent');
                    if (ub) ub.style.display = d.borrows > 0 ? 'inline' : 'none';
                    const borrowsSub = document.getElementById('borrows-sub');
                    if (borrowsSub) {
                        if (d.borrows > 0) {
                            borrowsSub.style.color = '#ef4444';
                            borrowsSub.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="margin-right:3px"></i>รอการตรวจสอบ';
                        } else {
                            borrowsSub.style.color = '#94a3b8';
                            borrowsSub.textContent = 'ไม่มีรายการค้างในระบบ';
                        }
                    }

                    // Quota & booking rate
                    if (d.total_quota !== undefined) {
                        const rate = d.booking_rate ?? 0;
                        const rateBar = document.getElementById('kpi-rate-bar');
                        const rateNum = document.getElementById('kpi-rate');
                        const kpiUsed = document.getElementById('kpi-used');
                        const kpiTQ = document.getElementById('kpi-total-quota');
                        const kpiQuota = document.getElementById('kpi-quota');
                        if (rateBar) rateBar.style.width = rate + '%';
                        if (rateNum) rateNum.textContent = rate;
                        if (kpiUsed) kpiUsed.textContent = (d.used_quota ?? 0).toLocaleString();
                        if (kpiTQ) kpiTQ.textContent = d.total_quota.toLocaleString();
                        if (kpiQuota) kpiQuota.textContent = d.total_quota.toLocaleString();
                    }

                    if (Array.isArray(d.activity)) renderActivity(d.activity);
                    setBadge('live');
                })
                .catch(() => setBadge('offline'));
        }

        /* ── Project Grid Controls ────────────────────────────────────────────────── */
        (function () {
            var currentFilter = 'all';
            var searchQuery = '';

            function applyFilters() {
                var cards = document.querySelectorAll('#project-container .proj-card');
                var visible = 0;
                cards.forEach(function (card) {
                    var name = (card.dataset.name || '').toLowerCase();
                    var keywords = (card.dataset.keywords || '').toLowerCase();
                    var category = card.dataset.category || '';
                    var matchSearch = !searchQuery || name.includes(searchQuery) || keywords.includes(searchQuery);
                    var matchFilter = currentFilter === 'all' || category === currentFilter;
                    if (matchSearch && matchFilter) {
                        card.style.display = ''; visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                var empty = document.getElementById('proj-empty');
                if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
            }

            window.projSetFilter = function (btn) {
                document.querySelectorAll('.proj-tab').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                currentFilter = btn.dataset.filter;
                applyFilters();
            };

            window.projSetView = function (view) {
                var container = document.getElementById('project-container');
                var btnGrid = document.getElementById('btn-grid');
                var btnList = document.getElementById('btn-list');
                var activeStyle = 'padding:5px 10px;border-radius:8px;border:none;cursor:pointer;background:#fff;color:#2e9e63;box-shadow:0 1px 4px rgba(0,0,0,.08);transition:all .2s';
                var inactiveStyle = 'padding:5px 10px;border-radius:8px;border:none;cursor:pointer;background:transparent;color:#94a3b8;transition:all .2s';
                if (view === 'list') {
                    container.classList.add('list-mode');
                    btnGrid.style.cssText = inactiveStyle;
                    btnList.style.cssText = activeStyle;
                } else {
                    container.classList.remove('list-mode');
                    btnGrid.style.cssText = activeStyle;
                    btnList.style.cssText = inactiveStyle;
                }
            };

            var searchInput = document.getElementById('search-project');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    searchQuery = this.value.toLowerCase().trim();
                    applyFilters();
                });
            }
        })();

        /* ── Identity & Governance ─────────────────────────────────────────────── */
        function switchIdTab(tab, btn) {
            document.querySelectorAll('.id-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.id-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('id-panel-' + tab).classList.add('active');

            // Header visibility
            const isUsers = tab === 'users';
            const isAdmins = tab === 'admins';
            const isStaff = tab === 'staff';

            const btnAdmin = document.getElementById('id-btn-add-admin');
            const btnStaff = document.getElementById('id-btn-add-staff');
            if (btnAdmin) btnAdmin.style.display = isAdmins ? 'block' : 'none';
            if (btnStaff) btnStaff.style.display = isStaff ? 'block' : 'none';

            // Search behavior
            const search = document.getElementById('id-search-input');
            if (search) {
                search.value = '';
                idUniversalFilter('');
                search.placeholder = isUsers ? 'ค้นหา Users...' : (isAdmins ? 'ค้นหา Admins...' : 'ค้นหา Staff...');
            }
        }

        function idUniversalFilter(val) {
            val = val.toLowerCase().trim();
            const activePanel = document.querySelector('.id-panel.active');
            if (!activePanel) return;

            const rows = activePanel.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (row.cells.length < 2) return;
                row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
            });
        }

        function openAddAdminModal() {
            document.getElementById('admAction').value = 'add_admin';
            document.getElementById('admModalTitle').textContent = 'เพิ่ม System Admin';
            document.getElementById('admForm').reset();
            document.getElementById('admPassword').placeholder = '';
            document.getElementById('pwdNotice').style.display = 'none';
            document.getElementById('admModal').style.display = 'flex';
        }

        function openEditAdminModal(adm) {
            document.getElementById('admAction').value = 'edit_admin';
            document.getElementById('admModalTitle').textContent = 'แก้ไข System Admin';
            document.getElementById('admId').value = adm.id;
            document.getElementById('admFullName').value = adm.full_name;
            document.getElementById('admUsername').value = adm.username;
            document.getElementById('admEmail').value = adm.email;
            document.getElementById('admRole').value = adm.role;
            document.getElementById('admPassword').value = '';
            document.getElementById('admPassword').placeholder = '••••••••';
            document.getElementById('pwdNotice').style.display = 'inline';
            document.getElementById('admModal').style.display = 'flex';
        }

        function openAddStaffModal() {
            document.getElementById('sfAction').value = 'add_staff';
            document.getElementById('sfModalTitle').textContent = 'เพิ่มเจ้าหน้าที่ (Staff)';
            document.getElementById('sfForm').reset();
            document.getElementById('sfPassword').required = true;
            document.getElementById('sfModal').style.display = 'flex';
        }

        function openEditStaffModal(st) {
            document.getElementById('sfAction').value = 'edit_staff';
            document.getElementById('sfModalTitle').textContent = 'แก้ไขข้อมูลเจ้าหน้าที่';
            document.getElementById('sfId').value = st.id;
            document.getElementById('sfFullName').value = st.full_name;
            document.getElementById('sfUsername').value = st.username;
            document.getElementById('sfRole').value = st.role;
            document.getElementById('sfStatus').value = st.account_status;
            document.getElementById('sfAccessEc').checked = parseInt(st.access_ecampaign) === 1;
            document.getElementById('sfEcRole').value = st.ecampaign_role;
            document.getElementById('sfPassword').value = '';
            document.getElementById('sfPassword').required = false;
            document.getElementById('sfModal').style.display = 'flex';
        }

        function idOpenEdit(u) {
            document.getElementById('id_edit_uid').value = u.id;
            document.getElementById('id_edit_name').value = u.full_name || '';
            document.getElementById('id_edit_citizen').value = u.citizen_id || '';
            document.getElementById('id_edit_sid').value = u.student_personnel_id || '';
            document.getElementById('id_edit_phone').value = u.phone_number || '';
            document.getElementById('id_edit_email').value = u.email || '';
            document.getElementById('id_edit_gender').value = u.gender || '';
            document.getElementById('id_edit_dept').value = u.department || '';
            document.getElementById('id_edit_status').value = u.status || '';
            document.getElementById('id_edit_sother').value = u.status_other || '';
            document.getElementById('id_edit_sother_wrap').style.display = u.status === 'other' ? 'block' : 'none';
            var m = document.getElementById('idEditModal');
            m.style.display = 'flex';
        }
        function idOpenView(u) {
            var statusMap = { student: 'นักศึกษา', staff: 'บุคลากร/อาจารย์', teacher: 'อาจารย์', other: 'บุคคลทั่วไป' };
            var genderMap = { male: 'ชาย', female: 'หญิง', other: 'อื่นๆ' };
            var map = [
                ['ชื่อ-นามสกุล', u.full_name],
                ['เลขบัตรประชาชน', u.citizen_id],
                ['รหัสนักศึกษา / บุคลากร', u.student_personnel_id],
                ['เบอร์โทรศัพท์', u.phone_number],
                ['อีเมล', u.email],
                ['เพศ', genderMap[u.gender] || u.gender],
                ['คณะ / หน่วยงาน', u.department],
                ['ประเภท', statusMap[u.status] || u.status],
            ];
            if (u.status === 'other' && u.status_other) {
                map.push(['ระบุสถานภาพ', u.status_other]);
            }
            map.push(['วันที่ลงทะเบียน', u.created_at ? new Date(u.created_at.replace(' ', 'T')).toLocaleString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—']);
            document.getElementById('idViewBody').innerHTML = map.map(function (r) {
                return '<div><div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:3px">' + r[0] + '</div>'
                    + '<div style="padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:700;color:#0f172a">' + (r[1] || '—') + '</div></div>';
            }).join('');
            document.getElementById('idViewModal').style.display = 'flex';
        }
        /* ── Identity pagination ── */
        (function () {
            var allRows = [];
            var filtered = [];
            var currentPage = 1;
            var pageSize = 25;

            function init() {
                var tableBody = document.getElementById('idUserTbody');
                if (!tableBody) return;
                allRows = Array.from(tableBody.querySelectorAll('.id-user-row'));
                filtered = allRows.slice();
                render();
            }

            function render() {
                var total = filtered.length;
                var totalPages = Math.max(1, Math.ceil(total / pageSize));
                if (currentPage > totalPages) currentPage = totalPages;
                var start = (currentPage - 1) * pageSize;
                var end = start + pageSize;

                allRows.forEach(function (r) { r.style.display = 'none'; });
                filtered.slice(start, end).forEach(function (r) { r.style.display = ''; });

                var from = total === 0 ? 0 : start + 1;
                var to = Math.min(end, total);
                var info = document.getElementById('id-page-info');
                if (info) info.textContent = total === 0 ? 'ไม่พบรายการ' : from + '–' + to + ' จาก ' + total.toLocaleString();

                var prev = document.getElementById('id-page-prev');
                var next = document.getElementById('id-page-next');
                if (prev) { prev.disabled = currentPage <= 1; prev.style.opacity = currentPage <= 1 ? '.35' : '1'; }
                if (next) { next.disabled = currentPage >= totalPages; next.style.opacity = currentPage >= totalPages ? '.35' : '1'; }
            }

            window.idFilterUsers = function (val) {
                val = val.toLowerCase().trim();
                filtered = val ? allRows.filter(function (r) { return r.innerText.toLowerCase().includes(val); }) : allRows.slice();
                currentPage = 1;
                render();
            };

            window.idSetPageSize = function (size) {
                pageSize = size;
                currentPage = 1;
                render();
                document.querySelectorAll('.id-ps-btn').forEach(function (b) {
                    var active = parseInt(b.dataset.size) === size;
                    b.style.background = active ? '#2e9e63' : '#f8fafc';
                    b.style.color = active ? '#fff' : '#374151';
                    b.style.borderColor = active ? '#2e9e63' : '#e2e8f0';
                });
            };

            window.idPrevPage = function () { if (currentPage > 1) { currentPage--; render(); } };
            window.idNextPage = function () {
                if (currentPage < Math.ceil(filtered.length / pageSize)) { currentPage++; render(); }
            };

            // run after DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
        // Close modals on backdrop click
        ['idEditModal', 'idViewModal', 'admModal', 'sfModal'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', function (e) {
                    if (e.target === this) this.style.display = 'none';
                });
            }
        });
        // Auto-switch section from URL ?section=identity
        (function () {
            var params = new URLSearchParams(window.location.search);
            var sec = params.get('section');
            var tab = params.get('tab');
            if (sec) {
                var btn = document.querySelector('.psb-item[data-section="' + sec + '"]');
                switchSection(sec, btn);
            }
            if (sec === 'identity' && tab) {
                var tabBtn = document.querySelector('.id-tab[data-tab="' + tab + '"]');
                if (tabBtn) switchIdTab(tab, tabBtn);
            }
            // Auto-dismiss toast
            var toast = document.getElementById('id-toast');
            if (toast) setTimeout(function () { toast.style.transition = 'opacity .5s'; toast.style.opacity = '0'; setTimeout(function () { toast.remove(); }, 500); }, 3000);
        })();

        /* ── Sidebar Controls ────────────────────────────────────────────────────── */
        function toggleSidebar() {
            var sidebar = document.getElementById('portal-sidebar');
            var icon = document.getElementById('sidebar-toggle-icon');
            var expanded = document.getElementById('psb-user-expanded');
            var collapsed = document.getElementById('psb-user-collapsed');
            sidebar.classList.toggle('collapsed');
            var isCollapsed = sidebar.classList.contains('collapsed');
            icon.style.transform = isCollapsed ? 'rotate(180deg)' : '';
            if (expanded) expanded.style.display = isCollapsed ? 'none' : 'flex';
            if (collapsed) collapsed.style.display = isCollapsed ? 'flex' : 'none';
        }

        function switchSection(sectionId, btn) {
            document.querySelectorAll('.portal-section').forEach(function (s) { s.style.display = 'none'; });
            var target = document.getElementById('section-' + sectionId);
            if (target) target.style.display = '';
            document.querySelectorAll('.psb-item').forEach(function (b) { b.classList.remove('psb-active'); });
            if (btn) btn.classList.add('psb-active');
        }

        // Pause when tab hidden, resume when visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(pollTimer);
                pollTimer = null;
            } else {
                poll();
                pollTimer = setInterval(poll, POLL_INTERVAL);
            }
        });

        /* ── Maintenance Mode Logic (Merged from Admin Tool) ─────────────────────── */
        const portal_CSRF = null;

        function showPortalToast(msg, type = 'success') {
            const id = 'portal-runtime-toast';
            let t = document.getElementById(id);
            if (!t) {
                t = document.createElement('div');
                t.id = id;
                t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:14px;font-size:13px;font-weight:700;box-shadow:0 4px 20px rgba(0,0,0,.12);transform:translateY(80px);opacity:0;transition:all .3s cubic-bezier(.16,1,.3,1);pointer-events:none;';
                document.body.appendChild(t);
            }
            t.textContent = msg;
            t.style.background = type === 'success' ? '#f0fdf4' : '#fef2f2';
            t.style.color = type === 'success' ? '#16a34a' : '#dc2626';
            t.style.border = type === 'success' ? '1.5px solid #bbf7d0' : '1.5px solid #fecaca';

            t.style.transform = 'translateY(0)';
            t.style.opacity = '1';
            clearTimeout(t._tid);
            t._tid = setTimeout(() => {
                t.style.transform = 'translateY(80px)';
                t.style.opacity = '0';
            }, 3000);
        }

        function updateMaintenanceUI(project, active) {
            const badge = document.getElementById('badge-' + project);
            if (badge) {
                badge.className = 'status-badge ' + (active ? 'on' : 'off');
                badge.innerHTML = `<span class="status-dot"></span>${active ? 'เปิดใช้งาน' : 'ปรับปรุง'}`;
                badge.classList.remove('badge-pop');
                void badge.offsetWidth;
                badge.classList.add('badge-pop');
            }

            // Update main status banner
            const toggles = document.querySelectorAll('[data-project]');
            const allOn = Array.from(toggles).every(t => t.checked);
            const banner = document.getElementById('status-banner');
            if (banner) {
                banner.dataset.state = allOn ? 'ok' : 'warn';
                const icon = document.getElementById('banner-icon');
                const title = document.getElementById('banner-title');
                const desc = document.getElementById('banner-desc');

                if (icon) icon.className = `fa-solid ${allOn ? 'fa-circle-check' : 'fa-triangle-exclamation'} text-base`;
                if (title) title.textContent = allOn ? 'ระบบทุกโปรเจกต์พร้อมใช้งาน' : 'มีบางโปรเจกต์ปิดปรับปรุงอยู่';
                if (desc) desc.textContent = allOn ? 'User ทุกคนสามารถเข้าใช้งานได้ตามปกติ' : 'คุณสามารถคลิกเปิดระบบได้จากรายการด้านล่าง';

                const iconWrap = icon?.parentElement;
                if (iconWrap) iconWrap.style.cssText = allOn ? 'background:#dcfce7;color:#16a34a' : 'background:#fef3c7;color:#d97706';
            }
        }

        function toggleMaintenance(input) {
            const project = input.dataset.project;
            const active = input.checked;

            // Optimistic UI update
            updateMaintenanceUI(project, active);

            const fd = new FormData();
            fd.append('action', 'set');
            fd.append('project', project);
            fd.append('active', active ? '1' : '0');
            fd.append('csrf_token', portal_CSRF);

            fetch('ajax_maintenance.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.ok) {
                        showPortalToast(active ? `${project} เปิดใช้งานแล้ว` : `${project} ปิดปรับปรุงแล้ว`, active ? 'success' : 'error');
                    } else {
                        input.checked = !active;
                        updateMaintenanceUI(project, !active);
                        showPortalToast('ผิดพลาด: ' + (d.message || 'Unknown error'), 'error');
                    }
                })
                .catch(() => {
                    input.checked = !active;
                    updateMaintenanceUI(project, !active);
                    showPortalToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                });
        }

        // Start polling after page is fully loaded
        window.addEventListener('load', () => {
            setBadge('live'); // optimistic: page data is fresh on load
            pollTimer = setInterval(poll, POLL_INTERVAL);
        });
    