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

    const events = loadEventsFromPage();
    const year = parseInt(mount.dataset.year || '', 10);
    const month = parseInt(mount.dataset.month || '', 10);
    const initialDate =
        year > 0 && month >= 1 && month <= 12
            ? `${year}-${String(month).padStart(2, '0')}-01`
            : undefined;

    const calendarEl = document.createElement('div');
    calendarEl.className = 'jurnal-fc-inner min-h-[520px]';
    mount.appendChild(calendarEl);

    let calInstance;
    let firstDatesSet = true;

    calInstance = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, listPlugin],
        initialView: 'dayGridMonth',
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
            right: 'dayGridMonth,listMonth',
        },
        height: 'auto',
        events,
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
        datesSet() {
            if (firstDatesSet) {
                firstDatesSet = false;
                return;
            }
            const d = calInstance.getDate();
            const y = d.getFullYear();
            const m = d.getMonth() + 1;
            const url = new URL(window.location.href);
            const curY = parseInt(url.searchParams.get('year') || '0', 10);
            const curM = parseInt(url.searchParams.get('month') || '0', 10);
            if (curY === y && curM === m) {
                return;
            }
            url.searchParams.set('year', String(y));
            url.searchParams.set('month', String(m));
            window.location.href = url.toString();
        },
    });

    calInstance.render();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKegiatanCalendar);
} else {
    initKegiatanCalendar();
}
