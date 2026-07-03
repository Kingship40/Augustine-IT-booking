<?php
declare(strict_types=1);
$viewer = current_user();
if (!$viewer) {
    redirect('login');
}

// Simple fetch of all recent contacts (either users who have messaged us, or platform users)
$db = db();
$stmt = $db->prepare("SELECT id, full_name, role FROM users WHERE id != :id ORDER BY full_name ASC");
$stmt->execute(['id' => $viewer['id']]);
$contacts = $stmt->fetchAll() ?: [];
?>

<div class="chat-container">
    <!-- Left Sidebar: Contacts List -->
    <div class="chat-sidebar">
        <h3>Conversations</h3>
        <div class="contact-list">
            <?php foreach ($contacts as $contact): ?>
                <div class="contact-item" data-id="<?= $contact['id'] ?>" data-name="<?= e($contact['full_name']) ?>">
                    <div class="avatar"><?= strtoupper(substr($contact['full_name'], 0, 2)) ?></div>
                    <div class="contact-info">
                        <strong><?= e($contact['full_name']) ?></strong>
                        <span class="role-tag"><?= e($contact['role']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Area: Active Chat Window -->
    <div class="chat-window">
        <div class="chat-header" id="active-chat-header">
            <h3>Select a contact to start messaging</h3>
        </div>
        
        <div class="chat-messages" id="chat-messages-box">
            <div class="chat-empty-state">
                <p class="muted">Fiverr & Messenger Realtime Chat Pipeline</p>
            </div>
        </div>

        <form class="chat-input-area" id="chat-form">
            <input type="text" id="chat-input-message" placeholder="Type a message..." autocomplete="off" disabled>
            <button type="submit" id="chat-send-btn" disabled>Send</button>
        </form>
    </div>
</div>

<!-- Include Supabase JS Client for Realtime Stream Hooks -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script>
    // Initialize Supabase configuration using your dashboard credentials
    const supabaseUrl = 'https://aws-0-eu-west-1.pooler.supabase.com'; // Note: Ensure this points to your standard REST API URL if different from the pooler endpoint
    const supabaseKey = 'YOUR_SUPABASE_ANON_PUBLIC_KEY'; 
    const supabase = supabase.createClient(supabaseUrl, supabaseKey);

    const currentUserId = <?= (int)$viewer['id'] ?>;
    let activeReceiverId = null;
    let realtimeChannel = null;

    // DOM Selection
    const contactItems = document.querySelectorAll('.contact-item');
    const messagesBox = document.getElementById('chat-messages-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('chat-input-message');
    const sendBtn = document.getElementById('chat-send-btn');
    const chatHeader = document.getElementById('active-chat-header');

    // Handle Contact Selection
    contactItems.forEach(item => {
        item.addEventListener('click', async () => {
            contactItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            activeReceiverId = parseInt(item.getAttribute('data-id'));
            const name = item.getAttribute('data-name');

            chatHeader.innerHTML = `<h3>Conversation with ${name}</h3>`;
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();

            // Load Historical Messages
            await fetchMessages();

            // Clear unread status locally for this provider/seeker conversation
            await supabase
                .from('messages')
                .update({ is_read: true })
                .eq('sender_id', activeReceiverId)
                .eq('receiver_id', currentUserId);

            // Re-trigger the global layout script calculation to clear notifications instantly
            if (typeof window.updateUnreadBadgeCount === 'function') {
                window.updateUnreadBadgeCount();
            }

            // Connect Realtime Channel
            setupRealtimeSubscription();
        });
    });

    // Load Messages from Supabase DB
    async function fetchMessages() {
        const { data, error } = await supabase
            .from('messages')
            .select('*')
            .or(`and(sender_id.eq.${currentUserId},receiver_id.eq.${activeReceiverId}),and(sender_id.eq.${activeReceiverId},receiver_id.eq.${currentUserId})`)
            .order('created_at', { ascending: true });

        if (error) console.error(error);
        
        messagesBox.innerHTML = '';
        if(data && data.length > 0) {
            data.forEach(msg => appendMessage(msg));
        } else {
            messagesBox.innerHTML = '<div class="chat-empty-state">No messages yet. Say hello!</div>';
        }
        scrollChatToBottom();
    }

    // Append Message UI Bubble safely
    function appendMessage(msg) {
        const isMe = msg.sender_id === currentUserId;
        const bubble = document.createElement('div');
        bubble.classList.add('message-bubble', isMe ? 'me' : 'them');
        bubble.innerText = msg.message_text;
        messagesBox.appendChild(bubble);
        scrollChatToBottom();
    }

    // Realtime Pipeline Subscriptions (Facebook Messenger Responsiveness Engine)
    function setupRealtimeSubscription() {
        if (realtimeChannel) {
            supabase.removeChannel(realtimeChannel);
        }

        realtimeChannel = supabase.channel('custom-filter-channel')
            .on('postgres_changes', { 
                event: 'INSERT', 
                schema: 'public', 
                table: 'messages' 
            }, async (payload) => {
                const newMsg = payload.new;
                // Only process the payload if it belongs to the current open conversation context
                if (
                    (newMsg.sender_id === currentUserId && newMsg.receiver_id === activeReceiverId) ||
                    (newMsg.sender_id === activeReceiverId && newMsg.receiver_id === currentUserId)
                ) {
                    // Avoid duplicating local UI optimistic inserts
                    const existingEmptyState = document.querySelector('.chat-empty-state');
                    if (existingEmptyState) existingEmptyState.remove();
                    
                    if (newMsg.sender_id !== currentUserId) {
                        appendMessage(newMsg);
                        
                        // Because we are actively reading this message right now, mark it read instantly
                        await supabase
                            .from('messages')
                            .update({ is_read: true })
                            .eq('id', newMsg.id);

                        if (typeof window.updateUnreadBadgeCount === 'function') {
                            window.updateUnreadBadgeCount();
                        }
                    }
                }
            })
            .subscribe();
    }

    // Send Message Operations
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text || !activeReceiverId) return;

        // Clear input layout smoothly instantly (Messenger Style Optimism)
        messageInput.value = '';

        // Render immediately locally
        const mockMsg = { sender_id: currentUserId, receiver_id: activeReceiverId, message_text: text };
        const emptyState = document.querySelector('.chat-empty-state');
        if (emptyState) emptyState.remove();
        appendMessage(mockMsg);

        // Write async directly downstream to Supabase DB backend
        const { error } = await supabase
            .from('messages')
            .insert([{ sender_id: currentUserId, receiver_id: activeReceiverId, message_text: text }]);

        if (error) console.error("Error broadcasting transaction:", error);
    });

    function scrollChatToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
</script>