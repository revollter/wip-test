'use client';

import { useState, useEffect, useCallback } from 'react';
import dynamic from 'next/dynamic';
import toast from 'react-hot-toast';
import { getRooms, getReservations, createReservation, updateReservation, deleteReservation } from '@/lib/api';
import { ConferenceRoom, Reservation, CreateReservationData } from '@/types';
import Modal from '@/components/Modal';
import ReservationForm from '@/components/ReservationForm';

// Dynamic import for FullCalendar to avoid SSR issues
const Calendar = dynamic(() => import('@/components/Calendar'), {
  ssr: false,
  loading: () => (
    <div className="flex items-center justify-center h-96">
      <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
    </div>
  ),
});

/**
 * Calendar page for viewing and managing reservations.
 */
export default function CalendarPage() {
  const [rooms, setRooms] = useState<ConferenceRoom[]>([]);
  const [reservations, setReservations] = useState<Reservation[]>([]);
  const [selectedRoom, setSelectedRoom] = useState<number | null>(null);
  const [loading, setLoading] = useState(true);

  // Modal states
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
  const [isViewModalOpen, setIsViewModalOpen] = useState(false);
  const [selectedReservation, setSelectedReservation] = useState<Reservation | null>(null);
  const [initialDate, setInitialDate] = useState<string>('');
  const [initialStartTime, setInitialStartTime] = useState<string>('');
  const [initialEndTime, setInitialEndTime] = useState<string>('');
  const [isEditing, setIsEditing] = useState(false);

  const fetchData = useCallback(async () => {
    try {
      const [roomsData, reservationsData] = await Promise.all([
        getRooms(),
        getReservations(selectedRoom ? { roomId: selectedRoom } : undefined),
      ]);
      setRooms(roomsData);
      setReservations(reservationsData);
    } catch (error) {
      console.error('Failed to fetch data:', error);
      toast.error('Failed to load calendar data');
    } finally {
      setLoading(false);
    }
  }, [selectedRoom]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const handleSelect = (date: string, startTime: string, endTime: string) => {
    setInitialDate(date);
    setInitialStartTime(startTime);
    setInitialEndTime(endTime);
    setIsCreateModalOpen(true);
  };

  const handleEventClick = (reservationId: number) => {
    const reservation = reservations.find(r => r.id === reservationId);
    if (reservation) {
      setSelectedReservation(reservation);
      setIsViewModalOpen(true);
    }
  };

  const handleCreateReservation = async (data: CreateReservationData) => {
    try {
      await createReservation(data);
      toast.success('Reservation created successfully');
      setIsCreateModalOpen(false);
      setInitialDate('');
      setInitialStartTime('');
      setInitialEndTime('');
      fetchData();
    } catch (error: unknown) {
      console.error('Failed to create reservation:', error);
      const err = error as { response?: { data?: { error?: string; message?: string; details?: Record<string, string> } } };
      const details = err.response?.data?.details;
      const message = details
        ? Object.values(details).join('. ')
        : err.response?.data?.message || err.response?.data?.error || 'Failed to create reservation';
      toast.error(message);
    }
  };

  const handleUpdateReservation = async (data: CreateReservationData) => {
    if (!selectedReservation) return;
    try {
      await updateReservation(selectedReservation.id, data);
      toast.success('Reservation updated successfully');
      setIsViewModalOpen(false);
      setSelectedReservation(null);
      setIsEditing(false);
      fetchData();
    } catch (error: unknown) {
      console.error('Failed to update reservation:', error);
      const err = error as { response?: { data?: { error?: string; message?: string; details?: Record<string, string> } } };
      const details = err.response?.data?.details;
      const message = details
        ? Object.values(details).join('. ')
        : err.response?.data?.message || err.response?.data?.error || 'Failed to update reservation';
      toast.error(message);
    }
  };

  const handleDeleteReservation = async () => {
    if (!selectedReservation) return;
    try {
      await deleteReservation(selectedReservation.id);
      toast.success('Reservation deleted successfully');
      setIsViewModalOpen(false);
      setSelectedReservation(null);
      fetchData();
    } catch (error: unknown) {
      console.error('Failed to delete reservation:', error);
      const err = error as { response?: { data?: { error?: string } } };
      toast.error(err.response?.data?.error || 'Failed to delete reservation');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <div>
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 className="text-3xl font-bold text-gray-900">Reservation Calendar</h1>

        <div className="flex items-center gap-4">
          <select
            value={selectedRoom || ''}
            onChange={(e) => setSelectedRoom(e.target.value ? parseInt(e.target.value) : null)}
            className="input w-48"
          >
            <option value="">All Rooms</option>
            {rooms.map((room) => (
              <option key={room.id} value={room.id}>
                {room.name}
              </option>
            ))}
          </select>

          <button onClick={() => setIsCreateModalOpen(true)} className="btn btn-primary">
            New Reservation
          </button>
        </div>
      </div>

      {rooms.length === 0 ? (
        <div className="card text-center py-12">
          <p className="text-gray-500 mb-4">No conference rooms available. Create a room first.</p>
        </div>
      ) : (
        <div className="card">
          <Calendar
            reservations={reservations}
            rooms={rooms}
            onSelect={handleSelect}
            onEventClick={handleEventClick}
          />
        </div>
      )}

      {/* Create Reservation Modal */}
      <Modal
        isOpen={isCreateModalOpen}
        onClose={() => {
          setIsCreateModalOpen(false);
          setInitialDate('');
          setInitialStartTime('');
          setInitialEndTime('');
        }}
        title="New Reservation"
      >
        <ReservationForm
          rooms={rooms}
          initialDate={initialDate}
          initialStartTime={initialStartTime}
          initialEndTime={initialEndTime}
          initialRoomId={selectedRoom || undefined}
          onSubmit={handleCreateReservation}
          onCancel={() => {
            setIsCreateModalOpen(false);
            setInitialDate('');
            setInitialStartTime('');
            setInitialEndTime('');
          }}
        />
      </Modal>

      {/* View/Edit Reservation Modal */}
      <Modal
        isOpen={isViewModalOpen}
        onClose={() => {
          setIsViewModalOpen(false);
          setSelectedReservation(null);
          setIsEditing(false);
        }}
        title={isEditing ? 'Edit Reservation' : 'Reservation Details'}
      >
        {selectedReservation && !isEditing ? (
          <div>
            <div className="space-y-3 mb-6">
              <p>
                <span className="font-medium text-gray-700">Room:</span>{' '}
                {selectedReservation.conferenceRoomName}
              </p>
              <p>
                <span className="font-medium text-gray-700">Reserved by:</span>{' '}
                {selectedReservation.reserverName}
              </p>
              <p>
                <span className="font-medium text-gray-700">Date:</span>{' '}
                {new Date(selectedReservation.date).toLocaleDateString()}
              </p>
              <p>
                <span className="font-medium text-gray-700">Time:</span>{' '}
                {selectedReservation.startTime} - {selectedReservation.endTime}
              </p>
              {selectedReservation.notes && (
                <p>
                  <span className="font-medium text-gray-700">Notes:</span>{' '}
                  {selectedReservation.notes}
                </p>
              )}
            </div>

            <div className="flex justify-end gap-3">
              <button onClick={handleDeleteReservation} className="btn btn-danger">
                Delete
              </button>
              <button onClick={() => setIsEditing(true)} className="btn btn-primary">
                Edit
              </button>
            </div>
          </div>
        ) : isEditing && selectedReservation ? (
          <ReservationForm
            rooms={rooms}
            reservation={selectedReservation}
            onSubmit={handleUpdateReservation}
            onCancel={() => setIsEditing(false)}
          />
        ) : null}
      </Modal>
    </div>
  );
}
