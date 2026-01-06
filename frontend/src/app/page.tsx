'use client';

import Link from 'next/link';
import { useEffect, useState } from 'react';
import { getRooms, getReservations } from '@/lib/api';
import { ConferenceRoom, Reservation } from '@/types';

/**
 * Dashboard page showing overview of rooms and today's reservations.
 */
export default function Home() {
  const [rooms, setRooms] = useState<ConferenceRoom[]>([]);
  const [todayReservations, setTodayReservations] = useState<Reservation[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      try {
        const [roomsData, reservationsData] = await Promise.all([
          getRooms(),
          getReservations({ date: new Date().toISOString().split('T')[0] }),
        ]);
        setRooms(roomsData);
        setTodayReservations(reservationsData);
      } catch (error) {
        console.error('Failed to fetch data:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchData();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {/* Stats Cards */}
        <div className="card">
          <h3 className="text-lg font-medium text-gray-500">Total Rooms</h3>
          <p className="text-3xl font-bold text-primary-600">{rooms.length}</p>
        </div>
        <div className="card">
          <h3 className="text-lg font-medium text-gray-500">Today&apos;s Reservations</h3>
          <p className="text-3xl font-bold text-primary-600">{todayReservations.length}</p>
        </div>
        <div className="card">
          <h3 className="text-lg font-medium text-gray-500">Total Capacity</h3>
          <p className="text-3xl font-bold text-primary-600">
            {rooms.reduce((sum, room) => sum + room.capacity, 0)}
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Conference Rooms */}
        <div className="card">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-semibold text-gray-900">Conference Rooms</h2>
            <Link href="/rooms" className="text-primary-600 hover:text-primary-700 text-sm font-medium">
              View All
            </Link>
          </div>
          {rooms.length === 0 ? (
            <p className="text-gray-500">No conference rooms yet.</p>
          ) : (
            <ul className="divide-y divide-gray-200">
              {rooms.slice(0, 5).map((room) => (
                <li key={room.id} className="py-3 flex justify-between items-center">
                  <div>
                    <p className="font-medium text-gray-900">{room.name}</p>
                    <p className="text-sm text-gray-500">
                      Capacity: {room.capacity} {room.location && `| ${room.location}`}
                    </p>
                  </div>
                </li>
              ))}
            </ul>
          )}
        </div>

        {/* Today's Reservations */}
        <div className="card">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-semibold text-gray-900">Today&apos;s Schedule</h2>
            <Link href="/calendar" className="text-primary-600 hover:text-primary-700 text-sm font-medium">
              View Calendar
            </Link>
          </div>
          {todayReservations.length === 0 ? (
            <p className="text-gray-500">No reservations for today.</p>
          ) : (
            <ul className="divide-y divide-gray-200">
              {todayReservations.map((reservation) => (
                <li key={reservation.id} className="py-3">
                  <div className="flex justify-between items-start">
                    <div>
                      <p className="font-medium text-gray-900">{reservation.conferenceRoomName}</p>
                      <p className="text-sm text-gray-500">
                        {reservation.startTime} - {reservation.endTime}
                      </p>
                    </div>
                    <span className="text-sm text-gray-600">{reservation.reserverName}</span>
                  </div>
                </li>
              ))}
            </ul>
          )}
        </div>
      </div>

      {/* Quick Actions */}
      <div className="mt-8 flex gap-4">
        <Link href="/calendar" className="btn btn-secondary">
          Make a Reservation
        </Link>
      </div>
    </div>
  );
}
