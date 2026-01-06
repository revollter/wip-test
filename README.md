# Conference Room Reservation System

A full-stack web application for managing conference room reservations, built with Symfony (PHP), Next.js (React), PostgreSQL, and RabbitMQ.

## Features

- **Conference Room Management**: Create, edit, and delete conference rooms with details like name, capacity, location, and description.
- **Reservation System**: Book conference rooms with time slot validation to prevent double-booking.
- **Interactive Calendar**: View and manage reservations using an intuitive calendar interface.
- **Real-time Notifications**: RabbitMQ integration for asynchronous notification processing.
- **RESTful API**: API with validation and error handling.

## Technology Stack

### Backend
- **PHP 8.2** with **Symfony 6.4** framework
- **PostgreSQL 15** database
- **RabbitMQ** for message queuing
- **Doctrine ORM** with Repository pattern
- **Symfony Messenger** for async message handling

### Frontend
- **React 18** with **Next.js 14**
- **TypeScript** for type safety
- **Tailwind CSS** for styling
- **FullCalendar** for calendar functionality
- **Axios** for API communication

### Infrastructure
- **Docker** & **Docker Compose** for containerization

## Project Structure

```
.
├── backend/                    # Symfony Backend
│   ├── config/                 # Configuration files
│   ├── migrations/             # Database migrations
│   ├── src/
│   │   ├── Controller/         # REST API controllers
│   │   ├── Entity/             # Doctrine entities
│   │   ├── Message/            # Messenger messages
│   │   ├── MessageHandler/     # Message handlers
│   │   └── Repository/         # Repository classes
│   └── tests/                  # PHPUnit tests
├── frontend/                   # Next.js Frontend
│   └── src/
│       ├── app/                # Next.js pages
│       ├── components/         # React components
│       ├── lib/                # API client
│       └── types/              # TypeScript types
└── docker-compose.yml          # Docker configuration
```

## Quick Start

### Prerequisites

- Docker and Docker Compose installed on your system
- Git

### Installation

1. **Clone the repository:**
   ```bash
   git clone git@github.com:revollter/wip-test.git
   cd wip-test
   ```

2. **Build and start the application:**
   ```bash
   docker compose up --build -d
   ```

   This single command will:
   - Build and start all containers (PostgreSQL, RabbitMQ, Symfony backend, Next.js frontend)
   - Install all dependencies


3. **Run database migrations:**
   ```bash
   docker exec reservation_backend php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Load sample data for development:**
   ```bash
   docker exec reservation_backend php bin/console doctrine:fixtures:load --no-interaction
   ```
   This will create 5 sample conference rooms and reservations for the current week.

5. **Access the application:**
   - **Frontend**: http://localhost:3000
   - **Backend API**: http://localhost:8080/api
   - **RabbitMQ Management**: http://localhost:15672 (guest/guest)


## API Endpoints

### Conference Rooms

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/rooms` | List all rooms |
| GET | `/api/rooms/{id}` | Get room details |
| POST | `/api/rooms` | Create a new room |
| PUT | `/api/rooms/{id}` | Update a room |
| DELETE | `/api/rooms/{id}` | Delete a room |

### Reservations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reservations` | List all reservations |
| GET | `/api/reservations?date=YYYY-MM-DD` | Filter by date |
| GET | `/api/reservations?roomId=1` | Filter by room |
| GET | `/api/reservations?startDate=...&endDate=...` | Filter by date range |
| GET | `/api/reservations/{id}` | Get reservation details |
| POST | `/api/reservations` | Create a reservation |
| PUT | `/api/reservations/{id}` | Update a reservation |
| DELETE | `/api/reservations/{id}` | Delete a reservation |

### Example Requests

**Create a conference room:**
```bash
curl -X POST http://localhost:8080/api/rooms \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Main Conference Room",
    "capacity": 20,
    "location": "Building A, Floor 2",
    "description": "Large conference room with projector"
  }'
```

**Create a reservation:**
```bash
curl -X POST http://localhost:8080/api/reservations \
  -H "Content-Type: application/json" \
  -d '{
    "conferenceRoom": 1,
    "reserverName": "John Smith",
    "date": "2024-01-15",
    "startTime": "10:00",
    "endTime": "11:30",
    "notes": "Team meeting"
  }'
```
