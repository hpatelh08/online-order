const express = require('express');
const fs = require('fs').promises;
const path = require('path');

const app = express();
const PORT = 3000;

// Middleware
app.use(express.json());
app.use(express.static(path.join(__dirname, '../fronted')));

// Ensure data directory exists
const dataDir = path.join(__dirname, 'data');
const ensureDataDir = async () => {
  try {
    await fs.mkdir(dataDir, { recursive: true });
  } catch (err) {
    console.error('Error creating data directory:', err);
  }
};

// Get user orders file path
const getUserOrdersFile = (phoneNumber) => {
  // Sanitize phone number for filename
  const sanitizedPhone = phoneNumber.replace(/[^0-9]/g, '');
  return path.join(dataDir, `${sanitizedPhone}_orders.json`);
};

// Save order for user
app.post('/api/orders', async (req, res) => {
  try {
    const { phoneNumber, orderData } = req.body;
    
    if (!phoneNumber || !orderData) {
      return res.status(400).json({ error: 'Phone number and order data required' });
    }
    
    const ordersFile = getUserOrdersFile(phoneNumber);
    
    // Read existing orders
    let orders = [];
    try {
      const data = await fs.readFile(ordersFile, 'utf8');
      orders = JSON.parse(data);
    } catch (err) {
      // File doesn't exist, start with empty array
      orders = [];
    }
    
    // Add new order
    orders.unshift(orderData); // Add to beginning
    
    // Keep only last 50 orders
    if (orders.length > 50) {
      orders = orders.slice(0, 50);
    }
    
    // Save orders
    await fs.writeFile(ordersFile, JSON.stringify(orders, null, 2));
    
    res.json({ success: true, message: 'Order saved successfully' });
  } catch (err) {
    console.error('Error saving order:', err);
    res.status(500).json({ error: 'Internal server error' });
  }
});

// Get user orders
app.get('/api/orders/:phoneNumber', async (req, res) => {
  try {
    const { phoneNumber } = req.params;
    
    if (!phoneNumber) {
      return res.status(400).json({ error: 'Phone number required' });
    }
    
    const ordersFile = getUserOrdersFile(phoneNumber);
    
    // Read orders
    try {
      const data = await fs.readFile(ordersFile, 'utf8');
      const orders = JSON.parse(data);
      res.json({ orders });
    } catch (err) {
      // File doesn't exist or invalid JSON
      res.json({ orders: [] });
    }
  } catch (err) {
    console.error('Error fetching orders:', err);
    res.status(500).json({ error: 'Internal server error' });
  }
});

// Serve index.html for root route
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, '../fronted/index.html'));
});

// Initialize and start server
const startServer = async () => {
  await ensureDataDir();
  app.listen(PORT, () => {
    console.log(`🚀 Canteen Server Running!`);
    console.log(`📱 Open http://localhost:${PORT} in your browser`);
    console.log(`📂 Order data will be saved in: ${dataDir}`);
  });
};

startServer();
