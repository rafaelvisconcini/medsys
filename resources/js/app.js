import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';

window.FullCalendar = {
    Calendar: function (el, options) {
        return new Calendar(el, {
            ...options,
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            locale: ptBrLocale,
        });
    },
};
