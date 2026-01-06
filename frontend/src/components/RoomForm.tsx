'use client';

import { useState, useEffect } from 'react';
import { ConferenceRoom, CreateRoomData, UpdateRoomData } from '@/types';

interface RoomFormProps {
  room?: ConferenceRoom | null;
  onSubmit: (data: CreateRoomData | UpdateRoomData) => Promise<void>;
  onCancel: () => void;
}

/**
 * Form component for creating and editing conference rooms.
 */
export default function RoomForm({ room, onSubmit, onCancel }: RoomFormProps) {
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [capacity, setCapacity] = useState('');
  const [location, setLocation] = useState('');
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const isEditing = !!room;

  useEffect(() => {
    if (room) {
      setName(room.name);
      setDescription(room.description || '');
      setCapacity(room.capacity.toString());
      setLocation(room.location || '');
    }
  }, [room]);

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!name.trim()) {
      newErrors.name = 'Name is required';
    }

    const capacityNum = parseInt(capacity);
    if (!capacity || isNaN(capacityNum) || capacityNum <= 0) {
      newErrors.capacity = 'Capacity must be a positive number';
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
        name: name.trim(),
        description: description.trim() || undefined,
        capacity: parseInt(capacity),
        location: location.trim() || undefined,
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <label htmlFor="name" className="label">
          Room Name <span className="text-red-500">*</span>
        </label>
        <input
          id="name"
          type="text"
          value={name}
          onChange={(e) => setName(e.target.value)}
          className={`input ${errors.name ? 'border-red-500' : ''}`}
          placeholder="e.g., Main Conference Room"
        />
        {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
      </div>

      <div>
        <label htmlFor="capacity" className="label">
          Capacity <span className="text-red-500">*</span>
        </label>
        <input
          id="capacity"
          type="number"
          min="1"
          value={capacity}
          onChange={(e) => setCapacity(e.target.value)}
          className={`input ${errors.capacity ? 'border-red-500' : ''}`}
          placeholder="e.g., 10"
        />
        {errors.capacity && <p className="text-red-500 text-sm mt-1">{errors.capacity}</p>}
      </div>

      <div>
        <label htmlFor="location" className="label">
          Location
        </label>
        <input
          id="location"
          type="text"
          value={location}
          onChange={(e) => setLocation(e.target.value)}
          className="input"
          placeholder="e.g., Building A, Floor 2"
        />
      </div>

      <div>
        <label htmlFor="description" className="label">
          Description
        </label>
        <textarea
          id="description"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
          className="input"
          rows={3}
          placeholder="Optional description of the room..."
        />
      </div>

      <div className="flex justify-end gap-3 pt-4">
        <button type="button" onClick={onCancel} className="btn btn-secondary" disabled={loading}>
          Cancel
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading ? 'Saving...' : isEditing ? 'Update Room' : 'Create Room'}
        </button>
      </div>
    </form>
  );
}
