# 🍕 FoodDash

A complete local food delivery system built with Laravel 12. Supports customers, restaurants, riders, and admin — all with real-time updates, live tracking, and push notifications.

---

## ✨ Features

### 🛒 For Customers
- Browse restaurants by cuisine
- Search & filter by location
- Save favorite restaurants
- Place orders with delivery address
- Live order tracking with real-time map
- In-app chat with riders
- Rate restaurants & riders
- Reorder in one click
- Cancel pending orders
- Push notifications for order updates

### 🏪 For Restaurants
- Confirm or reject incoming orders
- Manage menu items with images
- Toggle availability in real-time
- Set operating hours with auto open/close
- Track sales analytics and top-selling items
- Add external orders (walk-in / phone)
- Upload cover & profile images
- Custom prep time per order

### 🛵 For Riders
- Go online / offline toggle
- Automatic delivery offers based on proximity
- Accept offers in real-time
- Update order status (picked up, out for delivery, delivered)
- Live location sharing with customers
- Chat with customers
- Daily earnings dashboard with charts
- Delivery history with ratings
- Record payment upon delivery

### 🛡️ For Admins
- Approve or disable accounts
- Customizable logo with size slider
- System configuration (town, service radius, delivery fee)
- View all orders with advanced filters
- Export orders to CSV
- Generate sales reports
- Track top restaurants and riders
- Real-time order monitoring
- Push notification for new registrations

---

## 🚀 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | Blade, Alpine.js, Tailwind CSS |
| **Database** | MySQL / MariaDB |
| **Real-time** | Laravel Reverb (WebSockets) |
| **Push** | Web Push API + VAPID |
| **Maps** | Leaflet + OpenStreetMap |
| **Charts** | Chart.js |
| **PWA** | Service Worker + Manifest |
| **Build** | Vite 6 |

---

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL / MariaDB
- OpenSSL (for VAPID keys)

---

## 🔧 Installation

### 1. Clone the repository

```bash
git clone https://github.com/VoidSystem88/FoodDash.git
cd FoodDash