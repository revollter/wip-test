/**
 * Type definitions for the Conference Room Reservation System.
 */

export interface ConferenceRoom {
  id: number;
  name: string;
  description: string | null;
  capacity: number;
  location: string | null;
  createdAt: string;
  updatedAt: string;
}

export interface Reservation {
  id: number;
  conferenceRoom: number;
  conferenceRoomName: string;
  reserverName: string;
  date: string;
  startTime: string;
  endTime: string;
  notes: string | null;
  createdAt: string;
  updatedAt: string;
}

export interface CreateRoomData {
  name: string;
  description?: string;
  capacity: number;
  location?: string;
}

export interface UpdateRoomData {
  name?: string;
  description?: string | null;
  capacity?: number;
  location?: string | null;
}

export interface CreateReservationData {
  conferenceRoom: number;
  reserverName: string;
  date: string;
  startTime: string;
  endTime: string;
  notes?: string;
}

export interface UpdateReservationData {
  conferenceRoom?: number;
  reserverName?: string;
  date?: string;
  startTime?: string;
  endTime?: string;
  notes?: string | null;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
  total?: number;
}

export interface ApiError {
  error: string;
  details?: Record<string, string> | string[];
  message?: string;
}
