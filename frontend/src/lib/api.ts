/**
 * API client for communicating with the backend.
 */

import axios from 'axios';
import {
  ConferenceRoom,
  Reservation,
  CreateRoomData,
  UpdateRoomData,
  CreateReservationData,
  UpdateReservationData,
  ApiResponse,
} from '@/types';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Conference Room API

export async function getRooms(): Promise<ConferenceRoom[]> {
  const response = await api.get<ApiResponse<ConferenceRoom[]>>('/rooms');
  return response.data.data;
}

export async function getRoom(id: number): Promise<ConferenceRoom> {
  const response = await api.get<ApiResponse<ConferenceRoom>>(`/rooms/${id}`);
  return response.data.data;
}

export async function createRoom(data: CreateRoomData): Promise<ConferenceRoom> {
  const response = await api.post<ApiResponse<ConferenceRoom>>('/rooms', data);
  return response.data.data;
}

export async function updateRoom(id: number, data: UpdateRoomData): Promise<ConferenceRoom> {
  const response = await api.put<ApiResponse<ConferenceRoom>>(`/rooms/${id}`, data);
  return response.data.data;
}

export async function deleteRoom(id: number): Promise<void> {
  await api.delete(`/rooms/${id}`);
}

// Reservation API

export interface GetReservationsParams {
  roomId?: number;
  date?: string;
  startDate?: string;
  endDate?: string;
}

export async function getReservations(params?: GetReservationsParams): Promise<Reservation[]> {
  const response = await api.get<ApiResponse<Reservation[]>>('/reservations', { params });
  return response.data.data;
}

export async function getReservation(id: number): Promise<Reservation> {
  const response = await api.get<ApiResponse<Reservation>>(`/reservations/${id}`);
  return response.data.data;
}

export async function createReservation(data: CreateReservationData): Promise<Reservation> {
  const response = await api.post<ApiResponse<Reservation>>('/reservations', data);
  return response.data.data;
}

export async function updateReservation(id: number, data: UpdateReservationData): Promise<Reservation> {
  const response = await api.put<ApiResponse<Reservation>>(`/reservations/${id}`, data);
  return response.data.data;
}

export async function deleteReservation(id: number): Promise<void> {
  await api.delete(`/reservations/${id}`);
}

export default api;
