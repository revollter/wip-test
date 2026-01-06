'use client';

import { useState, useEffect, useCallback } from 'react';
import Link from 'next/link';
import toast from 'react-hot-toast';
import { getRooms, deleteRoom, createRoom, updateRoom } from '@/lib/api';
import { ConferenceRoom, CreateRoomData, UpdateRoomData } from '@/types';
import Modal from '@/components/Modal';
import RoomForm from '@/components/RoomForm';

/**
 * Page for listing and managing conference rooms.
 */
export default function RoomsPage() {
  const [rooms, setRooms] = useState<ConferenceRoom[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingRoom, setEditingRoom] = useState<ConferenceRoom | null>(null);
  const [deletingRoom, setDeletingRoom] = useState<ConferenceRoom | null>(null);

  const fetchRooms = useCallback(async () => {
    try {
      const data = await getRooms();
      setRooms(data);
    } catch (error) {
      console.error('Failed to fetch rooms:', error);
      toast.error('Failed to load rooms');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchRooms();
  }, [fetchRooms]);

  const handleCreateRoom = async (data: CreateRoomData | UpdateRoomData) => {
    try {
      await createRoom(data as CreateRoomData);
      toast.success('Room created successfully');
      setIsModalOpen(false);
      fetchRooms();
    } catch (error: unknown) {
      console.error('Failed to create room:', error);
      const err = error as { response?: { data?: { error?: string; details?: Record<string, string> } } };
      const details = err.response?.data?.details;
      const message = details
        ? Object.values(details).join('. ')
        : err.response?.data?.error || 'Failed to create room';
      toast.error(message);
    }
  };

  const handleUpdateRoom = async (data: CreateRoomData | UpdateRoomData) => {
    if (!editingRoom) return;
    try {
      await updateRoom(editingRoom.id, data);
      toast.success('Room updated successfully');
      setEditingRoom(null);
      fetchRooms();
    } catch (error: unknown) {
      console.error('Failed to update room:', error);
      const err = error as { response?: { data?: { error?: string; details?: Record<string, string> } } };
      const details = err.response?.data?.details;
      const message = details
        ? Object.values(details).join('. ')
        : err.response?.data?.error || 'Failed to update room';
      toast.error(message);
    }
  };

  const handleDeleteRoom = async () => {
    if (!deletingRoom) return;
    try {
      await deleteRoom(deletingRoom.id);
      toast.success('Room deleted successfully');
      setDeletingRoom(null);
      fetchRooms();
    } catch (error: unknown) {
      console.error('Failed to delete room:', error);
      const err = error as { response?: { data?: { error?: string; details?: Record<string, string> } } };
      const details = err.response?.data?.details;
      const message = details
        ? Object.values(details).join('. ')
        : err.response?.data?.error || 'Failed to delete room';
      toast.error(message);
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
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-gray-900">Conference Rooms</h1>
        <button onClick={() => setIsModalOpen(true)} className="btn btn-primary">
          Add New Room
        </button>
      </div>

      {rooms.length === 0 ? (
        <div className="card text-center py-12">
          <p className="text-gray-500 mb-4">No conference rooms yet.</p>
          <button onClick={() => setIsModalOpen(true)} className="btn btn-primary">
            Create Your First Room
          </button>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {rooms.map((room) => (
            <div key={room.id} className="card">
              <div className="flex justify-between items-start mb-3">
                <h3 className="text-lg font-semibold text-gray-900">{room.name}</h3>
                <span className="px-2 py-1 bg-primary-100 text-primary-700 text-sm rounded-full">
                  {room.capacity} seats
                </span>
              </div>

              {room.location && (
                <p className="text-sm text-gray-500 mb-2">
                  <span className="font-medium">Location:</span> {room.location}
                </p>
              )}

              {room.description && (
                <p className="text-gray-600 text-sm mb-4">{room.description}</p>
              )}

              <div className="flex gap-2 mt-4 pt-4 border-t">
                <button
                  onClick={() => setEditingRoom(room)}
                  className="flex-1 btn btn-secondary text-sm"
                >
                  Edit
                </button>
                <button
                  onClick={() => setDeletingRoom(room)}
                  className="flex-1 btn btn-danger text-sm"
                >
                  Delete
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Create Modal */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="Add New Room"
      >
        <RoomForm
          onSubmit={handleCreateRoom}
          onCancel={() => setIsModalOpen(false)}
        />
      </Modal>

      {/* Edit Modal */}
      <Modal
        isOpen={!!editingRoom}
        onClose={() => setEditingRoom(null)}
        title="Edit Room"
      >
        <RoomForm
          room={editingRoom}
          onSubmit={handleUpdateRoom}
          onCancel={() => setEditingRoom(null)}
        />
      </Modal>

      {/* Delete Confirmation Modal */}
      <Modal
        isOpen={!!deletingRoom}
        onClose={() => setDeletingRoom(null)}
        title="Delete Room"
      >
        <div>
          <p className="text-gray-600 mb-6">
            Are you sure you want to delete <strong>{deletingRoom?.name}</strong>?
            This action cannot be undone.
          </p>
          <div className="flex justify-end gap-3">
            <button
              onClick={() => setDeletingRoom(null)}
              className="btn btn-secondary"
            >
              Cancel
            </button>
            <button onClick={handleDeleteRoom} className="btn btn-danger">
              Delete
            </button>
          </div>
        </div>
      </Modal>
    </div>
  );
}
