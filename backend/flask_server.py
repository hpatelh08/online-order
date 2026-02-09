from flask import Flask, request, jsonify, send_from_directory
import os
import json
import re
from datetime import datetime

app = Flask(__name__)

# Create data directory if it doesn't exist
DATA_DIR = os.path.join(os.path.dirname(__file__), 'data')
if not os.path.exists(DATA_DIR):
    os.makedirs(DATA_DIR)

def get_user_file(phone_number):
    """Get file path for user's orders"""
    # Sanitize phone number
    sanitized_phone = re.sub(r'[^0-9]', '', phone_number)
    return os.path.join(DATA_DIR, f'{sanitized_phone}_orders.json')

@app.route('/')
def serve_frontend():
    """Serve the frontend files"""
    return send_from_directory('../fronted', 'index.html')

@app.route('/<path:path>')
def serve_static(path):
    """Serve static files"""
    if path.startswith('fronted/'):
        return send_from_directory('..', path)
    return send_from_directory('../fronted', path)

@app.route('/api/orders', methods=['POST'])
def save_order():
    """Save order for user"""
    try:
        data = request.get_json()
        
        if not data or 'phoneNumber' not in data or 'orderData' not in data:
            return jsonify({'error': 'Phone number and order data required'}), 400
        
        phone = data['phoneNumber']
        order_data = data['orderData']
        
        if not phone:
            return jsonify({'error': 'Invalid phone number'}), 400
        
        # Get user's file
        user_file = get_user_file(phone)
        
        # Read existing orders
        orders = []
        if os.path.exists(user_file):
            try:
                with open(user_file, 'r') as f:
                    orders = json.load(f)
                if not isinstance(orders, list):
                    orders = []
            except:
                orders = []
        
        # Add new order at the beginning
        orders.insert(0, order_data)
        
        # Keep only last 50 orders
        if len(orders) > 50:
            orders = orders[:50]
        
        # Save orders
        with open(user_file, 'w') as f:
            json.dump(orders, f, indent=2)
        
        return jsonify({'success': True, 'message': 'Order saved successfully'})
    
    except Exception as e:
        print(f"Error saving order: {e}")
        return jsonify({'error': 'Internal server error'}), 500

@app.route('/api/orders/<phone_number>')
def get_orders(phone_number):
    """Get orders for user"""
    try:
        if not phone_number:
            return jsonify({'error': 'Phone number required'}), 400
        
        # Get user's file
        user_file = get_user_file(phone_number)
        
        # Read orders
        if os.path.exists(user_file):
            try:
                with open(user_file, 'r') as f:
                    orders = json.load(f)
                if isinstance(orders, list):
                    return jsonify({'orders': orders})
            except:
                pass
        
        return jsonify({'orders': []})
    
    except Exception as e:
        print(f"Error fetching orders: {e}")
        return jsonify({'error': 'Internal server error'}), 500

if __name__ == '__main__':
    print("🚀 Canteen Server Starting...")
    print("📁 Data directory:", DATA_DIR)
    print("📱 Open http://localhost:5000 in your browser")
    app.run(host='localhost', port=5000, debug=True)
