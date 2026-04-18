import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';

function loadEventsFromPage() {
    const jsonEl = document.getElementById('kegiatan-calendar-json');
    if (jsonEl?.textContent) {
        try {
            const data = JSON.parse(jsonEl.textContent.trim());
            return Array.isArray(data) ? data : [];
        } catch {
            return [];
        }
    }
    return [];
}

function initKegiatanCalendar() {
    const mount = document.getElementById('kegiatan-calendar-mount');
    if (!mount) {
        return;
    }
    if (mount.dataset.fcInitialized === '1') {
        return;
    }
    mount.dataset.fcInitialized = '1';

    const url = new URL(window.location.href);
    const viewFromUrl = url.searchParams.get('view') || 'listMonth';

    const events = loadEventsFromPage();
    const yearFromData = parseInt(mount.dataset.year || '', 10);
    const monthFromData = parseInt(mount.dataset.month || '', 10);
    const initialDate =
        yearFromData > 0 && monthFromData >= 1 && monthFromData <= 12
            ? `${yearFromData}-${String(monthFromData).padStart(2, '0')}-01`
            : undefined;

    const calendarEl = document.createElement('div');
    calendarEl.className = 'jurnal-fc-inner min-h-[520px]';
    mount.appendChild(calendarEl);

    let calInstance;
    let isInternalNavigation = false;

    calInstance = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, listPlugin],
        initialView: viewFromUrl,
        initialDate,
        locale: 'en',
        buttonText: {
            today: 'Hari ini',
            month: 'Bulan',
            list: 'Daftar',
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'listMonth,dayGridMonth',
        },
        eventOrder: function (a, b) {
            // Newest first in list view
            const aDate = a.start ? a.start.valueOf() : 0;
            const bDate = b.start ? b.start.valueOf() : 0;
            return bDate - aDate;
        },
        height: 'auto',
        events,
        views: {
            listMonth: {
                eventContent: function (arg) {
                    const detail = arg.event.extendedProps.detail || {};
                    const title = arg.event.title;
                    const kelas = detail.kelas_name || '';
                    const desc = detail.description || '';

                    const container = document.createElement('div');
                    container.className = 'py-1';

                    const titleEl = document.createElement('div');
                    titleEl.className = 'font-bold text-gray-800';
                    titleEl.textContent = title;
                    container.appendChild(titleEl);

                    if (kelas && kelas !== '-') {
                        const kelasEl = document.createElement('div');
                        kelasEl.className = 'text-[10px] mt-0.5 font-bold text-teal-700';
                        kelasEl.textContent = 'Kelas: ' + kelas;
                        container.appendChild(kelasEl);
                    }

                    if (desc) {
                        const descEl = document.createElement('div');
                        descEl.className = 'text-[10px] mt-1 text-gray-500 italic line-clamp-2';
                        descEl.textContent = '"' + desc + '"';
                        container.appendChild(descEl);
                    }

                    return { domNodes: [container] };
                }
            }
        },
        eventClick(info) {
            info.jsEvent.preventDefault();
            window.dispatchEvent(
                new CustomEvent('kegiatan-cal-click', {
                    detail: {
                        id: info.event.id,
                        title: info.event.title,
                        start: info.event.startStr,
                        extendedProps: info.event.extendedProps || {},
                    },
                }),
            );
        },
        datesSet(dateInfo) {
            if (isInternalNavigation) {
                return;
            }

            const d = dateInfo.view.currentStart || calInstance.getDate();
            const y = d.getFullYear();
            const m = d.getMonth() + 1;
            const currentView = dateInfo.view.type;

            const curUrl = new URL(window.location.href);
            const curY = parseInt(curUrl.searchParams.get('year') || '0', 10);
            const curM = parseInt(curUrl.searchParams.get('month') || '0', 10);
            const curV = curUrl.searchParams.get('view') || 'listMonth';

            // Refresh if year/month changed or if view changed from default without reflecting in URL
            if (curY === y && curM === m && curV === currentView) {
                return;
            }

            isInternalNavigation = true;
            curUrl.searchParams.set('year', String(y));
            curUrl.searchParams.set('month', String(m));
            curUrl.searchParams.set('view', currentView);

            window.location.href = curUrl.toString();
        },
    });

    calInstance.render();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKegiatanCalendar);
} else {
    initKegiatanCalendar();
}
