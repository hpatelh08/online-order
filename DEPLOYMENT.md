# College Canteen Ordering System - Deployment Guide

## Current Setup
The system currently uses browser localStorage for order history, which means:
- Orders are saved only in the same browser
- Orders don't sync across different browsers/devices
- Data is lost if browser cache is cleared

## Solution for Cross-Browser Order History

To make order history work across ALL browsers and devices, you need a backend server. Here are the options:

## Option 1: Using Node.js (Recommended)

### Prerequisites:
1. Install Node.js from https://nodejs.org/
2. Make sure npm is available in your PATH

### Setup Instructions:
1. Open Command Prompt/Terminal
2. Navigate to the backend folder:
   ```bash
   cd e:\collage_canteen\backend
   ```
3. Install dependencies:
   ```bash
   npm install
   ```
4. Start the server:
   ```bash
   npm start
   ```
5. The server will run on http://localhost:3000

### How it works:
- Orders are saved to individual JSON files in `backend/data/` folder
- Each user's orders are stored separately by phone number
- API endpoints:
  - POST `/api/orders` - Save new order
  - GET `/api/orders/:phone` - Get user's orders

## Option 2: Using PHP

### Prerequisites:
1. Install PHP from https://windows.php.net/download/
2. Make sure PHP is available in your PATH

### Setup Instructions:
1. Open Command Prompt/Terminal
2. Navigate to the backend folder:
   ```bash
   cd e:\collage_canteen\backend
   ```
3. Start the PHP server:
   ```bash
   php -S localhost:8001
   ```
4. The server will run on http://localhost:8001

### How it works:
- Orders are saved to individual JSON files in `backend/data/` folder
- Each user's orders are stored separately by phone number
- PHP endpoints:
  - `save_order.php` - Save new order
  - `get_orders.php` - Get user's orders

## Option 3: Using Python Flask (Alternative)

If you want to use Python for the backend, you can install Flask:
```bash
pip install flask
```

Then start the Flask server:
```bash
cd e:\collage_canteen\backend
python flask_server.py
```

The server will run on http://localhost:5000

### How it works:
- Orders are saved to individual JSON files in `backend/data/` folder
- Each user's orders are stored separately by phone number
- API endpoints:
  - POST `/api/orders` - Save new order
  - GET `/api/orders/<phone>` - Get user's orders

## Testing Cross-Browser Sync

Once you have the backend running:

1. Open Chrome and go to http://localhost:3000 (or appropriate port)
2. Login and place an order
3. Open Firefox and go to the same URL
4. Login with the same phone number
5. Click "📋 History" - You should see the same order history!

## File Structure:
```
e:\collage_canteen\
├── backend\
│   ├── data\              # Order history storage (created automatically)
│   ├── server.js          # Node.js server (if using Node.js)
│   ├── save_order.php     # PHP endpoint (if using PHP)
│   ├── get_orders.php     # PHP endpoint (if using PHP)
│   └── package.json       # Node.js dependencies
└── fronted\
    ├── index.html         # Login page
    └── category.html      # Menu and ordering page
```

## Troubleshooting:

### If orders don't sync:
1. Make sure the backend server is running
2. Check browser console (F12) for errors
3. Verify the phone number is the same in both browsers
4. Check if the `backend/data/` folder has been created

### If you see "No order history":
1. Make sure you've placed at least one order
2. Verify the phone number in the URL matches
3. Check if the order was saved in the backend data folder

## Support:
For any issues, check the browser console (F12) for error messages and contact the developer.
