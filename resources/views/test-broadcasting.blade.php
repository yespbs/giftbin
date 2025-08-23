<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broadcasting Test - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f8fafc;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1f2937;
            margin-bottom: 20px;
        }
        .status {
            padding: 10px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .status.connecting {
            background: #fef3c7;
            color: #92400e;
        }
        .status.connected {
            background: #d1fae5;
            color: #065f46;
        }
        .status.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .event {
            background: #f3f4f6;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .event-title {
            font-weight: 600;
            color: #1f2937;
        }
        .event-details {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }
        button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Broadcasting Test</h1>
        <p>This page tests real-time broadcasting when events are published.</p>
        
        <div id="connection-status" class="status connecting">
            ⏳ Connecting to WebSocket...
        </div>

        <div style="margin: 20px 0;">
            <button onclick="createTestEvent()">Create Test Event</button>
            <button onclick="publishTestEvent()">Publish Test Event</button>
            <button onclick="clearEvents()">Clear Events</button>
        </div>

        <div id="events-container">
            <h3>📡 Real-time Event Updates</h3>
            <p>New published events will appear here automatically...</p>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Initialize connection status
        const statusDiv = document.getElementById('connection-status');
        const eventsContainer = document.getElementById('events-container');
        
        // Configure Pusher/Reverb connection
        const pusher = new Pusher('{{ env("VITE_REVERB_APP_KEY") }}', {
            wsHost: '{{ env("VITE_REVERB_HOST") }}',
            wsPort: {{ env('VITE_REVERB_PORT') }},
            wssPort: {{ env('VITE_REVERB_PORT') }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
        });

        // Handle connection states
        pusher.connection.bind('connected', function() {
            statusDiv.className = 'status connected';
            statusDiv.innerHTML = '✅ Connected to WebSocket server';
        });

        pusher.connection.bind('disconnected', function() {
            statusDiv.className = 'status error';
            statusDiv.innerHTML = '❌ Disconnected from WebSocket server';
        });

        pusher.connection.bind('error', function(error) {
            statusDiv.className = 'status error';
            statusDiv.innerHTML = '❌ Connection error: ' + (error.message || 'Unknown error');
            console.error('Pusher connection error:', error);
        });

        // Subscribe to events channel
        const channel = pusher.subscribe('events');
        
        channel.bind('event.published', function(data) {
            console.log('New event published:', data);
            displayNewEvent(data);
        });

        function displayNewEvent(event) {
            const eventDiv = document.createElement('div');
            eventDiv.className = 'event';
            eventDiv.innerHTML = `
                <div class="event-title">🎉 ${event.title}</div>
                <div class="event-details">
                    📍 ${event.location || 'Location TBD'}<br>
                    📅 ${new Date(event.start_date).toLocaleDateString()}<br>
                    👤 By ${event.user.name}<br>
                    💰 ${event.price ? '$' + event.price : 'Free'}<br>
                    🏷️ ${event.category || 'General'}
                </div>
            `;
            
            // Insert at the top
            const container = document.getElementById('events-container');
            container.insertBefore(eventDiv, container.children[2] || null);
        }

        // Test functions
        async function createTestEvent() {
            try {
                const response = await fetch('/api/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer YOUR_TOKEN_HERE' // You'll need to replace this
                    },
                    body: JSON.stringify({
                        title: 'Test Event - ' + new Date().toLocaleTimeString(),
                        description: 'This is a test event created from the broadcasting test page',
                        location: 'Test Location',
                        start_date: new Date(Date.now() + 86400000).toISOString(), // Tomorrow
                        status: 'draft',
                        category: 'Test'
                    })
                });
                
                if (response.ok) {
                    alert('Test event created! (Check admin panel)');
                } else {
                    alert('Failed to create event. You may need authentication.');
                }
            } catch (error) {
                console.error('Error creating test event:', error);
                alert('Error creating test event. Check console for details.');
            }
        }

        async function publishTestEvent() {
            alert('To test publishing, go to the admin panel and change an event status to "published"');
        }

        function clearEvents() {
            const events = document.querySelectorAll('.event');
            events.forEach(event => event.remove());
        }

        // Show initial status
        console.log('Broadcasting test page loaded. Reverb configuration:');
        console.log('App Key:', '{{ env("VITE_REVERB_APP_KEY") }}');
        console.log('Host:', '{{ env("VITE_REVERB_HOST") }}');
        console.log('Port:', {{ env('VITE_REVERB_PORT') }});
    </script>
</body>
</html>