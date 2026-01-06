'use client';

import { useState, useEffect } from 'react';
import { ConferenceRoom, Reservation, CreateReservationData } from '@/types';

interface ReservationFormProps {
  rooms: ConferenceRoom[];
  reservation?: Reservation | null;
  initialDate?: string;
  initialRoomId?: number;
  onSubmit: (data: CreateReservationData) => Promise<void>;
  onCancel: () => void;
}

/**
 * Form component for creating and editing reservations.
 */
export default function ReservationForm({
  rooms,
  reservation,
  initialDate,
  initialRoomId,
  onSubmit,
  onCancel,
}: ReservationFormProps) {
  const [conferenceRoomId, setConferenceRoomId] = useState<string>('');
  const [reserverName, setReserverName] = useState('');
  const [date, setDate] = useState('');
  const [startTime, setStartTime] = useState('');
  const [endTime, setEndTime] = useState('');
  const [notes, setNotes] = useState('');
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const isEditing = !!reservation;

  useEffect(() => {
    if (reservation) {
      setConferenceRoomId(reservation.conferenceRoom.toString());
      setReserverName(reservation.reserverName);
      setDate(reservation.date);
      setStartTime(reservation.startTime);
      setEndTime(reservation.endTime);
      setNotes(reservation.notes || '');
    } else {
      if (initialDate) setDate(initialDate);
      if (initialRoomId) setConferenceRoomId(initialRoomId.toString());
    }
  }, [reservation, initialDate, initialRoomId]);

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!conferenceRoomId) {
      newErrors.conferenceRoomId = 'Please select a room';
    }

    if (!reserverName.trim()) {
      newErrors.reserverName = 'Name is required';
    }

    if (!date) {
      newErrors.date = 'Date is required';
    }

    if (!startTime) {
      newErrors.startTime = 'Start time is required';
    }

    if (!endTime) {
      newErrors.endTime = 'End time is required';
    }

    if (startTime && endTime && startTime >= endTime) {
      newErrors.endTime = 'End time must be after start time';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!validate()) return;

    setLoading(true);
    try {
      await onSubmit({
        conferenceRoom: parseInt(conferenceRoomId),
        reserverName: reserverName.trim(),
        date,
        startTime,
        endTime,
        notes: notes.trim() || undefined,
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <label htmlFor="room" className="label">
          Conference Room <span className="text-red-500">*</span>
        </label>
        <select
          id="room"
          value={conferenceRoomId}
          onChange={(e) => setConferenceRoomId(e.target.value)}
          className={`input ${errors.conferenceRoomId ? 'border-red-500' : ''}`}
        >
          <option value="">Select a room...</option>
          {rooms.map((room) => (
            <option key={room.id} value={room.id}>
              {room.name} (Capacity: {room.capacity})
            </option>
          ))}
        </select>
        {errors.conferenceRoomId && (
          <p className="text-red-500 text-sm mt-1">{errors.conferenceRoomId}</p>
        )}
      </div>

      <div>
        <label htmlFor="reserverName" className="label">
          Your Name <span className="text-red-500">*</span>
        </label>
        <input
          id="reserverName"
          type="text"
          value={reserverName}
          onChange={(e) => setReserverName(e.target.value)}
          className={`input ${errors.reserverName ? 'border-red-500' : ''}`}
          placeholder="e.g., John Smith"
        />
        {errors.reserverName && (
          <p className="text-red-500 text-sm mt-1">{errors.reserverName}</p>
        )}
      </div>

      <div>
        <label htmlFor="date" className="label">
          Date <span className="text-red-500">*</span>
        </label>
        <input
          id="date"
          type="date"
          value={date}
          onChange={(e) => setDate(e.target.value)}
          className={`input ${errors.date ? 'border-red-500' : ''}`}
          min={new Date().toISOString().split('T')[0]}
        />
        {errors.date && <p className="text-red-500 text-sm mt-1">{errors.date}</p>}
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div>
          <label htmlFor="startTime" className="label">
            Start Time <span className="text-red-500">*</span>
          </label>
          <input
            id="startTime"
            type="time"
            value={startTime}
            onChange={(e) => setStartTime(e.target.value)}
            className={`input ${errors.startTime ? 'border-red-500' : ''}`}
          />
          {errors.startTime && (
            <p className="text-red-500 text-sm mt-1">{errors.startTime}</p>
          )}
        </div>

        <div>
          <label htmlFor="endTime" className="label">
            End Time <span className="text-red-500">*</span>
          </label>
          <input
            id="endTime"
            type="time"
            value={endTime}
            onChange={(e) => setEndTime(e.target.value)}
            className={`input ${errors.endTime ? 'border-red-500' : ''}`}
          />
          {errors.endTime && (
            <p className="text-red-500 text-sm mt-1">{errors.endTime}</p>
          )}
        </div>
      </div>

      <div>
        <label htmlFor="notes" className="label">
          Notes
        </label>
        <textarea
          id="notes"
          value={notes}
          onChange={(e) => setNotes(e.target.value)}
          className="input"
          rows={3}
          placeholder="Optional notes about the meeting..."
        />
      </div>

      <div className="flex justify-end gap-3 pt-4">
        <button type="button" onClick={onCancel} className="btn btn-secondary" disabled={loading}>
          Cancel
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading ? 'Saving...' : isEditing ? 'Update Reservation' : 'Create Reservation'}
        </button>
      </div>
    </form>
  );
}
