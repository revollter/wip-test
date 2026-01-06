'use client';

import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { ConferenceRoom, Reservation } from '@/types';

interface CalendarProps {
  reservations: Reservation[];
  rooms: ConferenceRoom[];
  onDateClick: (date: string) => void;
  onEventClick: (reservationId: number) => void;
}

// Color palette for different rooms
const roomColors = [
  '#3b82f6', // blue
  '#10b981', // green
  '#f59e0b', // amber
  '#ef4444', // red
  '#8b5cf6', // purple
  '#ec4899', // pink
  '#06b6d4', // cyan
  '#f97316', // orange
];

/**
 * Calendar component for displaying reservations using FullCalendar.
 */
export default function Calendar({
  reservations,
  rooms,
  onDateClick,
  onEventClick,
}: CalendarProps) {
  // Create a color map for rooms
  const roomColorMap = new Map<number, string>();
  rooms.forEach((room, index) => {
    roomColorMap.set(room.id, roomColors[index % roomColors.length]);
  });

  // Convert reservations to FullCalendar events
  const events = reservations.map((reservation) => ({
    id: reservation.id.toString(),
    title: `${reservation.conferenceRoomName} - ${reservation.reserverName}`,
    start: `${reservation.date}T${reservation.startTime}`,
    end: `${reservation.date}T${reservation.endTime}`,
    backgroundColor: roomColorMap.get(reservation.conferenceRoom) || '#3b82f6',
    borderColor: roomColorMap.get(reservation.conferenceRoom) || '#3b82f6',
    extendedProps: {
      reservationId: reservation.id,
      roomName: reservation.conferenceRoomName,
      reserverName: reservation.reserverName,
    },
  }));

  return (
    <div className="calendar-container">
      <FullCalendar
        plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
        initialView="timeGridWeek"
        headerToolbar={{
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay',
        }}
        events={events}
        dateClick={(info) => onDateClick(info.dateStr.split('T')[0])}
        eventClick={(info) => {
          const reservationId = parseInt(info.event.id);
          onEventClick(reservationId);
        }}
        slotMinTime="07:00:00"
        slotMaxTime="20:00:00"
        allDaySlot={false}
        weekends={true}
        nowIndicator={true}
        selectable={true}
        selectMirror={true}
        dayMaxEvents={true}
        height="auto"
        aspectRatio={1.8}
        eventTimeFormat={{
          hour: '2-digit',
          minute: '2-digit',
          meridiem: false,
          hour12: false,
        }}
        slotLabelFormat={{
          hour: '2-digit',
          minute: '2-digit',
          meridiem: false,
          hour12: false,
        }}
      />

      {/* Room Legend */}
      {rooms.length > 0 && (
        <div className="mt-4 flex flex-wrap gap-4">
          <span className="text-sm font-medium text-gray-700">Rooms:</span>
          {rooms.map((room) => (
            <div key={room.id} className="flex items-center gap-2">
              <span
                className="w-3 h-3 rounded-full"
                style={{ backgroundColor: roomColorMap.get(room.id) }}
              />
              <span className="text-sm text-gray-600">{room.name}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
